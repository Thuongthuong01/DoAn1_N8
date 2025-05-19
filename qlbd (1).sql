-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 19, 2025 lúc 09:44 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `qlbd`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bangdia`
--

CREATE TABLE `bangdia` (
  `MaBD` varchar(9) NOT NULL,
  `TenBD` varchar(50) NOT NULL,
  `Theloai` varchar(10) NOT NULL,
  `Dongia` varchar(10) NOT NULL,
  `NSX` varchar(50) NOT NULL,
  `Tinhtrang` varchar(20) NOT NULL,
  `ChatLuong` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `bangdia`
--

INSERT INTO `bangdia` (`MaBD`, `TenBD`, `Theloai`, `Dongia`, `NSX`, `Tinhtrang`, `ChatLuong`, `image`) VALUES
('1', 'shin', 'Phim', '1', '3wed', 'Đã thuê', NULL, 'SHOL0253.JPG'),
('2', '6yyyy', 'Phim', '2000', '3wed', 'Đang bảo trì', 'Trầy xước', 'meme-meo-bua-yody-vn-29.webp');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietphieunhap`
--

CREATE TABLE `chitietphieunhap` (
  `ID` int(11) NOT NULL,
  `MaPhieu` varchar(9) DEFAULT NULL,
  `MaBD` varchar(9) DEFAULT NULL,
  `GiaGoc` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chitietphieunhap`
--

INSERT INTO `chitietphieunhap` (`ID`, `MaPhieu`, `MaBD`, `GiaGoc`) VALUES
(13, '1', '1', 1),
(14, '2', '2', 2),
(15, '2', '3', 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietphieuthue`
--

CREATE TABLE `chitietphieuthue` (
  `MaCT` int(11) NOT NULL,
  `MaThue` int(11) DEFAULT NULL,
  `MaBD` varchar(10) DEFAULT NULL,
  `SoLuong` int(11) DEFAULT NULL,
  `DonGia` bigint(20) DEFAULT NULL,
  `ThanhTien` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chitietphieuthue`
--

INSERT INTO `chitietphieuthue` (`MaCT`, `MaThue`, `MaBD`, `SoLuong`, `DonGia`, `ThanhTien`) VALUES
(25, 18, '2', 1, 22, 88),
(26, 19, '1', 2, 1, 16),
(27, 20, '2', 3, 2000, 96000);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khachhang`
--

CREATE TABLE `khachhang` (
  `MaKH` varchar(50) NOT NULL,
  `TenKH` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `SDT` varchar(15) DEFAULT NULL,
  `Diachi` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Email` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `khachhang`
--

INSERT INTO `khachhang` (`MaKH`, `TenKH`, `SDT`, `Diachi`, `Email`) VALUES
('KH001', 'ưsdfghjk', '123456799', 'BG', 'tre22@gmail.com'),
('KH002', 'yyyyy', '0333333333', 'BG', 'demo123@gmail.com');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhacc`
--

CREATE TABLE `nhacc` (
  `MaNCC` varchar(9) NOT NULL,
  `TenNCC` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `SDT` int(11) DEFAULT NULL,
  `DiaChi` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nhacc`
--

INSERT INTO `nhacc` (`MaNCC`, `TenNCC`, `SDT`, `DiaChi`) VALUES
('ncc1', 'Thuong', 333333333, 'Hà Nội');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieunhap`
--

CREATE TABLE `phieunhap` (
  `MaPhieu` varchar(9) NOT NULL,
  `MaNCC` varchar(9) DEFAULT NULL,
  `NgayNhap` date DEFAULT NULL,
  `SoLuong` int(9) NOT NULL,
  `TongTien` int(20) NOT NULL,
  `MaAD` varchar(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `phieunhap`
--

INSERT INTO `phieunhap` (`MaPhieu`, `MaNCC`, `NgayNhap`, `SoLuong`, `TongTien`, `MaAD`) VALUES
('1', 'ncc1', '2025-05-15', 1, 1, NULL),
('2', 'ncc1', '2025-05-08', 2, 4, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieuthue`
--

CREATE TABLE `phieuthue` (
  `MaThue` int(11) NOT NULL,
  `MaKH` varchar(10) DEFAULT NULL,
  `NgayThue` date DEFAULT NULL,
  `NgayTraDK` date DEFAULT NULL,
  `TongTien` bigint(20) DEFAULT NULL,
  `MaAD` varchar(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `phieuthue`
--

INSERT INTO `phieuthue` (`MaThue`, `MaKH`, `NgayThue`, `NgayTraDK`, `TongTien`, `MaAD`) VALUES
(18, 'KH002', '2025-05-14', '2025-05-18', 50088, NULL),
(19, 'KH001', '2025-05-14', '2025-05-22', 50016, '2'),
(20, 'KH002', '2025-05-15', '2025-05-31', 146000, '2');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieutra`
--

CREATE TABLE `phieutra` (
  `MaTra` int(11) NOT NULL,
  `MaThue` int(11) NOT NULL,
  `MaKH` varchar(50) NOT NULL,
  `NgayTraTT` date NOT NULL,
  `ChatLuong` varchar(50) NOT NULL,
  `TraMuon` int(11) DEFAULT 0,
  `TienPhat` decimal(10,2) DEFAULT 0.00,
  `TienTra` decimal(15,2) NOT NULL DEFAULT 0.00,
  `MaAD` varchar(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `phieutra`
--

INSERT INTO `phieutra` (`MaTra`, `MaThue`, `MaKH`, `NgayTraTT`, `ChatLuong`, `TraMuon`, `TienPhat`, `TienTra`, `MaAD`) VALUES
(12, 18, 'KH002', '2025-05-30', 'Tốt', 12, 1200.00, 48800.00, '2'),
(13, 20, 'KH002', '2025-06-08', 'Trầy xước', 8, 1400.00, 48600.00, '2');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `quantri`
--

CREATE TABLE `quantri` (
  `MaAD` varchar(9) NOT NULL,
  `TenAD` varchar(50) NOT NULL,
  `Pass` varchar(255) NOT NULL,
  `SDT` int(11) NOT NULL,
  `Email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `quantri`
--

INSERT INTO `quantri` (`MaAD`, `TenAD`, `Pass`, `SDT`, `Email`) VALUES
('2', 'admin', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 347795984, 'nhungmin0712@gmail.com');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bangdia`
--
ALTER TABLE `bangdia`
  ADD PRIMARY KEY (`MaBD`);

--
-- Chỉ mục cho bảng `chitietphieunhap`
--
ALTER TABLE `chitietphieunhap`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `uk_chitietphieunhap_maBD` (`MaBD`),
  ADD KEY `fk_chitietphieunhap_phieunhap` (`MaPhieu`);

--
-- Chỉ mục cho bảng `chitietphieuthue`
--
ALTER TABLE `chitietphieuthue`
  ADD PRIMARY KEY (`MaCT`),
  ADD KEY `MaBD` (`MaBD`),
  ADD KEY `chitietphieuthue_ibfk_1` (`MaThue`);

--
-- Chỉ mục cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  ADD PRIMARY KEY (`MaKH`);

--
-- Chỉ mục cho bảng `nhacc`
--
ALTER TABLE `nhacc`
  ADD PRIMARY KEY (`MaNCC`);

--
-- Chỉ mục cho bảng `phieunhap`
--
ALTER TABLE `phieunhap`
  ADD PRIMARY KEY (`MaPhieu`),
  ADD KEY `fk_phieunhap_nhacc` (`MaNCC`),
  ADD KEY `fk_phieunhap_quantri` (`MaAD`);

--
-- Chỉ mục cho bảng `phieuthue`
--
ALTER TABLE `phieuthue`
  ADD PRIMARY KEY (`MaThue`),
  ADD KEY `MaKH` (`MaKH`),
  ADD KEY `fk_phieuthue_quantri` (`MaAD`);

--
-- Chỉ mục cho bảng `phieutra`
--
ALTER TABLE `phieutra`
  ADD PRIMARY KEY (`MaTra`),
  ADD KEY `MaThue` (`MaThue`),
  ADD KEY `MaKH` (`MaKH`),
  ADD KEY `fk_phieutra_quantri` (`MaAD`);

--
-- Chỉ mục cho bảng `quantri`
--
ALTER TABLE `quantri`
  ADD PRIMARY KEY (`MaAD`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `chitietphieunhap`
--
ALTER TABLE `chitietphieunhap`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `chitietphieuthue`
--
ALTER TABLE `chitietphieuthue`
  MODIFY `MaCT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT cho bảng `phieuthue`
--
ALTER TABLE `phieuthue`
  MODIFY `MaThue` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT cho bảng `phieutra`
--
ALTER TABLE `phieutra`
  MODIFY `MaTra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bangdia`
--
ALTER TABLE `bangdia`
  ADD CONSTRAINT `fk_bangdia_maBD` FOREIGN KEY (`MaBD`) REFERENCES `chitietphieunhap` (`MaBD`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `chitietphieunhap`
--
ALTER TABLE `chitietphieunhap`
  ADD CONSTRAINT `fk_chitietphieunhap_phieunhap` FOREIGN KEY (`MaPhieu`) REFERENCES `phieunhap` (`MaPhieu`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `chitietphieuthue`
--
ALTER TABLE `chitietphieuthue`
  ADD CONSTRAINT `chitietphieuthue_ibfk_1` FOREIGN KEY (`MaThue`) REFERENCES `phieuthue` (`MaThue`) ON DELETE CASCADE,
  ADD CONSTRAINT `chitietphieuthue_ibfk_2` FOREIGN KEY (`MaBD`) REFERENCES `bangdia` (`MaBD`);

--
-- Các ràng buộc cho bảng `phieunhap`
--
ALTER TABLE `phieunhap`
  ADD CONSTRAINT `fk_phieunhap_nhacc` FOREIGN KEY (`MaNCC`) REFERENCES `nhacc` (`MaNCC`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_phieunhap_quantri` FOREIGN KEY (`MaAD`) REFERENCES `quantri` (`MaAD`);

--
-- Các ràng buộc cho bảng `phieuthue`
--
ALTER TABLE `phieuthue`
  ADD CONSTRAINT `fk_phieuthue_quantri` FOREIGN KEY (`MaAD`) REFERENCES `quantri` (`MaAD`),
  ADD CONSTRAINT `phieuthue_ibfk_1` FOREIGN KEY (`MaKH`) REFERENCES `khachhang` (`MaKH`);

--
-- Các ràng buộc cho bảng `phieutra`
--
ALTER TABLE `phieutra`
  ADD CONSTRAINT `fk_phieutra_khachhang` FOREIGN KEY (`MaKH`) REFERENCES `khachhang` (`MaKH`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_phieutra_phieuthue` FOREIGN KEY (`MaThue`) REFERENCES `phieuthue` (`MaThue`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_phieutra_quantri` FOREIGN KEY (`MaAD`) REFERENCES `quantri` (`MaAD`),
  ADD CONSTRAINT `phieutra_ibfk_1` FOREIGN KEY (`MaThue`) REFERENCES `phieuthue` (`MaThue`),
  ADD CONSTRAINT `phieutra_ibfk_2` FOREIGN KEY (`MaKH`) REFERENCES `khachhang` (`MaKH`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
