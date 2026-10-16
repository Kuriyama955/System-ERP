@extends('layouts.main')

@section('page-title', 'Detail Training')

@section('main-content')
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,400,0,0&display=swap" />

    @php
        $preAvg = is_numeric($preTestAverage ?? null) ? (float) $preTestAverage : null;
        $postAvg = is_numeric($postTestAverage ?? null) ? (float) $postTestAverage : null;
        $avgGain = $preAvg !== null && $postAvg !== null ? $postAvg - $preAvg : null;
        $formatDelta = function ($value) {
            $text = \App\Models\TrainingParticipant::formatScore((string) abs($value));
            return ($value > 0 ? '+' : ($value < 0 ? '−' : '')) . $text;
        };
        $deltaClass = fn($value) => $value > 0 ? 'is-up' : ($value < 0 ? 'is-down' : 'is-flat');
    @endphp

    <style>
        .training-detail {
            --td-accent: #696cff;
            --td-accent-soft: #eef0ff;
            --td-border: #eceef2;
            --td-text: #32334a;
            --td-muted: #7c8190;
            --td-success: #28a745;
            --td-danger: #e5484d;
        }

        .training-detail .material-symbols-outlined {
            display: inline-block;
            flex: 0 0 auto;
            font-family: 'Material Symbols Outlined';
            font-size: 20px;
            font-style: normal;
            font-weight: 400;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            -webkit-font-feature-settings: 'liga';
            -webkit-font-smoothing: antialiased;
            font-feature-settings: 'liga';
        }

        /* ---------- Hero ---------- */
        .td-hero {
            position: relative;
            padding: 24px 28px;
            border: 1px solid #e7e8fb;
            border-radius: 16px;
            background: linear-gradient(120deg, #f4f5ff 0%, #ffffff 70%);
            overflow: hidden;
        }

        .td-hero::after {
            content: "";
            position: absolute;
            right: -60px;
            top: -60px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(105, 108, 255, .14), transparent 70%);
            pointer-events: none;
        }

        .td-breadcrumb {
            font-size: 12px;
            color: var(--td-muted);
            margin-bottom: 8px;
        }

        .td-hero h4 {
            color: var(--td-text);
            letter-spacing: -.2px;
        }

        .td-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }

        .td-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border: 1px solid var(--td-border);
            border-radius: 999px;
            background: #fff;
            color: #565b69;
            font-size: 12.5px;
            line-height: 1;
        }

        .td-chip .material-symbols-outlined {
            font-size: 16px;
            color: var(--td-accent);
        }

        /* ---------- Cards ---------- */
        .td-card {
            overflow: hidden;
            border: 1px solid var(--td-border);
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(50, 50, 93, .05);
            transition: box-shadow .2s ease, transform .2s ease;
        }

        .td-stat:hover {
            box-shadow: 0 8px 24px rgba(50, 50, 93, .09);
            transform: translateY(-2px);
        }

        .td-stat-icon {
            display: flex;
            width: 46px;
            height: 46px;
            flex: 0 0 46px;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: var(--td-accent-soft);
            color: var(--td-accent);
        }

        .td-stat-icon .material-symbols-outlined {
            font-size: 24px;
        }

        .td-stat-icon.is-success {
            background: #e8f7ec;
            color: var(--td-success);
        }

        .td-stat-icon.is-warn {
            background: #fff4e0;
            color: #f59f00;
        }

        .td-stat-label {
            color: var(--td-muted);
            font-size: 12.5px;
        }

        .td-stat-value {
            color: var(--td-text);
            font-size: 24px;
            font-weight: 700;
            line-height: 1.2;
        }

        /* ---------- Toolbar ---------- */
        .td-card .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--td-border);
            background: #fff;
        }

        .td-search {
            position: relative;
            min-width: 260px;
        }

        .td-search .material-symbols-outlined {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: #a1a6b5;
            pointer-events: none;
        }

        .td-search input {
            padding-left: 38px;
            border-radius: 10px;
            border-color: var(--td-border);
        }

        .td-search input:focus {
            border-color: var(--td-accent);
            box-shadow: 0 0 0 .2rem rgba(105, 108, 255, .12);
        }

        /* ---------- Table ---------- */
        .td-table {
            margin-bottom: 0;
        }

        .td-table thead th {
            padding: 12px 16px;
            border-bottom: 1px solid var(--td-border);
            background: #f8f8fb;
            color: #8b91a0;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .55px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .td-table tbody td,
        .td-table tfoot td {
            padding: 14px 16px;
            border-bottom-color: #f0f1f4;
            color: #565b69;
            font-size: 13px;
            vertical-align: middle;
        }

        .td-table tbody tr {
            transition: background .15s ease;
        }

        .td-table tbody tr:hover {
            background: #fafaff;
        }

        .td-table tfoot td {
            border-top: 1px solid var(--td-border);
            border-bottom: 0;
            background: #f8f8fb;
            font-weight: 600;
        }

        .td-avatar {
            display: inline-flex;
            width: 36px;
            height: 36px;
            flex: 0 0 36px;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: var(--td-accent-soft);
            color: var(--td-accent);
            font-size: 12px;
            font-weight: 700;
        }

        .td-name {
            color: var(--td-text);
            font-weight: 600;
            text-decoration: none;
        }

        .td-name:hover {
            color: var(--td-accent);
        }

        .td-npk {
            color: var(--td-muted);
            font-size: 12px;
        }

        .td-dept {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            background: #f3f4f7;
            color: #565b69;
            font-size: 12px;
            font-weight: 500;
        }

        .td-score {
            font-weight: 600;
            color: var(--td-text);
            font-variant-numeric: tabular-nums;
        }

        .td-delta {
            display: inline-block;
            margin-left: 6px;
            padding: 2px 7px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
        }

        .td-delta.is-up { background: #e8f7ec; color: var(--td-success); }
        .td-delta.is-down { background: #fdeaea; color: var(--td-danger); }
        .td-delta.is-flat { background: #f3f4f7; color: var(--td-muted); }

        .td-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .td-badge.is-success { background: #e8f7ec; color: var(--td-success); }
        .td-badge.is-danger { background: #fdeaea; color: var(--td-danger); }
        .td-badge.is-neutral { background: #f3f4f7; color: #565b69; }

        .td-average {
            color: var(--td-accent) !important;
            font-size: 14px !important;
            font-weight: 700 !important;
            font-variant-numeric: tabular-nums;
        }

        .td-empty {
            padding: 48px 16px !important;
            text-align: center;
            color: var(--td-muted);
        }

        .td-empty .material-symbols-outlined {
            display: block;
            margin-bottom: 8px;
            font-size: 40px;
            color: #c9ccd8;
        }

        .td-no-result {
            display: none;
            padding: 36px 16px;
            text-align: center;
            color: var(--td-muted);
            font-size: 13px;
        }

        @media (max-width: 767.98px) {
            .td-hero { padding: 20px; }
            .td-search { min-width: 100%; }
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y training-detail">
        @if (session('success'))
            <div class="alert alert-success d-flex align-items-center gap-2" role="status">
                <span class="material-symbols-outlined" style="font-size:20px">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        {{-- Hero --}}
        <div class="td-hero d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
            <div style="position:relative; z-index:1">
                <div class="td-breadcrumb">HRD <span class="mx-1">/</span> Training <span class="mx-1">/</span> Detail</div>
                <h4 class="fw-bold mb-0">{{ $training->nama_pelatihan }}</h4>
                <div class="td-chips">
                    <span class="td-chip">
                        <span class="material-symbols-outlined">topic</span>
                        {{ $training->tema ?: 'Tanpa tema' }}
                    </span>
                    <span class="td-chip">
                        <span class="material-symbols-outlined">calendar_month</span>
                        {{ $training->tanggal_aktual->format('d M Y') }}
                    </span>
                    @if ($training->lokasi)
                        <span class="td-chip">
                            <span class="material-symbols-outlined">location_on</span>
                            {{ $training->lokasi }}
                        </span>
                    @endif
                </div>
            </div>
            <a href="{{ route('training.index') }}" class="btn btn-outline-secondary" style="position:relative; z-index:1">
                <span class="material-symbols-outlined align-middle me-1" style="font-size:18px">arrow_back</span>
                Kembali
            </a>
        </div>

        {{-- Statistik --}}
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card td-card td-stat h-100">
                    <div class="card-body d-flex align-items-center gap-3 p-4">
                        <div class="td-stat-icon"><span class="material-symbols-outlined">groups</span></div>
                        <div>
                            <div class="td-stat-label">Jumlah Peserta</div>
                            <div class="td-stat-value">{{ $training->participants->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card td-card td-stat h-100">
                    <div class="card-body d-flex align-items-center gap-3 p-4">
                        <div class="td-stat-icon is-warn"><span class="material-symbols-outlined">edit_note</span></div>
                        <div>
                            <div class="td-stat-label">Rata-rata Pre-test</div>
                            <div class="td-stat-value">
                                {{ $preTestAverage === null ? '—' : \App\Models\TrainingParticipant::formatScore((string) $preTestAverage) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card td-card td-stat h-100">
                    <div class="card-body d-flex align-items-center gap-3 p-4">
                        <div class="td-stat-icon is-success"><span class="material-symbols-outlined">task_alt</span></div>
                        <div>
                            <div class="td-stat-label">Rata-rata Post-test</div>
                            <div class="td-stat-value">
                                {{ $postTestAverage === null ? '—' : \App\Models\TrainingParticipant::formatScore((string) $postTestAverage) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card td-card td-stat h-100">
                    <div class="card-body d-flex align-items-center gap-3 p-4">
                        <div class="td-stat-icon"><span class="material-symbols-outlined">trending_up</span></div>
                        <div>
                            <div class="td-stat-label">Peningkatan Rata-rata</div>
                            <div class="td-stat-value">
                                @if ($avgGain === null)
                                    —
                                @else
                                    <span class="td-delta {{ $deltaClass($avgGain) }}" style="margin-left:0; font-size:18px; padding:2px 10px">
                                        {{ $formatDelta($avgGain) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel peserta --}}
        <div class="card td-card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h5 class="fw-semibold mb-1">Peserta Training</h5>
                    <p class="text-muted small mb-0">Detail nilai dan hasil lengkap dapat dilihat di profil masing-masing staff.</p>
                </div>
                @if ($training->participants->isNotEmpty())
                    <div class="td-search">
                        <span class="material-symbols-outlined">search</span>
                        <input type="search" id="participantSearch" class="form-control"
                            placeholder="Cari nama, NPK, atau departemen…" aria-label="Cari peserta">
                    </div>
                @endif
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle td-table" id="participantTable">
                    <thead>
                        <tr>
                            <th scope="col">Karyawan</th>
                            <th scope="col">Departemen</th>
                            <th scope="col">Pre-test</th>
                            <th scope="col">Post-test</th>
                            <th scope="col">Hasil Training</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($training->participants as $participant)
                            @php
                                $nama = $participant->karyawanstaff->nama;
                                $initials = \Illuminate\Support\Str::upper(
                                    collect(preg_split('/\s+/', trim($nama)))
                                        ->filter()
                                        ->take(2)
                                        ->map(fn($w) => mb_substr($w, 0, 1))
                                        ->join(''),
                                );
                                $pre = $participant->nilai_pre_test;
                                $post = $participant->nilai_post_test;
                                $gain = is_numeric($pre) && is_numeric($post) ? (float) $post - (float) $pre : null;

                                $hasil = trim((string) $participant->hasil_training);
                                $hasilLower = mb_strtolower($hasil);
                                $hasilClass = 'is-neutral';
                                if ($hasil !== '') {
                                    if (str_contains($hasilLower, 'tidak') || str_contains($hasilLower, 'gagal')) {
                                        $hasilClass = 'is-danger';
                                    } elseif (str_contains($hasilLower, 'lulus') || str_contains($hasilLower, 'baik')) {
                                        $hasilClass = 'is-success';
                                    }
                                }
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="td-avatar" aria-hidden="true">{{ $initials }}</span>
                                        <div>
                                            <a href="{{ route('staff.show', $participant->karyawanstaff_id) }}"
                                                class="td-name">{{ $nama }}</a>
                                            <div class="td-npk">NPK {{ $participant->karyawanstaff->npk ?: '—' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($participant->karyawanstaff->departement)
                                        <span class="td-dept">{{ $participant->karyawanstaff->departement }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="td-score">{{ \App\Models\TrainingParticipant::formatScore($pre) }}</td>
                                <td class="td-score">
                                    {{ \App\Models\TrainingParticipant::formatScore($post) }}
                                    @if ($gain !== null)
                                        <span class="td-delta {{ $deltaClass($gain) }}" title="Selisih dari pre-test">{{ $formatDelta($gain) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($hasil !== '')
                                        <span class="td-badge {{ $hasilClass }}">{{ $hasil }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('staff.show', $participant->karyawanstaff_id) }}"
                                        class="btn btn-sm btn-outline-primary">Lihat Profil</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="td-empty">
                                    <span class="material-symbols-outlined">person_off</span>
                                    Belum ada peserta untuk training ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($training->participants->isNotEmpty())
                        <tfoot>
                            <tr>
                                <td colspan="2">Rata-rata Nilai</td>
                                <td class="td-average">
                                    {{ $preTestAverage === null ? '—' : \App\Models\TrainingParticipant::formatScore((string) $preTestAverage) }}
                                </td>
                                <td class="td-average">
                                    {{ $postTestAverage === null ? '—' : \App\Models\TrainingParticipant::formatScore((string) $postTestAverage) }}
                                </td>
                                <td colspan="2" class="text-muted small fw-normal">Nilai kosong tidak dihitung.</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            <div class="td-no-result" id="participantNoResult">
                Tidak ada peserta yang cocok dengan pencarian.
            </div>
        </div>
    </div>

    <script>
        (function () {
            var input = document.getElementById('participantSearch');
            if (!input) return;

            var rows = document.querySelectorAll('#participantTable tbody tr');
            var noResult = document.getElementById('participantNoResult');

            input.addEventListener('input', function () {
                var q = input.value.trim().toLowerCase();
                var visible = 0;

                rows.forEach(function (row) {
                    var match = row.textContent.toLowerCase().indexOf(q) !== -1;
                    row.style.display = match ? '' : 'none';
                    if (match) visible++;
                });

                noResult.style.display = visible === 0 ? 'block' : 'none';
            });
        })();
    </script>
@endsection