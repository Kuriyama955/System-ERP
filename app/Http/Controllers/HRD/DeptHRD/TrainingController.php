<?php

namespace App\Http\Controllers\HRD\DeptHRD;

use App\Http\Controllers\Controller;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            ->orderByDesc('tanggal_aktual')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('role.hrd.training.index', compact('trainings', 'totalTraining'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateTraining($request, 'create');
        unset($validated['_form']);

        Training::create($validated);

        return redirect()->route('training.index', $request->query())
            ->with('success', 'Data training berhasil ditambahkan.');
    }

    public function update(Request $request, Training $training)
    {
        $validated = $this->validateTraining($request, 'edit');
        unset($validated['_form']);

        $training->update($validated);

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
        ]);
    }
}
