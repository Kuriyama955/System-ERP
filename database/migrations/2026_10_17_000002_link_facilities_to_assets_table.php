<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->foreignId('asset_id')->nullable()->constrained('assets')->restrictOnDelete();
        });

        DB::table('facilities')
            ->orderBy('id')
            ->get(['id', 'nama_fasilitas', 'nomor_aset'])
            ->each(function ($facility): void {
                $name = trim((string) $facility->nama_fasilitas) ?: 'Asset lama';
                $code = trim((string) $facility->nomor_aset);

                if ($code === '') {
                    $code = 'LEGACY-FAC-'.$facility->id;
                }

                $assetId = DB::table('assets')->where('code', $code)->value('id');
                if (! $assetId) {
                    $assetId = DB::table('assets')->insertGetId([
                        'name' => $name,
                        'code' => $code,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('facilities')
                    ->where('id', $facility->id)
                    ->update(['asset_id' => $assetId]);
            });

        Schema::table('facilities', function (Blueprint $table) {
            $table->dropColumn(['nama_fasilitas', 'kategori', 'nomor_aset', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->string('nama_fasilitas', 150)->nullable();
            $table->string('kategori', 100)->nullable();
            $table->string('nomor_aset', 100)->nullable();
            $table->string('status')->default('Diberikan');
        });

        DB::table('facilities')
            ->leftJoin('assets', 'facilities.asset_id', '=', 'assets.id')
            ->select('facilities.id', 'assets.name', 'assets.code')
            ->get()
            ->each(function ($facility): void {
                DB::table('facilities')
                    ->where('id', $facility->id)
                    ->update([
                        'nama_fasilitas' => $facility->name ?? 'Asset',
                        'nomor_aset' => $facility->code,
                    ]);
            });

        Schema::table('facilities', function (Blueprint $table) {
            $table->dropConstrainedForeignId('asset_id');
        });
    }
};
