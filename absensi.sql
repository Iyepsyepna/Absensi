-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 03, 2025 at 10:35 PM
-- Server version: 10.11.11-MariaDB-cll-lve
-- PHP Version: 8.3.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pdimnnrq_absensi`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `status` enum('H','I','S','A') NOT NULL COMMENT 'H=Hadir, I=Izin, S=Sakit, A=Alpa',
  `tanggal` date NOT NULL,
  `jam` time DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `user_id` int(11) NOT NULL COMMENT 'User  yang input',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absensi`
--

INSERT INTO `absensi` (`id`, `siswa_id`, `status`, `tanggal`, `jam`, `keterangan`, `user_id`, `created_at`) VALUES
(21, 42, 'H', '2025-07-23', NULL, '', 5, '2025-07-22 19:37:29'),
(22, 43, 'H', '2025-07-23', NULL, '', 5, '2025-07-22 19:37:29'),
(23, 44, 'H', '2025-07-23', NULL, '', 5, '2025-07-22 19:37:29'),
(24, 45, 'H', '2025-07-23', NULL, '', 5, '2025-07-22 19:37:29'),


-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id` int(11) NOT NULL,
  `nama_kelas` varchar(20) NOT NULL,
  `tingkat` enum('10','11','12') NOT NULL,
  `jurusan` varchar(50) NOT NULL,
  `wali_kelas` varchar(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`id`, `nama_kelas`, `tingkat`, `jurusan`, `wali_kelas`, `keterangan`) VALUES
(7, 'VII A', '', 'A', 'Mayu Restu gurunanda, S.Pd.', ''),
(8, 'VII B', '', 'B', 'Annisa Fatchanah, S.Pd.', ''),
(9, 'VII C', '', 'C', 'Adlha Muhti Hidayat, S.S.', ''),
(10, 'VII D', '', 'D', 'Nida Fitria Nasrotin, S.pd.', ''),
(11, 'VII E', '', 'E', 'Ahmad Satori, S.Pd.I.', ''),
(12, 'VII F', '', 'F', 'Iyep Syepna, S.Pd.', ''),
(13, 'VII G', '', 'G', 'Siti Nurwaqiah, S.Pd.', ''),
(14, 'VII H', '', 'H', 'Feni Farihah, S.Pd.', ''),
(15, 'VII I', '', 'I', 'Daryuni, S.Pd.', ''),
(16, 'VIII D', '', 'D', 'Sri Hayati Hidayatillah, S.Pd.I.', ''),
(17, 'VIII E', '', 'E', 'Kuswendi, S.Pd.', ''),
(18, 'VIII F', '', 'F', 'Rastilah, S.Pd.', '');

-- --------------------------------------------------------

--
-- Table structure for table `sekolah`
--

CREATE TABLE `sekolah` (
  `id` int(11) NOT NULL,
  `nama_sekolah` varchar(100) NOT NULL,
  `nss` varchar(50) DEFAULT NULL,
  `npsn` varchar(50) DEFAULT NULL,
  `alamat` text NOT NULL,
  `kecamatan` varchar(50) DEFAULT NULL,
  `kabupaten` varchar(50) DEFAULT NULL,
  `provinsi` varchar(50) DEFAULT NULL,
  `telepon` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `website` varchar(100) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `kepala_sekolah` varchar(100) DEFAULT NULL,
  `nip_kepala_sekolah` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sekolah`
--

INSERT INTO `sekolah` (`id`, `nama_sekolah`, `nss`, `npsn`, `alamat`, `kecamatan`, `kabupaten`, `provinsi`, `telepon`, `email`, `website`, `logo`, `kepala_sekolah`, `nip_kepala_sekolah`) VALUES
(1, 'SMP Negeri 1 Patrol', 'Terakreditasi A (Unggul)', '20216026', 'Jl. Raya Patrol, Desa Patrol Baru, Kec. Patrol - Indramayu 45258 Jawa Barat', 'Kecamatan Patrol', 'Kab. Indramayu', 'Jawa Barat', '(021) 12345678', 'smpn1patrol@gmail.com', 'https://smpnegeri1patrol.sch.id', '', 'Sukana, M.Pd', '196508181387031010');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `id` int(11) NOT NULL,
  `nis` varchar(20) NOT NULL,
  `nisn` varchar(20) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `kelas_id` int(11) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `agama` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `nama_ortu` varchar(100) DEFAULT NULL,
  `telepon_ortu` varchar(20) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`id`, `nis`, `nisn`, `nama`, `kelas_id`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `agama`, `alamat`, `telepon`, `email`, `nama_ortu`, `telepon_ortu`, `foto`) VALUES
(10, '341543512', NULL, 'ADINDA MARSHA TRI HABSARI', 9, 'P', '', '0000-00-00', '', '', '', NULL, '', '', ''),
(11, '213551512', NULL, 'AHMAD RAMADHANI', 9, 'L', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL),
(12, '135515113', NULL, 'ALISA NURHAFSARI', 9, 'P', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL),
(13, '513131111', NULL, 'AMRULLAH', 9, 'L', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL),


-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) DEFAULT NULL,
  `nis` varchar(20) DEFAULT NULL,
  `nisn` varchar(20) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `kelas_id` int(11) DEFAULT NULL,
  `jenis_kelamin` char(1) DEFAULT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `agama` varchar(50) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `nama_ortu` varchar(100) DEFAULT NULL,
  `telepon_ortu` varchar(20) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `role` enum('admin','guru','staff') NOT NULL DEFAULT 'guru',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama`, `role`, `last_login`, `created_at`) VALUES
(1, 'admin', '$2y$10$8KBW9APh24q1Y0u/vx1UguEAvzD0SDT35ymTk1j0Xoa78Qe0dhh2C', 'Administrator Utama', 'admin', NULL, '2025-07-21 11:48:56'),



--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siswa_id` (`siswa_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `tanggal` (`tanggal`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sekolah`
--
ALTER TABLE `sekolah`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nis` (`nis`),
  ADD UNIQUE KEY `nisn` (`nisn`),
  ADD KEY `kelas_id` (`kelas_id`);

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
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=533;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `sekolah`
--
ALTER TABLE `sekolah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=394;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `absensi_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `siswa`
--
ALTER TABLE `siswa`
  ADD CONSTRAINT `siswa_ibfk_1` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
