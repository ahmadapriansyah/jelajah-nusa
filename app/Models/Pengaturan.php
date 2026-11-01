<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    use HasFactory;

    protected $table = 'pengaturan';

    protected $fillable = [
        'nama_bank',
        'no_rekening',
        'atas_nama',
        'batas_bayar_jam',
    ];

    protected function casts(): array
    {
        return [
            'batas_bayar_jam' => 'integer',
        ];
    }

    public static function getSettings(): self
    {
        return static::firstOrCreate([], [
            'nama_bank' => 'Bank Mandiri',
            'no_rekening' => '138-00-1234567-8',
            'atas_nama' => 'PT Jelajah Nusa Nusantara',
            'batas_bayar_jam' => 24,
        ]);
    }
}
