<?php

namespace App\Http\Controllers\HRD\DeptHRD;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Karyawan;
use App\Models\Karyawanstaff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DepartemenController extends Controller
{
    public function index(Request $request)
    {
        $query = Departemen::query();
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where('nama_departemen', 'like', '%'.$search.'%');
        }

        $departemens = $query
            ->orderBy('nama_departemen')
            ->paginate(15)
            ->withQueryString();

        return view('role.hrd.departemen.index', compact('departemens'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_departemen' => ['required', 'string', 'max:255', 'unique:departemens,nama_departemen'],
        ], [
            'nama_departemen.required' => 'Nama departemen wajib diisi.',
            'nama_departemen.max' => 'Nama departemen maksimal 255 karakter.',
            'nama_departemen.unique' => 'Nama departemen tersebut sudah terdaftar. Silakan gunakan nama lain.',
        ]);

        Departemen::create($validated);

        return redirect()->route('departemen.index')->with('success', 'Data departemen berhasil ditambahkan.');
    }

    public function update(Request $request, Departemen $departemen)
    {
        $validated = $request->validate([
            'nama_departemen' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departemens', 'nama_departemen')->ignore($departemen->id),
            ],
        ], [
            'nama_departemen.required' => 'Nama departemen wajib diisi.',
            'nama_departemen.max' => 'Nama departemen maksimal 255 karakter.',
            'nama_departemen.unique' => 'Nama departemen tersebut sudah terdaftar. Silakan gunakan nama lain.',
        ]);

        $oldName = $departemen->nama_departemen;
        $newName = $validated['nama_departemen'];

        DB::transaction(function () use ($departemen, $oldName, $newName) {
            Karyawan::where('departement', $oldName)->update(['departement' => $newName]);
            Karyawanstaff::where('departement', $oldName)->update(['departement' => $newName]);
            $departemen->update(['nama_departemen' => $newName]);
        });

        return redirect()->route('departemen.index')->with('success', 'Data departemen berhasil diperbarui.');
    }

    public function destroy(Departemen $departemen)
    {
        $hasKaryawan = Karyawan::where('departement', $departemen->nama_departemen)->exists();
        $hasKaryawanstaff = Karyawanstaff::where('departement', $departemen->nama_departemen)->exists();

        if ($hasKaryawan || $hasKaryawanstaff) {
            return redirect()->route('departemen.index')
                ->with('error', 'Departemen tidak dapat dihapus karena masih digunakan pada data karyawan.');
        }

        $departemen->delete();

        return redirect()->route('departemen.index')->with('success', 'Data departemen berhasil dihapus.');
    }
}
