<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomTrip extends Model
{
    use HasFactory;

    protected $table = 'custom_trip';

    protected $fillable = [
        'user_id',
        'kendaraan_id',
        'tujuan',
        'tanggal_diinginkan',
        'durasi_hari',
        'jumlah_peserta',
        'kebutuhan',
        'fasilitas_penawaran',
        'jadwal_penawaran',
        'biaya_penawaran',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_diinginkan' => 'date',
            'jadwal_penawaran' => 'date',
            'durasi_hari' => 'integer',
            'jumlah_peserta' => 'integer',
            'biaya_penawaran' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kendaraan(): BelongsTo
    {
        return $this->belongsTo(Kendaraan::class, 'kendaraan_id');
    }

    public function pemesanan(): HasOne
    {
        return $this->hasOne(Pemesanan::class, 'custom_trip_id');
    }

    public function getBiayaPenawaranFormatAttribute(): string
    {
        return $this->biaya_penawaran ? 'Rp ' . number_format($this->biaya_penawaran, 0, ',', '.') : '—';
    }
}
