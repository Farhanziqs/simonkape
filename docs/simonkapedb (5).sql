-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 01, 2025 at 08:59 AM
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
(15, '724027801', 'MOH ARIF SURYAWAN, S.Kom., M.T., MTA', 'Aktif', 'dosen.arwan97@email.com', '', '2025-07-01 05:11:02'),
(16, '915058201', 'NALDY NIRMANTO TJONDRONEGORO, S.Kom., M.T.', 'Aktif', 'dosen.naldy@email.com', '', '2025-07-01 05:11:02'),
(17, '913098203', 'ERY MUCHYAR HASIRI, S.Kom., M.T., MTA', 'Aktif', 'dosen.ery@email.com', '', '2025-07-01 05:11:03'),
(18, '922058101', 'LA RAUFUN, S.T., M.T', 'Aktif', 'dosen.raufun@email.com', '', '2025-07-01 05:11:03'),
(19, '906118502', 'AZLIN, S.Kom., M.T.', 'Aktif', 'dosen.azlin@email.com', '', '2025-07-01 05:11:03'),
(20, '505078501', 'LM. FAJAR ISRAWAN, S.Kom., M.Kom., M.M., MTA', 'Aktif', 'dosen.fajar@email.com', '', '2025-07-01 05:11:03'),
(21, '910096701', 'ASNIATI, S.T., M.T., MTA.', 'Aktif', 'dosen.as@email.com', '', '2025-07-01 05:11:04'),
(22, '911047304', 'Dr. Ir. MUHAMAD IRADAT ACHMAD, S.T., M.T.', 'Aktif', 'dosen.iradat@email.com', '', '2025-07-01 05:11:04'),
(23, '920118301', 'MUHAMMAD MUKMIN, S.Kom., M.T.', 'Aktif', 'dosen.mukmin@email.com', '', '2025-07-01 05:11:04'),
(24, '917018602', 'HENNY HAMSINAR, S.Kom., M.T., M.M., MTA', 'Aktif', 'dosen.henny@email.com', '', '2025-07-01 05:11:04'),
(25, '919058001', 'JABAL NUR, S.Kom., M.T., MTA.', 'Aktif', 'dosen.jabal@email.com', '', '2025-07-01 05:11:04'),
(26, '930058705', 'FITHRIAH MUSADAT, S.Si., M.T., MTA.', 'Aktif', 'dosen.fith@email.com', '', '2025-07-01 05:11:05'),
(27, '909028703', 'ARIF SYAM, M.Kom.', 'Aktif', 'dosen.arif@email.com', '', '2025-07-01 05:11:05'),
(28, '910038203', 'Ir. LA ATINA, S.T., M.T.', 'Aktif', 'dosen.atina@email.com', '', '2025-07-01 05:11:05'),
(29, '910068901', 'SULTAN HADY, S.T., M.T.', 'Aktif', 'dosen.sultan@email.com', '', '2025-07-01 05:11:05'),
(30, '912126101', 'Ir. CHRISTOPOL EDDY, M.Eng', 'Aktif', 'dosen.kris@email.com', '', '2025-07-01 05:11:05'),
(31, '914047306', 'KH. ABDUL RASYID SABIRIN, Lc., MA.', 'Aktif', 'dosen.rasyid@email.com', '', '2025-07-01 05:11:06'),
(32, '918088903', 'HELSON HAMID, S.T., M.T.', 'Aktif', 'dosen.helson@email.com', '', '2025-07-01 05:11:06');

-- --------------------------------------------------------

--
-- Table structure for table `instansi`
--

CREATE TABLE `instansi` (
  `id` int(11) NOT NULL,
  `nama_instansi` varchar(255) NOT NULL,
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

INSERT INTO `instansi` (`id`, `nama_instansi`, `alamat`, `kota_kab`, `telepon`, `email`, `pic`, `created_at`) VALUES
(7, 'Dinas Kominfo', 'Jl. Palatiga', 'Kota Baubau', '086699332255', 'kominfo@ac.id', '', '2025-06-30 19:56:27'),
(8, 'Dinas Sosial', 'Jl. Dayanu Ikhsanuddin', 'Baubau', '082250571800', 'dinassosial@ac.id', '', '2025-06-30 19:57:19'),
(9, 'PT. Pertamina (Persero) Terminal BBM Baubau', 'Jl. Topa', 'Baubau', '082250571800', 'Pertamina@gmail.com', '', '2025-06-30 19:59:41');

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

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id` int(11) NOT NULL,
  `nim` varchar(20) NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `program_studi` varchar(100) NOT NULL,
  `nomor_telepon` varchar(20) DEFAULT NULL,
  `instansi_id` int(11) DEFAULT NULL,
  `dosen_pembimbing_id` int(11) DEFAULT NULL,
  `status_kp` varchar(50) DEFAULT 'Belum Terdaftar',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `penempatan_kp_mahasiswa`
--

CREATE TABLE `penempatan_kp_mahasiswa` (
  `penempatan_kp_id` int(11) NOT NULL,
  `mahasiswa_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(81, 'adminprodi', '$2y$10$ePJ6955SWJGP0XZ.u9HDSuh5XVkEy2/SSv41v5Fl6gwVcTw08FKO.', 'admin', 0);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `dosen`
--
ALTER TABLE `dosen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `instansi`
--
ALTER TABLE `instansi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `laporan_mingguan`
--
ALTER TABLE `laporan_mingguan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `logbook_harian`
--
ALTER TABLE `logbook_harian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `penempatan_kp`
--
ALTER TABLE `penempatan_kp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

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
