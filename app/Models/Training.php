<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    protected $table = 'trainings';

    protected $fillable = [
        'nama_pelatihan',
        'tema',
        'tanggal_aktual',
        'lokasi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_aktual' => 'date',
        ];
    }
}
