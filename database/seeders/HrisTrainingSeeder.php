<?php

namespace Database\Seeders;

use App\Models\Training;
use Illuminate\Database\Seeder;
use RuntimeException;

class HrisTrainingSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('HrisTrainingSeeder hanya boleh dijalankan di environment non-production.');
        }

        foreach ([
            'Pelatihan Keselamatan Kerja (DEMO)' => 'Keselamatan Kerja',
            'Pelatihan Komunikasi Kerja (DEMO)' => 'Komunikasi Kerja',
            'Pelatihan Dasar K3 (DEMO)' => 'Keselamatan dan Kesehatan Kerja',
        ] as $name => $theme) {
            Training::query()->firstOrCreate(
                ['nama_pelatihan' => $name],
                [
                    'tema' => $theme,
                    'tanggal_aktual' => now()->toDateString(),
                    'lokasi' => 'Lokasi DEMO',

                ]
            );
        }
    }
}
