<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::dropIfExists('facilities');
    Schema::dropIfExists('assets');

    Schema::create('assets', function ($table) {
        $table->id();
        $table->string('name');
        $table->string('code', 100)->unique();
        $table->timestamps();
    });

    Schema::create('facilities', function ($table) {
        $table->id();
        $table->unsignedBigInteger('karyawan_id')->nullable();
        $table->unsignedBigInteger('karyawanstaff_id')->nullable();
        $table->string('nama_fasilitas', 150);
        $table->string('kategori', 100)->nullable();
        $table->string('nomor_aset', 100)->nullable();
        $table->date('tanggal_pemberian')->nullable();
        $table->string('kondisi', 50)->nullable();
        $table->string('status')->default('Diberikan');
        $table->date('tanggal_pengembalian')->nullable();
        $table->text('keterangan')->nullable();
        $table->timestamps();
    });
});

afterEach(function () {
    Schema::dropIfExists('facilities');
    Schema::dropIfExists('assets');
});

test('facility migration links existing asset details and can restore legacy columns', function () {
    DB::table('facilities')->insert([
        'nama_fasilitas' => 'Laptop kerja',
        'kategori' => 'Elektronik',
        'nomor_aset' => 'LTP-LEGACY-01',
        'tanggal_pemberian' => '2026-10-17',
        'kondisi' => 'Baik',
        'status' => 'Diberikan',
        'keterangan' => 'Data lama',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $migration = require base_path('database/migrations/2026_10_17_000002_link_facilities_to_assets_table.php');
    $migration->up();

    $facility = DB::table('facilities')->first();
    expect(DB::table('assets')->where('id', $facility->asset_id)->value('name'))
        ->toBe('Laptop kerja')
        ->and(DB::table('assets')->where('id', $facility->asset_id)->value('code'))
        ->toBe('LTP-LEGACY-01')
        ->and(Schema::hasColumn('facilities', 'status'))
        ->toBeFalse();

    $migration->down();

    $facility = DB::table('facilities')->first();
    expect($facility->nama_fasilitas)
        ->toBe('Laptop kerja')
        ->and($facility->nomor_aset)
        ->toBe('LTP-LEGACY-01')
        ->and(Schema::hasColumn('facilities', 'asset_id'))
        ->toBeFalse();
});
