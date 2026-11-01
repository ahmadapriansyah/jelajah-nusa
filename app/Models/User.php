<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nama_lengkap',
        'email',
        'no_hp',
        'username',
        'password',
        'role',
        'status_aktif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status_aktif' => 'boolean',
        ];
    }

    public function getNameAttribute(): string
    {
        return $this->nama_lengkap ?? '';
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['nama_lengkap'] = $value;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isWisatawan(): bool
    {
        return $this->role === 'wisatawan';
    }

    public function pemesanan(): HasMany
    {
        return $this->hasMany(Pemesanan::class, 'user_id');
    }

    public function customTrips(): HasMany
    {
        return $this->hasMany(CustomTrip::class, 'user_id');
    }
}
