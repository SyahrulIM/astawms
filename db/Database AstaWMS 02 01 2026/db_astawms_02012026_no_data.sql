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

-- Dumping structure for table db_astawms_26122025.acc_accurate
CREATE TABLE IF NOT EXISTS `acc_accurate` (
  `idacc_accurate` int NOT NULL AUTO_INCREMENT,
  `iduser` int DEFAULT NULL,
  `created_by` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_by` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`idacc_accurate`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.acc_accurate_detail
CREATE TABLE IF NOT EXISTS `acc_accurate_detail` (
  `idacc_accurate_detail` int NOT NULL AUTO_INCREMENT,
  `idacc_accurate` int DEFAULT NULL,
  `no_faktur` varchar(200) COLLATE armscii8_bin DEFAULT NULL,
  `pay_date` date DEFAULT NULL,
  `total_faktur` int DEFAULT NULL,
  `pay` int DEFAULT NULL,
  `discount` int DEFAULT NULL,
  `payment` int DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `updated_by` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`idacc_accurate_detail`) USING BTREE,
  KEY `idx_accurate_no_faktur` (`no_faktur`)
) ENGINE=InnoDB AUTO_INCREMENT=147432 DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.acc_lazada
CREATE TABLE IF NOT EXISTS `acc_lazada` (
  `idacc_lazada` int NOT NULL AUTO_INCREMENT,
  `iduser` int DEFAULT NULL,
  `excel_type` varchar(50) CHARACTER SET armscii8 COLLATE armscii8_bin DEFAULT NULL,
  `is_kotime` int DEFAULT NULL,
  `created_by` varchar(50) CHARACTER SET armscii8 COLLATE armscii8_bin DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_by` varchar(50) CHARACTER SET armscii8 COLLATE armscii8_bin DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`idacc_lazada`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.acc_lazada_additional
CREATE TABLE IF NOT EXISTS `acc_lazada_additional` (
  `idacc_lazada_additional` int NOT NULL AUTO_INCREMENT,
  `additional_revenue` int DEFAULT NULL,
  `is_kotime` int DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_by` varchar(50) CHARACTER SET armscii8 COLLATE armscii8_bin DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_by` varchar(50) CHARACTER SET armscii8 COLLATE armscii8_bin DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`idacc_lazada_additional`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.acc_lazada_detail
CREATE TABLE IF NOT EXISTS `acc_lazada_detail` (
  `idacc_lazada_detail` int NOT NULL AUTO_INCREMENT,
  `idacc_lazada` int DEFAULT NULL,
  `no_faktur` varchar(50) CHARACTER SET armscii8 COLLATE armscii8_bin DEFAULT NULL,
  `order_date` date DEFAULT NULL,
  `pay_date` date DEFAULT NULL,
  `total_faktur` int DEFAULT NULL,
  `pay` int DEFAULT NULL,
  `discount` int DEFAULT NULL,
  `payment` int DEFAULT NULL,
  `refund` int DEFAULT NULL,
  `note` varchar(255) CHARACTER SET armscii8 COLLATE armscii8_bin DEFAULT NULL,
  `is_check` int DEFAULT NULL,
  `status_dir` varchar(50) CHARACTER SET armscii8 COLLATE armscii8_bin DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` varchar(50) CHARACTER SET armscii8 COLLATE armscii8_bin DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `updated_by` varchar(50) CHARACTER SET armscii8 COLLATE armscii8_bin DEFAULT NULL,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`idacc_lazada_detail`) USING BTREE,
  KEY `idx_lazada_pay_date` (`pay_date`) USING BTREE,
  KEY `idx_lazada_order_date` (`order_date`) USING BTREE,
  KEY `idx_lazada_no_faktur` (`no_faktur`) USING BTREE,
  KEY `idx_lazada_refund` (`refund`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.acc_lazada_detail_details
CREATE TABLE IF NOT EXISTS `acc_lazada_detail_details` (
  `idacc_lazada_detail_details` int NOT NULL AUTO_INCREMENT,
  `no_faktur` varchar(50) CHARACTER SET armscii8 COLLATE armscii8_bin DEFAULT NULL,
  `sku` varchar(50) CHARACTER SET armscii8 COLLATE armscii8_bin DEFAULT NULL,
  `name_product` varchar(50) CHARACTER SET armscii8 COLLATE armscii8_bin DEFAULT NULL,
  `price_after_discount` int DEFAULT NULL,
  `address` varchar(225) CHARACTER SET armscii8 COLLATE armscii8_bin DEFAULT NULL,
  `pos_code` int DEFAULT NULL,
  `created_by` varchar(50) CHARACTER SET armscii8 COLLATE armscii8_bin DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_by` varchar(50) CHARACTER SET armscii8 COLLATE armscii8_bin DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`idacc_lazada_detail_details`) USING BTREE,
  KEY `idx_details_no_faktur` (`no_faktur`) USING BTREE,
  KEY `idx_details_poscode` (`pos_code`) USING BTREE,
  KEY `idx_lazada_details_no_faktur` (`no_faktur`) USING BTREE,
  KEY `idx_lazada_details_sku` (`sku`) USING BTREE,
  KEY `idx_details_sku` (`sku`) USING BTREE,
  KEY `idx_details_faktur` (`no_faktur`) USING BTREE,
  KEY `idx_details_pos` (`pos_code`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.acc_shopee
CREATE TABLE IF NOT EXISTS `acc_shopee` (
  `idacc_shopee` int NOT NULL AUTO_INCREMENT,
  `iduser` int DEFAULT NULL,
  `excel_type` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `is_kotime` int DEFAULT NULL,
  `created_by` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_by` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`idacc_shopee`)
) ENGINE=InnoDB AUTO_INCREMENT=168 DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.acc_shopee_additional
CREATE TABLE IF NOT EXISTS `acc_shopee_additional` (
  `idacc_shopee_additional` int NOT NULL AUTO_INCREMENT,
  `additional_revenue` int DEFAULT NULL,
  `is_kotime` int DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_by` datetime DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_by` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`idacc_shopee_additional`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.acc_shopee_bottom
CREATE TABLE IF NOT EXISTS `acc_shopee_bottom` (
  `idacc_shopee_bottom` int NOT NULL AUTO_INCREMENT,
  `sku` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `price_bottom` int DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `updated_by` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`idacc_shopee_bottom`),
  KEY `idx_bottom_sku` (`sku`)
) ENGINE=InnoDB AUTO_INCREMENT=701 DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.acc_shopee_detail
CREATE TABLE IF NOT EXISTS `acc_shopee_detail` (
  `idacc_shopee_detail` int NOT NULL AUTO_INCREMENT,
  `idacc_shopee` int DEFAULT NULL,
  `no_faktur` varchar(200) COLLATE armscii8_bin DEFAULT NULL,
  `order_date` date DEFAULT NULL,
  `pay_date` date DEFAULT NULL,
  `total_faktur` int DEFAULT NULL,
  `pay` int DEFAULT NULL,
  `discount` int DEFAULT NULL,
  `payment` int DEFAULT NULL,
  `refund` int DEFAULT NULL,
  `note` varchar(255) COLLATE armscii8_bin DEFAULT NULL,
  `is_check` int DEFAULT NULL,
  `status_dir` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `updated_by` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`idacc_shopee_detail`),
  KEY `idx_detail_nofaktur` (`no_faktur`),
  KEY `idx_detail_orderdate` (`order_date`),
  KEY `idx_shopee_pay_date` (`pay_date`),
  KEY `idx_shopee_order_date` (`order_date`),
  KEY `idx_shopee_no_faktur` (`no_faktur`),
  KEY `idx_shopee_refund` (`refund`),
  KEY `idx_shopee_faktur_date` (`no_faktur`,`order_date`)
) ENGINE=InnoDB AUTO_INCREMENT=202757 DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.acc_shopee_detail_details
CREATE TABLE IF NOT EXISTS `acc_shopee_detail_details` (
  `idacc_shopee_detail_details` int NOT NULL AUTO_INCREMENT,
  `no_faktur` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `sku` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `name_product` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `price_after_discount` int DEFAULT NULL,
  `address` varchar(225) COLLATE armscii8_bin DEFAULT NULL,
  `pos_code` int DEFAULT NULL,
  `created_by` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_by` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`idacc_shopee_detail_details`),
  KEY `idx_details_no_faktur` (`no_faktur`),
  KEY `idx_details_poscode` (`pos_code`),
  KEY `idx_shopee_details_no_faktur` (`no_faktur`),
  KEY `idx_shopee_details_sku` (`sku`),
  KEY `idx_details_sku` (`sku`),
  KEY `idx_details_faktur` (`no_faktur`),
  KEY `idx_details_pos` (`pos_code`)
) ENGINE=InnoDB AUTO_INCREMENT=161929 DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.acc_tiktok
CREATE TABLE IF NOT EXISTS `acc_tiktok` (
  `idacc_tiktok` int NOT NULL AUTO_INCREMENT,
  `iduser` int DEFAULT NULL,
  `excel_type` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `is_kotime` int DEFAULT NULL,
  `created_by` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_by` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`idacc_tiktok`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=116 DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.acc_tiktok_additional
CREATE TABLE IF NOT EXISTS `acc_tiktok_additional` (
  `idacc_tiktok_additional` int NOT NULL AUTO_INCREMENT,
  `additional_revenue` int DEFAULT NULL,
  `is_kotime` int DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_by` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_by` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`idacc_tiktok_additional`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.acc_tiktok_detail
CREATE TABLE IF NOT EXISTS `acc_tiktok_detail` (
  `idacc_tiktok_detail` int NOT NULL AUTO_INCREMENT,
  `idacc_tiktok` int DEFAULT NULL,
  `no_faktur` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `order_date` date DEFAULT NULL,
  `pay_date` date DEFAULT NULL,
  `total_faktur` int DEFAULT NULL,
  `pay` int DEFAULT NULL,
  `discount` int DEFAULT NULL,
  `payment` int DEFAULT NULL,
  `refund` int DEFAULT NULL,
  `note` varchar(255) COLLATE armscii8_bin DEFAULT NULL,
  `is_check` int DEFAULT NULL,
  `status_dir` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `updated_by` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`idacc_tiktok_detail`) USING BTREE,
  KEY `idx_tiktok_pay_date` (`pay_date`),
  KEY `idx_tiktok_order_date` (`order_date`),
  KEY `idx_tiktok_no_faktur` (`no_faktur`),
  KEY `idx_tiktok_refund` (`refund`)
) ENGINE=InnoDB AUTO_INCREMENT=4207 DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.acc_tiktok_detail_details
CREATE TABLE IF NOT EXISTS `acc_tiktok_detail_details` (
  `idacc_tiktok_detail_details` int NOT NULL AUTO_INCREMENT,
  `no_faktur` varchar(255) COLLATE armscii8_bin DEFAULT NULL,
  `sku` varchar(255) COLLATE armscii8_bin DEFAULT NULL,
  `name_product` varchar(255) COLLATE armscii8_bin DEFAULT NULL,
  `price_after_discount` int DEFAULT NULL,
  `address` varchar(255) COLLATE armscii8_bin DEFAULT NULL,
  `pos_code` int DEFAULT NULL,
  `created_by` varchar(255) COLLATE armscii8_bin DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  `updated_by` varchar(255) COLLATE armscii8_bin DEFAULT NULL,
  `updated_date` date DEFAULT NULL,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`idacc_tiktok_detail_details`) USING BTREE,
  KEY `idx_tiktok_details_no_faktur` (`no_faktur`),
  KEY `idx_tiktok_details_sku` (`sku`)
) ENGINE=InnoDB AUTO_INCREMENT=11794 DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.analisys_po
CREATE TABLE IF NOT EXISTS `analisys_po` (
  `idanalisys_po` int NOT NULL AUTO_INCREMENT,
  `number_po` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_manual` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_date` date DEFAULT NULL,
  `order_time` date DEFAULT NULL,
  `distribution_date` datetime DEFAULT NULL,
  `name_container` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_progress` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `money_currency` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_supplier` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idwarehouse` int DEFAULT NULL,
  `kategori` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idgudang` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_verification` int DEFAULT NULL,
  `created_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `status` tinyint DEFAULT NULL,
  PRIMARY KEY (`idanalisys_po`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.city
CREATE TABLE IF NOT EXISTS `city` (
  `idcity` int DEFAULT NULL,
  `city_name` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `idprovince` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.ci_sessions
CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `id` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.customer
CREATE TABLE IF NOT EXISTS `customer` (
  `idcustomer` int NOT NULL AUTO_INCREMENT,
  `name_customer` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `email` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `handphone` int DEFAULT NULL,
  `foto` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `created_by` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `update_by` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`idcustomer`)
) ENGINE=InnoDB DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.delivery_file
CREATE TABLE IF NOT EXISTS `delivery_file` (
  `iddelivery_file` int NOT NULL AUTO_INCREMENT,
  `name_supplier` varchar(200) COLLATE armscii8_bin NOT NULL,
  `foto` varchar(200) COLLATE armscii8_bin NOT NULL,
  `date_received` date NOT NULL,
  `iduser` int NOT NULL,
  `created_by` varchar(200) COLLATE armscii8_bin NOT NULL,
  `created_date` datetime NOT NULL,
  `updated_by` varchar(200) COLLATE armscii8_bin NOT NULL,
  `updated_date` datetime NOT NULL,
  `kategori` int NOT NULL,
  `status` int NOT NULL,
  PRIMARY KEY (`iddelivery_file`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.delivery_note
CREATE TABLE IF NOT EXISTS `delivery_note` (
  `iddelivery_note` int NOT NULL AUTO_INCREMENT,
  `no_manual` varchar(200) COLLATE armscii8_bin NOT NULL,
  `foto` varchar(200) COLLATE armscii8_bin NOT NULL,
  `send_date` datetime NOT NULL,
  `iduser` int NOT NULL,
  `created_by` varchar(200) COLLATE armscii8_bin NOT NULL,
  `created_date` datetime NOT NULL,
  `updated_by` varchar(200) COLLATE armscii8_bin NOT NULL,
  `updated_date` datetime NOT NULL,
  `kategori` int NOT NULL,
  `status` int NOT NULL,
  PRIMARY KEY (`iddelivery_note`)
) ENGINE=InnoDB AUTO_INCREMENT=715 DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.delivery_note_log
CREATE TABLE IF NOT EXISTS `delivery_note_log` (
  `iddelivery_note_log` int NOT NULL AUTO_INCREMENT,
  `iddelivery_note` int NOT NULL,
  `progress` int NOT NULL,
  `description` varchar(255) COLLATE armscii8_bin DEFAULT NULL,
  `created_by` varchar(50) COLLATE armscii8_bin NOT NULL,
  `created_date` datetime NOT NULL,
  `status_revision` varchar(50) COLLATE armscii8_bin NOT NULL DEFAULT '',
  `status` varchar(50) COLLATE armscii8_bin NOT NULL,
  PRIMARY KEY (`iddelivery_note_log`) USING BTREE,
  KEY `iddelivery_note` (`iddelivery_note`)
) ENGINE=InnoDB AUTO_INCREMENT=2797 DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.detail_analisys_po
CREATE TABLE IF NOT EXISTS `detail_analisys_po` (
  `iddetail_analisys_po` int NOT NULL AUTO_INCREMENT,
  `idanalisys_po` int NOT NULL,
  `idproduct` int DEFAULT NULL,
  `product_name_en` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_sgs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_unit` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latest_incoming_stock_mouth` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latest_incoming_stock_pcs` int DEFAULT NULL,
  `last_mouth_sales` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_month_sales` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_stock` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sale_week_one` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sale_week_two` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sale_week_three` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sale_week_four` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `balance_per_today` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qty_order` int DEFAULT NULL,
  `price` int DEFAULT NULL,
  `qty_receive` int DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`iddetail_analisys_po`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.detail_instock
CREATE TABLE IF NOT EXISTS `detail_instock` (
  `iddetail_instock` int NOT NULL AUTO_INCREMENT,
  `instock_code` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sku` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_produk` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `sisa` int DEFAULT NULL,
  `keterangan` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`iddetail_instock`)
) ENGINE=InnoDB AUTO_INCREMENT=630 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.detail_outstock
CREATE TABLE IF NOT EXISTS `detail_outstock` (
  `iddetail_outstock` int NOT NULL AUTO_INCREMENT,
  `outstock_code` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sku` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_produk` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `sisa` int DEFAULT NULL,
  `keterangan` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`iddetail_outstock`)
) ENGINE=InnoDB AUTO_INCREMENT=13168 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.district
CREATE TABLE IF NOT EXISTS `district` (
  `iddistrict` int DEFAULT NULL,
  `district_name` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `idcity` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.gudang
CREATE TABLE IF NOT EXISTS `gudang` (
  `idgudang` int NOT NULL AUTO_INCREMENT,
  `nama_gudang` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`idgudang`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.instock
CREATE TABLE IF NOT EXISTS `instock` (
  `idinstock` int NOT NULL AUTO_INCREMENT,
  `idgudang` int DEFAULT NULL,
  `instock_code` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tgl_terima` date DEFAULT NULL,
  `jam_terima` time DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `user` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kategori` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `no_manual` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `distribution_date` date DEFAULT NULL,
  `created_by` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `status_verification` int DEFAULT NULL,
  PRIMARY KEY (`idinstock`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.outstock
CREATE TABLE IF NOT EXISTS `outstock` (
  `idoutstock` int NOT NULL AUTO_INCREMENT,
  `idgudang` int DEFAULT NULL,
  `outstock_code` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tgl_keluar` date DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `user` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kategori` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `no_manual` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `outstock_type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `distribution_date` date DEFAULT NULL,
  `created_by` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `status_verification` int DEFAULT NULL,
  PRIMARY KEY (`idoutstock`)
) ENGINE=InnoDB AUTO_INCREMENT=373 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.postal_code
CREATE TABLE IF NOT EXISTS `postal_code` (
  `postal_id` int DEFAULT NULL,
  `subdis_id` int DEFAULT NULL,
  `dis_id` int DEFAULT NULL,
  `city_id` int DEFAULT NULL,
  `prov_id` int DEFAULT NULL,
  `pos_code` int DEFAULT NULL,
  `subdis_name` varchar(512) COLLATE armscii8_bin DEFAULT NULL,
  `dis_name` varchar(512) COLLATE armscii8_bin DEFAULT NULL,
  `city_name` varchar(512) COLLATE armscii8_bin DEFAULT NULL,
  `prov_name` varchar(512) COLLATE armscii8_bin DEFAULT NULL,
  KEY `idx_postalcode_poscode` (`pos_code`),
  KEY `idx_postalcode_prov` (`prov_id`,`prov_name`),
  KEY `idx_postalcode_city` (`city_id`),
  KEY `idx_postalcode_provid` (`prov_id`)
) ENGINE=InnoDB DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.ppl_bank_password
CREATE TABLE IF NOT EXISTS `ppl_bank_password` (
  `idppl_bank_password` int NOT NULL AUTO_INCREMENT,
  `account` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `browser` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `verification` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_by` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_by` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `status` int DEFAULT NULL,
  `idppl_devices` int DEFAULT NULL,
  PRIMARY KEY (`idppl_bank_password`)
) ENGINE=InnoDB AUTO_INCREMENT=183 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.ppl_devices
CREATE TABLE IF NOT EXISTS `ppl_devices` (
  `idppl_devices` int NOT NULL AUTO_INCREMENT,
  `devices` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idppl_devices`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.ppl_devices_bank_password
CREATE TABLE IF NOT EXISTS `ppl_devices_bank_password` (
  `idppl_devices_bank_password` int NOT NULL AUTO_INCREMENT,
  `idppl_devices` int NOT NULL,
  `idppl_bank_password` int NOT NULL,
  PRIMARY KEY (`idppl_devices_bank_password`),
  KEY `fk_devices` (`idppl_devices`),
  KEY `fk_devices_bank_password` (`idppl_bank_password`)
) ENGINE=MyISAM AUTO_INCREMENT=107 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.ppl_employee
CREATE TABLE IF NOT EXISTS `ppl_employee` (
  `idppl_employee` int DEFAULT NULL,
  `no_excel` int DEFAULT NULL,
  `name` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `iduser` int DEFAULT NULL,
  `place` varchar(50) COLLATE armscii8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.ppl_pic_bank_password
CREATE TABLE IF NOT EXISTS `ppl_pic_bank_password` (
  `idppl_pic_bank_password` int NOT NULL AUTO_INCREMENT,
  `idppl_bank_password` int DEFAULT NULL,
  `iduser` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`idppl_pic_bank_password`)
) ENGINE=InnoDB AUTO_INCREMENT=882 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.ppl_presence
CREATE TABLE IF NOT EXISTS `ppl_presence` (
  `idppl_presence` int NOT NULL AUTO_INCREMENT,
  `place` varchar(50) COLLATE armscii8_bin NOT NULL DEFAULT '0',
  `month` int NOT NULL DEFAULT (0),
  `year` int NOT NULL DEFAULT '0',
  `created_by` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`idppl_presence`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.ppl_presence_detail
CREATE TABLE IF NOT EXISTS `ppl_presence_detail` (
  `idppl_presence_detail` int NOT NULL AUTO_INCREMENT,
  `idppl_presence` int NOT NULL,
  `no_excel` int DEFAULT NULL,
  `date` date DEFAULT NULL,
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `reason` varchar(255) COLLATE armscii8_bin DEFAULT NULL,
  `is_permission` int DEFAULT NULL,
  `place` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `is_edit` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`idppl_presence_detail`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1427 DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.ppl_time_off
CREATE TABLE IF NOT EXISTS `ppl_time_off` (
  `idppl_time_off` int NOT NULL AUTO_INCREMENT,
  `iduser` int NOT NULL DEFAULT '0',
  `reason` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `description` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `date` date DEFAULT NULL,
  `is_verify` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`idppl_time_off`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.product
CREATE TABLE IF NOT EXISTS `product` (
  `idproduct` int NOT NULL AUTO_INCREMENT,
  `sku` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_produk` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gambar` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `barcode` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sni` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_by` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_by` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `status` int DEFAULT '1',
  PRIMARY KEY (`idproduct`),
  KEY `idx_product_sku` (`sku`)
) ENGINE=InnoDB AUTO_INCREMENT=715 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.product_stock
CREATE TABLE IF NOT EXISTS `product_stock` (
  `idproduct_stock` int NOT NULL AUTO_INCREMENT,
  `idproduct` int NOT NULL,
  `idgudang` int NOT NULL,
  `stok` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`idproduct_stock`)
) ENGINE=InnoDB AUTO_INCREMENT=789 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.province
CREATE TABLE IF NOT EXISTS `province` (
  `idprovince` int DEFAULT NULL,
  `province_name` varchar(50) COLLATE armscii8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.role
CREATE TABLE IF NOT EXISTS `role` (
  `idrole` int NOT NULL AUTO_INCREMENT,
  `nama_role` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `deskripsi` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_by` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_by` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`idrole`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.subdistrict
CREATE TABLE IF NOT EXISTS `subdistrict` (
  `idsubdistrict` int DEFAULT NULL,
  `subdistrict_name` varchar(50) COLLATE armscii8_bin DEFAULT NULL,
  `iddistrict` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin;

-- Data exporting was unselected.

-- Dumping structure for table db_astawms_26122025.user
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
  `idppl_devices` int DEFAULT NULL,
  `birth_date` datetime DEFAULT NULL,
  `created_by` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_by` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updated_date` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int DEFAULT '1',
  PRIMARY KEY (`iduser`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
