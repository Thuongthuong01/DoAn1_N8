-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 25, 2025 at 04:50 AM
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
  `MaBD` int(9) NOT NULL,
  `TenBD` varchar(50) NOT NULL,
  `Theloai` varchar(10) NOT NULL,
  `Dongia` varchar(10) NOT NULL,
  `NSX` varchar(50) NOT NULL,
  `Tinhtrang` varchar(10) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bangdia`
--

INSERT INTO `bangdia` (`MaBD`, `TenBD`, `Theloai`, `Dongia`, `NSX`, `Tinhtrang`, `image`) VALUES
(7, 'Ponyo - Cô bé người cá, 2008 ', 'Phim', '45000', 'Toshio Suzuki', 'mới', '2.png'),
(8, 'Doraemon - Xứ sở thần tiên ( version 4)  ', 'Phim', '56000', 'Fujiko F. Fujio', 'đã bán', '1.png'),
(9, 'Your Name ( Tên Cậu Là Gì ? )', 'Phim', '70000', 'Makoto Shinkai ', 'đang sửa c', '4.png'),
(10, 'Vùng Đất Linh Hồn ', 'Phim', '50000', 'Toshio Suzuki', 'mới', '6.png'),
(11, 'Cô gái người sói (Wolf Children)', 'Phim', '40000', 'Yuichiro Saito', 'mới', '7.png'),
(12, 'Album music BTS - 2021  ', 'Âm nhạc', '120000', 'Bighit Entertainment', 'mới', '10.png'),
(13, '&#34;Thriller&#34; – Michael Jackson (1982)', 'Âm nhạc', '210000', 'Michael Jackson', 'đã thuê', '11.png'),
(14, '&#34;1989&#34; – Taylor Swift (2014)', 'Âm nhạc', '110000', 'Taylor Swift', 'đã thuê', '12.png'),
(15, 'The Miseducation of Lauryn Hill  (1998)', 'Âm nhạc', '99000', 'Lauryn Hil', 'mới', '14.png'),
(16, 'Birthday Music - 2025', 'Âm nhạc', '50000', 'Báo thiếu nhi Việt Nam', 'mới', '13.png');

-- --------------------------------------------------------

--
-- Table structure for table `chitetphieunhap`
--

CREATE TABLE `chitetphieunhap` (
  `Maphieu` int(9) NOT NULL,
  `MaBD` varchar(9) NOT NULL,
  `soluong` varchar(30) NOT NULL,
  `tongtien` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chitetphieunhap`
--

INSERT INTO `chitetphieunhap` (`Maphieu`, `MaBD`, `soluong`, `tongtien`) VALUES
(1, 'bd1', '4', '2.0000'),
(2, 'bd2', '5', '34.0000'),
(3, 'bd3', '45', '45.0000'),
(4, 'bd4', '24', '23.0000'),
(5, 'bd5', '43', '12.0000');

-- --------------------------------------------------------

--
-- Table structure for table `chitiethoadon`
--

CREATE TABLE `chitiethoadon` (
  `MaHD` int(9) NOT NULL,
  `MaBD` varchar(50) NOT NULL,
  `soluong` varchar(30) NOT NULL,
  `dongia` varchar(10) NOT NULL,
  `tongtien` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chitiethoadon`
--

INSERT INTO `chitiethoadon` (`MaHD`, `MaBD`, `soluong`, `dongia`, `tongtien`) VALUES
(1, 'bd1', '2', '4.0000', '34.0000'),
(2, 'bd2', '34', '3.0000', '343.0000'),
(3, 'bd3', '23', '2.0000', '323.000'),
(4, 'bd4', '23', '6.0000', '56.0000'),
(5, 'bd5', '12', '4.0000', '343.0000');

-- --------------------------------------------------------

--
-- Table structure for table `hoadonthue`
--

CREATE TABLE `hoadonthue` (
  `MaHD` int(9) NOT NULL,
  `Ngaythue` date NOT NULL,
  `NgaytraDK` date NOT NULL,
  `NgaytraTT` date NOT NULL,
  `MaKH` varchar(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hoadonthue`
--

INSERT INTO `hoadonthue` (`MaHD`, `Ngaythue`, `NgaytraDK`, `NgaytraTT`, `MaKH`) VALUES
(1, '2024-12-07', '2024-12-08', '2024-12-09', 'kh1'),
(2, '2024-12-08', '2024-12-09', '2024-12-09', 'kh2'),
(3, '2024-11-08', '2024-11-10', '2024-11-11', 'kh3'),
(4, '2025-02-07', '2025-02-09', '2025-02-09', 'kh4'),
(5, '2025-03-07', '2025-03-09', '2025-03-11', 'kh5');

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
('kh1', 'nhung', 423422253, 'hà nội', 'nhung@gmail.com'),
('kh2', 'tuấn', 323183322, 'hà tĩnh', 'tuan@gmail.com'),
('kh3', 'hưng', 299383722, 'hưng yên', 'chi@gmail.com'),
('kh4', 'ngọc', 837465283, 'hà đông', 'ngoc@gmail.com'),
('kh5', 'thương', 487336288, 'bắc giang', 'thuong@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `nhacc`
--

CREATE TABLE `nhacc` (
  `MaNCC` varchar(50) NOT NULL,
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
('ncc4', 'Indonesia', 333443433, 'Quảng Ngãi'),
('ncc5', 'Mỹ', 334342222, 'Bình Định');

-- --------------------------------------------------------

--
-- Table structure for table `phieunhap`
--

CREATE TABLE `phieunhap` (
  `MAPHIEUNHAP` varchar(10) NOT NULL,
  `MANV` varchar(10) DEFAULT NULL,
  `NGAYNHAP` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `phieunhap`
--

INSERT INTO `phieunhap` (`MAPHIEUNHAP`, `MANV`, `NGAYNHAP`) VALUES
('phieu1', 'ncc1', '2025-06-07'),
('phieu2', 'ncc2', '2024-07-08'),
('phieu3', 'ncc3', '2025-03-06'),
('phieu4', 'ncc4', '2023-07-06'),
('phieu5', 'ncc5', '2025-03-03');

-- --------------------------------------------------------

--
-- Table structure for table `quantri`
--

CREATE TABLE `quantri` (
  `MaAD` int(9) NOT NULL,
  `TenAD` varchar(50) NOT NULL,
  `Pass` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quantri`
--

INSERT INTO `quantri` (`MaAD`, `TenAD`, `Pass`) VALUES
(2, 'admin', '40bd001563085fc35165329ea1ff5c5ecbdbbeef');

-- --------------------------------------------------------

--
-- Table structure for table `thetv`
--

CREATE TABLE `thetv` (
  `MATHE` varchar(10) NOT NULL,
  `MAKH` varchar(10) DEFAULT NULL,
  `NGAYDK` date DEFAULT NULL,
  `NGAYHETHAN` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `thetv`
--

INSERT INTO `thetv` (`MATHE`, `MAKH`, `NGAYDK`, `NGAYHETHAN`) VALUES
('the1', 'kh1', '2024-12-07', NULL),
('the2', 'kh2', '2023-05-06', NULL),
('the3', 'kh3', '2025-03-04', NULL),
('the4', 'kh4', '2024-07-05', NULL),
('the5', 'kh5', '2024-02-04', NULL);

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
  ADD PRIMARY KEY (`Maphieu`);

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
  ADD PRIMARY KEY (`MAPHIEUNHAP`);

--
-- Indexes for table `quantri`
--
ALTER TABLE `quantri`
  ADD PRIMARY KEY (`MaAD`);

--
-- Indexes for table `thetv`
--
ALTER TABLE `thetv`
  ADD PRIMARY KEY (`MATHE`),
  ADD KEY `MAKH` (`MAKH`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bangdia`
--
ALTER TABLE `bangdia`
  MODIFY `MaBD` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `chitetphieunhap`
--
ALTER TABLE `chitetphieunhap`
  MODIFY `Maphieu` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `chitiethoadon`
--
ALTER TABLE `chitiethoadon`
  MODIFY `MaHD` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `hoadonthue`
--
ALTER TABLE `hoadonthue`
  MODIFY `MaHD` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `quantri`
--
ALTER TABLE `quantri`
  MODIFY `MaAD` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `thetv`
--
ALTER TABLE `thetv`
  ADD CONSTRAINT `thetv_ibfk_1` FOREIGN KEY (`MAKH`) REFERENCES `khachhang` (`MaKH`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
