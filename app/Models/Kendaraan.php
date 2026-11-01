<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kendaraan extends Model
{
    use HasFactory;

    protected $table = 'kendaraan';

    protected $fillable = [
        'jenis_kendaraan',
        'kapasitas',
        'plat_nomor',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'kapasitas' => 'integer',
        ];
    }

    public function jadwal(): HasMany
    {
        return $this->hasMany(JadwalKeberangkatan::class, 'kendaraan_id');
    }

    public function customTrips(): HasMany
    {
        return $this->hasMany(CustomTrip::class, 'kendaraan_id');
    }

    public function scopeTersedia($query)
    {
        return $query->where('status', 'tersedia');
    }
}
