-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 28, 2025 at 04:34 PM
-- Server version: 10.3.39-MariaDB-cll-lve
-- PHP Version: 8.1.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `astahome_wms`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_instock`
--

CREATE TABLE `detail_instock` (
  `iddetail_instock` int(11) NOT NULL,
  `instock_code` varchar(200) DEFAULT NULL,
  `sku` varchar(200) DEFAULT NULL,
  `nama_produk` varchar(200) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `sisa` int(11) DEFAULT NULL,
  `keterangan` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_outstock`
--

CREATE TABLE `detail_outstock` (
  `iddetail_outstock` int(11) NOT NULL,
  `outstock_code` varchar(200) DEFAULT NULL,
  `sku` varchar(200) DEFAULT NULL,
  `nama_produk` varchar(200) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `sisa` int(11) DEFAULT NULL,
  `keterangan` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gudang`
--

CREATE TABLE `gudang` (
  `idgudang` int(11) NOT NULL,
  `nama_gudang` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gudang`
--

INSERT INTO `gudang` (`idgudang`, `nama_gudang`) VALUES
(1, 'Gudang Krian');

-- --------------------------------------------------------

--
-- Table structure for table `instock`
--

CREATE TABLE `instock` (
  `idinstock` int(11) NOT NULL,
  `idgudang` int(11) DEFAULT NULL,
  `instock_code` varchar(200) DEFAULT NULL,
  `tgl_terima` date DEFAULT NULL,
  `jam_terima` time DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `user` varchar(200) DEFAULT NULL,
  `kategori` varchar(200) DEFAULT NULL,
  `created_by` varchar(200) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `outstock`
--

CREATE TABLE `outstock` (
  `idoutstock` int(11) NOT NULL,
  `idgudang` int(11) DEFAULT NULL,
  `outstock_code` varchar(200) DEFAULT NULL,
  `tgl_keluar` date DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `user` varchar(200) DEFAULT NULL,
  `kategori` varchar(200) DEFAULT NULL,
  `created_by` varchar(200) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `idproduct` int(11) NOT NULL,
  `sku` varchar(200) DEFAULT NULL,
  `nama_produk` varchar(200) DEFAULT NULL,
  `gambar` varchar(200) DEFAULT NULL,
  `barcode` varchar(200) DEFAULT NULL,
  `sni` varchar(200) DEFAULT NULL,
  `created_by` varchar(200) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_by` varchar(200) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`idproduct`, `sku`, `nama_produk`, `gambar`, `barcode`, `sni`, `created_by`, `created_date`, `updated_by`, `updated_date`, `status`) VALUES
(1, 'AECK-0101', 'Cempal Kotak White Beige - Asta', '', 'AECK-0101', '', NULL, NULL, 'Superadmin', '2025-04-26 18:11:53', 1),
(2, 'AECO-0101', 'Cempal Oval Flower Green - Asta', '', 'AECO-0101', '', NULL, NULL, NULL, NULL, 1),
(3, 'AECO-0102', 'Cempal Oval White Beige - Asta', '', 'AECO-0102', '', NULL, NULL, NULL, NULL, 1),
(4, 'AECO-0103', 'Cempal Oval Peach - Asta', '', 'AECO-0103', '', NULL, NULL, NULL, NULL, 1),
(5, 'AECO-0104', 'Cempal Oval Flower Blue - Asta', '', 'AECO-0104', '', NULL, NULL, NULL, NULL, 1),
(6, 'AECO-0105', 'Cempal Oval Flower Purple - Asta', '', 'AECO-0105', '', NULL, NULL, NULL, NULL, 1),
(7, 'AECO-0106', 'Cempal Oval Flower Purple- Asta', '', 'AECO-0106', '', NULL, NULL, NULL, NULL, 1),
(8, 'AECO-0201', 'Cempal Oval Flower Blue in Pink Lining - Asta', '', 'AECO-0201', '', NULL, NULL, NULL, NULL, 1),
(9, 'AECO-0201+AEHG-0201', 'Sarung Tangan 0201 + Cempal 0201', '', 'AECO-0201+AEHG-0201', '', NULL, NULL, 'Superadmin', '2025-04-26 18:29:26', 1),
(10, 'AECO-0202', 'Cempal Oval Flower Pink in Mint Lining - Asta', '', 'AECO-0202', '', NULL, NULL, NULL, NULL, 1),
(11, 'AECO-0202+AEHG-0202', 'Sarung Tangan 0202 + Cempal 0202', '', 'AECO-0202+AEHG-0202', '', NULL, NULL, 'Superadmin', '2025-04-26 18:29:43', 1),
(12, 'AECO-0203', 'Cempal Oval Flower Purple in Mint Lining  - Asta', '', 'AECO-0203', '', NULL, NULL, NULL, NULL, 1),
(13, 'AECO-0301', 'Cempal Sarung Oval Flower Green in Green Lining - Asta', '', 'AECO-0301', '', NULL, NULL, NULL, NULL, 1),
(14, 'AECO-0302', 'Cempal Sarung Oval Flower Pink in Mint Lining - Asta', '', 'AECO-0302', '', NULL, NULL, NULL, NULL, 1),
(15, 'AECO-0303', 'Cempal Sarung Oval Flower Purple in Pink Lining - Asta', '', 'AECO-0303', '', NULL, NULL, NULL, NULL, 1),
(16, 'AECO-0304', 'Cempal Sarung Oval Flower Purple in Purple Lining - Asta', '', 'AECO-0304', '', NULL, NULL, NULL, NULL, 1),
(17, 'AECO-0305', 'Cempal Sarung Oval Flower Blue in Pink Lining - Asta', '', 'AECO-0305', '', NULL, NULL, NULL, NULL, 1),
(18, 'AECO-0306', 'Cempal Sarung Oval Flower Pink ini Green Lining - Asta', '', 'AECO-0306', '', NULL, NULL, NULL, NULL, 1),
(19, 'AECO-0307', 'Cempal Sarung Oval Flower Red in Red Lining - Asta', '', 'AECO-0307', '', NULL, NULL, NULL, NULL, 1),
(20, 'AEHG-0101', 'Sarung Tangan Flower Purple - Asta', '', 'AEHG-0101', '', NULL, NULL, NULL, NULL, 1),
(21, 'AEHG-0102', 'Sarung Tangan Flower Blue - Asta', '', 'AEHG-0102', '', NULL, NULL, NULL, NULL, 1),
(22, 'AEHG-0103', 'arung Tangan Flower Purple 2 - Ast', '', 'AEHG-0103', '', NULL, NULL, NULL, NULL, 1),
(23, 'AEHG-0201', 'Sarung Tangan Flower Blue in Pink lining - Asta', '', 'AEHG-0201', '', NULL, NULL, NULL, NULL, 1),
(24, 'AEHG-0202', 'Sarung Tangan Flower Pink in Mint lining - Asta', '', 'AEHG-0202', '', NULL, NULL, NULL, NULL, 1),
(25, 'AEHG-0203', 'Sarung Tangan Flower Purple in Mint lining - Asta', '', 'AEHG-0203', '', NULL, NULL, NULL, NULL, 1),
(26, 'AEHG-0301', 'Sarung Tangan Oven Panjang Flower Green in Green lining - Asta', '', 'AEHG-0301', '', NULL, NULL, NULL, NULL, 1),
(27, 'AEHG-0302', 'Sarung Tangan Oven Panjang Flower Purple in Pink lining - Asta', '', 'AEHG-0302', '', NULL, NULL, NULL, NULL, 1),
(28, 'AEHG-0303', 'Sarung Tangan Oven Panjang Flower Purple in Purple lining - Asta', '', 'AEHG-0303', '', NULL, NULL, NULL, NULL, 1),
(29, 'AEHG-0304', 'Sarung Tangan Oven Panjang Flower Pink in Green lining - Asta', '', 'AEHG-0304', '', NULL, NULL, NULL, NULL, 1),
(30, 'AEHG-0305', 'Sarung Tangan Oven Panjang Flower Red in Red lining - Asta', '', 'AEHG-0305', '', NULL, NULL, NULL, NULL, 1),
(31, 'AEKA-0201', 'Kitchen Apron Garis Biru - Asta', '', 'AEKA-0201', '', NULL, NULL, NULL, NULL, 1),
(32, 'AEKA-0202', 'Kitchen Apron Kotak Hijau - Asta', '', 'AEKA-0202', '', NULL, NULL, NULL, NULL, 1),
(33, 'AEKA-0203', 'Kitchen Apron Lemon - Asta', '', 'AEKA-0203', '', NULL, NULL, NULL, NULL, 1),
(34, 'AEKA-0601', 'Kitchen Apron Ruffle Beige - Asta', '', 'AEKA-0601', '', NULL, NULL, NULL, NULL, 1),
(35, 'AEKA-0602', 'Kitchen Apron Ruffle Pink- Asta', '', 'AEKA-0602', '', NULL, NULL, NULL, NULL, 1),
(36, 'AEKA-0603', 'Kitchen Apron Ruffle Navy - Asta', '', 'AEKA-0603', '', NULL, NULL, NULL, NULL, 1),
(37, 'AEKA-0604', 'Kitchen Apron Ruffle  Mint- Asta', '', 'AEKA-0604', '', NULL, NULL, NULL, NULL, 1),
(38, 'AKFP-01', 'Panci Penggorengan Premium CS Koch Fry Pan 24 cm', '', 'AKFP-01', '', NULL, NULL, NULL, NULL, 1),
(39, 'AKWO-01', 'Panci Wok Premium + Tutup Kaca CS Koch Wok 32 cm', '', 'AKWO-01', '', NULL, NULL, NULL, NULL, 1),
(40, 'ANCA-01', 'Panci Casserole Premium Neoflam Casserole 24 cm', '', 'ANCA-01', '', NULL, NULL, NULL, NULL, 1),
(41, 'ASAP-16', 'Ambrosia Panci Susu Milk Pot + Tutup Kaca 16 cm', '', 'ASAP-16', '', NULL, NULL, NULL, NULL, 1),
(42, 'ASAP-16+ASSL-01BL', 'Ambrosia panci susu biru + soup ladle biru', '', 'ASAP-16+ASSL-01BL', '', NULL, NULL, NULL, NULL, 1),
(43, 'ASAP-16S', 'Ambrosia Panci Susu Milk Pot + Tutup + Steamer', '', 'ASAP-16S', '', NULL, NULL, NULL, NULL, 1),
(44, 'ASAP-16S+ASSL-01PK', 'Ambrosia pink + Soup ladle pink', '', 'ASAP-16S+ASSL-01PK', '', NULL, NULL, NULL, NULL, 1),
(45, 'ASAP-22', 'Ambrosia Panci Sop Casserole Tutup Kaca', '', 'ASAP-22', '', NULL, NULL, NULL, NULL, 1),
(46, 'ASAP-22+ASSL-01BL', 'ambrosia soup pan 22 + soup ladle biru', '', 'ASAP-22+ASSL-01BL', '', NULL, NULL, NULL, NULL, 1),
(47, 'ASBA-01', 'Bastra Baskom Parut Stainless Steel Baskom Serbaguna Multifungsi', '', 'ASBA-01', '', NULL, NULL, NULL, NULL, 1),
(48, 'ASBM-03S', 'Baking Mat / Alas Panggangan Roti', '', 'ASBM-03S', '', NULL, NULL, NULL, NULL, 1),
(49, 'ASBS-01', 'Bakeware Loyang / Cetakan Kue Set 6 in 1 Anti Lengket', '', 'ASBS-01', '', NULL, NULL, NULL, NULL, 1),
(50, 'ASBU-M01GY', 'New Bunda Mop ABU', '', 'ASBU-M01GY', '', NULL, NULL, NULL, NULL, 1),
(51, 'ASBU-M01SC', 'New Bunda Mop Coklat', '', 'ASBU-M01SC', '', NULL, NULL, NULL, NULL, 1),
(52, 'ASBU-M01TQ', 'New Bunda Mop Biru', '', 'ASBU-M01TQ', '', NULL, NULL, NULL, NULL, 1),
(53, 'ASBU-M02WH', 'Bunda Mop Ekonomis White - Asta', '', 'ASBU-M02WH', '', NULL, NULL, NULL, NULL, 1),
(54, 'ASBU-MS1', 'Tongkat Pel Grey Mop Set + Kain Refill Mop', '', 'ASBU-MS1', '', NULL, NULL, NULL, NULL, 1),
(55, 'ASBU-WP35', 'Floor Wiper 35 cm - Asta', '', 'ASBU-WP35', '', NULL, NULL, NULL, NULL, 1),
(56, 'ASBU-WP50', 'Floor Wiper 50 cm - Asta', '', 'ASBU-WP50', '', NULL, NULL, NULL, NULL, 1),
(57, 'ASCB-01GR', 'Talenan Kayu Premium + Ulekan Talenan Kayu Inovasi Baru HIJAU', '', 'ASCB-01GR', '', NULL, NULL, NULL, NULL, 1),
(58, 'ASCB-01PK', 'Talenan Kayu Premium + Ulekan Talenan Kayu Inovasi Baru PINK', '', 'ASCB-01PK', '', NULL, NULL, NULL, NULL, 1),
(59, 'ASCB-01PR', 'Talenan Kayu Premium + Ulekan Talenan Kayu Inovasi Baru UNGU', '', 'ASCB-01PR', '', NULL, NULL, NULL, NULL, 1),
(60, 'ASCB-02', 'Wooden Chopping Board 02 - Asta', '', 'ASCB-02', '', NULL, NULL, NULL, NULL, 1),
(61, 'ASCM-11', 'Corong Stainless 11 cm - Asta', '', 'ASCM-11', '', NULL, NULL, NULL, NULL, 1),
(62, 'ASCM-15', 'Corong Stainless 15 cm - Asta', '', 'ASCM-15', '', NULL, NULL, NULL, NULL, 1),
(63, 'ASCR-01', 'Panggangan & Tirisan Minyak Crisper Square', '', 'ASCR-01', '', NULL, NULL, NULL, NULL, 1),
(64, 'ASCR-02', 'Panggangan & Tirisan Minyak Crisper Round', '', 'ASCR-02', '', NULL, NULL, NULL, NULL, 1),
(65, 'ASCR-03', 'Panggangan & Tirisan Minyak Crisper Black Noir', '', 'ASCR-03', '', NULL, NULL, NULL, NULL, 1),
(66, 'ASCS-03', 'Sendok Stainless Satuan - Asta', '', 'ASCS-03', '', NULL, NULL, NULL, NULL, 1),
(67, 'ASCS-0304', 'Sendok Stainless isi 4 - Asta', '', 'ASCS-0304', '', NULL, NULL, NULL, NULL, 1),
(68, 'ASCS-04', 'Garpu Stainless Satuan - Asta', '', 'ASCS-04', '', NULL, NULL, NULL, NULL, 1),
(69, 'ASCS-0404', 'Garpu Stainless isi 4 - Asta', '', 'ASCS-0404', '', NULL, NULL, NULL, NULL, 1),
(70, 'ASCW-01', 'Wajan Penggorengan Stainless Steel Chef Wok 32 Cm', '', 'ASCW-01', '', NULL, NULL, NULL, NULL, 1),
(71, 'ASCW-02', 'Wajan Penggorengan Stainless Steel Chef Wok 36 Cm', '', 'ASCW-02', '', NULL, NULL, NULL, NULL, 1),
(72, 'ASCW-03', 'Wajan Penggorengan Stainless Steel Chef Wok 40 Cm', '', 'ASCW-03', '', NULL, NULL, NULL, NULL, 1),
(73, 'ASCW-30', 'Wajan Penggorengan Stainless Steel Chef Wok 30 cm', '', 'ASCW-30', '', NULL, NULL, NULL, NULL, 1),
(74, 'ASFB-00SET', 'Fresh Box Seal Set (700,1000,1500,1900)', '', 'ASFB-00SET', '', NULL, NULL, NULL, NULL, 1),
(75, 'ASFB-01', 'Kotak Penyimpanan Food Container 304 Food Grade Stainless Steel 350 ml', '', 'ASFB-01', '', NULL, NULL, NULL, NULL, 1),
(76, 'ASFB-02', 'Kotak Penyimpanan Food Container 304 Food Grade Stainless Steel 550 ml', '', 'ASFB-02', '', NULL, NULL, NULL, NULL, 1),
(77, 'ASFB-03', 'Kotak Penyimpanan Food Container 304 Food Grade Stainless Steel 850 ml', '', 'ASFB-03', '', NULL, NULL, NULL, NULL, 1),
(78, 'ASFB-04', 'Kotak Penyimpanan Food Container 304 Stainless Steel + Tombol Kedap 700 ml', '', 'ASFB-04', '', NULL, NULL, NULL, NULL, 1),
(79, 'ASFB-05', 'Kotak Penyimpanan Food Container 304 Stainless Steel + Tombol Kedap 1000 ml', '', 'ASFB-05', '', NULL, NULL, NULL, NULL, 1),
(80, 'ASFB-06', 'Kotak Penyimpanan Food Container 304 Stainless Steel + Tombol Kedap 1500 ml', '', 'ASFB-06', '', NULL, NULL, NULL, NULL, 1),
(81, 'ASFB-07', 'Kotak Penyimpanan Food Container 304 Stainless Steel + Tombol Kedap 1900 ml', '', 'ASFB-07', '', NULL, NULL, NULL, NULL, 1),
(82, 'ASFB-08', '', '', 'ASFB-08', '', NULL, NULL, NULL, NULL, 1),
(83, 'ASFC-01', 'Rantang Stainless Steel Tunggal 14 cm Rantang Bakso Soto', '', 'ASFC-01', '', NULL, NULL, NULL, NULL, 1),
(84, 'ASFC-02', 'Rantang Stainless Steel Tunggal 16cm Rantang Bakso Soto', '', 'ASFC-02', '', NULL, NULL, NULL, NULL, 1),
(85, 'ASFF-01GF', 'Food Warmer Square dengan Prasmanan  Wadah Prasmanan', '', 'ASFF-01GF', '', NULL, NULL, NULL, NULL, 1),
(86, 'ASFF-03RF', 'Food Warmer Round dengan Pemasnan', '', 'ASFF-03RF', '', NULL, NULL, NULL, NULL, 1),
(87, 'ASFF-04RF', 'Food Warmer Round dengan Pemanas dan Frame', '', 'ASFF-04RF', '', NULL, NULL, NULL, NULL, 1),
(88, 'ASFF-05R', 'Food Warmer Penghangat Makanan Bulat - Asta', '', 'ASFF-05R', '', NULL, NULL, NULL, NULL, 1),
(89, 'ASFF-05S', 'Food Warmer Penghangat Makanan Persegi - Asta', '', 'ASFF-05S', '', NULL, NULL, NULL, NULL, 1),
(90, 'ASFM-02BG', 'Cutlery Set Beige Wheatstraw', '', 'ASFM-02BG', '', NULL, NULL, NULL, NULL, 1),
(91, 'ASFM-02BL', 'Cutlery Set Blue Wheatstraw', '', 'ASFM-02BL', '', NULL, NULL, NULL, NULL, 1),
(92, 'ASFM-02GR', 'Cutlery Set Green Wheatstraw', '', 'ASFM-02GR', '', NULL, NULL, NULL, NULL, 1),
(93, 'ASFM-02PK', 'Cutlery Set Pink Wheatstraw', '', 'ASFM-02PK', '', NULL, NULL, NULL, NULL, 1),
(94, 'ASFMD-01KS', 'Keset Kaki / Floor Mat Diatomite 3D Kotak 40x60 Motif 01', '', 'ASFMD-01KS', '', NULL, NULL, NULL, NULL, 1),
(95, 'ASFMD-01VS', 'Keset Kaki / Floor Mat Diatomite 3D Oval 40x60 Motif 01', '', 'ASFMD-01VS', '', NULL, NULL, NULL, NULL, 1),
(96, 'ASFMD-02KS', 'Keset Kaki / Floor Mat Diatomite 3D Kotak 40x60 Motif 02', '', 'ASFMD-02KS', '', NULL, NULL, NULL, NULL, 1),
(97, 'ASFMD-02VS', 'Keset Kaki / Floor Mat Diatomite 3D Oval 40x60 Motif 02', '', 'ASFMD-02VS', '', NULL, NULL, NULL, NULL, 1),
(98, 'ASFMD-03KB', 'Keset Kaki / Floor Mat Diatomite 3D Oval 50x80 Motif 03', '', 'ASFMD-03KB', '', NULL, NULL, NULL, NULL, 1),
(99, 'ASFMD-03KS', 'Keset Kaki / Floor Mat Diatomite 3D Oval 40x60 Motif 03', '', 'ASFMD-03KS', '', NULL, NULL, NULL, NULL, 1),
(100, 'ASFMD-04KB', 'Keset Kaki / Floor Mat Diatomite 3D Kotak 50x80 Motif 04', '', 'ASFMD-04KB', '', NULL, NULL, NULL, NULL, 1),
(101, 'ASFMD-04KS', 'Keset Kaki / Floor Mat Diatomite 3D Kotak 40x60 Motif 04', '', 'ASFMD-04KS', '', NULL, NULL, NULL, NULL, 1),
(102, 'ASFMD-05KB', 'Keset Kaki / Floor Mat Diatomite 3D Kotak 50x80 Motif 05', '', 'ASFMD-05KB', '', NULL, NULL, NULL, NULL, 1),
(103, 'ASFMD-05KS', 'Keset Kaki / Floor Mat Diatomite 3D Kotak 40x60 Motif 05', '', 'ASFMD-05KS', '', NULL, NULL, NULL, NULL, 1),
(104, 'ASFMD-06VB', 'Keset Kaki / Floor Mat Diatomite 3D Oval 50x80Motif 06', '', 'ASFMD-06VB', '', NULL, NULL, NULL, NULL, 1),
(105, 'ASFMD-06VS', 'Keset Kaki / Floor Mat Diatomite 3D Oval 40x60 Motif 06', '', 'ASFMD-06VS', '', NULL, NULL, NULL, NULL, 1),
(106, 'ASFMD-07VB', 'Keset Kaki / Floor Mat Diatomite 3D Oval 50x80 Motif 07', '', 'ASFMD-07VB', '', NULL, NULL, NULL, NULL, 1),
(107, 'ASFMD-07VS', 'Keset Kaki / Floor Mat Diatomite 3D Oval 40x60 Motif 07', '', 'ASFMD-07VS', '', NULL, NULL, NULL, NULL, 1),
(108, 'ASFMD-08VB', 'Keset Kaki / Floor Mat Diatomite 3D Oval 50x80 Motif 08', '', 'ASFMD-08VB', '', NULL, NULL, NULL, NULL, 1),
(109, 'ASFMD-08VS', 'Keset Kaki / Floor Mat Diatomite 3D Oval 40x60 Motif 08', '', 'ASFMD-08VS', '', NULL, NULL, NULL, NULL, 1),
(110, 'ASFMP-09KB', 'Keset Kaki / Floor Mat PVC Kotak 50x80 Motif 09', '', 'ASFMP-09KB', '', NULL, NULL, NULL, NULL, 1),
(111, 'ASFMP-09KS', 'Keset Kaki / Floor Mat PVC Kotak 40x60 Motif 09', '', 'ASFMP-09KS', '', NULL, NULL, NULL, NULL, 1),
(112, 'ASFMP-10KB', 'Keset Kaki / Floor Mat PVC Kotak 50x80 Motif 10', '', 'ASFMP-10KB', '', NULL, NULL, NULL, NULL, 1),
(113, 'ASFMP-10KS', 'Keset Kaki / Floor Mat PVC Kotak 40x60 Motif 10', '', 'ASFMP-10KS', '', NULL, NULL, NULL, NULL, 1),
(114, 'ASFMP-11KB', 'Keset Kaki / Floor Mat PVC Kotak 50x80 Motif 11', '', 'ASFMP-11KB', '', NULL, NULL, NULL, NULL, 1),
(115, 'ASFMP-11KS', 'Keset Kaki / Floor Mat PVC Kotak 40x60 Motif 11', '', 'ASFMP-11KS', '', NULL, NULL, NULL, NULL, 1),
(116, 'ASFMP-12KB', 'Keset Kaki / Floor Mat PVC Kotak 50x80 Motif 12', '', 'ASFMP-12KB', '', NULL, NULL, NULL, NULL, 1),
(117, 'ASFMP-12KS', 'Keset Kaki / Floor Mat PVC Kotak 40x60 Motif 12', '', 'ASFMP-12KS', '', NULL, NULL, NULL, NULL, 1),
(118, 'ASFMP-13KS', 'Keset Kaki / Floor Mat PVC Kotak 40x60 Motif 13', '', 'ASFMP-13KS', '', NULL, NULL, NULL, NULL, 1),
(119, 'ASFMP-14KS', 'Keset Kaki / Floor Mat PVC Kotak 40x60 Motif 14', '', 'ASFMP-14KS', '', NULL, NULL, NULL, NULL, 1),
(120, 'ASFMP-15KS', 'Keset Kaki / Floor Mat PVC Kotak 40x60 Motif 15', '', 'ASFMP-15KS', '', NULL, NULL, NULL, NULL, 1),
(121, 'ASFMP-16KS', 'Keset Kaki / Floor Mat PVC Kotak 40x60 Motif 16', '', 'ASFMP-16KS', '', NULL, NULL, NULL, NULL, 1),
(122, 'ASFMR-17KS', 'Keset Kaki / Floor Mat Polyester Rubber Kotak 40x60 Motif 17', '', 'ASFMR-17KS', '', NULL, NULL, NULL, NULL, 1),
(123, 'ASFMR-18KS', 'Keset Kaki / Floor Mat Polyester Rubber Kotak 40x60 Motif 18', '', 'ASFMR-18KS', '', NULL, NULL, NULL, NULL, 1),
(124, 'ASFMR-19KS', 'Keset Kaki / Floor Mat Polyester Rubber Kotak 40x60 Motif 19', '', 'ASFMR-19KS', '', NULL, NULL, NULL, NULL, 1),
(125, 'ASFO-01', 'Steak Tong - Asta', '', 'ASFO-01', '', NULL, NULL, NULL, NULL, 1),
(126, 'ASFO-02', 'Food Tong Stainless 23 cm - Asta', '', 'ASFO-02', '', NULL, NULL, NULL, NULL, 1),
(127, 'ASFO-02A', 'Food Tong Stainless 29 cm - Asta', '', 'ASFO-02A', '', NULL, NULL, NULL, NULL, 1),
(128, 'ASFO-03', 'Food Tong 18 cm Kecil - Asta', '', 'ASFO-03', '', NULL, NULL, NULL, NULL, 1),
(129, 'ASFO-04', 'Food Tong Teppan Stainless 26 cm - Asta', '', 'ASFO-04', '', NULL, NULL, NULL, NULL, 1),
(130, 'ASFO-05', 'Food Tong 23 cm - Asta', '', 'ASFO-05', '', NULL, NULL, NULL, NULL, 1),
(131, 'ASFO-06', 'Food Tong Silicone 23 cm - Asta', '', 'ASFO-06', '', NULL, NULL, NULL, NULL, 1),
(132, 'ASFO-06A', 'Food Tong Silicone 30 cm - Asta', '', 'ASFO-06A', '', NULL, NULL, NULL, NULL, 1),
(133, 'ASGD-612BK', 'Glass Dispenser / Dispenser Kaca 1,2 Ltr', '', 'ASGD-612BK', '', NULL, NULL, NULL, NULL, 1),
(134, 'ASGD-612BL', 'Glass Dispenser / Dispenser Kaca 1,2 Ltr', '', 'ASGD-612BL', '', NULL, NULL, NULL, NULL, 1),
(135, 'ASGD-612GR', 'Glass Dispenser / Dispenser Kaca 1,2 Ltr', '', 'ASGD-612GR', '', NULL, NULL, NULL, NULL, 1),
(136, 'ASGD-612GY', 'Glass Dispenser / Dispenser Kaca 1,2 Ltr', '', 'ASGD-612GY', '', NULL, NULL, NULL, NULL, 1),
(137, 'ASGD-612PR', 'Glass Dispenser / Dispenser Kaca 1,2 Ltr', '', 'ASGD-612PR', '', NULL, NULL, NULL, NULL, 1),
(138, 'ASGD-612RD', 'Glass Dispenser / Dispenser Kaca 1,2 Ltr', '', 'ASGD-612RD', '', NULL, NULL, NULL, NULL, 1),
(139, 'ASGD-612WH', 'Glass Dispenser / Dispenser Kaca 1,2 Ltr', '', 'ASGD-612WH', '', NULL, NULL, NULL, NULL, 1),
(140, 'ASGD-612YL', 'Glass Dispenser / Dispenser Kaca 1,2 Ltr', '', 'ASGD-612YL', '', NULL, NULL, NULL, NULL, 1),
(141, 'ASGD-620BK', 'Glass Dispenser / Dispenser Kaca 2 Ltr', '', 'ASGD-620BK', '', NULL, NULL, NULL, NULL, 1),
(142, 'ASGD-620BL', 'Glass Dispenser / Dispenser Kaca 2 Ltr', '', 'ASGD-620BL', '', NULL, NULL, NULL, NULL, 1),
(143, 'ASGD-620GR', 'Glass Dispenser / Dispenser Kaca 2 Ltr', '', 'ASGD-620GR', '', NULL, NULL, NULL, NULL, 1),
(144, 'ASGD-620GY', 'Glass Dispenser / Dispenser Kaca 2 Ltr', '', 'ASGD-620GY', '', NULL, NULL, NULL, NULL, 1),
(145, 'ASGD-620PR', 'Glass Dispenser / Dispenser Kaca 2 Ltr', '', 'ASGD-620PR', '', NULL, NULL, NULL, NULL, 1),
(146, 'ASGD-620RD', 'Glass Dispenser / Dispenser Kaca 2 Ltr', '', 'ASGD-620RD', '', NULL, NULL, NULL, NULL, 1),
(147, 'ASGD-620WH', 'Glass Dispenser / Dispenser Kaca 2 Ltr', '', 'ASGD-620WH', '', NULL, NULL, NULL, NULL, 1),
(148, 'ASGD-620YL', 'Glass Dispenser / Dispenser Kaca 2 Ltr', '', 'ASGD-620YL', '', NULL, NULL, NULL, NULL, 1),
(149, 'ASGD-639BK', 'Glass Dispenser / Dispenser Kaca 3,9 Ltr', '', 'ASGD-639BK', '', NULL, NULL, NULL, NULL, 1),
(150, 'ASGD-639BL', 'Glass Dispenser / Dispenser Kaca 3,9 Ltr', '', 'ASGD-639BL', '', NULL, NULL, NULL, NULL, 1),
(151, 'ASGD-639GR', 'Glass Dispenser / Dispenser Kaca 3,9 Ltr', '', 'ASGD-639GR', '', NULL, NULL, NULL, NULL, 1),
(152, 'ASGD-639GY', 'Glass Dispenser / Dispenser Kaca 3,9 Ltr', '', 'ASGD-639GY', '', NULL, NULL, NULL, NULL, 1),
(153, 'ASGD-639PR', 'Glass Dispenser / Dispenser Kaca 3,9 Ltr', '', 'ASGD-639PR', '', NULL, NULL, NULL, NULL, 1),
(154, 'ASGD-639RD', 'Glass Dispenser / Dispenser Kaca 3,9 Ltr', '', 'ASGD-639RD', '', NULL, NULL, NULL, NULL, 1),
(155, 'ASGD-639WH', 'Glass Dispenser / Dispenser Kaca 3,9 Ltr', '', 'ASGD-639WH', '', NULL, NULL, NULL, NULL, 1),
(156, 'ASGD-639YL', 'Glass Dispenser / Dispenser Kaca 3,9 Ltr', '', 'ASGD-639YL', '', NULL, NULL, NULL, NULL, 1),
(157, 'ASIC-01', 'Es Batu Ice Cube Food Grade Quality Kuda Unicorn', '', 'ASIC-01', '', NULL, NULL, NULL, NULL, 1),
(158, 'ASIC-02', 'Es Batu Ice Cube Food Grade Quality Hati', '', 'ASIC-02', '', NULL, NULL, NULL, NULL, 1),
(159, 'ASIS-01', 'Sendok Scoop Ice Cream Stainless - Asta', '', 'ASIS-01', '', NULL, NULL, NULL, NULL, 1),
(160, 'ASJS-01', 'Sutil Kayu Jumbo / Spatula Kayu', '', 'ASJS-01', '', NULL, NULL, NULL, NULL, 1),
(161, 'ASKK-01', 'Pisau daging 01', '', 'ASKK-01', '', NULL, NULL, NULL, NULL, 1),
(162, 'ASKK-02', 'Pisau daging 02', '', 'ASKK-02', '', NULL, NULL, NULL, NULL, 1),
(163, 'ASKK-03', 'Pisau daging 03', '', 'ASKK-03', '', NULL, NULL, NULL, NULL, 1),
(164, 'ASKP-01', 'Panggangan / Grill Pan / Koki Pan Square 01', '', 'ASKP-01', '', NULL, NULL, NULL, NULL, 1),
(165, 'ASKP-02', 'Panggangan / Grill Pan / Koki Pan Oishi 02', '', 'ASKP-02', '', NULL, NULL, NULL, NULL, 1),
(166, 'ASKP-03', 'Panggangan / Grill Pan / Koki Pan Round 03', '', 'ASKP-03', '', NULL, NULL, NULL, NULL, 1),
(167, 'ASKS-01OR', 'Pisau Set Orange', '', 'ASKS-01OR', '', NULL, NULL, NULL, NULL, 1),
(168, 'ASKS-01PR', 'Pisau Set Purple', '', 'ASKS-01PR', '', NULL, NULL, NULL, NULL, 1),
(169, 'ASLB-01-3B', 'Lunch Box 01 3 Compartments Blue - Asta', '', 'ASLB-01-3B', '', NULL, NULL, NULL, NULL, 1),
(170, 'ASLB-01-3P', 'Lunch Box 01 3 Compartments Pink - Asta', '', 'ASLB-01-3P', '', NULL, NULL, NULL, NULL, 1),
(171, 'ASLB-01-3R', 'Lunch Box 01 3 Compartments Red - Asta', '', 'ASLB-01-3R', '', NULL, NULL, NULL, NULL, 1),
(172, 'ASLB-01-3T', 'Lunch Box 01 3 Compartments Turquoise- Asta', '', 'ASLB-01-3T', '', NULL, NULL, NULL, NULL, 1),
(173, 'ASLB-01-4B', 'Lunch Box 01 4 Compartments Blue - Asta', '', 'ASLB-01-4B', '', NULL, NULL, NULL, NULL, 1),
(174, 'ASLB-01-4B+ALBG-01', 'Lunch Box 01 4 sekat blue + lunch bag', '', 'ASLB-01-4B+ALBG-01', '', NULL, NULL, NULL, NULL, 1),
(175, 'ASLB-01-4BK', 'Lunch Box 01 4 Compartments Black - Asta', '', 'ASLB-01-4BK', '', NULL, NULL, NULL, NULL, 1),
(176, 'ASLB-01-4BK+ALBG-01', 'Lunch Box 01 4 sekat black + lunch bag', '', 'ASLB-01-4BK+ALBG-01', '', NULL, NULL, NULL, NULL, 1),
(177, 'ASLB-01-4P', 'Lunch Box 01 4 Compartments Pink - Asta', '', 'ASLB-01-4P', '', NULL, NULL, NULL, NULL, 1),
(178, 'ASLB-01-4P+ALBG-01', 'Lunch Box 01 4 sekat pink + lunch bag', '', 'ASLB-01-4P+ALBG-01', '', NULL, NULL, NULL, NULL, 1),
(179, 'ASLB-01-4R', 'Lunch Box 01 4 Compartments Red - Asta', '', 'ASLB-01-4R', '', NULL, NULL, NULL, NULL, 1),
(180, 'ASLB-01-4R+ALBG-01', 'Lunch Box 01 4 sekat red + lunch bag', '', 'ASLB-01-4R+ALBG-01', '', NULL, NULL, NULL, NULL, 1),
(181, 'ASLB-01-4T', 'Lunch Box 01 4 Compartments Turquoise - Asta', '', 'ASLB-01-4T', '', NULL, NULL, NULL, NULL, 1),
(182, 'ASLB-01-4T+ALBG-01', 'Lunch Box 01 4 sekat turkis  + lunch bag', '', 'ASLB-01-4T+ALBG-01', '', NULL, NULL, NULL, NULL, 1),
(183, 'ASLB-02-BL', 'Kotak Makan Stainless Steel Lunch Box Dengan Tempat Sup BIRU', '', 'ASLB-02-BL', '', NULL, NULL, NULL, NULL, 1),
(184, 'ASLB-02-GR', 'Kotak Makan Stainless Steel Lunch Box Dengan Tempat Sup HIJAU', '', 'ASLB-02-GR', '', NULL, NULL, NULL, NULL, 1),
(185, 'ASLB-02-PK', 'Kotak Makan Stainless Steel Lunch Box Dengan Tempat Sup PINK', '', 'ASLB-02-PK', '', NULL, NULL, NULL, NULL, 1),
(186, 'ASLB-03-BL', 'Kotak Makan Stainless Steel Kotak Bekal Anti Tumpah BIRU', '', 'ASLB-03-BL', '', NULL, NULL, NULL, NULL, 1),
(187, 'ASLB-03-GR', 'Kotak Makan Stainless Steel Kotak Bekal Anti Tumpah GREEN', '', 'ASLB-03-GR', '', NULL, NULL, NULL, NULL, 1),
(188, 'ASLB-03-PK', 'Kotak Makan Stainless Steel Kotak Bekal Anti Tumpah PINK', '', 'ASLB-03-PK', '', NULL, NULL, NULL, NULL, 1),
(189, 'ASLB-04BROWN', 'Kotak Makan Stainless Steel Lunch Box 1 layer 2 sekat BROWN', '', 'ASLB-04BROWN', '', NULL, NULL, NULL, NULL, 1),
(190, 'ASLB-04CREAM', 'Kotak Makan Stainless Steel Lunch Box 1 layer 2 sekat CREAM', '', 'ASLB-04CREAM', '', NULL, NULL, NULL, NULL, 1),
(191, 'ASLB-05BROWN', 'Kotak Makan Stainless Steel Lunch Box 2 layer 2 sekat BROWN', '', 'ASLB-05BROWN', '', NULL, NULL, NULL, NULL, 1),
(192, 'ASLB-05CREAM', 'Kotak Makan Stainless Steel Lunch Box 2 layer 2 sekat CREAM', '', 'ASLB-05CREAM', '', NULL, NULL, NULL, NULL, 1),
(193, 'ASLL-01', 'Gayung Stainless Panjang 12 cm - Asta', '', 'ASLL-01', '', NULL, NULL, NULL, NULL, 1),
(194, 'ASLL-02', 'Gayung Stainless Panjang 14cm - Asta', '', 'ASLL-02', '', NULL, NULL, NULL, NULL, 1),
(195, 'ASLL-03', 'Gayung Stainless Panjang 18 cm - Asta', '', 'ASLL-03', '', NULL, NULL, NULL, NULL, 1),
(196, 'ASLP-01', 'akan Anak Stainless Steel Kids Food', '', 'ASLP-01', '', NULL, NULL, NULL, NULL, 1),
(197, 'ASLP-02', 'akan Anak Stainless Steel Kids Food', '', 'ASLP-02', '', NULL, NULL, NULL, NULL, 1),
(198, 'ASLP-03', 'akan Anak Stainless Steel Kids Food', '', 'ASLP-03', '', NULL, NULL, NULL, NULL, 1),
(199, 'ASLP-04P', 'tainless Steel Dengan Tutup Plastik', '', 'ASLP-04P', '', NULL, NULL, NULL, NULL, 1),
(200, 'ASLP-04S', 'inless Steel Dengan Tutup Stainless', '', 'ASLP-04S', '', NULL, NULL, NULL, NULL, 1),
(201, 'ASLP-05P', 'tainless Steel Dengan Tutup Plastik', '', 'ASLP-05P', '', NULL, NULL, NULL, NULL, 1),
(202, 'ASLP-05S', 'less Steel Dengan Tutup Stainless S', '', 'ASLP-05S', '', NULL, NULL, NULL, NULL, 1),
(203, 'ASLP-06', 'n Anak Stainless Steel Kids Food Tr', '', 'ASLP-06', '', NULL, NULL, NULL, NULL, 1),
(204, 'ASMB-18', 'Baskom Mangkok Stainless Steel Mixing Bowl Tebal Food Grade 18 cm', '', 'ASMB-18', '', NULL, NULL, NULL, NULL, 1),
(205, 'ASMB-18-24cm', 'Baskom Set 18-24 cm', '', 'ASMB-18-24cm', '', NULL, NULL, NULL, NULL, 1),
(206, 'ASMB-20', 'Baskom Mangkok Stainless Steel Mixing Bowl Tebal Food Grade 20 cm', '', 'ASMB-20', '', NULL, NULL, NULL, NULL, 1),
(207, 'ASMB-22', 'Baskom Mangkok Stainless Steel Mixing Bowl Tebal Food Grade 22 cm', '', 'ASMB-22', '', NULL, NULL, NULL, NULL, 1),
(208, 'ASMB-24', 'Baskom Mangkok Stainless Steel Mixing Bowl Tebal Food Grade 24 cm', '', 'ASMB-24', '', NULL, NULL, NULL, NULL, 1),
(209, 'ASMB-28', 'Baskom Mangkok Stainless Steel Mixing Bowl Tebal Food Grade 28 cm', '', 'ASMB-28', '', NULL, NULL, NULL, NULL, 1),
(210, 'ASMB-30', 'Baskom Mangkok Stainless Steel Mixing Bowl Tebal Food Grade 30 cm', '', 'ASMB-30', '', NULL, NULL, NULL, NULL, 1),
(211, 'ASMB-30-40cm', 'Baskom Set 30-40', '', 'ASMB-30-40cm', '', NULL, NULL, NULL, NULL, 1),
(212, 'ASMB-34', 'Baskom Mangkok Stainless Steel Mixing Bowl Tebal Food Grade 34 cm', '', 'ASMB-34', '', NULL, NULL, NULL, NULL, 1),
(213, 'ASMB-36', 'Baskom Mangkok Stainless Steel Mixing Bowl Tebal Food Grade 36 cm', '', 'ASMB-36', '', NULL, NULL, NULL, NULL, 1),
(214, 'ASMB-40', 'Baskom Mangkok Stainless Steel Mixing Bowl Tebal Food Grade 40 cm', '', 'ASMB-40', '', NULL, NULL, NULL, NULL, 1),
(215, 'ASMJ-100', 'Milk Jug Stainless 1000 ml - Asta', '', 'ASMJ-100', '', NULL, NULL, NULL, NULL, 1),
(216, 'ASMJ-35', 'Milk Jug Stainless 350 ml - Asta', '', 'ASMJ-35', '', NULL, NULL, NULL, NULL, 1),
(217, 'ASMJ-60', 'Milk Jug Stainless 600 ml - Asta', '', 'ASMJ-60', '', NULL, NULL, NULL, NULL, 1),
(218, 'ASMM-12', 'Saringan Mie Stainless 12 cm - Asta', '', 'ASMM-12', '', NULL, NULL, NULL, NULL, 1),
(219, 'ASMM-14', 'Saringan Mie Stainless 14 cm - Asta', '', 'ASMM-14', '', NULL, NULL, NULL, NULL, 1),
(220, 'ASMM-18', 'Saringan Mie Stainless 18 cm - Asta', '', 'ASMM-18', '', NULL, NULL, NULL, NULL, 1),
(221, 'ASMP-01', 'Mini Pots / Mini Stock Pots Panci Sop', '', 'ASMP-01', '', NULL, NULL, NULL, NULL, 1),
(222, 'ASMU-01', 'Gilingan Serbaguna Multiuse Mincer', '', 'ASMU-01', '', NULL, NULL, NULL, NULL, 1),
(223, 'ASNP-01', 'Noodle Pot Panci Susu / Panci Rebus / Milk Pot 18 cm', '', 'ASNP-01', '', NULL, NULL, NULL, NULL, 1),
(224, 'ASNP-02', 'Noodle pot Panci Susu / Panci Rebus / Milk Pot 20 cm', '', 'ASNP-02', '', NULL, NULL, NULL, NULL, 1),
(225, 'ASOC-01', 'Pelindung Percikan Minyak Oil Spatter 01', '', 'ASOC-01', '', NULL, NULL, NULL, NULL, 1),
(226, 'ASOC-02', 'Pelindung Percikan Minyak Oil Spatter 02', '', 'ASOC-02', '', NULL, NULL, NULL, NULL, 1),
(227, 'ASOC-03', 'Pelindung Percikan Minyak Oil Spatter 03', '', 'ASOC-03', '', NULL, NULL, NULL, NULL, 1),
(228, 'ASOC-04', 'Pelindung Percikan Minyak Oil Spatter 04', '', 'ASOC-04', '', NULL, NULL, NULL, NULL, 1),
(229, 'ASOW-01', 'Wajan Penggorengan Stainless Steel Oriental Wok 32 Cm', '', 'ASOW-01', '', NULL, NULL, NULL, NULL, 1),
(230, 'ASOW-01ASSA-03B', 'oriental wok 32 + ASSA 03 B', '', 'ASOW-01ASSA-03B', '', NULL, NULL, NULL, NULL, 1),
(231, 'ASOW-02', 'Wajan Penggorengan Stainless Steel Oriental Wok 36 Cm', '', 'ASOW-02', '', NULL, NULL, NULL, NULL, 1),
(232, 'ASOW-02ASSA-03B', 'oriental wok 36 + ASSA 03 B', '', 'ASOW-02ASSA-03B', '', NULL, NULL, NULL, NULL, 1),
(233, 'ASOW-03', 'Wajan Penggorengan Stainless Steel Oriental Wok 40 Cm', '', 'ASOW-03', '', NULL, NULL, NULL, NULL, 1),
(234, 'ASOW-03ASSA-03B', 'oriental wok 40 + ASSA 03 B', '', 'ASOW-03ASSA-03B', '', NULL, NULL, NULL, NULL, 1),
(235, 'ASOW-05', 'Wajan Penggorengan Stainless Steel Oriental Wok 50 Cm', '', 'ASOW-05', '', NULL, NULL, NULL, NULL, 1),
(236, 'ASOW-05ASSA-03B', 'oriental wok 50 + ASSA 03 B', '', 'ASOW-05ASSA-03B', '', NULL, NULL, NULL, NULL, 1),
(237, 'ASOW-06', 'Wajan Penggorengan Stainless Steel Oriental Wok 60 Cm', '', 'ASOW-06', '', NULL, NULL, NULL, NULL, 1),
(238, 'ASOW-06ASSA-03B', 'oriental wok 60 + ASSA 03 B', '', 'ASOW-06ASSA-03B', '', NULL, NULL, NULL, NULL, 1),
(239, 'ASOW-26', 'ggorengan Stainless Steel Oriental', '', 'ASOW-26', '', NULL, NULL, NULL, NULL, 1),
(240, 'ASOW-28', 'Wajan Penggorengan Stainless Steel Oriental Wok 28 cm', '', 'ASOW-28', '', NULL, NULL, NULL, NULL, 1),
(241, 'ASOW-28ASSA-03B', 'oriental wok 28 + ASSA 03 B', '', 'ASOW-28ASSA-03B', '', NULL, NULL, NULL, NULL, 1),
(242, 'ASOW-30', 'Wajan Penggorengan Stainless Steel Oriental Wok 30 cm', '', 'ASOW-30', '', NULL, NULL, NULL, NULL, 1),
(243, 'ASOW-30ASSA-03B', 'oriental wok 30 + ASSA 03 B', '', 'ASOW-30ASSA-03B', '', NULL, NULL, NULL, NULL, 1),
(244, 'ASPM-115AM', 'Gilingan Mie / Pasta Maker Ampia', '', 'ASPM-115AM', '', NULL, NULL, NULL, NULL, 1),
(245, 'ASPM-115RD', 'Gilingan Mie / Pasta Maker Red', '', 'ASPM-115RD', '', NULL, NULL, NULL, NULL, 1),
(246, 'ASPM-115SS', 'Gilingan Mie / Pasta Maker Stainless', '', 'ASPM-115SS', '', NULL, NULL, NULL, NULL, 1),
(247, 'ASPM-150', 'Gilingan Mie / Pasta Maker Jumbo', '', 'ASPM-150', '', NULL, NULL, NULL, NULL, 1),
(248, 'ASPS-01', 'Panci Set Marbello Panci Set Lengkap Anti Lengket', '', 'ASPS-01', '', NULL, NULL, NULL, NULL, 1),
(249, 'ASPS01CARD', 'Marbello Cookware Set + CARD - Asta', '', 'ASPS01CARD', '', NULL, NULL, NULL, NULL, 1),
(250, 'ASPS-01B', 'Marbello Cookware Set Beige - Asta', '', 'ASPS-01B', '', NULL, NULL, NULL, NULL, 1),
(251, 'ASPS01BCARD', 'Marbello Cookware Set Beige + CARD - Asta', '', 'ASPS01BCARD', '', NULL, NULL, NULL, NULL, 1),
(252, 'ASPS-02', 'Panci Set Lemona Panci Set Lengkap Anti Lengket', '', 'ASPS-02', '', NULL, NULL, NULL, NULL, 1),
(253, 'ASPS02CARD', 'Lemona Cookware Set + CARD - Asta', '', 'ASPS02CARD', '', NULL, NULL, NULL, NULL, 1),
(254, 'ASPS-05', 'Panci Set Tokyo', '', 'ASPS-05', '', NULL, NULL, NULL, NULL, 1),
(255, 'ASPS-05CARD', '7 pcs Tokyo Stainless Steel Cookware Set + CARD - Asta', '', 'ASPS-05CARD', '', NULL, NULL, NULL, NULL, 1),
(256, 'ASPS-07PK', 'Panci Set Valentina Panci Set Lengkap Anti Lengket PINK', '', 'ASPS-07PK', '', NULL, NULL, NULL, NULL, 1),
(257, 'ASPS07PKCARD', 'Valentina 3 pcs Cookware Set Pink + CARD - Asta', '', 'ASPS07PKCARD', '', NULL, NULL, NULL, NULL, 1),
(258, 'ASPS-07PR', 'Panci Set Valentina Panci Set Lengkap Anti Lengket Purple', '', 'ASPS-07PR', '', NULL, NULL, NULL, NULL, 1),
(259, 'ASPS07PRCARD', 'Valentina 3 pcs Cookware Set Purple + CARD - Asta', '', 'ASPS07PRCARD', '', NULL, NULL, NULL, NULL, 1),
(260, 'ASPS-08', 'Panci Set Cappucino Panci Set Lengkap Anti Lengket', '', 'ASPS-08', '', NULL, NULL, NULL, NULL, 1),
(261, 'ASPS-09', 'Panci Set New Kiwiz Panci Set Lengkap Anti Lengket', '', 'ASPS-09', '', NULL, NULL, NULL, NULL, 1),
(262, 'ASPS09CARD', 'New Kiwiz 7 Pieces Cookware Set+ CARD - Asta', '', 'ASPS09CARD', '', NULL, NULL, NULL, NULL, 1),
(263, 'ASPS-10', 'Panci Set Marbello Rosie Panci Set Lengkap Anti Lengket', '', 'ASPS-10', '', NULL, NULL, NULL, NULL, 1),
(264, 'ASPS10CARD', 'Marbello Rosie 4 pcs Cookware Set+ CARD - Asta', '', 'ASPS10CARD', '', NULL, NULL, NULL, NULL, 1),
(265, 'ASPS-11', 'Panci Set Marbello Fiesta Panci Set Lengkap Anti Lengket', '', 'ASPS-11', '', NULL, NULL, NULL, NULL, 1),
(266, 'ASPS-12', 'Panci Set Valentina Milan Panci Set Lengkap Anti Lengket', '', 'ASPS-12', '', NULL, NULL, NULL, NULL, 1),
(267, 'ASPS12CARD', 'Valentina Milan 7 pcs Cookware Set+ CARD  - Asta', '', 'ASPS12CARD', '', NULL, NULL, NULL, NULL, 1),
(268, 'ASPS-15', 'Panci Set Lemona 100 Panci Set Lengkap Anti Lengket', '', 'ASPS-15', '', NULL, NULL, NULL, NULL, 1),
(269, 'ASPS15CARD', 'Lemona 100 5 pcs Cookware Set+CARD - Asta', '', 'ASPS15CARD', '', NULL, NULL, NULL, NULL, 1),
(270, 'ASPS-16', 'Panci Set Tempura Panci Set Lengkap Anti Lengket', '', 'ASPS-16', '', NULL, NULL, NULL, NULL, 1),
(271, 'ASPU-01', 'Spons Mandi / Shower Puff', '', 'ASPU-01', '', NULL, NULL, NULL, NULL, 1),
(272, 'ASRB-01', 'Rak Kayu / Rak Bumbu Dapur Dari Kayu', '', 'ASRB-01', '', NULL, NULL, NULL, NULL, 1),
(273, 'ASSA-01A', 'Spatula Sutil sodet StainlessSteel Motif Marble', '', 'ASSA-01A', '', NULL, NULL, NULL, NULL, 1),
(274, 'ASSA-01B', 'Spatula Sutil sodet kipas Stainless Steel Motif Marble', '', 'ASSA-01B', '', NULL, NULL, NULL, NULL, 1),
(275, 'ASSA-01C', 'Sendok irus soup ladle Stainless Steel Motif Marble', '', 'ASSA-01C', '', NULL, NULL, NULL, NULL, 1),
(276, 'ASSA-01D', 'Sendok Irus Skimmer Stainless Steel Motif Marble', '', 'ASSA-01D', '', NULL, NULL, NULL, NULL, 1),
(277, 'ASSA-02A', 'Spatula Sutil sodet Stainless Steel Motif Hitam', '', 'ASSA-02A', '', NULL, NULL, NULL, NULL, 1),
(278, 'ASSA-02B', 'Spatula Sutil sodet kipas Stainless Steel Motif Hitam', '', 'ASSA-02B', '', NULL, NULL, NULL, NULL, 1),
(279, 'ASSA-02C', 'Sendok irus soup ladle Stainless Steel Motif Hitam', '', 'ASSA-02C', '', NULL, NULL, NULL, NULL, 1),
(280, 'ASSA-02D', 'Sendok Irus Skimmer Stainless Steel Motif Hitam', '', 'ASSA-02D', '', NULL, NULL, NULL, NULL, 1),
(281, 'ASSA-03A', 'Spatula Sutil sodet Stainless Steel Motif Coklat', '', 'ASSA-03A', '', NULL, NULL, NULL, NULL, 1),
(282, 'ASSA-03B', 'Spatula Sutil sodet kipas Stainless Steel Motif Coklat', '', 'ASSA-03B', '', NULL, NULL, NULL, NULL, 1),
(283, 'ASSA-03C', 'Sendok irus soup ladle Stainless Steel Motif Coklat', '', 'ASSA-03C', '', NULL, NULL, NULL, NULL, 1),
(284, 'ASSA-03D', 'Sendok Irus Skimmer Stainless Steel Motif Coklat', '', 'ASSA-03D', '', NULL, NULL, NULL, NULL, 1),
(285, 'ASSA-04A', 'Solid Turner Wooden Handle - Asta', '', 'ASSA-04A', '', NULL, NULL, NULL, NULL, 1),
(286, 'ASSA-04C', 'Soup Laddle Wooden Handle - Asta', '', 'ASSA-04C', '', NULL, NULL, NULL, NULL, 1),
(287, 'ASSA-05L', 'Teppan Spatula Lebar L - Asta', '', 'ASSA-05L', '', NULL, NULL, NULL, NULL, 1),
(288, 'ASSA-05M', 'Teppan Spatula Lebar M - Asta', '', 'ASSA-05M', '', NULL, NULL, NULL, NULL, 1),
(289, 'ASSA-06L', 'Teppan Spatula Panjang L - Asta', '', 'ASSA-06L', '', NULL, NULL, NULL, NULL, 1),
(290, 'ASSA-06M', 'Teppan Spatula Panjang M - Asta', '', 'ASSA-06M', '', NULL, NULL, NULL, NULL, 1),
(291, 'ASSA-07', 'Cake Scrapper - Asta', '', 'ASSA-07', '', NULL, NULL, NULL, NULL, 1),
(292, 'ASSB-06BL', 'Toples Snack Cantik La Bella Toples Plastik Tebal BLUE', '', 'ASSB-06BL', '', NULL, NULL, NULL, NULL, 1),
(293, 'ASSB-06PK', 'Toples Snack Cantik La Bella Toples Plastik Tebal PINK', '', 'ASSB-06PK', '', NULL, NULL, NULL, NULL, 1),
(294, 'ASSC-14', 'Saos Stainless Steel Sauce Contain', '', 'ASSC-14', '', NULL, NULL, NULL, NULL, 1),
(295, 'ASSC-16', 'Saos Stainless Steel Sauce Contain', '', 'ASSC-16', '', NULL, NULL, NULL, NULL, 1),
(296, 'ASSC-18', 'Saos Stainless Steel Sauce Contain', '', 'ASSC-18', '', NULL, NULL, NULL, NULL, 1),
(297, 'ASSL-01BL', 'Silicone Soup Ladle - Blue - Asta', '', 'ASSL-01BL', '', NULL, NULL, NULL, NULL, 1),
(298, 'ASSL-01PK', 'Silicone Soup Ladle - Pink - Asta', '', 'ASSL-01PK', '', NULL, NULL, NULL, NULL, 1),
(299, 'ASSP-01', 'Stock Pot Steamer 01 Panci Sop Besar', '', 'ASSP-01', '', NULL, NULL, NULL, NULL, 1),
(300, 'ASSP-02', 'Stock Pot 02', '', 'ASSP-02', '', NULL, NULL, NULL, NULL, 1),
(301, 'ASSP-03', 'Stock Pot European Style 03 Panci Sop Besar', '', 'ASSP-03', '', NULL, NULL, NULL, NULL, 1),
(302, 'ASSP-04', 'Steamer Pot 2 Susun - Asta', '', 'ASSP-04', '', NULL, NULL, NULL, NULL, 1),
(303, 'ASSR-304', 'Kukusan Steamer Stainless Steel 304', '', 'ASSR-304', '', NULL, NULL, NULL, NULL, 1),
(304, 'ASSS-01BL', 'Spatula Silicone Set BLUE', '', 'ASSS-01BL', '', NULL, NULL, NULL, NULL, 1),
(305, 'ASSS-01PK', 'Spatula Silicone Set PINK', '', 'ASSS-01PK', '', NULL, NULL, NULL, NULL, 1),
(306, 'ASTS-01BL', 'Silicone Turner Spatula - Blue - Asta', '', 'ASTS-01BL', '', NULL, NULL, NULL, NULL, 1),
(307, 'ASTS-01PK', 'Silicone Turner Spatula - Pink - Asta', '', 'ASTS-01PK', '', NULL, NULL, NULL, NULL, 1),
(308, 'ASTW-01', 'Timer Watch - Asta', '', 'ASTW-01', '', NULL, NULL, NULL, NULL, 1),
(309, 'ASWA-01', 'Spons Cuci Piring Kuning 1 pc', '', 'ASWA-01', '', NULL, NULL, NULL, NULL, 1),
(310, 'ASWA-016PCS', 'Spons Cuci Piring Kuning 6 pcs', '', 'ASWA-016PCS', '', NULL, NULL, NULL, NULL, 1),
(311, 'ASWA-02', 'Spons Cuci Piring Abu-Abu', '', 'ASWA-02', '', NULL, NULL, NULL, NULL, 1),
(312, 'ASWA-026PCS', 'Pisau daging 026PCS', '', 'ASWA-026PCS', '', NULL, NULL, NULL, NULL, 1),
(313, 'ASWA-036pcs', 'Spons Cuci Stock Pot 6 pcs', '', 'ASWA-036pcs', '', NULL, NULL, NULL, NULL, 1),
(314, 'ASWK-01', 'Teko / Ketel / Ceret Air Food Grade Valentina Whistling Kettle', '', 'ASWK-01', '', NULL, NULL, NULL, NULL, 1),
(315, 'ASWK-02BL', 'Teko / Ketel / Ceret Air Food Grade Valentina Whistling Kettle Handle Kayu BIRU', '', 'ASWK-02BL', '', NULL, NULL, NULL, NULL, 1),
(316, 'ASWK-02PK', 'Teko / Ketel / Ceret Air Food Grade Valentina Whistling Kettle Handle Kayu PINK', '', 'ASWK-02PK', '', NULL, NULL, NULL, NULL, 1),
(317, 'ASWK-02PR', 'Teko / Ketel / Ceret Air Food Grade Valentina Whistling Kettle Handle KayuUNGU', '', 'ASWK-02PR', '', NULL, NULL, NULL, NULL, 1),
(318, 'ASWK-03BM', 'Teko / Ketel / Ceret Air Food Grade Valentina Whistling Kettle Marble HandleKayu BLACK', '', 'ASWK-03BM', '', NULL, NULL, NULL, NULL, 1),
(319, 'ASWK-03BMCARD', 'Whistling Kettle - 03 (3L) - Black Marble + CARD - Asta', '', 'ASWK-03BMCARD', '', NULL, NULL, NULL, NULL, 1),
(320, 'ASWK-03CM', 'Teko / Ketel / Ceret Air Food Grade Valentina Whistling Kettle Marble HandleKayu CREAM', '', 'ASWK-03CM', '', NULL, NULL, NULL, NULL, 1),
(321, 'ASWK-03CMCARD', 'Whistling Kettle - 03 (3L) - Beige Marble + CARD - Asta', '', 'ASWK-03CMCARD', '', NULL, NULL, NULL, NULL, 1),
(322, 'ASWK-04', 'el / Ceret Air Food Grade Full Stain', '', 'ASWK-04', '', NULL, NULL, NULL, NULL, 1),
(323, 'ASWS-01', 'Sutil Kayu Set Warna Spatula Kayu Set', '', 'ASWS-01', '', NULL, NULL, NULL, NULL, 1),
(324, 'ASWS-02', 'Sutil Kayu / Spatula Kayu / Turner', '', 'ASWS-02', '', NULL, NULL, NULL, NULL, 1),
(325, 'ASWS-03', 'Sendok Sop Kayu/ Centong Kayu', '', 'ASWS-03', '', NULL, NULL, NULL, NULL, 1),
(326, 'ASWS-04', 'Spatula / Sutil Sode Kayu / Spatula kayu', '', 'ASWS-04', '', NULL, NULL, NULL, NULL, 1),
(327, 'ATCA-01', 'Panci Casserole Premium Tognana Casserole 24 cm', '', 'ATCA-01', '', NULL, NULL, NULL, NULL, 1),
(328, 'ATCA-01ASSL-01BL', 'Tognana casserole + soup ladle silikon blue', '', 'ATCA-01ASSL-01BL', '', NULL, NULL, NULL, NULL, 1),
(329, 'ATCA-01ASSL-01PK', 'Tognana casserole + soup ladle silikon pink', '', 'ATCA-01ASSL-01PK', '', NULL, NULL, NULL, NULL, 1),
(330, 'ATCA-01CARD', 'Tognana Casserole Set Hadiah', '', 'ATCA-01CARD', '', NULL, NULL, NULL, NULL, 1),
(331, 'ATFP-01', 'Panci Penggorengan Premium Tognana Fry Pan 20 cm', '', 'ATFP-01', '', NULL, NULL, NULL, NULL, 1),
(332, 'ATFP-01ATFP-02', 'Premium Tognana Fry Pan 20 + 24 cm - Asta', '', 'ATFP-01ATFP-02', '', NULL, NULL, NULL, NULL, 1),
(333, 'ATFP-02', 'Panci Penggorengan Premium Tognana Fry Pan 24 cm', '', 'ATFP-02', '', NULL, NULL, NULL, NULL, 1),
(334, 'ATSP-01', 'Panci Susu Milk Pan Premium Tognana Sauce Pan 18 cm', '', 'ATSP-01', '', NULL, NULL, NULL, NULL, 1),
(335, 'ATST4-Tognana', 'Premium Tognana Set 4 (Fry Pan 20 - Fry Pan 24 - Wok - Casserole)', '', 'ATST4-Tognana', '', NULL, NULL, NULL, NULL, 1),
(336, 'ATWO-01', 'Panci Wok Premium + Tutup Kaca Tognana Wok 28 cm', '', 'ATWO-01', '', NULL, NULL, NULL, NULL, 1),
(337, 'ATWO-01ASTS-01BL', 'Tognana wok + soup ladle silikon blue', '', 'ATWO-01ASTS-01BL', '', NULL, NULL, NULL, NULL, 1),
(338, 'ATWO-01ASTS-01PK', 'Tognana wok + soup ladle silikon pink', '', 'ATWO-01ASTS-01PK', '', NULL, NULL, NULL, NULL, 1),
(339, 'ATWO-01CARD', 'Tognana Wok Set Hadiah', '', 'ATWO-01CARD', '', NULL, NULL, NULL, NULL, 1),
(340, 'BB-ASBSLB', 'Bakeware Loyang Bulat - Asta', '', 'BB-ASBSLB', '', NULL, NULL, NULL, NULL, 1),
(341, 'BB-ASBSLP', 'Bakeware Loyang Persegi - Asta', '', 'BB-ASBSLP', '', NULL, NULL, NULL, NULL, 1),
(342, 'BB-ASBSMS', 'Bakeware - Measuring Spoon - Asta', '', 'BB-ASBSMS', '', NULL, NULL, NULL, NULL, 1),
(343, 'BB-ASBSSP', 'Bakeware - Spatula - Asta', '', 'BB-ASBSSP', '', NULL, NULL, NULL, NULL, 1),
(344, 'BB-ASBUM01CH', 'Refill Spin Mop Bulat', '', 'BB-ASBUM01CH', '', NULL, NULL, NULL, NULL, 1),
(345, 'BB-ASFM01GL4PCS', 'Gelas Familia 4 pcs (Set) 4 Warna', '', 'BB-ASFM01GL4PCS', '', NULL, NULL, NULL, NULL, 1),
(346, 'BB-ASFM01GLBG', 'Gelas Beige Wheatstraw', '', 'BB-ASFM01GLBG', '', NULL, NULL, NULL, NULL, 1),
(347, 'BB-ASFM01GLBL', 'Gelas Blue Wheatstraw', '', 'BB-ASFM01GLBL', '', NULL, NULL, NULL, NULL, 1),
(348, 'BB-ASFM01GLGR', 'Gelas Green Wheatstraw', '', 'BB-ASFM01GLGR', '', NULL, NULL, NULL, NULL, 1),
(349, 'BB-ASFM01GLPK', 'Gelas Pink Wheatstraw', '', 'BB-ASFM01GLPK', '', NULL, NULL, NULL, NULL, 1),
(350, 'BB-ASFM01MK4PCS', 'Mangkok Familia 4 pcs (Set) 4 Warna', '', 'BB-ASFM01MK4PCS', '', NULL, NULL, NULL, NULL, 1),
(351, 'BB-ASFM01MKBG', 'Mangkok Beige Wheatstraw', '', 'BB-ASFM01MKBG', '', NULL, NULL, NULL, NULL, 1),
(352, 'BB-ASFM01MKBL', 'Mangkok Blue Wheatstraw', '', 'BB-ASFM01MKBL', '', NULL, NULL, NULL, NULL, 1),
(353, 'BB-ASFM01MKGR', 'Mangkok Green Wheatstraw', '', 'BB-ASFM01MKGR', '', NULL, NULL, NULL, NULL, 1),
(354, 'BB-ASFM01MKPK', 'Mangkok Pink Wheatstraw', '', 'BB-ASFM01MKPK', '', NULL, NULL, NULL, NULL, 1),
(355, 'BB-ASFM01PR4PCS', 'Piring Famlia 4 pcs (Set) 4 Warna', '', 'BB-ASFM01PR4PCS', '', NULL, NULL, NULL, NULL, 1),
(356, 'BB-ASFM01PRBG', 'Piring Beige Wheatstraw', '', 'BB-ASFM01PRBG', '', NULL, NULL, NULL, NULL, 1),
(357, 'BB-ASFM01PRBL', 'Piring Blue Wheatstraw', '', 'BB-ASFM01PRBL', '', NULL, NULL, NULL, NULL, 1),
(358, 'BB-ASFM01PRGR', 'Piring Green Wheatstraw', '', 'BB-ASFM01PRGR', '', NULL, NULL, NULL, NULL, 1),
(359, 'BB-ASFM01PRPK', 'Piring Pink Wheatstraw', '', 'BB-ASFM01PRPK', '', NULL, NULL, NULL, NULL, 1),
(360, 'BB-ASPMBM', 'Botol Minyak - Asta', '', 'BB-ASPMBM', '', NULL, NULL, NULL, NULL, 1),
(361, 'BB-ASPMES', 'Pemisah Telur Plastik / Egg Seperator', '', 'BB-ASPMES', '', NULL, NULL, NULL, NULL, 1),
(362, 'BB-ASPS02MP17', 'Lemona Milk Pan 17 cm', '', 'BB-ASPS02MP17', '', NULL, NULL, NULL, NULL, 1),
(363, 'BB-ASPS02SL', 'Soup Ladle Nylon Black', '', 'BB-ASPS02SL', '', NULL, NULL, NULL, NULL, 1),
(364, 'BB-ASPS02SP23', 'Lemona Soup Pan+ Tutup 23 cm', '', 'BB-ASPS02SP23', '', NULL, NULL, NULL, NULL, 1),
(365, 'BB-ASPS02ST', 'Spatula Nylon Black', '', 'BB-ASPS02ST', '', NULL, NULL, NULL, NULL, 1),
(366, 'B-ASPS02WP32', 'Lemona Wok Pan 32 cm', '', 'B-ASPS02WP32', '', NULL, NULL, NULL, NULL, 1),
(367, 'B-ASPS07WKPK', 'DEFFECT Wok Pan Valentina', '', 'B-ASPS07WKPK', '', NULL, NULL, NULL, NULL, 1),
(368, 'BB-ASPS09CB', 'Talenan Kiwiz', '', 'BB-ASPS09CB', '', NULL, NULL, NULL, NULL, 1),
(369, 'BB-ASPS10WS', 'Talenan Rosie', '', 'BB-ASPS10WS', '', NULL, NULL, NULL, NULL, 1),
(370, 'BB-ASPS12WP', 'Valentina Wok Pan 32 cm', '', 'BB-ASPS12WP', '', NULL, NULL, NULL, NULL, 1),
(371, 'BB-ASSP01PC23', 'Stock Pot Steamer Set 23 cm', '', 'BB-ASSP01PC23', '', NULL, NULL, NULL, NULL, 1),
(372, 'BB-ASSP01PC23OF', 'Stock Pot Steamer Set 23 cm defect', '', 'BB-ASSP01PC23OF', '', NULL, NULL, NULL, NULL, 1),
(373, 'BB-ASSP01PC25', 'Stock Pot Steamer Set 25 cm', '', 'BB-ASSP01PC25', '', NULL, NULL, NULL, NULL, 1),
(374, 'BB-ASSP01PC25OF', 'Stock Pot Steamer Set 25 cm defect', '', 'BB-ASSP01PC25OF', '', NULL, NULL, NULL, NULL, 1),
(375, 'BB-ASSP01PC28', 'Stock Pot Steamer Set 28 cm', '', 'BB-ASSP01PC28', '', NULL, NULL, NULL, NULL, 1),
(376, 'BB-ASSP01PC28OF', 'Stock Pot Steamer Set 28 cm defect', '', 'BB-ASSP01PC28OF', '', NULL, NULL, NULL, NULL, 1),
(377, 'BB-ASSP01PC30', 'Stock Pot Steamer Set 30 cm', '', 'BB-ASSP01PC30', '', NULL, NULL, NULL, NULL, 1),
(378, 'BB-ASSP01PC30OF', 'Stock Pot Steamer Set 30 cm defect', '', 'BB-ASSP01PC30OF', '', NULL, NULL, NULL, NULL, 1),
(379, 'BB-KSK', 'Brush coklat', '', 'BB-KSK', '', NULL, NULL, NULL, NULL, 1),
(380, 'BB-PMCT', 'Cutter Adonan', '', 'BB-PMCT', '', NULL, NULL, NULL, NULL, 1),
(381, 'BB-PMKS', 'Brush White', '', 'BB-PMKS', '', NULL, NULL, NULL, NULL, 1),
(382, 'BB-ROCB01', 'hopping Board Wheat Straw Green', '', 'BB-ROCB01', '', NULL, NULL, NULL, NULL, 1),
(383, 'DLHAMPERS', 'Delight Hampers', '', 'DLHAMPERS', '', NULL, NULL, NULL, NULL, 1),
(384, 'EVHAMPERS', 'Eve Hampers', '', 'EVHAMPERS', '', NULL, NULL, NULL, NULL, 1),
(385, 'FSHAMPERS', 'Festive Hampers', '', 'FSHAMPERS', '', NULL, NULL, NULL, NULL, 1),
(386, 'FTHAMPERS', 'Bakeware + Pondan GF-PDSP', '', 'FTHAMPERS', '', NULL, NULL, NULL, NULL, 1),
(387, 'GF-BBBM', 'Bumbu Bamboe - Free Gift', '', 'GF-BBBM', '', NULL, NULL, NULL, NULL, 1),
(388, 'GF-OREO', 'Oreo - Free Gift', '', 'GF-OREO', '', NULL, NULL, NULL, NULL, 1),
(389, 'GF-STS', 'Set Stationary', '', 'GF-STS', '', NULL, NULL, NULL, NULL, 1),
(390, 'GF-TJJP', 'Teh Jawa Tubruk Premium (FREE)', '', 'GF-TJJP', '', NULL, NULL, NULL, NULL, 1),
(391, 'GF-TT-HORB', 'Tong Tji Harum Original Tea', '', 'GF-TT-HORB', '', NULL, NULL, NULL, NULL, 1),
(392, 'GF-TT-JPB', 'Tong Tji Jeruk Purut Box', '', 'GF-TT-JPB', '', NULL, NULL, NULL, NULL, 1),
(393, 'GF-TT-JTB', 'Tong Tji Jasmine tea Box', '', 'GF-TT-JTB', '', NULL, NULL, NULL, NULL, 1),
(394, 'GF-TT-LMB', 'Tong Tji Lemon Box', '', 'GF-TT-LMB', '', NULL, NULL, NULL, NULL, 1),
(395, 'GF-TT-ORB', 'Tong Tji Original Tea Box', '', 'GF-TT-ORB', '', NULL, NULL, NULL, NULL, 1),
(396, 'JYHAMPERS', 'Joyful hampers', '', 'JYHAMPERS', '', NULL, NULL, NULL, NULL, 1),
(397, 'KR20HAMPERS', 'Tognana Fry Pan 20 cm + ASFO-06', '', 'KR20HAMPERS', '', NULL, NULL, NULL, NULL, 1),
(398, 'KR24HAMPERS', 'Tognana Fry Pan 24 cm + ASFO-06', '', 'KR24HAMPERS', '', NULL, NULL, NULL, NULL, 1),
(399, 'LBHAMPERS', 'ASSB-06PK + ASFM-02BG', '', 'LBHAMPERS', '', NULL, NULL, NULL, NULL, 1),
(400, 'MBHAMPERS', 'ASWK-04 + ROSM- 08 + COOKIES', '', 'MBHAMPERS', '', NULL, NULL, NULL, NULL, 1),
(401, 'MRHAMPERS', 'Merry Hampers', '', 'MRHAMPERS', '', NULL, NULL, NULL, NULL, 1),
(402, 'NLHAMPERS', 'Noel Hampers', '', 'NLHAMPERS', '', NULL, NULL, NULL, NULL, 1),
(403, 'RDP-105A-1', '10,5\" Dinner Plate / Piring Ceper lis emas isi 1', '', 'RDP-105A-1', '', NULL, NULL, NULL, NULL, 1),
(404, 'RDP-105A-6', '10,5\" Dinner Plate / Piring Ceper lis emas isi 6', '', 'RDP-105A-6', '', NULL, NULL, NULL, NULL, 1),
(405, 'RDP-105B-1', '10,5\" Dinner Plate / Piring Ceper mahkota isi 1', '', 'RDP-105B-1', '', NULL, NULL, NULL, NULL, 1),
(406, 'RDP-105B-6', '10,5\" Dinner Plate / Piring Ceper mahkota isi 6', '', 'RDP-105B-6', '', NULL, NULL, NULL, NULL, 1),
(407, 'RDP-105C-1', '10,5\" Dinner Plate / Piring Ceper rose red isi 1', '', 'RDP-105C-1', '', NULL, NULL, NULL, NULL, 1),
(408, 'RDP-105C-6', '10,5\" Dinner Plate / Piring Ceper rose red isi 6', '', 'RDP-105C-6', '', NULL, NULL, NULL, NULL, 1),
(409, 'RMHAMPERS', 'ATCA-01 + ASSL- 01BL + COOKIES', '', 'RMHAMPERS', '', NULL, NULL, NULL, NULL, 1),
(410, 'ROBB-50', 'Baskom Jumbo Stainless Steel 50 cm', '', 'ROBB-50', '', NULL, NULL, NULL, NULL, 1),
(411, 'ROBB-60', 'Baskom Jumbo Stainless Steel 60 cm', '', 'ROBB-60', '', NULL, NULL, NULL, NULL, 1),
(412, 'ROBB-70', 'Baskom Jumbo Stainless Steel 70 cm', '', 'ROBB-70', '', NULL, NULL, NULL, NULL, 1),
(413, 'ROCB-01BG', 'Sikat Baju Beige', '', 'ROCB-01BG', '', NULL, NULL, NULL, NULL, 1),
(414, 'ROCB-01GR', 'Sikat Baju green', '', 'ROCB-01GR', '', NULL, NULL, NULL, NULL, 1),
(415, 'ROCB-01PK', 'SIkat Baju Pink', '', 'ROCB-01PK', '', NULL, NULL, NULL, NULL, 1),
(416, 'ROCB-02BL', 'Sikat Lantai Pembersih 02 Blue', '', 'ROCB-02BL', '', NULL, NULL, NULL, NULL, 1),
(417, 'ROCB-02GR', 'Sikat Lantai Pembersih 02 Green', '', 'ROCB-02GR', '', NULL, NULL, NULL, NULL, 1),
(418, 'ROCB-02PK', 'Sikat Lantai Pembersih 02 Pink', '', 'ROCB-02PK', '', NULL, NULL, NULL, NULL, 1),
(419, 'ROCM-01A', 'Capit Makanan ( Food Tong ) 24 cm', '', 'ROCM-01A', '', NULL, NULL, NULL, NULL, 1),
(420, 'ROCM-01B', 'Capit Makanan ( Food Tong ) 28 cm', '', 'ROCM-01B', '', NULL, NULL, NULL, NULL, 1),
(421, 'ROCP-01GR', 'Classic Pot Green - Royalton', '', 'ROCP-01GR', '', NULL, NULL, NULL, NULL, 1),
(422, 'ROCP01GR+CARD', 'Classic Pot Green + Card - Royalton', '', 'ROCP01GR+CARD', '', NULL, NULL, NULL, NULL, 1),
(423, 'ROCP-01RD', 'Classic Pot Red - Royalton', '', 'ROCP-01RD', '', NULL, NULL, NULL, NULL, 1),
(424, 'ROCP01RD+CARD', 'Classic Pot Red + Card - Royalton', '', 'ROCP01RD+CARD', '', NULL, NULL, NULL, NULL, 1),
(425, 'ROCP01-SETGR16CM', 'Classic Pot 16 cm Set Green', '', 'ROCP01-SETGR16CM', '', NULL, NULL, NULL, NULL, 1),
(426, 'ROCP01-SETGR18CM', 'Classic Pot 18 cm Set Green', '', 'ROCP01-SETGR18CM', '', NULL, NULL, NULL, NULL, 1),
(427, 'ROCP01-SETGR20CM', 'Classic Pot 20 cm Set Green', '', 'ROCP01-SETGR20CM', '', NULL, NULL, NULL, NULL, 1),
(428, 'ROCP01-SETGR22CM', 'Classic Pot 22 cm Set Green', '', 'ROCP01-SETGR22CM', '', NULL, NULL, NULL, NULL, 1),
(429, 'ROCP01-SETRD16CM', 'Classic Pot 16 cm Set Red', '', 'ROCP01-SETRD16CM', '', NULL, NULL, NULL, NULL, 1),
(430, 'ROCP01-SETRD18CM', 'Classic Pot 18 cm Set Red', '', 'ROCP01-SETRD18CM', '', NULL, NULL, NULL, NULL, 1),
(431, 'ROCP01-SETRD20CM', 'Classic Pot 20 cm Set Red', '', 'ROCP01-SETRD20CM', '', NULL, NULL, NULL, NULL, 1),
(432, 'ROCP01-SETRD22CM', 'Classic Pot 22 cm Set Red', '', 'ROCP01-SETRD22CM', '', NULL, NULL, NULL, NULL, 1),
(433, 'ROCP01-SETYL16CM', 'Classic Pot 16 cm Set Yellow', '', 'ROCP01-SETYL16CM', '', NULL, NULL, NULL, NULL, 1),
(434, 'ROCP01-SETYL18CM', 'Classic Pot 18 cm Set Yellow', '', 'ROCP01-SETYL18CM', '', NULL, NULL, NULL, NULL, 1),
(435, 'ROCP01-SETYL20CM', 'Classic Pot 20 cm Set Yellow', '', 'ROCP01-SETYL20CM', '', NULL, NULL, NULL, NULL, 1),
(436, 'ROCP01-SETYL22CM', 'Classic Pot 22 cm Set Yellow', '', 'ROCP01-SETYL22CM', '', NULL, NULL, NULL, NULL, 1),
(437, 'ROCP-01YL', 'Classic Pot Yellow - Royalton', '', 'ROCP-01YL', '', NULL, NULL, NULL, NULL, 1),
(438, 'ROCP01YL+CARD', 'Classic Pot Yellow + Card - Royalton', '', 'ROCP01YL+CARD', '', NULL, NULL, NULL, NULL, 1),
(439, 'RODC-01', 'Toples Cereal Storage Pet Feeder 1.5 kg', '', 'RODC-01', '', NULL, NULL, NULL, NULL, 1),
(440, 'RODC-02', 'Toples Cereal Storage Pet Feeder 2 kg', '', 'RODC-02', '', NULL, NULL, NULL, NULL, 1),
(441, 'RODC-02BL', 'Dry Food Container 2 Kg Blue - Royalton', '', 'RODC-02BL', '', NULL, NULL, NULL, NULL, 1),
(442, 'RODC-02WH', 'Dry Food Container 2 Kg White - Royalton', '', 'RODC-02WH', '', NULL, NULL, NULL, NULL, 1),
(443, 'RODL-01RD', 'D lima Red', '', 'RODL-01RD', '', NULL, NULL, NULL, NULL, 1),
(444, 'RODL01RD+CARD', 'D lima 5 Pcs Pot Set Red + CARD - Royalton', '', 'RODL01RD+CARD', '', NULL, NULL, NULL, NULL, 1),
(445, 'RODL-01TQ', 'DLima Turquoise', '', 'RODL-01TQ', '', NULL, NULL, NULL, NULL, 1),
(446, 'RODL01TQ+CARD', 'D lima 5 Pcs Pot Set Turquoise + CARD - Royalton', '', 'RODL01TQ+CARD', '', NULL, NULL, NULL, NULL, 1),
(447, 'ROFF-01G', 'Wadah Saji Prasmanan', '', 'ROFF-01G', '', NULL, NULL, NULL, NULL, 1),
(448, 'ROFF-01G2SET', 'Fast Food Glass Set (2) - Royalton', '', 'ROFF-01G2SET', '', NULL, NULL, NULL, NULL, 1),
(449, 'ROFL-051', 'Senter kepala 50 Watt 051 ( 2 baterai )', '', 'ROFL-051', '', NULL, NULL, NULL, NULL, 1),
(450, 'ROFL-052', 'Senter Kepala 50 Watt 052 ( 2 baterai )', '', 'ROFL-052', '', NULL, NULL, NULL, NULL, 1),
(451, 'ROFO-01', 'Garpu Makan 12 pcs', '', 'ROFO-01', '', NULL, NULL, NULL, NULL, 1),
(452, 'ROFO-0124', 'Garpu Makan 24 pcs', '', 'ROFO-0124', '', NULL, NULL, NULL, NULL, 1),
(453, 'ROFT-A30', 'Baki / Nampan Persegi Motif Bunga 30 cm', '', 'ROFT-A30', '', NULL, NULL, NULL, NULL, 1),
(454, 'ROFT-B35', 'Baki / Nampan Bentuk Ikan 35 cm', '', 'ROFT-B35', '', NULL, NULL, NULL, NULL, 1);
INSERT INTO `product` (`idproduct`, `sku`, `nama_produk`, `gambar`, `barcode`, `sni`, `created_by`, `created_date`, `updated_by`, `updated_date`, `status`) VALUES
(455, 'ROFT-C40', 'Baki / Nampan Oval Motif 40 cm', '', 'ROFT-C40', '', NULL, NULL, NULL, NULL, 1),
(456, 'ROFT-C50', 'Baki / Nampan Oval Motif 50 cm', '', 'ROFT-C50', '', NULL, NULL, NULL, NULL, 1),
(457, 'ROFT-D30', 'Baki / Nampan Segi 6 30 cm', '', 'ROFT-D30', '', NULL, NULL, NULL, NULL, 1),
(458, 'ROHL-031', 'Senter Kepala 30 Watt', '', 'ROHL-031', '', NULL, NULL, NULL, NULL, 1),
(459, 'ROKE-01', 'Keset Kaki Mutiara Super', '', 'ROKE-01', '', NULL, NULL, NULL, NULL, 1),
(460, 'ROKE-013PCS', 'Keset Kaki Mutiara Super isi 3pcs', '', 'ROKE-013PCS', '', NULL, NULL, NULL, NULL, 1),
(461, 'ROKE-02', 'Keset Kaki Jerami Super', '', 'ROKE-02', '', NULL, NULL, NULL, NULL, 1),
(462, 'ROKE-03', 'Keset Malaysia 1 pcs', '', 'ROKE-03', '', NULL, NULL, NULL, NULL, 1),
(463, 'ROKK-01', 'tchen Knife & Cover Green - Royalt', '', 'ROKK-01', '', NULL, NULL, NULL, NULL, 1),
(464, 'ROKS-01', 'Kitchen Scissors Black', '', 'ROKS-01', '', NULL, NULL, NULL, NULL, 1),
(465, 'RONS-01', 'Nylon Spatula Green - Royalton', '', 'RONS-01', '', NULL, NULL, NULL, NULL, 1),
(466, 'ROOP-01', 'Oil Pot Saringan Minyak 1.3 L', '', 'ROOP-01', '', NULL, NULL, NULL, NULL, 1),
(467, 'ROOP-02', 'Oil Pot Saringan Minyak 1.8 L', '', 'ROOP-02', '', NULL, NULL, NULL, NULL, 1),
(468, 'ROOP-03', 'Oil Pot Saringan Minyak 1.3 L + tatakan', '', 'ROOP-03', '', NULL, NULL, NULL, NULL, 1),
(469, 'ROOP-05', 'Oil Pot Saringan Minyak 1.8 L + tatakan', '', 'ROOP-05', '', NULL, NULL, NULL, NULL, 1),
(470, 'ROOS-01', 'Oil Strainer / Saringan Minyak 18 cm', '', 'ROOS-01', '', NULL, NULL, NULL, NULL, 1),
(471, 'ROOS-02', 'Oil Strainer / Saringan Minyak 22 cm', '', 'ROOS-02', '', NULL, NULL, NULL, NULL, 1),
(472, 'ROOS-03', 'Oil Strainer / Saringan Minyak 24 cm', '', 'ROOS-03', '', NULL, NULL, NULL, NULL, 1),
(473, 'ROOS-05', 'Oil Strainer / Saringan Minyak 28 cm', '', 'ROOS-05', '', NULL, NULL, NULL, NULL, 1),
(474, 'ROOS-05A', 'Oil Strainer / Saringan Minyak 30 cm', '', 'ROOS-05A', '', NULL, NULL, NULL, NULL, 1),
(475, 'ROOS-06', 'Oil Strainer / Saringan Minyak 32 cm', '', 'ROOS-06', '', NULL, NULL, NULL, NULL, 1),
(476, 'ROOS-07', 'Oil Strainer / Saringan Minyak 36 cm', '', 'ROOS-07', '', NULL, NULL, NULL, NULL, 1),
(477, 'ROP-925A-1', '9,25\" Omega Plate / Piring Lontong lis emas isi 1', '', 'ROP-925A-1', '', NULL, NULL, NULL, NULL, 1),
(478, 'ROP-925A-6', '9,25\" Omega Plate / Piring Lontong lis emas isi 6', '', 'ROP-925A-6', '', NULL, NULL, NULL, NULL, 1),
(479, 'ROP-925B-1', '9,25\" Omega Plate / Piring Lontong mahkota isi 1', '', 'ROP-925B-1', '', NULL, NULL, NULL, NULL, 1),
(480, 'ROP-925B-6', '9,25\" Omega Plate / Piring Lontong mahkota isi 6', '', 'ROP-925B-6', '', NULL, NULL, NULL, NULL, 1),
(481, 'ROP-925C-1', '9,25\" Omega Plate / Piring Lontong rose red isi 1', '', 'ROP-925C-1', '', NULL, NULL, NULL, NULL, 1),
(482, 'ROP-925C-6', '9,25\" Omega Plate / Piring Lontong rose red isi 6', '', 'ROP-925C-6', '', NULL, NULL, NULL, NULL, 1),
(483, 'ROSB-01', 'Tas Belanja Shopping Bag', '', 'ROSB-01', '', NULL, NULL, NULL, NULL, 1),
(484, 'ROSI-01', 'Sapu Ijuk Super', '', 'ROSI-01', '', NULL, NULL, NULL, NULL, 1),
(485, 'ROSM-07', 'Gelas Mug Stainless Steel Mug 7 cm ( Tanpa Tutup )', '', 'ROSM-07', '', NULL, NULL, NULL, NULL, 1),
(486, 'ROSM-08', 'Gelas Mug Stainless Steel Mug 8 cm', '', 'ROSM-08', '', NULL, NULL, NULL, NULL, 1),
(487, 'ROSM-09', 'Gelas Mug Stainless Steel Mug 9 cm', '', 'ROSM-09', '', NULL, NULL, NULL, NULL, 1),
(488, 'ROSM-10', 'Gelas Mug Stainless Steel Mug 10 cm', '', 'ROSM-10', '', NULL, NULL, NULL, NULL, 1),
(489, 'ROSM-11', 'Gelas Mug Stainless Steel Mug 11 cm', '', 'ROSM-11', '', NULL, NULL, NULL, NULL, 1),
(490, 'ROSM-12', 'Gelas Mug Stainless Steel Mug 12 cm', '', 'ROSM-12', '', NULL, NULL, NULL, NULL, 1),
(491, 'ROSP-01', 'Sendok Makan 12 pcs', '', 'ROSP-01', '', NULL, NULL, NULL, NULL, 1),
(492, 'ROSP-0124', 'Sendok Makan 24 pcs', '', 'ROSP-0124', '', NULL, NULL, NULL, NULL, 1),
(493, 'ROST-01', 'Sapu Taman Super', '', 'ROST-01', '', NULL, NULL, NULL, NULL, 1),
(494, 'ROTB-10BK', 'Milk Tea Bucket Water Jug 10 L - Black', '', 'ROTB-10BK', '', NULL, NULL, NULL, NULL, 1),
(495, 'ROTB-10RD', 'Milk Tea Bucket Water Jug 10 L - Red', '', 'ROTB-10RD', '', NULL, NULL, NULL, NULL, 1),
(496, 'ROTB-12BK', 'Milk Tea Bucket Water Jug 12 L - Black', '', 'ROTB-12BK', '', NULL, NULL, NULL, NULL, 1),
(497, 'ROTB-12RD', 'Milk Tea Bucket Water Jug 12 L - Black', '', 'ROTB-12RD', '', NULL, NULL, NULL, NULL, 1),
(498, 'RSB-07A-1', '7\" Salad Bowl / Mangkok 7 lis emas isi 1', '', 'RSB-07A-1', '', NULL, NULL, NULL, NULL, 1),
(499, 'RSB-07A-6', '7\" Salad Bowl / Mangkok 7 lis emas isi 6', '', 'RSB-07A-6', '', NULL, NULL, NULL, NULL, 1),
(500, 'RSB-07B-1', '7\" Salad Bowl / Mangkok 7 mahkota isi 1', '', 'RSB-07B-1', '', NULL, NULL, NULL, NULL, 1),
(501, 'RSB-07B-6', '7\" Salad Bowl / Mangkok 7 mahkota isi 6', '', 'RSB-07B-6', '', NULL, NULL, NULL, NULL, 1),
(502, 'RSB-07C-1', '7\" Salad Bowl / Mangkok 7 rose red isi 1', '', 'RSB-07C-1', '', NULL, NULL, NULL, NULL, 1),
(503, 'RSB-07C-6', '7\" Salad Bowl / Mangkok 7 rose red isi 6', '', 'RSB-07C-6', '', NULL, NULL, NULL, NULL, 1),
(504, 'RSP-09A-1', '9\" Soup Plate / Piring Makan 9 lis emas isi 1', '', 'RSP-09A-1', '', NULL, NULL, NULL, NULL, 1),
(505, 'RSP-09A-6', '9\" Soup Plate / Piring Makan 9 lis emas isi 6', '', 'RSP-09A-6', '', NULL, NULL, NULL, NULL, 1),
(506, 'RSP-09B-1', '9\" Soup Plate / Piring Makan 9 mahkota isi 1', '', 'RSP-09B-1', '', NULL, NULL, NULL, NULL, 1),
(507, 'RSP-09B-6', '9\" Soup Plate / Piring Makan 9 mahkota isi 6', '', 'RSP-09B-6', '', NULL, NULL, NULL, NULL, 1),
(508, 'RSP-09C-1', '9\" Soup Plate / Piring Makan 9 rose red isi 1', '', 'RSP-09C-1', '', NULL, NULL, NULL, NULL, 1),
(509, 'RSP-09C-6', '9\" Soup Plate / Piring Makan 9 rose red isi 6', '', 'RSP-09C-6', '', NULL, NULL, NULL, NULL, 1),
(510, 'SLHAMPERS', 'ROCP-01GR + ASFO-05', '', 'SLHAMPERS', '', NULL, NULL, NULL, NULL, 1),
(511, 'SPHAMPERS', 'Spark Hampers', '', 'SPHAMPERS', '', NULL, NULL, NULL, NULL, 1),
(512, 'TBFM-935-PP08A', 'Grande Purple', '', 'TBFM-935-PP08A', '', NULL, NULL, NULL, NULL, 1),
(513, 'TBKD-935-YL08A', 'Grande Kid Yellow', '', 'TBKD-935-YL08A', '', NULL, NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_stock`
--

CREATE TABLE `product_stock` (
  `idproduct_stock` int(11) NOT NULL,
  `idproduct` int(11) NOT NULL,
  `idgudang` int(11) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `idrole` int(11) NOT NULL,
  `nama_role` varchar(200) DEFAULT NULL,
  `deskripsi` varchar(200) DEFAULT NULL,
  `created_by` varchar(200) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_by` varchar(200) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`idrole`, `nama_role`, `deskripsi`, `created_by`, `created_date`, `updated_by`, `updated_date`, `status`) VALUES
(1, 'Superadmin', 'Superadmin', 'Superadmin', '2025-04-25 14:31:16', 'Superadmin', '2025-04-25 14:31:21', 1),
(2, 'Admin Gudang', 'Admin Gudang', 'Superadmin', '2025-04-25 14:37:08', 'Superadmin', '2025-04-25 14:37:11', 1),
(3, 'Admin Stock', 'Admin Stock', 'Admin Stock', '2025-04-25 14:38:08', 'Superadmin', '2025-04-25 14:38:15', 1),
(4, 'Staff', 'Staff', 'Staff', '2025-04-25 14:38:35', 'Superadmin', '2025-04-25 14:38:41', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `iduser` int(11) NOT NULL,
  `idrole` int(11) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `foto` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `is_active` int(11) DEFAULT 1,
  `created_by` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `updated_by` varchar(100) DEFAULT NULL,
  `updated_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`iduser`, `idrole`, `username`, `email`, `foto`, `password`, `full_name`, `is_active`, `created_by`, `created_date`, `updated_by`, `updated_date`, `status`) VALUES
(4, 1, 'Superadmin', 'superadmin@gmail.com', 'foto_1745588888.png', '$2y$10$Hpc5o/QfB3CsVGezMUhjdOClU2c16NRCnKvTLxMz3LcqKfONFY.p.', 'Superadmin', 1, NULL, '2025-04-25 20:48:09', 'Superadmin', '2025-04-26 10:20:35', 1),
(5, 4, 'Haris', 'haris@gmail.com', 'foto_1745724577.jpg', '$2y$10$h0nquo0wBJM/t6hUkgXhb.ggchVQ.byceneATYa9t9P6u4vepdY4e', 'Haris', 1, 'Superadmin', '2025-04-27 10:29:37', 'Superadmin', '2025-04-27 10:29:37', 1),
(6, 3, 'Suzan', 'suzan@gmail.com', 'foto_1745724656.jpg', '$2y$10$LFArAXh3s.u5LR7IhMBvteImaZF8exujbfrc4UUmXJeHoHQGFjsMe', 'Suzan', 1, 'Superadmin', '2025-04-27 10:30:56', 'Superadmin', '2025-04-27 10:30:56', 1),
(7, 2, 'Mustofa', 'mustofa@gmail.com', 'foto_1745824637.jpg', '$2y$10$pxu.7X0NRrXnLj350mKWk.E8jR.PNVx75jCojD/8cI3i83LE.J71K', 'Mustofa', 1, 'Superadmin', '2025-04-28 14:17:17', 'Superadmin', '2025-04-28 14:17:17', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_instock`
--
ALTER TABLE `detail_instock`
  ADD PRIMARY KEY (`iddetail_instock`);

--
-- Indexes for table `detail_outstock`
--
ALTER TABLE `detail_outstock`
  ADD PRIMARY KEY (`iddetail_outstock`);

--
-- Indexes for table `gudang`
--
ALTER TABLE `gudang`
  ADD PRIMARY KEY (`idgudang`);

--
-- Indexes for table `instock`
--
ALTER TABLE `instock`
  ADD PRIMARY KEY (`idinstock`);

--
-- Indexes for table `outstock`
--
ALTER TABLE `outstock`
  ADD PRIMARY KEY (`idoutstock`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`idproduct`);

--
-- Indexes for table `product_stock`
--
ALTER TABLE `product_stock`
  ADD PRIMARY KEY (`idproduct_stock`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`idrole`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`iduser`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_instock`
--
ALTER TABLE `detail_instock`
  MODIFY `iddetail_instock` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detail_outstock`
--
ALTER TABLE `detail_outstock`
  MODIFY `iddetail_outstock` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gudang`
--
ALTER TABLE `gudang`
  MODIFY `idgudang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `instock`
--
ALTER TABLE `instock`
  MODIFY `idinstock` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `outstock`
--
ALTER TABLE `outstock`
  MODIFY `idoutstock` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `idproduct` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=514;

--
-- AUTO_INCREMENT for table `product_stock`
--
ALTER TABLE `product_stock`
  MODIFY `idproduct_stock` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `idrole` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `iduser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
