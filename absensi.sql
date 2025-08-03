-- Database: absensi_sekolah

-- Struktur tabel users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `role` enum('admin','guru') NOT NULL DEFAULT 'guru',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data dummy untuk users
INSERT INTO `users` (`username`, `password`, `nama`, `role`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin'),
('guru1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Guru Pertama', 'guru');

-- Struktur tabel sekolah
CREATE TABLE IF NOT EXISTS `sekolah` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_sekolah` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `telepon` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data dummy untuk sekolah
INSERT INTO `sekolah` (`nama_sekolah`, `alamat`, `telepon`, `email`, `logo`) VALUES
('SMA Negeri 1 Contoh', 'Jl. Contoh No. 123', '(021) 1234567', 'info@sman1contoh.sch.id', 'logo.png');

-- Struktur tabel kelas
CREATE TABLE IF NOT EXISTS `kelas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kelas` varchar(20) NOT NULL,
  `tingkat` enum('10','11','12') NOT NULL,
  `jurusan` varchar(50) NOT NULL,
  `wali_kelas` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data dummy untuk kelas
INSERT INTO `kelas` (`nama_kelas`, `tingkat`, `jurusan`, `wali_kelas`) VALUES
('X IPA 1', '10', 'IPA', 'Budi Santoso'),
('X IPA 2', '10', 'IPA', 'Ani Wijaya'),
('XI IPS 1', '11', 'IPS', 'Citra Dewi'),
('XII IPA 1', '12', 'IPA', 'Dedi Prabowo');

-- Struktur tabel siswa
CREATE TABLE IF NOT EXISTS `siswa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nis` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kelas_id` int(11) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nis` (`nis`),
  KEY `kelas_id` (`kelas_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data dummy untuk siswa
INSERT INTO `siswa` (`nis`, `nama`, `kelas_id`, `jenis_kelamin`, `alamat`, `telepon`) VALUES
('20230101', 'Andi Wijaya', 1, 'L', 'Jl. Merdeka No. 10', '081234567890'),
('20230102', 'Budi Santoso', 1, 'L', 'Jl. Pahlawan No. 5', '081234567891'),
('20230103', 'Citra Dewi', 2, 'P', 'Jl. Sudirman No. 15', '081234567892'),
('20230104', 'Dedi Prabowo', 2, 'L', 'Jl. Gatot Subroto No. 20', '081234567893');

-- Struktur tabel absensi
CREATE TABLE IF NOT EXISTS `absensi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siswa_id` int(11) NOT NULL,
  `status` enum('H','I','S','A') NOT NULL COMMENT 'H=Hadir, I=Izin, S=Sakit, A=Alpa',
  `tanggal` datetime NOT NULL,
  `keterangan` text DEFAULT NULL,
  `user_id` int(11) NOT NULL COMMENT 'User yang input',
  PRIMARY KEY (`id`),
  KEY `siswa_id` (`siswa_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data dummy untuk absensi
INSERT INTO `absensi` (`siswa_id`, `status`, `tanggal`, `keterangan`, `user_id`) VALUES
(1, 'H', '2023-06-01 07:30:00', NULL, 1),
(2, 'H', '2023-06-01 07:31:00', NULL, 1),
(3, 'I', '2023-06-01 07:35:00', 'Izin keluarga', 1),
(4, 'S', '2023-06-01 07:40:00', 'Sakit demam', 1),
(1, 'H', '2023-06-02 07:28:00', NULL, 2),
(2, 'A', '2023-06-02 08:00:00', 'Tidak ada keterangan', 2);

-- Foreign key constraints
ALTER TABLE `siswa`
  ADD CONSTRAINT `siswa_ibfk_1` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`);

ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`),
  ADD CONSTRAINT `absensi_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);