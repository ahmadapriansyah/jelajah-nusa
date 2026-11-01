<?php

namespace Tests\Feature;

use App\Models\CustomTrip;
use App\Models\JadwalKeberangkatan;
use App\Models\Kendaraan;
use App\Models\PaketWisata;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\Pengaturan;
use App\Models\Peserta;
use App\Models\Tiket;
use App\Models\User;
use App\Services\BookingService;
use App\Services\FeasibilityService;
use App\Services\PaymentService;
use App\Services\QuotaService;
use App\Services\RefundService;
use App\Services\ReportService;
use App\Services\TicketService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class JelajahNusaTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $wisatawanA;
    protected User $wisatawanB;
    protected PaketWisata $paket;
    protected Kendaraan $kendaraan;
    protected JadwalKeberangkatan $jadwal;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Storage::fake('public');

        Pengaturan::create([
            'nama_bank' => 'Bank Mandiri',
            'no_rekening' => '138-00-1234567-8',
            'atas_nama' => 'PT Jelajah Nusa Tour & Travel',
            'batas_bayar_jam' => 24,
        ]);

        $this->admin = User::create([
            'nama_lengkap' => 'Admin Test',
            'email' => 'admin@test.com',
            'no_hp' => '081234567890',
            'username' => 'admintest',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status_aktif' => true,
        ]);

        $this->wisatawanA = User::create([
            'nama_lengkap' => 'Rina Wijaya',
            'email' => 'rina@test.com',
            'no_hp' => '081298765432',
            'username' => 'rinaw',
            'password' => Hash::make('password123'),
            'role' => 'wisatawan',
            'status_aktif' => true,
        ]);

        $this->wisatawanB = User::create([
            'nama_lengkap' => 'Budi Santoso',
            'email' => 'budi@test.com',
            'no_hp' => '081311223344',
            'username' => 'budis',
            'password' => Hash::make('password123'),
            'role' => 'wisatawan',
            'status_aktif' => true,
        ]);

        $this->kendaraan = Kendaraan::create([
            'jenis_kendaraan' => 'Toyota Hiace Premio',
            'kapasitas' => 14,
            'plat_nomor' => 'AD 1234 XX',
            'status' => 'tersedia',
        ]);

        $this->paket = PaketWisata::create([
            'nama_paket' => 'Bromo Midnight Sunrise 2H1M',
            'destinasi' => 'Bromo & Malang',
            'jenis_layanan' => 'open_trip',
            'harga' => 1250000,
            'durasi_hari' => 2,
            'deskripsi' => 'Sunrise terbaik Bromo',
            'itinerary' => [
                ['hari' => 1, 'judul' => 'Keberangkatan', 'aktivitas' => 'Kumpul di meeting point tirtonadi.'],
                ['hari' => 2, 'judul' => 'Sunrise & Kawah', 'aktivitas' => 'Jeep ke Penanjakan 1.'],
            ],
            'fasilitas' => 'Transportasi, Tiket Masuk, Jeep',
            'meeting_point' => 'Terminal Tirtonadi Solo',
            'jam_kumpul' => '20:30:00',
            'status_aktif' => true,
        ]);

        $this->jadwal = JadwalKeberangkatan::create([
            'paket_wisata_id' => $this->paket->id,
            'kendaraan_id' => $this->kendaraan->id,
            'tanggal_berangkat' => Carbon::now()->addDays(5)->toDateString(),
            'kuota' => 10,
            'sisa_kuota' => 10,
            'min_peserta' => 6,
            'biaya_operasional' => 6000000,
            'status_keberangkatan' => 'dijadwalkan',
        ]);
    }

    /**
     * T-01: Login dengan kredensial benar dan salah.
     */
    public function test_T01_login_with_valid_and_invalid_credentials(): void
    {
        // 1. Login salah
        $responseWrong = $this->post('/login', [
            'username' => 'rinaw',
            'password' => 'wrongpassword',
        ]);
        $responseWrong->assertSessionHasErrors('username');
        $this->assertGuest();

        // 2. Login benar wisatawan -> redirect ke pesanan-saya
        $responseOk = $this->post('/login', [
            'username' => 'rinaw',
            'password' => 'password123',
        ]);
        $responseOk->assertRedirect(route('pesanan-saya.index'));
        $this->assertAuthenticatedAs($this->wisatawanA);

        // 3. Logout
        $this->post('/logout');
        $this->assertGuest();

        // 4. Login benar admin -> redirect ke admin.dashboard
        $responseAdmin = $this->post('/login', [
            'username' => 'admintest',
            'password' => 'password123',
        ]);
        $responseAdmin->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->admin);
    }

    /**
     * T-02: Wisatawan membuka halaman /admin ditolak (403).
     */
    public function test_T02_wisatawan_cannot_access_admin_dashboard_returns_403(): void
    {
        $this->actingAs($this->wisatawanA);
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    /**
     * T-03: Registrasi dengan email atau username sudah terdaftar ditolak.
     */
    public function test_T03_registration_with_duplicate_email_or_username_is_rejected(): void
    {
        // Duplikat email
        $resEmail = $this->post('/register', [
            'nama_lengkap' => 'Nama Baru',
            'email' => 'rina@test.com', // Sudah terdaftar
            'no_hp' => '081299998888',
            'username' => 'usernamebaru',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);
        $resEmail->assertSessionHasErrors('email');

        // Duplikat username
        $resUsername = $this->post('/register', [
            'nama_lengkap' => 'Nama Baru',
            'email' => 'emailbaru@test.com',
            'no_hp' => '081299998888',
            'username' => 'rinaw', // Sudah terdaftar
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);
        $resUsername->assertSessionHasErrors('username');
    }

    /**
     * T-04: Pesan 5 kursi saat sisa kuota 3 ditolak dengan pesan: "Kuota penuh — sisa N kursi, kurangi jumlah peserta".
     */
    public function test_T04_booking_more_seats_than_remaining_quota_is_rejected_with_exact_message(): void
    {
        // Atur sisa kuota 3
        $this->jadwal->update(['kuota' => 3, 'sisa_kuota' => 3]);

        $bookingService = app(BookingService::class);

        $pesertaData = [];
        for ($i = 1; $i <= 5; $i++) {
            $pesertaData[] = [
                'nama_lengkap' => "Peserta {$i}",
                'jenis_identitas' => 'ktp',
                'no_identitas' => "337201000000000{$i}",
                'file_identitas_path' => "private/identitas/test_{$i}.jpg",
            ];
        }

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Kuota penuh — sisa 3 kursi, kurangi jumlah peserta.');

        $bookingService->createBooking($this->wisatawanA, $this->jadwal->id, $pesertaData);
    }

    /**
     * T-05: Sisa kuota tidak pernah negatif.
     */
    public function test_T05_race_condition_and_quota_not_negative(): void
    {
        $this->jadwal->update(['kuota' => 2, 'sisa_kuota' => 2]);
        $quotaService = app(QuotaService::class);

        $this->assertTrue($quotaService->checkAvailability($this->jadwal, 2));
        $this->assertFalse($quotaService->checkAvailability($this->jadwal, 3));
        $this->assertGreaterThanOrEqual(0, $this->jadwal->sisa_kuota);
    }

    /**
     * T-06: Konfirmasi lalu batalkan pemesanan -> sisa kuota berkurang lalu kembali.
     */
    public function test_T06_confirm_and_cancel_booking_restores_quota(): void
    {
        $bookingService = app(BookingService::class);

        $initialSisa = $this->jadwal->sisa_kuota; // 10

        $peserta = [
            [
                'nama_lengkap' => 'Rina Wijaya',
                'jenis_identitas' => 'ktp',
                'no_identitas' => '3372011203900001',
                'file_identitas_path' => 'private/identitas/rina.jpg',
            ],
            [
                'nama_lengkap' => 'Budi Teman Rina',
                'jenis_identitas' => 'ktp',
                'no_identitas' => '3372011203900002',
                'file_identitas_path' => 'private/identitas/budi.jpg',
            ]
        ];

        // Buat pemesanan untuk 2 orang
        $pemesanan = $bookingService->createBooking($this->wisatawanA, $this->jadwal->id, $peserta);
        $this->jadwal->refresh();
        $this->assertEquals($initialSisa - 2, $this->jadwal->sisa_kuota); // sisa 8

        // Batalkan pemesanan
        $bookingService->cancelBooking($pemesanan, 'Dibatalkan untuk uji coba');
        $this->jadwal->refresh();
        $this->assertEquals($initialSisa, $this->jadwal->sisa_kuota); // kembali 10
        $this->assertEquals('dibatalkan', $pemesanan->fresh()->status);
    }

    /**
     * T-07: Unggah berkas lebih dari 5 MB ditolak.
     */
    public function test_T07_upload_identity_file_too_large_is_rejected(): void
    {
        $this->actingAs($this->wisatawanA);

        // Simulasi file lebih dari 5120 KB
        $oversizedFile = UploadedFile::fake()->create('ktp_raksasa.jpg', 6000);

        $response = $this->post('/pemesanan', [
            'jadwal_id' => $this->jadwal->id,
            'peserta' => [
                [
                    'nama_lengkap' => 'Test User',
                    'jenis_identitas' => 'ktp',
                    'no_identitas' => '3372011203900001',
                    'file_identitas' => $oversizedFile,
                ]
            ]
        ]);

        $response->assertSessionHasErrors('peserta.0.file_identitas');
    }

    /**
     * T-08: Wisatawan A membuka dokumen peserta milik wisatawan B menghasilkan 403.
     */
    public function test_T08_wisatawan_a_cannot_access_wisatawan_b_identity_document_returns_403(): void
    {
        // Buat pesanan milik Wisatawan B
        $pemesananB = Pemesanan::create([
            'kode_pemesanan' => 'PSN-TESTB',
            'user_id' => $this->wisatawanB->id,
            'jadwal_id' => $this->jadwal->id,
            'jumlah_peserta' => 1,
            'total_biaya' => 1250000,
            'status' => 'menunggu',
        ]);

        $pesertaB = Peserta::create([
            'pemesanan_id' => $pemesananB->id,
            'nama_lengkap' => 'Budi Santoso',
            'jenis_identitas' => 'ktp',
            'no_identitas' => '3372019999990001',
            'file_identitas' => 'private/identitas/sample_budi.jpg',
        ]);

        // Wisatawan A mencoba membuka KTP Peserta B
        $this->actingAs($this->wisatawanA);
        $response = $this->get("/dokumen/identitas/{$pesertaB->id}");
        $response->assertStatus(403);

        // Admin membuka KTP Peserta B -> diizinkan (200)
        $this->actingAs($this->admin);
        $resAdmin = $this->get("/dokumen/identitas/{$pesertaB->id}");
        $resAdmin->assertStatus(200);
    }

    /**
     * T-09: Tolak pembayaran tanpa alasan ditolak (alasan wajib).
     */
    public function test_T09_reject_payment_without_reason_is_rejected(): void
    {
        $pemesanan = Pemesanan::create([
            'kode_pemesanan' => 'PSN-TEST9',
            'user_id' => $this->wisatawanA->id,
            'jadwal_id' => $this->jadwal->id,
            'jumlah_peserta' => 1,
            'total_biaya' => 1250000,
            'status' => 'menunggu',
        ]);

        $pembayaran = Pembayaran::create([
            'pemesanan_id' => $pemesanan->id,
            'nominal' => 1250000,
            'tanggal_bayar' => now()->toDateString(),
            'bukti_transfer' => 'private/bukti_transfer/test.jpg',
            'status' => 'menunggu',
        ]);

        $this->actingAs($this->admin);
        $response = $this->patch("/admin/pembayaran/{$pembayaran->id}/tolak", [
            'catatan_admin' => '', // Kosong
        ]);

        $response->assertSessionHasErrors('catatan_admin');
        $this->assertEquals('menunggu', $pembayaran->fresh()->status);
    }

    /**
     * T-10: Verifikasi pembayaran lunas -> Pemesanan Dikonfirmasi, tiket terbit dengan kode unik dan QR.
     */
    public function test_T10_payment_verified_confirms_booking_and_issues_tickets(): void
    {
        $pemesanan = Pemesanan::create([
            'kode_pemesanan' => 'PSN-1099',
            'user_id' => $this->wisatawanA->id,
            'jadwal_id' => $this->jadwal->id,
            'jumlah_peserta' => 1,
            'total_biaya' => 1250000,
            'status' => 'menunggu',
        ]);

        $peserta = Peserta::create([
            'pemesanan_id' => $pemesanan->id,
            'nama_lengkap' => 'Rina Wijaya',
            'jenis_identitas' => 'ktp',
            'no_identitas' => '3372011203900001',
            'file_identitas' => 'private/identitas/test.jpg',
        ]);

        $pembayaran = Pembayaran::create([
            'pemesanan_id' => $pemesanan->id,
            'nominal' => 1250000,
            'tanggal_bayar' => now()->toDateString(),
            'bukti_transfer' => 'private/bukti_transfer/test.jpg',
            'status' => 'menunggu',
        ]);

        $paymentService = app(PaymentService::class);
        $paymentService->verifyPayment($pembayaran, $this->admin);

        // Status pembayaran diverifikasi
        $this->assertEquals('diverifikasi', $pembayaran->fresh()->status);
        $this->assertNotNull($pembayaran->fresh()->diverifikasi_pada);

        // Status pemesanan menjadi dikonfirmasi
        $this->assertEquals('dikonfirmasi', $pemesanan->fresh()->status);

        // Tiket per peserta terbit
        $tiket = Tiket::where('pemesanan_id', $pemesanan->id)->first();
        $this->assertNotNull($tiket);
        $this->assertEquals('aktif', $tiket->status);
        $this->assertStringContainsString('JN-PSN', $tiket->kode_tiket);
        $this->assertNotNull($tiket->qr_code);
    }

    /**
     * T-11: Jadwal 2 dari 6 peserta minimal -> Status 'Belum memenuhi minimal'.
     */
    public function test_T11_feasibility_status_belum_memenuhi_minimal_when_below_min_participants(): void
    {
        // Minimal peserta adalah 6. Kita buat 2 peserta aktif.
        Pemesanan::create([
            'kode_pemesanan' => 'PSN-FEAS',
            'user_id' => $this->wisatawanA->id,
            'jadwal_id' => $this->jadwal->id,
            'jumlah_peserta' => 2,
            'total_biaya' => 2500000,
            'status' => 'menunggu',
        ]);

        $feasibilityService = app(FeasibilityService::class);
        $eval = $feasibilityService->evaluate($this->jadwal);

        $this->assertEquals(2, $eval['peserta_aktif']);
        $this->assertEquals(6, $eval['min_peserta']);
        $this->assertEquals('Belum memenuhi minimal', $eval['status']);
    }

    /**
     * T-12: Keputusan keberangkatan ditunda / dibatalkan oleh admin.
     */
    public function test_T12_admin_postpones_or_cancels_schedule(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post("/admin/kelayakan/{$this->jadwal->id}/keputusan", [
            'status_keberangkatan' => 'ditunda',
            'biaya_operasional' => 5500000,
        ]);

        $response->assertSessionHas('sukses');
        $this->assertEquals('ditunda', $this->jadwal->fresh()->status_keberangkatan);
        $this->assertEquals(5500000, (float)$this->jadwal->fresh()->biaya_operasional);
    }

    /**
     * T-13: Verifikasi kode tiket valid dan tidak valid.
     */
    public function test_T13_ticket_verification_valid_and_invalid(): void
    {
        $ticketService = app(TicketService::class);

        // Tiket tidak valid (kode sembarang)
        $invalidResult = $ticketService->verifyTicket('JN-TIDAKADA-99');
        $this->assertFalse($invalidResult['valid']);
        $this->assertEquals('TIKET TIDAK VALID', $invalidResult['status_teks']);

        // Buat tiket aktif sah
        $pemesanan = Pemesanan::create([
            'kode_pemesanan' => 'PSN-1088',
            'user_id' => $this->wisatawanA->id,
            'jadwal_id' => $this->jadwal->id,
            'jumlah_peserta' => 1,
            'total_biaya' => 1250000,
            'status' => 'dikonfirmasi',
        ]);
        $peserta = Peserta::create([
            'pemesanan_id' => $pemesanan->id,
            'nama_lengkap' => 'Rina Wijaya',
            'jenis_identitas' => 'ktp',
            'no_identitas' => '3372011203900001',
            'file_identitas' => 'private/identitas/test.jpg',
        ]);
        $issued = $ticketService->issueTickets($pemesanan);
        $validTiket = $issued[0];

        $validResult = $ticketService->verifyTicket($validTiket->kode_tiket);
        $this->assertTrue($validResult['valid']);
        $this->assertEquals('TIKET VALID', $validResult['status_teks']);
        $this->assertEquals('Rina Wijaya', $validResult['nama_peserta']);
    }

    /**
     * T-14: Hapus paket yang sudah punya pemesanan ditolak dengan saran menonaktifkan.
     */
    public function test_T14_deleting_package_with_existing_bookings_is_rejected_with_warning(): void
    {
        Pemesanan::create([
            'kode_pemesanan' => 'PSN-PKT1',
            'user_id' => $this->wisatawanA->id,
            'jadwal_id' => $this->jadwal->id,
            'jumlah_peserta' => 1,
            'total_biaya' => 1250000,
            'status' => 'dikonfirmasi',
        ]);

        $this->actingAs($this->admin);
        $response = $this->delete("/admin/paket/{$this->paket->id}");

        $response->assertSessionHas('error', 'Data ini sudah dipakai pemesanan dan tidak bisa dihapus. Nonaktifkan saja.');
        $this->assertDatabaseHas('paket_wisata', ['id' => $this->paket->id]);
    }

    /**
     * T-15: Ekspor laporan ke PDF dan Excel/CSV.
     */
    public function test_T15_export_reports_to_pdf_and_csv(): void
    {
        $this->actingAs($this->admin);

        // Ekspor PDF
        $resPdf = $this->get('/admin/laporan/ekspor-pdf');
        $resPdf->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $resPdf->headers->get('Content-Type'));

        // Ekspor CSV / Excel
        $resCsv = $this->get('/admin/laporan/ekspor-excel');
        $resCsv->assertStatus(200);
        $this->assertStringContainsString('text/csv', $resCsv->headers->get('Content-Type'));
    }

    /**
     * T-16: Pengajuan custom trip sampai disetujui (alur berurutan).
     */
    public function test_T16_custom_trip_workflow_from_submission_to_approval(): void
    {
        // 1. Wisatawan mengajukan custom trip (Diajukan)
        $this->actingAs($this->wisatawanA);
        $response = $this->post('/custom-trip', [
            'tujuan' => 'Raja Ampat Private Sailing',
            'tanggal_diinginkan' => Carbon::now()->addDays(20)->toDateString(),
            'durasi_hari' => 4,
            'jumlah_peserta' => 6,
            'kebutuhan' => 'Private speedboat, liveaboard, penginapan resort.',
        ]);
        $customTrip = CustomTrip::latest()->first();
        $this->assertEquals('diajukan', $customTrip->status);

        // 2. Admin memberikan penawaran (Ditawarkan)
        $this->actingAs($this->admin);
        $this->post("/admin/custom-trip/{$customTrip->id}/penawaran", [
            'kendaraan_id' => $this->kendaraan->id,
            'jadwal_penawaran' => Carbon::now()->addDays(20)->toDateString(),
            'fasilitas_penawaran' => 'Kapal privat, makan 9x, tour guide',
            'biaya_penawaran' => 24000000,
        ]);
        $this->assertEquals('ditawarkan', $customTrip->fresh()->status);

        // 3. Wisatawan menyetujui (Disetujui)
        $this->actingAs($this->wisatawanA);
        $this->post("/custom-trip/{$customTrip->id}/setujui");
        $this->assertEquals('disetujui', $customTrip->fresh()->status);
    }

    /**
     * T-17: Reset password dengan tautan kedaluwarsa ditolak.
     */
    public function test_T17_password_reset_with_expired_token_is_rejected(): void
    {
        $token = 'expiredtoken123';
        DB::table('password_reset_tokens')->insert([
            'email' => $this->wisatawanA->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now()->subMinutes(90), // Lewat dari 60 menit
        ]);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $this->wisatawanA->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * T-18: Menurunkan kuota jadwal di bawah jumlah peserta aktif ditolak.
     */
    public function test_T18_decreasing_schedule_quota_below_active_participants_is_rejected(): void
    {
        // 4 peserta aktif
        Pemesanan::create([
            'kode_pemesanan' => 'PSN-T18',
            'user_id' => $this->wisatawanA->id,
            'jadwal_id' => $this->jadwal->id,
            'jumlah_peserta' => 4,
            'total_biaya' => 5000000,
            'status' => 'menunggu',
        ]);

        $this->actingAs($this->admin);

        // Mencoba menurunkan kuota menjadi 3 (di bawah 4 peserta aktif)
        $response = $this->put("/admin/jadwal/{$this->jadwal->id}", [
            'tanggal_berangkat' => $this->jadwal->tanggal_berangkat->toDateString(),
            'kuota' => 3,
            'min_peserta' => 2,
        ]);

        $response->assertSessionHasErrors('kuota');
    }

    /**
     * T-19: Refund melebihi total terbayar ditolak.
     */
    public function test_T19_refund_amount_exceeding_total_paid_is_rejected(): void
    {
        $pemesanan = Pemesanan::create([
            'kode_pemesanan' => 'PSN-T19',
            'user_id' => $this->wisatawanA->id,
            'jadwal_id' => $this->jadwal->id,
            'jumlah_peserta' => 1,
            'total_biaya' => 1250000,
            'status' => 'dikonfirmasi',
        ]);

        Pembayaran::create([
            'pemesanan_id' => $pemesanan->id,
            'nominal' => 1000000, // Total terbayar 1 jt
            'tanggal_bayar' => now()->toDateString(),
            'bukti_transfer' => 'sample.jpg',
            'status' => 'diverifikasi',
        ]);

        $refundService = app(RefundService::class);

        $this->expectException(ValidationException::class);
        // Coba refund 1.5 jt
        $refundService->processRefund($pemesanan, 1500000, 'Kelebihan refund');
    }

    /**
     * T-20: Persentase perubahan saat bulan lalu bernilai 0 tampil null / '—' tanpa divide-by-zero error.
     */
    public function test_T20_percentage_calculation_handles_zero_previous_month(): void
    {
        $reportService = app(ReportService::class);
        $dashboardData = $reportService->getDashboardData();

        // dashboardData['persen_pemesanan'] is either float or null
        $this->assertTrue(is_null($dashboardData['persen_pemesanan']) || is_numeric($dashboardData['persen_pemesanan']));
    }

    /**
     * T-21: Pesanan Menunggu lewat batas bayar dibatalkan otomatis oleh scheduler.
     */
    public function test_T21_expired_waiting_bookings_canceled_automatically(): void
    {
        // Buat pesanan dengan batas bayar di masa lalu
        $expiredBooking = Pemesanan::create([
            'kode_pemesanan' => 'PSN-EXPIRED',
            'user_id' => $this->wisatawanA->id,
            'jadwal_id' => $this->jadwal->id,
            'jumlah_peserta' => 2,
            'total_biaya' => 2500000,
            'status' => 'menunggu',
            'batas_bayar' => Carbon::now()->subHours(2), // 2 jam yang lalu
        ]);

        $bookingService = app(BookingService::class);
        $count = $bookingService->cancelExpiredBookings();

        $this->assertGreaterThanOrEqual(1, $count);
        $this->assertEquals('dibatalkan', $expiredBooking->fresh()->status);
    }

    /**
     * T-22: Wisatawan membatalkan pesanan Menunggu -> Kursi kembali ke sisa_kuota.
     */
    public function test_T22_wisatawan_cancels_waiting_booking_restores_seats(): void
    {
        $initialSisa = $this->jadwal->sisa_kuota;

        $pemesanan = Pemesanan::create([
            'kode_pemesanan' => 'PSN-BATAL22',
            'user_id' => $this->wisatawanA->id,
            'jadwal_id' => $this->jadwal->id,
            'jumlah_peserta' => 2,
            'total_biaya' => 2500000,
            'status' => 'menunggu',
        ]);
        app(QuotaService::class)->syncRemainingSeats($this->jadwal);
        $this->assertEquals($initialSisa - 2, $this->jadwal->fresh()->sisa_kuota);

        $this->actingAs($this->wisatawanA);
        $response = $this->post("/pesanan-saya/{$pemesanan->id}/batal");
        $response->assertSessionHas('sukses');

        $this->assertEquals('dibatalkan', $pemesanan->fresh()->status);
        $this->assertEquals($initialSisa, $this->jadwal->fresh()->sisa_kuota);
    }

    /**
     * T-23: Pencarian global dengan kode pesanan dan nama peserta.
     */
    public function test_T23_global_search_returns_categorized_results(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin/cari?q=Bromo');
        $response->assertStatus(200);
        $response->assertSee('Hasil Pencarian Global');
        $response->assertSee('Bromo Midnight Sunrise 2H1M');
    }
}
