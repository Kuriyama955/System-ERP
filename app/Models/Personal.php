<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Personal extends Model
{
    protected $table = 'personal';

    protected $fillable = [
        'karyawan_id',
        'karyawanstaff_id',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'no_telepon',
        'email_pribadi',
        'agama',
        'status_pernikahan',
        'pendidikan',
        'pengalaman_kerja',
        'informasi_tambahan',
        'nama_bank',
        'nomor_rekening',
        'nama_pemilik_rekening',
        'cabang_bank',
        'nik',
        'jenis_kelamin',
        'golongan_darah',
        'alamat_ktp',
        'nama_kontak_darurat',
        'hubungan_kontak_darurat',
        'telepon_kontak_darurat',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function karyawanstaff(): BelongsTo
    {
        return $this->belongsTo(Karyawanstaff::class, 'karyawanstaff_id');
    }
}
