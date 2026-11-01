<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogAktivitas extends Model
{
    use HasFactory;

    protected $table = 'log_aktivitas';

    protected $fillable = [
        'user_id',
        'subjek_tipe',
        'subjek_id',
        'aksi',
        'keterangan',
        'ip_address',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function catat(string $aksi, ?string $subjekTipe = null, ?int $subjekId = null, ?string $keterangan = null, ?int $userId = null): self
    {
        return static::create([
            'user_id' => $userId ?? auth()->id(),
            'subjek_tipe' => $subjekTipe,
            'subjek_id' => $subjekId,
            'aksi' => $aksi,
            'keterangan' => $keterangan,
            'ip_address' => request()->ip(),
        ]);
    }
}
