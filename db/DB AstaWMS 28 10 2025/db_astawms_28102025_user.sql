-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table db_astawms_28102025.user
CREATE TABLE IF NOT EXISTS `user` (
  `iduser` int NOT NULL AUTO_INCREMENT,
  `idrole` int DEFAULT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `foto` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `handphone` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_active` int DEFAULT '1',
  `is_whatsapp` int DEFAULT '1',
  `created_by` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_by` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updated_date` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int DEFAULT '1',
  `idppl_devices` int DEFAULT NULL,
  PRIMARY KEY (`iduser`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table db_astawms_28102025.user: ~22 rows (approximately)
REPLACE INTO `user` (`iduser`, `idrole`, `username`, `email`, `foto`, `password`, `full_name`, `handphone`, `is_active`, `is_whatsapp`, `created_by`, `created_date`, `updated_by`, `updated_date`, `status`, `idppl_devices`) VALUES
	(4, 1, 'Superadmin', 'superadmin@gmail.com', 'foto_1745588888.png', '$2y$10$Hpc5o/QfB3CsVGezMUhjdOClU2c16NRCnKvTLxMz3LcqKfONFY.p.', 'Superadmin', '081331090331', 1, 1, NULL, '2025-04-25 20:48:09', 'Superadmin', '2025-06-10 10:26:07', 1, NULL),
	(8, 2, 'Alfi', 'Rosi.alvi123@gmail.com', '', '$2y$10$eKf7iC.pn8dt4dw7YiEUkOdfjX2y69GJJFhGVr8VeMhMJXOkA4NMu', 'Alfi', '085816236056', 1, 1, 'Superadmin', '2025-04-29 02:19:29', 'Superadmin', '2025-06-10 10:26:12', 1, NULL),
	(9, 2, 'Risma', 'rismaamelia630@gmail.com', '', '$2y$10$kU1Zf84AM4ZDj7W/xLcpdO426284GmnyxbpAjNu8XAXBCrhQsMXii', 'Risma', '081217422903', 1, 0, 'Superadmin', '2025-04-29 02:21:45', 'Superadmin', '2025-07-18 11:08:46', 1, NULL),
	(10, 4, 'Admin', 'admin@gmail.com', '', '$2y$10$Z1yQGVv5FP3aEksY.tMVE.uAiB4rov1kjA43tTwlfFCA4zNUa4tQ.', 'Admin', NULL, 1, 1, 'Superadmin', '2025-04-29 02:45:02', 'Superadmin', '2025-07-18 11:09:06', 0, NULL),
	(11, 3, 'Dwi', 'titysunshine@gmail.com', '', '$2y$10$Glh9DE0aJeb5vB4jQaYrYupNmjBKrOy7.7b9/hdF7m0sg3Wf6Ur5u', 'Dwi Wahyu', '089616460526', 1, 1, 'Superadmin', '2025-05-14 10:29:05', 'Superadmin', '2025-06-10 10:26:24', 1, NULL),
	(12, 2, 'Admin Northwest', 'adminnorthwest@gmail.com', '', '$2y$10$xh5yK/ms02Phu7j.gtSdtOsd6LHfOO.dhKij8Yq1lu1eetloiuY/u', 'Admin Northwest', NULL, 1, 1, 'Superadmin', '2025-05-19 15:43:35', 'Superadmin', '2025-07-18 16:02:01', 0, NULL),
	(13, 5, 'Ajeng', 'ajeng.ildha@gmail.com', '', '$2y$10$glm68YsODrlHcnliHNeYvu1FcdCIHCo4hhu8h4vH9acuwa57mkq5q', 'Ajeng', '083830242171', 1, 0, 'Superadmin', '2025-05-27 14:48:58', 'Superadmin', '2025-07-18 11:07:44', 1, NULL),
	(14, 5, 'Lia', 'alidaaprillia0804@gmail.com', '', '$2y$10$b/6IbGZ9d2Kf1SSJgFnrC.jUM7wXnFmbHXIpbRzeuJ49mHWbLy8lW', 'Lia', '085733207227', 1, 1, 'Superadmin', '2025-06-02 19:31:38', 'Superadmin', '2025-08-28 08:06:50', 1, NULL),
	(15, 4, 'Fani', 'salesteam.suryajayamakmur@gmail.com', '', '$2y$10$7R9BTZ4oCJI5OSHDA0t8kO7vhPsOZGNv7GHX4BGMQ6J.HddCqxTW.', 'Fani', '082301340432', 1, 1, 'Superadmin', '2025-06-30 08:06:35', 'Superadmin', '2025-06-30 08:06:35', 1, NULL),
	(16, 5, 'leli', 'acc.ekakarsagemilangraya@gmail.com', '', '$2y$10$1smGDatZ.FwKKg9nN1JnP.a23v9g1K3U7CD4jqk3nb.OB96MjSe5i', 'Leli', '089518331126', 1, 0, 'Superadmin', '2025-07-15 09:22:15', 'Superadmin', '2025-10-06 08:09:10', 0, NULL),
	(17, 4, 'Dylla', 'dyllagstyaw@gmail.com', '', '$2y$10$wzsLvw7.UFHutI9EdwgEiesx3I6ngMJN02Di6uhzE62kF0PvrCWcm', 'Dylla Agusta', NULL, 1, 1, 'Superadmin', '2025-08-23 10:46:34', 'Superadmin', '2025-08-23 10:46:34', 1, NULL),
	(18, 4, 'Tyeva', 'marketingastahomeware@gmail.com', '', '$2y$10$A6Yd0r5CLPuL4uCJSZN0Punjunx5bXl1kmbaYAKKJnV7AYMtIYpxO', 'Tyeva', '0895329414411', 1, 1, 'Superadmin', '2025-08-23 11:04:03', 'Syahrul', '2025-11-12 15:48:47', 1, NULL),
	(19, 6, 'anisa', 'annisaaziz5@gmail.com', '', '$2y$10$k2KeXKEHk7bRnj0eFufwou0AYc351IJ9nmmS9KKV0qHlErhPIEife', 'Anisa', '0895337783926', 1, 1, 'Superadmin', '2025-08-28 08:11:00', 'Superadmin', '2025-09-15 16:09:09', 1, NULL),
	(20, 1, 'Master', 'master@gmail.com', '', '$2y$10$pTcgFztZmaEBcNPwhrIJ8.mbQCfEFKEkcxJn3VILWlUOuh1XIR9ae', 'Master', '0816536516', 1, 1, 'Superadmin', '2025-09-15 16:07:19', 'Superadmin', '2025-09-15 16:07:19', 1, NULL),
	(21, 4, 'Achmad', 'rianfivers.02@gmail.com', 'foto_1758936694.jpg', '$2y$10$YPMKdQaQca1PTDBie8/LTOwxbH54vkffu0MUy3VptX.4WRpqL2Nyy', 'Achmad', NULL, 1, 1, 'Superadmin', '2025-09-16 10:58:46', 'Achmad', '2025-09-27 08:31:34', 1, NULL),
	(22, 4, 'Gita', 'adindagp19@gmail.com', '', '$2y$10$yWMNwA7LhTTysGNkxwVmpe1SJyc0V/ptYn634E71YnLSKqm03FEfW', 'Adinda Gita Puspita', '085735096566', 1, 1, 'Superadmin', '2025-09-17 08:40:53', 'Superadmin', '2025-09-17 08:40:53', 1, NULL),
	(23, 4, 'Dinda', 'adindaazp@gmail.com', 'foto_1758081838.jpeg', '$2y$10$38vCMv7Bxohxoy.rJJudoOI5BGA8zxpHvloim3TfkIqHgVTxUOPZq', 'Adinda Dewi Zulfia Putri ', NULL, 1, 1, 'Superadmin', '2025-09-17 10:48:08', 'Dinda', '2025-09-17 11:03:58', 1, NULL),
	(24, 4, 'Titin', 'imroatinfauziah12@gmail.com', '', '$2y$10$URCr5/GbkzJ2J1vgvtD.ZOj0Ca76XroEyg8E8wu4qNbiXPPP/ADDC', 'Imroatin Fauziah', NULL, 1, 1, 'Superadmin', '2025-09-17 10:53:05', 'Superadmin', '2025-09-17 10:53:05', 1, NULL),
	(25, 1, 'Syahrul', 'chalung.izha@gmail.com', 'foto_1763009575.jpeg', '$2y$10$0uvveqfo3JKTpoLlQIW8xuAuY.WwJtlk3ubUVVNivjDUYqvzRuoem', 'Syahrul Izha Mahendra', '085156340619', 1, 1, 'Superadmin', '2025-09-20 08:12:40', 'Syahrul', '2025-11-13 11:57:46', 1, NULL),
	(26, 4, 'Albab', 'albabmustofa@gmail.com', 'foto_1758703183.jpeg', '$2y$10$5Zm46SDGEholYDqDdUbchOq/2R3GRTlfZ997ahDjzQvZtmNeVs13a', 'Albab Mustofa', '085806241787', 1, 1, 'Syahrul', '2025-09-24 13:41:34', 'Albab', '2025-09-24 15:39:43', 1, NULL),
	(27, 4, 'Mirza', 'mirza.rian10@gmail.com', '', '$2y$10$UFkakRlH8I.XmSD83fuWk.HFZkzHzUwuX51dsNQHkOSMOa0RQHgCq', 'Mirza', '081215908797', 1, 1, 'Syahrul', '2025-09-24 13:57:51', 'Syahrul', '2025-09-24 13:58:39', 1, NULL),
	(28, 4, 'Michella', 'michellaaudrya@gmail.com', '', '$2y$10$tjTHxI6mK.5OGU4QpDqRsOEgHVNHSrU5zwm7doWodYA5RxOhsef7y', 'Michella Audry Anjarwati', '085856777414', 1, 1, 'Syahrul', '2025-09-24 15:03:42', 'Syahrul', '2025-09-24 15:04:18', 1, NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
