<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::dropIfExists('assets');

    $migration = require base_path('database/migrations/2026_10_17_000001_create_assets_table.php');
    $migration->up();
});

afterEach(function () {
    Schema::dropIfExists('assets');
});

test('HRD can list, create and edit assets', function () {
    $this->get(route('asset.index'))
        ->assertOk()
        ->assertSee('Manajemen Asset')
        ->assertSee('Belum ada data asset');

    $this->post(route('asset.store'), [
        '_form' => 'create_asset',
        'name' => 'Laptop',
        'code' => 'AST-001',
    ])->assertRedirect(route('asset.index'));

    $assetId = DB::table('assets')->value('id');
    $this->assertDatabaseHas('assets', [
        'id' => $assetId,
        'name' => 'Laptop',
        'code' => 'AST-001',
    ]);

    $this->get(route('asset.index', ['search' => 'AST-001']))
        ->assertOk()
        ->assertSee('Laptop')
        ->assertSee('AST-001');

    $this->put(route('asset.update', $assetId), [
        '_form' => 'edit_asset',
        'name' => 'Laptop Kerja',
        'code' => 'AST-001',
    ])->assertRedirect(route('asset.index'));

    $this->assertDatabaseHas('assets', [
        'id' => $assetId,
        'name' => 'Laptop Kerja',
        'code' => 'AST-001',
    ]);
});

test('asset code must be unique', function () {
    DB::table('assets')->insert([
        'name' => 'Laptop',
        'code' => 'AST-001',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->from(route('asset.index'))
        ->post(route('asset.store'), [
            '_form' => 'create_asset',
            'name' => 'Monitor',
            'code' => 'AST-001',
        ])
        ->assertSessionHasErrors('code');

    $this->assertDatabaseCount('assets', 1);
});

test('asset seeder creates ten demo assets idempotently', function () {
    $seeder = app(Database\Seeders\AssetSeeder::class);
    $seeder->run();
    $seeder->run();

    $this->assertDatabaseCount('assets', 10);
    expect(DB::table('assets')->where('code', 'like', 'AST-LAP-%')->count())->toBe(5);
    $this->assertDatabaseHas('assets', [
        'name' => 'Laptop Dell Latitude',
        'code' => 'AST-LAP-001',
    ]);
    $this->assertDatabaseHas('assets', [
        'name' => 'Monitor 24 Inch',
        'code' => 'AST-MON-001',
    ]);
});