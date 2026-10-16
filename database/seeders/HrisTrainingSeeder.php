<?php

namespace Database\Seeders;

use App\Models\Training;
use App\Models\Karyawanstaff;
use Illuminate\Database\Seeder;
use RuntimeException;

class HrisTrainingSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('HrisTrainingSeeder hanya boleh dijalankan di environment non-production.');
        }

        $participants = Karyawanstaff::query()
            ->orderBy('id')
            ->limit(3)
            ->get();

        foreach ([
            'Pelatihan Keselamatan Kerja (DEMO)' => 'Keselamatan Kerja',
            'Pelatihan Komunikasi Kerja (DEMO)' => 'Komunikasi Kerja',
            'Pelatihan Dasar K3 (DEMO)' => 'Keselamatan dan Kesehatan Kerja',
        ] as $name => $theme) {
            $training = Training::query()->firstOrCreate(
                ['nama_pelatihan' => $name],
                [
                    'tema' => $theme,
                    'tanggal_aktual' => now()->toDateString(),
                    'lokasi' => 'Lokasi DEMO',
                ]
            );

            foreach ($participants as $index => $karyawanstaff) {
                $isComplete = $index === 0;

                $training->participants()->updateOrCreate(
                    ['karyawanstaff_id' => $karyawanstaff->id],
                    [
                        'nilai_pre_test' => $isComplete ? 0 : null,
                        'nilai_post_test' => $isComplete ? 0 : null,
                        'hasil_training' => $isComplete
                            ? 'DATA DEMO - memahami materi training.'
                            : null,
                    ]
                );
            }
        }
    }
}
