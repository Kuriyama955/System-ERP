@extends('layouts.main')

@section('page-title', 'List Karyawan Non Staff')

@section('main-content')

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0" />

    <style>
        .filter-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .7px;
            text-transform: uppercase;
            color: #a1acb8;
            margin-bottom: 5px;
            display: block;
        }

        .stat-card {
            border-radius: 10px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            border: 1px solid transparent;
            height: 100%;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon span {
            font-size: 24px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            line-height: 1.1;
            margin-bottom: 2px;
        }

        .stat-label {
            font-size: 12px;
            color: #a1acb8;
            font-weight: 500;
        }

        .stat-total {
            background: #f0f1ff;
            border-color: #e2e3fd;
        }

        .stat-total .stat-icon {
            background: #e2e3fd;
        }

        .stat-total .stat-icon span,
        .stat-total .stat-value {
            color: #696cff;
        }

        .stat-aktif {
            background: #f0faf4;
            border-color: #c3e6cb;
        }

        .stat-aktif .stat-icon {
            background: #c3e6cb;
        }

        .stat-aktif .stat-icon span,
        .stat-aktif .stat-value {
            color: #28a745;
        }

        .stat-nonaktif {
            background: #fff5f5;
            border-color: #f5c6cb;
        }

        .stat-nonaktif .stat-icon {
            background: #f5c6cb;
        }

        .stat-nonaktif .stat-icon span,
        .stat-nonaktif .stat-value {
            color: #dc3545;
        }

        .filter-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #f0f1ff;
            color: #696cff;
            border: 1px solid #e2e3fd;
            border-radius: 20px;
            padding: 3px 12px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
        }

        .filter-chip-clear {
            background: #fff5f5;
            color: #dc3545;
            border-color: #f5c6cb;
        }

        .table>thead>tr>th {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .65px;
            text-transform: uppercase;
            color: #a1acb8;
            white-space: nowrap;
            padding: 12px 14px;
            border-bottom: 1px solid #e9eaee;
            background-color: #f8f8fb;
            vertical-align: middle;
        }

        .table>tbody>tr>td {
            font-size: 13px;
            padding: 11px 14px;
            vertical-align: middle;
            border-bottom: 1px solid #f2f3f5;
            color: #5d5f6e;
        }

        .table>tbody>tr:last-child>td {
            border-bottom: none;
        }

        .table>tbody>tr:hover {
            background-color: #f8f8fc;
        }

        .badge-dept,
        .badge-npk {
            display: inline-block;
            padding: 3px 9px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-dept {
            background: #eef0ff;
            color: #696cff;
        }

        .badge-npk {
            background: #f0faf4;
            color: #28a745;
            font-weight: 700;
            letter-spacing: .3px;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border-radius: 20px;
            padding: 3px 11px;
            font-size: 11px;
            font-weight: 600;
            border: 1px solid transparent;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-aktif {
            background: #f0faf4;
            color: #28a745;
            border-color: #c3e6cb;
        }

        .status-aktif .status-dot {
            background: #28a745;
        }

        .status-nonaktif {
            background: #fff5f5;
            color: #dc3545;
            border-color: #f5c6cb;
        }

        .status-nonaktif .status-dot {
            background: #dc3545;
        }

        .th-sort {
            color: #a1acb8;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }

        .th-sort:hover {
            color: #696cff;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        @if (isset($errors) && $errors->any())
            <div class="alert alert-danger" role="alert">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
            <div>
                <h4 class="fw-bold mb-1">Daftar Karyawan Non Staff</h4>
                <small class="text-muted">HRD &nbsp;/&nbsp; Karyawan Non Staff</small>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                <span class="material-symbols-outlined align-middle me-1" style="font-size:18px">person_add</span>
                Tambah Karyawan
            </button>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-sm-4">
                <div class="stat-card stat-total">
                    <div class="stat-icon"><span class="material-symbols-outlined">groups</span></div>
                    <div>
                        <div class="stat-value">{{ $totalKaryawan }}</div>
                        <div class="stat-label">Total Karyawan</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="stat-card stat-aktif">
                    <div class="stat-icon"><span class="material-symbols-outlined">check_circle</span></div>
                    <div>
                        <div class="stat-value">{{ $totalKaryawanAktif }}</div>
                        <div class="stat-label">Karyawan Aktif</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="stat-card stat-nonaktif">
                    <div class="stat-icon"><span class="material-symbols-outlined">person_off</span></div>
                    <div>
                        <div class="stat-value">{{ $totalKaryawanNonaktif }}</div>
                        <div class="stat-label">Non-Aktif</div>
                    </div>
                </div>
            </div>
        </div>

        @if (request('departement') || request('status') || request('search'))
            <div class="d-flex gap-2 flex-wrap mb-3">
                @if (request('search'))
                    <span class="filter-chip">
                        <span class="material-symbols-outlined" style="font-size:14px">search</span>
                        "{{ request('search') }}"
                    </span>
                @endif
                @if (request('departement'))
                    <span class="filter-chip">
                        <span class="material-symbols-outlined" style="font-size:14px">filter_alt</span>
                        {{ request('departement') }}
                    </span>
                @endif
                @if (request('status'))
                    <span class="filter-chip">
                        <span class="material-symbols-outlined" style="font-size:14px">toggle_on</span>
                        {{ request('status') === 'AKTIF' ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                @endif
                <a href="{{ route('karyawan.index') }}" class="filter-chip filter-chip-clear">
                    <span class="material-symbols-outlined" style="font-size:14px">close</span>
                    Hapus filter
                </a>
            </div>
        @endif

        <div class="card">
            <div class="card-header py-3 border-bottom">
                <form action="{{ route('karyawan.index') }}" method="GET">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="filter-label">Cari Karyawan</label>
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                    placeholder="Nama / NPK / Jabatan / Bagian...">
                                <span class="input-group-text bg-white">
                                    <span class="material-symbols-outlined text-muted" style="font-size:16px">search</span>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="filter-label">Departement</label>
                            <select name="departement" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Semua Departement</option>
                                @foreach ($departements as $dept)
                                    <option value="{{ $dept }}"
                                        {{ request('departement') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="filter-label">Status</label>
                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="AKTIF" {{ request('status') === 'AKTIF' ? 'selected' : '' }}>Aktif</option>
                                <option value="TIDAK AKTIF"
                                    {{ in_array(request('status'), ['TIDAK AKTIF', 'TIDAKAKTIF'], true) ? 'selected' : '' }}>
                                    Tidak Aktif</option>
                            </select>
                        </div>
                        <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                        <div class="col-auto d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm px-3">
                                <span class="material-symbols-outlined align-middle" style="font-size:15px">search</span>
                                Cari
                            </button>
                            <a href="{{ route('karyawan.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                                <span class="material-symbols-outlined align-middle"
                                    style="font-size:15px">restart_alt</span>
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0" id="employeeTable">
                        <thead>
                            <tr>
                                <th style="width:44px">#</th>
                                <th>
                                    <div class="dropdown d-inline-block">
                                        <a class="th-sort dropdown-toggle" href="#" data-bs-toggle="dropdown">NAMA</a>
                                        <ul class="dropdown-menu dropdown-menu-sm shadow-sm border-0">
                                            @foreach (['ascnama' => 'A → Z', 'descnama' => 'Z → A'] as $sort => $label)
                                                <li>
                                                    <form action="{{ route('karyawan.index') }}" method="GET">
                                                        <input type="hidden" name="search" value="{{ request('search') }}">
                                                        <input type="hidden" name="departement"
                                                            value="{{ request('departement') }}">
                                                        <input type="hidden" name="status" value="{{ request('status') }}">
                                                        <input type="hidden" name="sort_by" value="{{ $sort }}">
                                                        <button type="submit" class="dropdown-item small">{{ $label }}</button>
                                                    </form>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </th>
                                <th>DEPARTEMENT</th>
                                <th>JABATAN</th>
                                <th>GOLONGAN</th>
                                <th>NPK</th>
                                <th>BAGIAN</th>
                                <th>
                                    <div class="dropdown d-inline-block">
                                        <a class="th-sort dropdown-toggle" href="#" data-bs-toggle="dropdown">TGL MASUK</a>
                                        <ul class="dropdown-menu dropdown-menu-sm shadow-sm border-0">
                                            @foreach (['ascmasuk' => 'Terlama', 'descmasuk' => 'Terbaru'] as $sort => $label)
                                                <li>
                                                    <form action="{{ route('karyawan.index') }}" method="GET">
                                                        <input type="hidden" name="search" value="{{ request('search') }}">
                                                        <input type="hidden" name="departement"
                                                            value="{{ request('departement') }}">
                                                        <input type="hidden" name="status" value="{{ request('status') }}">
                                                        <input type="hidden" name="sort_by" value="{{ $sort }}">
                                                        <button type="submit" class="dropdown-item small">{{ $label }}</button>
                                                    </form>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </th>
                                <th>STATUS</th>
                                <th>AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($karyawans as $i => $karyawan)
                                <tr>
                                    <td class="text-muted" style="font-size:12px">
                                        {{ $karyawans->firstItem() + $i }}
                                    </td>
                                    <td style="font-weight:600;color:#333;min-width:160px">{{ $karyawan->nama }}</td>
                                    <td><span class="badge-dept">{{ $karyawan->departement }}</span></td>
                                    <td style="min-width:130px">{{ $karyawan->jabatan }}</td>
                                    <td>{{ $karyawan->golonganjabatan }}</td>
                                    <td><span class="badge-npk">{{ $karyawan->npk }}</span></td>
                                    <td>{{ $karyawan->bagian }}</td>
                                    <td style="white-space:nowrap">
                                        {{ $karyawan->tglmasuk ? \Carbon\Carbon::parse($karyawan->tglmasuk)->format('d M Y') : '-' }}
                                    </td>
                                    <td>
                                        @if ($karyawan->status === 'AKTIF')
                                            <span class="status-pill status-aktif">
                                                <span class="status-dot"></span> Aktif
                                            </span>
                                        @else
                                            <span class="status-pill status-nonaktif">
                                                <span class="status-dot"></span> Non-Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary js-edit-employee"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editEmployeeModal"
                                            data-update-url="{{ route('karyawan.update', $karyawan->id) }}"
                                            data-id="{{ $karyawan->id }}"
                                            data-nama="{{ $karyawan->nama }}"
                                            data-npk="{{ $karyawan->npk }}"
                                            data-departement="{{ $karyawan->departement }}"
                                            data-jabatan="{{ $karyawan->jabatan }}"
                                            data-golonganjabatan="{{ $karyawan->golonganjabatan }}"
                                            data-bagian="{{ $karyawan->bagian }}"
                                            data-tglmasuk="{{ $karyawan->tglmasuk ? \Carbon\Carbon::parse($karyawan->tglmasuk)->format('Y-m-d') : '' }}"
                                            data-status="{{ $karyawan->status }}"
                                            style="padding:4px 14px;font-size:12px">
                                            <span class="material-symbols-outlined align-middle"
                                                style="font-size:13px">edit</span>
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        <span class="material-symbols-outlined d-block mb-2"
                                            style="font-size:42px">manage_search</span>
                                        Tidak ada data karyawan ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 border-top">
                    {{ $karyawans->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom px-4 py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-label-primary rounded-2 p-2 d-flex align-items-center justify-content-center">
                            <span class="material-symbols-outlined text-primary" style="font-size:22px">person_add</span>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" id="addEmployeeModalLabel">Tambah Karyawan Non Staff</h5>
                            <small class="text-muted">Isi data karyawan baru di bawah ini</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('karyawan.store') }}">
                    @csrf
                    <input type="hidden" name="status" value="AKTIF">
                    <div class="modal-body px-4 py-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control" required placeholder="Nama lengkap">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Departement <span class="text-danger">*</span></label>
                                <select name="departement" class="form-select" required>
                                    <option value="" selected disabled>Pilih Departement</option>
                                    @foreach ($departements as $dept)
                                        <option value="{{ $dept }}">{{ $dept }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">NPK <span class="text-danger">*</span></label>
                                <input type="text" name="npk" class="form-control" required placeholder="Nomor Pegawai">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jabatan <span class="text-danger">*</span></label>
                                <input type="text" name="jabatan" class="form-control" required
                                    placeholder="Posisi / Jabatan">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Golongan Jabatan <span class="text-danger">*</span></label>
                                <input type="text" name="golonganjabatan" class="form-control" required
                                    placeholder="Golongan jabatan">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Bagian <span class="text-danger">*</span></label>
                                <input type="text" name="bagian" class="form-control" required placeholder="Bagian">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Masuk <span class="text-danger">*</span></label>
                                <input type="date" name="tglmasuk" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bx bx-x me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Simpan Karyawan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-labelledby="editEmployeeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-bottom px-4 py-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-label-primary rounded-2 p-2 d-flex align-items-center justify-content-center">
                        <span class="material-symbols-outlined text-primary" style="font-size:22px">edit</span>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="editEmployeeModalLabel">Edit Karyawan Non Staff</h5>
                        <small class="text-muted" id="editEmployeeSubtitle">Perbarui informasi karyawan</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form method="POST" id="editEmployeeForm"
                action="{{ old('employee_id') ? route('karyawan.update', old('employee_id')) : '' }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="employee_id" id="editEmployeeId" value="{{ old('employee_id') }}">
                <div class="modal-body px-4 py-3">
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="editEmployeeName" class="form-label fw-semibold">Nama <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="nama" id="editEmployeeName" class="form-control"
                                value="{{ old('nama') }}" required placeholder="Nama lengkap">
                        </div>
                        <div class="col-md-6">
                            <label for="editEmployeeNpk" class="form-label fw-semibold">NPK <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="npk" id="editEmployeeNpk" class="form-control"
                                value="{{ old('npk') }}" required placeholder="Nomor pegawai">
                        </div>
                        <div class="col-md-6">
                            <label for="editEmployeeDepartment" class="form-label fw-semibold">Departemen <span
                                    class="text-danger">*</span></label>
                            <select name="departement" id="editEmployeeDepartment" class="form-select" required>
                                <option value="" disabled {{ old('departement') ? '' : 'selected' }}>Pilih Departemen</option>
                                @foreach ($departements as $departement)
                                    <option value="{{ $departement }}"
                                        {{ old('departement') === $departement ? 'selected' : '' }}>
                                        {{ $departement }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="editEmployeePosition" class="form-label fw-semibold">Jabatan <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="jabatan" id="editEmployeePosition" class="form-control"
                                value="{{ old('jabatan') }}" required placeholder="Posisi / jabatan">
                        </div>
                        <div class="col-md-6">
                            <label for="editEmployeeGrade" class="form-label fw-semibold">Golongan Jabatan <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="golonganjabatan" id="editEmployeeGrade" class="form-control"
                                value="{{ old('golonganjabatan') }}" required placeholder="Golongan jabatan">
                        </div>
                        <div class="col-md-6">
                            <label for="editEmployeeSection" class="form-label fw-semibold">Bagian <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="bagian" id="editEmployeeSection" class="form-control"
                                value="{{ old('bagian') }}" required placeholder="Bagian">
                        </div>
                        <div class="col-md-6">
                            <label for="editEmployeeJoinDate" class="form-label fw-semibold">Tanggal Masuk <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="tglmasuk" id="editEmployeeJoinDate" class="form-control"
                                value="{{ old('tglmasuk') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="editEmployeeStatus" class="form-label fw-semibold">Status <span
                                    class="text-danger">*</span></label>
                            <select name="status" id="editEmployeeStatus" class="form-select" required>
                                <option value="AKTIF" {{ old('status') === 'AKTIF' ? 'selected' : '' }}>Aktif</option>
                                <option value="TIDAKAKTIF"
                                    {{ in_array(old('status'), ['TIDAKAKTIF', 'TIDAK AKTIF'], true) ? 'selected' : '' }}>
                                    Tidak Aktif
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="material-symbols-outlined align-middle me-1"
                            style="font-size:17px">save</span>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.js-edit-employee').forEach(function (button) {
        button.addEventListener('click', function () {
            const form = document.getElementById('editEmployeeForm');
            const status = button.dataset.status === 'TIDAKAKTIF' || button.dataset.status === 'TIDAK AKTIF'
                ? 'TIDAKAKTIF'
                : button.dataset.status;

            form.action = button.dataset.updateUrl;
            document.getElementById('editEmployeeId').value = button.dataset.id;
            document.getElementById('editEmployeeName').value = button.dataset.nama;
            document.getElementById('editEmployeeNpk').value = button.dataset.npk;
            document.getElementById('editEmployeeDepartment').value = button.dataset.departement;
            document.getElementById('editEmployeePosition').value = button.dataset.jabatan;
            document.getElementById('editEmployeeGrade').value = button.dataset.golonganjabatan;
            document.getElementById('editEmployeeSection').value = button.dataset.bagian;
            document.getElementById('editEmployeeJoinDate').value = button.dataset.tglmasuk;
            document.getElementById('editEmployeeStatus').value = status;
            document.getElementById('editEmployeeSubtitle').textContent = button.dataset.nama;
        });
    });

    @if ($errors->any() && old('employee_id'))
        document.addEventListener('DOMContentLoaded', function () {
            const editModal = document.getElementById('editEmployeeModal');
            if (editModal && window.bootstrap) {
                bootstrap.Modal.getOrCreateInstance(editModal).show();
            }
        });
    @endif
</script>


@endsection
