<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengembalian extends Model
{
    use HasFactory;

    protected $table = 'pengembalian';

    protected $fillable = [
        'pemesanan_id',
        'jadwal_baru_id',
        'jenis',
        'nominal',
        'alasan',
        'metode_pembayaran',
        'nomor_rekening',
        'nama_pemilik',
        'status',
        'tanggal_pengembalian',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'tanggal_pengembalian' => 'date',
        ];
    }

    public function pemesanan(): BelongsTo
    {
        return $this->belongsTo(Pemesanan::class, 'pemesanan_id');
    }

    public function jadwalBaru(): BelongsTo
    {
        return $this->belongsTo(JadwalKeberangkatan::class, 'jadwal_baru_id');
    }

    public function getNominalFormatAttribute(): string
    {
        return $this->nominal ? 'Rp ' . number_format($this->nominal, 0, ',', '.') : '—';
    }
}