<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tiket extends Model
{
    use HasFactory;

    protected $table = 'tiket';

    protected $fillable = [
        'pemesanan_id',
        'peserta_id',
        'kode_tiket',
        'qr_code',
        'tanggal_terbit',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_terbit' => 'datetime',
        ];
    }

    public function pemesanan(): BelongsTo
    {
        return $this->belongsTo(Pemesanan::class, 'pemesanan_id');
    }

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }

    /**
     * Ambil atau buat ulang QR Code SVG utuh jika di database sempat kosong atau terpotong.
     */
    public function getQrCodeSvgAttribute(): string
    {
        if (!empty($this->qr_code) && str_contains($this->qr_code, '</svg>')) {
            return $this->qr_code;
        }

        try {
            $verifyUrl = \Illuminate\Support\Facades\Route::has('tiket.verifikasi.kode')
                ? route('tiket.verifikasi.kode', ['kode' => $this->kode_tiket])
                : url('/verifikasi/' . $this->kode_tiket);

            $svg = (string) \SimpleSoftwareIO\QrCode\Facades\QrCode::size(140)->generate($verifyUrl);

            // Simpan otomatis ke database jika kolom sudah diperbesar
            try {
                $this->updateQuietly(['qr_code' => $svg]);
            } catch (\Throwable $t) {
                // Abaikan jika belum migrate
            }

            return $svg;
        } catch (\Throwable $e) {
            return '';
        }
    }
}
