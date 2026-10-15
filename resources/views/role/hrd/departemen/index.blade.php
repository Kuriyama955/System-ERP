@extends('layouts.main')

@section('page-title', 'Manajemen Departemen')

@section('main-content')
    <style>
        /* ---------- Card ---------- */
        .department-card {
            border: 1px solid #eceef1;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(50, 50, 93, .05);
        }

        /* ---------- Focus states ---------- */
        .department-toolbar .form-control:focus,
        .department-form .form-control:focus {
            border-color: #696cff;
            box-shadow: 0 0 0 .2rem rgba(105, 108, 255, .12);
        }

        /* ---------- Table ---------- */
        .department-table > thead > tr > th {
            position: sticky;
            top: 0;
            z-index: 1;
            padding: 12px 16px;
            border-bottom: 1px solid #e9eaee;
            background: #f8f8fb;
            color: #a1acb8;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .65px;
            text-transform: uppercase;
            white-space: nowrap;
            vertical-align: middle;
        }

        .department-table-scroll {
            max-height: min(60vh, 480px);
            overflow-y: auto;
        }

        .department-table > tbody > tr > td {
            padding: 14px 16px;
            border-bottom: 1px solid #f2f3f5;
            color: #5d5f6e;
            font-size: 13px;
            vertical-align: middle;
        }

        .department-table > tbody > tr:last-child > td {
            border-bottom: 0;
        }

        .department-table > tbody > tr {
            transition: background .12s ease;
        }

        .department-table > tbody > tr:hover {
            background: #f8f8fc;
        }

        .department-name {
            color: #32334a;
            font-weight: 600;
        }

        .department-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            padding: 0;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- ===== Header ===== --}}
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
            <div>
                <h4 class="fw-bold mb-1">Manajemen Departemen</h4>
                <small class="text-muted">HRD &nbsp;/&nbsp; Departemen &nbsp;·&nbsp; Kelola departemen yang digunakan pada data karyawan</small>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#departmentModal">
                <i class="bx bx-plus me-1"></i> Tambah Departemen
            </button>
        </div>

        {{-- ===== Alerts ===== --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible js-auto-dismiss" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif
        @if (isset($errors) && $errors->any() && !old('_form'))
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- ===== Active filter ===== --}}
        @if (request()->filled('search'))
            <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
                <small class="text-muted">Hasil pencarian untuk:</small>
                <span class="badge rounded-pill bg-primary">{{ request('search') }}</span>
                <a href="{{ route('departemen.index') }}" class="btn btn-sm btn-outline-danger">Hapus filter</a>
            </div>
        @endif

        {{-- ===== Table card ===== --}}
        <div class="card department-card overflow-hidden">
            <div class="card-header department-toolbar d-flex align-items-center justify-content-between flex-wrap gap-3 border-bottom">
                <div>
                    <h5 class="mb-1">Daftar Departemen</h5>
                    <small class="text-muted">Cari, ubah, dan hapus data departemen</small>
                </div>
                <form action="{{ route('departemen.index') }}" method="GET" class="w-100" style="max-width:320px">
                    <label for="searchDepartemen" class="visually-hidden">Cari departemen</label>
                    <div class="input-group">
                        <input id="searchDepartemen" type="search" name="search" class="form-control"
                            value="{{ request('search') }}" placeholder="Cari nama departemen..." autocomplete="off">
                        <button type="submit" class="btn btn-outline-primary" aria-label="Cari departemen">
                            <i class="bx bx-search"></i>
                        </button>
                        @if (request()->filled('search'))
                            <a href="{{ route('departemen.index') }}" class="btn btn-outline-secondary"
                                aria-label="Hapus kata pencarian">
                                <i class="bx bx-x"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="table-responsive department-table-scroll" role="region"
                aria-label="Daftar departemen" tabindex="0">
                <table class="table department-table mb-0">
                    <thead>
                        <tr>
                            <th style="width:64px">#</th>
                            <th>Nama Departemen</th>
                            <th class="text-end" style="width:140px">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($departemens as $departemen)
                            <tr>
                                <td class="text-muted">{{ $departemens->firstItem() + $loop->index }}</td>
                                <td class="department-name">{{ $departemen->nama_departemen }}</td>
                                <td class="text-end text-nowrap">
                                    <button type="button"
                                        class="btn btn-sm btn-outline-primary department-action-btn js-edit-department"
                                        data-bs-toggle="modal" data-bs-target="#departmentModal"
                                        data-url="{{ route('departemen.update', $departemen) }}"
                                        data-id="{{ $departemen->id }}"
                                        data-name="{{ $departemen->nama_departemen }}"
                                        aria-label="Ubah {{ $departemen->nama_departemen }}"
                                        title="Ubah departemen">
                                        <i class="bx bx-edit-alt"></i>
                                    </button>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-danger department-action-btn js-delete-department"
                                        data-bs-toggle="modal" data-bs-target="#deleteDepartmentModal"
                                        data-url="{{ route('departemen.destroy', $departemen) }}"
                                        data-name="{{ $departemen->nama_departemen }}"
                                        aria-label="Hapus {{ $departemen->nama_departemen }}"
                                        title="Hapus departemen">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-5 text-center">
                                    <h6 class="fw-semibold mb-1">
                                        {{ request()->filled('search') ? 'Departemen tidak ditemukan' : 'Belum ada data departemen' }}
                                    </h6>
                                    <p class="text-muted mb-3">
                                        {{ request()->filled('search') ? 'Coba gunakan kata kunci lain atau hapus filter pencarian.' : 'Mulai dengan menambahkan data departemen.' }}
                                    </p>
                                    @if (request()->filled('search'))
                                        <a href="{{ route('departemen.index') }}"
                                            class="btn btn-sm btn-outline-primary">Atur ulang pencarian</a>
                                    @else
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#departmentModal">
                                            <i class="bx bx-plus me-1"></i> Tambah Departemen
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($departemens->total() > 0)
                <div class="px-4 py-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <small class="text-muted">
                        Menampilkan {{ $departemens->firstItem() }}–{{ $departemens->lastItem() }}
                        dari {{ $departemens->total() }} departemen
                    </small>
                    @if ($departemens->hasPages())
                        {{ $departemens->links('pagination::bootstrap-5') }}
                    @endif
                </div>
            @endif
        </div>

        @php
            $isEditing = old('_form') === 'edit_department' && old('department_id');
            $editingDepartmentId = $isEditing ? old('department_id') : null;
        @endphp

        {{-- ===== Add / edit modal ===== --}}
        <div class="modal fade" id="departmentModal" tabindex="-1" aria-labelledby="departmentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="departmentForm" class="department-form" method="POST"
                        action="{{ $editingDepartmentId ? route('departemen.update', $editingDepartmentId) : route('departemen.store') }}">
                        @csrf
                        <input id="departmentMethod" type="hidden" @if ($editingDepartmentId) name="_method" @endif
                            value="{{ $editingDepartmentId ? 'PUT' : '' }}">
                        <input id="departmentFormMode" type="hidden" name="_form"
                            value="{{ $editingDepartmentId ? 'edit_department' : 'create_department' }}">
                        <input id="departmentId" type="hidden" name="department_id"
                            value="{{ $editingDepartmentId }}">

                        <div class="modal-header border-bottom">
                            <div>
                                <h5 class="modal-title fw-semibold mb-0" id="departmentModalLabel">
                                    {{ $editingDepartmentId ? 'Ubah Departemen' : 'Tambah Departemen' }}
                                </h5>
                                <small class="text-muted">Kolom bertanda <span class="text-danger">*</span> wajib diisi</small>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>

                        <div class="modal-body">
                            @if (isset($errors) && $errors->any() && old('_form'))
                                <div class="alert alert-danger" role="alert">
                                    @foreach ($errors->all() as $error)
                                        <div>{{ $error }}</div>
                                    @endforeach
                                </div>
                            @endif

                            <label for="departmentName" class="form-label">Nama Departemen <span
                                    class="text-danger">*</span></label>
                            <input id="departmentName" name="nama_departemen" type="text" class="form-control"
                                value="{{ old('nama_departemen') }}" maxlength="255" required
                                placeholder="Contoh: Sumber Daya Manusia">
                            <div class="form-text">Masukkan nama departemen yang belum terdaftar.</div>
                        </div>

                        <div class="modal-footer border-top">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ===== Delete modal ===== --}}
        <div class="modal fade" id="deleteDepartmentModal" tabindex="-1" aria-labelledby="deleteDepartmentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="deleteDepartmentForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header">
                            <h5 class="modal-title fw-semibold" id="deleteDepartmentModalLabel">Hapus Departemen</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            Apakah Anda yakin ingin menghapus <strong id="deleteDepartmentName"></strong>?
                            <div class="text-muted small mt-1">Tindakan ini tidak dapat dibatalkan.</div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const departmentForm = document.getElementById('departmentForm');
        const departmentModalLabel = document.getElementById('departmentModalLabel');
        const departmentModalEl = document.getElementById('departmentModal');
        const departmentNameInput = document.getElementById('departmentName');

        // Mode tambah: reset formulir ke kondisi awal.
        document.querySelectorAll('[data-bs-target="#departmentModal"]:not(.js-edit-department)').forEach(function (btn) {
            btn.addEventListener('click', function () {
                departmentForm.reset();
                departmentForm.action = @json(route('departemen.store'));
                document.getElementById('departmentMethod').removeAttribute('name');
                document.getElementById('departmentMethod').value = '';
                document.getElementById('departmentFormMode').value = 'create_department';
                document.getElementById('departmentId').value = '';
                departmentNameInput.value = '';
                departmentModalLabel.textContent = 'Tambah Departemen';
            });
        });

        // Mode ubah: isi formulir dengan departemen yang dipilih.
        document.querySelectorAll('.js-edit-department').forEach(function (button) {
            button.addEventListener('click', function () {
                departmentForm.action = button.dataset.url;
                document.getElementById('departmentMethod').setAttribute('name', '_method');
                document.getElementById('departmentMethod').value = 'PUT';
                document.getElementById('departmentFormMode').value = 'edit_department';
                document.getElementById('departmentId').value = button.dataset.id;
                departmentNameInput.value = button.dataset.name;
                departmentModalLabel.textContent = 'Ubah Departemen';
            });
        });

        // Konfirmasi penghapusan.
        document.querySelectorAll('.js-delete-department').forEach(function (button) {
            button.addEventListener('click', function () {
                document.getElementById('deleteDepartmentForm').action = button.dataset.url;
                document.getElementById('deleteDepartmentName').textContent = button.dataset.name;
            });
        });

        // Fokuskan kolom saat dialog dibuka.
        departmentModalEl.addEventListener('shown.bs.modal', function () {
            departmentNameInput.focus();
            departmentNameInput.select();
        });

        // Tutup notifikasi keberhasilan secara otomatis.
        document.querySelectorAll('.js-auto-dismiss').forEach(function (el) {
            setTimeout(function () {
                bootstrap.Alert.getOrCreateInstance(el).close();
            }, 4000);
        });

        @if (old('_form') && isset($errors) && $errors->any())
            document.addEventListener('DOMContentLoaded', function () {
                bootstrap.Modal.getOrCreateInstance(departmentModalEl).show();
            });
        @endif
    </script>
@endsection