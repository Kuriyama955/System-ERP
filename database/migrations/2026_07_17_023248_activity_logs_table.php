<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // Tabel proses_produksis dikelola di luar migration Laravel ini.
            $table->unsignedBigInteger('proses_produksi_id');

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Inti riwayat perubahan
            $table->string('field_name', 255);       // Nama kolom yang dirubah (e.g., input, jtpcs)
            $table->text('old_value')->nullable();  // Nilai sebelum diedit
            $table->text('new_value')->nullable();  // Nilai baru hasil edit

            // Waktu kejadian: Hanya butuh created_at karena log tidak boleh diedit (immutable)
            $table->timestamp('created_at')->useCurrent();

            // INDEXING: Agar performa filter dan search di halaman log tetap cepat
            $table->index('created_at');
            $table->index(['proses_produksi_id', 'field_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
