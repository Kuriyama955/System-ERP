@extends('layouts.main')

@section('page-title', 'Edit Profil Karyawan')

@section('main-content')
    <style>
        .profile-editor-accordion {
            --bs-accordion-border-color: #e9ebf2;
            --bs-accordion-border-radius: 12px;
            --bs-accordion-btn-focus-box-shadow: 0 0 0 .2rem rgba(105, 108, 255, .15);
            --bs-accordion-active-bg: #f7f7ff;
            --bs-accordion-active-color: #4f52c7;
        }

        .profile-editor-accordion .accordion-item {
            overflow: hidden;
            border-radius: 10px;
        }

        .profile-editor-accordion .accordion-item + .accordion-item {
            margin-top: 10px;
        }

        .profile-editor-accordion .accordion-button {
            min-height: 58px;
            font-weight: 600;
        }

        .profile-editor-accordion .accordion-body {
            padding: 24px;
        }

        @media (max-width: 575.98px) {
            .profile-editor-accordion .accordion-body {
                padding: 16px;
            }
        }
    </style>

    @php
        $profile = old('profile', []);
        $personalRecord = $karyawanstaff->personal;
        $personalData = old('profile.personal', [
            'nik' => $personalRecord?->nik,
            'birth_place' => $personalRecord?->tempat_lahir,
            'birth_date' => $personalRecord?->tanggal_lahir?->format('Y-m-d'),
            'gender' => $personalRecord?->jenis_kelamin,
            'marital_status' => $personalRecord?->status_pernikahan,
            'religion' => $personalRecord?->agama,
            'blood_type' => $personalRecord?->golongan_darah,
            'phone' => $personalRecord?->no_telepon,
            'email' => $personalRecord?->email_pribadi,
            'bank_name' => $personalRecord?->nama_bank,
            'bank_branch' => $personalRecord?->cabang_bank,
            'bank_account_number' => $personalRecord?->nomor_rekening,
            'bank_account_name' => $personalRecord?->nama_pemilik_rekening,
            'education' => $personalRecord?->pendidikan,
            'experience' => $personalRecord?->pengalaman_kerja,
            'additional_information' => $personalRecord?->informasi_tambahan,
            'address' => $personalRecord?->alamat,
            'ktp_address' => $personalRecord?->alamat_ktp,
            'emergency_name' => $personalRecord?->nama_kontak_darurat,
            'emergency_relationship' => $personalRecord?->hubungan_kontak_darurat,
            'emergency_phone' => $personalRecord?->telepon_kontak_darurat,
        ]);
        $profile['facilities'] = old('profile.facilities', $karyawanstaff->facilities
            ->map(fn ($facility) => [
                'id' => $facility->id,
                'asset_id' => $facility->asset_id,
                'tanggal' => $facility->tanggal_pemberian?->format('Y-m-d'),
                'kondisi' => $facility->kondisi,
                'tanggal_pengembalian' => $facility->tanggal_pengembalian?->format('Y-m-d'),
                'catatan' => $facility->keterangan,
            ])
            ->all() ?: [[]]);
        $openSection = 'main';
        if ($errors->any()) {
            $firstErrorKey = array_key_first($errors->messages());
            $errorSection = explode('.', $firstErrorKey)[1] ?? '';
            $openSection = match ($errorSection) {
                'personal', 'facilities' => $errorSection,
                default => 'main',
            };
        }
        $repeaters = [
            'facilities' => [
                ['name' => '', 'key' => 'id', 'type' => 'hidden'],
                ['name' => 'Asset', 'key' => 'asset_id', 'type' => 'select'],
                ['name' => 'Tanggal', 'key' => 'tanggal', 'type' => 'date'],
                ['name' => 'Kondisi', 'key' => 'kondisi', 'type' => 'text'],
                ['name' => 'Tanggal pengembalian', 'key' => 'tanggal_pengembalian', 'type' => 'date'],
                ['name' => 'Keterangan', 'key' => 'catatan', 'type' => 'text'],
            ],
        ];
    @endphp

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
            <div>
                <div class="small text-muted mb-1">HRD / Staff &amp; Department / Profil Karyawan</div>
                <h4 class="fw-bold mb-0">Edit Profil Karyawan</h4>
                <div class="text-muted mt-1">{{ $karyawanstaff->nama }} · NPK {{ $karyawanstaff->npk }}</div>
                <div class="small text-muted mt-2">Pilih bagian yang ingin diedit. Bagian lainnya tetap tertutup agar form lebih ringkas.</div>
            </div>
            <a href="{{ route('staff.show', $karyawanstaff->id) }}" class="btn btn-outline-secondary">
                Batal
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <div class="fw-semibold mb-1">Periksa kembali data yang dimasukkan.</div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('staff.update', $karyawanstaff->id) }}">
            @csrf
            @method('PUT')

            <div class="accordion profile-editor-accordion mb-4" id="profileEditorAccordion">
            <section class="accordion-item">
                <h2 class="accordion-header" id="heading-main">
                    <button class="accordion-button {{ $openSection === 'main' ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse-main" aria-expanded="{{ $openSection === 'main' ? 'true' : 'false' }}" aria-controls="collapse-main">
                        <span><strong>Data utama karyawan</strong><small class="d-block text-muted mt-1">Informasi pekerjaan dan identitas dasar</small></span>
                    </button>
                </h2>
                <div id="collapse-main" class="accordion-collapse collapse {{ $openSection === 'main' ? 'show' : '' }}" aria-labelledby="heading-main"
                    data-bs-parent="#profileEditorAccordion">
                <div class="accordion-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nama" class="form-label">Nama lengkap</label>
                            <input id="nama" name="nama" class="form-control" value="{{ old('nama', $karyawanstaff->nama) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="npk" class="form-label">NPK</label>
                            <input id="npk" name="npk" class="form-control" value="{{ old('npk', $karyawanstaff->npk) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="departement" class="form-label">Departemen</label>
                            <select id="departement" name="departement" class="form-select" required>
                                <option value="">Pilih departemen</option>
                                @foreach ($departements as $departement)
                                    <option value="{{ $departement }}"
                                        {{ old('departement', $karyawanstaff->departement) === $departement ? 'selected' : '' }}>
                                        {{ $departement }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="jabatan" class="form-label">Jabatan</label>
                            <input id="jabatan" name="jabatan" class="form-control" value="{{ old('jabatan', $karyawanstaff->jabatan) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="lokasi" class="form-label">Lokasi kerja</label>
                            <input id="lokasi" name="lokasi" class="form-control" value="{{ old('lokasi', $karyawanstaff->lokasi) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label for="tmk" class="form-label">Tanggal mulai kerja</label>
                            <input id="tmk" type="date" name="tmk" class="form-control" value="{{ old('tmk', $karyawanstaff->tmk) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status karyawan</label>
                            <select id="status" name="status" class="form-select" required>
                                @foreach (['ONBOARDING' => 'Onboarding', 'AKTIF' => 'Aktif', 'TIDAK AKTIF' => 'Tidak aktif'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', in_array($karyawanstaff->status, ['TIDAK AKTIF', 'TIDAKAKTIF'], true) ? 'TIDAK AKTIF' : $karyawanstaff->status) === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                </div>
            </section>

            <section class="accordion-item">
                <h2 class="accordion-header" id="heading-personal">
                    <button class="accordion-button {{ $openSection === 'personal' ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse-personal" aria-expanded="{{ $openSection === 'personal' ? 'true' : 'false' }}" aria-controls="collapse-personal">Personal</button>
                </h2>
                <div id="collapse-personal" class="accordion-collapse collapse {{ $openSection === 'personal' ? 'show' : '' }}" aria-labelledby="heading-personal"
                    data-bs-parent="#profileEditorAccordion">
                <div class="accordion-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="personal-name">Nama sesuai identitas</label>
                            <input id="personal-name" class="form-control" value="{{ old('nama', $karyawanstaff->nama) }}" disabled>
                            <div class="form-text">Ubah nama melalui bagian Data utama karyawan.</div>
                        </div>
                        @foreach ([
                            'nik' => ['NIK', 'text'],
                            'birth_place' => ['Tempat lahir', 'text'],
                            'birth_date' => ['Tanggal lahir', 'date'],
                            'gender' => ['Jenis kelamin', 'text'],
                            'marital_status' => ['Status perkawinan', 'text'],
                            'religion' => ['Agama', 'text'],
                            'blood_type' => ['Golongan darah', 'text'],
                            'phone' => ['Nomor telepon', 'tel'],
                            'email' => ['Email pribadi', 'email'],
                        ] as $key => [$label, $type])
                            <div class="col-md-6">
                                <label for="personal-{{ $key }}" class="form-label">{{ $label }}</label>
                                <input id="personal-{{ $key }}" type="{{ $type }}" name="profile[personal][{{ $key }}]"
                                    class="form-control" value="{{ data_get($personalData, $key) }}">
                            </div>
                        @endforeach
                        @foreach ([
                            'bank_name' => 'Nama bank',
                            'bank_branch' => 'Cabang bank',
                            'bank_account_number' => 'Nomor rekening',
                            'bank_account_name' => 'Nama pemilik rekening',
                        ] as $key => $label)
                            <div class="col-md-6">
                                <label for="personal-{{ $key }}" class="form-label">{{ $label }}</label>
                                <input id="personal-{{ $key }}" name="profile[personal][{{ $key }}]" class="form-control"
                                    value="{{ data_get($personalData, $key) }}">
                            </div>
                        @endforeach
                        @foreach ([
                            'education' => 'Pendidikan',
                            'experience' => 'Pengalaman kerja',
                            'additional_information' => 'Informasi tambahan',
                        ] as $key => $label)
                            <div class="col-md-6">
                                <label for="personal-{{ $key }}" class="form-label">{{ $label }}</label>
                                <input id="personal-{{ $key }}" name="profile[personal][{{ $key }}]" class="form-control"
                                    value="{{ data_get($personalData, $key) }}">
                            </div>
                        @endforeach
                        <div class="col-12">
                            <label for="personal-address" class="form-label">Alamat domisili</label>
                            <textarea id="personal-address" name="profile[personal][address]" class="form-control" rows="2">{{ data_get($personalData, 'address') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label for="personal-ktp-address" class="form-label">Alamat KTP</label>
                            <textarea id="personal-ktp-address" name="profile[personal][ktp_address]" class="form-control" rows="2">{{ data_get($personalData, 'ktp_address') }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label for="emergency-name" class="form-label">Nama kontak darurat</label>
                            <input id="emergency-name" name="profile[personal][emergency_name]" class="form-control"
                                value="{{ data_get($personalData, 'emergency_name') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="emergency-relationship" class="form-label">Hubungan</label>
                            <input id="emergency-relationship" name="profile[personal][emergency_relationship]" class="form-control"
                                value="{{ data_get($personalData, 'emergency_relationship') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="emergency-phone" class="form-label">Telepon kontak darurat</label>
                            <input id="emergency-phone" name="profile[personal][emergency_phone]" class="form-control"
                                value="{{ data_get($personalData, 'emergency_phone') }}">
                        </div>
                    </div>
                </div>
                </div>
            </section>




            @foreach (['facilities' => 'Fasilitas'] as $list => $title)
                @php $rows = data_get($profile, $list, [[]]); @endphp
                <section class="accordion-item" data-repeater="{{ $list }}">
                    <h2 class="accordion-header" id="heading-{{ $list }}">
                        <button class="accordion-button {{ $openSection === $list ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse-{{ $list }}" aria-expanded="{{ $openSection === $list ? 'true' : 'false' }}" aria-controls="collapse-{{ $list }}">
                            {{ $title }}
                        </button>
                    </h2>
                    <div id="collapse-{{ $list }}" class="accordion-collapse collapse {{ $openSection === $list ? 'show' : '' }}" aria-labelledby="heading-{{ $list }}"
                        data-bs-parent="#profileEditorAccordion">
                    <div class="accordion-body">
                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" data-add-row="{{ $list }}">Tambah baris</button>
                        </div>
                        <div class="row g-2" data-rows="{{ $list }}" data-next-index="{{ count($rows) }}">
                            @foreach ($rows as $index => $row)
                                <div class="col-12 border rounded p-3" data-row>
                                    <div class="row g-2">
                                        @foreach ($repeaters[$list] as $field)
                                            @if ($field['type'] === 'hidden')
                                                <input type="hidden"
                                                    name="profile[{{ $list }}][{{ $index }}][{{ $field['key'] }}]"
                                                    value="{{ old('profile.'.$list.'.'.$index.'.'.$field['key'], data_get($row, $field['key'])) }}">
                                            @else
                                                <div class="col-md-{{ $field['key'] === 'catatan' ? '12' : '4' }}">
                                                    <label class="form-label">{{ $field['name'] }}</label>
                                                    @if ($field['type'] === 'select')
                                                        <select class="form-select"
                                                            name="profile[{{ $list }}][{{ $index }}][{{ $field['key'] }}]">
                                                            <option value="">Pilih asset</option>
                                                            @foreach ($assets as $asset)
                                                                <option value="{{ $asset->id }}"
                                                                    {{ (string) old('profile.'.$list.'.'.$index.'.'.$field['key'], data_get($row, $field['key'])) === (string) $asset->id ? 'selected' : '' }}>
                                                                    {{ $asset->name }} ({{ $asset->code }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <input type="{{ $field['type'] }}" class="form-control"
                                                            name="profile[{{ $list }}][{{ $index }}][{{ $field['key'] }}]"
                                                            value="{{ old('profile.'.$list.'.'.$index.'.'.$field['key'], data_get($row, $field['key'])) }}">
                                                    @endif
                                                </div>
                                            @endif
                                        @endforeach
                                        <div class="col-12 text-end">
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-remove-row>Hapus baris</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <template data-row-template="{{ $list }}">
                            <div class="col-12 border rounded p-3" data-row>
                                <div class="row g-2">
                                    @foreach ($repeaters[$list] as $field)
                                        @if ($field['type'] === 'hidden')
                                            <input type="hidden"
                                                name="profile[{{ $list }}][__INDEX__][{{ $field['key'] }}]">
                                        @else
                                            <div class="col-md-{{ $field['key'] === 'catatan' ? '12' : '4' }}">
                                                <label class="form-label">{{ $field['name'] }}</label>
                                                @if ($field['type'] === 'select')
                                                    <select class="form-select"
                                                        name="profile[{{ $list }}][__INDEX__][{{ $field['key'] }}]">
                                                        <option value="">Pilih asset</option>
                                                        @foreach ($assets as $asset)
                                                            <option value="{{ $asset->id }}">{{ $asset->name }} ({{ $asset->code }})</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <input type="{{ $field['type'] }}" class="form-control"
                                                        name="profile[{{ $list }}][__INDEX__][{{ $field['key'] }}]">
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                    <div class="col-12 text-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-remove-row>Hapus baris</button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    </div>
                </section>
            @endforeach

            </div>

            <div class="d-flex justify-content-end gap-2 mb-4">
                <a href="{{ route('staff.show', $karyawanstaff->id) }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan profil</button>
            </div>
        </form>
    </div>

    <script>
        document.querySelectorAll('[data-add-row]').forEach((button) => {
            button.addEventListener('click', () => {
                const list = button.dataset.addRow;
                const container = document.querySelector(`[data-rows="${list}"]`);
                const template = document.querySelector(`[data-row-template="${list}"]`);
                const index = Number(container.dataset.nextIndex);
                container.dataset.nextIndex = index + 1;
                const html = template.innerHTML.replace(/__INDEX__/g, index);
                container.insertAdjacentHTML('beforeend', html);
            });
        });

        document.addEventListener('click', (event) => {
            const button = event.target.closest('[data-remove-row]');
            if (button) {
                button.closest('[data-row]').remove();
            }
        });
    </script>
@endsection
