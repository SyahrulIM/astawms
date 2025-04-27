-- Dumping structure for table db_astawms.detail_instock
CREATE TABLE IF NOT EXISTS `detail_instock` (
  `iddetail_instock` int NOT NULL AUTO_INCREMENT,
  `instock_code` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sku` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_produk` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `sisa` int DEFAULT NULL,
  `keterangan` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`iddetail_instock`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping structure for table db_astawms.detail_outstock
CREATE TABLE IF NOT EXISTS `detail_outstock` (
  `iddetail_outstock` int NOT NULL AUTO_INCREMENT,
  `outstock_code` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sku` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_produk` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `sisa` int DEFAULT NULL,
  `keterangan` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`iddetail_outstock`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping structure for table db_astawms.gudang
CREATE TABLE IF NOT EXISTS `gudang` (
  `idgudang` int NOT NULL AUTO_INCREMENT,
  `nama_gudang` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`idgudang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping structure for table db_astawms.instock
CREATE TABLE IF NOT EXISTS `instock` (
  `idinstock` int NOT NULL AUTO_INCREMENT,
  `idgudang` int DEFAULT NULL,
  `instock_code` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tgl_terima` date DEFAULT NULL,
  `jam_terima` time DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `user` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kategori` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_by` varchar(200) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  PRIMARY KEY (`idinstock`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping structure for table db_astawms.outstock
CREATE TABLE IF NOT EXISTS `outstock` (
  `idoutstock` int NOT NULL AUTO_INCREMENT,
  `idgudang` int DEFAULT NULL,
  `outstock_code` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tgl_keluar` date DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `user` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kategori` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_by` varchar(200) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  PRIMARY KEY (`idoutstock`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping structure for table db_astawms.product
CREATE TABLE IF NOT EXISTS `product` (
  `idproduct` int NOT NULL AUTO_INCREMENT,
  `sku` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_produk` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gambar` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `barcode` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sni` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_by` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_by` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `status` int DEFAULT 1,
  PRIMARY KEY (`idproduct`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping structure for table db_astawms.product_stock
CREATE TABLE IF NOT EXISTS `product_stock` (
  `idproduct_stock` int NOT NULL AUTO_INCREMENT,
  `idproduct` int NOT NULL,
  `idgudang` int NOT NULL,
  `stok` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`idproduct_stock`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping structure for table db_astawms.role
CREATE TABLE IF NOT EXISTS `role` (
  `idrole` int NOT NULL AUTO_INCREMENT,
  `nama_role` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `deskripsi` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_by` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_by` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`idrole`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping structure for table db_astawms.user
CREATE TABLE IF NOT EXISTS `user` (
  `iduser` int NOT NULL AUTO_INCREMENT,
  `idrole` int DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `foto` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `is_active` int DEFAULT 1,
  `created_by` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_by` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updated_date` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int DEFAULT 1,
  PRIMARY KEY (`iduser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;