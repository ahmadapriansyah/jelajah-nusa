<?php

use App\Services\BookingService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('booking:cancel-expired', function (BookingService $bookingService) {
    $count = $bookingService->cancelExpiredBookings();
    $this->info("Berhasil membatalkan {$count} pesanan yang kedaluwarsa.");
})->purpose('Batalkan pemesanan berstatus menunggu yang melewati batas bayar (F12-02)');

Artisan::command('tiket:fix-column', function () {
    \Illuminate\Support\Facades\DB::statement('ALTER TABLE `tiket` MODIFY `qr_code` MEDIUMTEXT NULL;');
    $this->info('Kolom qr_code pada tabel tiket berhasil diperbarui menjadi MEDIUMTEXT!');
})->purpose('Ubah kolom qr_code menjadi MEDIUMTEXT (MySQL)');

Schedule::command('booking:cancel-expired')->everyFifteenMinutes();
