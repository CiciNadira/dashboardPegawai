-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 23, 2025 at 12:25 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bps_kepegawaian`
--

-- --------------------------------------------------------

--
-- Table structure for table `data_fungsional`
--

CREATE TABLE `data_fungsional` (
  `id` int NOT NULL,
  `pegawai_id` int NOT NULL,
  `tmt_fungsional` date DEFAULT NULL,
  `ak_terakhir_angka` decimal(10,3) DEFAULT NULL,
  `ak_terakhir_tahun` year DEFAULT NULL,
  `ak_konversi_angka` decimal(10,3) DEFAULT NULL,
  `ak_konversi_tahun` year DEFAULT NULL,
  `keterangan` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `data_keuangan`
--

CREATE TABLE `data_keuangan` (
  `id` int NOT NULL,
  `jenis_aplikasi` enum('Spider','BOS','Sakti','OMSPAN','SIRUP') NOT NULL,
  `tanggal_kegiatan` date DEFAULT NULL,
  `uraian_kegiatan` varchar(255) DEFAULT NULL,
  `status` enum('Belum','Proses','Selesai') DEFAULT 'Proses',
  `bukti_file` varchar(255) DEFAULT NULL,
  `keterangan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `data_kgb`
--

CREATE TABLE `data_kgb` (
  `id` int NOT NULL,
  `pegawai_id` int NOT NULL,
  `mkg` varchar(50) DEFAULT NULL,
  `kgb_terakhir` date DEFAULT NULL,
  `kgb_yad` date DEFAULT NULL,
  `keterangan` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `data_kp`
--

CREATE TABLE `data_kp` (
  `id` int NOT NULL,
  `pegawai_id` int NOT NULL,
  `kp_terakhir` date DEFAULT NULL,
  `kp_yad` date DEFAULT NULL,
  `keterangan` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `data_laporan`
--

CREATE TABLE `data_laporan` (
  `id` int NOT NULL,
  `kategori` enum('Keuangan','Kepegawaian','Lakin','BMN') NOT NULL,
  `judul_laporan` varchar(200) DEFAULT NULL,
  `periode_bulan` varchar(50) DEFAULT NULL,
  `nama_file` varchar(255) DEFAULT NULL,
  `tanggal_upload` date DEFAULT (curdate()),
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `data_sakip`
--

CREATE TABLE `data_sakip` (
  `id` int NOT NULL,
  `tahun` year NOT NULL,
  `triwulan` enum('I','II','III','IV') NOT NULL,
  `judul_dokumen` varchar(200) DEFAULT NULL,
  `nama_file` varchar(255) DEFAULT NULL,
  `keterangan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dokumen_pppk`
--

CREATE TABLE `dokumen_pppk` (
  `id` int NOT NULL,
  `judul_laporan` varchar(150) DEFAULT NULL,
  `nama_file` varchar(255) DEFAULT NULL,
  `lokasi_file` varchar(255) DEFAULT NULL,
  `tanggal_upload` date DEFAULT (curdate()),
  `keterangan` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pegawai`
--

CREATE TABLE `pegawai` (
  `id` int NOT NULL,
  `nip` varchar(30) NOT NULL,
  `nip_bps` varchar(30) DEFAULT NULL,
  `nama_lengkap` varchar(150) NOT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `tmt_jabatan` date DEFAULT NULL,
  `golongan_akhir` varchar(10) DEFAULT NULL,
  `tmt_golongan` date DEFAULT NULL,
  `status_kepegawaian` varchar(50) DEFAULT NULL,
  `pendidikan_sk` varchar(50) DEFAULT NULL,
  `tmt_cpns` date DEFAULT NULL,
  `ak_terakhir_angka` decimal(10,3) DEFAULT NULL,
  `ak_terakhir_tahun` year DEFAULT NULL,
  `ak_konversi_angka` decimal(10,3) DEFAULT NULL,
  `ak_konversi_tahun` year DEFAULT NULL,
  `keterangan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `jabatan_dashboard` enum('Sekretaris','Bendahara','Staf','Tidak') DEFAULT 'Tidak'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `nip` varchar(30) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `role` enum('admin') DEFAULT 'admin',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `nip`, `jabatan`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$T2w4hqEH9VF5xd52ZqOjUupA4xLVVTHQJZ2Flrqf91DhpugwJhuvq', 'Rida Ainun Sembiring, SH.', '197812252011012005', 'Kepala Sub Bagian Umum', 'admin', '2025-12-19 12:53:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `data_fungsional`
--
ALTER TABLE `data_fungsional`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pegawai_id` (`pegawai_id`);

--
-- Indexes for table `data_keuangan`
--
ALTER TABLE `data_keuangan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_kgb`
--
ALTER TABLE `data_kgb`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pegawai_id` (`pegawai_id`);

--
-- Indexes for table `data_kp`
--
ALTER TABLE `data_kp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pegawai_id` (`pegawai_id`);

--
-- Indexes for table `data_laporan`
--
ALTER TABLE `data_laporan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_sakip`
--
ALTER TABLE `data_sakip`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dokumen_pppk`
--
ALTER TABLE `dokumen_pppk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pegawai`
--
ALTER TABLE `pegawai`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nip` (`nip`);

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
-- AUTO_INCREMENT for table `data_fungsional`
--
ALTER TABLE `data_fungsional`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `data_keuangan`
--
ALTER TABLE `data_keuangan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `data_kgb`
--
ALTER TABLE `data_kgb`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `data_kp`
--
ALTER TABLE `data_kp`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `data_laporan`
--
ALTER TABLE `data_laporan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `data_sakip`
--
ALTER TABLE `data_sakip`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `dokumen_pppk`
--
ALTER TABLE `dokumen_pppk`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pegawai`
--
ALTER TABLE `pegawai`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `data_fungsional`
--
ALTER TABLE `data_fungsional`
  ADD CONSTRAINT `data_fungsional_ibfk_1` FOREIGN KEY (`pegawai_id`) REFERENCES `pegawai` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `data_kgb`
--
ALTER TABLE `data_kgb`
  ADD CONSTRAINT `data_kgb_ibfk_1` FOREIGN KEY (`pegawai_id`) REFERENCES `pegawai` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `data_kp`
--
ALTER TABLE `data_kp`
  ADD CONSTRAINT `data_kp_ibfk_1` FOREIGN KEY (`pegawai_id`) REFERENCES `pegawai` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
