<?php

namespace App\Http\Controllers\HRD\DeptHRD;

use App\Http\Controllers\Controller;
use App\Models\Karyawanstaff;
use App\Models\OnboardingTask;
use App\Models\OnboardingTaskKaryawan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function index(): View
    {
        $onboardingTasks = OnboardingTask::query()
            ->withCount('assignments')
            ->orderBy('nama_task')
            ->get();

        return view('role.hrd.onboarding.index', compact('onboardingTasks'));
    }

    public function show(OnboardingTask $onboardingTask): View
    {
        $onboardingTask->load([
            'assignments' => fn ($query) => $query->with('karyawanstaff')->latest(),
        ]);
        $karyawanstaffs = Karyawanstaff::query()
            ->whereDoesntHave('onboardingAssignments', function ($query) use ($onboardingTask) {
                $query->where('onboarding_task_id', $onboardingTask->id);
            })
            ->orderBy('nama')
            ->get(['id', 'nama', 'npk', 'departement']);

        return view('role.hrd.onboarding.show', compact('onboardingTask', 'karyawanstaffs'));
    }

    public function storeTask(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_task' => ['required', 'string', 'max:255'],
        ]);

        OnboardingTask::create($validated);

        return redirect()->route('onboarding.index')->with('success', 'Master task berhasil ditambahkan.');
    }

    public function updateTask(Request $request, OnboardingTask $onboardingTask): RedirectResponse
    {
        $validated = $request->validate([
            'nama_task' => ['required', 'string', 'max:255'],
        ]);

        $onboardingTask->update($validated);

        return redirect()->route('onboarding.index')->with('success', 'Master task berhasil diperbarui.');
    }

    public function toggleTask(OnboardingTask $onboardingTask): RedirectResponse
    {
        $onboardingTask->update(['is_active' => ! $onboardingTask->is_active]);

        $message = $onboardingTask->is_active
            ? 'Master task berhasil diaktifkan.'
            : 'Master task berhasil dinonaktifkan.';

        return redirect()->route('onboarding.index')->with('success', $message);
    }

    public function assign(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'onboarding_task_id' => [
                'required',
                'integer',
                Rule::exists('onboarding_tasks', 'id')->where('is_active', true),
            ],
            'karyawanstaff_ids' => ['required', 'array', 'min:1'],
            'karyawanstaff_ids.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('karyawanstaffs', 'id'),
            ],
        ]);

        DB::transaction(function () use ($validated): void {
            foreach ($validated['karyawanstaff_ids'] as $karyawanstaffId) {
                OnboardingTaskKaryawan::query()->firstOrCreate(
                    [
                        'onboarding_task_id' => $validated['onboarding_task_id'],
                        'karyawanstaff_id' => $karyawanstaffId,
                    ],
                    ['status' => 'belum selesai']
                );
            }
        });

        return redirect()
            ->route('onboarding.tasks.show', $validated['onboarding_task_id'])
            ->with('success', 'Task berhasil ditugaskan kepada karyawan.');
    }

    public function assignToStaff(Request $request, Karyawanstaff $karyawanstaff): RedirectResponse
    {
        $validated = $request->validate([
            'onboarding_task_id' => [
                'required',
                'integer',
                Rule::exists('onboarding_tasks', 'id')->where('is_active', true),
                Rule::unique('onboarding_task_karyawan', 'onboarding_task_id')
                    ->where('karyawanstaff_id', $karyawanstaff->id),
            ],
        ]);

        $karyawanstaff->onboardingAssignments()->create([
            'onboarding_task_id' => $validated['onboarding_task_id'],
            'status' => 'belum selesai',
        ]);

        return redirect()
            ->route('staff.show', $karyawanstaff->id)
            ->with('success', 'Task onboarding berhasil ditambahkan.');
    }

    public function updateStatus(Request $request, OnboardingTaskKaryawan $assignment): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['belum selesai', 'selesai'])],
        ]);

        $assignment->update($validated);

        return redirect()->back()->with('success', 'Status task onboarding berhasil diperbarui.');
    }
}
