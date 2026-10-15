<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Karyawan extends Model
{
    use HasFactory;

    public function personal(): HasOne
    {
        return $this->hasOne(Personal::class, 'karyawan_id');
    }

    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class, 'karyawan_id');
    }

}
