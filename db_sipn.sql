-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 27, 2026 at 01:51 PM
-- Server version: 10.11.14-MariaDB-0ubuntu0.24.04.1
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_siacad_smk_2`
--

-- --------------------------------------------------------

--
-- Table structure for table `guru`
--

CREATE TABLE `guru` (
  `id` int(11) NOT NULL,
  `nip` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guru`
--

INSERT INTO `guru` (`id`, `nip`, `nama`, `jenis_kelamin`, `user_id`) VALUES
(1, '23297489275', 'Moch. Nafsir S.PD', 'L', 2);

-- --------------------------------------------------------

--
-- Table structure for table `mapel`
--

CREATE TABLE `mapel` (
  `id` int(11) NOT NULL,
  `kode_mapel` varchar(10) NOT NULL,
  `nama_mapel` varchar(100) NOT NULL,
  `guru_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mapel`
--

INSERT INTO `mapel` (`id`, `kode_mapel`, `nama_mapel`, `guru_id`) VALUES
(1, 'MP001', 'Pendidikan Agama Dan Budi Pekerti', 1);

-- --------------------------------------------------------

--
-- Table structure for table `mengajar`
--

CREATE TABLE `mengajar` (
  `id` int(11) NOT NULL,
  `mapel_id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nilai_rapor`
--

CREATE TABLE `nilai_rapor` (
  `id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `mapel_id` int(11) NOT NULL,
  `nilai_kkm` int(11) NOT NULL,
  `nilai_pengetahuan` int(11) NOT NULL,
  `nilai_keterampilan` int(11) NOT NULL,
  `Keterangan` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nilai_rapor`
--

INSERT INTO `nilai_rapor` (`id`, `siswa_id`, `mapel_id`, `nilai_kkm`, `nilai_pengetahuan`, `nilai_keterampilan`, `Keterangan`) VALUES
(2, 2, 1, 75, 86, 85, 'Tuntas');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `id` int(11) NOT NULL,
  `nis` varchar(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kelas` varchar(10) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`id`, `nis`, `nama`, `kelas`, `jenis_kelamin`, `user_id`) VALUES
(1, '3589389213', 'ujang', 'XI RPL 2', 'L', 3),
(2, '324122321', 'sanov', 'XI RPL 1', 'L', 5),
(4, '2874982', 'vina', 'X AKL 1', 'P', 7),
(5, '2983918487', 'Wiliam', 'X RPL 1', 'L', 11),
(6, '7382488373', 'Ardi loma', 'x rpl 3', 'L', 12);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','guru','siswa') NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `foto`, `created_at`) VALUES
(1, 'admin', '$2y$10$Xg.Q4Fhice7RqAc0rLy82eSP17g8ra.mYu8efCiHmtg5yRVZZ.7t6', 'admin', NULL, '2026-10-03 15:05:06'),
(2, 'Nafsir123', '$2y$10$tvZF5OjkXhAs4yRaMW2F6.A8KnYahFpTwz2ewveWdhPLYQDiaW4iu', 'guru', NULL, '2026-10-04 03:30:14'),
(3, 'ujang123', '$2y$10$VtLS0xrvjOZOf22ZjWyk5uN6NOJBOaPNvWIIoaVOgf7jHLhhik9Hu', 'siswa', NULL, '2026-10-04 03:34:24'),
(5, 'sanov12', '$2y$10$Kr5DfYshVD.MCh7zr8lMm.lsmUNt2yibTi9wTRHkeOQmF.WF2ODHK', 'siswa', 'profil_siswa_5_1794995549.png', '2026-10-12 13:43:33'),
(7, 'vina123', '$2y$10$Pt7zqhl0v1oo9hFsdarRr.8PaX8XoHAn5bZjWYUSq5g/6ScJikRFG', 'siswa', NULL, '2026-11-08 03:58:06'),
(11, 'Wiliam Nathanael', '$2y$10$7zJ9J3ImAs2b0sat5vBFa.lbsmydGUYKs/tbuB8NZNCV2UOjOXAiO', 'siswa', NULL, '2026-11-19 12:48:29'),
(12, 'ardi123', '$2y$10$pmR/EPU35PV9kb2kd2KDBuGG6cCU101TskFejORdLKwUSLMURp6m2', 'siswa', NULL, '2026-11-27 13:38:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `mapel`
--
ALTER TABLE `mapel`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guru_id` (`guru_id`);

--
-- Indexes for table `mengajar`
--
ALTER TABLE `mengajar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mapel_id` (`mapel_id`),
  ADD KEY `siswa_id` (`siswa_id`);

--
-- Indexes for table `nilai_rapor`
--
ALTER TABLE `nilai_rapor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siswa_id` (`siswa_id`),
  ADD KEY `mapel_id` (`mapel_id`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `guru`
--
ALTER TABLE `guru`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `mapel`
--
ALTER TABLE `mapel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mengajar`
--
ALTER TABLE `mengajar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nilai_rapor`
--
ALTER TABLE `nilai_rapor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `guru`
--
ALTER TABLE `guru`
  ADD CONSTRAINT `fk_guru_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `mapel`
--
ALTER TABLE `mapel`
  ADD CONSTRAINT `fk_mapel_guru` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `mengajar`
--
ALTER TABLE `mengajar`
  ADD CONSTRAINT `fk_mengajar_mapel` FOREIGN KEY (`mapel_id`) REFERENCES `mapel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mengajar_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `nilai_rapor`
--
ALTER TABLE `nilai_rapor`
  ADD CONSTRAINT `fk_nilai_mapel` FOREIGN KEY (`mapel_id`) REFERENCES `mapel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_nilai_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `siswa`
--
ALTER TABLE `siswa`
  ADD CONSTRAINT `fk_siswa_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
