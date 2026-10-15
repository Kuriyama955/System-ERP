@extends('layouts.main')

@section('page-title', 'Profil Karyawan')

@section('main-content')
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0" />

    <style>
        .employee-profile {
            --profile-accent: #696cff;
            --profile-text: #414352;
            --profile-muted: #8590a0;
            --profile-border: #eceef2;
        }

        .profile-hero {
            position: relative;
            overflow: hidden;
            border: 1px solid #e7e8fb;
            background: linear-gradient(115deg, #f6f7ff 0%, #fff 72%);
        }

        .profile-hero::after {
            position: absolute;
            top: -72px;
            right: -42px;
            width: 230px;
            height: 230px;
            border-radius: 50%;
            background: rgba(105, 108, 255, .055);
            content: "";
            pointer-events: none;
        }

        .profile-hero-content {
            position: relative;
            z-index: 1;
        }

        .profile-avatar {
            display: flex;
            width: 82px;
            height: 82px;
            flex: 0 0 82px;
            align-items: center;
            justify-content: center;
            border: 4px solid #fff;
            border-radius: 50%;
            background: #e7e8ff;
            box-shadow: 0 2px 12px rgba(45, 50, 90, .1);
            color: var(--profile-accent);
            font-size: 26px;
            font-weight: 700;
        }

        .profile-name {
            color: #333544;
            font-size: clamp(20px, 3vw, 25px);
            font-weight: 700;
            line-height: 1.25;
        }

        .profile-meta {
            color: var(--profile-muted);
            font-size: 13px;
        }

        .profile-status,
        .item-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }

        .profile-status {
            padding: 6px 11px;
            border: 1px solid #c3e6cb;
            background: #f0faf4;
            color: #218838;
            font-size: 12px;
        }

        .profile-status.is-inactive {
            border-color: #f5c6cb;
            background: #fff5f5;
            color: #c82333;
        }

        .profile-status.is-onboarding {
            border-color: #ffecb5;
            background: #fff9e8;
            color: #9a6b00;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: currentColor;
        }

        .profile-nav {
            display: flex;
            flex-wrap: nowrap;
            justify-content: flex-start;
            gap: 8px;
            overflow-x: auto;
            padding: 7px 10px;
            border: 1px solid var(--profile-border);
            border-radius: 10px;
            background: #fff;
            scrollbar-width: thin;
        }

        .profile-nav .nav-link {
            display: inline-flex;
            flex: 0 0 auto;
            min-height: 42px;
            align-items: center;
            gap: 7px;
            padding: 8px 12px;
            border: 1px solid transparent;
            border-radius: 8px;
            color: #666b79;
            font-size: 11px;
            font-weight: 600;
            line-height: 1.2;
            white-space: nowrap;
            transition: background-color .15s ease, border-color .15s ease, color .15s ease;
        }

        .profile-nav .nav-link:hover,
        .profile-nav .nav-link:focus:not(.active) {
            border-color: transparent;
            background: transparent;
            color: var(--profile-accent);
        }

        .profile-nav .nav-link.active {
            border-color: #e2e3fd;
            background: #f0f1ff;
            color: var(--profile-accent);
        }

        .profile-nav .material-symbols-outlined {
            flex: 0 0 18px;
            font-size: 18px;
        }

        .profile-nav .nav-link:focus-visible {
            outline: 2px solid var(--profile-accent);
            outline-offset: 2px;
        }

        .profile-panel {
            min-height: 350px;
            border: 1px solid var(--profile-border);
            border-radius: 10px;
            background: #fff;
            scroll-margin-top: 20px;
        }

        .profile-panel-heading {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 21px 24px;
            border-bottom: 1px solid var(--profile-border);
        }

        .profile-panel-icon {
            display: flex;
            width: 38px;
            height: 38px;
            flex: 0 0 38px;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: #f0f1ff;
            color: var(--profile-accent);
        }

        .profile-panel-title {
            margin: 0 0 3px;
            color: var(--profile-text);
            font-size: 15px;
            font-weight: 700;
        }

        .profile-panel-description {
            margin: 0;
            color: var(--profile-muted);
            font-size: 12px;
        }

        .profile-panel-content {
            padding: 24px 30px 28px;
        }

        .info-card {
            height: 100%;
            padding: 16px 18px;
            border: 1px solid #eff0f3;
            border-radius: 9px;
            background: #fff;
        }

        .info-card-title {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 11px;
            color: #525665;
            font-size: 12px;
            font-weight: 700;
        }

        .info-card-title .material-symbols-outlined {
            color: var(--profile-accent);
            font-size: 17px;
        }

        .profile-details {
            margin: 0;
        }

        .profile-detail-row {
            display: grid;
            grid-template-columns: minmax(115px, 40%) minmax(0, 1fr);
            gap: 12px;
            padding: 11px 0;
            border-bottom: 1px solid #f0f1f4;
        }

        .profile-detail-row:first-child {
            padding-top: 0;
        }

        .profile-detail-row:last-child {
            padding-bottom: 0;
            border-bottom: 0;
        }

        .profile-detail-label {
            color: var(--profile-muted);
            font-size: 12px;
        }

        .profile-detail-value {
            overflow-wrap: anywhere;
            color: var(--profile-text);
            font-size: 12px;
            font-weight: 600;
        }

        .personal-summary {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            align-items: stretch;
            gap: 10px;
            margin: 0 0 16px;
        }

        .personal-summary-item,
        .personal-section {
            min-width: 0;
            height: 100%;
            box-sizing: border-box;
            padding: 14px 16px;
            border: 1px solid var(--profile-border);
            border-radius: 8px;
            background: #fff;
        }

        .personal-summary-label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
            min-width: 0;
            color: var(--profile-muted);
            font-size: 11px;
            font-weight: 600;
        }

        .personal-summary-label .material-symbols-outlined {
            flex: 0 0 18px;
            color: var(--profile-accent);
            font-size: 18px;
        }

        .personal-summary-value {
            margin: 0;
            color: var(--profile-text);
            font-size: 16px;
            font-weight: 700;
            line-height: 1.3;
            overflow-wrap: anywhere;
        }

        .personal-sections {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            grid-auto-rows: 1fr;
            align-items: stretch;
            gap: 12px;
        }

        .personal-section-title {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 7px;
            color: var(--profile-text);
            font-size: 12px;
            font-weight: 700;
        }

        .personal-section-title .material-symbols-outlined {
            flex: 0 0 18px;
            color: var(--profile-accent);
            font-size: 18px;
        }

        .personal-section .profile-detail-row {
            grid-template-columns: minmax(105px, 38%) minmax(0, 1fr);
            gap: 10px;
            padding: 7px 0;
        }

        .personal-section .profile-detail-label,
        .personal-section .profile-detail-value {
            font-size: 11px;
            line-height: 1.4;
        }

        .personal-extra-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            align-items: stretch;
            gap: 12px;
            margin-top: 12px;
        }

        .personal-section p {
            margin: 0;
            color: var(--profile-text);
            font-size: 12px;
            line-height: 1.5;
            overflow-wrap: anywhere;
        }

        .personal-section.is-wide {
            grid-column: 1 / -1;
        }

        .item-status {
            padding: 4px 8px;
        }

        .item-status.is-ready {
            background: #f0faf4;
            color: #218838;
        }

        .item-status.is-info {
            background: #f0f1ff;
            color: #5559cc;
        }

        .profile-table {
            margin-bottom: 0;
            vertical-align: middle;
        }

        .profile-table thead th {
            padding: 10px 12px;
            border-bottom-color: var(--profile-border);
            background: #f8f8fb;
            color: var(--profile-muted);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .45px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .profile-table tbody td {
            padding: 12px;
            border-bottom-color: #f0f1f4;
            color: var(--profile-text);
            font-size: 12px;
        }

        .profile-table tbody tr:last-child td {
            border-bottom: 0;
        }

        @media (max-width: 575.98px) {
            .profile-avatar {
                width: 68px;
                height: 68px;
                flex-basis: 68px;
                font-size: 22px;
            }

            .profile-nav {
                justify-content: flex-start;
                gap: 2px;
                padding: 5px;
            }

            .profile-nav .nav-link {
                min-height: 38px;
                gap: 6px;
                padding: 6px 9px;
                font-size: 10px;
            }

            .profile-nav .nav-link .material-symbols-outlined {
                flex-basis: 16px;
                font-size: 17px;
            }

            .profile-panel-heading {
                padding: 17px;
            }

            .profile-panel-content {
                padding: 16px;
            }

            .personal-summary-item,
            .personal-section {
                padding: 10px;
            }

            .personal-summary-value {
                font-size: 14px;
            }

            .personal-sections {
                grid-template-columns: 1fr;
                grid-auto-rows: auto;
            }

            .personal-extra-grid {
                grid-template-columns: 1fr;
            }

            .personal-section.is-wide {
                grid-column: auto;
            }

            .personal-section .profile-detail-row {
                grid-template-columns: minmax(100px, 40%) minmax(0, 1fr);
                gap: 7px;
            }

            .profile-detail-row {
                grid-template-columns: minmax(100px, 42%) minmax(0, 1fr);
                gap: 8px;
            }
        }

        @media (max-width: 420px) {
            .personal-summary {
                grid-template-columns: 1fr;
                gap: 7px;
            }
        }

    </style>

    @php
        $isActiveEmployee = $karyawanstaff->status === 'AKTIF';
        $employeeStatusLabel = match ($karyawanstaff->status) {
            'ONBOARDING' => 'Onboarding',
            'AKTIF' => 'Aktif',
            'TIDAK AKTIF', 'TIDAKAKTIF' => 'Tidak Aktif',
            default => 'Belum diisi',
        };
        $nameParts = preg_split('/\s+/', trim($karyawanstaff->nama ?? ''));
        $initials = collect($nameParts)
            ->filter()
            ->take(2)
            ->map(fn ($part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
            ->implode('');

        $personal = $karyawanstaff->personal;
        $profilePersonal = [
            'Identitas Dasar' => [
                'Nama Lengkap' => $karyawanstaff->nama ?: 'Belum diisi',
                'Nomor Induk Kependudukan (NIK)' => $personal?->nik ?: 'Belum diisi',
                'Tempat dan Tanggal Lahir' => trim(implode(', ', array_filter([
                    $personal?->tempat_lahir,
                    $personal?->tanggal_lahir?->format('d M Y'),
                ]))) ?: 'Belum diisi',
                'Jenis Kelamin' => $personal?->jenis_kelamin ?: 'Belum diisi',
                'Status Perkawinan' => $personal?->status_pernikahan ?: 'Belum diisi',
                'Agama' => $personal?->agama ?: 'Belum diisi',
                'Golongan Darah' => $personal?->golongan_darah ?: 'Belum diisi',
            ],
            'Kontak dan Alamat' => [
                'Nomor Telepon' => $personal?->no_telepon ?: 'Belum diisi',
                'Email Pribadi' => $personal?->email_pribadi ?: 'Belum diisi',
                'Alamat Domisili' => $personal?->alamat ?: 'Belum diisi',
                'Alamat KTP' => $personal?->alamat_ktp ?: 'Belum diisi',
            ],
            'Kontak Darurat' => [
                'Nama Kontak' => $personal?->nama_kontak_darurat ?: 'Belum diisi',
                'Hubungan' => $personal?->hubungan_kontak_darurat ?: 'Belum diisi',
                'Nomor Telepon' => $personal?->telepon_kontak_darurat ?: 'Belum diisi',
            ],
            'Informasi Perbankan' => [
                'Nama Bank' => $personal?->nama_bank ?: 'Belum diisi',
                'Cabang Bank' => $personal?->cabang_bank ?: 'Belum diisi',
                'Nomor Rekening' => $personal?->nomor_rekening ?: 'Belum diisi',
                'Nama Pemilik Rekening' => $personal?->nama_pemilik_rekening ?: 'Belum diisi',
            ],
        ];

        $profileEmployee = [
            'Informasi Pekerjaan' => [
                'Nomor Pokok Karyawan (NPK)' => $karyawanstaff->npk ?: 'Belum diisi',
                'Status Karyawan' => match ($karyawanstaff->status) {
                    'AKTIF' => 'Aktif',
                    'ONBOARDING' => 'Onboarding',
                    'TIDAK AKTIF', 'TIDAKAKTIF' => 'Tidak Aktif',
                    default => 'Belum diisi',
                },
                'Departemen' => $karyawanstaff->departement ?: 'Belum diisi',
                'Jabatan' => $karyawanstaff->jabatan ?: 'Belum diisi',
                'Lokasi Kerja' => $karyawanstaff->lokasi ?: 'Belum diisi',
                'Tanggal Masuk Kerja' => $karyawanstaff->tmk
                    ? \Carbon\Carbon::parse($karyawanstaff->tmk)->format('d M Y')
                    : 'Belum diisi',
            ],
        ];

        $profileEducation = $personal?->pendidikan;
        $profileExperience = $personal?->pengalaman_kerja;
        $profileAdditional = $personal?->informasi_tambahan;
        $profileFacilities = $karyawanstaff->facilities;
        $serviceDuration = $karyawanstaff->tmk
            ? \Carbon\Carbon::parse($karyawanstaff->tmk)->diff(now())->format('%y tahun %m bulan')
            : 'Belum diisi';

        $profileTabs = [
            ['id' => 'personal', 'label' => 'Data Pribadi', 'icon' => 'person'],
            ['id' => 'employee', 'label' => 'Informasi Pekerjaan', 'icon' => 'badge'],
            ['id' => 'onboarding', 'label' => 'Onboarding', 'icon' => 'checklist'],
        ];
        $completedOnboardingTasks = $karyawanstaff->onboardingAssignments
            ->where('status', 'selesai')
            ->count();
        $totalOnboardingTasks = $karyawanstaff->onboardingAssignments->count();
    @endphp

    <div class="container-xxl flex-grow-1 container-p-y employee-profile">
        @if (session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div>
                <div class="small text-muted mb-1">HRD <span class="mx-1">/</span> Karyawan &amp; Departemen</div>
                <h4 class="fw-bold mb-0">Profil Karyawan</h4>
            </div> 
            
            <div class="d-flex gap-2">
                <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary btn-sm">
                    <span class="material-symbols-outlined align-middle me-1" style="font-size:17px">arrow_back</span>
                    Kembali
                </a>
                {{-- <a href="{{ route('staff.edit', $karyawanstaff->id) }}" class="btn btn-primary btn-sm">
                    <span class="material-symbols-outlined align-middle me-1" style="font-size:17px">edit</span>
                    Ubah Profil
                </a> --}}
            </div>
        </div>

        <section class="card profile-hero mb-3" aria-label="Ringkasan karyawan">
            <div class="card-body p-4 profile-hero-content">
                <div class="d-flex align-items-center flex-wrap gap-3 gap-md-4">
                    <div class="profile-avatar" aria-hidden="true">{{ $initials ?: '?' }}</div>
                    <div class="flex-grow-1">
                        <div class="profile-name mb-1">{{ $karyawanstaff->nama ?: 'Nama belum diisi' }}</div>
                        <div class="profile-meta mb-3">
                            {{ $karyawanstaff->jabatan ?: 'Jabatan belum diisi' }}
                            @if ($karyawanstaff->departement)
                                <span class="mx-1">·</span>{{ $karyawanstaff->departement }}
                            @endif
                            @if ($karyawanstaff->npk)
                                <span class="mx-1">·</span>NPK {{ $karyawanstaff->npk }}
                            @endif
                        </div>
                        <span class="profile-status {{ $isActiveEmployee ? '' : ($karyawanstaff->status === 'ONBOARDING' ? 'is-onboarding' : 'is-inactive') }}">
                            <span class="status-dot" aria-hidden="true"></span>
                            Karyawan {{ $employeeStatusLabel }}
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <nav class="profile-nav mb-3" aria-label="Bagian profil karyawan" role="tablist">
            @foreach ($profileTabs as $index => $tab)
                <button
                    class="nav-link {{ $index === 0 ? 'active' : '' }}"
                    id="tab-{{ $tab['id'] }}"
                    data-bs-toggle="tab"
                    data-bs-target="#panel-{{ $tab['id'] }}"
                    type="button"
                    role="tab"
                    aria-controls="panel-{{ $tab['id'] }}"
                    aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                    <span class="material-symbols-outlined" aria-hidden="true">{{ $tab['icon'] }}</span>
                    {{ $tab['label'] }}
                </button>
            @endforeach
        </nav>

        <div class="tab-content">
            <section class="tab-pane fade show active" id="panel-personal" role="tabpanel"
                aria-labelledby="tab-personal" tabindex="0">
                <div class="profile-panel">
                    <div class="profile-panel-heading">
                        <div class="profile-panel-icon"><span class="material-symbols-outlined">person</span></div>
                        <div>
                            <h5 class="profile-panel-title">Personal</h5>
                            <p class="profile-panel-description">Informasi identitas, alamat, dan kontak karyawan.</p>
                        </div>
                    </div>
                    <div class="profile-panel-content">
                        <div class="personal-summary">
                            <div class="personal-summary-item">
                                <div class="personal-summary-label">
                                    <span class="material-symbols-outlined" aria-hidden="true">event_available</span>
                                    Tanggal Masuk Kerja
                                </div>
                                <p class="personal-summary-value">{{ $karyawanstaff->tmk ? \Carbon\Carbon::parse($karyawanstaff->tmk)->format('d M Y') : 'Belum diisi' }}</p>
                            </div>
                            <div class="personal-summary-item">
                                <div class="personal-summary-label">Masa Kerja</div>
                                <p class="personal-summary-value">{{ $serviceDuration }}</p>
                            </div>
                            <div class="personal-summary-item">
                                <div class="personal-summary-label">Status Karyawan</div>
                                <p class="personal-summary-value">{{ match ($karyawanstaff->status) {
                                    'ONBOARDING' => 'Onboarding',
                                    'AKTIF' => 'Aktif',
                                    'TIDAK AKTIF', 'TIDAKAKTIF' => 'Tidak Aktif',
                                    default => 'Belum diisi',
                                } }}</p>
                            </div>
                        </div>
                        <div class="personal-sections">
                            @foreach ($profilePersonal as $group => $fields)
                                <section class="personal-section">
                                    <h6 class="personal-section-title">
                                        <span class="material-symbols-outlined" aria-hidden="true">
                                            {{ match ($group) {
                                                'Identitas Dasar' => 'id_card',
                                                'Kontak dan Alamat' => 'location_on',
                                                'Kontak Darurat' => 'contact_phone',
                                                'Informasi Perbankan' => 'account_balance',
                                                default => 'person',
                                            } }}
                                        </span>
                                        {{ $group }}
                                    </h6>
                                    <dl class="profile-details">
                                        @foreach ($fields as $label => $value)
                                            <div class="profile-detail-row">
                                                <dt class="profile-detail-label">{{ $label }}</dt>
                                                <dd class="profile-detail-value mb-0">{{ $value }}</dd>
                                            </div>
                                        @endforeach
                                    </dl>
                                </section>
                            @endforeach
                        </div>
                        <div class="personal-extra-grid">
                            <section class="personal-section">
                                <h6 class="info-card-title">
                                    <span class="material-symbols-outlined">workspace_premium</span>
                                    Pendidikan
                                </h6>
                                <p>{{ $profileEducation ?: 'Belum ada informasi pendidikan.' }}</p>
                            </section>
                            <section class="personal-section">
                                <h6 class="info-card-title">
                                    <span class="material-symbols-outlined">work_history</span>
                                    Pengalaman Kerja
                                </h6>
                                <p>{{ $profileExperience ?: 'Belum ada informasi pengalaman kerja.' }}</p>
                            </section>
                            <section class="personal-section is-wide">
                                <h6 class="info-card-title">
                                    <span class="material-symbols-outlined">info</span>
                                    Informasi Tambahan
                                </h6>
                                <p>{{ $profileAdditional ?: 'Belum ada informasi tambahan.' }}</p>
                            </section>
                        </div>
                    </div>
                </div>
            </section>

            <section class="tab-pane fade" id="panel-employee" role="tabpanel"
                aria-labelledby="tab-employee" tabindex="0">
                <div class="profile-panel">
                    <div class="profile-panel-heading">
                        <div class="profile-panel-icon"><span class="material-symbols-outlined">badge</span></div>
                        <div>
                            <h5 class="profile-panel-title">Employee</h5>
                            <p class="profile-panel-description">Informasi pekerjaan untuk karyawan yang dipilih.</p>
                        </div>
                    </div>
                    <div class="profile-panel-content">
                        <div class="row g-3">
                            @foreach ($profileEmployee as $group => $fields)
                                <div class="col-lg-6">
                                    <div class="info-card">
                                        <h6 class="info-card-title">
                                            <span class="material-symbols-outlined">{{ $loop->first ? 'work' : 'description' }}</span>
                                            {{ $group }}
                                        </h6>
                                        <dl class="profile-details">
                                            @foreach ($fields as $label => $value)
                                                <div class="profile-detail-row">
                                                    <dt class="profile-detail-label">{{ $label }}</dt>
                                                    <dd class="profile-detail-value mb-0">{{ $value }}</dd>
                                                </div>
                                            @endforeach
                                        </dl>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-12">
                                <div class="info-card">
                                    <h6 class="info-card-title">
                                        <span class="material-symbols-outlined">apparel</span>
                                        Fasilitas
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="table profile-table">
                                            <thead>
                                                <tr><th>Nama Fasilitas</th><th>Kategori</th><th>Nomor Aset</th><th>Tanggal Pemberian</th><th>Kondisi</th><th>Status</th><th>Tanggal Pengembalian</th><th>Keterangan</th></tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($profileFacilities as $facility)
                                                    <tr>
                                                        <td class="fw-semibold">{{ $facility->nama_fasilitas }}</td>
                                                        <td>{{ $facility->kategori ?: '—' }}</td>
                                                        <td>{{ $facility->nomor_aset }}</td>
                                                        <td>{{ $facility->tanggal_pemberian?->format('d M Y') }}</td>
                                                        <td>{{ $facility->kondisi ?: '—' }}</td>
                                                        <td><span class="item-status is-ready">{{ $facility->status }}</span></td>
                                                        <td>{{ $facility->tanggal_pengembalian?->format('d M Y') ?: '—' }}</td>
                                                        <td>{{ $facility->keterangan ?: '—' }}</td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="8" class="text-center text-muted">Belum ada data fasilitas.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </section>

            <section class="tab-pane fade" id="panel-onboarding" role="tabpanel"
                aria-labelledby="tab-onboarding" tabindex="0">
                <div class="profile-panel">
                    <div class="profile-panel-heading">
                        <div class="profile-panel-icon"><span class="material-symbols-outlined">checklist</span></div>
                        <div class="flex-grow-1">
                            <h5 class="profile-panel-title">Onboarding</h5>
                            <p class="profile-panel-description">Daftar task onboarding dan status pengerjaan karyawan.</p>
                        </div>
                        @if ($availableOnboardingTasks->isNotEmpty())
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#addOnboardingTaskModal">
                                Tambah Task
                            </button>
                        @endif
                        <a href="{{ route('onboarding.index') }}" class="btn btn-outline-primary btn-sm">Kelola onboarding</a>
                    </div>
                    <div class="profile-panel-content">
                        @if ($totalOnboardingTasks > 0)
                            <p class="text-muted mb-3">
                                {{ $completedOnboardingTasks }} dari {{ $totalOnboardingTasks }} task selesai
                            </p>
                        @endif
                        <div class="table-responsive">
                            <table class="table profile-table">
                                <thead>
                                    <tr>
                                        <th>Task</th>
                                        <th>Status</th>
                                        <th>Perbarui Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($karyawanstaff->onboardingAssignments as $assignment)
                                        <tr>
                                            <td class="fw-semibold">{{ $assignment->onboardingTask->nama_task }}</td>
                                            <td>
                                                <span class="item-status {{ $assignment->status === 'selesai' ? 'is-ready' : 'is-info' }}">
                                                    {{ ucfirst($assignment->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <form action="{{ route('onboarding.assignments.status', $assignment) }}" method="POST"
                                                    class="d-flex align-items-center gap-2">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="status" class="form-select form-select-sm" aria-label="Status task onboarding">
                                                        <option value="belum selesai" {{ $assignment->status === 'belum selesai' ? 'selected' : '' }}>Belum selesai</option>
                                                        <option value="selesai" {{ $assignment->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                                    </select>
                                                    <button type="submit" class="btn btn-sm btn-outline-primary">Simpan</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">
                                                Belum ada task onboarding untuk karyawan ini.
                                                <a href="{{ route('onboarding.index') }}">Buka pengelolaan onboarding</a>.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @if ($availableOnboardingTasks->isNotEmpty())
                    <div class="modal fade" id="addOnboardingTaskModal" tabindex="-1"
                        aria-labelledby="addOnboardingTaskModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form action="{{ route('onboarding.staff.assign', $karyawanstaff) }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addOnboardingTaskModalLabel">Tambah Task Onboarding</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                    </div>
                                    <div class="modal-body">
                                        <label for="onboarding-task-select" class="form-label">Pilih task</label>
                                        <select id="onboarding-task-select" name="onboarding_task_id" class="form-select" required>
                                            <option value="" disabled selected>Pilih task onboarding</option>
                                            @foreach ($availableOnboardingTasks as $task)
                                                <option value="{{ $task->id }}" @selected((string) old('onboarding_task_id') === (string) $task->id)>
                                                    {{ $task->nama_task }}
                                                </option>
                                            @endforeach
                                        </select>
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
            </section>

        </div>
    </div>
@endsection
