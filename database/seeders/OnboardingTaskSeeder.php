<?php

namespace Database\Seeders;

use App\Models\OnboardingTask;
use Illuminate\Database\Seeder;
use RuntimeException;

class OnboardingTaskSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('OnboardingTaskSeeder hanya boleh dijalankan di environment non-production.');
        }

        foreach ([
            'Induksi',
            'Pelengkapan Data',
            'Serah Terima Fasilitas',
        ] as $namaTask) {
            OnboardingTask::query()->firstOrCreate(
                ['nama_task' => $namaTask],
                ['is_active' => true]
            );
        }
    }
}
