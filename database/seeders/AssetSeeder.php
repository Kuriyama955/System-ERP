<?php

namespace Database\Seeders;

use App\Models\Asset;
use Illuminate\Database\Seeder;
use RuntimeException;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('AssetSeeder hanya boleh dijalankan di environment non-production.');
        }

        foreach ([
            ['name' => 'Laptop Dell Latitude', 'code' => 'AST-LAP-001'],
            ['name' => 'Laptop Lenovo ThinkPad', 'code' => 'AST-LAP-002'],
            ['name' => 'Laptop HP ProBook', 'code' => 'AST-LAP-003'],
            ['name' => 'Laptop ASUS ExpertBook', 'code' => 'AST-LAP-004'],
            ['name' => 'Laptop Acer TravelMate', 'code' => 'AST-LAP-005'],
            ['name' => 'Monitor 24 Inch', 'code' => 'AST-MON-001'],
            ['name' => 'Printer Laser', 'code' => 'AST-PRN-001'],
            ['name' => 'Proyektor', 'code' => 'AST-PRJ-001'],
            ['name' => 'Kursi Kerja', 'code' => 'AST-KRS-001'],
            ['name' => 'Meja Kerja', 'code' => 'AST-MJA-001'],
        ] as $asset) {
            Asset::query()->firstOrCreate(
                ['code' => $asset['code']],
                ['name' => $asset['name']]
            );
        }
    }
}
