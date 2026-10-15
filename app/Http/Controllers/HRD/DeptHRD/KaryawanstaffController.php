<?php

namespace App\Http\Controllers\HRD\DeptHRD;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Karyawanstaff;
use App\Models\OnboardingTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class KaryawanstaffController extends Controller
{
    public function index(Request $request)
    {
        $totalKaryawan = Karyawanstaff::count();
        $totalKaryawanAktif = Karyawanstaff::where('status', 'AKTIF')->count();
        $totalKaryawanNonaktif = Karyawanstaff::whereIn('status', ['TIDAK AKTIF', 'TIDAKAKTIF'])->count();

        $query = Karyawanstaff::query();
        // Pencarian berdasarkan ID atau produk

        if ($request->has('search')) {
            $searchValue = $request->input('search');

            $query->where(function ($q) use ($searchValue) {
                $q->where('id', $searchValue)
                    ->orWhere('nama', 'like', '%'.$searchValue.'%')
                    ->orWhere('departement', 'like', '%'.$searchValue.'%');
            });
        }

        $status = $request->input('status', null);
        if (in_array($status, ['TIDAK AKTIF', 'TIDAKAKTIF'], true)) {
            $query->whereIn('status', ['TIDAKAKTIF', 'TIDAK AKTIF']);
        } elseif ($status === 'AKTIF') {
            $query->where('status', 'AKTIF');
        }
        $departement = $request->input('departement', null);
        if ($departement) {
            $query->where('departement', $departement);
        }

        // Filter produk berdasarkan ascending (asc) atau descending (desc)
        $sortBy = $request->input('sort_by', null);
        if (! $sortBy) {
            // Jika tidak ada query pencarian, urutkan berdasarkan updated_at terbaru
            $query->orderBy('id');
        } elseif ($sortBy == 'asc') {
            // Jika ada query pencarian dan sort_by = asc, urutkan berdasarkan id secara ascending
            $query->orderBy('id');
        } elseif ($sortBy == 'desc') {
            // Jika ada query pencarian dan sort_by = desc, urutkan berdasarkan id secara descending
            $query->orderByDesc('id');
        } elseif ($sortBy == 'ascnama') {
            // Jika ada query pencarian dan sort_by = asc, urutkan berdasarkan product secara ascending
            $query->orderBy('nama');
        } elseif ($sortBy == 'descnama') {
            // Jika ada query pencarian dan sort_by = desc, urutkan berdasarkan product secara descending
            $query->orderByDesc('nama');
        }

        $karyawanstaffs = $query->paginate(15);
        $departements = Departemen::query()->orderBy('nama_departemen')->pluck('nama_departemen');

        return view('role.hrd.karyawanstaff.karyawanstaffdept.listkaryawan', compact(
            'karyawanstaffs',
            'totalKaryawan',
            'totalKaryawanAktif',
            'totalKaryawanNonaktif',
            'departements'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'npk' => ['required', 'string', 'max:100'],
            'departement' => ['required', 'string', 'exists:departemens,nama_departemen'],
            'jabatan' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
            'tmk' => ['required', 'date'],
        ]);

        $karyawanstaff = DB::transaction(function () use ($validated) {
            $karyawanstaff = new Karyawanstaff;
            $karyawanstaff->nama = $validated['nama'];
            $karyawanstaff->status = 'AKTIF';
            $karyawanstaff->npk = $validated['npk'];
            $karyawanstaff->tmk = $validated['tmk'];
            $karyawanstaff->jabatan = $validated['jabatan'];
            $karyawanstaff->lokasi = $validated['lokasi'];
            $karyawanstaff->departement = $validated['departement'];
            $karyawanstaff->save();

            return $karyawanstaff;
        });

        return redirect()->route('staff.show', $karyawanstaff->id)
            ->with('success', 'Karyawan berhasil dibuat.');

    }

    public function edit($id)
    {
        $karyawanstaff = Karyawanstaff::with(['personal', 'facilities'])->findOrFail($id);
        $departements = Departemen::query()->orderBy('nama_departemen')->pluck('nama_departemen');

        return view('role.hrd.karyawanstaff.karyawanstaffdept.editkaryawan', compact('karyawanstaff', 'departements'));
    }

    public function show($id)
    {
        $karyawanstaff = Karyawanstaff::with([
            'personal',
            'facilities',
            'onboardingAssignments.onboardingTask',
        ])->findOrFail($id);
        $availableOnboardingTasks = OnboardingTask::query()
            ->where('is_active', true)
            ->whereDoesntHave('assignments', function ($query) use ($karyawanstaff) {
                $query->where('karyawanstaff_id', $karyawanstaff->id);
            })
            ->orderBy('nama_task')
            ->get();

        return view('role.hrd.karyawanstaff.karyawanstaffdept.detailkaryawan', compact(
            'karyawanstaff',
            'availableOnboardingTasks'
        ));
    }

    public function update(Request $request, $id)
    {
        $karyawanstaff = Karyawanstaff::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'nama' => ['required', 'string', 'max:255'],
            'npk' => ['required', 'string', 'max:100'],
            'departement' => ['required', 'string', 'exists:departemens,nama_departemen'],
            'jabatan' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
            'tmk' => ['required', 'date'],
            'status' => ['required', 'in:AKTIF,TIDAK AKTIF,TIDAKAKTIF'],
            'profile' => ['nullable', 'array'],
            'profile.personal' => ['nullable', 'array'],
            'profile.personal.nik' => ['nullable', 'string', 'max:100'],
            'profile.personal.birth_place' => ['nullable', 'string', 'max:100'],
            'profile.personal.birth_date' => ['nullable', 'date'],
            'profile.personal.gender' => ['nullable', 'string', 'max:50'],
            'profile.personal.marital_status' => ['nullable', 'string', 'max:30'],
            'profile.personal.religion' => ['nullable', 'string', 'max:50'],
            'profile.personal.blood_type' => ['nullable', 'string', 'max:5'],
            'profile.personal.phone' => ['nullable', 'string', 'max:20'],
            'profile.personal.email' => ['nullable', 'email', 'max:255'],
            'profile.personal.address' => ['nullable', 'string', 'max:2000'],
            'profile.personal.ktp_address' => ['nullable', 'string', 'max:2000'],
            'profile.personal.emergency_name' => ['nullable', 'string', 'max:255'],
            'profile.personal.emergency_relationship' => ['nullable', 'string', 'max:100'],
            'profile.personal.emergency_phone' => ['nullable', 'string', 'max:50'],
            'profile.personal.bank_name' => ['nullable', 'string', 'max:100'],
            'profile.personal.bank_branch' => ['nullable', 'string', 'max:100'],
            'profile.personal.bank_account_number' => ['nullable', 'string', 'max:50'],
            'profile.personal.bank_account_name' => ['nullable', 'string', 'max:255'],
            'profile.personal.education' => ['nullable', 'string', 'max:255'],
            'profile.personal.experience' => ['nullable', 'string', 'max:255'],
            'profile.personal.additional_information' => ['nullable', 'string', 'max:255'],
            'profile.facilities' => ['nullable', 'array', 'max:50'],
            'profile.facilities.*' => ['array'],
            'profile.facilities.*.id' => [
                'nullable',
                'integer',
                Rule::exists('facilities', 'id')->where('karyawanstaff_id', $karyawanstaff->id),
            ],
            'profile.facilities.*.item' => ['nullable', 'string', 'max:255'],
            'profile.facilities.*.kategori' => ['nullable', 'string', 'max:100'],
            'profile.facilities.*.nomor' => ['nullable', 'string', 'max:100'],
            'profile.facilities.*.tanggal' => ['nullable', 'date'],
            'profile.facilities.*.kondisi' => ['nullable', 'string', 'max:50'],
            'profile.facilities.*.status' => ['nullable', 'string', 'max:100'],
            'profile.facilities.*.tanggal_pengembalian' => ['nullable', 'date'],
            'profile.facilities.*.catatan' => ['nullable', 'string', 'max:2000'],
        ]);
        $validator->after(function ($validator) use ($request, $karyawanstaff) {
            foreach ($request->input('profile.facilities', []) as $index => $facility) {
                if (! is_array($facility)) {
                    continue;
                }

                $hasOtherValue = collect($facility)
                    ->except('id', 'item')
                    ->contains(fn ($value) => filled($value));

                if ($hasOtherValue && blank($facility['item'] ?? null)) {
                    $validator->errors()->add(
                        "profile.facilities.{$index}.item",
                        'Nama fasilitas wajib diisi jika data fasilitas lainnya diisi.'
                    );
                }
            }
        });
        $validated = $validator->validate();

        $karyawanstaff->nama = $validated['nama'];
        $karyawanstaff->status = $validated['status'];
        $karyawanstaff->npk = $validated['npk'];
        $karyawanstaff->tmk = $validated['tmk'];
        $karyawanstaff->jabatan = $validated['jabatan'];
        $karyawanstaff->lokasi = $validated['lokasi'];
        $karyawanstaff->departement = $validated['departement'];

        DB::transaction(function () use ($karyawanstaff, $validated) {
            $karyawanstaff->save();

            if (array_key_exists('personal', $validated['profile'] ?? [])) {
                $personal = $validated['profile']['personal'];
                $karyawanstaff->personal()->updateOrCreate([], [
                    'nik' => $personal['nik'] ?? null,
                    'tempat_lahir' => $personal['birth_place'] ?? null,
                    'tanggal_lahir' => $personal['birth_date'] ?? null,
                    'jenis_kelamin' => $personal['gender'] ?? null,
                    'status_pernikahan' => $personal['marital_status'] ?? null,
                    'agama' => $personal['religion'] ?? null,
                    'golongan_darah' => $personal['blood_type'] ?? null,
                    'no_telepon' => $personal['phone'] ?? null,
                    'email_pribadi' => $personal['email'] ?? null,
                    'alamat' => $personal['address'] ?? null,
                    'alamat_ktp' => $personal['ktp_address'] ?? null,
                    'nama_kontak_darurat' => $personal['emergency_name'] ?? null,
                    'hubungan_kontak_darurat' => $personal['emergency_relationship'] ?? null,
                    'telepon_kontak_darurat' => $personal['emergency_phone'] ?? null,
                    'nama_bank' => $personal['bank_name'] ?? null,
                    'cabang_bank' => $personal['bank_branch'] ?? null,
                    'nomor_rekening' => $personal['bank_account_number'] ?? null,
                    'nama_pemilik_rekening' => $personal['bank_account_name'] ?? null,
                    'pendidikan' => $personal['education'] ?? null,
                    'pengalaman_kerja' => $personal['experience'] ?? null,
                    'informasi_tambahan' => $personal['additional_information'] ?? null,
                ]);
            }

            $facilityRows = array_values(array_filter(
                $validated['profile']['facilities'] ?? [],
                fn ($row) => collect($row)
                    ->except('id')
                    ->contains(fn ($value) => filled($value))
            ));
            $submittedFacilityIds = collect($facilityRows)
                ->pluck('id')
                ->filter()
                ->all();

            $facilitiesToDelete = $karyawanstaff->facilities();
            if ($submittedFacilityIds) {
                $facilitiesToDelete->whereNotIn('id', $submittedFacilityIds);
            }
            $facilitiesToDelete->delete();

            foreach ($facilityRows as $facility) {
                $attributes = [
                    'nama_fasilitas' => $facility['item'] ?? '',
                    'kategori' => $facility['kategori'] ?? null,
                    'nomor_aset' => $facility['nomor'] ?? null,
                    'tanggal_pemberian' => $facility['tanggal'] ?? null,
                    'kondisi' => $facility['kondisi'] ?? null,
                    'status' => filled($facility['status'] ?? null) ? $facility['status'] : 'Diberikan',
                    'tanggal_pengembalian' => $facility['tanggal_pengembalian'] ?? null,
                    'keterangan' => $facility['catatan'] ?? null,
                ];

                if (! empty($facility['id'])) {
                    $karyawanstaff->facilities()->whereKey($facility['id'])->update($attributes);
                } else {
                    $karyawanstaff->facilities()->create($attributes);
                }
            }

        });

        return redirect()->route('staff.show', $karyawanstaff->id)
            ->with('success', 'Profil karyawan berhasil diperbarui.');
    }
}
