-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 14, 2025 at 06:42 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `qlbd`
--

-- --------------------------------------------------------

--
-- Table structure for table `bangdia`
--

CREATE TABLE `bangdia` (
  `MaBD` varchar(9) NOT NULL,
  `TenBD` varchar(50) NOT NULL,
  `Theloai` varchar(10) NOT NULL,
  `Dongia` varchar(10) NOT NULL,
  `NSX` varchar(50) NOT NULL,
  `Tinhtrang` varchar(20) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bangdia`
--

INSERT INTO `bangdia` (`MaBD`, `TenBD`, `Theloai`, `Dongia`, `NSX`, `Tinhtrang`, `image`) VALUES
('10', 'Vùng Đất Linh Hồn ', 'Phim', '50000', 'Toshio Suzuki', 'Trống', '6.png'),
('11', 'Cô gái người sói (Wolf Children)', 'Phim', '40000', 'Yuichiro Saito', 'Trống', '7.png'),
('12', 'Album music BTS - 2021  ', 'Âm nhạc', '120000', 'Bighit Entertainment', 'Trống', '9.png'),
('13', '&#34;Thriller&#34; – Michael Jackson (1982)', 'Âm nhạc', '210000', 'Michael Jackson', 'Đã cho thuê', '11.png'),
('9', 'Doreamon ', 'Âm nhạc', '23000', 'Hồng Nhung', 'Đã cho thuê', '1.png');

-- --------------------------------------------------------

--
-- Table structure for table `chitetphieunhap`
--

CREATE TABLE `chitetphieunhap` (
  `MaPhieu` varchar(9) NOT NULL,
  `MaBD` varchar(9) DEFAULT NULL,
  `soluong` varchar(30) NOT NULL,
  `tongtien` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chitetphieunhap`
--

INSERT INTO `chitetphieunhap` (`MaPhieu`, `MaBD`, `soluong`, `tongtien`) VALUES
('1', 'bd1', '4', '2.0000'),
('2', 'bd2', '5', '34.0000'),
('3', 'bd3', '45', '45.0000'),
('4', 'bd4', '24', '23.0000'),
('5', 'bd5', '43', '12.0000');

-- --------------------------------------------------------

--
-- Table structure for table `chitiethoadon`
--

CREATE TABLE `chitiethoadon` (
  `MaHD` varchar(9) NOT NULL,
  `MaBD` varchar(9) NOT NULL,
  `soluong` varchar(30) NOT NULL,
  `dongia` varchar(10) NOT NULL,
  `tongtien` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chitiethoadon`
--

INSERT INTO `chitiethoadon` (`MaHD`, `MaBD`, `soluong`, `dongia`, `tongtien`) VALUES
('1', 'bd1', '2', '40000', '340000'),
('2', 'bd2', '34', '30000', '343000'),
('3', 'bd3', '23', '20000', '323000'),
('4', 'bd4', '23', '60000', '560000'),
('5', 'bd5', '12', '40000', '400000');

-- --------------------------------------------------------

--
-- Table structure for table `hoadonthue`
--

CREATE TABLE `hoadonthue` (
  `MaHD` varchar(9) NOT NULL,
  `Ngaythue` date NOT NULL,
  `NgaytraDK` date NOT NULL,
  `NgaytraTT` date NOT NULL,
  `MaKH` varchar(9) NOT NULL,
  `MaBD` varchar(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hoadonthue`
--

INSERT INTO `hoadonthue` (`MaHD`, `Ngaythue`, `NgaytraDK`, `NgaytraTT`, `MaKH`, `MaBD`) VALUES
('1', '2024-12-07', '2024-12-08', '2024-12-09', 'kh1', '0'),
('2', '2024-12-08', '2024-12-09', '2024-12-09', 'kh2', '0'),
('3', '2024-11-08', '2024-11-10', '2024-11-11', 'kh3', '0'),
('4', '2025-02-07', '2025-02-09', '2025-02-09', 'kh4', '0'),
('5', '2025-03-07', '2025-03-09', '2025-03-11', 'kh5', '0');

-- --------------------------------------------------------

--
-- Table structure for table `khachhang`
--

CREATE TABLE `khachhang` (
  `MaKH` varchar(50) NOT NULL,
  `TenKH` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `SDT` int(11) DEFAULT NULL,
  `Diachi` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Email` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `khachhang`
--

INSERT INTO `khachhang` (`MaKH`, `TenKH`, `SDT`, `Diachi`, `Email`) VALUES
('KH006', 'Ngân', 2132132332, 'Quốc Oai, Hà Nội', 'nhungmin0712@gmail.com'),
('KH007', 'Hồng Ngọc', 2147483647, 'Hà Giang', 'Ngoc123@gmail.com'),
('kh1', 'Hồng Nhung', 397114951, 'hà nội', 'nhung@gmail.com'),
('kh2', 'tuấn', 323183322, 'hà tĩnh', 'tuan@gmail.com'),
('kh3', 'Hưng Nguyễn', 299383722, 'hưng yên', 'chi@gmail.com'),
('kh4', 'ngọc', 837465283, 'hà đông', 'ngoc@gmail.com'),
('kh5', 'thương', 487336288, 'bắc giang', 'thuong@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `nhacc`
--

CREATE TABLE `nhacc` (
  `MaNCC` varchar(9) NOT NULL,
  `TenNCC` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `SDT` int(11) DEFAULT NULL,
  `DiaChi` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nhacc`
--

INSERT INTO `nhacc` (`MaNCC`, `TenNCC`, `SDT`, `DiaChi`) VALUES
('ncc1', 'Nhật', 738233288, 'Hà Nội'),
('ncc2', 'Hải', 847362738, 'Hà Nam'),
('ncc3', 'Đức', 384767878, 'Nghệ An'),
('ncc4', 'Indonesia', 333443433, 'Quảng Ngãi');

-- --------------------------------------------------------

--
-- Table structure for table `phieunhap`
--

CREATE TABLE `phieunhap` (
  `MaPhieu` varchar(9) NOT NULL,
  `MaNCC` varchar(9) DEFAULT NULL,
  `NgayLap` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `phieunhap`
--

INSERT INTO `phieunhap` (`MaPhieu`, `MaNCC`, `NgayLap`) VALUES
('phieu1', 'ncc1', '2025-06-07'),
('phieu2', 'ncc2', '2024-07-08'),
('phieu3', 'ncc3', '2025-03-06'),
('phieu4', 'ncc4', '2023-07-06'),
('phieu5', 'ncc5', '2025-03-03');

-- --------------------------------------------------------

--
-- Table structure for table `phieuthue`
--

CREATE TABLE `phieuthue` (
  `MaThue` int(11) NOT NULL,
  `MaBD` varchar(9) NOT NULL,
  `MaKH` varchar(50) NOT NULL,
  `NgayThue` date NOT NULL,
  `NgayTraDK` date NOT NULL,
  `SoLuong` int(9) NOT NULL,
  `TongTien` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `phieuthue`
--

INSERT INTO `phieuthue` (`MaThue`, `MaBD`, `MaKH`, `NgayThue`, `NgayTraDK`, `SoLuong`, `TongTien`) VALUES
(6, '11', 'kh2', '2025-12-07', '2025-12-10', 2, 100000),
(10, '10', 'kh2', '2025-12-07', '2025-12-15', 2, 800000),
(11, '11', 'kh1', '2025-11-08', '2025-11-10', 2, 160000);

-- --------------------------------------------------------

--
-- Table structure for table `phieutra`
--

CREATE TABLE `phieutra` (
  `MaTra` int(11) NOT NULL,
  `MaThue` int(11) NOT NULL,
  `MaKH` varchar(50) NOT NULL,
  `NgayTraTT` date NOT NULL,
  `ChatLuong` varchar(50) NOT NULL,
  `TraMuon` int(11) DEFAULT 0,
  `TienPhat` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `phieutra`
--

INSERT INTO `phieutra` (`MaTra`, `MaThue`, `MaKH`, `NgayTraTT`, `ChatLuong`, `TraMuon`, `TienPhat`) VALUES
(1, 10, 'kh2', '2025-12-16', 'Tốt', 1, 0.00),
(3, 6, 'kh2', '2025-12-15', 'Trầy xước', 5, 22000.00);

-- --------------------------------------------------------

--
-- Table structure for table `quantri`
--

CREATE TABLE `quantri` (
  `MaAD` varchar(9) NOT NULL,
  `TenAD` varchar(50) NOT NULL,
  `Pass` varchar(255) NOT NULL,
  `SDT` int(11) NOT NULL,
  `Email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quantri`
--

INSERT INTO `quantri` (`MaAD`, `TenAD`, `Pass`, `SDT`, `Email`) VALUES
('2', 'admin', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 347795984, 'nhungmin0712@gmail.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bangdia`
--
ALTER TABLE `bangdia`
  ADD PRIMARY KEY (`MaBD`);

--
-- Indexes for table `chitetphieunhap`
--
ALTER TABLE `chitetphieunhap`
  ADD PRIMARY KEY (`MaPhieu`);

--
-- Indexes for table `chitiethoadon`
--
ALTER TABLE `chitiethoadon`
  ADD PRIMARY KEY (`MaHD`);

--
-- Indexes for table `hoadonthue`
--
ALTER TABLE `hoadonthue`
  ADD PRIMARY KEY (`MaHD`);

--
-- Indexes for table `khachhang`
--
ALTER TABLE `khachhang`
  ADD PRIMARY KEY (`MaKH`);

--
-- Indexes for table `nhacc`
--
ALTER TABLE `nhacc`
  ADD PRIMARY KEY (`MaNCC`);

--
-- Indexes for table `phieunhap`
--
ALTER TABLE `phieunhap`
  ADD PRIMARY KEY (`MaPhieu`);

--
-- Indexes for table `phieuthue`
--
ALTER TABLE `phieuthue`
  ADD PRIMARY KEY (`MaThue`);

--
-- Indexes for table `phieutra`
--
ALTER TABLE `phieutra`
  ADD PRIMARY KEY (`MaTra`),
  ADD KEY `MaThue` (`MaThue`),
  ADD KEY `MaKH` (`MaKH`);

--
-- Indexes for table `quantri`
--
ALTER TABLE `quantri`
  ADD PRIMARY KEY (`MaAD`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `phieuthue`
--
ALTER TABLE `phieuthue`
  MODIFY `MaThue` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `phieutra`
--
ALTER TABLE `phieutra`
  MODIFY `MaTra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `phieutra`
--
ALTER TABLE `phieutra`
  ADD CONSTRAINT `phieutra_ibfk_1` FOREIGN KEY (`MaThue`) REFERENCES `phieuthue` (`MaThue`),
  ADD CONSTRAINT `phieutra_ibfk_2` FOREIGN KEY (`MaKH`) REFERENCES `khachhang` (`MaKH`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
