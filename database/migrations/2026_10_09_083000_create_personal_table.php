<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('personal')) {
            Schema::table('personal', function (Blueprint $table) {
                $table->foreign('karyawan_id')
                    ->references('id')
                    ->on('karyawans')
                    ->cascadeOnDelete();

                $table->foreign('karyawanstaff_id')
                    ->references('id')
                    ->on('karyawanstaffs')
                    ->cascadeOnDelete();
            });

            return;
        }

        Schema::create('personal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')
                ->nullable()
                ->unique()
                ->constrained('karyawans')
                ->cascadeOnDelete();
            $table->foreignId('karyawanstaff_id')
                ->nullable()
                ->unique()
                ->constrained('karyawanstaffs')
                ->cascadeOnDelete();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_telepon', 20)->nullable();
            $table->string('email_pribadi')->nullable();
            $table->string('agama', 50)->nullable();
            $table->string('status_pernikahan', 30)->nullable();
            $table->string('pendidikan', 255)->nullable();
            $table->string('pengalaman_kerja', 255)->nullable();
            $table->string('informasi_tambahan', 255)->nullable();
            $table->string('nama_bank', 100)->nullable();
            $table->string('nomor_rekening', 50)->nullable();
            $table->string('nama_pemilik_rekening', 255)->nullable();
            $table->string('cabang_bank', 100)->nullable();
            $table->string('nik', 100)->nullable();
            $table->string('jenis_kelamin', 50)->nullable();
            $table->string('golongan_darah', 5)->nullable();
            $table->text('alamat_ktp')->nullable();
            $table->string('nama_kontak_darurat', 255)->nullable();
            $table->string('hubungan_kontak_darurat', 100)->nullable();
            $table->string('telepon_kontak_darurat', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal');
    }
};
