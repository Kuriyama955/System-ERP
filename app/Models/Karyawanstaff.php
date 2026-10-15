<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Karyawanstaff extends Model
{
    use HasFactory;

    public function personal(): HasOne
    {
        return $this->hasOne(Personal::class, 'karyawanstaff_id');
    }

    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class, 'karyawanstaff_id');
    }

    public function onboardingAssignments(): HasMany
    {
        return $this->hasMany(OnboardingTaskKaryawan::class, 'karyawanstaff_id');
    }

}
