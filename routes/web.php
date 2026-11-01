<?php

use App\Http\Controllers\Admin\CustomTripController as AdminCustomTripController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\GlobalSearchController as AdminGlobalSearchController;
use App\Http\Controllers\Admin\JadwalController as AdminJadwalController;
use App\Http\Controllers\Admin\KelayakanController as AdminKelayakanController;
use App\Http\Controllers\Admin\KendaraanController as AdminKendaraanController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;
use App\Http\Controllers\Admin\PaketWisataController as AdminPaketWisataController;
use App\Http\Controllers\Admin\PembayaranController as AdminPembayaranController;
use App\Http\Controllers\Admin\PemesananController as AdminPemesananController;
use App\Http\Controllers\Admin\PengaturanController as AdminPengaturanController;
use App\Http\Controllers\Admin\PengembalianController as AdminPengembalianController;
use App\Http\Controllers\Admin\PesertaController as AdminPesertaController;
use App\Http\Controllers\Admin\TiketController as AdminTiketController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomTripController;
use App\Http\Controllers\FileAccessController;
use App\Http\Controllers\PaketController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\PesananSayaController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TiketController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Jelajah Nusa (Sistem Informasi Travel dan Agen Wisata)
|--------------------------------------------------------------------------
*/

// Beranda & Katalog Paket Wisata (Publik)
Route::get('/', function () {
    return redirect()->route('paket.index');
});
Route::get('/paket', [PaketController::class, 'index'])->name('paket.index');
Route::get('/paket/{id}', [PaketController::class, 'show'])->name('paket.show');

// Inisialisasi Database Cloud Sekali Klik (Migrate & Seed Otomatis)
Route::get('/setup-database', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', [
            '--force' => true,
        ]);
        $migrateOutput = \Illuminate\Support\Facades\Artisan::output();

        \Illuminate\Support\Facades\Artisan::call('db:seed', [
            '--force' => true,
        ]);
        $seedOutput = \Illuminate\Support\Facades\Artisan::output();

        return response("
            <div style='font-family: system-ui, sans-serif; max-width: 700px; margin: 40px auto; padding: 32px; border-radius: 12px; background: #ffffff; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); border: 1px solid #e2e8f0;'>
                <div style='display: flex; align-items: center; gap: 12px; margin-bottom: 16px;'>
                    <div style='background: #dcfce7; color: #16a34a; width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold;'>✓</div>
                    <h2 style='color: #0f172a; margin: 0; font-size: 20px;'>Database Jelajah Nusa Berhasil Diinisialisasi!</h2>
                </div>
                <p style='color: #475569; font-size: 14px; line-height: 1.6; margin-bottom: 20px;'>
                    Semua tabel database (paket wisata, jadwal, kendaraan, pemesanan, tiket) beserta data awal berhasil dibuat di database cloud Anda.
                </p>
                <div style='background: #0f172a; color: #38bdf8; padding: 16px; border-radius: 8px; font-family: monospace; font-size: 12px; max-height: 250px; overflow-y: auto; margin-bottom: 24px; white-space: pre-wrap;'>" . htmlspecialchars($migrateOutput . "\n" . $seedOutput) . "</div>
                <div style='display: flex; gap: 12px;'>
                    <a href='/' style='background: #0d9488; color: #ffffff; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600;'>Kunjungi Halaman Beranda &rarr;</a>
                    <a href='/login' style='background: #f1f5f9; color: #334155; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600;'>Masuk / Login &rarr;</a>
                </div>
            </div>
        ", 200);
    } catch (\Throwable $e) {
        return response("
            <div style='font-family: system-ui, sans-serif; max-width: 700px; margin: 40px auto; padding: 32px; border-radius: 12px; background: #ffffff; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); border: 1px solid #fecaca;'>
                <div style='display: flex; align-items: center; gap: 12px; margin-bottom: 16px;'>
                    <div style='background: #fee2e2; color: #dc2626; width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold;'>✕</div>
                    <h2 style='color: #991b1b; margin: 0; font-size: 20px;'>Gagal Menginisialisasi Database</h2>
                </div>
                <p style='color: #7f1d1d; font-size: 14px; line-height: 1.6; margin-bottom: 16px;'>
                    Pesan Kesalahan: <strong>" . htmlspecialchars($e->getMessage()) . "</strong>
                </p>
                <div style='background: #0f172a; color: #f87171; padding: 16px; border-radius: 8px; font-family: monospace; font-size: 12px; max-height: 250px; overflow-y: auto; margin-bottom: 24px; white-space: pre-wrap;'>" . htmlspecialchars($e->getTraceAsString()) . "</div>
            </div>
        ", 500);
    }
});

// Verifikasi Keaslian Tiket (Publik / QR Code)
Route::get('/verifikasi/{kode?}', [TiketController::class, 'verifikasiPublik'])->name('tiket.verifikasi.kode');

// Autentikasi Pengguna
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Akses Dokumen Privat Terlindungi (KTP/Paspor & Bukti Transfer)
Route::middleware('auth')->group(function () {
    Route::get('/dokumen/identitas/{id}', [FileAccessController::class, 'viewIdentity'])->name('dokumen.identitas');
    Route::get('/dokumen/bukti/{id}', [FileAccessController::class, 'viewPaymentProof'])->name('dokumen.bukti');
});

// Area Pengguna / Wisatawan (Membutuhkan Login)
Route::middleware(['auth', 'role:wisatawan,admin'])->group(function () {
    // Pemesanan
    Route::get('/pemesanan', [PemesananController::class, 'create'])->name('pemesanan.create');
    Route::post('/pemesanan', [PemesananController::class, 'store'])->name('pemesanan.store');

    // Pembayaran
    Route::get('/pemesanan/{id}/bayar', [PembayaranController::class, 'show'])->name('pembayaran.show');
    Route::post('/pemesanan/{id}/bayar', [PembayaranController::class, 'store'])->name('pembayaran.store');

    // Pesanan Saya
    Route::get('/pesanan-saya', [PesananSayaController::class, 'index'])->name('pesanan-saya.index');
    Route::get('/pesanan-saya/{id}', [PesananSayaController::class, 'show'])->name('pesanan-saya.show');
    Route::post('/pesanan-saya/{id}/batal', [PesananSayaController::class, 'batal'])->name('pesanan-saya.batal');
    Route::post('/pesanan-saya/{id}/ajukan-refund', [PesananSayaController::class, 'ajukanRefund'])->name('pesanan.ajukan-refund');

    // Custom Trip
    Route::get('/custom-trip', [CustomTripController::class, 'index'])->name('custom-trip.index');
    Route::get('/custom-trip/ajukan', [CustomTripController::class, 'create'])->name('custom-trip.create');
    Route::post('/custom-trip', [CustomTripController::class, 'store'])->name('custom-trip.store');
    Route::get('/custom-trip/{id}', [CustomTripController::class, 'show'])->name('custom-trip.show');
    Route::post('/custom-trip/{id}/setujui', [CustomTripController::class, 'setujui'])->name('custom-trip.setujui');
    Route::post('/custom-trip/{id}/tolak', [CustomTripController::class, 'tolak'])->name('custom-trip.tolak');

    // Tiket Wisatawan
    Route::get('/tiket/{id}/unduh', [TiketController::class, 'unduh'])->name('tiket.unduh');

    // Profil Pengguna
    Route::get('/profil', [ProfilController::class, 'show'])->name('profil.show');
    Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');
});

// Area Admin Panel Jelajah Nusa
Route::prefix('admin')->as('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    // 1. Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // 2. Kelola Paket Wisata
    Route::get('/paket', [AdminPaketWisataController::class, 'index'])->name('paket.index');
    Route::get('/paket/tambah', [AdminPaketWisataController::class, 'create'])->name('paket.create');
    Route::post('/paket', [AdminPaketWisataController::class, 'store'])->name('paket.store');
    Route::get('/paket/{id}/ubah', [AdminPaketWisataController::class, 'edit'])->name('paket.edit');
    Route::put('/paket/{id}', [AdminPaketWisataController::class, 'update'])->name('paket.update');
    Route::delete('/paket/{id}', [AdminPaketWisataController::class, 'destroy'])->name('paket.destroy');
    Route::patch('/paket/{id}/toggle-aktif', [AdminPaketWisataController::class, 'toggleAktif'])->name('paket.toggle-aktif');

    // Jadwal Keberangkatan (Nested di Paket)
    Route::post('/paket/{paket}/jadwal', [AdminJadwalController::class, 'store'])->name('jadwal.store');
    Route::put('/jadwal/{id}', [AdminJadwalController::class, 'update'])->name('jadwal.update');
    Route::delete('/jadwal/{id}', [AdminJadwalController::class, 'destroy'])->name('jadwal.destroy');

    // 3. Kelola Kendaraan
    Route::get('/kendaraan', [AdminKendaraanController::class, 'index'])->name('kendaraan.index');
    Route::post('/kendaraan', [AdminKendaraanController::class, 'store'])->name('kendaraan.store');
    Route::put('/kendaraan/{id}', [AdminKendaraanController::class, 'update'])->name('kendaraan.update');
    Route::delete('/kendaraan/{id}', [AdminKendaraanController::class, 'destroy'])->name('kendaraan.destroy');

    // 4. Pemesanan & Peserta
    Route::get('/pemesanan', [AdminPemesananController::class, 'index'])->name('pemesanan.index');
    Route::patch('/pemesanan/{id}/status', [AdminPemesananController::class, 'updateStatus'])->name('pemesanan.status');

    // 5. Data Peserta
    Route::get('/peserta', [AdminPesertaController::class, 'index'])->name('peserta.index');
    Route::put('/peserta/{id}', [AdminPesertaController::class, 'update'])->name('peserta.update');

    // 6. Custom Trip
    Route::get('/custom-trip', [AdminCustomTripController::class, 'index'])->name('custom-trip.index');
    Route::post('/custom-trip/{id}/penawaran', [AdminCustomTripController::class, 'berikanPenawaran'])->name('custom-trip.penawaran');
    Route::post('/custom-trip/{id}/tolak', [AdminCustomTripController::class, 'tolak'])->name('custom-trip.tolak');

    // 7. Verifikasi Pembayaran
    Route::get('/pembayaran', [AdminPembayaranController::class, 'index'])->name('pembayaran.index');
    Route::patch('/pembayaran/{id}/verifikasi', [AdminPembayaranController::class, 'verifikasi'])->name('pembayaran.verifikasi');
    Route::patch('/pembayaran/{id}/tolak', [AdminPembayaranController::class, 'tolak'])->name('pembayaran.tolak');

    // 8. Kelayakan Keberangkatan
    Route::get('/kelayakan', [AdminKelayakanController::class, 'index'])->name('kelayakan.index');
    Route::post('/kelayakan/{id}/keputusan', [AdminKelayakanController::class, 'updateKeputusan'])->name('kelayakan.keputusan');

    // 9. Pengembalian & Reschedule
    Route::get('/pengembalian', [AdminPengembalianController::class, 'index'])->name('pengembalian.index');
    Route::post('/pengembalian', [AdminPengembalianController::class, 'store'])->name('pengembalian.store');

    // 10. Manajemen Tiket
    Route::get('/tiket', [AdminTiketController::class, 'index'])->name('tiket.index');
    Route::get('/tiket/{id}/cetak', [AdminTiketController::class, 'cetak'])->name('tiket.cetak');

    // 11. Laporan
    Route::get('/laporan', [AdminLaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/ekspor-pdf', [AdminLaporanController::class, 'eksporPdf'])->name('laporan.ekspor-pdf');
    Route::get('/laporan/ekspor-excel', [AdminLaporanController::class, 'eksporExcel'])->name('laporan.ekspor-excel');

    // 12. Pengaturan & User Management
    Route::get('/pengaturan', [AdminPengaturanController::class, 'index'])->name('pengaturan.index');
    Route::put('/pengaturan/rekening', [AdminPengaturanController::class, 'updateSettings'])->name('pengaturan.update');
    Route::post('/pengaturan/admin', [AdminPengaturanController::class, 'storeAdmin'])->name('pengaturan.admin.store');
    Route::patch('/pengaturan/user/{id}/toggle', [AdminPengaturanController::class, 'toggleUserStatus'])->name('pengaturan.user.toggle');

    // 13. Global Search Header
    Route::get('/cari', [AdminGlobalSearchController::class, 'search'])->name('cari');
});