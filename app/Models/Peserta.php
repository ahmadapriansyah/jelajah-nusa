<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Peserta extends Model
{
    use HasFactory;

    protected $table = 'peserta';

    protected $fillable = [
        'pemesanan_id',
        'nama_lengkap',
        'jenis_identitas',
        'no_identitas',
        'file_identitas',
        'no_hp',
    ];

    public function pemesanan(): BelongsTo
    {
        return $this->belongsTo(Pemesanan::class, 'pemesanan_id');
    }

    public function tiket(): HasOne
    {
        return $this->hasOne(Tiket::class, 'peserta_id');
    }
}
