-- --------------------------------------------------------
-- Host:                         103.163.138.82
-- Server version:               10.3.39-MariaDB-cll-lve - MariaDB Server
-- Server OS:                    Linux
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

-- Dumping structure for table astahome_wms.user
CREATE TABLE IF NOT EXISTS `user` (
  `iduser` int(11) NOT NULL AUTO_INCREMENT,
  `idrole` int(11) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `foto` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `handphone` varchar(50) DEFAULT NULL,
  `is_active` int(11) DEFAULT 1,
  `is_whatsapp` int(11) DEFAULT 1,
  `created_by` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `updated_by` varchar(100) DEFAULT NULL,
  `updated_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` int(11) DEFAULT 1,
  PRIMARY KEY (`iduser`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table astahome_wms.user: ~13 rows (approximately)
REPLACE INTO `user` (`iduser`, `idrole`, `username`, `email`, `foto`, `password`, `full_name`, `handphone`, `is_active`, `is_whatsapp`, `created_by`, `created_date`, `updated_by`, `updated_date`, `status`) VALUES
	(4, 1, 'Superadmin', 'superadmin@gmail.com', 'foto_1745588888.png', '$2y$10$Hpc5o/QfB3CsVGezMUhjdOClU2c16NRCnKvTLxMz3LcqKfONFY.p.', 'Superadmin', '081331090331', 1, 1, NULL, '2025-04-25 20:48:09', 'Superadmin', '2025-06-10 10:26:07', 1),
	(5, 4, 'Haris', 'haris@gmail.com', 'foto_1745724577.jpg', '$2y$10$h0nquo0wBJM/t6hUkgXhb.ggchVQ.byceneATYa9t9P6u4vepdY4e', 'Haris', NULL, 1, 1, 'Superadmin', '2025-04-27 10:29:37', 'Superadmin', '2025-04-29 02:10:57', 0),
	(6, 3, 'Suzan', 'suzan@gmail.com', 'foto_1745724656.jpg', '$2y$10$LFArAXh3s.u5LR7IhMBvteImaZF8exujbfrc4UUmXJeHoHQGFjsMe', 'Suzan', NULL, 1, 1, 'Superadmin', '2025-04-27 10:30:56', 'Superadmin', '2025-04-29 02:11:00', 0),
	(7, 2, 'Mustofa', 'mustofa@gmail.com', 'foto_1745824637.jpg', '$2y$10$pxu.7X0NRrXnLj350mKWk.E8jR.PNVx75jCojD/8cI3i83LE.J71K', 'Mustofa', NULL, 1, 1, 'Superadmin', '2025-04-28 14:17:17', 'Superadmin', '2025-05-27 09:46:52', 0),
	(8, 2, 'Alfi', 'Rosi.alvi123@gmail.com', '', '$2y$10$eKf7iC.pn8dt4dw7YiEUkOdfjX2y69GJJFhGVr8VeMhMJXOkA4NMu', 'Alfi', '085816236056', 1, 1, 'Superadmin', '2025-04-29 02:19:29', 'Superadmin', '2025-06-10 10:26:12', 1),
	(9, 2, 'Risma', 'rismaamelia630@gmail.com', '', '$2y$10$kU1Zf84AM4ZDj7W/xLcpdO426284GmnyxbpAjNu8XAXBCrhQsMXii', 'Risma', '081217422903', 1, 0, 'Superadmin', '2025-04-29 02:21:45', 'Superadmin', '2025-07-18 11:08:46', 1),
	(10, 4, 'Admin', 'admin@gmail.com', '', '$2y$10$Z1yQGVv5FP3aEksY.tMVE.uAiB4rov1kjA43tTwlfFCA4zNUa4tQ.', 'Admin', NULL, 1, 1, 'Superadmin', '2025-04-29 02:45:02', 'Superadmin', '2025-07-18 11:09:06', 0),
	(11, 3, 'Dwi', 'titysunshine@gmail.com', '', '$2y$10$Glh9DE0aJeb5vB4jQaYrYupNmjBKrOy7.7b9/hdF7m0sg3Wf6Ur5u', 'Dwi Wahyu', '089616460526', 1, 1, 'Superadmin', '2025-05-14 10:29:05', 'Superadmin', '2025-06-10 10:26:24', 1),
	(12, 2, 'Admin Northwest', 'adminnorthwest@gmail.com', '', '$2y$10$xh5yK/ms02Phu7j.gtSdtOsd6LHfOO.dhKij8Yq1lu1eetloiuY/u', 'Admin Northwest', NULL, 1, 1, 'Superadmin', '2025-05-19 15:43:35', 'Superadmin', '2025-07-18 16:02:01', 0),
	(13, 5, 'Ajeng', 'ajeng.ildha@gmail.com', '', '$2y$10$glm68YsODrlHcnliHNeYvu1FcdCIHCo4hhu8h4vH9acuwa57mkq5q', 'Ajeng', '083830242171', 1, 0, 'Superadmin', '2025-05-27 14:48:58', 'Superadmin', '2025-07-18 11:07:44', 1),
	(14, 6, 'Lia', 'alidaaprillia0804@gmail.com', '', '$2y$10$b/6IbGZ9d2Kf1SSJgFnrC.jUM7wXnFmbHXIpbRzeuJ49mHWbLy8lW', 'Lia', '085733207227', 1, 1, 'Superadmin', '2025-06-02 19:31:38', 'Superadmin', '2025-06-10 10:26:48', 1),
	(15, 4, 'Fani', 'salesteam.suryajayamakmur@gmail.com', '', '$2y$10$7R9BTZ4oCJI5OSHDA0t8kO7vhPsOZGNv7GHX4BGMQ6J.HddCqxTW.', 'Fani', '082301340432', 1, 1, 'Superadmin', '2025-06-30 08:06:35', 'Superadmin', '2025-06-30 08:06:35', 1),
	(16, 5, 'leli', 'acc.ekakarsagemilangraya@gmail.com', '', '$2y$10$1smGDatZ.FwKKg9nN1JnP.a23v9g1K3U7CD4jqk3nb.OB96MjSe5i', 'Leli', '089518331126', 1, 1, 'Superadmin', '2025-07-15 09:22:15', 'Superadmin', '2025-07-15 09:22:15', 1);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
