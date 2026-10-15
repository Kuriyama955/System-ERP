@extends('layouts.main')

@section('page-title', 'Onboarding Karyawan')

@section('main-content')
    @php
        $oldForm = old('_form');

        // Modal edit: dibuka ulang bila validasi gagal
        $editingTask = $oldForm === 'edit-task'
            ? $onboardingTasks->firstWhere('id', (int) old('_edit_task_id'))
            : null;

        $reopenModal = null;
        if ($errors->any()) {
            if ($oldForm === 'create-task') {
                $reopenModal = 'createTaskModal';
            } elseif ($oldForm === 'edit-task' && $editingTask) {
                $reopenModal = 'editTaskModal';
            }
        }
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
            padding: 0.85rem 1rem;
        }

        .onboarding-table .task-name {
            white-space: normal;
            min-width: 200px;
        }

        .onboarding-table .nowrap {
            white-space: nowrap;
        }

        @media (max-width: 575.98px) {
            .onboarding-row-actions {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- ================= Header ================= --}}
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
            <div>
                <h4 class="fw-bold mb-1">Onboarding Karyawan</h4>
                <small class="text-muted">HRD / Onboarding Karyawan / Daftar Task</small>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                <span class="material-symbols-outlined onboarding-icon me-1" aria-hidden="true">add</span>
                Tambah Task Onboarding
            </button>
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

        {{-- ================= Daftar task onboarding ================= --}}
        <section class="card">
            <div class="card-header border-bottom">
                <h5 class="mb-1">Daftar Task Onboarding</h5>
                <small class="text-muted">Task yang harus diselesaikan karyawan baru selama masa onboarding. Klik Detail untuk melihat progres tiap karyawan.</small>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle onboarding-table">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4">Nama Task</th>
                            <th scope="col">Status</th>
                            <th scope="col">Karyawan Onboarding</th>
                            <th scope="col" class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($onboardingTasks as $task)
                            <tr>
                                <td class="ps-4 task-name">
                                    <a href="{{ route('onboarding.tasks.show', $task) }}"
                                        class="fw-semibold {{ $task->is_active ? '' : 'text-muted' }}">
                                        {{ $task->nama_task }}
                                    </a>
                                </td>
                                <td class="nowrap">
                                    <span class="badge {{ $task->is_active ? 'bg-label-success' : 'bg-label-secondary' }}"
                                        title="{{ $task->is_active ? 'Dapat ditambahkan ke karyawan onboarding' : 'Tidak dapat ditambahkan ke karyawan baru' }}">
                                        {{ $task->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="nowrap">{{ $task->assignments_count }} karyawan</td>
                                <td class="text-end pe-4 nowrap">
                                    <div class="d-inline-flex justify-content-end gap-2 onboarding-row-actions">
                                        <a href="{{ route('onboarding.tasks.show', $task) }}" class="btn btn-sm btn-outline-primary">
                                            <span class="material-symbols-outlined onboarding-icon me-1" aria-hidden="true">visibility</span>
                                            Detail
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="modal" data-bs-target="#editTaskModal"
                                            data-url="{{ route('onboarding.tasks.update', $task) }}"
                                            data-id="{{ $task->id }}"
                                            data-name="{{ $task->nama_task }}">
                                            <span class="material-symbols-outlined onboarding-icon me-1" aria-hidden="true">edit</span>
                                            Edit
                                        </button>
                                        <form action="{{ route('onboarding.tasks.toggle', $task) }}" method="POST" data-loading>
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="btn btn-sm {{ $task->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                                {{ $task->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <span class="material-symbols-outlined text-muted d-block mb-2" style="font-size:48px" aria-hidden="true">inventory_2</span>
                                    <div class="text-muted mb-3">Belum ada task onboarding. Tambahkan task yang perlu diselesaikan karyawan baru.</div>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                                        Tambah Task Onboarding
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    {{-- ================= Modal: Tambah Task Onboarding ================= --}}
    <div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('onboarding.tasks.store') }}" method="POST" data-loading>
                    @csrf
                    <input type="hidden" name="_form" value="create-task">
                    <div class="modal-header">
                        <h5 class="modal-title fw-semibold" id="createTaskModalLabel">Tambah Task Onboarding</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any() && $oldForm === 'create-task')
                            <div class="alert alert-danger" role="alert">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif
                        <label for="nama_task" class="form-label">Nama task</label>
                        <input type="text" name="nama_task" id="nama_task" class="form-control" maxlength="255"
                            placeholder="Contoh: Penandatanganan kontrak kerja"
                            value="{{ $oldForm === 'create-task' ? old('nama_task') : '' }}" required>
                        <div class="form-text">Task ini nantinya dapat ditambahkan ke karyawan yang sedang onboarding.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ================= Modal: Edit Task Onboarding (satu modal dipakai semua baris) ================= --}}
    <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="editTaskForm" method="POST" data-loading
                    action="{{ $editingTask ? route('onboarding.tasks.update', $editingTask) : '' }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_form" value="edit-task">
                    <input type="hidden" name="_edit_task_id" id="editTaskId" value="{{ $editingTask?->id }}">
                    <div class="modal-header">
                        <h5 class="modal-title fw-semibold" id="editTaskModalLabel">Edit Task Onboarding</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any() && $oldForm === 'edit-task')
                            <div class="alert alert-danger" role="alert">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif
                        <label for="editTaskName" class="form-label">Nama task</label>
                        <input type="text" name="nama_task" id="editTaskName" class="form-control" maxlength="255"
                            value="{{ $oldForm === 'edit-task' ? old('nama_task', $editingTask?->nama_task) : '' }}" required>
                        <div class="form-text">Perubahan nama akan terlihat pada semua karyawan yang memiliki task ini.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const createModal = document.getElementById('createTaskModal');
            const editModal = document.getElementById('editTaskModal');

            @if ($reopenModal)
                bootstrap.Modal.getOrCreateInstance(document.getElementById('{{ $reopenModal }}')).show();
            @endif

            /* ---- Modal tambah: fokus ke input ---- */
            createModal.addEventListener('shown.bs.modal', function () {
                document.getElementById('nama_task').focus();
            });

            /* ---- Modal edit: isi action, id, dan nama dari tombol pemicu ---- */
            editModal.addEventListener('show.bs.modal', function (event) {
                const trigger = event.relatedTarget;
                if (!trigger) {
                    return;
                }
                document.getElementById('editTaskForm').action = trigger.dataset.url;
                document.getElementById('editTaskId').value = trigger.dataset.id;
                document.getElementById('editTaskName').value = trigger.dataset.name;
            });
            editModal.addEventListener('shown.bs.modal', function () {
                const input = document.getElementById('editTaskName');
                input.focus();
                input.select();
            });

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