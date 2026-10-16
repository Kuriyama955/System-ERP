@extends('layouts.main')

@section('page-title', 'Manajemen Training')

@section('main-content')
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0" />

    <style>
        .material-symbols-outlined {
            vertical-align: middle;
            user-select: none;
        }

        .training-filter-label {
            display: block;
            margin-bottom: 5px;
            color: #a1acb8;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .7px;
            text-transform: uppercase;
        }

        .training-summary-value {
            color: #696cff;
            font-size: 26px;
            font-weight: 700;
            line-height: 1;
        }

        .training-summary-label {
            color: #a1acb8;
            font-size: 12px;
            font-weight: 500;
        }

        /* ---------- Card ---------- */
        .training-card {
            border: 1px solid #eceef1;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(50, 50, 93, .05);
        }

        /* ---------- Filter ---------- */
        .training-toolbar .form-control:focus,
        .training-form .form-control:focus {
            border-color: #696cff;
            box-shadow: 0 0 0 .2rem rgba(105, 108, 255, .12);
        }

        /* ---------- Table ---------- */
        .training-table > thead > tr > th {
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

        .training-table > tbody > tr > td {
            padding: 14px 16px;
            border-bottom: 1px solid #f2f3f5;
            color: #5d5f6e;
            font-size: 13px;
            vertical-align: middle;
        }

        .training-table > tbody > tr:last-child > td {
            border-bottom: 0;
        }

        .training-table > tbody > tr {
            transition: background .12s ease;
        }

        .training-table > tbody > tr:hover {
            background: #f8f8fc;
        }

        .training-name {
            color: #32334a;
            font-weight: 600;
            line-height: 1.3;
        }

        .training-note {
            max-width: 300px;
            margin-top: 2px;
            overflow: hidden;
            color: #a1acb8;
            font-size: 12px;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .training-theme-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            background: #eef0ff;
            color: #696cff;
            font-size: 11px;
            font-weight: 600;
        }

        .training-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            padding: 0;
        }

        .training-participant-list {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #d9dee5;
            border-radius: 6px;
        }

        .training-participant-option {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 9px 12px;
            border-bottom: 1px solid #f0f1f4;
            cursor: pointer;
        }

        .training-participant-option:last-child {
            border-bottom: 0;
        }

        .training-participant-option:hover {
            background: #f8f8fc;
        }

        .training-participant-option[hidden] {
            display: none;
        }

        /* ---------- Modal ---------- */
        .training-section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            color: #696cff;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .7px;
            text-transform: uppercase;
        }

        .training-section-title::after {
            flex: 1;
            height: 1px;
            background: #e9eaee;
            content: "";
        }

        @media (max-width: 575.98px) {
            .training-note {
                max-width: 180px;
            }
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- ===== Header ===== --}}
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
            <div>
                <h4 class="fw-bold mb-1">Manajemen Training</h4>
                <small class="text-muted">HRD &nbsp;/&nbsp; Training &nbsp;·&nbsp; Kelola dan pantau data pelatihan</small>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#trainingModal">
                + Tambah Training
            </button>
        </div>

        {{-- ===== Alerts ===== --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible js-auto-dismiss" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif
        @if ($errors->any() && !old('_form'))
            <div class="alert alert-danger" role="alert">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        {{-- ===== Active filter ===== --}}
        @if (request('search'))
            <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
                <small class="text-muted">Hasil pencarian untuk:</small>
                <span class="badge rounded-pill bg-primary">{{ request('search') }}</span>
                <a href="{{ route('training.index') }}" class="btn btn-sm btn-outline-danger">Hapus filter</a>
            </div>
        @endif

        {{-- ===== Table card ===== --}}
        <div class="card training-card overflow-hidden">
            <div class="card-header training-toolbar py-3 border-bottom">
                <form action="{{ route('training.index') }}" method="GET">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-5 col-lg-4">
                            <label class="training-filter-label" for="training-search">Cari Training</label>
                            <input id="training-search" type="text" name="search" value="{{ request('search') }}"
                                class="form-control form-control-sm" placeholder="Cari nama atau tema training..."
                                autocomplete="off">
                        </div>
                        <div class="col-auto d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm px-3">Search</button>
                            <a href="{{ route('training.index') }}" class="btn btn-outline-secondary btn-sm px-3">Reset</a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table training-table mb-0">
                        <thead>
                            <tr>
                                <th style="width:44px">#</th>
                                <th>Nama Training</th>
                                <th>Tema</th>
                                <th>Tanggal</th>
                                <th class="d-none d-md-table-cell">Lokasi</th>
                                <th class="text-center">Total Peserta</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($trainings as $training)
                                <tr>
                                    <td class="text-muted">{{ $trainings->firstItem() + $loop->index }}</td>
                                    <td>
                                        <a class="training-name text-decoration-none"
                                            href="{{ route('training.show', $training) }}">
                                            {{ $training->nama_pelatihan }}
                                        </a>
                                        @if ($training->keterangan)
                                            <div class="training-note" title="{{ $training->keterangan }}">
                                                {{ $training->keterangan }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($training->tema)
                                            <span class="training-theme-badge">{{ $training->tema }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">{{ $training->tanggal_aktual->format('d M Y') }}</td>
                                    <td class="d-none d-md-table-cell">{{ $training->lokasi ?: '—' }}</td>
                                    <td class="text-center">{{ $training->participants->count() }}</td>
                                    <td class="text-end text-nowrap">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <a href="{{ route('training.show', $training) }}"
                                                class="btn btn-sm btn-outline-secondary training-action-btn"
                                                title="Detail training"
                                                aria-label="Detail {{ $training->nama_pelatihan }}">
                                                <span class="material-symbols-outlined" style="font-size:17px">visibility</span>
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-outline-primary training-action-btn js-edit-training"
                                                data-bs-toggle="modal" data-bs-target="#trainingModal"
                                                title="Ubah training"
                                                data-url="{{ route('training.update', array_merge(['training' => $training->id], request()->query())) }}"
                                                data-id="{{ $training->id }}"
                                                data-nama="{{ $training->nama_pelatihan }}"
                                                data-tema="{{ $training->tema }}"
                                                data-tanggal="{{ $training->tanggal_aktual->format('Y-m-d') }}"
                                                data-lokasi="{{ $training->lokasi }}"
                                                data-sertifikat="{{ $training->sertifikat }}"
                                                data-keterangan="{{ $training->keterangan }}"
                                                data-participants="{{ $training->participants->pluck('karyawanstaff_id')->implode(',') }}"
                                                aria-label="Ubah {{ $training->nama_pelatihan }}">
                                                <span class="material-symbols-outlined" style="font-size:17px">edit</span>
                                            </button>
                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger training-action-btn js-delete-training"
                                                data-bs-toggle="modal" data-bs-target="#deleteTrainingModal"
                                                title="Hapus training"
                                                data-url="{{ route('training.destroy', array_merge(['training' => $training->id], request()->query())) }}"
                                                data-name="{{ $training->nama_pelatihan }}"
                                                aria-label="Hapus {{ $training->nama_pelatihan }}">
                                                <span class="material-symbols-outlined" style="font-size:17px">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-5 text-center">
                                        <h6 class="fw-semibold mb-1">
                                            {{ request('search') ? 'Training tidak ditemukan' : 'Belum ada data training' }}
                                        </h6>
                                        <p class="text-muted mb-3">
                                            {{ request('search') ? 'Coba kata kunci lain atau hapus filter pencarian.' : 'Mulai dengan menambahkan data training.' }}
                                        </p>
                                        @if (request('search'))
                                            <a href="{{ route('training.index') }}"
                                                class="btn btn-sm btn-outline-primary">Atur ulang pencarian</a>
                                        @else
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#trainingModal">+ Tambah Training</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($trainings->total() > 0)
                    <div class="px-4 py-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <small class="text-muted">
                            Menampilkan {{ $trainings->firstItem() }}–{{ $trainings->lastItem() }}
                            dari {{ $trainings->total() }} data training
                        </small>
                        @if ($trainings->hasPages())
                            {{ $trainings->links('pagination::bootstrap-5') }}
                        @endif
                    </div>
                @endif
            </div>
        </div>

        @php
            $editingTrainingId = old('_form') === 'edit' ? old('training_id') : null;
        @endphp

        {{-- ===== Modal tambah / ubah ===== --}}
        <div class="modal fade" id="trainingModal" tabindex="-1" aria-labelledby="trainingModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <form id="trainingForm" class="training-form d-flex flex-column overflow-hidden" method="POST"
                        action="{{ $editingTrainingId ? route('training.update', array_merge(['training' => $editingTrainingId], request()->query())) : route('training.store', request()->query()) }}">
                        @csrf
                        <input id="trainingMethod" type="hidden" @if ($editingTrainingId) name="_method" @endif
                            value="{{ $editingTrainingId ? 'PUT' : '' }}">
                        <input id="trainingFormMode" type="hidden" name="_form"
                            value="{{ $editingTrainingId ? 'edit' : 'create' }}">
                        <input id="trainingId" type="hidden" name="training_id" value="{{ $editingTrainingId }}">

                        <div class="modal-header border-bottom">
                            <div>
                                <h5 class="modal-title fw-semibold mb-0" id="trainingModalLabel">
                                    {{ $editingTrainingId ? 'Ubah Training' : 'Tambah Training' }}
                                </h5>
                                <small class="text-muted">Kolom bertanda <span class="text-danger">*</span> wajib diisi</small>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>

                        <div class="modal-body">
                            @if ($errors->any() && old('_form'))
                                <div class="alert alert-danger" role="alert">
                                    @foreach ($errors->all() as $error)
                                        <div>{{ $error }}</div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="training-section-title">Informasi Training</div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="trainingName" class="form-label">Nama Training <span
                                            class="text-danger">*</span></label>
                                    <input id="trainingName" type="text" name="nama_pelatihan" class="form-control"
                                        value="{{ old('nama_pelatihan') }}" maxlength="255"
                                        placeholder="Contoh: Kepemimpinan Dasar" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="trainingTheme" class="form-label">Tema</label>
                                    <input id="trainingTheme" type="text" name="tema" class="form-control"
                                        value="{{ old('tema') }}" maxlength="255" placeholder="Contoh: Keterampilan Interpersonal">
                                </div>
                                <div class="col-md-6">
                                    <label for="trainingDate" class="form-label">Tanggal <span
                                            class="text-danger">*</span></label>
                                    <input id="trainingDate" type="date" name="tanggal_aktual" class="form-control"
                                        value="{{ old('tanggal_aktual') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="trainingLocation" class="form-label">Lokasi</label>
                                    <input id="trainingLocation" type="text" name="lokasi" class="form-control"
                                        value="{{ old('lokasi') }}" maxlength="255"
                                        placeholder="Contoh: Ruang Rapat, Lantai 2">
                                </div>
                            </div>
                            <div id="trainingParticipantsField" class="mb-3">
                                <label for="trainingParticipants" class="form-label">
                                    Peserta <span class="text-danger">*</span>
                                </label>
                                <input id="trainingParticipantSearch" type="search" class="form-control mb-2"
                                    placeholder="Cari nama, NPK, atau departemen..." autocomplete="off">
                                <div id="trainingParticipantList" class="training-participant-list">
                                    @foreach ($karyawanstaffs as $karyawanstaff)
                                        <label class="training-participant-option"
                                            data-search="{{ \Illuminate\Support\Str::lower(implode(' ', array_filter([$karyawanstaff->nama, $karyawanstaff->npk, $karyawanstaff->departement]))) }}">
                                            <input class="form-check-input mt-1" type="checkbox" name="karyawanstaff_ids[]"
                                                value="{{ $karyawanstaff->id }}"
                                                @checked(in_array((string) $karyawanstaff->id, (array) old('karyawanstaff_ids', []), true))>
                                            <span>
                                                <span class="d-block fw-semibold">{{ $karyawanstaff->nama }}</span>
                                                <span class="small text-muted">
                                                    {{ $karyawanstaff->npk ?: 'Tanpa NPK' }}
                                                    @if ($karyawanstaff->departement)
                                                        · {{ $karyawanstaff->departement }}
                                                    @endif
                                                </span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted">Pilih peserta yang mengikuti training ini.</small>
                                    <small id="trainingParticipantCount" class="text-muted"></small>
                                </div>
                                <small id="trainingParticipantEditNote" class="form-text text-warning d-none">
                                    Menghapus peserta saat edit juga akan menghapus isian training peserta tersebut.
                                </small>
                            </div>
                        </div>

                        <div class="modal-footer border-top">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ===== Modal hapus ===== --}}
        <div class="modal fade" id="deleteTrainingModal" tabindex="-1" aria-labelledby="deleteTrainingModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="deleteTrainingForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header">
                            <h5 class="modal-title fw-semibold" id="deleteTrainingModalLabel">Hapus Training</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            Apakah Anda yakin ingin menghapus <strong id="deleteTrainingName"></strong>?
                            <div class="text-muted small mt-1">Aksi ini tidak dapat dibatalkan.</div>
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
        const trainingForm = document.getElementById('trainingForm');
        const trainingModalLabel = document.getElementById('trainingModalLabel');
        const trainingModalEl = document.getElementById('trainingModal');
        const trainingParticipantsField = document.getElementById('trainingParticipantsField');
        const trainingParticipantEditNote = document.getElementById('trainingParticipantEditNote');
        const trainingParticipantCheckboxes = Array.from(
            document.querySelectorAll('#trainingParticipantList input[name="karyawanstaff_ids[]"]')
        );
        const trainingParticipantCount = document.getElementById('trainingParticipantCount');

        function updateTrainingParticipantCount() {
            const selectedCount = trainingParticipantCheckboxes.filter(function (checkbox) {
                return checkbox.checked;
            }).length;
            trainingParticipantCount.textContent = selectedCount + ' peserta dipilih';
        }

        function setTrainingParticipants(participantIds) {
            const selectedIds = new Set(participantIds.map(String));
            trainingParticipantCheckboxes.forEach(function (checkbox) {
                checkbox.checked = selectedIds.has(checkbox.value);
            });
            updateTrainingParticipantCount();
        }

        trainingParticipantCheckboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', updateTrainingParticipantCount);
        });

        document.getElementById('trainingParticipantSearch').addEventListener('input', function (event) {
            const search = event.target.value.trim().toLowerCase();
            document.querySelectorAll('.training-participant-option').forEach(function (option) {
                option.hidden = !option.dataset.search.includes(search);
            });
        });

        document.querySelectorAll('[data-bs-target="#trainingModal"]:not(.js-edit-training)').forEach(function (btn) {
            btn.addEventListener('click', function () {
                trainingForm.reset();
                trainingForm.action = @json(route('training.store', request()->query()));
                document.getElementById('trainingMethod').removeAttribute('name');
                document.getElementById('trainingMethod').value = '';
                document.getElementById('trainingFormMode').value = 'create';
                document.getElementById('trainingId').value = '';
                trainingParticipantsField.classList.remove('d-none');
                trainingParticipantEditNote.classList.add('d-none');
                document.getElementById('trainingParticipantSearch').value = '';
                document.querySelectorAll('.training-participant-option').forEach(function (option) {
                    option.hidden = false;
                });
                setTrainingParticipants([]);
                trainingForm.querySelectorAll('input:not([type="hidden"]):not([type="checkbox"]), select, textarea')
                    .forEach(function (field) {
                        field.value = '';
                    });
                trainingModalLabel.textContent = 'Tambah Training';
            });
        });

        document.querySelectorAll('.js-edit-training').forEach(function (button) {
            button.addEventListener('click', function () {
                trainingForm.action = button.dataset.url;
                document.getElementById('trainingMethod').setAttribute('name', '_method');
                document.getElementById('trainingMethod').value = 'PUT';
                document.getElementById('trainingFormMode').value = 'edit';
                document.getElementById('trainingId').value = button.dataset.id;
                document.getElementById('trainingName').value = button.dataset.nama;
                document.getElementById('trainingTheme').value = button.dataset.tema;
                document.getElementById('trainingDate').value = button.dataset.tanggal;
                document.getElementById('trainingLocation').value = button.dataset.lokasi;
                trainingParticipantsField.classList.remove('d-none');
                trainingParticipantEditNote.classList.remove('d-none');
                document.getElementById('trainingParticipantSearch').value = '';
                document.querySelectorAll('.training-participant-option').forEach(function (option) {
                    option.hidden = false;
                });
                setTrainingParticipants(button.dataset.participants.split(',').filter(Boolean));
                trainingModalLabel.textContent = 'Ubah Training';
            });
        });

        document.querySelectorAll('.js-delete-training').forEach(function (button) {
            button.addEventListener('click', function () {
                document.getElementById('deleteTrainingForm').action = button.dataset.url;
                document.getElementById('deleteTrainingName').textContent = button.dataset.name;
            });
        });

        // Focus the first field when the modal opens
        trainingModalEl.addEventListener('shown.bs.modal', function () {
            document.getElementById('trainingName').focus();
        });

        // Dismiss the success alert automatically
        document.querySelectorAll('.js-auto-dismiss').forEach(function (el) {
            setTimeout(function () {
                bootstrap.Alert.getOrCreateInstance(el).close();
            }, 4000);
        });

        @if ($errors->any() && old('_form'))
            document.addEventListener('DOMContentLoaded', function () {
                @if (old('_form') === 'edit')
                    const editTrainingId = @json((string) old('training_id'));
                    const editButton = document.querySelector('.js-edit-training[data-id="' + editTrainingId + '"]');
                    if (editButton) {
                        editButton.click();
                        setTrainingParticipants(@json((array) old('karyawanstaff_ids', [])));
                    }
                @else
                    setTrainingParticipants(@json((array) old('karyawanstaff_ids', [])));
                @endif
                bootstrap.Modal.getOrCreateInstance(trainingModalEl).show();
            });
        @endif
    </script>
@endsection