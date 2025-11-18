-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 18, 2025 lúc 02:58 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `nln_nlcs`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `binh_luan`
--

CREATE TABLE `binh_luan` (
  `ID_HH` varchar(5) NOT NULL,
  `ID_BL` varchar(5) NOT NULL,
  `ID_TK` char(15) NOT NULL,
  `BINH_LUAN` varchar(255) NOT NULL,
  `SO_SAO` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_don_hang`
--

CREATE TABLE `chi_tiet_don_hang` (
  `ID_DH` varchar(5) NOT NULL,
  `ID_HH` varchar(5) NOT NULL,
  `SO_LUONG_BAN_RA` int(11) NOT NULL,
  `don_gia_ban` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `chi_tiet_don_hang`
--

INSERT INTO `chi_tiet_don_hang` (`ID_DH`, `ID_HH`, `SO_LUONG_BAN_RA`, `don_gia_ban`) VALUES
('DH081', '00011', 2, 0),
('DH0B5', '00004', 1, 0),
('DH0B5', '00008', 1, 0),
('DH0D8', '00006', 4, 0),
('DH0D8', '00041', 1, 0),
('DH31E', '00003', 1, 0),
('DH706', '00004', 3, 0),
('DH706', '00008', 2, 0),
('DH7A2', '00002', 1, 0),
('DH7A2', '00033', 1, 0),
('DH9B2', '00006', 1, 0),
('DHA4D', '00096', 1, 0),
('DHA72', '00002', 1, 0),
('DHD08', '00001', 1, 0),
('DHD45', '00128', 1, 0),
('DHF32', '00003', 1, 0),
('DHFF3', '00026', 1, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_gio_hang`
--

CREATE TABLE `chi_tiet_gio_hang` (
  `ID_GH` varchar(5) NOT NULL,
  `ID_HH` varchar(5) NOT NULL,
  `SO_LUONG_SP` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `chi_tiet_gio_hang`
--

INSERT INTO `chi_tiet_gio_hang` (`ID_GH`, `ID_HH`, `SO_LUONG_SP`) VALUES
('GH31f', '00003', 1),
('GH444', '00002', 3),
('GH444', '00003', 1),
('GH444', '00013', 1),
('GH444', '00018', 2),
('GH444', '00019', 3),
('GH444', '00059', 1),
('GHab0', '00058', 1),
('GHab1', '00005', 1),
('GHcf3', '00059', 1),
('GHcf3', '00096', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_phieu_nhap`
--

CREATE TABLE `chi_tiet_phieu_nhap` (
  `ID_PN` varchar(5) NOT NULL,
  `ID_HH` varchar(5) NOT NULL,
  `SO_LUONG_NHAP` int(11) NOT NULL,
  `DON_GIA_NHAP` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_muc`
--

CREATE TABLE `danh_muc` (
  `ID_DM` varchar(5) NOT NULL,
  `TEN_DM` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='1. Th?c ph?m so ch?\r\n2. Rau c? qu?\r\n3. Tr i c y tu';

--
-- Đang đổ dữ liệu cho bảng `danh_muc`
--

INSERT INTO `danh_muc` (`ID_DM`, `TEN_DM`) VALUES
('DM01', 'Thực phẩm sơ chế'),
('DM02', 'Hải sản'),
('DM03', 'Rau củ quả'),
('DM04', 'Trái cây tươi');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dia_chi_giao_hang`
--

CREATE TABLE `dia_chi_giao_hang` (
  `ID_DIA_CHI` int(11) NOT NULL,
  `ID_TK` char(15) NOT NULL,
  `TEN_NGUOI_NHAN` varchar(250) NOT NULL,
  `SDT_GH` char(15) NOT NULL,
  `ID_TINH_TP` varchar(5) DEFAULT NULL,
  `TEN_TINH_TP` varchar(50) DEFAULT NULL,
  `ID_QUAN_HUYEN` varchar(5) DEFAULT NULL,
  `TEN_QUAN_HUYEN` varchar(100) DEFAULT NULL,
  `ID_XA_PHUONG` varchar(5) DEFAULT NULL,
  `TEN_XA_PHUONG` varchar(100) DEFAULT NULL,
  `DIA_CHI_CHI_TIET` varchar(250) DEFAULT NULL,
  `IS_DEFAULT` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `dia_chi_giao_hang`
--

INSERT INTO `dia_chi_giao_hang` (`ID_DIA_CHI`, `ID_TK`, `TEN_NGUOI_NHAN`, `SDT_GH`, `ID_TINH_TP`, `TEN_TINH_TP`, `ID_QUAN_HUYEN`, `TEN_QUAN_HUYEN`, `ID_XA_PHUONG`, `TEN_XA_PHUONG`, `DIA_CHI_CHI_TIET`, `IS_DEFAULT`) VALUES
(17, 'TK690a2d551daad', 'lam', '099', '24', 'Tỉnh Bắc Giang', '223', 'Huyện Hiệp Hòa', '7864', 'Xã Mai Trung', '123', 0),
(18, 'TK690a2d551daad', 'Quân', '099', '24', 'Tỉnh Bắc Giang', '222', 'Thị xã Việt Yên', '7795', 'Phường Nếnh', '123', 0),
(19, 'TK690a2d551daad', 'lam', '099', '24', 'Tỉnh Bắc Giang', '215', 'Huyện Yên Thế', '7288', 'Thị trấn Phồn Xương', '123', 0),
(20, 'TK690a2d551daad', 'ca', '099', '10', 'Tỉnh Lào Cai', '83', 'Huyện Mường Khương', '2788', 'Xã Bản Lầu', '222', 0),
(21, 'TK690a2d551daad', 'hoa', '011', '24', 'Tỉnh Bắc Giang', '215', 'Huyện Yên Thế', '7291', 'Xã Tân Sỏi', '43', 0),
(22, 'TK690a2d551daad', 'tấn', '099', '92', 'Thành phố Cần Thơ', '923', 'Quận Thốt Nốt', '31210', 'Phường Thới Thuận', '234', 0),
(23, 'TK690a2d551daad', 'lam', '099', '95', 'Tỉnh Bạc Liêu', '961', 'Huyện Hoà Bình', '31918', 'Xã Vĩnh Bình', '12', 1),
(26, 'TK690a2db982cee', 'dao', '02222', '22', 'Tỉnh Quảng Ninh', '203', 'Huyện Vân Đồn', '7018', 'Xã Bản Sen', '5', 0),
(28, 'TK690a2db982cee', 'dao', '02222', '25', 'Tỉnh Phú Thọ', '230', 'Huyện Đoan Hùng', '8044', 'Xã Chân Mộng', 't', 0),
(29, 'TK690a2db982cee', 'hoa', '02222', '22', 'Tỉnh Quảng Ninh', '203', 'Huyện Vân Đồn', '7024', 'Xã Quan Lạn', '34', 1),
(30, 'TK690a29dd24ac3', 'tram', '011111', '22', 'Tỉnh Quảng Ninh', '194', 'Thành phố Móng Cái', '6751', 'Phường Bình Ngọc', 'Hẻm 156', 0),
(31, 'TK69186339d8aab', 'teo', '09123', '24', 'Tỉnh Bắc Giang', '223', 'Huyện Hiệp Hòa', '7873', 'Xã Xuân Cẩm', '123', 0),
(32, 'TK69187df4a4440', 'capy', '0437', '22', 'Tỉnh Quảng Ninh', '205', 'Thành phố Đông Triều', '7117', 'Phường Yên Thọ', '123', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `don_hang`
--

CREATE TABLE `don_hang` (
  `ID_DH` varchar(5) NOT NULL,
  `ID_PTTT` varchar(5) NOT NULL,
  `ID_TK` char(15) NOT NULL,
  `DIA_CHI_GIAO_DH` varchar(1000) NOT NULL,
  `NGAY_GIO_TAO_DON` datetime NOT NULL,
  `NGAY_DU_KIEN_GIAO` date NOT NULL,
  `TONG_GIA_TRI_DH` decimal(10,2) NOT NULL,
  `TIEN_GIAM_GIA` decimal(10,2) NOT NULL,
  `SO_TIEN_THANH_TOAN` decimal(10,2) NOT NULL,
  `TRANG_THAI_THANH_TOAN` varchar(50) NOT NULL,
  `NGAY_THANH_TOAN` datetime NOT NULL,
  `TRANG_THAI_BL` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `don_hang`
--

INSERT INTO `don_hang` (`ID_DH`, `ID_PTTT`, `ID_TK`, `DIA_CHI_GIAO_DH`, `NGAY_GIO_TAO_DON`, `NGAY_DU_KIEN_GIAO`, `TONG_GIA_TRI_DH`, `TIEN_GIAM_GIA`, `SO_TIEN_THANH_TOAN`, `TRANG_THAI_THANH_TOAN`, `NGAY_THANH_TOAN`, `TRANG_THAI_BL`) VALUES
('DH081', 'PTTT1', 'TK69187df4a4440', 'capy (0437)\nĐịa chỉ: 123, Phường Yên Thọ, Thành phố Đông Triều, Tỉnh Quảng Ninh', '2025-11-15 20:25:50', '2025-11-18', 198000.00, 39600.00, 158400.00, 'Chưa thanh toán', '0000-00-00 00:00:00', 'Chưa đánh giá'),
('DH0B5', 'PTTT1', 'TK690a2db982cee', 'hoa (02222)\nĐịa chỉ: 34, Xã Quan Lạn, Huyện Vân Đồn, Tỉnh Quảng Ninh', '2025-11-11 19:19:23', '2025-11-14', 188000.00, 17800.00, 170200.00, 'Chưa thanh toán', '0000-00-00 00:00:00', 'Chưa đánh giá'),
('DH0D8', 'PTTT1', 'TK690a2db982cee', 'hoa (02222)\nĐịa chỉ: 34, Xã Quan Lạn, Huyện Vân Đồn, Tỉnh Quảng Ninh', '2025-11-11 19:29:54', '2025-11-14', 749000.00, 149800.00, 599200.00, 'Chưa thanh toán', '0000-00-00 00:00:00', 'Chưa đánh giá'),
('DH31E', 'PTTT1', 'TK69187df4a4440', 'capy (0437)\nĐịa chỉ: 123, Phường Yên Thọ, Thành phố Đông Triều, Tỉnh Quảng Ninh', '2025-11-15 22:04:19', '2025-11-18', 89000.00, 0.00, 89000.00, 'Chưa thanh toán', '0000-00-00 00:00:00', 'Chưa đánh giá'),
('DH706', 'PTTT1', 'TK690a29dd24ac3', 'tram (011111)\nĐịa chỉ: Hẻm 156, Phường Bình Ngọc, Thành phố Móng Cái, Tỉnh Quảng Ninh', '2025-11-15 14:57:14', '2025-11-18', 475000.00, 35600.00, 439400.00, 'Chưa thanh toán', '0000-00-00 00:00:00', 'Chưa đánh giá'),
('DH7A2', 'PTTT1', 'TK690a2db982cee', 'dao (02222)\nĐịa chỉ: t, Xã Chân Mộng, Huyện Đoan Hùng, Tỉnh Phú Thọ', '2025-11-11 19:18:45', '2025-11-14', 219000.00, 28000.00, 191000.00, 'Chưa thanh toán', '0000-00-00 00:00:00', 'Chưa đánh giá'),
('DH9B2', 'PTTT1', 'TK690a2d551daad', 'lam (099)\nĐịa chỉ: 123, Xã Đoan Bái, Huyện Hiệp Hòa, Tỉnh Bắc Giang', '2025-11-11 03:16:32', '2025-11-14', 110000.00, 22000.00, 88000.00, 'Chưa thanh toán', '0000-00-00 00:00:00', 'Chưa đánh giá'),
('DHA4D', 'PTTT1', 'TK69186339d8aab', 'teo (09123)\nĐịa chỉ: 123, Xã Xuân Cẩm, Huyện Hiệp Hòa, Tỉnh Bắc Giang', '2025-11-15 19:11:17', '2025-11-18', 30000.00, 6000.00, 24000.00, 'Chưa thanh toán', '0000-00-00 00:00:00', 'Chưa đánh giá'),
('DHA72', 'PTTT1', 'TK690a2db982cee', 'hoa (02222)\nĐịa chỉ: 34, Xã Quan Lạn, Huyện Vân Đồn, Tỉnh Quảng Ninh', '2025-11-11 19:13:57', '2025-11-14', 79000.00, 0.00, 79000.00, 'Chưa thanh toán', '0000-00-00 00:00:00', 'Chưa đánh giá'),
('DHD08', 'PTTT1', 'TK690a2d551daad', 'lam (099)\nĐịa chỉ: 123, Xã Đoan Bái, Huyện Hiệp Hòa, Tỉnh Bắc Giang', '2025-11-11 03:02:03', '2025-11-14', 89000.00, 17800.00, 71200.00, 'Chưa thanh toán', '0000-00-00 00:00:00', 'Chưa đánh giá'),
('DHD45', 'PTTT1', 'TK69186339d8aab', 'teo (09123)\nĐịa chỉ: 123, Xã Xuân Cẩm, Huyện Hiệp Hòa, Tỉnh Bắc Giang', '2025-11-15 22:22:51', '2025-11-18', 500000.00, 100000.00, 400000.00, 'Chưa thanh toán', '0000-00-00 00:00:00', 'Chưa đánh giá'),
('DHF32', 'PTTT1', 'TK69187df4a4440', 'capy (0437)\nĐịa chỉ: 123, Phường Yên Thọ, Thành phố Đông Triều, Tỉnh Quảng Ninh', '2025-11-15 22:04:16', '2025-11-18', 89000.00, 0.00, 89000.00, 'Chưa thanh toán', '0000-00-00 00:00:00', 'Chưa đánh giá'),
('DHFF3', 'PTTT2', 'TK690a2d551daad', 'lam (099)\nĐịa chỉ: 123, Xã Đoan Bái, Huyện Hiệp Hòa, Tỉnh Bắc Giang', '2025-11-11 03:03:21', '2025-11-14', 89000.00, 17800.00, 71200.00, 'Đã thanh toán', '0000-00-00 00:00:00', 'Chưa đánh giá');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `don_hang_hien_tai`
--

CREATE TABLE `don_hang_hien_tai` (
  `ID_DH` varchar(5) NOT NULL,
  `TRANG_THAI_DHHT` varchar(50) NOT NULL DEFAULT 'Chờ xử lý',
  `NGAY_GIO_CAP_NHAT` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `don_hang_hien_tai`
--

INSERT INTO `don_hang_hien_tai` (`ID_DH`, `TRANG_THAI_DHHT`, `NGAY_GIO_CAP_NHAT`) VALUES
('DH081', 'Đã hủy', '2025-11-15 22:20:19'),
('DH0B5', 'Chờ xử lý', '2025-11-15 22:11:18'),
('DH0D8', 'Chờ xử lý', '2025-11-15 22:11:18'),
('DH31E', 'Đã hủy', '2025-11-15 22:11:35'),
('DH706', 'Chờ xử lý', '2025-11-15 22:11:18'),
('DH7A2', 'Chờ xử lý', '2025-11-15 22:11:18'),
('DH9B2', 'Chờ xử lý', '2025-11-15 22:11:18'),
('DHA4D', 'Chờ xử lý', '2025-11-15 22:11:18'),
('DHA72', 'Chờ xử lý', '2025-11-15 22:11:18'),
('DHD08', 'Chờ xử lý', '2025-11-15 22:11:18'),
('DHD45', 'Chờ xử lý', '2025-11-15 22:22:51'),
('DHF32', 'Đã hủy', '2025-11-15 22:19:43'),
('DHFF3', 'Chờ xử lý', '2025-11-15 22:11:18');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dvt`
--

CREATE TABLE `dvt` (
  `ID_DVT` varchar(5) NOT NULL,
  `DVT` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `dvt`
--

INSERT INTO `dvt` (`ID_DVT`, `DVT`) VALUES
('DVT01', 'Hộp'),
('DVT02', 'Kg'),
('DVT03', 'Gram');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `gia_ban_hien_tai`
--

CREATE TABLE `gia_ban_hien_tai` (
  `ID_HH` varchar(5) NOT NULL,
  `ID_TD` varchar(5) NOT NULL,
  `GIA_HIEN_TAI` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `gia_ban_hien_tai`
--

INSERT INTO `gia_ban_hien_tai` (`ID_HH`, `ID_TD`, `GIA_HIEN_TAI`) VALUES
('00001', 'TD003', 89000.00),
('00002', 'TD003', 79000.00),
('00003', 'TD003', 89000.00),
('00004', 'TD003', 99000.00),
('00005', 'TD003', 78000.00),
('00006', 'TD003', 110000.00),
('00007', 'TD003', 99000.00),
('00008', 'TD003', 89000.00),
('00009', 'TD003', 109000.00),
('00010', 'TD003', 126000.00),
('00011', 'TD003', 99000.00),
('00012', 'TD003', 55000.00),
('00013', 'TD003', 89000.00),
('00014', 'TD003', 159000.00),
('00015', 'TD003', 189000.00),
('00016', 'TD003', 20000.00),
('00017', 'TD003', 39000.00),
('00018', 'TD003', 26000.00),
('00019', 'TD003', 37000.00),
('00020', 'TD003', 29000.00),
('00021', 'TD003', 34000.00),
('00022', 'TD003', 94000.00),
('00023', 'TD003', 99000.00),
('00024', 'TD003', 119000.00),
('00025', 'TD003', 35000.00),
('00026', 'TD003', 89000.00),
('00027', 'TD003', 57000.00),
('00028', 'TD003', 53000.00),
('00029', 'TD003', 119000.00),
('00030', 'TD003', 85000.00),
('00031', 'TD003', 120000.00),
('00032', 'TD003', 100000.00),
('00033', 'TD003', 140000.00),
('00034', 'TD003', 125000.00),
('00035', 'TD003', 33000.00),
('00036', 'TD003', 105000.00),
('00037', 'TD003', 52000.00),
('00038', 'TD003', 285000.00),
('00039', 'TD003', 183000.00),
('00040', 'TD003', 154000.00),
('00041', 'TD003', 309000.00),
('00042', 'TD003', 149000.00),
('00043', 'TD003', 175000.00),
('00044', 'TD003', 65000.00),
('00045', 'TD003', 104000.00),
('00046', 'TD003', 225000.00),
('00047', 'TD003', 210000.00),
('00048', 'TD003', 139000.00),
('00049', 'TD003', 142000.00),
('00050', 'TD003', 112000.00),
('00051', 'TD003', 110000.00),
('00052', 'TD003', 162000.00),
('00053', 'TD003', 139000.00),
('00054', 'TD003', 45000.00),
('00055', 'TD003', 25000.00),
('00056', 'TD003', 30000.00),
('00057', 'TD003', 35000.00),
('00058', 'TD003', 15000.00),
('00059', 'TD003', 12000.00),
('00060', 'TD003', 10000.00),
('00061', 'TD003', 28000.00),
('00062', 'TD003', 22000.00),
('00063', 'TD003', 20000.00),
('00064', 'TD003', 30000.00),
('00065', 'TD003', 25000.00),
('00066', 'TD003', 35000.00),
('00067', 'TD003', 40000.00),
('00068', 'TD003', 32000.00),
('00069', 'TD003', 38000.00),
('00070', 'TD003', 45000.00),
('00071', 'TD003', 42000.00),
('00072', 'TD003', 39000.00),
('00073', 'TD003', 49000.00),
('00074', 'TD003', 45000.00),
('00075', 'TD003', 35000.00),
('00076', 'TD003', 15000.00),
('00077', 'TD003', 25000.00),
('00078', 'TD003', 75000.00),
('00079', 'TD003', 20000.00),
('00080', 'TD003', 22000.00),
('00081', 'TD003', 54000.00),
('00082', 'TD003', 47000.00),
('00083', 'TD003', 20000.00),
('00084', 'TD003', 25000.00),
('00085', 'TD003', 25000.00),
('00086', 'TD003', 30000.00),
('00087', 'TD003', 35000.00),
('00088', 'TD003', 25000.00),
('00089', 'TD003', 25000.00),
('00090', 'TD003', 30000.00),
('00091', 'TD003', 20000.00),
('00092', 'TD003', 28000.00),
('00093', 'TD003', 18000.00),
('00094', 'TD003', 22000.00),
('00095', 'TD003', 28000.00),
('00096', 'TD003', 30000.00),
('00097', 'TD003', 25000.00),
('00098', 'TD003', 30000.00),
('00099', 'TD003', 35000.00),
('00100', 'TD003', 65000.00),
('00101', 'TD003', 79000.00),
('00102', 'TD003', 55000.00),
('00103', 'TD003', 45000.00),
('00104', 'TD003', 35000.00),
('00105', 'TD003', 60000.00),
('00106', 'TD003', 80000.00),
('00107', 'TD003', 150000.00),
('00108', 'TD003', 30000.00),
('00109', 'TD003', 40000.00),
('00110', 'TD003', 70000.00),
('00111', 'TD003', 65000.00),
('00112', 'TD003', 25000.00),
('00113', 'TD003', 59000.00),
('00114', 'TD003', 30000.00),
('00115', 'TD003', 199000.00),
('00116', 'TD003', 249000.00),
('00117', 'TD003', 129000.00),
('00118', 'TD003', 450000.00),
('00119', 'TD003', 99000.00),
('00120', 'TD003', 80000.00),
('00121', 'TD003', 350000.00),
('00122', 'TD003', 89000.00),
('00123', 'TD003', 150000.00),
('00124', 'TD003', 110000.00),
('00125', 'TD003', 120000.00),
('00126', 'TD003', 119000.00),
('00127', 'TD003', 180000.00),
('00128', 'TD003', 500000.00),
('00129', 'TD003', 99000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `gio_hang`
--

CREATE TABLE `gio_hang` (
  `ID_GH` varchar(5) NOT NULL,
  `ID_TK` char(15) DEFAULT NULL,
  `NGAY_TAO_GH` datetime NOT NULL,
  `NGAY_CAP_NHAT_GH` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `gio_hang`
--

INSERT INTO `gio_hang` (`ID_GH`, `ID_TK`, `NGAY_TAO_GH`, `NGAY_CAP_NHAT_GH`) VALUES
('GH31f', 'TK690a394e5231c', '2025-11-05 00:35:10', '2025-11-05 00:35:10'),
('GH444', 'TK69187df4a4440', '2025-11-15 20:19:48', '2025-11-15 20:19:48'),
('GH690', 'TK690a29dd24ac3', '2025-11-04 23:29:17', '2025-11-04 23:29:17'),
('GH691', 'TK6912f22e29d58', '2025-11-11 15:22:06', '2025-11-11 15:22:06'),
('GH6b9', 'TK690b6c23666b4', '2025-11-05 22:24:19', '2025-11-05 22:24:19'),
('GHa2e', 'TK690b6bf878a2b', '2025-11-05 22:23:36', '2025-11-05 22:23:36'),
('GHab0', 'TK690a2d551daad', '2025-11-04 23:44:05', '2025-11-04 23:44:05'),
('GHab1', 'TK69186339d8aab', '2025-11-15 18:25:45', '2025-11-15 18:25:45'),
('GHb93', 'TK69186da1a7b90', '2025-11-15 19:10:09', '2025-11-15 19:10:09'),
('GHcf3', 'TK690a2db982cee', '2025-11-04 23:45:45', '2025-11-04 23:45:45'),
('GHddd', 'TK691862df94dd9', '2025-11-15 18:24:15', '2025-11-15 18:24:15');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hang_hoa`
--

CREATE TABLE `hang_hoa` (
  `ID_HH` varchar(5) NOT NULL,
  `ID_LHH` varchar(5) NOT NULL,
  `ID_DVT` varchar(5) NOT NULL,
  `ID_KM` varchar(5) DEFAULT NULL,
  `TEN_HH` varchar(100) NOT NULL,
  `link_anh` varchar(255) DEFAULT NULL,
  `MO_TA_HH` text NOT NULL,
  `SO_LUONG_TON_HH` decimal(10,2) NOT NULL,
  `DUOC_PHEP_BAN` tinyint(1) NOT NULL,
  `LA_HANG_SX` tinyint(1) NOT NULL,
  `HSD` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT=' u?c Ph p B n _   NG n?u hi?n th? tr n web, SAI n?u l  h ng ';

--
-- Đang đổ dữ liệu cho bảng `hang_hoa`
--

INSERT INTO `hang_hoa` (`ID_HH`, `ID_LHH`, `ID_DVT`, `ID_KM`, `TEN_HH`, `link_anh`, `MO_TA_HH`, `SO_LUONG_TON_HH`, `DUOC_PHEP_BAN`, `LA_HANG_SX`, `HSD`) VALUES
('00001', 'LHH01', 'DVT01', 'KM003', 'Ba Rọi Chiên Nước Mắm (Khay 300gr)', '00001.png', 'Thành phần: 300gr Ba rọi heo, 50gr sốt nước mắm, Tỏi xay 3S\r\nKhẩu phần: 23 Người Ăn\r\n\r\nGM ra mắt các sản phẩm Ready To Cook với mong muốn đáp ứng nhu cầu chuẩn bị bữa ăn NHANH CHÓNG - VỆ SINH - TƯƠI NGON của người bận rộn.\r\n\r\nVới Ready To Cook, bạn có thể: \r\n- Tự tay chuẩn bị bữa cơm gia đình nóng hổi mà không tốn nhiều thời gian sơ chế. \r\n- Nguyên liệu vừa đủ cho mỗi món ăn, không dư thừa. \r\n- Thưởng thức các món ăn sạch sẽ, tươi ngon với nguyên liệu đảm bảo an toàn, lại thơm ngon, hợp vị.', 50.00, 1, 1, '2025-11-10 23:59:59'),
('00002', 'LHH01', 'DVT01', NULL, 'Ba Rọi Chiên Sả Ớt (Khay 300G 79k)', '00002.png', 'Khẩu phần: 2-3 Người Ăn. Thành phần: Gà làm sạch 300g + Sả xay 50gr + Ớt xay 10gr', 50.00, 1, 1, '2025-11-10 23:59:59'),
('00003', 'LHH01', 'DVT01', NULL, 'Ba Rọi Kho Củ Cải Trắng (Khay 89k)', '00003.png', 'Khẩu phần: 2-3 Người Ăn. Thành phần bao gồm: Ba Rọi Heo 200g + Củ Cải Trắng Cắt Sẵn...', 50.00, 1, 1, '2025-11-10 23:59:59'),
('00004', 'LHH01', 'DVT01', NULL, 'Ba Rọi Kho Măng (Khay 450G) 99k)', '00004.png', 'Khẩu phần: 2-3 Người Ăn. Thành phần 200gr Ba Rọi Heo, 150gr Măng Tươi Cắt Sẵn...', 50.00, 1, 1, '2025-11-10 23:59:59'),
('00005', 'LHH01', 'DVT01', NULL, 'Ba Rọi Kho Thơm (Khay 300g) 78k)', '00005.png', 'Khẩu phần: 2-3 Người Ăn. Thành phần (Khay 560g) gồm: Ba Rọi Heo: 200g + Thơm gọt (Trái): 300g...', 50.00, 1, 1, '2025-11-10 23:59:59'),
('00006', 'LHH01', 'DVT01', 'KM003', 'Ba Rọi Rút Sườn Khoa BBQ (Khay 110k)', '00006.png', 'Khẩu phần: 2-3 Người Ăn. Thành phần: Ba Rọi Heo Rút Sườn: 300g + Sốt ướp nướng GreenMeal...', 50.00, 1, 1, '2025-11-10 23:59:59'),
('00007', 'LHH01', 'DVT01', NULL, 'Ba Rọi Ướp Xá Xíu (Khay 300g) 99k)', '00007.png', 'Khẩu phần: 2-3 Người Ăn. Thành phần: 300g Ba rọi heo, 50g Sốt ướp GreenMeal Kitchen', 50.00, 1, 1, '2025-11-10 23:59:59'),
('00008', 'LHH01', 'DVT01', 'KM003', 'Bắp Giò Heo Kho Củ Cải (Khay 89k)', '00008.png', 'Thành phần: 400gr Bắp Giò Heo, 200gr Củ Cải Trắng Cắt Sẵn, Sốt Kho GreenMeal...', 50.00, 1, 1, '2025-11-10 23:59:59'),
('00009', 'LHH01', 'DVT01', NULL, 'Cá Bạc Má Kho Thơm (Khay 400g 109k)', '00009.png', 'Khẩu phần: 2-3 Người Ăn. Thành phần: Cá Bạc Má S.P C.P Sẵn 300G, Thơm gọt (Trái) 1/4 trái...', 50.00, 1, 1, '2025-11-10 23:59:59'),
('00010', 'LHH01', 'DVT01', NULL, 'Cá Basa Kho Ba Rọi GreenMeal (Khay 126k)', '00010.png', 'Khẩu phần: 1-2 người ăn. Thành phần: Cá Basa S.P Size 2-2.5: 300 g + Thịt Ba Rọi Heo: 200g...', 50.00, 1, 1, '2025-11-10 23:59:59'),
('00011', 'LHH01', 'DVT01', 'KM003', 'Cá Basa Kho Tiêu (Khay 300g) 99k)', '00011.png', 'Khẩu phần: Thành phần: Cá Basa S.P Size 2-2.5: 300gr + Tiêu Đen Xay Việt San: 10 g...', 50.00, 1, 1, '2025-11-10 23:59:59'),
('00012', 'LHH01', 'DVT01', NULL, 'Cá Basa Kho Tộ GreenMeal (Khay 300G 55k)', '00012.png', 'Khẩu phần: 1-2 người ăn. Thành phần: Cá Basa Kho Tộ [GreenMeal] - phần 2 người ăn: Cá Basa: 300g...', 50.00, 1, 1, '2025-11-10 23:59:59'),
('00013', 'LHH01', 'DVT01', 'KM003', 'Cá Bống Đục Kho Tiêu (Khay 300g 89k)', '00013.png', 'Khẩu phần: Cá Bống Đục Kho Tiêu (Khay 300g), Cá Bống S.P Sz 20-25 Sốt Kho GreenMeal...', 50.00, 1, 1, '2025-11-10 23:59:59'),
('00014', 'LHH01', 'DVT01', NULL, 'Cá Bống Tượng Kho Tiêu (Khay 159k)', '00014.png', 'Khẩu phần: 2-3 Người Ăn. Thành phần: Cá Bống Tượng Sz 20-25 con: 300gr + Sốt Kho GreenMeal...', 50.00, 1, 1, '2025-11-10 23:59:59'),
('00015', 'LHH01', 'DVT01', NULL, 'Cá Bớp Kho Thịt Ba Rọi (Khay 189k)', '00015.png', 'Khẩu phần: 1-2 người ăn. Thành phần: Cá Bớp: 300g + Ba Rọi Heo: 150g + Sốt Kho GreenMeal...', 50.00, 1, 1, '2025-11-10 23:59:59'),
('00016', 'LHH02', 'DVT01', NULL, 'Bắp Cải Trái Tim Cắt Sẵn (Khay 300g)', '00016.png', 'Thành phần: Bắp cải trái tim cắt sẵn (300g)', 50.00, 1, 1, '2025-11-15 23:59:59'),
('00017', 'LHH02', 'DVT01', NULL, 'Bắp Cải Xào Cà Chua (Khay 600Gr)', '00017.png', 'Thành phần: + Bắp Cải Trắng: 300 g + Cà chua Rita: 1 trái/75g + Sốt Xào GreenMeal Kitchen (Túi 50g): 1 túi + Hành Tím Xay GreenMeal', 50.00, 1, 1, '2025-11-15 23:59:59'),
('00018', 'LHH02', 'DVT01', NULL, 'Bầu Cắt Sẵn (Khay 300Gr)', '00018.png', 'Thành phần: Bầu cắt sẵn - Khẩu phần: 1-2 người ăn', 50.00, 1, 1, '2025-11-15 23:59:59'),
('00019', 'LHH02', 'DVT01', NULL, 'Bí Đao Cắt Sẵn khay 300gr', '00019.png', 'Thành phần: Bí đao cắt sẵn - Khẩu phần: 1-2 người ăn', 50.00, 1, 1, '2025-11-15 23:59:59'),
('00020', 'LHH02', 'DVT01', NULL, 'Bí Đỏ Hồ Lô Cắt Sẵn (Khay 300Gr)', '00020.png', 'Thành phần: Bí Đỏ Hồ Lô Cắt Sẵn - Khẩu phần: 1-2 người ăn', 50.00, 1, 1, '2025-11-15 23:59:59'),
('00021', 'LHH02', 'DVT01', NULL, 'Bí Đỏ Tròn Cắt Sẵn (Khay 300Gr)', '00021.png', 'Thành phần: Bí Đỏ Tròn Cắt Sẵn - Khẩu phần: 1-2 người ăn', 50.00, 1, 1, '2025-11-15 23:59:59'),
('00022', 'LHH02', 'DVT01', NULL, 'Bò Xào Đậu Que (Khay 300Gr)', '00022.png', 'Thành phần: Thịt Bò Xào Pacow: 125gr + Đậu Que/Đậu Cove cắt sẵn: 200gr + Sốt xào GreenMeal (Túi 50gr): 1 túi + Tỏi xay GreenMeal: 10gr', 50.00, 1, 1, '2025-11-15 23:59:59'),
('00023', 'LHH02', 'DVT01', NULL, 'Bò Xào Măng Tây (Khay 270Gr)', '00023.png', 'Thành phần: Thịt bò xào Pacow (Gói 250g): 125g + Măng Tây: 100g + Sốt xào GreenMeal (Túi 50gr): 1 túi + Tỏi xay GreenMeal: 10g', 50.00, 1, 1, '2025-11-15 23:59:59'),
('00024', 'LHH02', 'DVT01', NULL, 'Bò Xào Nấm (Khay 250G)', '00024.png', 'Thành phần: 125gr Thịt Bò Xào Pacow + 100gr Nấm Đùi Gà Tht + Hành Lá: 13gr + Sốt xào GreenMeal (Túi 50gr): 1 túi + Tỏi Xay GreenMeal: 10gr - Khẩu phần: 3-4 Người Ăn', 50.00, 1, 1, '2025-11-15 23:59:59'),
('00025', 'LHH02', 'DVT01', NULL, 'Bông Bí Xào Tỏi (Khay 200g)', '00025.png', 'Thành phần: 180g Bông Bí, 20g Tỏi xay, Sốt xào, Hành lá. Khẩu phần: 2 Người Ăn', 50.00, 1, 1, '2025-11-15 23:59:59'),
('00026', 'LHH02', 'DVT01', 'KM003', 'Bông Bí Xào Tôm (Khay 300g)', '00026.png', 'Thành phần: 180g Bông Bí, 100g Tôm Lột Vỏ Sz nhỏ 30-40, Sốt xào, Tỏi xay, Hành lá. Khẩu phần: 2 Người Ăn', 50.00, 1, 1, '2025-11-15 23:59:59'),
('00027', 'LHH02', 'DVT01', NULL, 'Bông Cải Trắng Cắt Sẵn (Khay 300Gr)', '00027.png', 'Thành phần: Bông Cải Trắng Cắt Sẵn - Khẩu phần: 1-2 người ăn', 50.00, 1, 1, '2025-11-15 23:59:59'),
('00028', 'LHH02', 'DVT01', NULL, 'Bông Cải Xanh Cắt Sẵn (Khay 300Gr)', '00028.png', 'Thành phần: Bông cải cắt sẵn - Khẩu phần: 1-2 người ăn', 50.00, 1, 1, '2025-11-15 23:59:59'),
('00029', 'LHH02', 'DVT01', NULL, 'Cá Diêu Hồng Hấp Hành Gừng (Khay 700G)', '00029.png', 'Thành phần: 700gr Cá Diêu Hồng, Gừng Tươi, Hành Lá, Hành Tím Xay, Tỏi Xay', 50.00, 1, 1, '2025-11-15 23:59:59'),
('00030', 'LHH03', 'DVT01', NULL, 'Sụn Gà Nướng Muối Ớt (Khay 500g)', '00030.png', 'Thành phần: 500gr Sụn ức gà CP, 2gr Muối tinh bạc liêu Natural DH Foods, 5gr Bột ớt DH Foods, 7gr Đường tinh luyện biên hòa, Dầu điều GreenMeal, Hành tím xay GreenMeal, Tỏi xay GreenMeal - Khẩu phần: 4 Người Ăn. GreenMeal Mart ra mắt các sản phẩm Ready To Cook với mong muốn đáp ứng nhu cầu chuẩn bị bữa ăn NHANH CHÓNG - VỆ SINH - TƯƠI NGON của người bận rộn. Với Ready To Cook, bạn có thể: - Tự tay chuẩn bị bữa cơm gia đình nóng hổi mà không tốn nhiều thời gian sơ chế. - Nguyên liệu vừa đủ cho mỗi món ăn, không dư thừa. - Thưởng thức các món ăn sạch sẽ, tươi ngon với nguyên liệu đảm bảo an toàn, lại thơm ngon, hợp vị.', 50.00, 1, 1, '2025-11-15 23:59:59'),
('00031', 'LHH03', 'DVT01', NULL, 'Tôm Lớn Nướng Muối Ớt (Khay 300G)', '00031.png', 'Chưa có mô tả cho sản phẩm này', 50.00, 1, 1, '2025-11-15 23:59:59'),
('00032', 'LHH03', 'DVT01', NULL, 'Tôm Nhỏ Nướng Muối Ớt (Khay 300G)', '00032.png', 'Chưa có mô tả cho sản phẩm này', 50.00, 1, 1, '2025-11-15 23:59:59'),
('00033', 'LHH03', 'DVT01', 'KM003', 'Mực Nướng Muối Ớt (Khay 300G)', '00033.png', 'Chưa có mô tả cho sản phẩm này', 50.00, 1, 1, '2025-11-15 23:59:59'),
('00034', 'LHH03', 'DVT01', NULL, 'Sườn Nướng Muối Ớt (Túi 300G)', '00034.png', 'Chưa có mô tả cho sản phẩm này', 50.00, 1, 1, '2025-11-15 23:59:59'),
('00035', 'LHH04', 'DVT03', 'KM003', 'Bao Tử Cá Basa (250gr)', '00035.png', 'Bao Tử Cá Basa là một phần thịt từ cá basa, được chế biến sạch và có thể sử dụng trong nhiều món ăn hấp dẫn. Bao tử cá basa có đặc điểm là dai, giòn, và có khả năng hấp thụ hương vị gia vị rất tốt...', 50.00, 1, 0, '2025-11-15 23:59:59'),
('00036', 'LHH04', 'DVT03', NULL, 'Cá Bạc Má Size 5-8 Con/Kg - 500gr', '00036.png', 'Cá bạc má là một trong những thức ăn hải sản được người tiêu dùng ưa chuộng bởi độ lành tính và vị béo đặc trưng khó có thể nhầm lẫn. Vốn thịt mềm, ngọt tự nhiên nên rất dễ để có thể chế biến...', 50.00, 1, 0, '2025-11-15 23:59:59'),
('00037', 'LHH04', 'DVT03', NULL, 'Cá Basa - 500 gr', '00037.png', 'Cá basa là dòng cá da trơn được nuôi làm kinh tế ở rất nhiều quốc gia trên thế giới, đặc biệt là các quốc gia thuộc khu vực châu Á. Thịt cá không những thơm ngon về hương vị mà lại còn rất tốt cho sức khỏe...', 50.00, 1, 0, '2025-11-15 23:59:59'),
('00038', 'LHH04', 'DVT03', NULL, 'Cá Bớp Cắt Khoanh - 500gr', '00038.png', 'Cá bớp với thịt béo ngọt, chắc thịt đặc trưng của các loài cá biển luôn là nguyên liệu \"vàng\" trong nấu nướng bởi hương vị trong các mà món lẩu, món kho,... đều vô cùng ngon.', 50.00, 1, 0, '2025-11-15 23:59:59'),
('00039', 'LHH04', 'DVT03', NULL, 'Cá Chẽm Quy Nhơn (Size 0.8-1.2) - 0,8kg', '00039.png', 'Cá Chẽm Quy Nhơn là một loại cá biển tươi ngon, nổi tiếng với hương vị thơm ngon và chất lượng vượt trội, được đánh bắt từ vùng biển Quy Nhơn. Cá chẽm Quy Nhơn có thịt trắng, ngọt và rất giàu dinh dưỡng...', 50.00, 1, 0, '2025-11-15 23:59:59'),
('00040', 'LHH04', 'DVT03', NULL, 'Cá Chim Đen Size 2-4 Con/Kg - 500gr', '00040.png', 'Có nhiều loại cá chim hiện nay như: cá chim trắng, cá chim Ấn Độ, cá chim gai hay cá chim đen. Trong số này loại cá có chất thịt ngon và được giá trị kinh tế cao nhất chính là cá chim đen...', 50.00, 1, 0, '2025-11-15 23:59:59'),
('00041', 'LHH04', 'DVT03', 'KM003', 'Cá Chim Trắng Lớn Size 0.8-1.0', '00041.png', 'Cá Chim Trắng Lớn là một loại cá biển cao cấp, nổi bật với thịt trắng, săn chắc và vị ngọt tự nhiên... Đây là loại cá có kích thước lớn, thường được tìm thấy ở các vùng biển miền Trung và miền Nam Việt Nam.', 50.00, 1, 0, '2025-11-15 23:59:59'),
('00042', 'LHH04', 'DVT03', NULL, 'Cá Chim Trắng Nhỏ (Size 0.5-0.7) - 0,5kg', '00042.png', 'Cá chim trắng là loài cá biển có màu bạc hoặc trắng với một ít vảy... Loại cá này được đánh giá cao trong ẩm thực vì hương vị đặc biệt của nó. Cá chim trắng có hương vị thơm ngon, thịt nhiều nước, chất dinh dưỡng cao...', 50.00, 1, 0, '2025-11-15 23:59:59'),
('00043', 'LHH04', 'DVT03', NULL, 'Cá Dìa Size 2-4Con/Kg - 500gr', '00043.png', 'Cá dìa bông thân dẹp tròn, da trơn màu nâu xám, trên thân hình có những chấm nâu đen, đầu nhỏ, mắt đen tròn. Là một món ăn ngon thịt cá ngọt, béo, dai, thơm ngon...', 50.00, 1, 0, '2025-11-15 23:59:59'),
('00044', 'LHH04', 'DVT03', NULL, 'Cá Diêu Hồng - 500 gr', '00044.png', 'Cá diêu hồng hay còn gọi là cá điêu hồng là một loài cá sống trong môi trường nước ngọt thuộc họ Cá rô phi... thịt cá diêu hồng có màu trắng, trong sạch, các thớ thịt được cấu trúc chắc và đặc biệt là thịt không quá nhiều xương.', 50.00, 1, 0, '2025-11-15 23:59:59'),
('00045', 'LHH04', 'DVT03', NULL, 'Cá Diêu Hồng - 800 gr', '00045.png', 'Cá diêu hồng hay còn gọi là cá điêu hồng là một loài cá sống trong môi trường nước ngọt thuộc họ Cá rô phi... Đặc biệt là cá có hàm lượng mỡ cao nên ăn rất béo.', 50.00, 1, 0, '2025-11-15 23:59:59'),
('00046', 'LHH04', 'DVT01', NULL, 'Cá Hồi Tươi Fille Kome (300gr)', '00046.png', 'Cá Hồi Tươi Fille Kome là sản phẩm cá hồi tươi được fillet (cắt bỏ xương và da)... Sản phẩm này thuộc thương hiệu Kome, nổi bật với chất lượng cá hồi cao cấp, giữ nguyên được độ tươi ngon và giá trị dinh dưỡng của cá.', 50.00, 1, 0, '2025-11-15 23:59:59'),
('00047', 'LHH04', 'DVT03', NULL, 'Cá Hồi Tươi Fillet - 300 gr', '00047.png', 'Cá Hồi Tươi Fille Kome là sản phẩm cá hồi tươi được fillet (cắt bỏ xương và da)... Miếng cá đã được cắt bỏ xương và da, chỉ còn lại phần thịt cá tươi ngon, sạch sẽ.', 50.00, 1, 0, '2025-11-15 23:59:59'),
('00048', 'LHH04', 'DVT02', NULL, 'Cá Hú - 1 Kg', '00048.png', 'Cá hú từ lâu đã trở thành thực phẩm được ưa chuộng nhất hiện nay... Cá hú rất giàu dinh dưỡng đặc biệt là chứa hàm lượng protein cao. Ngoài ra, cá còn chứa nhiều thành phần dinh dưỡng quan trọng...', 50.00, 1, 0, '2025-11-15 23:59:59'),
('00049', 'LHH04', 'DVT03', NULL, 'Cá Kèo - 400gr', '00049.png', 'Thuộc họ cá bống trắng nên cá kèo có vị ngọt mặn, tính bình, có tác dụng kiện tỳ trừ đàm, dưỡng can thận, giúp gân xương chắc khỏe, thông huyết mạch, lợi thủy, an thai và giúp chị em có nhiều sữa cho con bú.', 50.00, 1, 0, '2025-11-15 23:59:59'),
('00050', 'LHH05', 'DVT03', NULL, 'Bạch Tuộc Sz 10-12 (500g)', '00050.png', 'Bạch tuộc size 10–12 con/kg là lựa chọn lý tưởng cho những ai yêu thích hải sản tươi ngon, giàu dinh dưỡng. Với kích thước vừa phải, loại bạch tuộc này thích hợp để chế biến nhiều món ăn hấp dẫn như nướng, xào, lẩu hoặc nhúng giấm. Gợi ý món ngon: Bạch tuộc nướng sa tế, Bạch tuộc xào cay, Lẩu bạch tuộc...', 50.00, 1, 0, '2025-11-15 23:59:59'),
('00051', 'LHH05', 'DVT03', NULL, 'Ếch Size 7-10 Con/Kg - 400gr', '00051.png', 'Thịt ếch rất thơm ngon, ngọt và có kết cấu thịt dai, ít mỡ, vì vậy được nhiều người ưa chuộng cho các món ăn gia đình hay tiệc tùng. Chất lượng: Thịt ếch tươi có màu trắng ngà, độ săn chắc vừa phải, không có mùi tanh khó chịu. Ếch được chế biến cẩn thận, đảm bảo vệ sinh an toàn thực phẩm...', 50.00, 1, 0, '2025-11-15 23:59:59'),
('00052', 'LHH05', 'DVT03', NULL, 'Lươn - 400 gr', '00052.png', 'Lươn được xếp vào nhóm thực phẩm cung cấp rất nhiều chất dinh dưỡng có lợi cho sức khỏe. Trong Đông Y lươn có nhiều vai trò trong việc cải thiện tình trạng bổn hư tổn, khu phong trừ thấp…', 50.00, 1, 0, '2025-11-15 23:59:59'),
('00053', 'LHH05', 'DVT03', 'KM003', 'Mực Nang Sz 20 - 25 (500gr)', '00053.png', 'Mực nang hấp hành gừng: Giữ nguyên vị ngọt tự nhiên của mực, thơm lừng mùi hành gừng. Mực nang xào chua ngọt: Món ăn đậm đà, hấp dẫn với vị chua ngọt hài hòa. Mực nang nướng muối ớt: Thơm ngon, cay nồng, thích hợp cho các buổi tiệc nướng ngoài trời...', 50.00, 1, 0, '2025-11-15 23:59:59'),
('00054', 'LHH05', 'DVT01', NULL, 'Nghêu Sạch Lenger (Hộp 0.6kg)', '00054.png', 'Nghêu Sạch Lenger, được chọn lọc từ những con nghêu sống tươi ngon, khỏe mạnh nhất trước khi làm sạch và đóng gói. Nghêu đảm bảo sạch cát, sạch chất bẩn nội tạng và vi khuẩn gây bệnh nhưng vẫn giữ được hương vị ngọt tự nhiên vốn có...', 50.00, 1, 0, '2025-11-15 23:59:59'),
('00055', 'LHH06', 'DVT02', NULL, 'Bắp Cải Trắng - 800 gr', '00055.png', 'Bắp cải trắng tươi, canh tác theo tiêu chuẩn an toàn.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00056', 'LHH06', 'DVT02', NULL, 'Bắp Cải Trái Tim - 800 gr', '00056.png', 'Bắp cải trái tim (bắp cải non), lõi ngọt, dùng làm salad hoặc xào.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00057', 'LHH06', 'DVT02', NULL, 'Bắp Cải Tím - 800gr', '00057.png', 'Bắp cải tím giàu vitamin, thích hợp làm salad trộn.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00058', 'LHH06', 'DVT01', 'KM003', 'Bắp Chuối Bào (Gói 200g)', '00058.png', 'Bắp chuối bào sẵn, tiện lợi cho các món gỏi hoặc canh chua.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00059', 'LHH06', 'DVT01', 'KM003', 'Bắp Mỹ (Trái)', '00059.png', 'Bắp mỹ tươi, hạt mẩy, ngọt.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00060', 'LHH06', 'DVT01', NULL, 'Bắp Nếp (Trái)', '00060.png', 'Bắp nếp dẻo, thơm, dùng để luộc hoặc nướng.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00061', 'LHH06', 'DVT01', NULL, 'Bắp Non (Khay 200g)', '00061.png', 'Bắp non (ngô bao tử) sạch, dùng xào thập cẩm hoặc nấu lẩu.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00062', 'LHH06', 'DVT02', NULL, 'Bầu - 500 gr', '00062.png', 'Bầu sao tươi, non, dùng nấu canh tôm hoặc luộc.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00063', 'LHH06', 'DVT02', NULL, 'Bí Đao - 500 gr', '00063.png', 'Bí đao (bí xanh) dùng nấu canh hoặc làm trà bí đao.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00064', 'LHH06', 'DVT02', NULL, 'Bí Đỏ Hồ Lô - 700 gr', '00064.png', 'Bí đỏ hồ lô dẻo, bùi, dùng nấu canh hoặc nấu sữa.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00065', 'LHH06', 'DVT01', NULL, 'Bí Đỏ Tròn (400g)', '00065.png', 'Bí đỏ tròn cắt sẵn, tiện lợi.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00066', 'LHH06', 'DVT01', NULL, 'Bí Hạt Đậu (400g)', '00066.png', 'Bí hạt đậu (Butternut Squash) dẻo, thơm, dùng làm súp.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00067', 'LHH06', 'DVT02', NULL, 'Bí Ngô Non (500g)', '00067.png', 'Bí ngô non (nụ bí) nguyên trái, dùng để xào tỏi hoặc luộc.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00068', 'LHH06', 'DVT02', NULL, 'Bí Ngòi Xanh (500g)', '00068.png', 'Bí ngòi xanh (Zucchini) tươi, dùng nhúng lẩu hoặc xào.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00069', 'LHH06', 'DVT01', 'KM003', 'Bông Cải Trắng', '00069.png', 'Bông cải trắng (Súp lơ trắng) tươi, an toàn.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00070', 'LHH06', 'DVT02', NULL, 'Bông Cải Xanh - 400 gr', '00070.png', 'Bông cải xanh (Broccoli) giàu dinh dưỡng.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00071', 'LHH06', 'DVT01', NULL, 'Bông Cải Xanh Baby (Khay 300g)', '00071.png', 'Bông cải xanh baby (Cải rổ) xào tỏi.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00072', 'LHH06', 'DVT01', NULL, 'Cà Cherry Đỏ (Hộp 250g)', '00072.png', 'Cà chua cherry đỏ, mọng nước, vị chua ngọt.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00073', 'LHH06', 'DVT01', NULL, 'Cà Cherry Socola (Hộp 250g)', '00073.png', 'Cà chua cherry socola, ngọt đậm, dùng ăn sống.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00074', 'LHH06', 'DVT01', NULL, 'Cà Chua Beef (Khay 500g)', '00074.png', 'Cà chua beef trái to, nhiều thịt, ít hạt.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00075', 'LHH06', 'DVT01', NULL, 'Cà Chua Rita (Khay 500g)', '00075.png', 'Cà chua Rita (giống cà chua chùm), dùng nấu canh hoặc xào.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00076', 'LHH07', 'DVT01', NULL, 'Thì Là (Gói 100g)', '00076.png', 'Thì là tươi được tuyển chọn từ những cánh đồng rau sạch, lá xanh mềm, mùi thơm dịu đặc trưng. Đây là loại rau gia vị không thể thiếu trong các món như canh cá, cháo, lẩu...', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00077', 'LHH07', 'DVT01', NULL, 'Xà Lách Xoong (Gói 300g)', '00077.png', 'Xà Lách Xoong Tươi (Gói 300g). Lá non giòn mát, vị cay nhẹ đặc trưng. Giàu vitamin A, C và khoáng chất, tốt cho sức khỏe tim mạch và tiêu hóa.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00078', 'LHH07', 'DVT01', NULL, 'Rau Tiến Vua (Gói 100g)', '00078.png', 'Rau Tiến Vua là loại rau đặc sản nổi tiếng có nguồn gốc từ vùng đồng bằng Bắc Bộ... Với phần thân giòn sần sật đặc trưng, rau Tiến Vua thường được dùng để chế biến thành nhiều món ăn hấp dẫn.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00079', 'LHH07', 'DVT01', NULL, 'Bông So Đũa (Khay 100g)', '00079.png', 'Bông so đũa tươi, thường dùng nấu canh chua hoặc luộc chấm mắm.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00080', 'LHH07', 'DVT01', NULL, 'Đọt Bầu (Khay 200g)', '00080.png', 'Đọt bầu non (ngọn bầu) tươi, dùng xào tỏi hoặc luộc.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00081', 'LHH07', 'DVT01', NULL, 'Bí Nụ (Khay 500g)', '00081.png', 'Bí nụ (bông bí đực) non, dùng xào tỏi hoặc nấu canh, nhúng lẩu.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00082', 'LHH07', 'DVT02', NULL, 'Cần Tây Lớn - 800 gr', '00082.png', 'Cần tây là loại rau có mùi vị khá đặc biệt và có nhiều lợi ích sức khỏe khi kết hợp với thịt bò. Ngăn ngừa viêm nhiễm và ung thư. Cải thiện huyết áp.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00083', 'LHH07', 'DVT02', NULL, 'Cải Thảo - 800 gr', '00083.png', 'Đặc điểm: Cải thảo là loại rau mùa vụ, nó phát triển trong điều kiện thời tiết lạnh. Cũng giống như bắp cải thì cải thảo cũng bao gồm các lớp lá bao phủ lên với nhau.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00084', 'LHH07', 'DVT01', NULL, 'Cải Thìa Thủy Canh (Gói 300g)', '00084.png', 'Cải thìa trồng thủy canh, đảm bảo tiêu chuẩn sạch, an toàn.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00085', 'LHH07', 'DVT01', NULL, 'Cải Ngọt Thủy Canh (Gói 300g)', '00085.png', 'Cải ngọt trồng thủy canh, thân non, vị ngọt thanh, dùng xào hoặc nấu canh.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00086', 'LHH07', 'DVT01', NULL, 'Xà Lách Lolo Xanh Thủy Canh (300g)', '00086.png', 'Xà lách Lolo xanh trồng thủy canh, lá giòn, tươi, dùng cho các món salad.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00087', 'LHH07', 'DVT01', NULL, 'Cải Bó Xôi Thủy Canh (Gói 250g)', '00087.png', 'Cải bó xôi (spinach) trồng thủy canh, giàu sắt và vitamin.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00088', 'LHH07', 'DVT01', NULL, 'Cải Bẹ Xanh Thủy Canh (Gói 300g)', '00088.png', 'Cải bẹ xanh thủy canh, vị nồng đặc trưng, dùng nấu canh hoặc nhúng lẩu.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00089', 'LHH07', 'DVT01', NULL, 'Cải Bẹ Trắng (Gói 300g)', '00089.png', 'Cải bẹ trắng (cải chíp) tươi non, dùng xào nấm hoặc luộc.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00090', 'LHH07', 'DVT01', NULL, 'Rau Càng Cua (Gói 300g)', '00090.png', 'Rau càng cua sạch, vị chua nhẹ, giòn, dùng làm gỏi với thịt bò hoặc trứng.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00091', 'LHH07', 'DVT01', 'KM003', 'Rau Đắng (Gói 300g)', '00091.png', 'Rau đắng đất, dùng nấu canh cá hoặc ăn lẩu mắm.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00092', 'LHH07', 'DVT01', NULL, 'Rau Nhút (Gói 300g)', '00092.png', 'Rau nhút (rau rút) đã nhặt, dùng nấu canh chua hoặc lẩu.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00093', 'LHH07', 'DVT01', NULL, 'Bạc Hà (Khay 300g)', '00093.png', 'Bạc hà (dọc mùng) đã tước vỏ, thái vát, tiện lợi cho món canh chua.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00094', 'LHH07', 'DVT01', 'KM003', 'Cải Bẹ Xanh (Gói 300g)', '00094.png', 'Cải bẹ xanh (cải đắng) loại thường, dùng nấu canh thịt bằm.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00095', 'LHH07', 'DVT01', NULL, 'Cải Bẹ Xanh Baby (Gói 300g)', '00095.png', 'Cải bẹ xanh non (cải mầm), dùng nhúng lẩu hoặc xào.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00096', 'LHH07', 'DVT01', 'KM003', 'Cải Bó Xôi (Gói 250g)', '00096.png', 'Cải bó xôi (spinach) trồng đất, giàu sắt, dùng nấu canh.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00097', 'LHH07', 'DVT01', NULL, 'Cải Dún (Gói 300g)', '00097.png', 'Cải dún (cải ngọt nhăn) dùng xào tỏi hoặc nấu canh.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00098', 'LHH07', 'DVT01', NULL, 'Cải Ngồng (Gói 300g)', '00098.png', 'Cải ngồng (cải làn) tươi, phần bông non, xào dầu hào.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00099', 'LHH07', 'DVT01', NULL, 'Cải Ngồng Baby (Gói 300g)', '00099.png', 'Cải ngồng baby, thân non, vị ngọt, xào tỏi.', 100.00, 1, 0, '2025-11-20 23:59:59'),
('00100', 'LHH09', 'DVT02', NULL, 'Bưởi Da Xanh (Trái 1.2-1.5kg)', '00100.png', 'Bưởi da xanh ruột hồng, vị ngọt thanh, mọng nước, không hạt hoặc ít hạt.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00101', 'LHH09', 'DVT02', NULL, 'Xoài Cát Hòa Lộc (Kg)', '00101.png', 'Xoài Cát Hòa Lộc chín vàng, thịt mịn, dẻo, thơm và rất ngọt.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00102', 'LHH09', 'DVT02', NULL, 'Thanh Long Ruột Đỏ (Kg)', '00102.png', 'Thanh long ruột đỏ, vỏ mỏng, vị ngọt đậm hơn thanh long trắng.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00103', 'LHH09', 'DVT02', 'KM003', 'Cam Sành (Kg)', '00103.png', 'Cam sành Tiền Giang, mọng nước, vị chua ngọt, giàu vitamin C.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00104', 'LHH09', 'DVT02', NULL, 'Ổi Nữ Hoàng (Kg)', '00104.png', 'Ổi nữ hoàng giòn, ngọt, ít hạt, thơm đặc trưng.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00105', 'LHH09', 'DVT02', NULL, 'Chôm Chôm Nhãn (Kg)', '00105.png', 'Chôm chôm nhãn cùi dày, tróc ráo, giòn và rất ngọt.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00106', 'LHH09', 'DVT02', NULL, 'Măng Cụt (Kg)', '00106.png', 'Măng cụt Lái Thiêu, múi trắng, vị chua ngọt thanh mát.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00107', 'LHH09', 'DVT02', NULL, 'Sầu Riêng Ri 6 (Kg)', '00107.png', 'Sầu riêng Ri 6 cơm vàng, hạt lép, vị béo, ngọt đậm.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00108', 'LHH09', 'DVT02', NULL, 'Dưa Hấu Không Hạt (Kg)', '00108.png', 'Dưa hấu ruột đỏ, không hạt, vỏ mỏng, ngọt mát.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00109', 'LHH09', 'DVT02', 'KM003', 'Mít Thái (Kg)', '00109.png', 'Mít Thái múi to, dày, giòn và ngọt.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00110', 'LHH09', 'DVT02', NULL, 'Vải Thiều (Kg)', '00110.png', 'Vải thiều Lục Ngạn, hạt nhỏ, cùi dày, mọng nước.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00111', 'LHH09', 'DVT02', NULL, 'Nhãn Lồng Hưng Yên (Kg)', '00111.png', 'Nhãn lồng Hưng Yên quả to, cùi dày, thơm, ngọt.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00112', 'LHH09', 'DVT02', 'KM003', 'Đu Đủ (Kg)', '00112.png', 'Đu đủ ruột cam, vị ngọt, mềm, tốt cho tiêu hóa.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00113', 'LHH09', 'DVT02', NULL, 'Bơ 034 (Kg)', '00113.png', 'Bơ 034 vỏ xanh, dẻo, béo, hạt nhỏ.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00114', 'LHH09', 'DVT02', NULL, 'Chuối Cau (Nải)', '00114.png', 'Chuối cau chín tự nhiên, quả nhỏ, thơm, ngọt.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00115', 'LHH10', 'DVT02', 'KM003', 'Táo Envy New Zealand (Kg)', '00115.png', 'Táo Envy NZ size 30, vỏ đỏ, giòn, ngọt đậm và rất thơm.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00116', 'LHH10', 'DVT02', NULL, 'Nho Đen Không Hạt Mỹ (Kg)', '00116.png', 'Nho đen không hạt Mỹ, vỏ mỏng, giòn, ngọt thanh.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00117', 'LHH10', 'DVT01', NULL, 'Kiwi Vàng New Zealand (Hộp 500g)', '00117.png', 'Kiwi vàng Zespri, vị ngọt, giàu Vitamin C.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00118', 'LHH10', 'DVT02', NULL, 'Cherry Đỏ Mỹ (Kg)', '00118.png', 'Cherry đỏ size 9.0, quả to, mọng nước, ngọt đậm.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00119', 'LHH10', 'DVT01', NULL, 'Việt Quất (Hộp 125g)', '00119.png', 'Việt quất (Blueberry) nhập khẩu, giàu chất chống oxy hóa.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00120', 'LHH10', 'DVT02', NULL, 'Lê Hàn Quốc (Trái)', '00120.png', 'Lê Hàn Quốc quả to, giòn, mọng nước, vị ngọt mát.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00121', 'LHH10', 'DVT01', NULL, 'Dâu Tây Hàn Quốc (Hộp 330g)', '00121.png', 'Dâu tây Hàn Quốc, quả to, thơm, vị ngọt.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00122', 'LHH10', 'DVT02', NULL, 'Táo Gala Mỹ (Kg)', '00122.png', 'Táo Gala Mỹ, vỏ sọc đỏ vàng, giòn, vị ngọt nhẹ.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00123', 'LHH10', 'DVT02', NULL, 'Lựu Peru (Kg)', '00123.png', 'Lựu Peru hạt mềm, ruột đỏ, mọng nước, vị ngọt.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00124', 'LHH10', 'DVT02', NULL, 'Cam Vàng Navel Mỹ (Kg)', '00124.png', 'Cam Navel không hạt, vỏ vàng, mọng nước, ngọt.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00125', 'LHH10', 'DVT01', NULL, 'Phúc Bồn Tử (Hộp 170g)', '00125.png', 'Phúc bồn tử (Raspberry) nhập khẩu, vị chua ngọt nhẹ.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00126', 'LHH10', 'DVT01', NULL, 'Táo Rockit New Zealand (Ống 4 trái)', '00126.png', 'Táo Rockit NZ, size nhỏ, giòn tan, ngọt đậm, đóng ống tiện lợi.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00127', 'LHH10', 'DVT02', NULL, 'Mận Đen Mỹ (Kg)', '00127.png', 'Mận đen Mỹ, ruột vàng, vị ngọt, giòn.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00128', 'LHH10', 'DVT02', 'KM003', 'Dưa Lưới Nhật Bản (Trái)', '00128.png', 'Dưa lưới Nhật Bản (Muskmelon) ruột xanh, ngọt thơm.', 100.00, 1, 0, '2025-11-25 23:59:59'),
('00129', 'LHH10', 'DVT02', NULL, 'Táo Xanh Granny Smith (Kg)', '00129.png', 'Táo xanh Granny Smith, giòn, vị chua đậm, dùng làm nước ép.', 100.00, 1, 0, '2025-11-25 23:59:59');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khuyen_mai`
--

CREATE TABLE `khuyen_mai` (
  `ID_KM` varchar(5) NOT NULL,
  `TEN_KM` varchar(100) NOT NULL,
  `PHAN_TRAM_KM` decimal(10,2) NOT NULL,
  `NGAY_BD_KM` datetime NOT NULL,
  `NGAY_KT_KM` datetime NOT NULL,
  `TRANG_THAI_KM` enum('Sắp diễn ra','Đang diễn ra','Đã kết thúc','Đã hủy') NOT NULL DEFAULT 'Sắp diễn ra'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `khuyen_mai`
--

INSERT INTO `khuyen_mai` (`ID_KM`, `TEN_KM`, `PHAN_TRAM_KM`, `NGAY_BD_KM`, `NGAY_KT_KM`, `TRANG_THAI_KM`) VALUES
('KM001', 'Chào mừng khách hàng mới', 15.00, '2025-10-01 00:00:00', '2026-02-01 23:59:59', 'Đang diễn ra'),
('KM002', 'Giảm giá cuối tuần (Hải sản)', 10.00, '2025-10-24 00:00:00', '2025-10-26 23:59:59', 'Đã kết thúc'),
('KM003', 'Ưu đãi đặc biệt', 20.00, '2025-11-01 00:00:00', '2026-01-31 08:52:08', 'Đang diễn ra'),
('KM004', 'Tuần lễ Vàng (Rau củ)', 12.00, '2025-09-15 00:00:00', '2025-09-21 23:59:59', 'Đã kết thúc'),
('KM005', 'Flash Sale (Trái cây)', 25.00, '2025-10-25 10:00:00', '2025-10-25 14:00:00', 'Sắp diễn ra');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loai_hang_hoa`
--

CREATE TABLE `loai_hang_hoa` (
  `ID_LHH` varchar(5) NOT NULL,
  `ID_DM` varchar(5) NOT NULL,
  `TEN_LHH` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='1.M n m?n, Canh x o, Chi n, Nu?ng, Tr ng mi?ng\r\n2. Rau ';

--
-- Đang đổ dữ liệu cho bảng `loai_hang_hoa`
--

INSERT INTO `loai_hang_hoa` (`ID_LHH`, `ID_DM`, `TEN_LHH`) VALUES
('LHH01', 'DM01', 'Món mặn'),
('LHH02', 'DM01', 'Canh xào'),
('LHH03', 'DM01', 'Chiên, Nướng'),
('LHH04', 'DM02', 'Cá'),
('LHH05', 'DM02', 'Tôm, Cua, Mực, khác...'),
('LHH06', 'DM03', 'Rau ăn củ'),
('LHH07', 'DM03', 'Rau lá'),
('LHH09', 'DM04', 'Trái cây nội địa'),
('LHH10', 'DM04', 'Trái cây nhập khẩu');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoi_dung`
--

CREATE TABLE `nguoi_dung` (
  `ID_ND` varchar(5) NOT NULL,
  `PHAN_QUYEN_TK` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`ID_ND`, `PHAN_QUYEN_TK`) VALUES
('AD', 'Admin'),
('KH', 'Khách hàng');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nha_cung_cap`
--

CREATE TABLE `nha_cung_cap` (
  `ID_NCC` varchar(5) NOT NULL,
  `TEN_NCC` varchar(100) NOT NULL,
  `DIA_CHI_NCC` varchar(1000) NOT NULL,
  `SDT_NCC` char(15) NOT NULL,
  `EMAIL_NCC` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `nha_cung_cap`
--

INSERT INTO `nha_cung_cap` (`ID_NCC`, `TEN_NCC`, `DIA_CHI_NCC`, `SDT_NCC`, `EMAIL_NCC`) VALUES
('NCC01', 'Công ty Cổ phần Chăn nuôi C.P. Việt Nam', 'Số 2, Đường 2A, KCN Biên Hòa 2, P. Long Bình, TP. Biên Hòa, Đồng Nai', '02513836251', 'cpvietnam@cp.com.vn'),
('NCC02', 'Công ty Cổ phần Việt Nam Kỹ nghệ Súc sản (VISSAN)', '420 Nơ Trang Long, Phường 13, Quận Bình Thạnh, TP. Hồ Chí Minh', '19001960', 'vissan@vissan.com.vn'),
('NCC03', 'Công ty TNHH Dalatroi (Rau củ Đà Lạt)', 'Phường 12, TP. Đà Lạt, Tỉnh Lâm Đồng', '02633828999', 'info@dalatroi.com'),
('NCC04', 'Tập đoàn Thủy sản Minh Phú', 'Khu Công nghiệp Phường 8, TP. Cà Mau, Tỉnh Cà Mau', '02903838262', 'info@minhphu.com'),
('NCC05', 'Công ty Cổ phần Vĩnh Hoàn (VHC)', 'Quốc lộ 30, Phường 11, TP. Cao Lãnh, Tỉnh Đồng Tháp', '02773891166', 'info@vinhhoan.com');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieu_nhap`
--

CREATE TABLE `phieu_nhap` (
  `ID_PN` varchar(5) NOT NULL,
  `ID_NCC` varchar(5) NOT NULL,
  `NGAY_LAP_PHIEU_NHAP` datetime NOT NULL,
  `TONG_TIEN_NHAP` decimal(10,2) NOT NULL,
  `VAT` decimal(10,2) NOT NULL,
  `TONG_GIA_TRI_PHIEU_NHAP` decimal(10,2) NOT NULL,
  `CHUNG_TU_GOC` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phuong_thuc_thanh_toan`
--

CREATE TABLE `phuong_thuc_thanh_toan` (
  `ID_PTTT` varchar(5) NOT NULL,
  `TEN_PTTT` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `phuong_thuc_thanh_toan`
--

INSERT INTO `phuong_thuc_thanh_toan` (`ID_PTTT`, `TEN_PTTT`) VALUES
('PTTT1', 'Thanh toán khi nhận hàng (COD)'),
('PTTT2', 'Chuyển khoản ngân hàng'),
('PTTT3', 'Thanh toán qua ví MoMo');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tai_khoan`
--

CREATE TABLE `tai_khoan` (
  `ID_TK` char(15) NOT NULL,
  `ID_GH` varchar(5) NOT NULL,
  `ID_ND` varchar(5) NOT NULL,
  `HO_TEN` varchar(30) NOT NULL,
  `GIOI_TINH` char(10) NOT NULL,
  `SDT_TK` char(15) NOT NULL,
  `EMAIL` varchar(100) NOT NULL,
  `MAT_KHAU` varchar(255) NOT NULL,
  `NGAY_GIO_TAO_TK` datetime NOT NULL,
  `NGAY_GIO_CAP_NHAT` datetime NOT NULL,
  `DIA_CHI_AVT` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tai_khoan`
--

INSERT INTO `tai_khoan` (`ID_TK`, `ID_GH`, `ID_ND`, `HO_TEN`, `GIOI_TINH`, `SDT_TK`, `EMAIL`, `MAT_KHAU`, `NGAY_GIO_TAO_TK`, `NGAY_GIO_CAP_NHAT`, `DIA_CHI_AVT`) VALUES
('TK690a29dd24ac3', 'GH690', 'AD', 'tram', 'Nữ', '011111', 'tram@gmail.com', '$2y$10$htapMP/bY7YUDj1Y5DEyc.isJuNG298XFqepSQeKPHfVaQum9/kY2', '2025-11-04 23:29:17', '2025-11-04 23:29:17', NULL),
('TK690a2d551daad', 'GHab0', 'AD', 'lam', 'Nữ', '099', 'lam@gmail.com', '$2y$10$5bQuBHHhW2OqtbHjT7dvZewvj5tryRVKbQs2GLeNH/E407MebgiCy', '2025-11-04 23:44:05', '2025-11-04 23:44:05', NULL),
('TK690a2db982cee', 'GHcf3', 'KH', 'dao', 'Nữ', '02222', 'dao@gmail.com', '$2y$10$A7MpPzNNeT2Dd6pujlS89.vdAMcECW8VRo/mjG59eGcOX1.E0g/hi', '2025-11-04 23:45:45', '2025-11-09 23:53:04', NULL),
('TK690a394e5231c', 'GH31f', 'KH', 'aa', 'Nam', '0223', 'a@gmail.com', '$2y$10$tktAmDmQB3Q.Ewy5FiA.gumRI3P8pdIHpAPY/7U/vei3NJG1IeGOa', '2025-11-05 00:35:10', '2025-11-05 00:35:10', NULL),
('TK690b6bf878a2b', 'GHa2e', 'KH', 'hoa', 'Nam', '0234', 'hoa@gmail.com', '$2y$10$1KX1OMj05MNT0DzzAxyPnuy0OECH486Sf/T8cnhY1CYomdDPoZNoa', '2025-11-05 22:23:36', '2025-11-05 22:23:36', NULL),
('TK690b6c23666b4', 'GH6b9', 'KH', 'ti@gmail.com', 'Nữ', '0222', 'ti@gmail.com', '$2y$10$uojuuWm80noDerbqpYH3DebPP.dX7TE6bfxp.6xieRkcfqVjL3JrK', '2025-11-05 22:24:19', '2025-11-05 22:24:19', NULL),
('TK6912f22e29d58', 'GH691', 'KH', 'Sơn Tùng MTP', 'Nữ', '09999', 'mtp@gmail.com', '$2y$10$UI5MN3fT2LVQy7aP9z7PWeg1FELFFv0TkPMma0zot4h5W4Rb9vBBO', '2025-11-11 15:22:06', '2025-11-11 15:22:06', NULL),
('TK691862df94dd9', 'GHddd', 'KH', 'mia', 'Nữ', '07878', 'mia@gmail.com', '$2y$10$vhhTbPwLD8gQqDZu1Ld8g.uaJ38KXm5fb.22P71820d1ZZiEXT3kS', '2025-11-15 18:24:15', '2025-11-15 18:24:15', NULL),
('TK69186339d8aab', 'GHab1', 'KH', 'teo', 'Nữ', '09123', 'teo@gmail.com', '$2y$10$kyPLcJqfAhvkx4wWp3VxjOzq99UeUIcLBJ46I2O8SYb3NHVu2sW2m', '2025-11-15 18:25:46', '2025-11-15 18:25:46', NULL),
('TK69186da1a7b90', 'GHb93', 'KH', 'siêu nhân', 'Nam', '07895', 'sieunhan@gmail.com', '$2y$10$TtEsdp9oGktbcs4nbLpjxu08KkmGUW63971206uCS9noJaGUKKIc2', '2025-11-15 19:10:09', '2025-11-15 19:10:09', NULL),
('TK69187df4a4440', 'GH444', 'KH', 'capy', 'Nam', '0437', 'capy@gmail.com', '$2y$10$Edg17i5d3wh0jUynSq/veuvmTrQhvaw/kf13SqKfO47QW37rJ7hwq', '2025-11-15 20:19:48', '2025-11-15 20:19:48', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thoi_diem`
--

CREATE TABLE `thoi_diem` (
  `ID_TD` varchar(5) NOT NULL,
  `NGAY_BD_GIA_BAN` datetime NOT NULL,
  `NGAY_KT_GIA_BAN` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `thoi_diem`
--

INSERT INTO `thoi_diem` (`ID_TD`, `NGAY_BD_GIA_BAN`, `NGAY_KT_GIA_BAN`) VALUES
('TD001', '2025-09-01 00:00:00', '2025-09-30 00:00:00'),
('TD002', '2025-10-01 00:00:00', '2025-10-31 00:00:00'),
('TD003', '2025-11-01 00:00:00', '2026-01-01 00:00:00'),
('TD004', '2026-01-02 00:00:00', '2026-01-31 00:00:00');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `binh_luan`
--
ALTER TABLE `binh_luan`
  ADD PRIMARY KEY (`ID_HH`,`ID_BL`),
  ADD KEY `FK_BINH_LUAN_TK_BL_TAI_KHOA` (`ID_TK`);

--
-- Chỉ mục cho bảng `chi_tiet_don_hang`
--
ALTER TABLE `chi_tiet_don_hang`
  ADD PRIMARY KEY (`ID_DH`,`ID_HH`),
  ADD KEY `FK_CHI_TIET_CTDH_HH_HANG_HOA` (`ID_HH`);

--
-- Chỉ mục cho bảng `chi_tiet_gio_hang`
--
ALTER TABLE `chi_tiet_gio_hang`
  ADD PRIMARY KEY (`ID_GH`,`ID_HH`),
  ADD KEY `FK_CHI_TIET_CTGH_HH_HANG_HOA` (`ID_HH`);

--
-- Chỉ mục cho bảng `chi_tiet_phieu_nhap`
--
ALTER TABLE `chi_tiet_phieu_nhap`
  ADD PRIMARY KEY (`ID_PN`,`ID_HH`),
  ADD KEY `FK_CHI_TIET_CTPN_HH_HANG_HOA` (`ID_HH`);

--
-- Chỉ mục cho bảng `danh_muc`
--
ALTER TABLE `danh_muc`
  ADD PRIMARY KEY (`ID_DM`);

--
-- Chỉ mục cho bảng `dia_chi_giao_hang`
--
ALTER TABLE `dia_chi_giao_hang`
  ADD PRIMARY KEY (`ID_DIA_CHI`),
  ADD KEY `FK_DIA_CHI_TAI_KHOAN` (`ID_TK`);

--
-- Chỉ mục cho bảng `don_hang`
--
ALTER TABLE `don_hang`
  ADD PRIMARY KEY (`ID_DH`),
  ADD KEY `FK_DON_HANG_DH_PTTT_PHUONG_T` (`ID_PTTT`),
  ADD KEY `FK_DON_HANG_TK_DH_TAI_KHOA` (`ID_TK`);

--
-- Chỉ mục cho bảng `don_hang_hien_tai`
--
ALTER TABLE `don_hang_hien_tai`
  ADD PRIMARY KEY (`ID_DH`);

--
-- Chỉ mục cho bảng `dvt`
--
ALTER TABLE `dvt`
  ADD PRIMARY KEY (`ID_DVT`);

--
-- Chỉ mục cho bảng `gia_ban_hien_tai`
--
ALTER TABLE `gia_ban_hien_tai`
  ADD PRIMARY KEY (`ID_HH`,`ID_TD`),
  ADD KEY `FK_GIA_BAN__TD_GBHT_THOI_DIE` (`ID_TD`);

--
-- Chỉ mục cho bảng `gio_hang`
--
ALTER TABLE `gio_hang`
  ADD PRIMARY KEY (`ID_GH`),
  ADD KEY `FK_GIO_HANG_GH_TK2_TAI_KHOA` (`ID_TK`);

--
-- Chỉ mục cho bảng `hang_hoa`
--
ALTER TABLE `hang_hoa`
  ADD PRIMARY KEY (`ID_HH`),
  ADD KEY `FK_HANG_HOA_HH_DVT_DVT` (`ID_DVT`),
  ADD KEY `FK_HANG_HOA_HH_KM_KHUYEN_M` (`ID_KM`),
  ADD KEY `FK_HANG_HOA_HH_LHH_LOAI_HAN` (`ID_LHH`);

--
-- Chỉ mục cho bảng `khuyen_mai`
--
ALTER TABLE `khuyen_mai`
  ADD PRIMARY KEY (`ID_KM`);

--
-- Chỉ mục cho bảng `loai_hang_hoa`
--
ALTER TABLE `loai_hang_hoa`
  ADD PRIMARY KEY (`ID_LHH`),
  ADD KEY `FK_LOAI_HAN_LSP_DM_DANH_MUC` (`ID_DM`);

--
-- Chỉ mục cho bảng `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  ADD PRIMARY KEY (`ID_ND`);

--
-- Chỉ mục cho bảng `nha_cung_cap`
--
ALTER TABLE `nha_cung_cap`
  ADD PRIMARY KEY (`ID_NCC`);

--
-- Chỉ mục cho bảng `phieu_nhap`
--
ALTER TABLE `phieu_nhap`
  ADD PRIMARY KEY (`ID_PN`),
  ADD KEY `FK_PHIEU_NH_PN_NCC_NHA_CUNG` (`ID_NCC`);

--
-- Chỉ mục cho bảng `phuong_thuc_thanh_toan`
--
ALTER TABLE `phuong_thuc_thanh_toan`
  ADD PRIMARY KEY (`ID_PTTT`);

--
-- Chỉ mục cho bảng `tai_khoan`
--
ALTER TABLE `tai_khoan`
  ADD PRIMARY KEY (`ID_TK`),
  ADD KEY `FK_TAI_KHOA_TK_ND_NGUOI_DU` (`ID_ND`),
  ADD KEY `FK_TAI_KHOA_GH_TK_GIO_HANG` (`ID_GH`);

--
-- Chỉ mục cho bảng `thoi_diem`
--
ALTER TABLE `thoi_diem`
  ADD PRIMARY KEY (`ID_TD`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `dia_chi_giao_hang`
--
ALTER TABLE `dia_chi_giao_hang`
  MODIFY `ID_DIA_CHI` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `binh_luan`
--
ALTER TABLE `binh_luan`
  ADD CONSTRAINT `FK_BINH_LUAN_HH_BL_HANG_HOA` FOREIGN KEY (`ID_HH`) REFERENCES `hang_hoa` (`ID_HH`),
  ADD CONSTRAINT `FK_BINH_LUAN_TK_BL_TAI_KHOA` FOREIGN KEY (`ID_TK`) REFERENCES `tai_khoan` (`ID_TK`);

--
-- Các ràng buộc cho bảng `chi_tiet_don_hang`
--
ALTER TABLE `chi_tiet_don_hang`
  ADD CONSTRAINT `FK_CHI_TIET_CTDH_DH_DON_HANG` FOREIGN KEY (`ID_DH`) REFERENCES `don_hang` (`ID_DH`),
  ADD CONSTRAINT `FK_CHI_TIET_CTDH_HH_HANG_HOA` FOREIGN KEY (`ID_HH`) REFERENCES `hang_hoa` (`ID_HH`);

--
-- Các ràng buộc cho bảng `chi_tiet_gio_hang`
--
ALTER TABLE `chi_tiet_gio_hang`
  ADD CONSTRAINT `FK_CHI_TIET_CTGH_GH_GIO_HANG` FOREIGN KEY (`ID_GH`) REFERENCES `gio_hang` (`ID_GH`),
  ADD CONSTRAINT `FK_CHI_TIET_CTGH_HH_HANG_HOA` FOREIGN KEY (`ID_HH`) REFERENCES `hang_hoa` (`ID_HH`);

--
-- Các ràng buộc cho bảng `chi_tiet_phieu_nhap`
--
ALTER TABLE `chi_tiet_phieu_nhap`
  ADD CONSTRAINT `FK_CHI_TIET_CTPN_HH_HANG_HOA` FOREIGN KEY (`ID_HH`) REFERENCES `hang_hoa` (`ID_HH`),
  ADD CONSTRAINT `FK_CHI_TIET_CTPN_PN_PHIEU_NH` FOREIGN KEY (`ID_PN`) REFERENCES `phieu_nhap` (`ID_PN`);

--
-- Các ràng buộc cho bảng `dia_chi_giao_hang`
--
ALTER TABLE `dia_chi_giao_hang`
  ADD CONSTRAINT `FK_DIA_CHI_TAI_KHOAN` FOREIGN KEY (`ID_TK`) REFERENCES `tai_khoan` (`ID_TK`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `don_hang`
--
ALTER TABLE `don_hang`
  ADD CONSTRAINT `FK_DON_HANG_DH_PTTT_PHUONG_T` FOREIGN KEY (`ID_PTTT`) REFERENCES `phuong_thuc_thanh_toan` (`ID_PTTT`),
  ADD CONSTRAINT `FK_DON_HANG_TK_DH_TAI_KHOA` FOREIGN KEY (`ID_TK`) REFERENCES `tai_khoan` (`ID_TK`);

--
-- Các ràng buộc cho bảng `don_hang_hien_tai`
--
ALTER TABLE `don_hang_hien_tai`
  ADD CONSTRAINT `FK_DHHT_TO_DH` FOREIGN KEY (`ID_DH`) REFERENCES `don_hang` (`ID_DH`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `gia_ban_hien_tai`
--
ALTER TABLE `gia_ban_hien_tai`
  ADD CONSTRAINT `FK_GIA_BAN__GBHT_HH_HANG_HOA` FOREIGN KEY (`ID_HH`) REFERENCES `hang_hoa` (`ID_HH`),
  ADD CONSTRAINT `FK_GIA_BAN__TD_GBHT_THOI_DIE` FOREIGN KEY (`ID_TD`) REFERENCES `thoi_diem` (`ID_TD`);

--
-- Các ràng buộc cho bảng `hang_hoa`
--
ALTER TABLE `hang_hoa`
  ADD CONSTRAINT `FK_HANG_HOA_HH_DVT_DVT` FOREIGN KEY (`ID_DVT`) REFERENCES `dvt` (`ID_DVT`),
  ADD CONSTRAINT `FK_HANG_HOA_HH_KM_KHUYEN_M` FOREIGN KEY (`ID_KM`) REFERENCES `khuyen_mai` (`ID_KM`),
  ADD CONSTRAINT `FK_HANG_HOA_HH_LHH_LOAI_HAN` FOREIGN KEY (`ID_LHH`) REFERENCES `loai_hang_hoa` (`ID_LHH`);

--
-- Các ràng buộc cho bảng `loai_hang_hoa`
--
ALTER TABLE `loai_hang_hoa`
  ADD CONSTRAINT `FK_LOAI_HAN_LSP_DM_DANH_MUC` FOREIGN KEY (`ID_DM`) REFERENCES `danh_muc` (`ID_DM`);

--
-- Các ràng buộc cho bảng `phieu_nhap`
--
ALTER TABLE `phieu_nhap`
  ADD CONSTRAINT `FK_PHIEU_NH_PN_NCC_NHA_CUNG` FOREIGN KEY (`ID_NCC`) REFERENCES `nha_cung_cap` (`ID_NCC`);

--
-- Các ràng buộc cho bảng `tai_khoan`
--
ALTER TABLE `tai_khoan`
  ADD CONSTRAINT `FK_TAI_KHOA_TK_ND_NGUOI_DU` FOREIGN KEY (`ID_ND`) REFERENCES `nguoi_dung` (`ID_ND`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
