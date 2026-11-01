# Jelajah Nusa — Sistem Informasi Travel dan Agen Wisata

> **Sistem Informasi Manajemen Reservasi, Operasional Keberangkatan, Verifikasi Pembayaran, E-Tiket QR Code, dan Pelaporan Keuangan Agen Wisata Jelajah Nusa.**
> Dibangun sesuai dengan Dokumen Spesifikasi Kebutuhan Perangkat Lunak (PRD v1.0 & v1.1).

---

## 📋 Daftar Isi
1. [Tentang Sistem](#-tentang-sistem)
2. [Fitur Utama](#-fitur-utama)
3. [Arsitektur & Teknologi](#-arsitektur--teknologi)
4. [Struktur Database & Relasi](#-struktur-database--relasi)
5. [Akun Pengguna Default (Seeder)](#-akun-pengguna-default-seeder)
6. [Panduan Instalasi & Menjalankan Sistem](#-panduan-instalasi--menjalankan-sistem)
   - [Opsi A: Menggunakan XAMPP & MySQL / phpMyAdmin](#opsi-a-menggunakan-xampp--mysql--phpmyadmin-rekomendasi)
   - [Opsi B: Menggunakan SQLite (Zero Setup)](#opsi-b-menggunakan-sqlite-zero-setup)
7. [Pengujian Otomatis (Automated Tests)](#-pengujian-otomatis-automated-tests)
8. [Aturan Bisnis & Mekanisme Kunci](#-aturan-bisnis--mekanisme-kunci)
9. [Struktur Direktori Proyek](#-struktur-direktori-proyek)

---

## 🌟 Tentang Sistem
**Jelajah Nusa** adalah platform digital komprehensif untuk biro perjalanan wisata yang menghubungkan wisatawan dengan pengelola agen travel secara real-time. Sistem ini menyelesaikan berbagai masalah operasional tour & travel konvensional seperti:
- Menghilangkan *overbooking* melalui penguncian kuota kursi atomik (*atomic lock*).
- Menjamin kelayakan margin keberangkatan dengan perhitungan otomatis ambang batas minimal peserta (*feasibility panel*).
- Penerbitan tiket digital per individu penumpang lengkap dengan QR code terenkripsi dan verifikasi online.
- Perlindungan dokumen pribadi (KTP/Paspor & Bukti Transfer) melalui penyimpanan *private storage* berotentikasi.
- Pelaporan operasional & laba kotor yang dapat diekspor langsung ke PDF dan CSV (Excel).

---

## 🚀 Fitur Utama

### 🧑‍💼 1. Panel Wisatawan (Frontend & Self-Service)
- **Katalog & Filter Interaktif:** Cari paket wisata berdasarkan destinasi, jenis paket (*Open Trip*, *Private Trip*, *Family Gathering*), rentang harga, dan pengurutan (termurah, terpopuler, terbaru).
- **Detail Paket Komprehensif:** Galeri foto, *itinerary* dinamis hari demi hari, fasilitas include/exclude, informasi armada kendaraan, dan live jadwal keberangkatan dengan sisa kuota kursi.
- **Pemesanan Multi-Step Dinamis:**
  - Pilih jadwal dan jumlah kursi.
  - Input biodata peserta satu per satu (Nama Lengkap, KTP/Paspor, No HP, upload file identitas maks 2MB).
  - *Seat holding* otomatis selama 24 jam dengan penghitungan mundur pembayaran (*countdown timer*).
- **Pembayaran Mandiri:** Informasi rekening bank agen, upload bukti transfer dengan verifikasi format file, dan status pembayaran real-time.
- **Pesanan Saya:** Manajemen riwayat pemesanan dengan filter tab status (*Semua*, *Menunggu*, *Dikonfirmasi*, *Batal*), tombol batalkan pesanan mandiri, dan unduh tiket.
- **E-Tiket PDF & QR Code:** Tiket individual per penumpang dengan kode tiket unik (misal `JN-PSN1041-01`), informasi barcode QR, serta tombol cetak/unduh PDF.
- **Halaman Verifikasi Tiket Publik:** URL verifikasi aman (`/verifikasi/{kode}`) untuk pemindaian di lapangan tanpa mengekspos data pribadi sensitif.
- **Permintaan Custom Trip:** Form pengajuan tur khusus (tujuan kustom, tanggal, armada yang diinginkan, catatan khusus) dan peninjauan penawaran harga dari admin.

### 🛡️ 2. Panel Admin Travel (Backoffice Operasional)
- **Dashboard Eksekutif:**
  - 4 Kartu Metrik: Omset Bulan Ini, Total Pemesanan, Jadwal Aktif, dan Verifikasi Tertunda.
  - Grafik tren pendapatan bulanan interaktif (*Chart.js*).
  - Panel Aksi Mendesak (*Action Needed*): Antrean bukti transfer menunggu verifikasi & jadwal belum layak berangkat.
  - Tabel 5 Paket Terlaris (*Best Sellers*).
- **Master Data Paket Wisata:** Manajemen CRUD paket, unggah thumbnail/galeri, builder timeline itinerary dinamis, fasilitas, dan toggle status aktif.
- **Master Jadwal & Armada:** Manajemen jadwal keberangkatan, alokasi armada bus/hiace/speedboat, kuota kursi, serta proteksi penurunan kuota di bawah kursi aktif.
- **Master Data Kendaraan:** Pelacakan status armada (*Tersedia*, *Dalam Perjalanan*, *Perawatan*).
- **Antrean Verifikasi Pembayaran:**
  - Pratinjau bukti transfer aman.
  - Tombol Verifikasi: Mengonfirmasi pemesanan dan otomatis menerbitkan tiket digital untuk seluruh peserta.
  - Modal Penolakan: Wajib menyertakan alasan penolakan, membatalkan pesanan, dan mengembalikan kuota kursi seketika.
- **Panel Kelayakan Keberangkatan (*Feasibility Panel*):**
  - Progress bar visual keterisian kursi dengan garis ambang batas minimal peserta (*break-even point*).
  - Status badge: `Memenuhi Kuota Minimal` (Hijau) vs `Belum Memenuhi Minimal` (Merah).
  - Tombol aksi terintegrasi: Konfirmasi Keberangkatan, Tunda (Reschedule), atau Batalkan Keberangkatan dengan otomatisasi antrean *refund*.
- **Kelola Pengembalian Dana (Refund & Reschedule):** Validasi nominal refund tidak boleh melebihi total bayar, pencatatan rekening tujuan wisatawan, dan pelacakan status dana.
- **Pemindai & Validasi Tiket:** Antarmuka verifikasi tiket petugas lapangan (pencarian manual kode atau scan QR).
- **Laporan Operasional & Keuangan:**
  - Filter periode tanggal, paket, dan status.
  - Ringkasan total transaksi, omset kotor, estimasi laba kotor, dan jumlah penumpang terangkut.
  - **Ekspor Laporan ke PDF:** Dokumen tabel resmi berstempel dan rapi siap cetak.
  - **Ekspor Laporan ke CSV:** File data terstandarisasi kompatibel dengan Microsoft Excel / Google Sheets.
- **Pencarian Global (*Global Search*):** Bar pencarian cepat (`Command + K` / `Ctrl + K`) untuk melacak paket, nomor pemesanan, nama peserta, atau jadwal.
- **Pengaturan Sistem:** Konfigurasi rekening bank penerima dan batas waktu pembayaran (*payment deadline window*).

---

## 🛠️ Arsitektur & Teknologi

| Komponen | Spesifikasi / Library | Keterangan |
| :--- | :--- | :--- |
| **Bahasa Pemrograman** | PHP 8.2+ | Strict typing, arrow functions, match expressions |
| **Framework Backend** | Laravel 11.x | Eloquent ORM, Service Layer, Middleware, Policies |
| **Database** | MySQL / MariaDB & SQLite | Kompatibel penuh phpMyAdmin (dump SQL disertakan) |
| **Frontend Layout** | Blade Templates | Komponen modular, transisi status UI bersih |
| **Styling CSS** | Tailwind CSS v3 (Vite) | Utility-first, responsif desktop & mobile |
| **Interaktivitas UI** | Alpine.js | Modal, tab filter, dynamic form row, countdown |
| **Grafik Dashboard** | Chart.js | Visualisasi omset bulanan dinamis |
| **Penerbitan PDF** | Barryvdh/Laravel-DomPDF | Render tiket e-boarding pass & laporan berkas A4 |
| **Generator QR Code** | SimpleSoftwareIO/Simple-QRCode | Vektor SVG QR Code standar ISO tanpa dependensi eksternal |
| **Zona Waktu & Uang** | Asia/Jakarta (WIB) & IDR | Format Rupiah `Rp X.XXX.XXX` & Tanggal `d M Y` |

---

## 🗄️ Struktur Database & Relasi

Sistem menggunakan skema relasional terstandarisasi:
- `users`: Akun admin dan wisatawan (`id`, `nama_lengkap`, `username`, `email`, `role`, `status_aktif`).
- `paket_wisata`: Master destinasi, durasi, harga per orang, deskripsi, fasilitas, itinerary JSON.
- `kendaraan`: Master armada travel (`jenis_kendaraan`, `kapasitas`, `plat_nomor`, `status`).
- `jadwal_keberangkatan`: Tanggal jalan, kuota, sisa kuota, minimal peserta, biaya operasional, status.
- `custom_trip`: Pengajuan tur kustom oleh wisatawan beserta penawaran balik dari admin.
- `pemesanan`: Header transaksi reservasi (`kode_pemesanan`, `total_biaya`, `batas_bayar`, `status`).
- `peserta`: Detail penumpang per kursi (`nama_lengkap`, `jenis_identitas`, `no_identitas`, `file_identitas`).
- `pembayaran`: Catatan transfer (`nominal`, `bukti_transfer`, `status`, `catatan_admin`, `diverifikasi_oleh`).
- `tiket`: Tiket individu per peserta (`kode_tiket`, `qr_code`, `status`).
- `pengembalian`: Catatan pengembalian dana atau jadwal baru jika trip dibatalkan/ditunda.
- `pengaturan`: Konfigurasi rekening bank dan batas waktu pembayaran.
- `log_aktivitas`: Jejak audit trail aktivitas penting sistem.

---

## 🔑 Akun Pengguna Default (Seeder)

Database telah dilengkapi data sampel siap pakai:

| Role | Username | Email | Password | Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| **Admin Travel** | `admin` | `admin@jelajahnusa.com` | `password` | Hak akses penuh backoffice & verifikasi |
| **Wisatawan 1** | `budi_santoso` | `budi@gmail.com` | `password` | Pemesanan Dikonfirmasi & E-Tiket Aktif |
| **Wisatawan 2** | `rina_wijaya` | `rina@gmail.com` | `password` | Pemesanan Menunggu Verifikasi Pembayaran |
| **Wisatawan 3** | `andi_saputra` | `andi@gmail.com` | `password` | Pengajuan Custom Trip Menunggu Review |

---

## 💻 Panduan Instalasi & Menjalankan Sistem

### Opsi A: Menggunakan XAMPP & MySQL / phpMyAdmin (Rekomendasi)

1. **Jalankan Apache & MySQL** pada aplikasi XAMPP Anda.
2. **Buka phpMyAdmin** di browser (`http://localhost/phpmyadmin`).
3. **Impor Database:**
   - Klik tab **Import** (atau buat database baru bernama `jelajah_nusa`).
   - Pilih berkas **`jelajah_nusa.sql`** yang ada di direktori root proyek ini.
   - Klik tombol **Go / Impor**. Seluruh tabel dan data seeder otomatis terpasang.
4. **Konfigurasi File `.env`:**
   Pastikan konfigurasi koneksi database di berkas `.env` mengarah ke MySQL:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=jelajah_nusa
   DB_USERNAME=root
   DB_PASSWORD=
   ```
5. **Install Dependensi & Build Asset (Jika belum):**
   ```bash
   composer install
   npm install
   npm run build
   php artisan storage:link
   ```
6. **Jalankan Server Laravel:**
   ```bash
   php artisan serve
   ```
7. Akses sistem melalui browser: `http://127.0.0.1:8000`.

---

### Opsi B: Menggunakan SQLite (Zero Setup)

Proyek ini telah dikonfigurasi dengan SQLite lokal siap pakai tanpa perlu server eksternal:

1. **Pastikan database SQLite siap:**
   ```bash
   touch database/database.sqlite
   php artisan migrate:fresh --seed
   ```
2. **Jalankan Server Development:**
   ```bash
   php artisan serve
   ```
3. Buka browser pada `http://127.0.0.1:8000`.

---

## 🧪 Pengujian Otomatis (Automated Tests)

Sistem telah dilengkapi dengan *Automated Test Suite* lengkap yang menguji seluruh 23 kasus uji dalam dokumen PRD:

```bash
php artisan test
```

### Hasil Pengujian:
```text
   PASS  Tests\Unit\ExampleTest
  ✓ that true is true

   PASS  Tests\Feature\ExampleTest
  ✓ the application returns a successful response

   PASS  Tests\Feature\JelajahNusaTest
  ✓ t01 login with valid and invalid credentials
  ✓ t02 wisatawan cannot access admin dashboard returns 403
  ✓ t03 registration with duplicate email or username is rejected
  ✓ t04 booking more seats than remaining quota is rejected with exact remaining count
  ✓ t05 race condition and quota not negative
  ✓ t06 confirm and cancel booking restores quota
  ✓ t07 upload identity file too large is rejected
  ✓ t08 wisatawan a cannot access wisatawan b identity document returns 403
  ✓ t09 reject payment without reason is rejected
  ✓ t10 payment verified confirms booking and issues tickets
  ✓ t11 feasibility status belum memenuhi minimal when below min participants
  ✓ t12 admin postpones or cancels schedule
  ✓ t13 ticket verification valid and invalid
  ✓ t14 deleting package with existing bookings is rejected with warning
  ✓ t15 export reports to pdf and csv
  ✓ t16 custom trip workflow from submission to approval
  ✓ t17 password reset with expired token is rejected
  ✓ t18 decreasing schedule quota below active participants is rejected
  ✓ t19 refund amount exceeding total paid is rejected
  ✓ t20 percentage calculation handles zero previous month
  ✓ t21 expired waiting bookings canceled automatically
  ✓ t22 wisatawan cancels waiting booking restores seats
  ✓ t23 global search returns categorized results

  Tests:    25 passed (82 assertions)
  Duration: 4.27s
```

---

## 🔒 Aturan Bisnis & Mekanisme Kunci

1. **Pencegahan Overbooking (Atomic Quota Lock):**
   Pada `BookingService::createBooking()`, pengecekan kuota kursi menggunakan query `lockForUpdate()`. Hal ini menjamin bahwa reservasi simultan pada milidetik yang sama tidak akan menyebabkan kuota menjadi minus atau terjadi pesanan ganda.
2. **Seat Holding & Pembatalan Otomatis 24 Jam:**
   Setiap pemesanan yang dibuat langsung memotong `sisa_kuota` dan statusnya `menunggu`. Jika bukti transfer tidak diverifikasi dalam batas waktu (default 24 jam), perintah terjadwal `php artisan booking:cancel-expired` otomatis membatalkan pesanan dan mengembalikan kursi ke inventori.
3. **E-Tiket Per Peserta:**
   Satu kode pemesanan dengan 3 orang peserta akan menghasilkan 3 lembar e-tiket terpisah. Masing-masing tiket memiliki kode unik (`JN-XXXXX-01`, `JN-XXXXX-02`, dst.) dan kode QR tersendiri untuk kemudahan boarding.
4. **Keamanan Dokumen Pribadi (Private Storage):**
   Berkas identitas (KTP/Paspor) dan bukti transfer disimpan di `storage/app/private/` (bukan di folder publik). Berkas hanya dapat diunduh melalui `FileAccessController` yang memeriksa otorisasi pemilik pesanan atau administrator travel. Wisatawan lain yang mencoba mengakses URL dokumen milik orang lain otomatis menerima respon `403 Forbidden`.
5. **Formula Kelayakan Keberangkatan (*Feasibility*):**
   Jadwal dinyatakan `Memenuhi Kuota Minimal` jika total penumpang terkonfirmasi $\ge$ `min_peserta`. Rasio margin keuntungan dihitung dari:
   $$\text{Estimasi Margin} = \frac{\text{Pendapatan Terkonfirmasi} - \text{Biaya Operasional}}{\text{Pendapatan Terkonfirmasi}} \times 100\%$$

---

## 📂 Struktur Direktori Proyek

```text
uas-pak-marta-2/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/           # 13 Controller Backoffice Admin
│   │   │   ├── AuthController.php
│   │   │   ├── BookingController.php
│   │   │   ├── FileAccessController.php
│   │   │   ├── TiketController.php
│   │   │   └── ...
│   │   └── Middleware/
│   │       └── RoleMiddleware.php
│   ├── Models/                  # 12 Model Eloquent (User, Paket, Tiket, dll.)
│   └── Services/                # Service Layer (Booking, Ticket, Quota, Report, dll.)
├── database/
│   ├── migrations/              # Skema tabel database MySQL/SQLite
│   ├── seeders/                 # Seeder akun, master armada, paket, dan transaksi
│   └── jelajah_nusa.sql         # SQL dump siap impor phpMyAdmin
├── resources/
│   ├── css/                     # Tailwind CSS v3
│   ├── js/                      # Alpine.js & Chart.js integration
│   └── views/
│       ├── layouts/             # Layout Admin (Figma Frame) & Wisatawan
│       ├── admin/               # Tampilan panel backoffice
│       ├── wisatawan/           # Tampilan portal pemesanan wisatawan
│       └── tiket/               # Template PDF tiket & halaman verifikasi QR
├── routes/
│   ├── web.php                  # 72 route terdaftar rapi
│   └── console.php              # Scheduler booking:cancel-expired
├── tests/
│   └── Feature/
│       └── JelajahNusaTest.php  # 23 Test Cases PRD (T-01 s/d T-23)
├── jelajah_nusa.sql             # SQL dump root untuk kemudahan akses phpMyAdmin
└── README.md                    # Dokumentasi lengkap sistem
```

---

*Dikembangkan untuk Tugas Akhir / UAS Sistem Informasi Travel dan Agen Wisata Jelajah Nusa.*
