-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 24, 2025 lúc 09:30 PM
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
('BD001', 'Ponyo - Cô bé người cá ', 'Phim', '15000', 'Studio Ghibli', 'Trống', 'Tốt', '2.png'),
('BD002', 'Vùng đất linh hồn', 'Phim', '18000', 'Studio Ghibli', 'Trống', 'Tốt', '6.png'),
('BD005', 'Công chúa Mononoke', 'Phim', '17000', 'Studio Ghibli', 'Trống', 'Tốt', '7.png'),
('BD007', 'Lâu đài di động của Howl', 'Phim', '18000', 'Studio Ghibli', 'Trống', 'Tốt', '8.png'),
('BD009', 'Your Name', 'Phim', '20000', 'CoMix Wave Films', 'Đã thuê', 'Tốt', '4.png'),
('BD013', 'Hàng xóm của tôi là Totoro', 'Phim', '16000', 'Studio Ghibli', 'Trống', 'Tốt', '3.png'),
('BD015', 'Happy Birthday', 'Âm nhạc', '10000', 'Universal Music', 'Đã thuê', 'Tốt', '13.png');

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
(25, 'PN001', 'BD001', 10000),
(26, 'PN001', 'BD002', 12000),
(27, 'PN001', 'BD003', 9000),
(28, 'PN002', 'BD004', 15000),
(29, 'PN002', 'BD005', 14000),
(30, 'PN003', 'BD006', 11000),
(31, 'PN003', 'BD007', 10000),
(32, 'PN004', 'BD008', 13000),
(33, 'PN004', 'BD009', 12500),
(34, 'PN005', 'BD010', 11500),
(35, 'PN005', 'BD011', 9000),
(36, 'PN006', 'BD012', 8000),
(37, 'PN007', 'BD013', 10000),
(38, 'PN007', 'BD014', 9500),
(39, 'PN008', 'BD015', 12000);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietphieuthue`
--

CREATE TABLE `chitietphieuthue` (
  `MaCT` int(11) NOT NULL,
  `MaThue` int(11) DEFAULT NULL,
  `MaBD` varchar(10) DEFAULT NULL,
  `SoLuong` int(11) DEFAULT NULL,
  `DonGia` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chitietphieuthue`
--

INSERT INTO `chitietphieuthue` (`MaCT`, `MaThue`, `MaBD`, `SoLuong`, `DonGia`) VALUES
(45, 41, 'BD001', 1, 15000),
(46, 42, 'BD005', 1, 17000),
(47, 42, 'BD013', 1, 16000),
(48, 43, 'BD015', 1, 10000),
(49, 44, 'BD009', 1, 20000);

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
('KH001', 'Nguyễn Văn An', '0912345678', 'Hà Nội', 'an.nguyen@gmail.com'),
('KH002', 'Trần Thị Bình', '0987654321', 'TP. Hồ Chí Minh', 'binhtran@yahoo.com'),
('KH003', 'Lê Quốc Cường', '0933221144', 'Đà Nẵng', 'cuongle@hotmail.com'),
('KH004', 'Phạm Minh Dũng', '0909090909', 'Cần Thơ', 'dungpm@gmail.com'),
('KH005', 'Hoàng Thảo My', '0966778899', 'Nha Trang', 'myhoang@gmail.com'),
('KH006', 'Vũ Hồng Sơn', '0977886655', 'Hải Phòng', 'son.vu@gmail.com'),
('KH007', 'Đinh Thị Hạnh', '0944556677', 'Huế', 'hanhdinh@yahoo.com'),
('KH008', 'Lý Minh Khoa', '0922334455', 'Bình Dương', 'khoaly@outlook.com'),
('KH009', 'Ngô Quỳnh Anh', '0933445566', 'Quảng Ninh', 'anhngo@gmail.com'),
('KH010', 'Tạ Hữu Tài', '0911223344', 'Vũng Tàu', 'taihieu@yahoo.com');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhacc`
--

CREATE TABLE `nhacc` (
  `MaNCC` varchar(9) NOT NULL,
  `TenNCC` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `SDT` varchar(15) DEFAULT NULL,
  `DiaChi` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nhacc`
--

INSERT INTO `nhacc` (`MaNCC`, `TenNCC`, `SDT`, `DiaChi`) VALUES
('NCC001', 'Phương Nam Film', '02838229999', '11 Nguyễn Huệ, Quận 1, TP.HCM'),
('NCC002', 'Sài Gòn CD', '0909888123', '120 Lý Chính Thắng, Quận 3, TP.HCM'),
('NCC003', 'Vafaco', '02838454567', '62 Trần Hưng Đạo, Quận 5, TP.HCM'),
('NCC004', 'Cửa hàng DVD Việt', '0938123456', '45 Hoàng Hoa Thám, Tân Bình, TP.HCM'),
('NCC006', 'Thanh Nhạc Media', '0987654321', '23 Trần Quang Diệu, Quận 3, TP.HCM'),
('NCC007', 'Hà Nội DVD Shop', '02437654321', '99 Đê La Thành, Đống Đa, Hà Nội'),
('NCC008', 'CD Nhạc Vàng', '0902222888', '12B Hùng Vương, Huế'),
('NCC009', 'Đĩa Game 4U', '0981111333', '55 Võ Văn Ngân, Thủ Đức, TP.HCM'),
('NCC010', 'MediaZone Đà Nẵng', '02363567890', '101 Nguyễn Văn Linh, Đà Nẵng'),
('NCC011', 'Tiệm Băng Đĩa Phú Nhuận', '0911567999', '66 Trường Sa, Quận Phú Nhuận, TP.HCM'),
('NCC012', 'Thế Giới CD & DVD', '0976555444', '8 Lê Hồng Phong, Nha Trang'),
('NCC013', 'Classic Movie Discs', '0965888777', '33 Nguyễn Tri Phương, Hà Nội'),
('NCC014', 'Giải Trí Audio', '0907321654', '104 Phan Đăng Lưu, Bình Thạnh, TP.HCM'),
('NCC015', 'Hương Nhạc Xưa', '0903777001', '77 Hòa Bình, Quận Tân Phú, TP.HCM');

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
('PN001', 'NCC001', '2025-05-01', 3, 1000000, '2'),
('PN002', 'NCC003', '2025-05-02', 2, 750000, '2'),
('PN003', 'NCC007', '2025-05-03', 2, 840000, '2'),
('PN004', 'NCC010', '2025-05-04', 2, 1440000, '2'),
('PN005', 'NCC006', '2025-05-05', 2, 960000, '2'),
('PN006', 'NCC008', '2025-05-06', 1, 300000, '2'),
('PN007', 'NCC012', '2025-05-07', 2, 1120000, '2'),
('PN008', 'NCC002', '2025-05-08', 1, 600000, '2');

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
(41, 'KH001', '2025-05-23', '2025-05-25', 80000, '2'),
(42, 'KH002', '2025-05-23', '2025-05-27', 182000, '2'),
(43, 'KH003', '2025-05-21', '2025-05-28', 120000, '2'),
(44, 'KH009', '2025-05-23', '2025-05-31', 210000, '2');

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
(17, 41, 'KH001', '2025-05-28', 'Tốt', 3, 2250.00, 47750.00, '2'),
(18, 42, 'KH002', '2025-05-25', 'Tốt', 0, 0.00, 50000.00, '2');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `quantri`
--

CREATE TABLE `quantri` (
  `MaAD` varchar(9) NOT NULL,
  `TenAD` varchar(50) NOT NULL,
  `Pass` varchar(255) NOT NULL,
  `SDT` varchar(15) NOT NULL,
  `Email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `quantri`
--

INSERT INTO `quantri` (`MaAD`, `TenAD`, `Pass`, `SDT`, `Email`) VALUES
('2', 'admin', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '347795984', 'nhungmin0712@gmail.com');

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
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT cho bảng `chitietphieuthue`
--
ALTER TABLE `chitietphieuthue`
  MODIFY `MaCT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT cho bảng `phieuthue`
--
ALTER TABLE `phieuthue`
  MODIFY `MaThue` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT cho bảng `phieutra`
--
ALTER TABLE `phieutra`
  MODIFY `MaTra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

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
