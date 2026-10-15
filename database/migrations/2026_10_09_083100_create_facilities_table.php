<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();

            $table->foreignId('karyawan_id')
                ->nullable()
                ->constrained('karyawans')
                ->cascadeOnDelete();

            $table->foreignId('karyawanstaff_id')
                ->nullable()
                ->constrained('karyawanstaffs')
                ->cascadeOnDelete();

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
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
