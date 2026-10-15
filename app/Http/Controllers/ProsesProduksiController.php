<?php

namespace App\Http\Controllers;

use App\Models\ProsesProduksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ProsesProduksiController extends Controller
{
    public function calculateDerivedValues($record)
    {
        // Pastikan nilai input, jtdrik, dan upspk adalah angka sebelum melakukan perhitungan
        $input = (float) str_replace('.', '', (string) ($record->input ?? 0));
        $jtdrik = (float) str_replace('.', '', (string) ($record->jtdrik ?? 0));
        $upspk = (float) str_replace('.', '', (string) ($record->upspk ?? 0));
        $prosesName = strtolower((string) ($record->proses ?? ''));

        $record->input = $input;
        $record->jtdrik = $jtdrik;
        $record->upspk = $upspk;

        if ($prosesName === 'lem') {
            $record->jtdrik = $upspk > 0 ? $input / $upspk : 0;
            $record->jtpcs = $input;
            $record->outputdrik = $input - $record->jtdrik;
            $record->outputpcs = $record->outputdrik * $upspk;
            $record->total_pengerjaan_drik = $record->jtdrik + $record->outputdrik;
            $record->total_pengerjaan_pcs = $record->jtpcs + $record->outputpcs;

            return $record;
        }

        // Perhitungan outputdrik dan outputpcs
        $record->jtpcs = $jtdrik * $upspk;
        $record->outputdrik = $input - $jtdrik;
        $record->outputpcs = $record->outputdrik * $upspk;
        $record->total_pengerjaan_drik = $jtdrik + $record->outputdrik;
        $record->total_pengerjaan_pcs = $record->jtpcs + $record->outputpcs;

        return $record;
    }

    public function index(Request $request)
    {
        // 1.Tangkap input tanggal dari form filter
        $filterProses = $request->get('proses');
        $filterId = $request->get('id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $filterJob = $request->get('job');
        $filterOperator = $request->get('operator');
        $filterTanggal = $request->get('tanggal');
        $filterDocket = $request->get('designno');
        $filterProduct = $request->get('product');

        $query = ProsesProduksi::query();

        if (! empty($filterId)) {
            $query->where('id', $filterId);
        }
        if (! empty($filterProses)) {
            $query->where('proses', $filterProses);
        }
        // filter multi job di index
        if (! empty($filterJob)) {
            $jobsList = preg_split('/[\s,;|]+/', trim($filterJob));
            $jobsList = array_filter($jobsList);
            if (count($jobsList) > 1) {
                $query->where(function ($q) use ($jobsList) {
                    foreach ($jobsList as $jobItem) {
                        $q->orWhere('job', 'like', '%'.$jobItem.'%');
                    }
                });
            } elseif (count($jobsList) == 1) {
                $query->where('job', 'like', '%'.$jobsList[0].'%');
            }
        }
        if (! empty($filterOperator)) {
            $query->where('operator', 'like', '%'.$filterOperator.'%');
        }
        if (! empty($filterTanggal)) {
            $query->whereDate('tanggal', $filterTanggal);
        }
        if (! empty($filterDocket)) {
            $docketsList = preg_split('/[\s,;|]+/', trim($filterDocket));
            $docketsList = array_filter($docketsList);
            if (count($docketsList) > 1) {
                $query->where(function ($q) use ($docketsList) {
                    foreach ($docketsList as $docketItem) {
                        $q->orWhere('designno', 'like', '%'.$docketItem.'%');
                    }
                });
            } elseif (count($docketsList) == 1) {
                $query->where('designno', 'like', '%'.$docketsList[0].'%');
            }
        }
        if (! empty($filterProduct)) {
            $query->where('product', 'like', '%'.$filterProduct.'%');
        }
        // 2.Logika filter rentang tanggal
        if (! empty($startDate) && ! empty($endDate)) {
            // Jika dari tanggal & sampai tanggal diisi keduanya
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        } elseif (! empty($startDate)) {
            // Jika hanya mengisi "Dari Tanggal" saja
            $query->whereDate('tanggal', '>=', $startDate);
        } elseif (! empty($endDate)) {
            // Jika hanya mengisi "Sampai Tanggal" saja
            $query->whereDate('tanggal', '<=', $endDate);
        }

        // Sorting support
        $allowedSorts = [
            'job' => 'job',
            'docket' => 'designno',
            'proses' => 'proses',
            'product' => 'product',
            'operator' => 'operator',
            'tanggal' => 'tanggal',
        ];

        $sort = $request->query('sort');
        $dir = strtolower($request->query('dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        if ($sort && isset($allowedSorts[$sort])) {
            $query->orderBy($allowedSorts[$sort], $dir);
        } else {
            $query->orderBy('id', 'desc');
        }

        // Bagian ini sudah sangat tepat karena pakai ->appends($request->query())
        $prosesProduksi = $query->paginate(15)->appends($request->query());

        foreach ($prosesProduksi as $data) {
            $totalJam = 0;

            if (! empty($data->finish) && (! empty($data->set) || ! empty($data->run))) {
                $waktuMulaiString = ! empty($data->set) ? $data->set : $data->run;
                $waktuMulai = Carbon::parse($waktuMulaiString);
                $waktuFinish = Carbon::parse($data->finish);

                $selisihMenit = $waktuMulai->diffInMinutes($waktuFinish);
                $totalJam = $selisihMenit / 60;

                if (strtoupper($data->break) === 'TRUE' || $data->break == 1) {
                    $totalJam -= 1;
                }
            }

            // Timpa langsung properti asli database
            $data->totaljam = max(0, round($totalJam, 2));
            $this->calculateDerivedValues($data);
        }

        // =================================================================
        // PERBAIKAN: Tarik daftar proses yang unik langsung dari database!
        // =================================================================
        $daftarProses = ProsesProduksi::select('proses')
            ->whereNotNull('proses')
            ->distinct()
            ->orderBy('proses')
            ->pluck('proses');

        // Ambil data
        $data = $query->get();

        // Hitung total dari data yang tampil
        $total = [
            'input' => $prosesProduksi->sum(fn ($x) => (float) ($x->input ?? 0)),
            'jtpcs' => $prosesProduksi->sum(fn ($x) => (float) ($x->jtpcs ?? 0)),
            'jtdrik' => $prosesProduksi->sum(fn ($x) => (float) ($x->jtdrik ?? 0)),
            'outputpcs' => $prosesProduksi->sum(fn ($x) => (float) ($x->outputpcs ?? 0)),
            'outputdrik' => $prosesProduksi->sum(fn ($x) => (float) ($x->outputdrik ?? 0)),
        ];

        // Kirim $daftarProses (bukan masterProses) ke view index
        return view('role.produksi.proses_produksi.index', compact('prosesProduksi', 'daftarProses', 'total'));
    }

    public function create()
    {
        $jobs = ProsesProduksi::select('job')
            ->whereNotNull('job')
            ->distinct()
            ->orderBy('job')
            ->get();

        return view('proses_produksi.create', compact('jobs'));
    }

    public function show(Request $request, $job_id)
    {
        // 1. CARI NOMOR DOCKET DARI JOB INI
        $firstRecord = ProsesProduksi::where('job', $job_id)->first();
        if (! $firstRecord) {
            abort(404, 'Job tidak ditemukan.');
        }
        $docket = $firstRecord->designno ?? '-';

        // 2. KUMPULKAN DAFTAR NOMOR JOB YANG AKAN DITAMPILKAN
        $jobsToQuery = [];

        $searchJobsInput = $request->query('search_jobs');
        if (! empty($searchJobsInput)) {
            // Bersihkan dan pisahkan input pencarian berdasarkan koma/spasi/titik koma/pipe (sama seperti index)
            $searchedJobs = preg_split('/[\s,;|]+/', trim($searchJobsInput));
            // Hapus elemen kosong dan duplikat
            $searchedJobs = array_filter(array_unique(array_map('trim', $searchedJobs)));

            if (! empty($searchedJobs)) {
                $matchedJobs = [];
                foreach ($searchedJobs as $sJob) {
                    $jobsFound = ProsesProduksi::where('job', 'like', '%'.$sJob.'%')
                        ->distinct()
                        ->pluck('job')
                        ->toArray();

                    if (empty($jobsFound)) {
                        return redirect()->route('proses-produksi.show', $job_id)
                            ->with('error', "Tidak ada Job ID yang cocok dengan pencarian '{$sJob}'.");
                    }

                    $matchedJobs = array_merge($matchedJobs, $jobsFound);
                }

                $matchedJobs = array_unique($matchedJobs);
                $validatedJobs = [];
                $commonDocket = null;

                foreach ($matchedJobs as $mJob) {
                    $checkRecord = ProsesProduksi::where('job', $mJob)->first();
                    if ($checkRecord) {
                        $jobDocket = $checkRecord->designno ?? '-';

                        if ($commonDocket === null) {
                            $commonDocket = $jobDocket;
                        } elseif ($jobDocket !== $commonDocket) {
                            return redirect()->route('proses-produksi.show', $job_id)
                                ->with('error', "Pencarian multi-job harus memiliki Docket/Design No yang sama. Job '{$mJob}' memiliki Docket '{$jobDocket}' yang berbeda dengan Docket '{$commonDocket}'.");
                        }

                        $validatedJobs[] = $mJob;
                    }
                }

                $jobsToQuery = $validatedJobs;
                $docket = $commonDocket;
            }
        }

        // Jika tidak melakukan pencarian khusus, tampilkan job_id dari URL saja
        if (empty($jobsToQuery)) {
            $jobsToQuery = [$job_id];
        }

        // 3. AMBIL DATA HANYA UNTUK JOB YANG DIPILIH / HASIL PENCARIAN
        $allowedSorts = [
            'job' => 'job',
            'docket' => 'designno',
            'proses' => 'proses',
            'product' => 'product',
            'operator' => 'operator',
            'tanggal' => 'tanggal',
        ];

        $sort = $request->query('sort');
        $dir = strtolower($request->query('dir', 'asc')) === 'asc' ? 'asc' : 'desc';

        $detailQuery = ProsesProduksi::whereIn('job', $jobsToQuery);

        if ($sort && isset($allowedSorts[$sort])) {
            $detailQuery->orderBy($allowedSorts[$sort], $dir);
        } else {
            $detailOrder = [
                'PRINT', 'SORTIR CETAK', 'WATERBASE', 'HOCK', 'HOTPRINT',
                'LAMINASI', 'LAMINATING', 'EMBOSS', 'DIECUT', 'CUTTING',
                'PRETEL', 'LEM', 'SORTIR', 'PACKING',
            ];
            $caseSql = "CASE UPPER(proses) ";
            foreach ($detailOrder as $index => $processName) {
                $caseSql .= "WHEN ? THEN {$index} ";
            }
            $caseSql .= "ELSE " . count($detailOrder) . " END";

            $detailQuery->orderByRaw($caseSql, $detailOrder)
                ->orderBy('tanggal', 'desc');
        }

        $detailProses = $detailQuery->get();

        // 3. HITUNG ATRIBUT VIRTUAL
        foreach ($detailProses as $data) {
            $totalJam = 0;

            if (! empty($data->finish) && (! empty($data->set) || ! empty($data->run))) {
                $waktuMulaiString = ! empty($data->set) ? $data->set : $data->run;
                $waktuMulai = Carbon::parse($waktuMulaiString);
                $waktuFinish = Carbon::parse($data->finish);

                $selisihMenit = $waktuMulai->diffInMinutes($waktuFinish);
                $totalJam = $selisihMenit / 60;

                if (strtoupper($data->break) === 'TRUE' || $data->break == 1) {
                    $totalJam -= 1;
                }
            }

            $data->totaljam = max(0, round($totalJam, 2));

            $this->calculateDerivedValues($data);
        }

        // 4. BUAT TABEL RANGKUMAN (DOCKET-WIDE)
        $masterProses = [
            'PRINT', 'SORTIR CETAK', 'WATERBASE', 'HOCK', 'HOTPRINT',
            'LAMINASI', 'LAMINATING', 'EMBOSS', 'DIECUT', 'CUTTING',
            'PRETEL', 'LEM', 'SORTIR', 'PACKING',
        ];

        $rangkuman = [];

        foreach ($masterProses as $prosesName) {
            // Filter data yang sudah dihitung di atas, khusus untuk proses ini
            $dataPerProses = $detailProses->filter(function ($item) use ($prosesName) {
                return strtoupper($item->proses) == $prosesName;
            });

            $rangkuman[] = [
                'proses' => $prosesName,
                'jam' => $dataPerProses->sum('totaljam'),
                'jt_drik' => $dataPerProses->sum('jtdrik'),
                'jt_pcs' => $dataPerProses->sum('jtpcs'),
                'output_drik' => $dataPerProses->sum('outputdrik'),
                'output_pcs' => $dataPerProses->sum('outputpcs'),
                'total_pengerjaan_drik' => $dataPerProses->sum('total_pengerjaan_drik'),
                'total_pengerjaan_pcs' => $dataPerProses->sum('total_pengerjaan_pcs'),
                'selisih_drik' => 0,
                'selisih_pcs' => 0,
            ];
        }

        $total = [
            'input' => $detailProses->sum(fn ($x) => (float) ($x->input ?? 0)),
            'jtpcs' => $detailProses->sum(fn ($x) => (float) ($x->jtpcs ?? 0)),
            'jtdrik' => $detailProses->sum(fn ($x) => (float) ($x->jtdrik ?? 0)),
            'outputpcs' => $detailProses->sum(fn ($x) => (float) ($x->outputpcs ?? 0)),
            'outputdrik' => $detailProses->sum(fn ($x) => (float) ($x->outputdrik ?? 0)),
        ];

        // 5. KIRIM DATA KE BLADE
        return view('proses_produksi.show', compact('rangkuman', 'detailProses', 'job_id', 'docket', 'total', 'jobsToQuery'));
    }

    public function inlineUpdate(Request $request)
    {
        $allowedFields = ['input', 'jtdrik', 'jtpcs'];

        $request->validate([
            'id' => 'required|integer|exists:proses_produksis,id',
            'field' => 'required|string|in:'.implode(',', $allowedFields),
            'value' => 'required|numeric',
        ]);

        $record = ProsesProduksi::findOrFail($request->id);
        $field = $request->field;
        $numericValue = (float) $request->input('value');
        $prosesName = strtolower((string) ($record->proses ?? ''));
        $saveField = $field;

        if ($field === 'jtdrik' && $prosesName === 'lem') {
            return response()->json([
                'success' => false,
                'message' => 'Kolom JT Drik untuk proses LEM tidak dapat diinput secara inline.',
            ], 422);
        }

        if ($field === 'jtpcs' && ! in_array($prosesName, ['lem', 'sortpacking'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Kolom JT PCS hanya dapat diubah untuk proses LEM dan SORTPACKING.',
            ], 422);
        }

        if ($prosesName === 'lem' && $field === 'jtpcs') {
            $saveField = 'input';
        }

        // Validation: JT Drik cannot be greater than Input
        if ($prosesName !== 'lem') {
            if ($field === 'jtdrik' && $numericValue > $record->input) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kolom JT Drik tidak boleh lebih besar dari Input.',
                ], 422);
            }
            if ($field === 'input' && $record->jtdrik > $numericValue) {
                return response()->json([
                    'success' => false,
                    'message' => 'Input tidak boleh lebih kecil dari JT Drik.',
                ], 422);
            }
        }

        DB::transaction(function () use ($record, $saveField, $numericValue) {
            $record->{$saveField} = $numericValue;
            $record->save();
        });

        $this->calculateDerivedValues($record);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui.',
            'value' => number_format($numericValue, 0, ',', '.'),
            'values' => [
                'input' => (float) $record->input,
                'jtdrik' => (float) $record->jtdrik,
                'jtpcs' => (float) $record->jtpcs,
                'outputdrik' => (float) $record->outputdrik,
                'outputpcs' => (float) $record->outputpcs,
            ],
        ]);
    }

    public function getJobData($job_id)
    {
        // Menggunakan model Proses_Produksi untuk mencari berdasarkan kolom 'job'
        $jobData = ProsesProduksi::where('job', $job_id)->first();

        // Jika data job tersebut pernah diinput dan ditemukan di tabel
        if ($jobData) {
            return response()->json([
                'product' => $jobData->product,
                'designno' => $jobData->designno,
                'po' => $jobData->po,
                'qty' => $jobData->qty,
            ]);
        }

        // Jika job baru dan belum ada datanya sama sekali
        return response()->json(null);
    }

    public function store(Request $request)
    {
        // Validasi: (SEMUA RULES ANDA DISALIN LENGKAP)
        $rules = [
            'proses' => 'required|array|min:1',
            'proses.*' => 'required|string',
            'job' => 'nullable|array',
            'job.*' => 'nullable|string',
            'product' => 'nullable|array',
            'product.*' => 'nullable|string',
            'designno' => 'nullable|array',
            'designno.*' => 'nullable|string',
            'po' => 'nullable|array',
            'po.*' => 'nullable|string',
            'qty' => 'nullable|array',
            'qty.*' => 'nullable|string',
            'pengawas' => 'nullable|array',
            'pengawas.*' => 'nullable|string',
            'shiftpengawas' => 'nullable|array',
            'shiftpengawas.*' => 'nullable|string',
            'upspk' => 'nullable|array',
            'upspk.*' => 'nullable|string',
            'tanggal' => 'nullable|array',
            'tanggal.*' => 'nullable|string',
            'mesin' => 'nullable|array',
            'mesin.*' => 'nullable|string',
            'vendormat' => 'nullable|array',
            'vendormat.*' => 'nullable|string',
            'shift' => 'nullable|array',
            'shift.*' => 'nullable|string',
            'palet' => 'nullable|array',
            'palet.*' => 'nullable|string',
            'set' => 'nullable|array',
            'set.*' => 'nullable|string',
            'operator' => 'nullable|array',
            'operator.*' => 'nullable|string',
            'jumlahtim' => 'nullable|array',
            'jumlahtim.*' => 'nullable|string',
            'run' => 'nullable|array',
            'run.*' => 'nullable|string',
            'finish' => 'nullable|array',
            'finish.*' => 'nullable|string',
            'break' => 'nullable|array',
            'break.*' => 'nullable|string',
            'totaljam' => 'nullable|array',
            'totaljam.*' => 'nullable|string',
            'input' => 'nullable|array',
            'input.*' => 'nullable|string',
            'ket' => 'nullable|array',
            'ket.*' => 'nullable|string',
            'jtdrik' => 'nullable|array',
            'jtdrik.*' => 'nullable|string',
            'target' => 'nullable|array',
            'target.*' => 'nullable|string',
            'karantina' => 'nullable|array',
            'karantina.*' => 'nullable|string',
            'outputdrik' => 'nullable|array',
            'outputdrik.*' => 'nullable|string',
            'type' => 'nullable|array',
            'type.*' => 'nullable|string',
            'toleransi' => 'nullable|array',
            'toleransi.*' => 'nullable|string',
            'ok' => 'nullable|array',
            'ok.*' => 'nullable|string',
            'jtpcs' => 'nullable|array',
            'jtpcs.*' => 'nullable|string',
            'warna' => 'nullable|array',
            'warna.*' => 'nullable|string',
            'banjir' => 'nullable|array',
            'banjir.*' => 'nullable|string',
            'beset' => 'nullable|array',
            'beset.*' => 'nullable|string',
            'notok' => 'nullable|array',
            'notok.*' => 'nullable|string',
            'powder' => 'nullable|array',
            'powder.*' => 'nullable|string',
            'wb' => 'nullable|array',
            'wb.*' => 'nullable|string',
            'uvkasar' => 'nullable|array',
            'uvkasar.*' => 'nullable|string',
            'uvmbleset' => 'nullable|array',
            'uvmbleset.*' => 'nullable|string',
            'tidakuv' => 'nullable|array',
            'tidakuv.*' => 'nullable|string',
            'hotprint' => 'nullable|array',
            'hotprint.*' => 'nullable|string',
            'laminating' => 'nullable|array',
            'laminating.*' => 'nullable|string',
            'laminasikurang' => 'nullable|array',
            'laminasikurang.*' => 'nullable|string',
            'laminasi' => 'nullable|array',
            'laminasi.*' => 'nullable|string',
            'tidakpresisi' => 'nullable|array',
            'tidakpresisi.*' => 'nullable|string',
            'pecah' => 'nullable|array',
            'pecah.*' => 'nullable|string',
            'emboss' => 'nullable|array',
            'emboss.*' => 'nullable|string',
            'porforasi' => 'nullable|array',
            'porforasi.*' => 'nullable|string',
            'sobek' => 'nullable|array',
            'sobek.*' => 'nullable|string',
            'lengket' => 'nullable|array',
            'lengket.*' => 'nullable|string',
            'll' => 'nullable|array',
            'll.*' => 'nullable|string',
            'noteoperator' => 'nullable|array',
            'noteoperator.*' => 'nullable|string',
        ];

        // 1. Validasi data
        try {
            $validated = $request->validate($rules);
            $rowsCount = count($validated['proses'] ?? []);

            if ($rowsCount === 0) {
                return back()->with('error', 'Tidak ada baris untuk disimpan.');
            }

        } catch (ValidationException $e) {
            // Jika validasi gagal, kembalikan seperti biasa
            return back()->withErrors($e->errors())->withInput();
        }

        try {

            DB::transaction(function () use ($validated, $rowsCount) {

                $dataInsert = [];

                for ($i = 0; $i < $rowsCount; $i++) {

                    $row = [];

                    foreach ($validated as $field => $values) {
                        $row[$field] = $values[$i] ?? null;
                    }

                    $row['created_at'] = now();
                    $row['updated_at'] = now();

                    $dataInsert[] = $row;
                }

                ProsesProduksi::insert($dataInsert);
            });

            return back()->with('success', $rowsCount.' data berhasil disimpan.');

        } catch (\Exception $e) {

            Log::error($e->getMessage());

            return back()->with('error', 'Gagal menyimpan data.');
        }
    }

    public function edit($id)
    {
        $prosesProduksi = ProsesProduksi::findOrFail($id);

        return view('proses_produksi.edit', compact('prosesProduksi'));
    }

    public function update(Request $request, $id)
    {
        $prosesProduksi = ProsesProduksi::findOrFail($id);

        $rules = [
            'job' => 'nullable|string',
            'tanggal' => 'nullable|string',
            'product' => 'nullable|string',
            'designno' => 'nullable|string',
            'po' => 'nullable|string',
            'qty' => 'nullable|string',
            'palet' => 'nullable|string',
            'proses' => 'required|string',
            'mesin' => 'nullable|string',
            'shift' => 'nullable|string',
            'vendormat' => 'nullable|string',
            'type' => 'nullable|string',
            'operator' => 'nullable|string',
            'jumlahtim' => 'nullable|string',
            'toleransi' => 'nullable|string',
            'pengawas' => 'nullable|string',
            'shiftpengawas' => 'nullable|string',
            'set' => 'nullable|string',
            'run' => 'nullable|string',
            'finish' => 'nullable|string',
            'break' => 'nullable|string',
            'input' => 'nullable|string',
            'upspk' => 'nullable|string',
            'target' => 'nullable|string',
            'jtdrik' => 'nullable|string',
            'jtpcs' => 'nullable|string',
            'outputdrik' => 'nullable|string',
            'karantina' => 'nullable|string',
            'notok' => 'nullable|string',
            'ok' => 'nullable|string',
            // reject checkboxes
            'warna' => 'nullable|string',
            'banjir' => 'nullable|string',
            'beset' => 'nullable|string',
            'powder' => 'nullable|string',
            'wb' => 'nullable|string',
            'uvkasar' => 'nullable|string',
            'uvmbleset' => 'nullable|string',
            'tidakuv' => 'nullable|string',
            'hotprint' => 'nullable|string',
            'laminating' => 'nullable|string',
            'laminasikurang' => 'nullable|string',
            'laminasi' => 'nullable|string',
            'tidakpresisi' => 'nullable|string',
            'pecah' => 'nullable|string',
            'emboss' => 'nullable|string',
            'porforasi' => 'nullable|string',
            'sobek' => 'nullable|string',
            'lengket' => 'nullable|string',
            'll' => 'nullable|string',
            'noteoperator' => 'nullable|string',
            'ket' => 'nullable|string',
        ];

        $validated = $request->validate($rules);

        // Validation: JT Drik cannot be greater than Input
        $prosesName = strtolower((string) ($validated['proses'] ?? $prosesProduksi->proses));
        if ($prosesName !== 'lem') {
            $inputVal = isset($validated['input']) ? (float) str_replace('.', '', (string) $validated['input']) : (float) $prosesProduksi->input;
            $jtdrikVal = isset($validated['jtdrik']) ? (float) str_replace('.', '', (string) $validated['jtdrik']) : (float) $prosesProduksi->jtdrik;

            if ($jtdrikVal > $inputVal) {
                return back()->withErrors(['jtdrik' => 'JT Drik tidak boleh lebih besar dari Input.'])->withInput();
            }
        }

        // Checkbox yang tidak dicentang tidak terkirim → set null / 0
        $checkboxFields = [
            'warna', 'banjir', 'beset', 'powder', 'wb', 'uvkasar', 'uvmbleset',
            'tidakuv', 'hotprint', 'laminating', 'laminasikurang', 'laminasi',
            'tidakpresisi', 'pecah', 'emboss', 'porforasi', 'sobek', 'lengket', 'll',
        ];
        foreach ($checkboxFields as $field) {
            $validated[$field] = $request->has($field) ? '1' : null;
        }

        $prosesProduksi->update($validated);
        $this->calculateDerivedValues($prosesProduksi);

        return redirect()->route('proses-produksi.index')
            ->with('success', 'Data berhasil diperbarui.');
    }
    public function searchSuggestions(Request $request)
    {
        $keyword = trim((string) $request->input('q', ''));

        $query = ProsesProduksi::query()
            ->select('job')
            ->whereNotNull('job')
            ->where('job', '!=', '')
            ->distinct()
            ->orderBy('job');

        if ($keyword !== '') {
            $query->where('job', 'like', "%{$keyword}%" );
        }

        $jobs = $query->limit(10)->get();

        return response()->json($jobs->map(function ($item) {
            return ['job' => $item->job];
        })->values());
    }
}