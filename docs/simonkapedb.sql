-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 28, 2025 at 09:50 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `simonkapedb`
--

-- --------------------------------------------------------

--
-- Table structure for table `absen_harian`
--

CREATE TABLE `absen_harian` (
  `id` int(11) NOT NULL,
  `mahasiswa_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu_absen` time NOT NULL,
  `status_kehadiran` enum('Hadir','Izin','Sakit','Alpha') DEFAULT 'Hadir'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absen_harian`
--

INSERT INTO `absen_harian` (`id`, `mahasiswa_id`, `tanggal`, `waktu_absen`, `status_kehadiran`) VALUES
(3, 9, '2025-06-28', '20:01:18', 'Alpha'),
(4, 9, '2025-06-27', '00:00:00', 'Alpha');

-- --------------------------------------------------------

--
-- Table structure for table `dosen`
--

CREATE TABLE `dosen` (
  `id` int(11) NOT NULL,
  `nidn` varchar(20) NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `status_aktif` enum('Aktif','Tidak Aktif') DEFAULT 'Aktif',
  `email` varchar(255) NOT NULL,
  `nomor_telepon` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dosen`
--

INSERT INTO `dosen` (`id`, `nidn`, `nama_lengkap`, `status_aktif`, `email`, `nomor_telepon`, `created_at`) VALUES
(7, '0921128902', 'Nalis Hendrawan, S.T., M.T.', 'Aktif', 'nalis@unidayan.ac.id', '082187292905', '2025-06-28 11:51:45'),
(8, '0913049103', 'Wa Ode Rahma Agus Udaya Manarfa., S.T., M.Kom.', 'Aktif', 'wd.rahma.a.u.m@unidayan.ac.id', '085156571662', '2025-06-28 11:52:58'),
(9, '0910096701', 'FITHRIAH MUSDAT.S.Si., M.T.', 'Aktif', 'fith@unidayan.ac.id', '08114004813', '2025-06-28 11:54:57');

-- --------------------------------------------------------

--
-- Table structure for table `instansi`
--

CREATE TABLE `instansi` (
  `id` int(11) NOT NULL,
  `nama_instansi` varchar(255) NOT NULL,
  `bidang_kerja` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `kota_kab` varchar(100) DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `instansi`
--

INSERT INTO `instansi` (`id`, `nama_instansi`, `bidang_kerja`, `alamat`, `kota_kab`, `telepon`, `email`, `pic`, `created_at`) VALUES
(4, 'Laboratorium Teknik Informatika', 'Anak Magang', 'Jl. Dayanu Ikhsanuddin', 'Baubau', '082255446699', 'labunidayan@unidayan.ac.id', '', '2025-06-28 11:57:13'),
(5, 'Dinas Sosial', 'Copy Writer', 'Jl. Dayanu Ikhsanuddin', 'Baubau', '082250578899', 'dinassosial@gmail.com', '', '2025-06-28 11:58:15');

-- --------------------------------------------------------

--
-- Table structure for table `laporan_mingguan`
--

CREATE TABLE `laporan_mingguan` (
  `id` int(11) NOT NULL,
  `mahasiswa_id` int(11) NOT NULL,
  `periode_mingguan` varchar(100) NOT NULL,
  `file_laporan` varchar(255) DEFAULT NULL,
  `status_laporan` enum('Belum Dibuat','Menunggu Persetujuan','Disetujui','Ditolak','Revisi') DEFAULT 'Belum Dibuat',
  `feedback_dosen` text DEFAULT NULL,
  `dosen_pembimbing_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan_mingguan`
--

INSERT INTO `laporan_mingguan` (`id`, `mahasiswa_id`, `periode_mingguan`, `file_laporan`, `status_laporan`, `feedback_dosen`, `dosen_pembimbing_id`, `created_at`, `updated_at`) VALUES
(1, 9, '23 Jun - 27 Jun 2025', '/public/uploads/laporan_mingguan/laporan_mingguan_9_23_Jun_-_27_Jun_2025.txt', 'Menunggu Persetujuan', NULL, 7, '2025-06-28 14:33:20', '2025-06-28 14:33:20');

-- --------------------------------------------------------

--
-- Table structure for table `logbook_harian`
--

CREATE TABLE `logbook_harian` (
  `id` int(11) NOT NULL,
  `mahasiswa_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `uraian_kegiatan` text NOT NULL,
  `dokumentasi` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logbook_harian`
--

INSERT INTO `logbook_harian` (`id`, `mahasiswa_id`, `tanggal`, `uraian_kegiatan`, `dokumentasi`, `created_at`) VALUES
(1, 9, '2025-06-28', 'hari ini sedang melakukan bersih bersih', '/uploads/logbook/logbook_685fda66b64a0.jpeg', '2025-06-28 12:04:54'),
(2, 9, '2025-06-27', 'melakukan perbaikan laptop', '/uploads/logbook/logbook_685ffa1d59f39.jpeg', '2025-06-28 14:20:13'),
(3, 9, '2025-06-26', 'memperbaiki lampu', '/uploads/logbook/logbook_685ffa57afed0.jpeg', '2025-06-28 14:21:11'),
(5, 9, '2025-06-25', 'bersih bersih karpet lab', '/uploads/logbook/logbook_685ffb42657b8.jpeg', '2025-06-28 14:25:06'),
(7, 9, '2025-06-24', 'sdfghjkl', '/uploads/logbook/logbook_685ffcbc1c599.jpeg', '2025-06-28 14:31:24'),
(8, 9, '2025-06-23', 'nonton seminar', '/uploads/logbook/logbook_685ffd1f8ff4d.jpeg', '2025-06-28 14:33:03');

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id` int(11) NOT NULL,
  `nim` varchar(20) NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `program_studi` varchar(100) NOT NULL,
  `instansi_id` int(11) DEFAULT NULL,
  `dosen_pembimbing_id` int(11) DEFAULT NULL,
  `status_kp` varchar(50) DEFAULT 'Belum Terdaftar',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`id`, `nim`, `nama_lengkap`, `program_studi`, `instansi_id`, `dosen_pembimbing_id`, `status_kp`, `created_at`) VALUES
(7, '2160125', 'Farhan Hidayat', 'Teknik Informatika', 5, 7, 'Sedang KP', '2025-06-24 07:24:44'),
(8, '21650122', 'La Ode Muhammad Adam Safitra', 'Teknik Informatika', NULL, NULL, 'Terdaftar', '2025-06-24 07:25:41'),
(9, '21650098', 'Iksan', 'Teknik Informatika', 5, 7, 'Sedang KP', '2025-06-24 07:26:18'),
(10, '21650021', 'Musriddin', 'Teknik Informatika', NULL, NULL, 'Terdaftar', '2025-06-24 07:27:58'),
(11, '21650063', 'Ruslan Mansyur', 'Teknik Informatika', NULL, NULL, '', '2025-06-24 08:35:52'),
(12, '21650207', 'Jasmin Kaspaola', 'Teknik Informatika', NULL, NULL, '', '2025-06-24 08:37:27'),
(13, '21650050', 'Murziki', 'Teknik Informatika', NULL, NULL, '', '2025-06-24 08:47:06'),
(14, '21650092', 'Abdul Faroq Arsa', 'Teknik Informatika', NULL, NULL, '', '2025-06-24 08:48:01'),
(15, '21650064', 'Abdul Aziz', 'Teknik Informatika', NULL, NULL, '', '2025-06-24 08:48:51');

-- --------------------------------------------------------

--
-- Table structure for table `penempatan_kp`
--

CREATE TABLE `penempatan_kp` (
  `id` int(11) NOT NULL,
  `instansi_id` int(11) NOT NULL,
  `dosen_pembimbing_id` int(11) NOT NULL,
  `nama_kelompok` varchar(255) DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penempatan_kp`
--

INSERT INTO `penempatan_kp` (`id`, `instansi_id`, `dosen_pembimbing_id`, `nama_kelompok`, `tanggal_mulai`, `tanggal_selesai`, `created_at`, `updated_at`) VALUES
(4, 5, 7, 'Kelompok Gacor', '2025-06-28', '2025-08-28', '2025-06-28 11:59:36', '2025-06-28 11:59:36');

-- --------------------------------------------------------

--
-- Table structure for table `penempatan_kp_mahasiswa`
--

CREATE TABLE `penempatan_kp_mahasiswa` (
  `penempatan_kp_id` int(11) NOT NULL,
  `mahasiswa_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penempatan_kp_mahasiswa`
--

INSERT INTO `penempatan_kp_mahasiswa` (`penempatan_kp_id`, `mahasiswa_id`) VALUES
(4, 7),
(4, 9);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','mahasiswa','dosen') NOT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `user_id`) VALUES
(1, 'adminprodi', '$2y$10$ePJ6955SWJGP0XZ.u9HDSuh5XVkEy2/SSv41v5Fl6gwVcTw08FKO.', 'admin', NULL),
(16, '2160125', '$2y$10$utVB7VKPHq/UXECciPHqUO4Y6OQAI/5fR9qsohnT3M86Wr.9R30qG', 'mahasiswa', 7),
(17, '21650122', '$2y$10$c/KyQ/VLwNIdW8LK33dlWeuRTmGp9xXnZDOszf9SSGii1djMS6Yn.', 'mahasiswa', 8),
(18, '21650098', '$2y$10$8GOXnnvFi2vHyScaj9yXjObLQlbiEtDn5rGOcBL9G1fo4ki3hXYRG', 'mahasiswa', 9),
(19, '21650021', '$2y$10$dgfu6mqzbfAqw5sjtzV58er4.exeWYLyJLdaxkubqk28WhcwEwU4K', 'mahasiswa', 10),
(20, '21650063', '$2y$10$2790EkgKpFTScWKPcGU6Dex8ynQGAPZr/.1kPkJWzZ7yS60Se.ARC', 'mahasiswa', 11),
(21, '21650207', '$2y$10$ljPmLbcvxLQc8CkPthE11eIBIsZBgP.VKfzZks.F9LfLEGcyVbZbW', 'mahasiswa', 12),
(22, '21650050', '$2y$10$QMNovv/pCUD/ikE8aWO29.eMBa.BESPq7hH0Cp7qkBfsxF2cCOGtW', 'mahasiswa', 13),
(23, '21650092', '$2y$10$zW5Jaddoq36fM2Ock8o0LOwnlk/4wrXZtiwIbwegyB40PKnLgx/Bq', 'mahasiswa', 14),
(24, '21650064', '$2y$10$AmEKa9SrJFPROxf1JtHuAeKA.nv07c5T0Y/UZZQaD6V8GJziB7qD6', 'mahasiswa', 15),
(25, '0921128902', '$2y$10$fE7v1LRMPOF5skPLaWzkT.1Hlg/GKQT4K7a4BqS4jVFT1SNuL7JnG', 'dosen', 7),
(26, '0913049103', '$2y$10$/vUWz0ixJaSHADb9sqjzoeg8rBe/yWOVuTNpi9HgBRDSW5dsZNIBe', 'dosen', 8),
(27, '0910096701', '$2y$10$0nM/jVXHnQVyJ4rwM.VbOu8EIHeB7A4EU8fvseIAR/28h6esskKS2', 'dosen', 9);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absen_harian`
--
ALTER TABLE `absen_harian`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mahasiswa_id` (`mahasiswa_id`,`tanggal`);

--
-- Indexes for table `dosen`
--
ALTER TABLE `dosen`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nidn` (`nidn`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `instansi`
--
ALTER TABLE `instansi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_instansi` (`nama_instansi`);

--
-- Indexes for table `laporan_mingguan`
--
ALTER TABLE `laporan_mingguan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_laporan_mahasiswa` (`mahasiswa_id`),
  ADD KEY `fk_laporan_dosen` (`dosen_pembimbing_id`);

--
-- Indexes for table `logbook_harian`
--
ALTER TABLE `logbook_harian`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mahasiswa_id` (`mahasiswa_id`,`tanggal`);

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nim` (`nim`);

--
-- Indexes for table `penempatan_kp`
--
ALTER TABLE `penempatan_kp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_penempatan_instansi` (`instansi_id`),
  ADD KEY `fk_penempatan_dosen` (`dosen_pembimbing_id`);

--
-- Indexes for table `penempatan_kp_mahasiswa`
--
ALTER TABLE `penempatan_kp_mahasiswa`
  ADD PRIMARY KEY (`penempatan_kp_id`,`mahasiswa_id`),
  ADD KEY `fk_pkm_mahasiswa` (`mahasiswa_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absen_harian`
--
ALTER TABLE `absen_harian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `dosen`
--
ALTER TABLE `dosen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `instansi`
--
ALTER TABLE `instansi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `laporan_mingguan`
--
ALTER TABLE `laporan_mingguan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `logbook_harian`
--
ALTER TABLE `logbook_harian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `penempatan_kp`
--
ALTER TABLE `penempatan_kp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absen_harian`
--
ALTER TABLE `absen_harian`
  ADD CONSTRAINT `fk_absen_mahasiswa` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `laporan_mingguan`
--
ALTER TABLE `laporan_mingguan`
  ADD CONSTRAINT `fk_laporan_dosen` FOREIGN KEY (`dosen_pembimbing_id`) REFERENCES `dosen` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_laporan_mahasiswa` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `logbook_harian`
--
ALTER TABLE `logbook_harian`
  ADD CONSTRAINT `fk_logbook_mahasiswa` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `penempatan_kp`
--
ALTER TABLE `penempatan_kp`
  ADD CONSTRAINT `fk_penempatan_dosen` FOREIGN KEY (`dosen_pembimbing_id`) REFERENCES `dosen` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_penempatan_instansi` FOREIGN KEY (`instansi_id`) REFERENCES `instansi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `penempatan_kp_mahasiswa`
--
ALTER TABLE `penempatan_kp_mahasiswa`
  ADD CONSTRAINT `fk_pkm_mahasiswa` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pkm_penempatan` FOREIGN KEY (`penempatan_kp_id`) REFERENCES `penempatan_kp` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
