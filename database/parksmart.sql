-- Database: `parksmart`

CREATE DATABASE IF NOT EXISTS `parksmart` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `parksmart`;

-- Table structure for table `users`
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default admin user (password: admin123)
-- MD5 hash for admin123 is 0192023a7bbd73250516f069df18b500
-- Let's use password_hash() in PHP, but for SQL insert we can use md5 for simplicity, or just plain text if they want basic, but let's use a standard bcrypt hash.
-- password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO `users` (`id`, `username`, `email`, `password`, `name`) VALUES
(1, 'admin', 'admin@parksmart.com', '$2y$10$RkcGrZDZWCMuhAr18t2yuOJMM5L7NH5UufZt4HaJVFrBAUPC7gYFu', 'Administrator');


-- Table structure for table `settings`
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'tarif_motor', '2000'),
(2, 'tarif_mobil', '5000'),
(3, 'kapasitas_motor', '200'),
(4, 'kapasitas_mobil', '100');


-- Table structure for table `tickets`
CREATE TABLE `tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_code` varchar(50) NOT NULL,
  `plat_nomor` varchar(20) NOT NULL,
  `jenis_kendaraan` enum('motor','mobil') NOT NULL,
  `jam_masuk` datetime NOT NULL,
  `jam_keluar` datetime DEFAULT NULL,
  `biaya` int(11) DEFAULT NULL,
  `status` enum('parkir','selesai') NOT NULL DEFAULT 'parkir',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ticket_code` (`ticket_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
