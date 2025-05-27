-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table db_astawms.user
CREATE TABLE IF NOT EXISTS `user` (
  `iduser` int NOT NULL AUTO_INCREMENT,
  `idrole` int DEFAULT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `foto` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `full_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `handphone` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_active` int DEFAULT '1',
  `created_by` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_by` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updated_date` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int DEFAULT '1',
  PRIMARY KEY (`iduser`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table db_astawms.user: ~9 rows (approximately)
REPLACE INTO `user` (`iduser`, `idrole`, `username`, `email`, `foto`, `password`, `full_name`, `handphone`, `is_active`, `created_by`, `created_date`, `updated_by`, `updated_date`, `status`) VALUES
	(4, 1, 'Superadmin', 'superadmin@gmail.com', 'foto_1745588888.png', '$2y$10$Hpc5o/QfB3CsVGezMUhjdOClU2c16NRCnKvTLxMz3LcqKfONFY.p.', 'Superadmin', NULL, 1, NULL, '2025-04-25 20:48:09', 'Superadmin', '2025-04-26 10:20:35', 1),
	(5, 4, 'Haris', 'haris@gmail.com', 'foto_1745724577.jpg', '$2y$10$h0nquo0wBJM/t6hUkgXhb.ggchVQ.byceneATYa9t9P6u4vepdY4e', 'Haris', NULL, 1, 'Superadmin', '2025-04-27 10:29:37', 'Superadmin', '2025-04-29 02:10:57', 0),
	(6, 3, 'Suzan', 'suzan@gmail.com', 'foto_1745724656.jpg', '$2y$10$LFArAXh3s.u5LR7IhMBvteImaZF8exujbfrc4UUmXJeHoHQGFjsMe', 'Suzan', NULL, 1, 'Superadmin', '2025-04-27 10:30:56', 'Superadmin', '2025-04-29 02:11:00', 0),
	(7, 2, 'Mustofa', 'mustofa@gmail.com', 'foto_1745824637.jpg', '$2y$10$pxu.7X0NRrXnLj350mKWk.E8jR.PNVx75jCojD/8cI3i83LE.J71K', 'Mustofa', NULL, 1, 'Superadmin', '2025-04-28 14:17:17', 'Superadmin', '2025-05-27 13:33:25', 0),
	(8, 2, 'Alfi', 'Rosi.alvi123@gmail.com', '', '$2y$10$eKf7iC.pn8dt4dw7YiEUkOdfjX2y69GJJFhGVr8VeMhMJXOkA4NMu', 'Alfi', NULL, 1, 'Superadmin', '2025-04-29 02:19:29', 'Superadmin', '2025-04-29 10:46:59', 1),
	(9, 4, 'Risma', 'risma@gmail.com', '', '$2y$10$fr28RRQg/suANnWlF8UyBODm2MuH0O6DbiQG3hwDN6zvTzFLdSCGm', 'Risma', '123', 1, 'Superadmin', '2025-04-29 02:21:45', 'Superadmin', '2025-05-27 13:33:38', 1),
	(10, 4, 'Admin', 'admin@gmail.com', 'foto_1745869996.jpg', '$2y$10$Z1yQGVv5FP3aEksY.tMVE.uAiB4rov1kjA43tTwlfFCA4zNUa4tQ.', 'Admin', NULL, 1, 'Superadmin', '2025-04-29 02:45:02', 'Superadmin', '2025-04-29 02:53:16', 1),
	(11, 3, 'Dwi', 'titysunshine@gmail.com', '', '$2y$10$Glh9DE0aJeb5vB4jQaYrYupNmjBKrOy7.7b9/hdF7m0sg3Wf6Ur5u', 'Dwi Wahyu', '085156340619', 1, 'Superadmin', '2025-05-14 10:29:05', 'Superadmin', '2025-05-27 14:25:44', 1),
	(12, 2, 'Admin Northwest', 'adminnorthwest@gmail.com', '', '$2y$10$xh5yK/ms02Phu7j.gtSdtOsd6LHfOO.dhKij8Yq1lu1eetloiuY/u', 'Admin Northwest', NULL, 1, 'Superadmin', '2025-05-19 15:43:35', 'Superadmin', '2025-05-19 15:43:35', 1);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
