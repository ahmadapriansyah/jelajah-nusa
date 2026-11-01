-- ==============================================================
-- Jelajah Nusa - Database Dump for MySQL / phpMyAdmin
-- Sistem Informasi Travel dan Agen Wisata
-- Generated: 2026-10-30 13:16:56
-- ==============================================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+07:00";
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `jelajah_nusa` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `jelajah_nusa`;

DROP TABLE IF EXISTS `log_aktivitas`;
DROP TABLE IF EXISTS `pengaturan`;
DROP TABLE IF EXISTS `pengembalian`;
DROP TABLE IF EXISTS `tiket`;
DROP TABLE IF EXISTS `pembayaran`;
DROP TABLE IF EXISTS `peserta`;
DROP TABLE IF EXISTS `pemesanan`;
DROP TABLE IF EXISTS `custom_trip`;
DROP TABLE IF EXISTS `jadwal_keberangkatan`;
DROP TABLE IF EXISTS `kendaraan`;
DROP TABLE IF EXISTS `paket_wisata`;
DROP TABLE IF EXISTS `failed_jobs`;
DROP TABLE IF EXISTS `job_batches`;
DROP TABLE IF EXISTS `jobs`;
DROP TABLE IF EXISTS `cache_locks`;
DROP TABLE IF EXISTS `cache`;
DROP TABLE IF EXISTS `sessions`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `users`;

create table `users` (`id` bigint unsigned not null auto_increment primary key, `nama_lengkap` varchar(100) not null, `email` varchar(100) not null, `no_hp` varchar(20) null, `username` varchar(50) not null, `password` varchar(255) not null, `role` enum('admin', 'wisatawan') not null default 'wisatawan', `status_aktif` tinyint(1) not null default '1', `email_verified_at` timestamp null, `remember_token` varchar(100) null, `created_at` timestamp null, `updated_at` timestamp null);
alter table `users` add unique `users_email_unique`(`email`);
alter table `users` add unique `users_username_unique`(`username`);
alter table `users` add index `users_role_index`(`role`);
alter table `users` add index `users_status_aktif_index`(`status_aktif`);
create table `password_reset_tokens` (`email` varchar(255) not null, `token` varchar(255) not null, `created_at` timestamp null, primary key (`email`));
create table `sessions` (`id` varchar(255) not null, `user_id` bigint unsigned null, `ip_address` varchar(45) null, `user_agent` text null, `payload` longtext not null, `last_activity` int not null, primary key (`id`));
alter table `sessions` add index `sessions_user_id_index`(`user_id`);
alter table `sessions` add index `sessions_last_activity_index`(`last_activity`);
create table `cache` (`key` varchar(255) not null, `value` mediumtext not null, `expiration` int not null, primary key (`key`));
create table `cache_locks` (`key` varchar(255) not null, `owner` varchar(255) not null, `expiration` int not null, primary key (`key`));
create table `jobs` (`id` bigint unsigned not null auto_increment primary key, `queue` varchar(255) not null, `payload` longtext not null, `attempts` tinyint unsigned not null, `reserved_at` int unsigned null, `available_at` int unsigned not null, `created_at` int unsigned not null);
alter table `jobs` add index `jobs_queue_index`(`queue`);
create table `job_batches` (`id` varchar(255) not null, `name` varchar(255) not null, `total_jobs` int not null, `pending_jobs` int not null, `failed_jobs` int not null, `failed_job_ids` longtext not null, `options` mediumtext null, `cancelled_at` int null, `created_at` int not null, `finished_at` int null, primary key (`id`));
create table `failed_jobs` (`id` bigint unsigned not null auto_increment primary key, `uuid` varchar(255) not null, `connection` text not null, `queue` text not null, `payload` longtext not null, `exception` longtext not null, `failed_at` timestamp not null default CURRENT_TIMESTAMP);
alter table `failed_jobs` add unique `failed_jobs_uuid_unique`(`uuid`);
create table `paket_wisata` (`id` bigint unsigned not null auto_increment primary key, `nama_paket` varchar(150) not null, `destinasi` varchar(100) not null, `jenis_layanan` enum('open_trip', 'paket_reguler') not null, `harga` decimal(12, 2) not null, `durasi_hari` tinyint unsigned not null, `deskripsi` text null, `itinerary` json not null, `fasilitas` text not null, `fasilitas_tidak_termasuk` text null, `meeting_point` varchar(150) not null, `jam_kumpul` time not null, `foto` varchar(255) null, `status_aktif` tinyint(1) not null default '1', `created_at` timestamp null, `updated_at` timestamp null);
alter table `paket_wisata` add index `paket_wisata_destinasi_index`(`destinasi`);
alter table `paket_wisata` add index `paket_wisata_jenis_layanan_index`(`jenis_layanan`);
alter table `paket_wisata` add index `paket_wisata_status_aktif_index`(`status_aktif`);
create table `kendaraan` (`id` bigint unsigned not null auto_increment primary key, `jenis_kendaraan` varchar(50) not null, `kapasitas` tinyint unsigned not null, `plat_nomor` varchar(15) null, `status` enum('tersedia', 'tidak_tersedia') not null default 'tersedia', `created_at` timestamp null, `updated_at` timestamp null);
alter table `kendaraan` add index `kendaraan_status_index`(`status`);
create table `jadwal_keberangkatan` (`id` bigint unsigned not null auto_increment primary key, `paket_wisata_id` bigint unsigned not null, `kendaraan_id` bigint unsigned null, `tanggal_berangkat` date not null, `kuota` int unsigned not null, `sisa_kuota` int unsigned not null, `min_peserta` int unsigned not null, `biaya_operasional` decimal(12, 2) not null default '0', `status_keberangkatan` enum('dijadwalkan', 'dikonfirmasi', 'ditunda', 'dibatalkan') not null default 'dijadwalkan', `created_at` timestamp null, `updated_at` timestamp null);
alter table `jadwal_keberangkatan` add constraint `jadwal_keberangkatan_paket_wisata_id_foreign` foreign key (`paket_wisata_id`) references `paket_wisata` (`id`) on delete restrict;
alter table `jadwal_keberangkatan` add constraint `jadwal_keberangkatan_kendaraan_id_foreign` foreign key (`kendaraan_id`) references `kendaraan` (`id`) on delete set null;
alter table `jadwal_keberangkatan` add index `jadwal_keberangkatan_paket_wisata_id_tanggal_berangkat_index`(`paket_wisata_id`, `tanggal_berangkat`);
alter table `jadwal_keberangkatan` add index `jadwal_keberangkatan_status_keberangkatan_index`(`status_keberangkatan`);
create table `custom_trip` (`id` bigint unsigned not null auto_increment primary key, `user_id` bigint unsigned not null, `kendaraan_id` bigint unsigned null, `tujuan` varchar(150) not null, `tanggal_diinginkan` date not null, `durasi_hari` tinyint unsigned not null, `jumlah_peserta` int unsigned not null, `kebutuhan` text not null, `fasilitas_penawaran` text null, `jadwal_penawaran` date null, `biaya_penawaran` decimal(12, 2) null, `status` enum('diajukan', 'ditawarkan', 'disetujui', 'ditolak') not null default 'diajukan', `created_at` timestamp null, `updated_at` timestamp null);
alter table `custom_trip` add constraint `custom_trip_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete restrict;
alter table `custom_trip` add constraint `custom_trip_kendaraan_id_foreign` foreign key (`kendaraan_id`) references `kendaraan` (`id`) on delete set null;
alter table `custom_trip` add index `custom_trip_status_index`(`status`);
create table `pemesanan` (`id` bigint unsigned not null auto_increment primary key, `kode_pemesanan` varchar(20) not null, `user_id` bigint unsigned not null, `jadwal_id` bigint unsigned null, `custom_trip_id` bigint unsigned null, `jumlah_peserta` int unsigned not null, `total_biaya` decimal(12, 2) not null, `batas_bayar` datetime null, `status` enum('menunggu', 'dikonfirmasi', 'dibatalkan') not null default 'menunggu', `created_at` timestamp null, `updated_at` timestamp null);
alter table `pemesanan` add constraint `pemesanan_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete restrict;
alter table `pemesanan` add constraint `pemesanan_jadwal_id_foreign` foreign key (`jadwal_id`) references `jadwal_keberangkatan` (`id`) on delete restrict;
alter table `pemesanan` add constraint `pemesanan_custom_trip_id_foreign` foreign key (`custom_trip_id`) references `custom_trip` (`id`) on delete restrict;
alter table `pemesanan` add index `pemesanan_user_id_status_index`(`user_id`, `status`);
alter table `pemesanan` add unique `pemesanan_kode_pemesanan_unique`(`kode_pemesanan`);
alter table `pemesanan` add unique `pemesanan_custom_trip_id_unique`(`custom_trip_id`);
alter table `pemesanan` add index `pemesanan_status_index`(`status`);
create table `peserta` (`id` bigint unsigned not null auto_increment primary key, `pemesanan_id` bigint unsigned not null, `nama_lengkap` varchar(100) not null, `jenis_identitas` enum('ktp', 'paspor') not null, `no_identitas` varchar(30) not null, `file_identitas` varchar(255) not null, `no_hp` varchar(20) null, `created_at` timestamp null, `updated_at` timestamp null);
alter table `peserta` add constraint `peserta_pemesanan_id_foreign` foreign key (`pemesanan_id`) references `pemesanan` (`id`) on delete cascade;
alter table `peserta` add index `peserta_no_identitas_index`(`no_identitas`);
create table `pembayaran` (`id` bigint unsigned not null auto_increment primary key, `pemesanan_id` bigint unsigned not null, `nominal` decimal(12, 2) not null, `tanggal_bayar` date not null, `bukti_transfer` varchar(255) not null, `status` enum('menunggu', 'diverifikasi', 'ditolak') not null default 'menunggu', `catatan_admin` text null, `diverifikasi_pada` timestamp null, `diverifikasi_oleh` bigint unsigned null, `created_at` timestamp null, `updated_at` timestamp null);
alter table `pembayaran` add constraint `pembayaran_pemesanan_id_foreign` foreign key (`pemesanan_id`) references `pemesanan` (`id`) on delete restrict;
alter table `pembayaran` add constraint `pembayaran_diverifikasi_oleh_foreign` foreign key (`diverifikasi_oleh`) references `users` (`id`) on delete set null;
alter table `pembayaran` add index `pembayaran_status_index`(`status`);
create table `tiket` (`id` bigint unsigned not null auto_increment primary key, `pemesanan_id` bigint unsigned not null, `peserta_id` bigint unsigned not null, `kode_tiket` varchar(30) not null, `qr_code` mediumtext null, `tanggal_terbit` datetime not null, `status` enum('aktif', 'dibatalkan') not null default 'aktif', `created_at` timestamp null, `updated_at` timestamp null);
alter table `tiket` add constraint `tiket_pemesanan_id_foreign` foreign key (`pemesanan_id`) references `pemesanan` (`id`) on delete cascade;
alter table `tiket` add constraint `tiket_peserta_id_foreign` foreign key (`peserta_id`) references `peserta` (`id`) on delete cascade;
alter table `tiket` add unique `tiket_kode_tiket_unique`(`kode_tiket`);
alter table `tiket` add index `tiket_status_index`(`status`);
create table `pengembalian` (`id` bigint unsigned not null auto_increment primary key, `pemesanan_id` bigint unsigned not null, `jadwal_baru_id` bigint unsigned null, `jenis` enum('refund', 'pindah_jadwal') not null, `nominal` decimal(12, 2) null, `alasan` text not null, `tanggal_pengembalian` date not null, `created_at` timestamp null, `updated_at` timestamp null);
alter table `pengembalian` add constraint `pengembalian_pemesanan_id_foreign` foreign key (`pemesanan_id`) references `pemesanan` (`id`) on delete restrict;
alter table `pengembalian` add constraint `pengembalian_jadwal_baru_id_foreign` foreign key (`jadwal_baru_id`) references `jadwal_keberangkatan` (`id`) on delete restrict;
alter table `pengembalian` add index `pengembalian_jenis_index`(`jenis`);
create table `pengaturan` (`id` bigint unsigned not null auto_increment primary key, `nama_bank` varchar(50) not null, `no_rekening` varchar(50) not null, `atas_nama` varchar(100) not null, `batas_bayar_jam` int unsigned not null default '24', `created_at` timestamp null, `updated_at` timestamp null);
create table `log_aktivitas` (`id` bigint unsigned not null auto_increment primary key, `user_id` bigint unsigned null, `subjek_tipe` varchar(100) null, `subjek_id` bigint unsigned null, `aksi` varchar(100) not null, `keterangan` text null, `ip_address` varchar(45) null, `created_at` timestamp null, `updated_at` timestamp null);
alter table `log_aktivitas` add constraint `log_aktivitas_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete set null;
alter table `log_aktivitas` add index `log_aktivitas_aksi_index`(`aksi`);

-- Data for table `users` --
INSERT INTO `users` (`id`, `nama_lengkap`, `email`, `no_hp`, `username`, `password`, `role`, `status_aktif`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`) VALUES (1, 'Admin Travel Jelajah Nusa', 'admin@jelajahnusa.com', '081234567890', 'admin', '$2y$12$ZFhMzaZ6AZYUL5w24Vn39OoewRJ/d81EI/IiEPfytjU9cjxRVAPum', 'admin', 1, NULL, NULL, '2026-10-30 12:49:07', '2026-10-30 12:49:07');
INSERT INTO `users` (`id`, `nama_lengkap`, `email`, `no_hp`, `username`, `password`, `role`, `status_aktif`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`) VALUES (2, 'Rina Wijaya', 'rina@example.com', '081298765432', 'rinawijaya', '$2y$12$rd6iNnfT4b.Ni2W8ITnHCOTurepCHcMP5AybA2ttQnIubU9P0A44q', 'wisatawan', 1, NULL, NULL, '2026-10-30 12:49:08', '2026-10-30 12:49:08');
INSERT INTO `users` (`id`, `nama_lengkap`, `email`, `no_hp`, `username`, `password`, `role`, `status_aktif`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`) VALUES (3, 'Budi Santoso', 'budi@example.com', '081311223344', 'budisantoso', '$2y$12$O.X6OIqvdyDruevItT0Sw.Pe6qEMnCO9pfbk1ttU.espes8c2ZByO', 'wisatawan', 1, NULL, NULL, '2026-10-30 12:49:08', '2026-10-30 12:49:08');
INSERT INTO `users` (`id`, `nama_lengkap`, `email`, `no_hp`, `username`, `password`, `role`, `status_aktif`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`) VALUES (4, 'Siti Nurhaliza', 'siti@example.com', '085612345678', 'sitinur', '$2y$12$0.ZKDwWbDhdOyX6T9bxbveHpIYEZsj78Fhz8NIuKndpmxoQBX5zVq', 'wisatawan', 1, NULL, NULL, '2026-10-30 12:49:09', '2026-10-30 12:49:09');

-- Data for table `paket_wisata` --
INSERT INTO `paket_wisata` (`id`, `nama_paket`, `destinasi`, `jenis_layanan`, `harga`, `durasi_hari`, `deskripsi`, `itinerary`, `fasilitas`, `fasilitas_tidak_termasuk`, `meeting_point`, `jam_kumpul`, `foto`, `status_aktif`, `created_at`, `updated_at`) VALUES (1, 'Solo – Bali Eksotis 4H3M', 'Bali & Nusa Penida', 'open_trip', 3500000, 4, 'Nikmati pesona Pulau Dewata mulai dari Pantai Pandawa, Pura Uluwatu, Nusa Penida Kelingking Beach hingga sunset di Tanah Lot dengan akomodasi hotel bintang 3.', '[{"hari":1,"judul":"Keberangkatan & Penyeberangan Ketapang-Gilimanuk","aktivitas":"Kumpul di Meeting Point Stasiun Solo Balapan, perjalanan malam via tol trans jawa menuju Pelabuhan Ketapang."},{"hari":2,"judul":"Tiba di Bali & Explore Pesisir Selatan","aktivitas":"Sarapan pagi di Gilimanuk, menuju Pantai Pandawa, Pura Uluwatu, dan makan malam seafood di Pantai Jimbaran."},{"hari":3,"judul":"Island Hopping Nusa Penida","aktivitas":"Naik fastboat Sanur - Nusa Penida, kunjungan ke Kelingking Cliff, Broken Beach, Angel Billabong, dan Crystal Bay."},{"hari":4,"judul":"Belanja Oleh-oleh Krisna & Perjalanan Pulang","aktivitas":"Check-out hotel, belanja di Krisna Oleh-oleh Bali, perjalanan pulang kembali ke Solo."}]', '• Transportasi Bus Pariwisata AC Eksekutif
• Hotel Bintang 3 (Twin/Triple share)
• Tiket Penyeberangan Kapal Feri PP
• Tiket Fastboat Nusa Penida PP
• Tiket Masuk Seluruh Objek Wisata
• Makan 8x sesuai itinerary & Air Mineral
• Tour Leader & Driver Berpengalaman', '• Pengeluaran pribadi & laundry
• Tip Driver dan Tour Leader sukarela
• Sewa perlengkapan watersport di Tanjung Benoa', 'Halaman Parkir Depan Stasiun Solo Balapan', '16:00:00', NULL, 1, '2026-10-30 12:49:09', '2026-10-30 12:49:09');
INSERT INTO `paket_wisata` (`id`, `nama_paket`, `destinasi`, `jenis_layanan`, `harga`, `durasi_hari`, `deskripsi`, `itinerary`, `fasilitas`, `fasilitas_tidak_termasuk`, `meeting_point`, `jam_kumpul`, `foto`, `status_aktif`, `created_at`, `updated_at`) VALUES (2, 'Bromo Midnight Sunrise 2H1M', 'Bromo Tengger Semeru', 'open_trip', 1250000, 2, 'Menyaksikan golden sunrise terbaik di Penanjakan 1 Bromo, dilanjutkan jelajah Kawah Bromo, Pasir Berbisik, dan Bukit Teletubbies menaiki Jeep 4x4.', '[{"hari":1,"judul":"Penjemputan & Perjalanan Menuju Rest Area Sukapura","aktivitas":"Kumpul di meeting point, briefing perjalanan malam menuju Transit Jeep Sukapura Probolinggo."},{"hari":2,"judul":"Jeep 4x4 Sunrise Bromo & Kawah","aktivitas":"Pukul 03:00 transfer Jeep 4x4 ke Penanjakan 1 menikmati Sunrise, turun ke Laut Pasir, mendaki Kawah Bromo, foto di Pasir Berbisik & Bukit Teletubbies. Makan siang dan kembali ke kota asal."}]', '• Transportasi Elf Long AC PP
• Jeep Bromo 4x4 (5 spot utama)
• Tiket Masuk Taman Nasional TNBTS
• Sarapan & Makan Siang 1x
• Driver & Tour Leader', '• Sewa kuda di Laut Pasir Bromo
• Jaket & sarung tangan sewa
• Pengeluaran pribadi', 'Terminal Tirtonadi Solo (Pintu Timur)', '20:30:00', NULL, 1, '2026-10-30 12:49:09', '2026-10-30 12:49:09');
INSERT INTO `paket_wisata` (`id`, `nama_paket`, `destinasi`, `jenis_layanan`, `harga`, `durasi_hari`, `deskripsi`, `itinerary`, `fasilitas`, `fasilitas_tidak_termasuk`, `meeting_point`, `jam_kumpul`, `foto`, `status_aktif`, `created_at`, `updated_at`) VALUES (3, 'Labuan Bajo Sailing Komodo 3H2M', 'Labuan Bajo & Pulau Komodo', 'paket_reguler', 4800000, 3, 'Pengalaman liveaboard berlayar dengan kapal pinisi di perairan Taman Nasional Komodo, trekking Pulau Padar, snorkeling Pink Beach, dan melihat kelelawar di Pulau Kalong.', '[{"hari":1,"judul":"Kelor - Manjarite - Kalong","aktivitas":"Penjemputan di Bandara Komodo, check in kapal phinisi, trekking Pulau Kelor, snorkeling di Manjarite, sunset & kelelawar di Pulau Kalong."},{"hari":2,"judul":"Padar - Pink Beach - Komodo - Manta Point","aktivitas":"Sunrise trekking di puncak Pulau Padar, santai di Pink Beach, trekking ranger mencari komodo dragon, snorkeling berenang bersama manta ray."},{"hari":3,"judul":"Taka Makassar - Kanawa - Bandara","aktivitas":"Foto di pulau pasir timbul Taka Makassar, snorkeling terumbu karang Pulau Kanawa, kembali ke pelabuhan Labuan Bajo."}]', '• Liveaboard Kapal Phinisi AC Kabin Mewah 3H2M
• Makan 7x prasmanan chef di kapal
• Alat Snorkeling & Life Jacket lengkap
• Dokumentasi Drone, DSLR & Underwater Go-Pro
• Tour Guide berlisensi HPI', '• Tiket Pesawat ke/dari Labuan Bajo
• Tiket Masuk TN Komodo (KPA & Ranger fee)
• Pengeluaran pribadi & tip crew kapal', 'Bandara Internasional Komodo (LBJ)', '09:00:00', NULL, 1, '2026-10-30 12:49:09', '2026-10-30 12:49:09');
INSERT INTO `paket_wisata` (`id`, `nama_paket`, `destinasi`, `jenis_layanan`, `harga`, `durasi_hari`, `deskripsi`, `itinerary`, `fasilitas`, `fasilitas_tidak_termasuk`, `meeting_point`, `jam_kumpul`, `foto`, `status_aktif`, `created_at`, `updated_at`) VALUES (4, 'Raja Ampat Wayag & Pianemo Explorer 4H3M', 'Kepulauan Raja Ampat', 'open_trip', 7500000, 4, 'Jelajahi surga bawah laut dunia di Pianemo, Teluk Kabui, Pasir Timbul, Sawinggrai, dan gugusan karst ikonik Wayag yang memukau mata.', '[{"hari":1,"judul":"Sorong ke Waisai & Check in Resort","aktivitas":"Meeting point di Pelabuhan Sorong, naik fast ferry express Bahari ke Waisai, transfer resort tepi pantai."},{"hari":2,"judul":"Trekking Puncak Pianemo & Teluk Kabui","aktivitas":"Trekking tangga kayu ke viewpoint Pianemo, Teluk Bintang, snorkeling di Friwen Wall, dan foto di Batu Pensil."},{"hari":3,"judul":"Eksplorasi Gugusan Karst Wayag","aktivitas":"Speedboat menuju gugusan Wayag 1 & 2, memberi makan hiu karang (baby shark) di pos pantai Wayag."},{"hari":4,"judul":"Wisata Burung Cendrawasih & Kembali ke Sorong","aktivitas":"Pagi birdwatching Cendrawasih di Sawinggrai, kembali dengan ferry ke Sorong."}]', '• Resort Waterfront AC di Waisai 4H3M
• Speedboat private selama tur
• Tiket Kapal Cepat Sorong-Waisai PP VIP
• Makan lengkap selama tur 9x
• Guide lokal berpengalaman
• Dokumentasi underwater & aerial', '• Tiket Pesawat ke Sorong (SOQ)
• PIN Kartu Masuk Kawasan Konservasi Perairan (KKP) Raja Ampat
• Tipping guide', 'Pelabuhan Rakyat Kota Sorong', '08:00:00', NULL, 1, '2026-10-30 12:49:09', '2026-10-30 12:49:09');

-- Data for table `kendaraan` --
INSERT INTO `kendaraan` (`id`, `jenis_kendaraan`, `kapasitas`, `plat_nomor`, `status`, `created_at`, `updated_at`) VALUES (1, 'Toyota Hiace Premio Luxury', 14, 'AD 7123 XX', 'tersedia', '2026-10-30 12:49:09', '2026-10-30 12:49:09');
INSERT INTO `kendaraan` (`id`, `jenis_kendaraan`, `kapasitas`, `plat_nomor`, `status`, `created_at`, `updated_at`) VALUES (2, 'Toyota Innova Reborn', 7, 'AD 1982 AB', 'tersedia', '2026-10-30 12:49:09', '2026-10-30 12:49:09');
INSERT INTO `kendaraan` (`id`, `jenis_kendaraan`, `kapasitas`, `plat_nomor`, `status`, `created_at`, `updated_at`) VALUES (3, 'Isuzu Elf Long Giga', 19, 'AD 8821 ZZ', 'tersedia', '2026-10-30 12:49:09', '2026-10-30 12:49:09');
INSERT INTO `kendaraan` (`id`, `jenis_kendaraan`, `kapasitas`, `plat_nomor`, `status`, `created_at`, `updated_at`) VALUES (4, 'Medium Bus Pariwisata', 32, 'AD 9001 YY', 'tersedia', '2026-10-30 12:49:09', '2026-10-30 12:49:09');
INSERT INTO `kendaraan` (`id`, `jenis_kendaraan`, `kapasitas`, `plat_nomor`, `status`, `created_at`, `updated_at`) VALUES (5, 'Speedboat Komodo Cruiser 200PK', 12, NULL, 'tersedia', '2026-10-30 12:49:09', '2026-10-30 12:49:09');

-- Data for table `jadwal_keberangkatan` --
INSERT INTO `jadwal_keberangkatan` (`id`, `paket_wisata_id`, `kendaraan_id`, `tanggal_berangkat`, `kuota`, `sisa_kuota`, `min_peserta`, `biaya_operasional`, `status_keberangkatan`, `created_at`, `updated_at`) VALUES (1, 1, 4, '2026-11-06 00:00:00', 30, 28, 15, 45000000, 'dijadwalkan', '2026-10-30 12:49:09', '2026-10-30 12:49:09');
INSERT INTO `jadwal_keberangkatan` (`id`, `paket_wisata_id`, `kendaraan_id`, `tanggal_berangkat`, `kuota`, `sisa_kuota`, `min_peserta`, `biaya_operasional`, `status_keberangkatan`, `created_at`, `updated_at`) VALUES (2, 2, 1, '2026-11-02 00:00:00', 14, 4, 10, 11000000, 'dikonfirmasi', '2026-10-30 12:49:09', '2026-10-30 12:49:09');
INSERT INTO `jadwal_keberangkatan` (`id`, `paket_wisata_id`, `kendaraan_id`, `tanggal_berangkat`, `kuota`, `sisa_kuota`, `min_peserta`, `biaya_operasional`, `status_keberangkatan`, `created_at`, `updated_at`) VALUES (3, 3, 5, '2026-11-13 00:00:00', 12, 10, 6, 22000000, 'dijadwalkan', '2026-10-30 12:49:09', '2026-10-30 12:49:09');
INSERT INTO `jadwal_keberangkatan` (`id`, `paket_wisata_id`, `kendaraan_id`, `tanggal_berangkat`, `kuota`, `sisa_kuota`, `min_peserta`, `biaya_operasional`, `status_keberangkatan`, `created_at`, `updated_at`) VALUES (4, 4, NULL, '2026-11-19 00:00:00', 16, 16, 8, 48000000, 'dijadwalkan', '2026-10-30 12:49:09', '2026-10-30 12:49:09');
INSERT INTO `jadwal_keberangkatan` (`id`, `paket_wisata_id`, `kendaraan_id`, `tanggal_berangkat`, `kuota`, `sisa_kuota`, `min_peserta`, `biaya_operasional`, `status_keberangkatan`, `created_at`, `updated_at`) VALUES (5, 1, 4, '2026-11-06 00:00:00', 30, 28, 15, 45000000, 'dijadwalkan', '2026-10-30 12:49:27', '2026-10-30 12:49:27');
INSERT INTO `jadwal_keberangkatan` (`id`, `paket_wisata_id`, `kendaraan_id`, `tanggal_berangkat`, `kuota`, `sisa_kuota`, `min_peserta`, `biaya_operasional`, `status_keberangkatan`, `created_at`, `updated_at`) VALUES (6, 2, 1, '2026-11-02 00:00:00', 14, 4, 10, 11000000, 'dikonfirmasi', '2026-10-30 12:49:27', '2026-10-30 12:49:27');
INSERT INTO `jadwal_keberangkatan` (`id`, `paket_wisata_id`, `kendaraan_id`, `tanggal_berangkat`, `kuota`, `sisa_kuota`, `min_peserta`, `biaya_operasional`, `status_keberangkatan`, `created_at`, `updated_at`) VALUES (7, 3, 5, '2026-11-13 00:00:00', 12, 10, 6, 22000000, 'dijadwalkan', '2026-10-30 12:49:27', '2026-10-30 12:49:27');
INSERT INTO `jadwal_keberangkatan` (`id`, `paket_wisata_id`, `kendaraan_id`, `tanggal_berangkat`, `kuota`, `sisa_kuota`, `min_peserta`, `biaya_operasional`, `status_keberangkatan`, `created_at`, `updated_at`) VALUES (8, 4, NULL, '2026-11-19 00:00:00', 16, 16, 8, 48000000, 'dijadwalkan', '2026-10-30 12:49:27', '2026-10-30 12:49:27');

-- Data for table `custom_trip` --
INSERT INTO `custom_trip` (`id`, `user_id`, `kendaraan_id`, `tujuan`, `tanggal_diinginkan`, `durasi_hari`, `jumlah_peserta`, `kebutuhan`, `fasilitas_penawaran`, `jadwal_penawaran`, `biaya_penawaran`, `status`, `created_at`, `updated_at`) VALUES (1, 4, NULL, 'Dieng Plateau Family Gathering', '2026-11-24 00:00:00', 3, 12, 'Private tour reuni keluarga besar, butuh armada Hiace Premio ber-AC, penginapan villa privat 4 kamar di Dieng, kunjungan ke Kawah Sikidang, Telaga Warna, Candi Arjuna, dan berburu Golden Sunrise Sikunir. Mohon sertakan catering makanan halal 3x sehari.', NULL, NULL, NULL, 'diajukan', '2026-10-30 12:49:27', '2026-10-30 12:49:27');

-- Data for table `pemesanan` --
INSERT INTO `pemesanan` (`id`, `kode_pemesanan`, `user_id`, `jadwal_id`, `custom_trip_id`, `jumlah_peserta`, `total_biaya`, `batas_bayar`, `status`, `created_at`, `updated_at`) VALUES (1, 'PSN-1041', 3, 2, NULL, 2, 2500000, '2026-10-31 12:49:09', 'dikonfirmasi', '2026-10-30 12:49:09', '2026-10-30 12:49:09');
INSERT INTO `pemesanan` (`id`, `kode_pemesanan`, `user_id`, `jadwal_id`, `custom_trip_id`, `jumlah_peserta`, `total_biaya`, `batas_bayar`, `status`, `created_at`, `updated_at`) VALUES (2, 'PSN-1042', 2, 5, NULL, 2, 7000000, '2026-10-31 06:49:27', 'menunggu', '2026-10-30 12:49:27', '2026-10-30 12:49:27');

-- Data for table `peserta` --
INSERT INTO `peserta` (`id`, `pemesanan_id`, `nama_lengkap`, `jenis_identitas`, `no_identitas`, `file_identitas`, `no_hp`, `created_at`, `updated_at`) VALUES (1, 1, 'Budi Santoso', 'ktp', 3372011203900001, 'private/identitas/sample_ktp_budi.jpg', '081311223344', '2026-10-30 12:49:09', '2026-10-30 12:49:09');
INSERT INTO `peserta` (`id`, `pemesanan_id`, `nama_lengkap`, `jenis_identitas`, `no_identitas`, `file_identitas`, `no_hp`, `created_at`, `updated_at`) VALUES (2, 1, 'Dewi Anggraini', 'ktp', 3372015509920002, 'private/identitas/sample_ktp_dewi.jpg', '081311223345', '2026-10-30 12:49:09', '2026-10-30 12:49:09');
INSERT INTO `peserta` (`id`, `pemesanan_id`, `nama_lengkap`, `jenis_identitas`, `no_identitas`, `file_identitas`, `no_hp`, `created_at`, `updated_at`) VALUES (3, 2, 'Rina Wijaya', 'ktp', 3371024508930005, 'private/identitas/sample_ktp_rina.jpg', '081298765432', '2026-10-30 12:49:27', '2026-10-30 12:49:27');
INSERT INTO `peserta` (`id`, `pemesanan_id`, `nama_lengkap`, `jenis_identitas`, `no_identitas`, `file_identitas`, `no_hp`, `created_at`, `updated_at`) VALUES (4, 2, 'Doni Kusuma', 'ktp', 3371021204910003, 'private/identitas/sample_ktp_doni.jpg', '081298765433', '2026-10-30 12:49:27', '2026-10-30 12:49:27');

-- Data for table `pembayaran` --
INSERT INTO `pembayaran` (`id`, `pemesanan_id`, `nominal`, `tanggal_bayar`, `bukti_transfer`, `status`, `catatan_admin`, `diverifikasi_pada`, `diverifikasi_oleh`, `created_at`, `updated_at`) VALUES (1, 1, 2500000, '2026-10-29 00:00:00', 'private/bukti_transfer/sample_transfer_budi.jpg', 'diverifikasi', NULL, '2026-10-30 00:49:09', 1, '2026-10-30 12:49:09', '2026-10-30 12:49:09');
INSERT INTO `pembayaran` (`id`, `pemesanan_id`, `nominal`, `tanggal_bayar`, `bukti_transfer`, `status`, `catatan_admin`, `diverifikasi_pada`, `diverifikasi_oleh`, `created_at`, `updated_at`) VALUES (2, 2, 7000000, '2026-10-30 00:00:00', 'private/bukti_transfer/sample_transfer_rina.jpg', 'menunggu', NULL, NULL, NULL, '2026-10-30 12:49:27', '2026-10-30 12:49:27');

-- Data for table `tiket` --
INSERT INTO `tiket` (`id`, `pemesanan_id`, `peserta_id`, `kode_tiket`, `qr_code`, `tanggal_terbit`, `status`, `created_at`, `updated_at`) VALUES (1, 1, 1, 'JN-PSN1041-01', '<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="200" height="200" viewBox="0 0 200 200"><rect x="0" y="0" width="200" height="200" fill="#ffffff"/><g transform="scale(6.897)"><g transform="translate(0,0)"><path fill-rule="evenodd" d="M9 0L9 1L8 1L8 2L9 2L9 3L8 3L8 4L10 4L10 7L11 7L11 4L12 4L12 5L13 5L13 4L14 4L14 5L15 5L15 6L14 6L14 8L13 8L13 6L12 6L12 9L13 9L13 10L11 10L11 11L10 11L10 9L11 9L11 8L6 8L6 9L9 9L9 14L10 14L10 15L11 15L11 13L12 13L12 14L13 14L13 13L14 13L14 14L15 14L15 15L19 15L19 14L20 14L20 16L21 16L21 14L22 14L22 16L23 16L23 15L24 15L24 16L25 16L25 13L26 13L26 15L27 15L27 16L26 16L26 17L22 17L22 18L21 18L21 17L18 17L18 16L15 16L15 17L14 17L14 16L13 16L13 15L12 15L12 17L14 17L14 18L15 18L15 19L14 19L14 20L15 20L15 22L14 22L14 21L12 21L12 19L13 19L13 18L11 18L11 16L10 16L10 17L9 17L9 15L7 15L7 14L8 14L8 12L6 12L6 11L8 11L8 10L6 10L6 11L3 11L3 12L6 12L6 13L7 13L7 14L6 14L6 15L7 15L7 16L6 16L6 17L7 17L7 18L6 18L6 19L7 19L7 18L8 18L8 20L6 20L6 21L8 21L8 23L9 23L9 24L8 24L8 29L10 29L10 28L11 28L11 29L12 29L12 27L13 27L13 26L11 26L11 25L10 25L10 24L11 24L11 23L9 23L9 21L8 21L8 20L9 20L9 19L10 19L10 22L11 22L11 21L12 21L12 22L13 22L13 23L12 23L12 25L13 25L13 24L14 24L14 25L15 25L15 24L17 24L17 23L18 23L18 22L19 22L19 24L20 24L20 25L17 25L17 27L16 27L16 26L15 26L15 27L14 27L14 28L15 28L15 27L16 27L16 28L17 28L17 27L18 27L18 28L19 28L19 27L20 27L20 28L21 28L21 29L22 29L22 28L21 28L21 25L25 25L25 26L22 26L22 27L23 27L23 29L24 29L24 27L25 27L25 28L26 28L26 29L27 29L27 28L28 28L28 26L29 26L29 24L26 24L26 23L27 23L27 22L29 22L29 20L28 20L28 19L27 19L27 16L28 16L28 15L27 15L27 12L28 12L28 11L27 11L27 12L25 12L25 13L21 13L21 12L22 12L22 11L23 11L23 12L24 12L24 11L25 11L25 9L26 9L26 8L25 8L25 9L24 9L24 8L23 8L23 9L22 9L22 8L21 8L21 9L20 9L20 8L19 8L19 9L18 9L18 8L16 8L16 7L17 7L17 6L18 6L18 7L19 7L19 5L20 5L20 7L21 7L21 4L18 4L18 3L16 3L16 1L15 1L15 0L14 0L14 2L15 2L15 4L14 4L14 3L13 3L13 4L12 4L12 2L13 2L13 1L11 1L11 2L10 2L10 0ZM17 0L17 1L18 1L18 2L19 2L19 3L21 3L21 2L19 2L19 1L21 1L21 0ZM10 3L10 4L11 4L11 3ZM15 4L15 5L17 5L17 4ZM8 5L8 7L9 7L9 5ZM15 6L15 7L16 7L16 6ZM0 8L0 9L2 9L2 10L3 10L3 9L4 9L4 10L5 10L5 8ZM14 8L14 10L13 10L13 11L12 11L12 13L13 13L13 12L14 12L14 13L17 13L17 14L19 14L19 13L20 13L20 12L21 12L21 10L19 10L19 11L18 11L18 9L15 9L15 8ZM27 8L27 9L28 9L28 10L29 10L29 9L28 9L28 8ZM14 10L14 12L15 12L15 11L16 11L16 12L17 12L17 13L18 13L18 12L17 12L17 11L16 11L16 10ZM23 10L23 11L24 11L24 10ZM19 11L19 12L20 12L20 11ZM0 12L0 13L2 13L2 14L4 14L4 15L5 15L5 13L2 13L2 12ZM10 12L10 13L11 13L11 12ZM28 13L28 14L29 14L29 13ZM0 15L0 21L1 21L1 17L2 17L2 16L1 16L1 15ZM7 16L7 17L8 17L8 16ZM3 17L3 18L2 18L2 21L3 21L3 20L4 20L4 21L5 21L5 19L4 19L4 18L5 18L5 17ZM28 17L28 18L29 18L29 17ZM10 18L10 19L11 19L11 18ZM16 18L16 20L19 20L19 21L20 21L20 20L19 20L19 19L21 19L21 20L22 20L22 19L23 19L23 20L24 20L24 19L25 19L25 21L26 21L26 20L27 20L27 19L25 19L25 18L24 18L24 19L23 19L23 18L22 18L22 19L21 19L21 18L19 18L19 19L18 19L18 18ZM21 21L21 24L24 24L24 21ZM22 22L22 23L23 23L23 22ZM14 23L14 24L15 24L15 23ZM25 24L25 25L26 25L26 24ZM9 26L9 28L10 28L10 27L11 27L11 26ZM25 26L25 27L26 27L26 28L27 28L27 26ZM0 0L0 7L7 7L7 0ZM1 1L1 6L6 6L6 1ZM2 2L2 5L5 5L5 2ZM22 0L22 7L29 7L29 0ZM23 1L23 6L28 6L28 1ZM24 2L24 5L27 5L27 2ZM0 22L0 29L7 29L7 22ZM1 23L1 28L6 28L6 23ZM2 24L2 27L5 27L5 24Z" fill="#000000"/></g></g></svg>
', '2026-10-30 12:49:27', 'aktif', '2026-10-30 12:49:27', '2026-10-30 12:49:27');
INSERT INTO `tiket` (`id`, `pemesanan_id`, `peserta_id`, `kode_tiket`, `qr_code`, `tanggal_terbit`, `status`, `created_at`, `updated_at`) VALUES (2, 1, 2, 'JN-PSN1041-02', '<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="200" height="200" viewBox="0 0 200 200"><rect x="0" y="0" width="200" height="200" fill="#ffffff"/><g transform="scale(6.897)"><g transform="translate(0,0)"><path fill-rule="evenodd" d="M10 0L10 1L8 1L8 3L9 3L9 4L8 4L8 8L6 8L6 9L7 9L7 10L5 10L5 12L4 12L4 11L3 11L3 9L4 9L4 8L3 8L3 9L2 9L2 8L0 8L0 9L2 9L2 12L3 12L3 14L4 14L4 15L3 15L3 17L2 17L2 20L3 20L3 21L5 21L5 20L3 20L3 19L7 19L7 20L6 20L6 21L7 21L7 20L8 20L8 19L9 19L9 21L8 21L8 23L9 23L9 21L10 21L10 26L9 26L9 25L8 25L8 26L9 26L9 27L8 27L8 29L9 29L9 27L10 27L10 26L12 26L12 28L11 28L11 29L12 29L12 28L13 28L13 29L16 29L16 28L18 28L18 27L19 27L19 29L21 29L21 27L22 27L22 26L24 26L24 28L23 28L23 29L24 29L24 28L25 28L25 29L26 29L26 28L25 28L25 26L28 26L28 27L27 27L27 29L28 29L28 27L29 27L29 26L28 26L28 25L25 25L25 20L24 20L24 17L22 17L22 16L24 16L24 15L25 15L25 17L26 17L26 15L27 15L27 17L28 17L28 18L27 18L27 20L26 20L26 22L27 22L27 23L26 23L26 24L27 24L27 23L28 23L28 22L29 22L29 21L27 21L27 20L29 20L29 17L28 17L28 15L29 15L29 14L27 14L27 13L29 13L29 12L28 12L28 10L29 10L29 9L28 9L28 8L26 8L26 9L25 9L25 8L22 8L22 9L20 9L20 8L19 8L19 6L20 6L20 7L21 7L21 6L20 6L20 5L19 5L19 4L20 4L20 3L21 3L21 2L19 2L19 3L18 3L18 1L20 1L20 0L15 0L15 1L14 1L14 2L16 2L16 3L15 3L15 5L14 5L14 4L13 4L13 3L12 3L12 7L11 7L11 4L10 4L10 3L9 3L9 2L11 2L11 0ZM17 3L17 4L16 4L16 5L17 5L17 4L18 4L18 3ZM9 5L9 8L8 8L8 9L10 9L10 10L9 10L9 11L6 11L6 12L5 12L5 15L4 15L4 18L7 18L7 19L8 19L8 18L10 18L10 17L9 17L9 16L8 16L8 15L9 15L9 14L11 14L11 13L10 13L10 12L9 12L9 11L11 11L11 8L10 8L10 5ZM18 5L18 6L17 6L17 7L18 7L18 6L19 6L19 5ZM13 6L13 7L12 7L12 8L14 8L14 9L13 9L13 10L14 10L14 12L16 12L16 11L17 11L17 12L18 12L18 13L21 13L21 12L20 12L20 11L19 11L19 12L18 12L18 10L20 10L20 9L19 9L19 8L14 8L14 6ZM15 6L15 7L16 7L16 6ZM17 9L17 10L18 10L18 9ZM23 9L23 11L22 11L22 10L21 10L21 11L22 11L22 12L23 12L23 13L22 13L22 15L21 15L21 16L22 16L22 15L24 15L24 14L25 14L25 15L26 15L26 13L27 13L27 12L26 12L26 13L25 13L25 12L23 12L23 11L24 11L24 10L25 10L25 9ZM26 9L26 10L28 10L28 9ZM0 12L0 13L1 13L1 12ZM6 12L6 13L7 13L7 14L6 14L6 15L7 15L7 14L9 14L9 13L8 13L8 12ZM12 12L12 16L11 16L11 18L12 18L12 16L13 16L13 17L14 17L14 18L15 18L15 19L13 19L13 20L12 20L12 19L11 19L11 21L12 21L12 22L11 22L11 25L12 25L12 24L13 24L13 27L15 27L15 28L16 28L16 25L15 25L15 24L14 24L14 23L16 23L16 22L15 22L15 21L17 21L17 22L18 22L18 23L17 23L17 27L18 27L18 26L19 26L19 25L20 25L20 20L19 20L19 21L18 21L18 20L15 20L15 19L16 19L16 18L15 18L15 17L16 17L16 16L17 16L17 15L18 15L18 14L17 14L17 13L16 13L16 14L14 14L14 13L13 13L13 12ZM1 14L1 15L0 15L0 17L1 17L1 15L2 15L2 14ZM19 14L19 15L20 15L20 14ZM13 15L13 16L14 16L14 17L15 17L15 16L14 16L14 15ZM5 16L5 17L8 17L8 16ZM18 16L18 18L17 18L17 19L18 19L18 18L19 18L19 19L20 19L20 18L21 18L21 20L23 20L23 19L22 19L22 17L19 17L19 16ZM0 18L0 19L1 19L1 18ZM25 18L25 19L26 19L26 18ZM0 20L0 21L1 21L1 20ZM21 21L21 24L24 24L24 21ZM22 22L22 23L23 23L23 22ZM18 23L18 24L19 24L19 23ZM21 25L21 26L22 26L22 25ZM0 0L0 7L7 7L7 0ZM1 1L1 6L6 6L6 1ZM2 2L2 5L5 5L5 2ZM22 0L22 7L29 7L29 0ZM23 1L23 6L28 6L28 1ZM24 2L24 5L27 5L27 2ZM0 22L0 29L7 29L7 22ZM1 23L1 28L6 28L6 23ZM2 24L2 27L5 27L5 24Z" fill="#000000"/></g></g></svg>
', '2026-10-30 12:49:27', 'aktif', '2026-10-30 12:49:27', '2026-10-30 12:49:27');

-- Data for table `pengaturan` --
INSERT INTO `pengaturan` (`id`, `nama_bank`, `no_rekening`, `atas_nama`, `batas_bayar_jam`, `created_at`, `updated_at`) VALUES (1, 'Bank Mandiri', '138-00-8899221-1', 'PT Jelajah Nusa Tour & Travel', 24, '2026-10-30 12:49:06', '2026-10-30 12:49:06');

-- Data for table `log_aktivitas` --
INSERT INTO `log_aktivitas` (`id`, `user_id`, `subjek_tipe`, `subjek_id`, `aksi`, `keterangan`, `ip_address`, `created_at`, `updated_at`) VALUES (1, 1, 'Sistem', NULL, 'seeder_database', 'Inisialisasi database master data Jelajah Nusa berhasil dijalankan.', '127.0.0.1', '2026-10-30 12:49:27', '2026-10-30 12:49:27');

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
