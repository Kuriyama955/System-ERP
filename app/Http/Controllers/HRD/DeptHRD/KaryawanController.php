<?php

namespace App\Http\Controllers\HRD\DeptHRD;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $totalKaryawan = Karyawan::count();
        $totalKaryawanAktif = Karyawan::where('status', 'AKTIF')->count();
        $totalKaryawanNonaktif = Karyawan::whereIn('status', ['TIDAK AKTIF', 'TIDAKAKTIF'])->count();

        $query = Karyawan::query();
        $searchValue = $request->input('search');
        if ($searchValue !== null && $searchValue !== '') {
            $query->where(function ($query) use ($searchValue) {
                $query->where('id', $searchValue)
                    ->orWhere('nama', 'like', '%'.$searchValue.'%')
                    ->orWhere('npk', 'like', '%'.$searchValue.'%')
                    ->orWhere('departement', 'like', '%'.$searchValue.'%')
                    ->orWhere('jabatan', 'like', '%'.$searchValue.'%')
                    ->orWhere('golonganjabatan', 'like', '%'.$searchValue.'%')
                    ->orWhere('bagian', 'like', '%'.$searchValue.'%');
            });
        }

        $status = $request->input('status');
        if (in_array($status, ['TIDAK AKTIF', 'TIDAKAKTIF'], true)) {
            $query->whereIn('status', ['TIDAK AKTIF', 'TIDAKAKTIF']);
        } elseif ($status) {
            $query->where('status', $status);
        }

        if ($request->filled('departement')) {
            $query->where('departement', $request->input('departement'));
        }

        switch ($request->input('sort_by')) {
            case 'asc':
                $query->orderBy('id');
                break;
            case 'descnama':
                $query->orderByDesc('nama');
                break;
            case 'ascnama':
                $query->orderBy('nama');
                break;
            case 'ascmasuk':
                $query->orderBy('tglmasuk')->orderBy('id');
                break;
            case 'descmasuk':
                $query->orderByDesc('tglmasuk')->orderByDesc('id');
                break;
            default:
                $query->orderByDesc('id');
                break;
        }

        $karyawans = $query->paginate(15)->withQueryString();
        $departements = Departemen::query()->orderBy('nama_departemen')->pluck('nama_departemen');

        return view('role.hrd.karyawan.karyawandept.listkaryawan', compact(
            'karyawans',
            'totalKaryawan',
            'totalKaryawanAktif',
            'totalKaryawanNonaktif',
            'departements'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'departement' => ['required', 'string', 'exists:departemens,nama_departemen'],
        ]);

        $karyawan = new Karyawan;
        $karyawan->nama = $request->nama;
        $karyawan->npk = $request->npk;
        $karyawan->status = $request->status;
        $karyawan->jabatan = $request->jabatan;
        $karyawan->golonganjabatan = $request->golonganjabatan;
        $karyawan->departement = $request->departement;
        $karyawan->bagian = $request->bagian;
        $karyawan->tglmasuk = $request->tglmasuk;
        $karyawan->save();
        return redirect()->route('karyawan.index');
    }

    public function edit($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $departements = Departemen::query()->orderBy('nama_departemen')->pluck('nama_departemen');

        return view('role.hrd.karyawan.karyawandept.editkaryawan', compact('karyawan', 'departements'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'departement' => ['required', 'string', 'exists:departemens,nama_departemen'],
        ]);

        $karyawan = Karyawan::findOrFail($id);
        $karyawan->nama = $request->nama;
        $karyawan->npk = $request->npk;
        $karyawan->status = $request->status;
        $karyawan->jabatan = $request->jabatan;
        $karyawan->golonganjabatan = $request->golonganjabatan;
        $karyawan->departement = $request->departement;
        $karyawan->bagian = $request->bagian;
        $karyawan->tglmasuk = $request->tglmasuk;
        $karyawan->save();
        return redirect()->route('karyawan.index');
    }
}
