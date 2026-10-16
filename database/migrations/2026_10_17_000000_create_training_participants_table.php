<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')
                ->constrained('trainings')
                ->cascadeOnDelete();
            $table->foreignId('karyawanstaff_id')
                ->constrained('karyawanstaffs')
                ->cascadeOnDelete();
            $table->decimal('nilai_pre_test', 5, 2)->nullable();
            $table->decimal('nilai_post_test', 5, 2)->nullable();
            $table->text('hasil_training')->nullable();
            $table->timestamps();

            $table->unique(['training_id', 'karyawanstaff_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_participants');
    }
};
