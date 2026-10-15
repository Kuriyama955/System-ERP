<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingTaskKaryawan extends Model
{
    protected $table = 'onboarding_task_karyawan';

    protected $fillable = [
        'onboarding_task_id',
        'karyawanstaff_id',
        'status',
    ];

    public function onboardingTask(): BelongsTo
    {
        return $this->belongsTo(OnboardingTask::class);
    }

    public function karyawanstaff(): BelongsTo
    {
        return $this->belongsTo(Karyawanstaff::class);
    }
}
