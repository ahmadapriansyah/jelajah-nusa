<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pemesanan extends Model
{
    use HasFactory;

    protected $table = 'pemesanan';

    protected $fillable = [
        'kode_pemesanan',
        'user_id',
        'jadwal_id',
        'custom_trip_id',
        'jumlah_peserta',
        'total_biaya',
        'batas_bayar',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total_biaya' => 'decimal:2',
            'batas_bayar' => 'datetime',
            'jumlah_peserta' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(JadwalKeberangkatan::class, 'jadwal_id');
    }

    public function customTrip(): BelongsTo
    {
        return $this->belongsTo(CustomTrip::class, 'custom_trip_id');
    }

    public function peserta(): HasMany
    {
        return $this->hasMany(Peserta::class, 'pemesanan_id');
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'pemesanan_id');
    }

    public function tiket(): HasMany
    {
        return $this->hasMany(Tiket::class, 'pemesanan_id');
    }

    public function pengembalian(): HasMany
    {
        return $this->hasMany(Pengembalian::class, 'pemesanan_id');
    }

    public function getTotalTerbayarAttribute(): float
    {
        return (float) $this->pembayaran()
            ->where('status', 'diverifikasi')
            ->sum('nominal');
    }

    public function getSisaTagihanAttribute(): float
    {
        return max(0, (float) $this->total_biaya - $this->total_terbayar);
    }

    public function getIsLunasAttribute(): bool
    {
        return $this->total_terbayar >= (float) $this->total_biaya && (float) $this->total_biaya > 0;
    }

    public function getTotalBiayaFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->total_biaya, 0, ',', '.');
    }

    public function getTotalTerbayarFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->total_terbayar, 0, ',', '.');
    }

    public function getSisaTagihanFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->sisa_tagihan, 0, ',', '.');
    }

    public function getDisplayKodeAttribute(): string
    {
        return '#' . ltrim($this->kode_pemesanan, '#');
    }

    public function getJudulTripAttribute(): string
    {
        if ($this->jadwal && $this->jadwal->paket) {
            return $this->jadwal->paket->nama_paket;
        }
        if ($this->customTrip) {
            return 'Custom Trip: ' . $this->customTrip->tujuan;
        }
        return 'Perjalanan Jelajah Nusa';
    }
    public function refund()
{
    return $this->hasOne(\App\Models\Pengembalian::class, 'pemesanan_id');
}

    public function getTanggalBerangkatAttribute()
    {
        if ($this->jadwal) {
            return $this->jadwal->tanggal_berangkat;
        }
        if ($this->customTrip) {
            return $this->customTrip->jadwal_penawaran ?? $this->customTrip->tanggal_diinginkan;
        }
        return null;
    }
}
