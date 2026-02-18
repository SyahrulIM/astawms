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

-- Dumping structure for table db_astawms_26122025.ppl_employee
CREATE TABLE IF NOT EXISTS `ppl_employee` (
  `idppl_employee` int DEFAULT NULL,
  `no_excel` int DEFAULT NULL,
  `name` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `iduser` int DEFAULT NULL,
  `place` varchar(50) COLLATE armscii8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin;

-- Dumping data for table db_astawms_26122025.ppl_employee: ~24 rows (approximately)
DELETE FROM `ppl_employee`;
INSERT INTO `ppl_employee` (`idppl_employee`, `no_excel`, `name`, `iduser`, `place`) VALUES
	(1, 31, 'Gerry', NULL, 'IV'),
	(2, 30, 'Dwi', 11, 'IV'),
	(3, 29, 'Syahrul', 25, 'IV'),
	(4, 28, 'Tyeva', 18, 'IV'),
	(5, 55, 'Achmad', 21, 'IV'),
	(6, 27, 'Ajeng', 13, 'IV'),
	(7, 26, 'Mirza', 27, 'IV'),
	(8, 24, 'Mustofa', 26, 'IV'),
	(9, 21, 'Risma', 9, 'IV'),
	(10, 80, 'Nisa', 19, 'IV'),
	(11, 38, 'Gita', 22, 'IV'),
	(12, 37, 'Dinda', 23, 'IV'),
	(13, 36, 'Titin', 24, 'IV'),
	(14, 2, 'Michella', 28, 'IV'),
	(15, 35, 'Lia', 14, 'IV'),
	(16, 1, 'Eka', 29, 'IV'),
	(17, 4, 'Chandra', 30, 'IV'),
	(18, 5, 'Widia', 36, 'Gudang'),
	(19, 8, 'Atika', 34, 'Gudang'),
	(20, 30, 'Adi', 32, 'Gudang'),
	(21, 52, 'Aji', 31, 'Gudang'),
	(22, 29, 'Catur', 35, 'Gudang'),
	(23, 31, 'Alfi', 8, 'Gudang'),
	(24, 34, 'Purwono', 33, 'Gudang');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
