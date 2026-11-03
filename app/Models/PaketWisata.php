<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class PaketWisata extends Model
{
    use HasFactory;

    protected $table = 'paket_wisata';

    protected $fillable = [
        'nama_paket',
        'destinasi',
        'jenis_layanan',
        'harga',
        'durasi_hari',
        'deskripsi',
        'itinerary',
        'fasilitas',
        'fasilitas_tidak_termasuk',
        'meeting_point',
        'jam_kumpul',
        'foto',
        'status_aktif',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
            'durasi_hari' => 'integer',
            'itinerary' => 'array',
            'status_aktif' => 'boolean',
        ];
    }

    public function jadwal(): HasMany
    {
        return $this->hasMany(JadwalKeberangkatan::class, 'paket_wisata_id');
    }

    public function pemesanan(): HasManyThrough
    {
        return $this->hasManyThrough(Pemesanan::class, JadwalKeberangkatan::class, 'paket_wisata_id', 'jadwal_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('status_aktif', true);
    }

    public function getHargaFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    public function getDurasiLabelAttribute(): string
    {
        $malam = max(0, $this->durasi_hari - 1);
        return "{$this->durasi_hari}H{$malam}M";
    }
    public function getFotoUrlAttribute()
{
    if ($this->foto && file_exists(public_path('storage/paket/' . $this->foto))) {
        return asset('storage/paket/' . $this->foto);
    }
    
    if ($this->foto && file_exists(public_path('storage/' . $this->foto))) {
        return asset('storage/' . $this->foto);
    }

    
    return 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=600&q=80';
}

    public function getJadwalMendatangAttribute()
    {
        return $this->jadwal()
            ->where('tanggal_berangkat', '>=', now()->toDateString())
            ->whereIn('status_keberangkatan', ['dijadwalkan', 'dikonfirmasi'])
            ->orderBy('tanggal_berangkat', 'asc')
            ->first();
    }
}
