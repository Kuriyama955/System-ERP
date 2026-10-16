<?php

namespace Database\Seeders;

use App\Models\Departemen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartemenSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['karyawans', 'karyawanstaffs'] as $table) {
            DB::table($table)
                ->whereNotNull('departement')
                ->distinct()
                ->pluck('departement')
                ->map(fn (string $name) => trim($name))
                ->filter()
                ->unique()
                ->each(fn (string $name) => Departemen::query()->firstOrCreate([
                    'nama_departemen' => $name,
                ]));
        }
    }
}
