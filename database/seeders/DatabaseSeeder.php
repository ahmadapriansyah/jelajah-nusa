<?php

namespace Database\Seeders;

use App\Models\CustomTrip;
use App\Models\JadwalKeberangkatan;
use App\Models\Kendaraan;
use App\Models\LogAktivitas;
use App\Models\PaketWisata;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\Pengaturan;
use App\Models\Peserta;
use App\Models\Tiket;
use App\Models\User;
use App\Services\TicketService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Pengaturan Default
        Pengaturan::firstOrCreate([], [
            'nama_bank' => 'Bank Mandiri',
            'no_rekening' => '138-00-8899221-1',
            'atas_nama' => 'PT Jelajah Nusa Tour & Travel',
            'batas_bayar_jam' => 24,
        ]);

        // 2. Akun Admin (F1, F8-04)
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'nama_lengkap' => 'Admin Travel Jelajah Nusa',
                'email' => 'admin@jelajahnusa.com',
                'no_hp' => '081234567890',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status_aktif' => true,
            ]
        );

        // 3. Tiga Akun Wisatawan Contoh
        $userRina = User::firstOrCreate(
            ['username' => 'rinawijaya'],
            [
                'nama_lengkap' => 'Rina Wijaya',
                'email' => 'rina@example.com',
                'no_hp' => '081298765432',
                'password' => Hash::make('password'),
                'role' => 'wisatawan',
                'status_aktif' => true,
            ]
        );

        $userBudi = User::firstOrCreate(
            ['username' => 'budisantoso'],
            [
                'nama_lengkap' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'no_hp' => '081311223344',
                'password' => Hash::make('password'),
                'role' => 'wisatawan',
                'status_aktif' => true,
            ]
        );

        $userSiti = User::firstOrCreate(
            ['username' => 'sitinur'],
            [
                'nama_lengkap' => 'Siti Nurhaliza',
                'email' => 'siti@example.com',
                'no_hp' => '085612345678',
                'password' => Hash::make('password'),
                'role' => 'wisatawan',
                'status_aktif' => true,
            ]
        );

        // 4. Lima Armada Kendaraan
        $hiace = Kendaraan::firstOrCreate(
            ['plat_nomor' => 'AD 7123 XX'],
            [
                'jenis_kendaraan' => 'Toyota Hiace Premio Luxury',
                'kapasitas' => 14,
                'status' => 'tersedia',
            ]
        );

        $innova = Kendaraan::firstOrCreate(
            ['plat_nomor' => 'AD 1982 AB'],
            [
                'jenis_kendaraan' => 'Toyota Innova Reborn',
                'kapasitas' => 7,
                'status' => 'tersedia',
            ]
        );

        $elf = Kendaraan::firstOrCreate(
            ['plat_nomor' => 'AD 8821 ZZ'],
            [
                'jenis_kendaraan' => 'Isuzu Elf Long Giga',
                'kapasitas' => 19,
                'status' => 'tersedia',
            ]
        );

        $bus = Kendaraan::firstOrCreate(
            ['plat_nomor' => 'AD 9001 YY'],
            [
                'jenis_kendaraan' => 'Medium Bus Pariwisata',
                'kapasitas' => 32,
                'status' => 'tersedia',
            ]
        );

        $speedboat = Kendaraan::firstOrCreate(
            ['jenis_kendaraan' => 'Speedboat Komodo Cruiser 200PK'],
            [
                'kapasitas' => 12,
                'plat_nomor' => null,
                'status' => 'tersedia',
            ]
        );

        // 5. Empat Paket Wisata Utama
        $paketBali = PaketWisata::firstOrCreate(
            ['nama_paket' => 'Solo – Bali Eksotis 4H3M'],
            [
                'destinasi' => 'Bali & Nusa Penida',
                'jenis_layanan' => 'open_trip',
                'harga' => 3500000,
                'durasi_hari' => 4,
                'deskripsi' => 'Nikmati pesona Pulau Dewata mulai dari Pantai Pandawa, Pura Uluwatu, Nusa Penida Kelingking Beach hingga sunset di Tanah Lot dengan akomodasi hotel bintang 3.',
                'itinerary' => [
                    ['hari' => 1, 'judul' => 'Keberangkatan & Penyeberangan Ketapang-Gilimanuk', 'aktivitas' => 'Kumpul di Meeting Point Stasiun Solo Balapan, perjalanan malam via tol trans jawa menuju Pelabuhan Ketapang.'],
                    ['hari' => 2, 'judul' => 'Tiba di Bali & Explore Pesisir Selatan', 'aktivitas' => 'Sarapan pagi di Gilimanuk, menuju Pantai Pandawa, Pura Uluwatu, dan makan malam seafood di Pantai Jimbaran.'],
                    ['hari' => 3, 'judul' => 'Island Hopping Nusa Penida', 'aktivitas' => 'Naik fastboat Sanur - Nusa Penida, kunjungan ke Kelingking Cliff, Broken Beach, Angel Billabong, dan Crystal Bay.'],
                    ['hari' => 4, 'judul' => 'Belanja Oleh-oleh Krisna & Perjalanan Pulang', 'aktivitas' => 'Check-out hotel, belanja di Krisna Oleh-oleh Bali, perjalanan pulang kembali ke Solo.'],
                ],
                'fasilitas' => "• Transportasi Bus Pariwisata AC Eksekutif\n• Hotel Bintang 3 (Twin/Triple share)\n• Tiket Penyeberangan Kapal Feri PP\n• Tiket Fastboat Nusa Penida PP\n• Tiket Masuk Seluruh Objek Wisata\n• Makan 8x sesuai itinerary & Air Mineral\n• Tour Leader & Driver Berpengalaman",
                'fasilitas_tidak_termasuk' => "• Pengeluaran pribadi & laundry\n• Tip Driver dan Tour Leader sukarela\n• Sewa perlengkapan watersport di Tanjung Benoa",
                'meeting_point' => 'Halaman Parkir Depan Stasiun Solo Balapan',
                'jam_kumpul' => '16:00:00',
                'foto' => null,
                'status_aktif' => true,
            ]
        );

        $paketBromo = PaketWisata::firstOrCreate(
            ['nama_paket' => 'Bromo Midnight Sunrise 2H1M'],
            [
                'destinasi' => 'Bromo Tengger Semeru',
                'jenis_layanan' => 'open_trip',
                'harga' => 1250000,
                'durasi_hari' => 2,
                'deskripsi' => 'Menyaksikan golden sunrise terbaik di Penanjakan 1 Bromo, dilanjutkan jelajah Kawah Bromo, Pasir Berbisik, dan Bukit Teletubbies menaiki Jeep 4x4.',
                'itinerary' => [
                    ['hari' => 1, 'judul' => 'Penjemputan & Perjalanan Menuju Rest Area Sukapura', 'aktivitas' => 'Kumpul di meeting point, briefing perjalanan malam menuju Transit Jeep Sukapura Probolinggo.'],
                    ['hari' => 2, 'judul' => 'Jeep 4x4 Sunrise Bromo & Kawah', 'aktivitas' => 'Pukul 03:00 transfer Jeep 4x4 ke Penanjakan 1 menikmati Sunrise, turun ke Laut Pasir, mendaki Kawah Bromo, foto di Pasir Berbisik & Bukit Teletubbies. Makan siang dan kembali ke kota asal.'],
                ],
                'fasilitas' => "• Transportasi Elf Long AC PP\n• Jeep Bromo 4x4 (5 spot utama)\n• Tiket Masuk Taman Nasional TNBTS\n• Sarapan & Makan Siang 1x\n• Driver & Tour Leader",
                'fasilitas_tidak_termasuk' => "• Sewa kuda di Laut Pasir Bromo\n• Jaket & sarung tangan sewa\n• Pengeluaran pribadi",
                'meeting_point' => 'Terminal Tirtonadi Solo (Pintu Timur)',
                'jam_kumpul' => '20:30:00',
                'foto' => null,
                'status_aktif' => true,
            ]
        );

        $paketBajo = PaketWisata::firstOrCreate(
            ['nama_paket' => 'Labuan Bajo Sailing Komodo 3H2M'],
            [
                'destinasi' => 'Labuan Bajo & Pulau Komodo',
                'jenis_layanan' => 'paket_reguler',
                'harga' => 4800000,
                'durasi_hari' => 3,
                'deskripsi' => 'Pengalaman liveaboard berlayar dengan kapal pinisi di perairan Taman Nasional Komodo, trekking Pulau Padar, snorkeling Pink Beach, dan melihat kelelawar di Pulau Kalong.',
                'itinerary' => [
                    ['hari' => 1, 'judul' => 'Kelor - Manjarite - Kalong', 'aktivitas' => 'Penjemputan di Bandara Komodo, check in kapal phinisi, trekking Pulau Kelor, snorkeling di Manjarite, sunset & kelelawar di Pulau Kalong.'],
                    ['hari' => 2, 'judul' => 'Padar - Pink Beach - Komodo - Manta Point', 'aktivitas' => 'Sunrise trekking di puncak Pulau Padar, santai di Pink Beach, trekking ranger mencari komodo dragon, snorkeling berenang bersama manta ray.'],
                    ['hari' => 3, 'judul' => 'Taka Makassar - Kanawa - Bandara', 'aktivitas' => 'Foto di pulau pasir timbul Taka Makassar, snorkeling terumbu karang Pulau Kanawa, kembali ke pelabuhan Labuan Bajo.'],
                ],
                'fasilitas' => "• Liveaboard Kapal Phinisi AC Kabin Mewah 3H2M\n• Makan 7x prasmanan chef di kapal\n• Alat Snorkeling & Life Jacket lengkap\n• Dokumentasi Drone, DSLR & Underwater Go-Pro\n• Tour Guide berlisensi HPI",
                'fasilitas_tidak_termasuk' => "• Tiket Pesawat ke/dari Labuan Bajo\n• Tiket Masuk TN Komodo (KPA & Ranger fee)\n• Pengeluaran pribadi & tip crew kapal",
                'meeting_point' => 'Bandara Internasional Komodo (LBJ)',
                'jam_kumpul' => '09:00:00',
                'foto' => null,
                'status_aktif' => true,
            ]
        );

        $paketRajaAmpat = PaketWisata::firstOrCreate(
            ['nama_paket' => 'Raja Ampat Wayag & Pianemo Explorer 4H3M'],
            [
                'destinasi' => 'Kepulauan Raja Ampat',
                'jenis_layanan' => 'open_trip',
                'harga' => 7500000,
                'durasi_hari' => 4,
                'deskripsi' => 'Jelajahi surga bawah laut dunia di Pianemo, Teluk Kabui, Pasir Timbul, Sawinggrai, dan gugusan karst ikonik Wayag yang memukau mata.',
                'itinerary' => [
                    ['hari' => 1, 'judul' => 'Sorong ke Waisai & Check in Resort', 'aktivitas' => 'Meeting point di Pelabuhan Sorong, naik fast ferry express Bahari ke Waisai, transfer resort tepi pantai.'],
                    ['hari' => 2, 'judul' => 'Trekking Puncak Pianemo & Teluk Kabui', 'aktivitas' => 'Trekking tangga kayu ke viewpoint Pianemo, Teluk Bintang, snorkeling di Friwen Wall, dan foto di Batu Pensil.'],
                    ['hari' => 3, 'judul' => 'Eksplorasi Gugusan Karst Wayag', 'aktivitas' => 'Speedboat menuju gugusan Wayag 1 & 2, memberi makan hiu karang (baby shark) di pos pantai Wayag.'],
                    ['hari' => 4, 'judul' => 'Wisata Burung Cendrawasih & Kembali ke Sorong', 'aktivitas' => 'Pagi birdwatching Cendrawasih di Sawinggrai, kembali dengan ferry ke Sorong.'],
                ],
                'fasilitas' => "• Resort Waterfront AC di Waisai 4H3M\n• Speedboat private selama tur\n• Tiket Kapal Cepat Sorong-Waisai PP VIP\n• Makan lengkap selama tur 9x\n• Guide lokal berpengalaman\n• Dokumentasi underwater & aerial",
                'fasilitas_tidak_termasuk' => "• Tiket Pesawat ke Sorong (SOQ)\n• PIN Kartu Masuk Kawasan Konservasi Perairan (KKP) Raja Ampat\n• Tipping guide",
                'meeting_point' => 'Pelabuhan Rakyat Kota Sorong',
                'jam_kumpul' => '08:00:00',
                'foto' => null,
                'status_aktif' => true,
            ]
        );

        // 6. Jadwal Keberangkatan Realistis
        // Jadwal Bali: 1 minggu lagi
        $tglBali = Carbon::now()->addDays(7)->toDateString();
        $jadwalBali = JadwalKeberangkatan::firstOrCreate(
            ['paket_wisata_id' => $paketBali->id, 'tanggal_berangkat' => $tglBali],
            [
                'kendaraan_id' => $bus->id,
                'kuota' => 30,
                'sisa_kuota' => 28,
                'min_peserta' => 15,
                'biaya_operasional' => 45000000,
                'status_keberangkatan' => 'dijadwalkan',
            ]
        );

        // Jadwal Bromo: 3 hari lagi (hampir penuh)
        $tglBromo = Carbon::now()->addDays(3)->toDateString();
        $jadwalBromo = JadwalKeberangkatan::firstOrCreate(
            ['paket_wisata_id' => $paketBromo->id, 'tanggal_berangkat' => $tglBromo],
            [
                'kendaraan_id' => $hiace->id,
                'kuota' => 14,
                'sisa_kuota' => 4,
                'min_peserta' => 10,
                'biaya_operasional' => 11000000,
                'status_keberangkatan' => 'dikonfirmasi',
            ]
        );

        // Jadwal Labuan Bajo: 14 hari lagi
        $tglBajo = Carbon::now()->addDays(14)->toDateString();
        $jadwalBajo = JadwalKeberangkatan::firstOrCreate(
            ['paket_wisata_id' => $paketBajo->id, 'tanggal_berangkat' => $tglBajo],
            [
                'kendaraan_id' => $speedboat->id,
                'kuota' => 12,
                'sisa_kuota' => 10,
                'min_peserta' => 6,
                'biaya_operasional' => 22000000,
                'status_keberangkatan' => 'dijadwalkan',
            ]
        );

        // Jadwal Raja Ampat: 20 hari lagi
        $tglRaja = Carbon::now()->addDays(20)->toDateString();
        $jadwalRaja = JadwalKeberangkatan::firstOrCreate(
            ['paket_wisata_id' => $paketRajaAmpat->id, 'tanggal_berangkat' => $tglRaja],
            [
                'kendaraan_id' => null,
                'kuota' => 16,
                'sisa_kuota' => 16,
                'min_peserta' => 8,
                'biaya_operasional' => 48000000,
                'status_keberangkatan' => 'dijadwalkan',
            ]
        );

        // 7. Contoh Pemesanan Dikonfirmasi & Tiket Terbit (untuk Budi Santoso di Trip Bromo)
        $pesananBudi = Pemesanan::firstOrCreate(
            ['kode_pemesanan' => 'PSN-1041'],
            [
                'user_id' => $userBudi->id,
                'jadwal_id' => $jadwalBromo->id,
                'jumlah_peserta' => 2,
                'total_biaya' => 2500000,
                'status' => 'dikonfirmasi',
                'batas_bayar' => Carbon::now()->addHours(24),
            ]
        );

        // Peserta untuk Budi
        $pesertaBudi1 = Peserta::firstOrCreate(
            ['pemesanan_id' => $pesananBudi->id, 'nama_lengkap' => 'Budi Santoso'],
            [
                'jenis_identitas' => 'ktp',
                'no_identitas' => '3372011203900001',
                'file_identitas' => 'private/identitas/sample_ktp_budi.jpg',
                'no_hp' => '081311223344',
            ]
        );

        $pesertaBudi2 = Peserta::firstOrCreate(
            ['pemesanan_id' => $pesananBudi->id, 'nama_lengkap' => 'Dewi Anggraini'],
            [
                'jenis_identitas' => 'ktp',
                'no_identitas' => '3372015509920002',
                'file_identitas' => 'private/identitas/sample_ktp_dewi.jpg',
                'no_hp' => '081311223345',
            ]
        );

        // Pembayaran terverifikasi untuk Budi
        $pembayaranBudi = Pembayaran::firstOrCreate(
            ['pemesanan_id' => $pesananBudi->id],
            [
                'nominal' => 2500000,
                'tanggal_bayar' => Carbon::now()->subDays(1)->toDateString(),
                'bukti_transfer' => 'private/bukti_transfer/sample_transfer_budi.jpg',
                'status' => 'diverifikasi',
                'diverifikasi_pada' => Carbon::now()->subHours(12),
                'diverifikasi_oleh' => $admin->id,
            ]
        );

        // Tiket untuk kedua peserta Budi
        $ticketService = app(TicketService::class);
        $ticketService->issueTickets($pesananBudi);

        // 8. Contoh Pemesanan Menunggu Verifikasi Pembayaran (Rina Wijaya di Trip Bali)
        $pesananRina = Pemesanan::firstOrCreate(
            ['kode_pemesanan' => 'PSN-1042'],
            [
                'user_id' => $userRina->id,
                'jadwal_id' => $jadwalBali->id,
                'jumlah_peserta' => 2,
                'total_biaya' => 7000000,
                'status' => 'menunggu',
                'batas_bayar' => Carbon::now()->addHours(18),
            ]
        );

        Peserta::firstOrCreate(
            ['pemesanan_id' => $pesananRina->id, 'nama_lengkap' => 'Rina Wijaya'],
            [
                'jenis_identitas' => 'ktp',
                'no_identitas' => '3371024508930005',
                'file_identitas' => 'private/identitas/sample_ktp_rina.jpg',
                'no_hp' => '081298765432',
            ]
        );

        Peserta::firstOrCreate(
            ['pemesanan_id' => $pesananRina->id, 'nama_lengkap' => 'Doni Kusuma'],
            [
                'jenis_identitas' => 'ktp',
                'no_identitas' => '3371021204910003',
                'file_identitas' => 'private/identitas/sample_ktp_doni.jpg',
                'no_hp' => '081298765433',
            ]
        );

        Pembayaran::firstOrCreate(
            ['pemesanan_id' => $pesananRina->id],
            [
                'nominal' => 7000000,
                'tanggal_bayar' => Carbon::now()->toDateString(),
                'bukti_transfer' => 'private/bukti_transfer/sample_transfer_rina.jpg',
                'status' => 'menunggu',
                'catatan_admin' => null,
            ]
        );

        // 9. Contoh Custom Trip dari Siti Nurhaliza
        CustomTrip::firstOrCreate(
            ['user_id' => $userSiti->id, 'tujuan' => 'Dieng Plateau Family Gathering'],
            [
                'tanggal_diinginkan' => Carbon::now()->addDays(25)->toDateString(),
                'durasi_hari' => 3,
                'jumlah_peserta' => 12,
                'kebutuhan' => 'Private tour reuni keluarga besar, butuh armada Hiace Premio ber-AC, penginapan villa privat 4 kamar di Dieng, kunjungan ke Kawah Sikidang, Telaga Warna, Candi Arjuna, dan berburu Golden Sunrise Sikunir. Mohon sertakan catering makanan halal 3x sehari.',
                'status' => 'diajukan',
            ]
        );

        // Log seeding selesai
        LogAktivitas::catat('seeder_database', 'Sistem', null, 'Inisialisasi database master data Jelajah Nusa berhasil dijalankan.', $admin->id);
    }
}
