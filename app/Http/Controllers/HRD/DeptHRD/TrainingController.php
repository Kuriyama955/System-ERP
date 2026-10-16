<?php

namespace App\Http\Controllers\HRD\DeptHRD;

use App\Http\Controllers\Controller;
use App\Models\Karyawanstaff;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TrainingController extends Controller
{
    public function index(Request $request)
    {
        $totalTraining = Training::count();

        $query = Training::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($query) use ($search) {
                $query->where('nama_pelatihan', 'like', '%'.$search.'%')
                    ->orWhere('tema', 'like', '%'.$search.'%');
            });
        }

        $trainings = $query
            ->with('participants:karyawanstaff_id,training_id')
            ->orderByDesc('tanggal_aktual')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $karyawanstaffs = Karyawanstaff::query()
            ->orderBy('nama')
            ->get(['id', 'nama', 'npk', 'departement']);

        return view('role.hrd.training.index', compact('trainings', 'totalTraining', 'karyawanstaffs'));
    }

    public function show(Training $training): View
    {
        $training->load([
            'participants' => fn ($query) => $query
                ->with('karyawanstaff')
                ->orderBy('id'),
        ]);

        $preTestAverage = $training->participants
            ->pluck('nilai_pre_test')
            ->filter(fn ($score) => $score !== null)
            ->average();
        $postTestAverage = $training->participants
            ->pluck('nilai_post_test')
            ->filter(fn ($score) => $score !== null)
            ->average();

        return view('role.hrd.training.show', compact(
            'training',
            'preTestAverage',
            'postTestAverage'
        ));
    }

    public function store(Request $request)
    {
        $validated = $this->validateTraining($request, 'create');
        $participantIds = $validated['karyawanstaff_ids'];
        unset($validated['_form']);
        unset($validated['karyawanstaff_ids']);

        $training = DB::transaction(function () use ($validated, $participantIds) {
            $training = Training::create($validated);
            $training->participants()->createMany(
                collect($participantIds)
                    ->map(fn ($karyawanstaffId) => [
                        'karyawanstaff_id' => $karyawanstaffId,
                    ])
                    ->all()
            );

            return $training;
        });

        return redirect()->route('training.show', $training)
            ->with('success', 'Data training berhasil ditambahkan.');
    }

    public function update(Request $request, Training $training)
    {
        $validated = $this->validateTraining($request, 'edit');
        $participantIds = $validated['karyawanstaff_ids'];
        unset($validated['_form']);
        unset($validated['karyawanstaff_ids']);

        DB::transaction(function () use ($training, $validated, $participantIds): void {
            $training->update($validated);

            $participants = $training->participants()->get();
            $removedParticipants = $participants
                ->whereNotIn('karyawanstaff_id', $participantIds);

            foreach ($removedParticipants as $participant) {
                $participant->delete();
            }

            $existingStaffIds = $participants
                ->whereIn('karyawanstaff_id', $participantIds)
                ->pluck('karyawanstaff_id')
                ->all();

            $training->participants()->createMany(
                collect($participantIds)
                    ->diff($existingStaffIds)
                    ->map(fn ($karyawanstaffId) => [
                        'karyawanstaff_id' => $karyawanstaffId,
                    ])
                    ->all()
            );
        });

        return redirect()->route('training.index', $request->query())
            ->with('success', 'Data training berhasil diperbarui.');
    }

    public function destroy(Training $training)
    {
        $training->delete();

        return redirect()->route('training.index', request()->query())
            ->with('success', 'Data training berhasil dihapus.');
    }

    private function validateTraining(Request $request, string $form): array
    {
        return $request->validate([
            '_form' => ['required', Rule::in([$form])],
            'nama_pelatihan' => ['required', 'string', 'max:255'],
            'tema' => ['nullable', 'string', 'max:255'],
            'tanggal_aktual' => ['required', 'date'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'karyawanstaff_ids' => [
                'required',
                'array',
                'min:1',
            ],
            'karyawanstaff_ids.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('karyawanstaffs', 'id'),
            ],
        ]);
    }
}
