@extends('layouts.main')

@section('page-title', 'Detail Task Onboarding')

@section('main-content')
    @php
        $assignmentList = $onboardingTask->assignments;
        $canAddStaff = $onboardingTask->is_active && $karyawanstaffs->isNotEmpty();
        $selectedStaffIds = array_map('strval', (array) old('karyawanstaff_ids', []));
        $reopenModal = $errors->any() && old('_form') === 'add-staff';
    @endphp

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0&display=swap" />
    <style>
        .onboarding-icon {
            font-size: 18px;
            vertical-align: middle;
        }

        .onboarding-table th,
        .onboarding-table td {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }

        .onboarding-table .nowrap {
            white-space: nowrap;
        }

        .onboarding-table-scroll {
            max-height: min(60vh, 480px);
            overflow: auto;
        }

        .onboarding-table thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background-color: var(--bs-tertiary-bg, #f8f9fa);
            box-shadow: inset 0 -1px var(--bs-border-color, #dee2e6);
        }

        .onboarding-employee-list {
            max-height: 260px;
            overflow-y: auto;
        }

        .onboarding-employee-list .list-group-item {
            cursor: pointer;
        }

        .onboarding-employee-list .list-group-item:hover {
            background-color: var(--bs-light, #f8f9fa);
        }

        .onboarding-employee-list.is-invalid {
            border-color: var(--bs-danger, #dc3545) !important;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- ================= Header ================= --}}
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
            <div>
                <div class="small text-muted mb-1">HRD / Onboarding Karyawan / Detail Task</div>
                <h4 class="fw-bold mb-2">{{ $onboardingTask->nama_task }}</h4>
                <span class="badge {{ $onboardingTask->is_active ? 'bg-label-success' : 'bg-label-secondary' }}"
                    title="{{ $onboardingTask->is_active ? 'Dapat ditambahkan ke karyawan onboarding' : 'Tidak dapat ditambahkan ke karyawan baru' }}">
                    {{ $onboardingTask->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
                @unless ($onboardingTask->is_active)
                    <div class="small text-muted mt-2">
                        Task ini nonaktif, jadi tidak dapat ditambahkan ke karyawan baru.
                    </div>
                @endunless
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('onboarding.index') }}" class="btn btn-outline-secondary">
                    <span class="material-symbols-outlined onboarding-icon me-1" aria-hidden="true">arrow_back</span>
                    Kembali ke Daftar Task
                </a>
                @if ($onboardingTask->is_active)
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal"
                        {{ $canAddStaff ? '' : 'disabled' }}
                        @unless ($canAddStaff) title="Semua karyawan sudah memiliki task ini" @endunless>
                        <span class="material-symbols-outlined onboarding-icon me-1" aria-hidden="true">person_add</span>
                        Tambah Karyawan Onboarding
                    </button>
                @endif
            </div>
        </div>

        {{-- ================= Notifikasi ================= --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="status">
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
        @if ($errors->any() && !$reopenModal)
            <div class="alert alert-danger alert-dismissible" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        {{-- ================= Daftar karyawan ================= --}}
        <div class="card">
            <div class="table-responsive onboarding-table-scroll" role="region"
                aria-label="Daftar karyawan onboarding" tabindex="0">
                <table class="table table-sm table-hover mb-0 align-middle onboarding-table">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4">Karyawan</th>
                            <th scope="col">Departemen</th>
                            <th scope="col">Status Task</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($assignmentList as $assignment)
                            @php $isDone = $assignment->status === 'selesai'; @endphp
                            <tr>
                                <td class="ps-4">
                                    <a href="{{ route('staff.show', $assignment->karyawanstaff) }}" class="fw-semibold">
                                        {{ $assignment->karyawanstaff->nama }}
                                    </a>
                                    <div class="small text-muted">{{ $assignment->karyawanstaff->npk ?: 'NPK belum diisi' }}</div>
                                </td>
                                <td>{{ $assignment->karyawanstaff->departement ?: '—' }}</td>
                                <td class="nowrap">
                                    <span class="badge {{ $isDone ? 'bg-label-success' : 'bg-label-warning' }}">
                                        {{ $isDone ? 'Selesai' : 'Belum selesai' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <span class="material-symbols-outlined text-muted d-block mb-2" style="font-size:48px" aria-hidden="true">group_off</span>
                                    <div class="text-muted {{ $canAddStaff ? 'mb-3' : '' }}">Belum ada karyawan onboarding pada task ini.</div>
                                    @if ($canAddStaff)
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                                            Tambah Karyawan
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ================= Modal: Tambah Karyawan ================= --}}
    @if ($onboardingTask->is_active)
        <div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <form action="{{ route('onboarding.assignments.store') }}" method="POST" id="addStaffForm" data-loading>
                        @csrf
                        <input type="hidden" name="_form" value="add-staff">
                        <input type="hidden" name="onboarding_task_id" value="{{ $onboardingTask->id }}">
                        <div class="modal-header">
                            <div>
                                <h5 class="modal-title fw-semibold" id="addStaffModalLabel">Tambah Karyawan Onboarding</h5>
                                <small class="text-muted">Pilih karyawan yang perlu menyelesaikan task ini. Karyawan yang sudah memiliki task ini tidak ditampilkan.</small>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            @if ($reopenModal)
                                <div class="alert alert-danger" role="alert">
                                    @foreach ($errors->all() as $error)
                                        <div>{{ $error }}</div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label mb-0" id="staffPickerLabel">Pilih karyawan</label>
                                <span class="badge bg-label-primary" id="staffSelectedCount" aria-live="polite">0 dipilih</span>
                            </div>

                            <div class="input-group input-group-sm mb-2">
                                <span class="input-group-text" aria-hidden="true">
                                    <span class="material-symbols-outlined" style="font-size:16px">search</span>
                                </span>
                                <input type="search" class="form-control" id="staffSearch" autocomplete="off"
                                    placeholder="Cari nama, NPK, atau departemen" aria-label="Cari karyawan">
                                <button type="button" class="btn btn-outline-secondary" id="staffSelectAll">Pilih Semua</button>
                            </div>

                            <div class="list-group onboarding-employee-list border rounded" id="staffList"
                                role="group" aria-labelledby="staffPickerLabel">
                                @foreach ($karyawanstaffs as $staff)
                                    <label class="list-group-item d-flex align-items-center gap-3 border-0 border-bottom mb-0"
                                        for="staff-{{ $staff->id }}"
                                        data-search="{{ \Illuminate\Support\Str::lower($staff->nama . ' ' . $staff->npk . ' ' . $staff->departement) }}">
                                        <input class="form-check-input mt-0" type="checkbox" name="karyawanstaff_ids[]"
                                            id="staff-{{ $staff->id }}" value="{{ $staff->id }}"
                                            @checked(in_array((string) $staff->id, $selectedStaffIds, true))>
                                        <span>
                                            <span class="d-block fw-semibold">{{ $staff->nama }}</span>
                                            <small class="text-muted">
                                                {{ $staff->npk ?: 'NPK belum diisi' }}@if ($staff->departement) · {{ $staff->departement }}@endif
                                            </small>
                                        </span>
                                    </label>
                                @endforeach
                                <div class="list-group-item border-0 text-center text-muted small d-none" id="staffNoResult">
                                    Tidak ada karyawan yang cocok.
                                </div>
                            </div>
                            <div class="small text-danger mt-1 d-none" id="staffError">Pilih minimal satu karyawan.</div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Tambahkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalEl = document.getElementById('addStaffModal');

            if (modalEl) {
                @if ($reopenModal)
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                @endif

                modalEl.addEventListener('shown.bs.modal', function () {
                    document.getElementById('staffSearch').focus();
                });

                /* ---- Picker karyawan ---- */
                const list = document.getElementById('staffList');
                const items = Array.from(list.querySelectorAll('label[data-search]'));
                const counter = document.getElementById('staffSelectedCount');
                const noResult = document.getElementById('staffNoResult');
                const searchInput = document.getElementById('staffSearch');
                const selectAllBtn = document.getElementById('staffSelectAll');
                const errorMsg = document.getElementById('staffError');

                const visibleItems = () => items.filter(i => !i.classList.contains('d-none'));
                const checkboxOf = (item) => item.querySelector('input[type="checkbox"]');
                const clearError = function () {
                    list.classList.remove('is-invalid');
                    errorMsg.classList.add('d-none');
                };
                const updateCounter = function () {
                    counter.textContent = items.filter(i => checkboxOf(i).checked).length + ' dipilih';
                };

                searchInput.addEventListener('input', function () {
                    const keyword = this.value.trim().toLowerCase();
                    items.forEach(function (item) {
                        item.classList.toggle('d-none', keyword !== '' && !item.dataset.search.includes(keyword));
                    });
                    noResult.classList.toggle('d-none', visibleItems().length > 0);
                });

                selectAllBtn.addEventListener('click', function () {
                    const visible = visibleItems();
                    const allChecked = visible.length > 0 && visible.every(i => checkboxOf(i).checked);
                    visible.forEach(i => { checkboxOf(i).checked = !allChecked; });
                    this.textContent = allChecked ? 'Pilih Semua' : 'Hapus Pilihan';
                    updateCounter();
                    clearError();
                });

                list.addEventListener('change', function () {
                    updateCounter();
                    clearError();
                });
                updateCounter();

                // Minimal satu karyawan harus dipilih
                document.getElementById('addStaffForm').addEventListener('submit', function (e) {
                    if (!this.querySelector('input[name="karyawanstaff_ids[]"]:checked')) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        list.classList.add('is-invalid');
                        errorMsg.classList.remove('d-none');
                    }
                });
            }

            /* ---- Cegah double submit ---- */
            document.querySelectorAll('form[data-loading]').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    if (e.defaultPrevented || !form.checkValidity()) {
                        return;
                    }
                    const btn = form.querySelector('button[type="submit"]');
                    if (!btn) {
                        return;
                    }
                    setTimeout(function () {
                        btn.disabled = true;
                        btn.dataset.originalHtml = btn.innerHTML;
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>Menyimpan...';
                    }, 0);
                });
            });

            // Pulihkan tombol jika halaman dimuat dari cache (tombol Back)
            window.addEventListener('pageshow', function (e) {
                if (!e.persisted) {
                    return;
                }
                document.querySelectorAll('form[data-loading] button[type="submit"]').forEach(function (btn) {
                    if (btn.dataset.originalHtml) {
                        btn.innerHTML = btn.dataset.originalHtml;
                        btn.disabled = false;
                    }
                });
            });
        });
    </script>
@endsection