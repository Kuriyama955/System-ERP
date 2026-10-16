<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Facility extends Model
{
    protected $table = 'facilities';

    protected $fillable = [
        'karyawan_id',
        'karyawanstaff_id',
        'asset_id',
        'tanggal_pemberian',
        'kondisi',
        'tanggal_pengembalian',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pemberian' => 'date',
            'tanggal_pengembalian' => 'date',
        ];
    }

    // relasi karyawan
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    // relasi karyawanstaff
    public function karyawanstaff(): BelongsTo
    {
        return $this->belongsTo(Karyawanstaff::class, 'karyawanstaff_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
