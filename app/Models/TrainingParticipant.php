<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingParticipant extends Model
{
    protected $fillable = [
        'training_id',
        'karyawanstaff_id',
        'nilai_pre_test',
        'nilai_post_test',
        'hasil_training',
    ];

    protected function casts(): array
    {
        return [
            'nilai_pre_test' => 'decimal:2',
            'nilai_post_test' => 'decimal:2',
        ];
    }

    public static function formatScore(?string $score): string
    {
        if ($score === null) {
            return '';
        }

        return rtrim(rtrim(number_format((float) $score, 2, '.', ''), '0'), '.');
    }

    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }

    public function karyawanstaff(): BelongsTo
    {
        return $this->belongsTo(Karyawanstaff::class);
    }
}
