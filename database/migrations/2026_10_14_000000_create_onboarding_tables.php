<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onboarding_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_task');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('onboarding_task_karyawan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('onboarding_task_id')
                ->constrained('onboarding_tasks')
                ->cascadeOnDelete();
            $table->foreignId('karyawanstaff_id')
                ->constrained('karyawanstaffs')
                ->cascadeOnDelete();
            $table->string('status')->default('belum selesai');
            $table->timestamps();

            $table->unique(
                ['onboarding_task_id', 'karyawanstaff_id'],
                'otk_task_staff_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboarding_task_karyawan');
        Schema::dropIfExists('onboarding_tasks');
    }
};
