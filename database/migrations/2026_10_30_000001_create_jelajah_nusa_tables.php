<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Paket Wisata
        Schema::create('paket_wisata', function (Blueprint $table) {
            $table->id();
            $table->string('nama_paket', 150);
            $table->string('destinasi', 100)->index();
            $table->enum('jenis_layanan', ['open_trip', 'paket_reguler'])->index();
            $table->decimal('harga', 12, 2);
            $table->unsignedTinyInteger('durasi_hari');
            $table->text('deskripsi')->nullable();
            $table->json('itinerary'); // Array of {hari: 1, judul: "...", aktivitas: "..."}
            $table->text('fasilitas');
            $table->text('fasilitas_tidak_termasuk')->nullable();
            $table->string('meeting_point', 150);
            $table->time('jam_kumpul');
            $table->string('foto', 255)->nullable();
            $table->boolean('status_aktif')->default(true)->index();
            $table->timestamps();
        });

        // 2. Kendaraan
        Schema::create('kendaraan', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_kendaraan', 50);
            $table->unsignedTinyInteger('kapasitas');
            $table->string('plat_nomor', 15)->nullable();
            $table->enum('status', ['tersedia', 'tidak_tersedia'])->default('tersedia')->index();
            $table->timestamps();
        });

        // 3. Jadwal Keberangkatan
        Schema::create('jadwal_keberangkatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paket_wisata_id')->constrained('paket_wisata')->restrictOnDelete();
            $table->foreignId('kendaraan_id')->nullable()->constrained('kendaraan')->nullOnDelete();
            $table->date('tanggal_berangkat');
            $table->unsignedInteger('kuota');
            $table->unsignedInteger('sisa_kuota');
            $table->unsignedInteger('min_peserta');
            $table->decimal('biaya_operasional', 12, 2)->default(0);
            $table->enum('status_keberangkatan', ['dijadwalkan', 'dikonfirmasi', 'ditunda', 'dibatalkan'])->default('dijadwalkan')->index();
            $table->timestamps();

            $table->index(['paket_wisata_id', 'tanggal_berangkat']);
        });

        // 4. Custom Trip
        Schema::create('custom_trip', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('kendaraan_id')->nullable()->constrained('kendaraan')->nullOnDelete();
            $table->string('tujuan', 150);
            $table->date('tanggal_diinginkan');
            $table->unsignedTinyInteger('durasi_hari');
            $table->unsignedInteger('jumlah_peserta');
            $table->text('kebutuhan');
            $table->text('fasilitas_penawaran')->nullable();
            $table->date('jadwal_penawaran')->nullable();
            $table->decimal('biaya_penawaran', 12, 2)->nullable();
            $table->enum('status', ['diajukan', 'ditawarkan', 'disetujui', 'ditolak'])->default('diajukan')->index();
            $table->timestamps();
        });

        // 5. Pemesanan
        Schema::create('pemesanan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pemesanan', 20)->unique();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('jadwal_id')->nullable()->constrained('jadwal_keberangkatan')->restrictOnDelete();
            $table->foreignId('custom_trip_id')->nullable()->unique()->constrained('custom_trip')->restrictOnDelete();
            $table->unsignedInteger('jumlah_peserta');
            $table->decimal('total_biaya', 12, 2);
            $table->dateTime('batas_bayar')->nullable();
            $table->enum('status', ['menunggu', 'dikonfirmasi', 'dibatalkan'])->default('menunggu')->index();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        // 6. Peserta
        Schema::create('peserta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemesanan_id')->constrained('pemesanan')->cascadeOnDelete();
            $table->string('nama_lengkap', 100);
            $table->enum('jenis_identitas', ['ktp', 'paspor']);
            $table->string('no_identitas', 30)->index();
            $table->string('file_identitas', 255);
            $table->string('no_hp', 20)->nullable();
            $table->timestamps();
        });

        // 7. Pembayaran
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemesanan_id')->constrained('pemesanan')->restrictOnDelete();
            $table->decimal('nominal', 12, 2);
            $table->date('tanggal_bayar');
            $table->string('bukti_transfer', 255);
            $table->enum('status', ['menunggu', 'diverifikasi', 'ditolak'])->default('menunggu')->index();
            $table->text('catatan_admin')->nullable();
            $table->timestamp('diverifikasi_pada')->nullable();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 8. Tiket (per peserta, sesuai BR-07 & Proposal 4)
        Schema::create('tiket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemesanan_id')->constrained('pemesanan')->cascadeOnDelete();
            $table->foreignId('peserta_id')->constrained('peserta')->cascadeOnDelete();
            $table->string('kode_tiket', 30)->unique();
            $table->mediumText('qr_code')->nullable();
            $table->dateTime('tanggal_terbit');
            $table->enum('status', ['aktif', 'dibatalkan'])->default('aktif')->index();
            $table->timestamps();
        });

        // 9. Pengembalian (Refund & Pindah Jadwal)
        Schema::create('pengembalian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemesanan_id')->constrained('pemesanan')->restrictOnDelete();
            $table->foreignId('jadwal_baru_id')->nullable()->constrained('jadwal_keberangkatan')->restrictOnDelete();
            $table->enum('jenis', ['refund', 'pindah_jadwal'])->index();
            $table->decimal('nominal', 12, 2)->nullable();
            $table->text('alasan');
            $table->date('tanggal_pengembalian');
            $table->timestamps();
        });

        // 10. Pengaturan (Rekening Tujuan & Batas Bayar)
        Schema::create('pengaturan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bank', 50);
            $table->string('no_rekening', 50);
            $table->string('atas_nama', 100);
            $table->unsignedInteger('batas_bayar_jam')->default(24);
            $table->timestamps();
        });

        // 11. Log Aktivitas
        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subjek_tipe', 100)->nullable();
            $table->unsignedBigInteger('subjek_id')->nullable();
            $table->string('aksi', 100)->index();
            $table->text('keterangan')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
        Schema::dropIfExists('pengaturan');
        Schema::dropIfExists('pengembalian');
        Schema::dropIfExists('tiket');
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('peserta');
        Schema::dropIfExists('pemesanan');
        Schema::dropIfExists('custom_trip');
        Schema::dropIfExists('jadwal_keberangkatan');
        Schema::dropIfExists('kendaraan');
        Schema::dropIfExists('paket_wisata');
    }
};
