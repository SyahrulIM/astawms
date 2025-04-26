-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.10.0.7000
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for db_astachecker
CREATE DATABASE IF NOT EXISTS `db_astachecker` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `db_astachecker`;

-- Dumping structure for table db_astachecker.detail_instock
CREATE TABLE IF NOT EXISTS `detail_instock` (
  `iddetail_instock` int NOT NULL AUTO_INCREMENT,
  `instock_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `nama_produk` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `sisa` int DEFAULT NULL,
  `keterangan` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`iddetail_instock`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table db_astachecker.detail_instock: ~11 rows (approximately)
INSERT INTO `detail_instock` (`iddetail_instock`, `instock_code`, `sku`, `nama_produk`, `jumlah`, `sisa`, `keterangan`) VALUES
	(40, 'TSC20250424223350', 'ASPM-150', 'Gilingan Mie / Pasta Maker Jumbo', 10, 10, 'test tambah'),
	(41, 'TSC20250424223528', 'ASPM-150', 'Gilingan Mie / Pasta Maker Jumbo', 2, 2, 'test tambah'),
	(42, 'TSC20250424223640', 'ASPM-150', 'Gilingan Mie / Pasta Maker Jumbo', 5, 5, 'test01'),
	(43, 'TSC20250424223805', 'ASPM-150', 'Gilingan Mie / Pasta Maker Jumbo', 7, 24, 'test tambah'),
	(44, 'TSC20250424223850', 'ASPM-150', 'Gilingan Mie / Pasta Maker Jumbo', 5, 29, 'test01'),
	(45, 'TSC20250424224528', 'ASPM-150', 'Gilingan Mie / Pasta Maker Jumbo', 10, 10, ''),
	(46, 'TSC20250424225624', 'ASPM-150', 'Gilingan Mie / Pasta Maker Jumbo', 10, 39, 'test tambah'),
	(47, 'TSC20250424232359', 'ASPM-150', 'Gilingan Mie / Pasta Maker Jumbo', 30, 59, ''),
	(48, 'TSC20250424232359', 'ASPM-115SS', 'Gilingan Mie / Pasta Maker Stainless', 30, 30, ''),
	(49, 'TSC20250424232441', 'ASPM-150', 'Gilingan Mie / Pasta Maker Jumbo', 50, 60, ''),
	(50, 'TSC20250424232441', 'ASPM-115SS', 'Gilingan Mie / Pasta Maker Stainless', 50, 50, '');

-- Dumping structure for table db_astachecker.detail_outstock
CREATE TABLE IF NOT EXISTS `detail_outstock` (
  `iddetail_outstock` int NOT NULL AUTO_INCREMENT,
  `outstock_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `sku` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `nama_produk` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `sisa` int DEFAULT NULL,
  `keterangan` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`iddetail_outstock`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table db_astachecker.detail_outstock: ~3 rows (approximately)
INSERT INTO `detail_outstock` (`iddetail_outstock`, `outstock_code`, `sku`, `nama_produk`, `jumlah`, `sisa`, `keterangan`) VALUES
	(30, 'TSC20250424231813', 'ASPM-150', 'Gilingan Mie / Pasta Maker Jumbo', 10, 29, ''),
	(31, 'TSC20250424232516', 'ASPM-150', 'Gilingan Mie / Pasta Maker Jumbo', 10, 49, ''),
	(32, 'TSC20250424232516', 'ASPM-115SS', 'Gilingan Mie / Pasta Maker Stainless', 10, 20, '');

-- Dumping structure for table db_astachecker.gudang
CREATE TABLE IF NOT EXISTS `gudang` (
  `idgudang` int NOT NULL AUTO_INCREMENT,
  `nama_gudang` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`idgudang`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table db_astachecker.gudang: ~2 rows (approximately)
INSERT INTO `gudang` (`idgudang`, `nama_gudang`) VALUES
	(1, 'D17'),
	(2, 'D09');

-- Dumping structure for table db_astachecker.instock
CREATE TABLE IF NOT EXISTS `instock` (
  `idinstock` int NOT NULL AUTO_INCREMENT,
  `idgudang` int DEFAULT NULL,
  `instock_code` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `tgl_terima` date DEFAULT NULL,
  `jam_terima` time DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `user` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `kategori` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`idinstock`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table db_astachecker.instock: ~9 rows (approximately)
INSERT INTO `instock` (`idinstock`, `idgudang`, `instock_code`, `tgl_terima`, `jam_terima`, `datetime`, `user`, `kategori`) VALUES
	(27, NULL, 'TSC20250424223350', '2025-04-24', '22:33:50', '2025-04-24 22:33:50', 'Admin', 'Barang Masuk'),
	(28, NULL, 'TSC20250424223528', '2025-04-24', '22:35:28', '2025-04-24 22:35:28', 'Admin', 'Barang Masuk'),
	(29, NULL, 'TSC20250424223640', '2025-04-24', '22:36:40', '2025-04-24 22:36:40', 'Admin', 'Barang Masuk'),
	(30, NULL, 'TSC20250424223805', '2025-04-24', '22:38:05', '2025-04-24 22:38:05', 'Admin', 'Barang Masuk'),
	(31, NULL, 'TSC20250424223850', '2025-04-24', '22:38:50', '2025-04-24 22:38:50', 'Admin', 'Barang Masuk'),
	(32, 2, 'TSC20250424224528', '2025-04-24', '22:45:28', '2025-04-24 22:45:28', 'Admin', 'Barang Masuk'),
	(33, 1, 'TSC20250424225624', '2025-04-24', '22:56:24', '2025-04-24 22:56:24', 'Admin', 'Barang Masuk'),
	(34, 1, 'TSC20250424232359', '2025-04-24', '23:23:59', '2025-04-24 23:23:59', 'Admin', 'Barang Masuk'),
	(35, 2, 'TSC20250424232441', '2025-04-24', '23:24:41', '2025-04-24 23:24:41', 'Admin', 'Barang Masuk');

-- Dumping structure for table db_astachecker.outstock
CREATE TABLE IF NOT EXISTS `outstock` (
  `idoutstock` int NOT NULL AUTO_INCREMENT,
  `idgudang` int DEFAULT NULL,
  `outstock_code` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `tgl_keluar` date DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `user` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `kategori` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`idoutstock`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table db_astachecker.outstock: ~3 rows (approximately)
INSERT INTO `outstock` (`idoutstock`, `idgudang`, `outstock_code`, `tgl_keluar`, `jam_keluar`, `datetime`, `user`, `kategori`) VALUES
	(18, 1, 'TSC20250424231335', '2025-04-24', '23:13:35', '2025-04-24 23:13:35', 'Admin', 'Barang Keluar'),
	(19, 1, 'TSC20250424231813', '2025-04-24', '23:18:13', '2025-04-24 23:18:13', 'Admin', 'Barang Keluar'),
	(20, 1, 'TSC20250424232516', '2025-04-24', '23:25:16', '2025-04-24 23:25:16', 'Admin', 'Barang Keluar');

-- Dumping structure for table db_astachecker.product
CREATE TABLE IF NOT EXISTS `product` (
  `idproduct` int NOT NULL AUTO_INCREMENT,
  `sku` varchar(50) NOT NULL DEFAULT '0',
  `nama_produk` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `gambar` varchar(50) DEFAULT NULL,
  `barcode` varchar(50) DEFAULT NULL,
  `sni` varchar(50) DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_by` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `status` int DEFAULT '1',
  PRIMARY KEY (`idproduct`)
) ENGINE=InnoDB AUTO_INCREMENT=396 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table db_astachecker.product: ~105 rows (approximately)
INSERT INTO `product` (`idproduct`, `sku`, `nama_produk`, `gambar`, `barcode`, `sni`, `created_by`, `created_date`, `updated_by`, `updated_date`, `status`) VALUES
	(291, 'ASPM-150', 'Gilingan Mie / Pasta Maker Jumbo', 'aspm-150.jpg', '100000000001', 'SNI-0001', NULL, NULL, NULL, NULL, 1),
	(292, 'ASPM-115SS', 'Gilingan Mie / Pasta Maker Stainless', 'aspm-115ss.jpg', '100000000002', 'SNI-0002', NULL, NULL, NULL, NULL, 1),
	(293, 'ASPM-115RD', 'Gilingan Mie / Pasta Maker Red', 'aspm-115rd.jpg', '100000000003', 'SNI-0003', NULL, NULL, NULL, NULL, 1),
	(294, 'ASPM-115AM', 'Gilingan Mie / Pasta Maker Ampia', 'aspm-115am.jpg', '100000000004', 'SNI-0004', NULL, NULL, NULL, NULL, 1),
	(295, 'ASMU-01', 'Gilingan Serbaguna Multiuse Mincer', 'asmu-01.jpg', '100000000005', 'SNI-0005', NULL, NULL, NULL, NULL, 1),
	(296, 'ASGD-612BL', 'Glass Dispenser / Dispenser Kaca 1,2 Ltr Blue', 'asgd-612bl.jpg', '100000000006', 'SNI-0006', NULL, NULL, NULL, NULL, 1),
	(297, 'ASGD-612BK', 'Glass Dispenser / Dispenser Kaca 1,2 Ltr Black', 'asgd-612bk.jpg', '100000000007', 'SNI-0007', NULL, NULL, NULL, NULL, 1),
	(298, 'ASGD-612GR', 'Glass Dispenser / Dispenser Kaca 1,2 Ltr Green', 'asgd-612gr.jpg', '100000000008', 'SNI-0008', NULL, NULL, NULL, NULL, 1),
	(299, 'ASGD-612GY', 'Glass Dispenser / Dispenser Kaca 1,2 Ltr Grey', 'asgd-612gy.jpg', '100000000009', 'SNI-0009', NULL, NULL, NULL, NULL, 1),
	(300, 'ASGD-612PR', 'Glass Dispenser / Dispenser Kaca 1,2 Ltr Purple', 'asgd-612pr.jpg', '100000000010', 'SNI-0010', NULL, NULL, NULL, NULL, 1),
	(301, 'ASGD-612RD', 'Glass Dispenser / Dispenser Kaca 1,2 Ltr Red', 'asgd-612rd.jpg', '100000000011', 'SNI-0011', NULL, NULL, NULL, NULL, 1),
	(302, 'ASGD-612WH', 'Glass Dispenser / Dispenser Kaca 1,2 Ltr White', 'asgd-612wh.jpg', '100000000012', 'SNI-0012', NULL, NULL, NULL, NULL, 1),
	(303, 'ASGD-612YL', 'Glass Dispenser / Dispenser Kaca 1,2 Ltr Yellow', 'asgd-612yl.jpg', '100000000013', 'SNI-0013', NULL, NULL, NULL, NULL, 1),
	(304, 'ASGD-620BK', 'Glass Dispenser / Dispenser Kaca 2 Ltr Black', 'asgd-620bk.jpg', '100000000014', 'SNI-0014', NULL, NULL, NULL, NULL, 1),
	(305, 'ASGD-620BL', 'Glass Dispenser / Dispenser Kaca 2 Ltr Blue', 'asgd-620bl.jpg', '100000000015', 'SNI-0015', NULL, NULL, NULL, NULL, 1),
	(306, 'ASGD-620GR', 'Glass Dispenser / Dispenser Kaca 2 Ltr Green', 'asgd-620gr.jpg', '100000000016', 'SNI-0016', NULL, NULL, NULL, NULL, 1),
	(307, 'ASGD-620GY', 'Glass Dispenser / Dispenser Kaca 2 Ltr Grey', 'asgd-620gy.jpg', '100000000017', 'SNI-0017', NULL, NULL, NULL, NULL, 1),
	(308, 'ASGD-620PR', 'Glass Dispenser / Dispenser Kaca 2 Ltr Purple', 'asgd-620pr.jpg', '100000000018', 'SNI-0018', NULL, NULL, NULL, NULL, 1),
	(309, 'ASGD-620RD', 'Glass Dispenser / Dispenser Kaca 2 Ltr Red', 'asgd-620rd.jpg', '100000000019', 'SNI-0019', NULL, NULL, NULL, NULL, 1),
	(310, 'ASGD-620WH', 'Glass Dispenser / Dispenser Kaca 2 Ltr White', 'asgd-620wh.jpg', '100000000020', 'SNI-0020', NULL, NULL, NULL, NULL, 1),
	(311, 'ASGD-620YL', 'Glass Dispenser / Dispenser Kaca 2 Ltr Yellow', 'asgd-620yl.jpg', '100000000021', 'SNI-0021', NULL, NULL, NULL, NULL, 1),
	(312, 'ASGD-639BK', 'Glass Dispenser / Dispenser Kaca 3,9 Ltr Black', 'asgd-639bk.jpg', '100000000022', 'SNI-0022', NULL, NULL, NULL, NULL, 1),
	(313, 'ASGD-639BL', 'Glass Dispenser / Dispenser Kaca 3,9 Ltr Blue', 'asgd-639bl.jpg', '100000000023', 'SNI-0023', NULL, NULL, NULL, NULL, 1),
	(314, 'ASGD-639GR', 'Glass Dispenser / Dispenser Kaca 3,9 Ltr Green', 'asgd-639gr.jpg', '100000000024', 'SNI-0024', NULL, NULL, NULL, NULL, 1),
	(315, 'ASGD-639GY', 'Glass Dispenser / Dispenser Kaca 3,9 Ltr Grey', 'asgd-639gy.jpg', '100000000025', 'SNI-0025', NULL, NULL, NULL, NULL, 1),
	(316, 'ASGD-639PR', 'Glass Dispenser / Dispenser Kaca 3,9 Ltr Purple', 'asgd-639pr.jpg', '100000000026', 'SNI-0026', NULL, NULL, NULL, NULL, 1),
	(317, 'ASGD-639RD', 'Glass Dispenser / Dispenser Kaca 3,9 Ltr Red', 'asgd-639rd.jpg', '100000000027', 'SNI-0027', NULL, NULL, NULL, NULL, 1),
	(318, 'ASGD-639WH', 'Glass Dispenser / Dispenser Kaca 3,9 Ltr White', 'asgd-639wh.jpg', '100000000028', 'SNI-0028', NULL, NULL, NULL, NULL, 1),
	(319, 'ASGD-639YL', 'Glass Dispenser / Dispenser Kaca 3,9 Ltr Yellow', 'asgd-639yl.jpg', '100000000029', 'SNI-0029', NULL, NULL, NULL, NULL, 1),
	(320, 'ASBM-03S', 'Baking Mat / Alas Panggangan Roti', 'asbm-03s.jpg', '100000000030', 'SNI-0030', NULL, NULL, NULL, NULL, 1),
	(321, 'ASBS-01', 'Bakeware Loyang / Cetakan Kue Set 6 in 1 Anti Lengket', 'asbs-01.jpg', '100000000031', 'SNI-0031', NULL, NULL, NULL, NULL, 1),
	(322, 'ASFF-03RF', 'Food Warmer Round dengan Pemasnan', 'asff-03rf.jpg', '100000000032', 'SNI-0032', NULL, NULL, NULL, NULL, 1),
	(323, 'ASFF-04RF', 'Food Warmer Round dengan Pemanas dan Frame', 'asff-04rf.jpg', '100000000033', 'SNI-0033', NULL, NULL, NULL, NULL, 1),
	(324, 'ASFF-01GF', 'Food Warmer Square dengan Prasmanan Wadah Prasmanan', 'asff-01gf.jpg', '100000000034', 'SNI-0034', NULL, NULL, NULL, NULL, 1),
	(325, 'ASSP-01', 'Stock Pot Steamer 01 Panci Sop Besar', 'assp-01.jpg', '100000000035', 'SNI-0035', NULL, NULL, NULL, NULL, 1),
	(326, 'ASSP-02', 'Stock Pot 02 Duo Steamer - Asta', 'assp-02.jpg', '100000000036', 'SNI-0036', NULL, NULL, NULL, NULL, 1),
	(327, 'ASSP-03N', 'Stock Pot European Style 03 Panci Sop Besar', 'assp-03n.jpg', '100000000037', 'SNI-0037', NULL, NULL, NULL, NULL, 1),
	(328, 'ASMP-01', 'Mini Pots / Mini Stock Pots Panci Sop', 'asmp-01.jpg', '100000000038', 'SNI-0038', NULL, NULL, NULL, NULL, 1),
	(329, 'ASNP-01', 'Noodle Pot Panci Susu / Panci Rebus / Milk Pot 18 cm', 'asnp-01.jpg', '100000000039', 'SNI-0039', NULL, NULL, NULL, NULL, 1),
	(330, 'ASNP-02', 'Noodle pot Panci Susu / Panci Rebus / Milk Pot 20 cm', 'asnp-02.jpg', '100000000040', 'SNI-0040', NULL, NULL, NULL, NULL, 1),
	(331, 'ASCW-01', 'Wajan Penggorengan Stainless Steel Chef Wok 32 Cm', 'ascw-01.jpg', '100000000041', 'SNI-0041', NULL, NULL, NULL, NULL, 1),
	(332, 'ASCW-02', 'Wajan Penggorengan Stainless Steel Chef Wok 36 Cm', 'ascw-02.jpg', '100000000042', 'SNI-0042', NULL, NULL, NULL, NULL, 1),
	(333, 'ASCW-03', 'Wajan Penggorengan Stainless Steel Chef Wok 40 Cm', 'ascw-03.jpg', '100000000043', 'SNI-0043', NULL, NULL, NULL, NULL, 1),
	(334, 'ASCW-30', 'Wajan Penggorengan Stainless Steel Chef Wok 30 cm', 'ascw-30.jpg', '100000000044', 'SNI-0044', NULL, NULL, NULL, NULL, 1),
	(335, 'ASOW-30', 'Wajan Penggorengan Stainless Steel Oriental Wok 30 cm', 'asow-30.jpg', '100000000045', 'SNI-0045', NULL, NULL, NULL, NULL, 1),
	(336, 'ASOW-28', 'Wajan Penggorengan Stainless Steel Oriental Wok 28 cm', 'asow-28.jpg', '100000000046', 'SNI-0046', NULL, NULL, NULL, NULL, 1),
	(337, 'ASOW-01', 'Wajan Penggorengan Stainless Steel Oriental Wok 32 Cm', 'asow-01.jpg', '100000000047', 'SNI-0047', NULL, NULL, NULL, NULL, 1),
	(338, 'ASOW-02', 'Wajan Penggorengan Stainless Steel Oriental Wok 36 Cm', 'asow-02.jpg', '100000000048', 'SNI-0048', NULL, NULL, NULL, NULL, 1),
	(339, 'ASOW-03', 'Wajan Penggorengan Stainless Steel Oriental Wok 40 Cm', 'asow-03.jpg', '100000000049', 'SNI-0049', NULL, NULL, NULL, NULL, 1),
	(340, 'ASOW-05', 'Wajan Penggorengan Stainless Steel Oriental Wok 50 Cm', 'asow-05.jpg', '100000000050', 'SNI-0050', NULL, NULL, NULL, NULL, 1),
	(341, 'ASOW-06', 'Wajan Penggorengan Stainless Steel Oriental Wok 60 Cm', 'asow-06.jpg', '100000000051', 'SNI-0051', NULL, NULL, NULL, NULL, 1),
	(342, 'ASFC-01', 'Rantang Stainless Steel Tunggal 14 cm Rantang Bakso Soto', 'asfc-01.jpg', '100000000052', 'SNI-0052', NULL, NULL, NULL, NULL, 1),
	(343, 'ASFC-02', 'Rantang Stainless Steel Tunggal 16cm Rantang Bakso Soto', 'asfc-02.jpg', '100000000053', 'SNI-0053', NULL, NULL, NULL, NULL, 1),
	(344, 'ASOC-01', 'Pelindung Percikan Minyak Oil Spatter 01', 'asoc-01.jpg', '100000000054', 'SNI-0054', NULL, NULL, NULL, NULL, 1),
	(345, 'ASOC-02', 'Pelindung Percikan Minyak Oil Spatter 02', 'asoc-02.jpg', '100000000055', 'SNI-0055', NULL, NULL, NULL, NULL, 1),
	(346, 'ASOC-03', 'Pelindung Percikan Minyak Oil Spatter 03', 'asoc-03.jpg', '100000000056', 'SNI-0056', NULL, NULL, NULL, NULL, 1),
	(347, 'ASOC-04', 'Pelindung Percikan Minyak Oil Spatter 04', 'asoc-04.jpg', '100000000057', 'SNI-0057', NULL, NULL, NULL, NULL, 1),
	(348, 'ASCR-01', 'Panggangan & Tirisan Minyak Crisper Square', 'ascr-01.jpg', '100000000058', 'SNI-0058', NULL, NULL, NULL, NULL, 1),
	(349, 'ASCR-02', 'Panggangan & Tirisan Minyak Crisper Round', 'ascr-02.jpg', '100000000059', 'SNI-0059', NULL, NULL, NULL, NULL, 1),
	(350, 'ASCR-03', 'Panggangan & Tirisan Minyak Crisper Black Noir', 'ascr-03.jpg', '100000000060', 'SNI-0060', NULL, NULL, NULL, NULL, 1),
	(351, 'ASKP-01', 'Panggangan / Grill Pan / Koki Pan Square 01', 'askp-01.jpg', '100000000061', 'SNI-0061', NULL, NULL, NULL, NULL, 1),
	(352, 'ASKP-02', 'Panggangan / Grill Pan / Koki Pan Oishi 02', 'askp-02.jpg', '100000000062', 'SNI-0062', NULL, NULL, NULL, NULL, 1),
	(353, 'ASKP-03', 'Panggangan / Grill Pan / Koki Pan Round 03', 'askp-03.jpg', '100000000063', 'SNI-0063', NULL, NULL, NULL, NULL, 1),
	(354, 'ASPS-02', 'Panci Set Lemona Panci Set Lengkap Anti Lengket', 'asps-02.jpg', '100000000064', 'SNI-0064', NULL, NULL, NULL, NULL, 1),
	(355, 'ASPS-09', 'Panci Set New Kiwiz Panci Set Lengkap Anti Lengket', 'asps-09.jpg', '100000000065', 'SNI-0065', NULL, NULL, NULL, NULL, 1),
	(356, 'ASPS-01', 'Panci Set Marbello Panci Set Lengkap Anti Lengket', 'asps-01.jpg', '100000000066', 'SNI-0066', NULL, NULL, NULL, NULL, 1),
	(357, 'ASPS-05', 'Panci Set Tokyo', 'asps-05.jpg', '100000000067', 'SNI-0067', NULL, NULL, NULL, NULL, 1),
	(358, 'ASPS-07PK', 'Panci Set Valentina Panci Set Lengkap Anti Lengket PINK', 'asps-07pk.jpg', '100000000068', 'SNI-0068', NULL, NULL, NULL, NULL, 1),
	(359, 'ASPS-07PR', 'Panci Set Valentina Panci Set Lengkap Anti Lengket Purple', 'asps-07pr.jpg', '100000000069', 'SNI-0069', NULL, NULL, NULL, NULL, 1),
	(360, 'ASPS-08', 'Panci Set Cappucino Panci Set Lengkap Anti Lengket', 'asps-08.jpg', '100000000070', 'SNI-0070', NULL, NULL, NULL, NULL, 1),
	(361, 'ASPS-10', 'Panci Set Marbello Rosie Panci Set Lengkap Anti Lengket', 'asps-10.jpg', '100000000071', 'SNI-0071', NULL, NULL, NULL, NULL, 1),
	(362, 'ASPS-11', 'Panci Set Marbello Fiesta Panci Set Lengkap Anti Lengket', 'asps-11.jpg', '100000000072', 'SNI-0072', NULL, NULL, NULL, NULL, 1),
	(363, 'ASPS-12', 'Panci Set Valentina Milan Panci Set Lengkap Anti Lengket', 'asps-12.jpg', '100000000073', 'SNI-0073', NULL, NULL, NULL, NULL, 1),
	(364, 'ASPS-15', 'Panci Set Lemona 100 Panci Set Lengkap Anti Lengket', 'asps-15.jpg', '100000000074', 'SNI-0074', NULL, NULL, NULL, NULL, 1),
	(365, 'ASPS-16', 'Panci Set Tempura Panci Set Lengkap Anti Lengket', 'asps-16.jpg', '100000000075', 'SNI-0075', NULL, NULL, NULL, NULL, 1),
	(366, 'ASWK-01', 'Teko / Ketel / Ceret Air Food Grade Valentina Whistling Kettle', 'aswk-01.jpg', '100000000076', 'SNI-0076', NULL, NULL, NULL, NULL, 1),
	(367, 'ASWK-02BL', 'Teko / Ketel / Ceret Air Food Grade Valentina Whistling Kettle Handle Kayu BIRU', 'aswk-02bl.jpg', '100000000077', 'SNI-0077', NULL, NULL, NULL, NULL, 1),
	(368, 'ASWK-02PK', 'Teko / Ketel / Ceret Air Food Grade Valentina Whistling Kettle Handle Kayu PINK', 'aswk-02pk.jpg', '100000000078', 'SNI-0078', NULL, NULL, NULL, NULL, 1),
	(369, 'ASWK-02PR', 'Teko / Ketel / Ceret Air Food Grade Valentina Whistling Kettle Handle Kayu UNGU', 'aswk-02pr.jpg', '100000000079', 'SNI-0079', NULL, NULL, NULL, NULL, 1),
	(370, 'ASWK-03CM', 'Teko / Ketel / Ceret Air Food Grade Valentina Whistling Kettle Marble Handle Kayu CREAM', 'aswk-03cm.jpg', '100000000080', 'SNI-0080', NULL, NULL, NULL, NULL, 1),
	(371, 'ASWK-03BM', 'Teko / Ketel / Ceret Air Food Grade Valentina Whistling Kettle Marble Handle Kayu BLACK', 'aswk-03bm.jpg', '100000000081', 'SNI-0081', NULL, NULL, NULL, NULL, 1),
	(372, 'ASAP-16', 'Ambrosia Panci Susu Milk Pot + Tutup Kaca 16 cm', 'asap-16.jpg', '100000000082', 'SNI-0082', NULL, NULL, NULL, NULL, 1),
	(373, 'ASAP-22', 'Ambrosia Panci Sop Casserole Tutup Kaca', 'asap-22.jpg', '100000000083', 'SNI-0083', NULL, NULL, NULL, NULL, 1),
	(374, 'ASAP-16S', 'Ambrosia Panci Susu Milk Pot + Tutup + Steamer', 'asap-16s.jpg', '100000000084', 'SNI-0084', NULL, NULL, NULL, NULL, 1),
	(375, 'ASSA-01A', 'Spatula Sutil sodet Stainless Steel Motif Marble', 'assa-01a.jpg', '100000000085', 'SNI-0085', NULL, NULL, NULL, NULL, 1),
	(376, 'ASSA-01B', 'Spatula Sutil sodet kipas Stainless Steel Motif Marble', 'assa-01b.jpg', '100000000086', 'SNI-0086', NULL, NULL, NULL, NULL, 1),
	(377, 'ASSA-01C', 'Sendok irus soup ladle Stainless Steel Motif Marble', 'assa-01c.jpg', '100000000087', 'SNI-0087', NULL, NULL, NULL, NULL, 1),
	(378, 'ASSA-01D', 'Sendok Irus Skimmer Stainless Steel Motif Marble', 'assa-01d.jpg', '100000000088', 'SNI-0088', NULL, NULL, NULL, NULL, 1),
	(379, 'ASSA-02A', 'Spatula Sutil sodet Stainless Steel Motif Hitam', 'assa-02a.jpg', '100000000089', 'SNI-0089', NULL, NULL, NULL, NULL, 1),
	(380, 'ASSA-02B', 'Spatula Sutil sodet kipas Stainless Steel Motif Hitam', 'assa-02b.jpg', '100000000090', 'SNI-0090', NULL, NULL, NULL, NULL, 1),
	(381, 'ASSA-02C', 'Sendok irus soup ladle Stainless Steel Motif Hitam', 'assa-02c.jpg', '100000000091', 'SNI-0091', NULL, NULL, NULL, NULL, 1),
	(382, 'ASSA-02D', 'Sendok Irus Skimmer Stainless Steel Motif Hitam', 'assa-02d.jpg', '100000000092', 'SNI-0092', NULL, NULL, NULL, NULL, 1),
	(383, 'ASSA-03A', 'Spatula Sutil sodet Stainless Steel Motif Coklat', 'assa-03a.jpg', '100000000093', 'SNI-0093', NULL, NULL, NULL, NULL, 1),
	(384, 'ASSA-03B', 'Spatula Sutil sodet kipas Stainless Steel Motif Coklat', 'assa-03b.jpg', '100000000094', 'SNI-0094', NULL, NULL, NULL, NULL, 1),
	(385, 'ASSA-03C', 'Sendok irus soup ladle Stainless Steel Motif Coklat', 'assa-03c.jpg', '100000000095', 'SNI-0095', NULL, NULL, NULL, NULL, 1),
	(386, 'ASSA-03D', 'Sendok Irus Skimmer Stainless Steel Motif Coklat', 'assa-03d.jpg', '100000000096', 'SNI-0096', NULL, NULL, NULL, NULL, 1),
	(387, 'ASMB-18', 'Baskom Mangkok Stainless Steel Mixing Bowl Tebal Food Grade 18 cm', 'asmb-18.jpg', '100000000097', 'SNI-0097', NULL, NULL, NULL, NULL, 1),
	(388, 'ASMB-20', 'Baskom Mangkok Stainless Steel Mixing Bowl Tebal Food Grade 20 cm', 'asmb-20.jpg', '100000000098', 'SNI-0098', NULL, NULL, NULL, NULL, 1),
	(389, 'ASMB-22', 'Baskom Mangkok Stainless Steel Mixing Bowl Tebal Food Grade 22 cm', 'asmb-22.jpg', '100000000099', 'SNI-0099', NULL, NULL, NULL, NULL, 1),
	(390, 'ASMB-24', 'Baskom Mangkok Stainless Steel Mixing Bowl Tebal Food Grade 24 cm', 'asmb-24.jpg', '100000000100', 'SNI-0100', NULL, NULL, NULL, NULL, 1),
	(391, 'AECO-0104', 'Asta Cempal Anti Panas Tebal Pelindung Tangan Busa Katun Premium 1 piece Ã¢â‚¬â€œ Flower blue', 'gambar_1745507739.jpg', 'AECO-0104', 'sni_1745497815.jpg', NULL, NULL, NULL, NULL, 0),
	(392, 'AEKA-0602', 'Asta Celemek Masak Apron Kitchen Wanita Panjang Premium Ruffle Ã¢â‚¬â€œ Pink', 'gambar_1745507870.jpg', 'AEKA-0602', 'sni_1745507870.png', NULL, NULL, NULL, NULL, 1),
	(393, 'test01', 'test01', 'gambar_1745566075.jpg', 'test01', 'sni_1745566075.jpg', 'test', '2025-04-25 14:27:55', 'test', '2025-04-25 14:27:55', 1),
	(394, 'test45', 'Gilingan Mie / Pasta Maker Jumbo', 'gambar_1745570531.jpg', 'ASPM-150', 'sni_1745570531.jpg', 'test', '2025-04-25 15:42:11', 'test', '2025-04-25 15:42:11', 1),
	(395, 'test02', 'test02', 'gambar_1745571545.jpg', 'test02213', 'sni_1745571545.jpg', 'test', '2025-04-25 15:59:05', 'test', '2025-04-25 19:48:10', 1);

-- Dumping structure for table db_astachecker.product_stock
CREATE TABLE IF NOT EXISTS `product_stock` (
  `idproduct_stock` int NOT NULL AUTO_INCREMENT,
  `idproduct` int NOT NULL,
  `idgudang` int NOT NULL,
  `stok` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`idproduct_stock`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table db_astachecker.product_stock: ~0 rows (approximately)
INSERT INTO `product_stock` (`idproduct_stock`, `idproduct`, `idgudang`, `stok`) VALUES
	(1, 392, 1, 0),
	(2, 392, 2, 0),
	(3, 291, 1, 49),
	(4, 291, 2, 60),
	(5, 292, 1, 20),
	(6, 292, 2, 50),
	(7, 393, 1, 0),
	(8, 393, 2, 0),
	(9, 394, 1, 0),
	(10, 394, 2, 0),
	(11, 395, 1, 0),
	(12, 395, 2, 0);

-- Dumping structure for table db_astachecker.role
CREATE TABLE IF NOT EXISTS `role` (
  `idrole` int NOT NULL AUTO_INCREMENT,
  `nama_role` varchar(50) DEFAULT NULL,
  `deskripsi` varchar(50) DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_by` varchar(50) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`idrole`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table db_astachecker.role: ~0 rows (approximately)
INSERT INTO `role` (`idrole`, `nama_role`, `deskripsi`, `created_by`, `created_date`, `updated_by`, `updated_date`, `status`) VALUES
	(1, 'Superadmin', 'Superadmin', 'Superadmin', '2025-04-25 14:31:16', 'Superadmin', '2025-04-25 14:31:21', 1),
	(2, 'Admin Gudang', 'Admin Gudang', 'Superadmin', '2025-04-25 14:37:08', 'Superadmin', '2025-04-25 14:37:11', 1),
	(3, 'Admin Stock', 'Admin Stock', 'Admin Stock', '2025-04-25 14:38:08', 'Superadmin', '2025-04-25 14:38:15', 1),
	(4, 'Staff', 'Staff', 'Staff', '2025-04-25 14:38:35', 'Superadmin', '2025-04-25 14:38:41', 1);

-- Dumping structure for table db_astachecker.user
CREATE TABLE IF NOT EXISTS `user` (
  `iduser` int NOT NULL AUTO_INCREMENT,
  `idrole` int DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `foto` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `is_active` int DEFAULT '1',
  `created_by` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_by` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `updated_date` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int DEFAULT '1',
  PRIMARY KEY (`iduser`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table db_astachecker.user: ~1 rows (approximately)
INSERT INTO `user` (`iduser`, `idrole`, `username`, `email`, `foto`, `password`, `full_name`, `is_active`, `created_by`, `created_date`, `updated_by`, `updated_date`, `status`) VALUES
	(4, 1, 'Superadmin', 'Superadmin@gmail.com', 'foto_1745588888.png', '$2y$10$Hpc5o/QfB3CsVGezMUhjdOClU2c16NRCnKvTLxMz3LcqKfONFY.p.', 'Superadmin', 1, NULL, '2025-04-25 20:48:09', NULL, '2025-04-25 20:48:09', 1);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
