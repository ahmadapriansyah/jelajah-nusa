<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalKeberangkatan extends Model
{
    use HasFactory;

    protected $table = 'jadwal_keberangkatan';

    protected $fillable = [
        'paket_wisata_id',
        'kendaraan_id',
        'tanggal_berangkat',
        'kuota',
        'sisa_kuota',
        'min_peserta',
        'biaya_operasional',
        'status_keberangkatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_berangkat' => 'date',
            'kuota' => 'integer',
            'sisa_kuota' => 'integer',
            'min_peserta' => 'integer',
            'biaya_operasional' => 'decimal:2',
        ];
    }

    public function paket(): BelongsTo
    {
        return $this->belongsTo(PaketWisata::class, 'paket_wisata_id');
    }

    public function kendaraan(): BelongsTo
    {
        return $this->belongsTo(Kendaraan::class, 'kendaraan_id');
    }

    public function pemesanan(): HasMany
    {
        return $this->hasMany(Pemesanan::class, 'jadwal_id');
    }

    /**
     * Hitung jumlah peserta aktif (pesanan berstatus menunggu atau dikonfirmasi).
     */
    public function getPesertaAktifAttribute(): int
    {
        return (int) $this->pemesanan()
            ->whereIn('status', ['menunggu', 'dikonfirmasi'])
            ->sum('jumlah_peserta');
    }

    /**
     * Hitung jumlah peserta terkonfirmasi.
     */
    public function getPesertaDikonfirmasiAttribute(): int
    {
        return (int) $this->pemesanan()
            ->where('status', 'dikonfirmasi')
            ->sum('jumlah_peserta');
    }

    /**
     * Sinkronisasi dan hitung sisa kuota secara akurat (BR-03 & BR-04).
     */
    public function hitungSisaKuota(): int
    {
        $terpakai = $this->peserta_aktif;
        return max(0, $this->kuota - $terpakai);
    }

    /**
     * Estimasi pendapatan dari peserta terkonfirmasi dikali harga paket.
     */
    public function getEstimasiPendapatanAttribute(): float
    {
        $harga = $this->paket ? (float) $this->paket->harga : 0;
        return (float) ($this->peserta_dikonfirmasi * $harga);
    }

    /**
     * Status kelayakan keberangkatan (dihitung dinamis, BR-08).
     * - Penuh: peserta aktif >= kuota
     * - Belum memenuhi minimal: peserta aktif < min_peserta
     * - Layak dijalankan: peserta aktif >= min_peserta dan estimasi pendapatan >= biaya_operasional
     */
    public function getStatusKelayakanAttribute(): string
    {
        $aktif = $this->peserta_aktif;
        $kuota = $this->kuota;
        $min = $this->min_peserta;
        $biaya = (float) $this->biaya_operasional;
        $estimasi = $this->estimasi_pendapatan;

        if ($aktif >= $kuota && $kuota > 0) {
            return 'Penuh';
        }

        if ($aktif < $min) {
            return 'Belum memenuhi minimal';
        }

        if ($aktif >= $min && $estimasi >= $biaya) {
            return 'Layak dijalankan';
        }

        return 'Belum memenuhi minimal';
    }
}
