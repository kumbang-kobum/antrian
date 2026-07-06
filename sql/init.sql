-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 25, 2025 at 10:14 AM
-- Server version: 10.4.28-MariaDB-log
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `antrian_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `antrian`
--

CREATE TABLE `antrian` (
  `id` int(11) NOT NULL,
  `tgl` date NOT NULL,
  `jenis` enum('P','F','M') NOT NULL,
  `no_reg_mjkn` varchar(20) DEFAULT NULL COMMENT 'Format: KD_BPJS-NOURUT, contoh ANA-3. NULL = pasien walk-in.',
  `nomor` int(11) NOT NULL,
  `status` enum('menunggu','dipanggil','selesai') NOT NULL DEFAULT 'menunggu',
  `loket` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `called_at` datetime DEFAULT NULL,
  `finished_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `antrian`
--

INSERT INTO `antrian` (`id`, `tgl`, `jenis`, `nomor`, `status`, `loket`, `created_at`, `called_at`, `finished_at`) VALUES
(1, '2025-09-24', 'P', 1, 'selesai', 1, '2025-09-24 06:36:05', '2025-09-24 14:20:41', '2025-09-24 14:50:12'),
(2, '2025-09-24', 'F', 1, 'selesai', 6, '2025-09-24 06:36:21', '2025-09-24 14:50:59', '2025-09-24 14:51:27'),
(3, '2025-09-24', 'P', 2, 'selesai', 1, '2025-09-24 07:09:47', '2025-09-24 14:43:44', '2025-09-24 14:50:14'),
(4, '2025-09-24', 'P', 3, 'selesai', 1, '2025-09-24 07:10:50', '2025-09-24 14:57:04', '2025-09-24 15:44:28'),
(5, '2025-09-24', 'P', 4, 'selesai', 1, '2025-09-24 07:11:09', '2025-09-24 14:57:04', '2025-09-24 15:44:27'),
(6, '2025-09-24', 'F', 2, 'selesai', 6, '2025-09-24 07:13:39', '2025-09-24 14:43:54', '2025-09-24 14:51:30'),
(7, '2025-09-24', 'P', 5, 'selesai', 2, '2025-09-24 07:38:20', '2025-09-24 14:57:02', '2025-09-24 15:44:28'),
(8, '2025-09-24', 'P', 6, 'selesai', 2, '2025-09-24 07:38:26', '2025-09-24 14:57:01', '2025-09-24 15:44:29'),
(9, '2025-09-24', 'F', 3, 'selesai', 8, '2025-09-24 07:38:32', '2025-09-24 14:57:51', '2025-09-24 15:43:41'),
(10, '2025-09-24', 'F', 4, 'selesai', 6, '2025-09-24 07:38:37', '2025-09-24 14:57:53', '2025-09-24 15:43:48'),
(11, '2025-09-24', 'F', 5, 'selesai', 6, '2025-09-24 07:38:41', '2025-09-24 14:57:40', '2025-09-24 15:43:48'),
(12, '2025-09-24', 'P', 7, 'selesai', 1, '2025-09-24 08:41:26', '2025-09-24 15:42:33', '2025-09-24 15:44:30'),
(13, '2025-09-24', 'P', 8, 'selesai', 1, '2025-09-24 08:41:34', '2025-09-24 15:44:36', '2025-09-24 15:44:44'),
(14, '2025-09-24', 'F', 6, 'selesai', 8, '2025-09-24 08:41:39', '2025-09-24 15:43:01', '2025-09-24 15:43:49'),
(15, '2025-09-24', 'F', 7, 'selesai', 8, '2025-09-24 08:41:45', '2025-09-24 15:43:51', '2025-09-24 15:44:00'),
(16, '2025-10-01', 'P', 1, 'menunggu', NULL, '2025-10-01 06:03:38', NULL, NULL),
(17, '2025-10-01', 'F', 1, 'menunggu', NULL, '2025-10-01 06:03:50', NULL, NULL),
(18, '2025-10-01', 'P', 2, 'menunggu', NULL, '2025-10-01 06:04:53', NULL, NULL),
(19, '2025-10-01', 'P', 3, 'menunggu', NULL, '2025-10-01 06:04:58', NULL, NULL),
(20, '2025-10-01', 'F', 2, 'menunggu', NULL, '2025-10-01 06:06:21', NULL, NULL),
(21, '2025-10-01', 'P', 4, 'menunggu', NULL, '2025-10-01 06:06:33', NULL, NULL),
(22, '2025-10-16', 'F', 1, 'menunggu', NULL, '2025-10-16 05:49:33', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `last_called`
--

CREATE TABLE `last_called` (
  `jenis` enum('P','F','M') NOT NULL,
  `kode` varchar(10) NOT NULL,
  `loket` tinyint(4) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `last_called`
--

INSERT INTO `last_called` (`jenis`, `kode`, `loket`, `updated_at`) VALUES
('P', 'P0008', 1, '2025-09-24 08:44:36'),
('F', 'F0007', 8, '2025-09-24 08:43:51'),
('M', '-',    NULL, '2025-09-24 08:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `antrian`
--
ALTER TABLE `antrian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tgl` (`tgl`,`jenis`,`status`),
  ADD UNIQUE KEY `uq_mjkn_per_hari` (`tgl`,`no_reg_mjkn`);

--
-- Indexes for table `last_called`
--
ALTER TABLE `last_called`
  ADD PRIMARY KEY (`jenis`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `antrian`
--
ALTER TABLE `antrian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;