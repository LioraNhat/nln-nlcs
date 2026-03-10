-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 06, 2026 at 06:45 PM
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
-- Database: `luanvan`
--

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_don_hang`
--

CREATE TABLE `chi_tiet_don_hang` (
  `id_dh` varchar(10) NOT NULL,
  `id_hh` varchar(10) NOT NULL,
  `id_lo` varchar(10) NOT NULL,
  `so_luong_ban_ra` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chi_tiet_don_hang`
--

INSERT INTO `chi_tiet_don_hang` (`id_dh`, `id_hh`, `id_lo`, `so_luong_ban_ra`) VALUES
('DH0000001', '00003', 'LO00003A', 1),
('DH0000001', '00004', 'LO00004A', 1),
('DH0000002', '00001', 'LO00001A', 2),
('DH0000002', '00002', 'LO00002A', 3),
('DH0000003', '00005', 'LO00005A', 2);

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_gio_hang`
--

CREATE TABLE `chi_tiet_gio_hang` (
  `id_gh` varchar(5) NOT NULL,
  `id_hh` varchar(10) NOT NULL,
  `so_luong` int(11) NOT NULL
) ;

--
-- Dumping data for table `chi_tiet_gio_hang`
--

INSERT INTO `chi_tiet_gio_hang` (`id_gh`, `id_hh`, `so_luong`) VALUES
('GH31f', '00004', 1),
('GH31f', '00005', 1),
('GH31f', '00063', 1),
('GH31f', '00113', 1),
('GH5b9', '00003', 1),
('GH5b9', '00004', 2),
('GHK01', '00100', 2),
('GHK02', '00055', 3),
('GHQ01', '00076', 1);

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_phieu_nhap`
--

CREATE TABLE `chi_tiet_phieu_nhap` (
  `id_pn` varchar(10) NOT NULL,
  `id_lo` varchar(10) NOT NULL,
  `so_luong_nhap_lo` int(11) NOT NULL,
  `don_gia_nhap_lo` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chi_tiet_phieu_nhap`
--

INSERT INTO `chi_tiet_phieu_nhap` (`id_pn`, `id_lo`, `so_luong_nhap_lo`, `don_gia_nhap_lo`) VALUES
('PN0000001', 'LO00014A', 1, 20000.00),
('PN0000001', 'LO00123A', 7, 300000.00),
('PN0000002', 'LO00116A', 10, 10000.00),
('PN0000002', 'LO00120A', 5, 90000.00),
('PN0000003', 'LO00109A', 3, 10000.00);

-- --------------------------------------------------------

--
-- Table structure for table `danh_muc`
--

CREATE TABLE `danh_muc` (
  `id_dm` varchar(5) NOT NULL,
  `ten_dm` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `danh_muc`
--

INSERT INTO `danh_muc` (`id_dm`, `ten_dm`) VALUES
('DM01', 'Thực phẩm sơ chế'),
('DM02', 'Hải sản'),
('DM03', 'Rau củ quả'),
('DM04', 'Trái cây tươi'),
('DM05', 'Gia vị');

-- --------------------------------------------------------

--
-- Table structure for table `danh_muc_trang_thai`
--

CREATE TABLE `danh_muc_trang_thai` (
  `id_ttd` varchar(5) NOT NULL,
  `ten_trang_thai` varchar(100) NOT NULL,
  `mo_ta` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `danh_muc_trang_thai`
--

INSERT INTO `danh_muc_trang_thai` (`id_ttd`, `ten_trang_thai`, `mo_ta`) VALUES
('TTD01', 'Chờ xử lý', 'Đơn hàng mới tạo, chờ nhân viên xác nhận'),
('TTD02', 'Đã xác nhận', 'Đơn hàng đã được xác nhận, chuẩn bị hàng'),
('TTD03', 'Đang giao hàng', 'Đơn hàng đang trên đường giao đến khách'),
('TTD04', 'Giao thành công', 'Khách hàng đã nhận được hàng'),
('TTD05', 'Đã hủy', 'Đơn hàng đã bị hủy bởi khách hoặc hệ thống');

-- --------------------------------------------------------

--
-- Table structure for table `dia_chi_giao_hang`
--

CREATE TABLE `dia_chi_giao_hang` (
  `id_dc` int(11) NOT NULL,
  `id_tk` char(15) NOT NULL,
  `ten_nguoi_nhan` varchar(250) NOT NULL,
  `sdt_gh` char(15) NOT NULL,
  `ma_tinh_tp` varchar(20) NOT NULL,
  `ten_tinh_tp` varchar(100) NOT NULL,
  `ma_quan_huyen` varchar(20) DEFAULT NULL,
  `ten_quan_huyen` varchar(100) DEFAULT NULL,
  `ma_xa_phuong` varchar(20) NOT NULL,
  `ten_xa_phuong` varchar(100) NOT NULL,
  `dia_chi_chi_tiet` varchar(255) NOT NULL,
  `mac_dinh` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dia_chi_giao_hang`
--

INSERT INTO `dia_chi_giao_hang` (`id_dc`, `id_tk`, `ten_nguoi_nhan`, `sdt_gh`, `ma_tinh_tp`, `ten_tinh_tp`, `ma_quan_huyen`, `ten_quan_huyen`, `ma_xa_phuong`, `ten_xa_phuong`, `dia_chi_chi_tiet`, `mac_dinh`) VALUES
(1, 'TK69451a062e5b5', 'Khánh', '0123456789', 'CT', 'Cần Thơ', 'NK', 'Ninh Kiều', 'AH', 'An Hội', '123 Đường Trần Phú', 1),
(2, 'TK69a69ba3e0319', 'Hoa Mai', '0373205595', 'CT', 'Cần Thơ', 'NK', 'Ninh Kiều', 'XT', 'Xuân Khánh', '456 Đường 30/4', 1),
(3, 'TK00000000000KH', 'Nguyễn Văn An', '0901111222', 'CT', 'Cần Thơ', 'BT', 'Bình Thủy', 'LH', 'Long Hòa', '789 Nguyễn Văn Cừ', 1),
(4, 'TK00000000000K2', 'Trần Thị Bình', '0902222333', 'CT', 'Cần Thơ', 'NK', 'Ninh Kiều', 'TA', 'Tân An', '101 Mậu Thân', 1),
(5, 'TK00000000000QL', 'Lê Văn Cường', '0903333444', 'CT', 'Cần Thơ', 'OC', 'Ô Môn', 'PH', 'Phước Hưng', '202 Cách Mạng Tháng 8', 1);

-- --------------------------------------------------------

--
-- Table structure for table `don_hang`
--

CREATE TABLE `don_hang` (
  `id_dh` varchar(10) NOT NULL,
  `id_tk` char(15) DEFAULT NULL,
  `id_pttt` varchar(5) DEFAULT NULL,
  `id_dc` int(11) DEFAULT NULL,
  `ngay_gio_tao_don` datetime NOT NULL DEFAULT current_timestamp(),
  `tong_gia_tri_don` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tien_giam_gia` decimal(10,2) NOT NULL DEFAULT 0.00,
  `thanh_tien` decimal(10,2) NOT NULL DEFAULT 0.00,
  `trang_thai_thanh_toan` tinyint(4) NOT NULL DEFAULT 0,
  `ngay_thanh_toan` datetime DEFAULT NULL
) ;

--
-- Dumping data for table `don_hang`
--

INSERT INTO `don_hang` (`id_dh`, `id_tk`, `id_pttt`, `id_dc`, `ngay_gio_tao_don`, `tong_gia_tri_don`, `tien_giam_gia`, `thanh_tien`, `trang_thai_thanh_toan`, `ngay_thanh_toan`) VALUES
('DH0000001', 'TK69451a062e5b5', 'PTTT1', 1, '2025-12-20 09:00:00', 267000.00, 0.00, 267000.00, 1, '2025-12-20 09:05:00'),
('DH0000002', 'TK69a69ba3e0319', 'PTTT2', 2, '2025-12-22 14:30:00', 498000.00, 50000.00, 448000.00, 1, '2025-12-22 15:00:00'),
('DH0000003', 'TK00000000000KH', 'PTTT3', 3, '2026-01-05 10:00:00', 320000.00, 0.00, 320000.00, 0, NULL),
('DH0000004', 'TK00000000000K2', 'PTTT1', 4, '2026-01-10 08:45:00', 189000.00, 0.00, 189000.00, 0, NULL),
('DH0000005', 'TK00000000000QL', 'PTTT2', 5, '2026-01-15 16:20:00', 750000.00, 75000.00, 675000.00, 1, '2026-01-15 17:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `don_hang_hien_tai`
--

CREATE TABLE `don_hang_hien_tai` (
  `id_log` int(11) NOT NULL,
  `id_dh` varchar(10) DEFAULT NULL,
  `id_ttd` varchar(5) DEFAULT NULL,
  `thoi_gian` datetime NOT NULL,
  `ghi_chu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `don_hang_hien_tai`
--

INSERT INTO `don_hang_hien_tai` (`id_log`, `id_dh`, `id_ttd`, `thoi_gian`, `ghi_chu`) VALUES
(1, 'DH0000001', 'TTD04', '2025-12-20 09:05:00', 'Giao thành công'),
(2, 'DH0000002', 'TTD04', '2025-12-22 15:00:00', 'Giao thành công'),
(3, 'DH0000003', 'TTD01', '2026-01-05 10:00:00', 'Đơn mới, chờ xử lý'),
(4, 'DH0000004', 'TTD02', '2026-01-10 09:00:00', 'Đã xác nhận đơn'),
(5, 'DH0000005', 'TTD03', '2026-01-15 17:30:00', 'Đang giao hàng');

-- --------------------------------------------------------

--
-- Table structure for table `dvt`
--

CREATE TABLE `dvt` (
  `id_dvt` varchar(5) NOT NULL,
  `dvt` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dvt`
--

INSERT INTO `dvt` (`id_dvt`, `dvt`) VALUES
('DVT01', 'Hộp'),
('DVT02', 'Kg'),
('DVT03', 'Gram');

-- --------------------------------------------------------

--
-- Table structure for table `gia_ban_hien_tai`
--

CREATE TABLE `gia_ban_hien_tai` (
  `id_lo` varchar(10) NOT NULL,
  `id_td` varchar(5) NOT NULL,
  `gia_hien_tai` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gia_ban_hien_tai`
--

INSERT INTO `gia_ban_hien_tai` (`id_lo`, `id_td`, `gia_hien_tai`) VALUES
('LO00001A', 'TD003', 89000.00),
('LO00002A', 'TD003', 79000.00),
('LO00003A', 'TD003', 89000.00),
('LO00004A', 'TD003', 99000.00),
('LO00005A', 'TD003', 78000.00),
('LO00014A', 'TD003', 159000.00),
('LO00109A', 'TD003', 40000.00),
('LO00116A', 'TD003', 249000.00),
('LO00120A', 'TD003', 80000.00),
('LO00123A', 'TD003', 150000.00);

-- --------------------------------------------------------

--
-- Table structure for table `gio_hang`
--

CREATE TABLE `gio_hang` (
  `id_gh` varchar(5) NOT NULL,
  `id_tk` char(15) DEFAULT NULL,
  `ngay_tao_gh` datetime NOT NULL DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gio_hang`
--

INSERT INTO `gio_hang` (`id_gh`, `id_tk`, `ngay_tao_gh`, `ngay_cap_nhat`) VALUES
('GH31f', 'TK69a69ba3e0319', '2026-03-03 15:28:19', '2026-03-03 15:28:19'),
('GH5b9', 'TK69451a062e5b5', '2025-12-19 16:25:26', '2025-12-19 16:25:26'),
('GHK01', 'TK00000000000KH', '2026-01-04 08:00:00', '2026-01-04 08:00:00'),
('GHK02', 'TK00000000000K2', '2026-01-09 10:00:00', '2026-01-09 10:00:00'),
('GHQ01', 'TK00000000000QL', '2026-01-14 14:00:00', '2026-01-14 14:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `hang_hoa`
--

CREATE TABLE `hang_hoa` (
  `id_hh` varchar(10) NOT NULL,
  `id_loai2` varchar(5) NOT NULL,
  `id_dvt` varchar(5) NOT NULL,
  `ten_hh` varchar(100) NOT NULL,
  `mo_ta_hh` text NOT NULL,
  `duoc_phep_ban` tinyint(1) NOT NULL DEFAULT 1,
  `la_hang_sx` tinyint(1) NOT NULL DEFAULT 0,
  `link_anh` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hang_hoa`
--

INSERT INTO `hang_hoa` (`id_hh`, `id_loai2`, `id_dvt`, `ten_hh`, `mo_ta_hh`, `duoc_phep_ban`, `la_hang_sx`, `link_anh`) VALUES
('00001', 'LHH01', 'DVT01', 'Ba Rọi Chiên Nước Mắm (Khay 300gr)', 'Thành phần: 300gr Ba rọi heo, 50gr sốt nước mắm, Tỏi xay 3S. Khẩu phần: 2-3 Người Ăn.', 1, 1, '00001.png'),
('00002', 'LHH01', 'DVT01', 'Ba Rọi Chiên Sả Ớt (Khay 300G)', 'Khẩu phần: 2-3 Người Ăn. Thành phần: Gà làm sạch 300g + Sả xay 50gr + Ớt xay 10gr.', 1, 1, '00002.png'),
('00003', 'LHH01', 'DVT01', 'Ba Rọi Kho Củ Cải Trắng (Khay 89k)', 'Khẩu phần: 2-3 Người Ăn. Thành phần: Ba Rọi Heo 200g + Củ Cải Trắng Cắt Sẵn...', 1, 1, '00003.png'),
('00004', 'LHH01', 'DVT01', 'Ba Rọi Kho Măng (Khay 450G)', 'Khẩu phần: 2-3 Người Ăn. Thành phần 200gr Ba Rọi Heo, 150gr Măng Tươi Cắt Sẵn...', 1, 1, '00004.png'),
('00005', 'LHH01', 'DVT01', 'Ba Rọi Kho Thơm (Khay 300g)', 'Khẩu phần: 2-3 Người Ăn. Thành phần: Ba Rọi Heo: 200g + Thơm gọt (Trái): 300g...', 1, 1, '00005.png'),
('00006', 'LHH01', 'DVT01', 'Ba Rọi Rút Sườn Khoa BBQ (Khay 110k)', 'Khẩu phần: 2-3 Người Ăn. Thành phần: Ba Rọi Heo Rút Sườn: 300g + Sốt ướp nướng GreenMeal...', 1, 1, '00006.png'),
('00007', 'LHH01', 'DVT01', 'Ba Rọi Ướp Xá Xíu (Khay 300g)', 'Khẩu phần: 2-3 Người Ăn. Thành phần: 300g Ba rọi heo, 50g Sốt ướp GreenMeal Kitchen.', 1, 1, '00007.png'),
('00008', 'LHH01', 'DVT01', 'Bắp Giò Heo Kho Củ Cải (Khay 89k)', 'Thành phần: 400gr Bắp Giò Heo, 200gr Củ Cải Trắng Cắt Sẵn, Sốt Kho GreenMeal...', 1, 1, '00008.png'),
('00009', 'LHH01', 'DVT01', 'Cá Bạc Má Kho Thơm (Khay 400g)', 'Khẩu phần: 2-3 Người Ăn. Thành phần: Cá Bạc Má SP CP Sẵn 300G, Thơm gọt (Trái) 1/4 trái...', 1, 1, '00009.png'),
('00010', 'LHH01', 'DVT01', 'Cá Basa Kho Ba Rọi GreenMeal (Khay 126k)', 'Khẩu phần: 1-2 người ăn. Thành phần: Cá Basa SP Size 2-2.5: 300g + Thịt Ba Rọi Heo: 200g...', 0, 1, '00010.png'),
('00011', 'LHH01', 'DVT01', 'Cá Basa Kho Tiêu (Khay 300g)', 'Thành phần: Cá Basa SP Size 2-2.5: 300gr + Tiêu Đen Xay Việt San: 10g...', 1, 1, '00011.png'),
('00012', 'LHH01', 'DVT01', 'Cá Basa Kho Tộ GreenMeal (Khay 300G)', 'Khẩu phần: 1-2 người ăn. Thành phần: Cá Basa: 300g...', 1, 1, '00012.png'),
('00013', 'LHH01', 'DVT01', 'Cá Bống Đục Kho Tiêu (Khay 300g)', 'Cá Bống Đục Kho Tiêu (Khay 300g), Cá Bống SP Sz 20-25, Sốt Kho GreenMeal...', 1, 1, '00013.png'),
('00014', 'LHH01', 'DVT01', 'Cá Bống Tượng Kho Tiêu (Khay 159k)', 'Khẩu phần: 2-3 Người Ăn. Thành phần: Cá Bống Tượng Sz 20-25 con: 300gr + Sốt Kho GreenMeal...', 1, 1, '00014.png'),
('00015', 'LHH01', 'DVT01', 'Cá Bớp Kho Thịt Ba Rọi (Khay 189k)', 'Khẩu phần: 1-2 người ăn. Thành phần: Cá Bớp: 300g + Ba Rọi Heo: 150g + Sốt Kho GreenMeal...', 1, 1, '00015.png'),
('00016', 'LHH02', 'DVT01', 'Bắp Cải Trái Tim Cắt Sẵn (Khay 300g)', 'Thành phần: Bắp cải trái tim cắt sẵn (300g).', 1, 1, '00016.png'),
('00017', 'LHH02', 'DVT01', 'Bắp Cải Xào Cà Chua (Khay 600Gr)', 'Thành phần: Bắp Cải Trắng 300g + Cà chua Rita 1 trái + Sốt Xào GreenMeal Kitchen...', 1, 1, '00017.png'),
('00018', 'LHH02', 'DVT01', 'Bầu Cắt Sẵn (Khay 300Gr)', 'Thành phần: Bầu cắt sẵn. Khẩu phần: 1-2 người ăn.', 1, 1, '00018.png'),
('00019', 'LHH02', 'DVT01', 'Bí Đao Cắt Sẵn (Khay 300gr)', 'Thành phần: Bí đao cắt sẵn. Khẩu phần: 1-2 người ăn.', 1, 1, '00019.png'),
('00020', 'LHH02', 'DVT01', 'Bí Đỏ Hồ Lô Cắt Sẵn (Khay 300Gr)', 'Thành phần: Bí Đỏ Hồ Lô Cắt Sẵn. Khẩu phần: 1-2 người ăn.', 1, 1, '00020.png'),
('00021', 'LHH02', 'DVT01', 'Bí Đỏ Tròn Cắt Sẵn (Khay 300Gr)', 'Thành phần: Bí Đỏ Tròn Cắt Sẵn. Khẩu phần: 1-2 người ăn.', 1, 1, '00021.png'),
('00022', 'LHH02', 'DVT01', 'Bò Xào Đậu Que (Khay 300Gr)', 'Thành phần: Thịt Bò Xào 125gr + Đậu Que cắt sẵn 200gr + Sốt xào GreenMeal + Tỏi xay.', 1, 1, '00022.png'),
('00023', 'LHH02', 'DVT01', 'Bò Xào Măng Tây (Khay 270Gr)', 'Thành phần: Thịt bò xào 125g + Măng Tây 100g + Sốt xào GreenMeal + Tỏi xay.', 1, 1, '00023.png'),
('00024', 'LHH02', 'DVT01', 'Bò Xào Nấm (Khay 250G)', 'Thành phần: 125gr Thịt Bò + 100gr Nấm Đùi Gà + Hành Lá + Sốt xào GreenMeal + Tỏi Xay.', 1, 1, '00024.png'),
('00025', 'LHH02', 'DVT01', 'Bông Bí Xào Tỏi (Khay 200g)', 'Thành phần: 180g Bông Bí, 20g Tỏi xay, Sốt xào, Hành lá. Khẩu phần: 2 Người Ăn.', 1, 1, '00025.png'),
('00026', 'LHH02', 'DVT01', 'Bông Bí Xào Tôm (Khay 300g)', 'Thành phần: 180g Bông Bí, 100g Tôm Lột Vỏ, Sốt xào, Tỏi xay, Hành lá. Khẩu phần: 2 Người Ăn.', 1, 1, '00026.png'),
('00027', 'LHH02', 'DVT01', 'Bông Cải Trắng Cắt Sẵn (Khay 300Gr)', 'Thành phần: Bông Cải Trắng Cắt Sẵn. Khẩu phần: 1-2 người ăn.', 1, 1, '00027.png'),
('00028', 'LHH02', 'DVT01', 'Bông Cải Xanh Cắt Sẵn (Khay 300Gr)', 'Thành phần: Bông cải cắt sẵn. Khẩu phần: 1-2 người ăn.', 1, 1, '00028.png'),
('00029', 'LHH02', 'DVT01', 'Cá Diêu Hồng Hấp Hành Gừng (Khay 700G)', 'Thành phần: 700gr Cá Diêu Hồng, Gừng Tươi, Hành Lá, Hành Tím Xay, Tỏi Xay.', 1, 1, '00029.png'),
('00030', 'LHH03', 'DVT01', 'Sụn Gà Nướng Muối Ớt (Khay 500g)', 'Thành phần: 500gr Sụn ức gà CP, Muối tinh, Bột ớt DH Foods, Đường tinh luyện, Dầu điều GreenMeal.', 1, 1, '00030.png'),
('00031', 'LHH03', 'DVT01', 'Tôm Lớn Nướng Muối Ớt (Khay 300G)', 'Chưa có mô tả cho sản phẩm này.', 1, 1, '00031.png'),
('00032', 'LHH03', 'DVT01', 'Tôm Nhỏ Nướng Muối Ớt (Khay 300G)', 'Chưa có mô tả cho sản phẩm này.', 1, 1, '00032.png'),
('00033', 'LHH03', 'DVT01', 'Mực Nướng Muối Ớt (Khay 300G)', 'Chưa có mô tả cho sản phẩm này.', 1, 1, '00033.png'),
('00034', 'LHH03', 'DVT01', 'Sườn Nướng Muối Ớt (Túi 300G)', 'Chưa có mô tả cho sản phẩm này.', 1, 1, '00034.png'),
('00035', 'LHH04', 'DVT03', 'Bao Tử Cá Basa (250gr)', 'Bao Tử Cá Basa làm sạch, dai, giòn, hấp thụ gia vị tốt, dùng cho nhiều món hấp dẫn.', 1, 0, '00035.png'),
('00036', 'LHH04', 'DVT03', 'Cá Bạc Má Size 5-8 Con/Kg - 500gr', 'Cá bạc má tươi, vị béo đặc trưng, thịt mềm ngọt tự nhiên, dễ chế biến.', 1, 0, '00036.png'),
('00037', 'LHH04', 'DVT03', 'Cá Basa - 500 gr', 'Cá basa da trơn, thịt thơm ngon, giàu dinh dưỡng, phổ biến tại châu Á.', 1, 0, '00037.png'),
('00038', 'LHH04', 'DVT03', 'Cá Bớp Cắt Khoanh - 500gr', 'Cá bớp thịt béo ngọt, chắc, dùng cho lẩu, kho, chiên đều ngon.', 1, 0, '00038.png'),
('00039', 'LHH04', 'DVT03', 'Cá Chẽm Quy Nhơn (Size 0.8-1.2) - 0,8kg', 'Cá Chẽm Quy Nhơn tươi, thịt trắng, ngọt, giàu dinh dưỡng từ vùng biển Quy Nhơn.', 1, 0, '00039.png'),
('00040', 'LHH04', 'DVT03', 'Cá Chim Đen Size 2-4 Con/Kg - 500gr', 'Cá chim đen thịt ngon, giá trị kinh tế cao, được ưa chuộng nhất trong các loại cá chim.', 1, 0, '00040.png'),
('00041', 'LHH04', 'DVT03', 'Cá Chim Trắng Lớn Size 0.8-1.0', 'Cá Chim Trắng Lớn cao cấp, thịt trắng, săn chắc, vị ngọt tự nhiên từ vùng biển miền Nam.', 1, 0, '00041.png'),
('00042', 'LHH04', 'DVT03', 'Cá Chim Trắng Nhỏ (Size 0.5-0.7) - 0,5kg', 'Cá chim trắng thịt nhiều nước, thơm ngon, chất dinh dưỡng cao.', 1, 0, '00042.png'),
('00043', 'LHH04', 'DVT03', 'Cá Dìa Size 2-4Con/Kg - 500gr', 'Cá dìa bông thân dẹp tròn, thịt ngọt, béo, dai, thơm ngon.', 1, 0, '00043.png'),
('00044', 'LHH04', 'DVT03', 'Cá Diêu Hồng - 500 gr', 'Cá diêu hồng thịt trắng sạch, thớ thịt chắc, ít xương, giàu dinh dưỡng.', 1, 0, '00044.png'),
('00045', 'LHH04', 'DVT03', 'Cá Diêu Hồng - 800 gr', 'Cá diêu hồng hàm lượng mỡ cao, ăn béo, thơm ngon.', 1, 0, '00045.png'),
('00046', 'LHH04', 'DVT01', 'Cá Hồi Tươi Fille Kome (300gr)', 'Cá hồi tươi đã fillet, thương hiệu Kome, giữ nguyên độ tươi và giá trị dinh dưỡng.', 1, 0, '00046.png'),
('00047', 'LHH04', 'DVT03', 'Cá Hồi Tươi Fillet - 300 gr', 'Cá hồi tươi đã fillet, bỏ xương và da, chỉ còn phần thịt tươi ngon, sạch sẽ.', 1, 0, '00047.png'),
('00048', 'LHH04', 'DVT02', 'Cá Hú - 1 Kg', 'Cá hú giàu protein và dinh dưỡng, được ưa chuộng trong ẩm thực Việt Nam.', 1, 0, '00048.png'),
('00049', 'LHH04', 'DVT03', 'Cá Kèo - 400gr', 'Cá kèo họ cá bống trắng, vị ngọt mặn, tính bình, tốt cho sức khỏe.', 1, 0, '00049.png'),
('00050', 'LHH05', 'DVT03', 'Bạch Tuộc Sz 10-12 (500g)', 'Bạch tuộc size 10-12 con/kg, tươi ngon, thích hợp nướng, xào, lẩu.', 1, 0, '00050.png'),
('00051', 'LHH05', 'DVT03', 'Ếch Size 7-10 Con/Kg - 400gr', 'Thịt ếch tươi trắng ngà, dai, ít mỡ, thơm ngon, đảm bảo vệ sinh an toàn thực phẩm.', 1, 0, '00051.png'),
('00052', 'LHH05', 'DVT03', 'Lươn - 400 gr', 'Lươn giàu dinh dưỡng, hỗ trợ bồi bổ sức khỏe, khu phong trừ thấp theo Đông Y.', 1, 0, '00052.png'),
('00053', 'LHH05', 'DVT03', 'Mực Nang Sz 20-25 (500gr)', 'Mực nang tươi, ngọt tự nhiên, dùng cho các món hấp, xào chua ngọt, nướng muối ớt.', 1, 0, '00053.png'),
('00054', 'LHH05', 'DVT01', 'Nghêu Sạch Lenger (Hộp 0.6kg)', 'Nghêu sạch Lenger, không cát, không vi khuẩn, giữ vị ngọt tự nhiên.', 1, 0, '00054.png'),
('00055', 'LHH06', 'DVT02', 'Bắp Cải Trắng - 800 gr', 'Bắp cải trắng tươi, canh tác theo tiêu chuẩn an toàn.', 1, 0, '00055.png'),
('00056', 'LHH06', 'DVT02', 'Bắp Cải Trái Tim - 800 gr', 'Bắp cải trái tim (bắp cải non), lõi ngọt, dùng làm salad hoặc xào.', 1, 0, '00056.png'),
('00057', 'LHH06', 'DVT02', 'Bắp Cải Tím - 800gr', 'Bắp cải tím giàu vitamin, thích hợp làm salad trộn.', 1, 0, '00057.png'),
('00058', 'LHH06', 'DVT01', 'Bắp Chuối Bào (Gói 200g)', 'Bắp chuối bào sẵn, tiện lợi cho các món gỏi hoặc canh chua.', 1, 0, '00058.png'),
('00059', 'LHH06', 'DVT01', 'Bắp Mỹ (Trái)', 'Bắp mỹ tươi, hạt mẩy, ngọt.', 1, 0, '00059.png'),
('00060', 'LHH06', 'DVT01', 'Bắp Nếp (Trái)', 'Bắp nếp dẻo, thơm, dùng để luộc hoặc nướng.', 1, 0, '00060.png'),
('00061', 'LHH06', 'DVT01', 'Bắp Non (Khay 200g)', 'Bắp non (ngô bao tử) sạch, dùng xào thập cẩm hoặc nấu lẩu.', 1, 0, '00061.png'),
('00062', 'LHH06', 'DVT02', 'Bầu - 500 gr', 'Bầu sao tươi, non, dùng nấu canh tôm hoặc luộc.', 1, 0, '00062.png'),
('00063', 'LHH06', 'DVT02', 'Bí Đao - 500 gr', 'Bí đao (bí xanh) dùng nấu canh hoặc làm trà bí đao.', 1, 0, '00063.png'),
('00064', 'LHH06', 'DVT02', 'Bí Đỏ Hồ Lô - 700 gr', 'Bí đỏ hồ lô dẻo, bùi, dùng nấu canh hoặc nấu sữa.', 1, 0, '00064.png'),
('00065', 'LHH06', 'DVT01', 'Bí Đỏ Tròn (400g)', 'Bí đỏ tròn cắt sẵn, tiện lợi.', 1, 0, '00065.png'),
('00066', 'LHH06', 'DVT01', 'Bí Hạt Đậu (400g)', 'Bí hạt đậu (Butternut Squash) dẻo, thơm, dùng làm súp.', 1, 0, '00066.png'),
('00067', 'LHH06', 'DVT02', 'Bí Ngô Non (500g)', 'Bí ngô non (nụ bí) nguyên trái, dùng để xào tỏi hoặc luộc.', 1, 0, '00067.png'),
('00068', 'LHH06', 'DVT02', 'Bí Ngòi Xanh (500g)', 'Bí ngòi xanh (Zucchini) tươi, dùng nhúng lẩu hoặc xào.', 1, 0, '00068.png'),
('00069', 'LHH06', 'DVT01', 'Bông Cải Trắng', 'Bông cải trắng (Súp lơ trắng) tươi, an toàn.', 1, 0, '00069.png'),
('00070', 'LHH06', 'DVT02', 'Bông Cải Xanh - 400 gr', 'Bông cải xanh (Broccoli) giàu dinh dưỡng.', 1, 0, '00070.png'),
('00071', 'LHH06', 'DVT01', 'Bông Cải Xanh Baby (Khay 300g)', 'Bông cải xanh baby (Cải rổ) xào tỏi.', 1, 0, '00071.png'),
('00072', 'LHH06', 'DVT01', 'Cà Cherry Đỏ (Hộp 250g)', 'Cà chua cherry đỏ, mọng nước, vị chua ngọt.', 1, 0, '00072.png'),
('00073', 'LHH06', 'DVT01', 'Cà Cherry Socola (Hộp 250g)', 'Cà chua cherry socola, ngọt đậm, dùng ăn sống.', 1, 0, '00073.png'),
('00074', 'LHH06', 'DVT01', 'Cà Chua Beef (Khay 500g)', 'Cà chua beef trái to, nhiều thịt, ít hạt.', 1, 0, '00074.png'),
('00075', 'LHH06', 'DVT01', 'Cà Chua Rita (Khay 500g)', 'Cà chua Rita (giống cà chua chùm), dùng nấu canh hoặc xào.', 1, 0, '00075.png'),
('00076', 'LHH07', 'DVT01', 'Thì Là (Gói 100g)', 'Thì là tươi, lá xanh mềm, mùi thơm dịu, không thể thiếu trong các món canh cá, cháo, lẩu.', 1, 0, '00076.png'),
('00077', 'LHH07', 'DVT01', 'Xà Lách Xoong (Gói 300g)', 'Xà Lách Xoong Tươi, lá non giòn mát, vị cay nhẹ, giàu vitamin A, C và khoáng chất.', 1, 0, '00077.png'),
('00078', 'LHH07', 'DVT01', 'Rau Tiến Vua (Gói 100g)', 'Rau Tiến Vua đặc sản miền Bắc, thân giòn sần sật đặc trưng, chế biến được nhiều món hấp dẫn.', 1, 0, '00078.png'),
('00079', 'LHH07', 'DVT01', 'Bông So Đũa (Khay 100g)', 'Bông so đũa tươi, thường dùng nấu canh chua hoặc luộc chấm mắm.', 1, 0, '00079.png'),
('00080', 'LHH07', 'DVT01', 'Đọt Bầu (Khay 200g)', 'Đọt bầu non (ngọn bầu) tươi, dùng xào tỏi hoặc luộc.', 1, 0, '00080.png'),
('00081', 'LHH07', 'DVT01', 'Bí Nụ (Khay 500g)', 'Bí nụ (bông bí đực) non, dùng xào tỏi hoặc nấu canh, nhúng lẩu.', 1, 0, '00081.png'),
('00082', 'LHH07', 'DVT02', 'Cần Tây Lớn - 800 gr', 'Cần tây mùi vị đặc biệt, kết hợp với thịt bò tốt, ngăn ngừa viêm nhiễm, cải thiện huyết áp.', 1, 0, '00082.png'),
('00083', 'LHH07', 'DVT02', 'Cải Thảo - 800 gr', 'Cải thảo mùa vụ, phát triển trong thời tiết lạnh, lớp lá bao phủ nhau như bắp cải.', 1, 0, '00083.png'),
('00084', 'LHH07', 'DVT01', 'Cải Thìa Thủy Canh (Gói 300g)', 'Cải thìa trồng thủy canh, đảm bảo tiêu chuẩn sạch, an toàn.', 1, 0, '00084.png'),
('00085', 'LHH07', 'DVT01', 'Cải Ngọt Thủy Canh (Gói 300g)', 'Cải ngọt trồng thủy canh, thân non, vị ngọt thanh, dùng xào hoặc nấu canh.', 1, 0, '00085.png'),
('00086', 'LHH07', 'DVT01', 'Xà Lách Lolo Xanh Thủy Canh (300g)', 'Xà lách Lolo xanh trồng thủy canh, lá giòn, tươi, dùng cho các món salad.', 1, 0, '00086.png'),
('00087', 'LHH07', 'DVT01', 'Cải Bó Xôi Thủy Canh (Gói 250g)', 'Cải bó xôi (spinach) trồng thủy canh, giàu sắt và vitamin.', 1, 0, '00087.png'),
('00088', 'LHH07', 'DVT01', 'Cải Bẹ Xanh Thủy Canh (Gói 300g)', 'Cải bẹ xanh thủy canh, vị nồng đặc trưng, dùng nấu canh hoặc nhúng lẩu.', 1, 0, '00088.png'),
('00089', 'LHH07', 'DVT01', 'Cải Bẹ Trắng (Gói 300g)', 'Cải bẹ trắng (cải chíp) tươi non, dùng xào nấm hoặc luộc.', 1, 0, '00089.png'),
('00090', 'LHH07', 'DVT01', 'Rau Càng Cua (Gói 300g)', 'Rau càng cua sạch, vị chua nhẹ, giòn, dùng làm gỏi với thịt bò hoặc trứng.', 1, 0, '00090.png'),
('00091', 'LHH07', 'DVT01', 'Rau Đắng (Gói 300g)', 'Rau đắng đất, dùng nấu canh cá hoặc ăn lẩu mắm.', 1, 0, '00091.png'),
('00092', 'LHH07', 'DVT01', 'Rau Nhút (Gói 300g)', 'Rau nhút (rau rút) đã nhặt, dùng nấu canh chua hoặc lẩu.', 1, 0, '00092.png'),
('00093', 'LHH07', 'DVT01', 'Bạc Hà (Khay 300g)', 'Bạc hà (dọc mùng) đã tước vỏ, thái vát, tiện lợi cho món canh chua.', 1, 0, '00093.png'),
('00094', 'LHH07', 'DVT01', 'Cải Bẹ Xanh (Gói 300g)', 'Cải bẹ xanh (cải đắng) loại thường, dùng nấu canh thịt bằm.', 1, 0, '00094.png'),
('00095', 'LHH07', 'DVT01', 'Cải Bẹ Xanh Baby (Gói 300g)', 'Cải bẹ xanh non (cải mầm), dùng nhúng lẩu hoặc xào.', 1, 0, '00095.png'),
('00096', 'LHH07', 'DVT01', 'Cải Bó Xôi (Gói 250g)', 'Cải bó xôi (spinach) trồng đất, giàu sắt, dùng nấu canh.', 1, 0, '00096.png'),
('00097', 'LHH07', 'DVT01', 'Cải Dún (Gói 300g)', 'Cải dún (cải ngọt nhăn) dùng xào tỏi hoặc nấu canh.', 1, 0, '00097.png'),
('00098', 'LHH07', 'DVT01', 'Cải Ngồng (Gói 300g)', 'Cải ngồng (cải làn) tươi, phần bông non, xào dầu hào.', 1, 0, '00098.png'),
('00099', 'LHH07', 'DVT01', 'Cải Ngồng Baby (Gói 300g)', 'Cải ngồng baby, thân non, vị ngọt, xào tỏi.', 1, 0, '00099.png'),
('00100', 'LHH09', 'DVT02', 'Bưởi Da Xanh (Trái 1.2-1.5kg)', 'Bưởi da xanh ruột hồng, vị ngọt thanh, mọng nước, không hạt hoặc ít hạt.', 1, 0, '00100.png'),
('00101', 'LHH09', 'DVT02', 'Xoài Cát Hòa Lộc (Kg)', 'Xoài Cát Hòa Lộc chín vàng, thịt mịn, dẻo, thơm và rất ngọt.', 1, 0, '00101.png'),
('00102', 'LHH09', 'DVT02', 'Thanh Long Ruột Đỏ (Kg)', 'Thanh long ruột đỏ, vỏ mỏng, vị ngọt đậm hơn thanh long trắng.', 1, 0, '00102.png'),
('00103', 'LHH09', 'DVT02', 'Cam Sành (Kg)', 'Cam sành Tiền Giang, mọng nước, vị chua ngọt, giàu vitamin C.', 1, 0, '00103.png'),
('00104', 'LHH09', 'DVT02', 'Ổi Nữ Hoàng (Kg)', 'Ổi nữ hoàng giòn, ngọt, ít hạt, thơm đặc trưng.', 1, 0, '00104.png'),
('00105', 'LHH09', 'DVT02', 'Chôm Chôm Nhãn (Kg)', 'Chôm chôm nhãn cùi dày, tróc ráo, giòn và rất ngọt.', 1, 0, '00105.png'),
('00106', 'LHH09', 'DVT02', 'Măng Cụt (Kg)', 'Măng cụt Lái Thiêu, múi trắng, vị chua ngọt thanh mát.', 1, 0, '00106.png'),
('00107', 'LHH09', 'DVT02', 'Sầu Riêng Ri 6 (Kg)', 'Sầu riêng Ri 6 cơm vàng, hạt lép, vị béo, ngọt đậm.', 1, 0, '00107.png'),
('00108', 'LHH09', 'DVT02', 'Dưa Hấu Không Hạt (Kg)', 'Dưa hấu ruột đỏ, không hạt, vỏ mỏng, ngọt mát.', 1, 0, '00108.png'),
('00109', 'LHH09', 'DVT02', 'Mít Thái (Kg)', 'Mít Thái múi to, dày, giòn và ngọt.', 1, 0, '00109.png'),
('00110', 'LHH09', 'DVT02', 'Vải Thiều (Kg)', 'Vải thiều Lục Ngạn, hạt nhỏ, cùi dày, mọng nước.', 1, 0, '00110.png'),
('00111', 'LHH09', 'DVT02', 'Nhãn Lồng Hưng Yên (Kg)', 'Nhãn lồng Hưng Yên quả to, cùi dày, thơm, ngọt.', 1, 0, '00111.png'),
('00112', 'LHH09', 'DVT02', 'Đu Đủ (Kg)', 'Đu đủ ruột cam, vị ngọt, mềm, tốt cho tiêu hóa.', 1, 0, '00112.png'),
('00113', 'LHH09', 'DVT02', 'Bơ 034 (Kg)', 'Bơ 034 vỏ xanh, dẻo, béo, hạt nhỏ.', 1, 0, '00113.png'),
('00114', 'LHH09', 'DVT02', 'Chuối Cau (Nải)', 'Chuối cau chín tự nhiên, quả nhỏ, thơm, ngọt.', 1, 0, '00114.png'),
('00115', 'LHH10', 'DVT02', 'Táo Envy New Zealand (Kg)', 'Táo Envy NZ size 30, vỏ đỏ, giòn, ngọt đậm và rất thơm.', 1, 0, '00115.png'),
('00116', 'LHH10', 'DVT02', 'Nho Đen Không Hạt Mỹ (Kg)', 'Nho đen không hạt Mỹ, vỏ mỏng, giòn, ngọt thanh.', 1, 0, '00116.png'),
('00117', 'LHH10', 'DVT01', 'Kiwi Vàng New Zealand (Hộp 500g)', 'Kiwi vàng Zespri, vị ngọt, giàu Vitamin C.', 1, 0, '00117.png'),
('00118', 'LHH10', 'DVT02', 'Cherry Đỏ Mỹ (Kg)', 'Cherry đỏ size 9.0, quả to, mọng nước, ngọt đậm.', 1, 0, '00118.png'),
('00119', 'LHH10', 'DVT01', 'Việt Quất (Hộp 125g)', 'Việt quất (Blueberry) nhập khẩu, giàu chất chống oxy hóa.', 1, 0, '00119.png'),
('00120', 'LHH10', 'DVT02', 'Lê Hàn Quốc (Trái)', 'Lê Hàn Quốc quả to, giòn, mọng nước, vị ngọt mát.', 1, 0, '00120.png'),
('00121', 'LHH10', 'DVT01', 'Dâu Tây Hàn Quốc (Hộp 330g)', 'Dâu tây Hàn Quốc, quả to, thơm, vị ngọt.', 1, 0, '00121.png'),
('00122', 'LHH10', 'DVT02', 'Táo Gala Mỹ (Kg)', 'Táo Gala Mỹ, vỏ sọc đỏ vàng, giòn, vị ngọt nhẹ.', 1, 0, '00122.png'),
('00123', 'LHH10', 'DVT02', 'Lựu Peru (Kg)', 'Lựu Peru hạt mềm, ruột đỏ, mọng nước, vị ngọt.', 1, 0, '00123.png'),
('00124', 'LHH10', 'DVT02', 'Cam Vàng Navel Mỹ (Kg)', 'Cam Navel không hạt, vỏ vàng, mọng nước, ngọt.', 1, 0, '00124.png'),
('00126', 'LHH10', 'DVT01', 'Táo Rockit New Zealand (Ống 4 trái)', 'Táo Rockit NZ, size nhỏ, giòn tan, ngọt đậm, đóng ống tiện lợi.', 1, 0, '00126.png'),
('00129', 'LHH10', 'DVT02', 'Táo đỏ Granny Smith (Kg)', 'Táo xanh Granny Smith, giòn, vị chua đậm, dùng làm nước ép.', 1, 0, '00129.png'),
('00130', 'LHH02', 'DVT02', 'Canh chua lương 500gr', 'An toàn, bổ dưỡng.', 1, 1, '00130.png');

-- --------------------------------------------------------

--
-- Table structure for table `khuyen_mai`
--

CREATE TABLE `khuyen_mai` (
  `id_km` varchar(10) NOT NULL,
  `ten_km` varchar(100) NOT NULL,
  `phan_tram_km` decimal(10,2) NOT NULL,
  `ngay_bd_km` datetime NOT NULL,
  `ngay_kt_km` datetime NOT NULL,
  `trang_thai_km` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `khuyen_mai`
--

INSERT INTO `khuyen_mai` (`id_km`, `ten_km`, `phan_tram_km`, `ngay_bd_km`, `ngay_kt_km`, `trang_thai_km`) VALUES
('KM001', 'Giảm 10% cho rau lá', 10.00, '2025-12-09 11:05:00', '2025-12-13 11:14:00', 1),
('KM002', 'Giảm 50%', 50.00, '2025-12-02 11:16:00', '2025-12-03 11:16:00', 2),
('KM003', 'Giảm 15% cho canh xào', 15.00, '2025-12-10 01:22:00', '2025-12-18 01:22:00', 1),
('KM004', 'Giảm 20% cho chiên nướng', 20.00, '2025-12-10 14:24:00', '2025-12-27 14:22:00', 1),
('KM005', 'Giảm 5% toàn bộ sản phẩm', 5.00, '2026-01-01 00:00:00', '2026-01-31 23:59:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `loai_hang_hoa`
--

CREATE TABLE `loai_hang_hoa` (
  `id_loai2` varchar(5) NOT NULL,
  `id_dm` varchar(5) NOT NULL,
  `ten_loai` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loai_hang_hoa`
--

INSERT INTO `loai_hang_hoa` (`id_loai2`, `id_dm`, `ten_loai`) VALUES
('LHH01', 'DM01', 'Món mặn'),
('LHH02', 'DM01', 'Canh xào'),
('LHH03', 'DM01', 'Chiên, Nướng'),
('LHH04', 'DM02', 'Cá'),
('LHH05', 'DM02', 'Tôm, Cua, Mực, khác...'),
('LHH06', 'DM03', 'Rau ăn củ'),
('LHH07', 'DM03', 'Rau lá'),
('LHH09', 'DM04', 'Trái cây nội địa'),
('LHH10', 'DM04', 'Trái cây nhập khẩu'),
('LHH11', 'DM01', 'Hấp');

-- --------------------------------------------------------

--
-- Table structure for table `lo_hang`
--

CREATE TABLE `lo_hang` (
  `id_lo` varchar(10) NOT NULL,
  `id_hh` varchar(10) NOT NULL,
  `id_km` varchar(10) DEFAULT NULL,
  `id_trang_thai_lo` varchar(5) NOT NULL,
  `hsd_lo` datetime NOT NULL,
  `so_luong_nhap` int(11) NOT NULL,
  `so_luong_con_lai` int(11) NOT NULL
) ;

--
-- Dumping data for table `lo_hang`
--

INSERT INTO `lo_hang` (`id_lo`, `id_hh`, `id_km`, `id_trang_thai_lo`, `hsd_lo`, `so_luong_nhap`, `so_luong_con_lai`) VALUES
('LO00001A', '00001', 'KM002', 'TTL01', '2025-11-10 23:59:59', 50, 50),
('LO00002A', '00002', 'KM002', 'TTL01', '2025-11-10 23:59:59', 50, 50),
('LO00003A', '00003', 'KM002', 'TTL01', '2025-11-10 23:59:59', 50, 50),
('LO00004A', '00004', 'KM002', 'TTL01', '2025-11-10 23:59:59', 50, 50),
('LO00005A', '00005', 'KM002', 'TTL01', '2025-11-10 23:59:59', 50, 50),
('LO00014A', '00014', 'KM002', 'TTL01', '2025-11-10 23:59:59', 51, 51),
('LO00109A', '00109', NULL, 'TTL01', '2025-11-25 23:59:59', 103, 103),
('LO00116A', '00116', NULL, 'TTL01', '2025-11-25 23:59:59', 110, 110),
('LO00120A', '00120', NULL, 'TTL01', '2025-11-25 23:59:59', 106, 106),
('LO00123A', '00123', NULL, 'TTL01', '2025-11-25 23:59:59', 107, 107);

-- --------------------------------------------------------

--
-- Table structure for table `nguoi_dung`
--

CREATE TABLE `nguoi_dung` (
  `id_nd` varchar(5) NOT NULL,
  `phan_quyen_tk` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`id_nd`, `phan_quyen_tk`) VALUES
('AD', 'Admin'),
('KH', 'Khách hàng'),
('KT', 'Kế toán'),
('NV', 'Nhân viên'),
('QL', 'Quản lý kho');

-- --------------------------------------------------------

--
-- Table structure for table `nguyen_lieu`
--

CREATE TABLE `nguyen_lieu` (
  `id_nl` varchar(5) NOT NULL,
  `ten_nl` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nguyen_lieu`
--

INSERT INTO `nguyen_lieu` (`id_nl`, `ten_nl`) VALUES
('NL001', 'Thịt heo ba rọi'),
('NL002', 'Cá basa phi lê'),
('NL003', 'Tôm thẻ chân trắng'),
('NL004', 'Sốt xào GreenMeal'),
('NL005', 'Tỏi xay');

-- --------------------------------------------------------

--
-- Table structure for table `nha_cung_cap`
--

CREATE TABLE `nha_cung_cap` (
  `id_ncc` varchar(5) NOT NULL,
  `ten_ncc` varchar(100) NOT NULL,
  `sdt_ncc` char(15) DEFAULT '0',
  `dia_chi_ncc` varchar(255) DEFAULT '0',
  `email_ncc` varchar(100) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nha_cung_cap`
--

INSERT INTO `nha_cung_cap` (`id_ncc`, `ten_ncc`, `sdt_ncc`, `dia_chi_ncc`, `email_ncc`) VALUES
('NCC01', 'Công ty Cổ phần Chăn nuôi C.P. Việt Nam', '02513836251', 'Số 2, Đường 2A, KCN Biên Hòa 2, P. Long Bình, TP. Biên Hòa, Đồng Nai', 'cpvietnam@cp.com.vn'),
('NCC02', 'Công ty Cổ phần Việt Nam Kỹ nghệ Súc sản (VISSAN)', '19001960', '420 Nơ Trang Long, P.13, Q. Bình Thạnh, TP. HCM', 'vissan@vissan.com.vn'),
('NCC03', 'Công ty TNHH Dalatroi (Rau củ Đà Lạt)', '02633828999', 'Phường 12, TP. Đà Lạt, Tỉnh Lâm Đồng', 'info@dalatroi.com'),
('NCC04', 'Tập đoàn Thủy sản Minh Phú', '02903838262', 'Khu Công nghiệp Phường 8, TP. Cà Mau, Tỉnh Cà Mau', 'info@minhphu.com'),
('NCC05', 'Công ty TNHH Chicken Talk', '0999999999', 'Ninh Kiều, Cần Thơ', 'ctalk@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `phieu_nhap`
--

CREATE TABLE `phieu_nhap` (
  `id_pn` varchar(10) NOT NULL,
  `id_ncc` varchar(5) DEFAULT NULL,
  `ngay_lap_phieu_nhap` datetime NOT NULL,
  `tong_tien_nhap` decimal(10,2) NOT NULL
) ;

--
-- Dumping data for table `phieu_nhap`
--

INSERT INTO `phieu_nhap` (`id_pn`, `id_ncc`, `ngay_lap_phieu_nhap`, `tong_tien_nhap`) VALUES
('PN0000001', 'NCC01', '2025-11-24 07:01:00', 2120000.00),
('PN0000002', 'NCC03', '2025-12-10 06:10:00', 550000.00),
('PN0000003', 'NCC03', '2025-12-10 08:24:00', 100000.00),
('PN0000004', 'NCC02', '2025-12-15 09:00:00', 800000.00),
('PN0000005', 'NCC04', '2025-12-20 14:30:00', 1200000.00);

-- --------------------------------------------------------

--
-- Table structure for table `phuong_thuc_thanh_toan`
--

CREATE TABLE `phuong_thuc_thanh_toan` (
  `id_pttt` varchar(5) NOT NULL,
  `ten_pttt` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `phuong_thuc_thanh_toan`
--

INSERT INTO `phuong_thuc_thanh_toan` (`id_pttt`, `ten_pttt`) VALUES
('PTTT1', 'Thanh toán khi nhận hàng (COD)'),
('PTTT2', 'Chuyển khoản ngân hàng'),
('PTTT3', 'Thanh toán qua ví MoMo'),
('PTTT4', 'Thanh toán qua ZaloPay'),
('PTTT5', 'Thanh toán qua thẻ tín dụng/ghi nợ');

-- --------------------------------------------------------

--
-- Table structure for table `tai_khoan`
--

CREATE TABLE `tai_khoan` (
  `id_tk` char(15) NOT NULL,
  `id_nd` varchar(5) DEFAULT NULL,
  `ho_ten` varchar(50) NOT NULL DEFAULT '0',
  `gioi_tinh` enum('Nam','Nữ','Khác') DEFAULT NULL,
  `sdt_tk` char(14) NOT NULL DEFAULT '',
  `email_tk` varchar(100) DEFAULT NULL,
  `mat_khau_tk` varchar(255) NOT NULL,
  `ngay_gio_tao_tk` datetime NOT NULL DEFAULT current_timestamp(),
  `dia_chi_avt` varchar(255) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tai_khoan`
--

INSERT INTO `tai_khoan` (`id_tk`, `id_nd`, `ho_ten`, `gioi_tinh`, `sdt_tk`, `email_tk`, `mat_khau_tk`, `ngay_gio_tao_tk`, `dia_chi_avt`) VALUES
('TK00000000000K2', 'KH', 'Trần Thị Bình', 'Nữ', '0902222333', 'binh@gmail.com', '$2y$10$abcdefghijklmnopqrstuuVGZW1K2M3N4O5P6Q7R8S9T0UVWXYZ', '2025-12-05 10:30:00', NULL),
('TK00000000000KH', 'KH', 'Nguyễn Văn An', 'Nam', '0901111222', 'an@gmail.com', '$2y$10$abcdefghijklmnopqrstuuVGZW1K2M3N4O5P6Q7R8S9T0UVWXYZ', '2025-12-01 09:00:00', NULL),
('TK00000000000QL', 'QL', 'Lê Văn Cường', 'Nam', '0903333444', 'cuong@gmail.com', '$2y$10$abcdefghijklmnopqrstuuVGZW1K2M3N4O5P6Q7R8S9T0UVWXYZ', '2025-11-20 08:00:00', NULL),
('TK69451a062e5b5', 'AD', 'Khánh', 'Nữ', '0123456789', 'a@gmail.com', 'cfa2c70dbd143e6ed814270530a9ce7c', '2025-12-19 16:25:26', NULL),
('TK69a69ba3e0319', 'AD', 'Hoa Mai', 'Nữ', '0373205595', 'mai@gmail.com', '$2y$10$/fxH2z/t8WMy.KSjmpEONu.nDxrxI6gN2mfGSMlhdV1fvuK9HBNnC', '2026-03-03 15:28:20', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `thanh_phan`
--

CREATE TABLE `thanh_phan` (
  `id_hh` varchar(10) NOT NULL,
  `id_nl` varchar(5) NOT NULL,
  `so_luong_nl_trong_hh` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `thanh_phan`
--

INSERT INTO `thanh_phan` (`id_hh`, `id_nl`, `so_luong_nl_trong_hh`) VALUES
('00001', 'NL001', 300),
('00001', 'NL005', 10),
('00003', 'NL001', 200),
('00003', 'NL004', 50),
('00007', 'NL001', 300);

-- --------------------------------------------------------

--
-- Table structure for table `thoi_diem`
--

CREATE TABLE `thoi_diem` (
  `id_td` varchar(5) NOT NULL,
  `ngay_bd_gia_ban` datetime DEFAULT NULL,
  `ngay_kt_gia_ban` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `thoi_diem`
--

INSERT INTO `thoi_diem` (`id_td`, `ngay_bd_gia_ban`, `ngay_kt_gia_ban`) VALUES
('TD001', '2025-12-31 00:00:00', '2026-04-30 00:00:00'),
('TD002', '2026-01-01 00:00:00', '2026-10-30 00:00:00'),
('TD003', '2025-11-01 00:00:00', '2026-01-01 00:00:00'),
('TD004', '2026-01-02 00:00:00', '2026-01-31 00:00:00'),
('TD005', '2026-02-01 00:00:00', '2026-06-30 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `trang_thai_lo_hang`
--

CREATE TABLE `trang_thai_lo_hang` (
  `id_trang_thai_lo` varchar(5) NOT NULL,
  `ten_trang_thai_lo` varchar(255) NOT NULL,
  `mo_ta` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `trang_thai_lo_hang`
--

INSERT INTO `trang_thai_lo_hang` (`id_trang_thai_lo`, `ten_trang_thai_lo`, `mo_ta`) VALUES
('TTL01', 'Còn hàng', 'Lô hàng còn trong kho, đủ điều kiện bán'),
('TTL02', 'Sắp hết', 'Lô hàng còn ít, cần nhập thêm'),
('TTL03', 'Hết hàng', 'Lô hàng đã bán hết'),
('TTL04', 'Sắp hết hạn', 'Lô hàng sẽ hết hạn sử dụng trong vòng 7 ngày'),
('TTL05', 'Hết hạn / Hủy bỏ', 'Lô hàng đã hết hạn sử dụng hoặc bị hủy');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chi_tiet_don_hang`
--
ALTER TABLE `chi_tiet_don_hang`
  ADD PRIMARY KEY (`id_dh`,`id_hh`,`id_lo`),
  ADD KEY `id_hh` (`id_hh`),
  ADD KEY `id_lo` (`id_lo`);

--
-- Indexes for table `chi_tiet_gio_hang`
--
ALTER TABLE `chi_tiet_gio_hang`
  ADD PRIMARY KEY (`id_gh`,`id_hh`),
  ADD KEY `id_hh` (`id_hh`);

--
-- Indexes for table `chi_tiet_phieu_nhap`
--
ALTER TABLE `chi_tiet_phieu_nhap`
  ADD PRIMARY KEY (`id_pn`,`id_lo`),
  ADD KEY `id_lo` (`id_lo`);

--
-- Indexes for table `danh_muc`
--
ALTER TABLE `danh_muc`
  ADD PRIMARY KEY (`id_dm`);

--
-- Indexes for table `danh_muc_trang_thai`
--
ALTER TABLE `danh_muc_trang_thai`
  ADD PRIMARY KEY (`id_ttd`);

--
-- Indexes for table `dia_chi_giao_hang`
--
ALTER TABLE `dia_chi_giao_hang`
  ADD PRIMARY KEY (`id_dc`),
  ADD KEY `id_tk` (`id_tk`);

--
-- Indexes for table `don_hang`
--
ALTER TABLE `don_hang`
  ADD PRIMARY KEY (`id_dh`),
  ADD KEY `id_tk` (`id_tk`),
  ADD KEY `id_pttt` (`id_pttt`),
  ADD KEY `idx_don_hang_time` (`ngay_gio_tao_don`),
  ADD KEY `fk_don_hang_dia_chi` (`id_dc`);

--
-- Indexes for table `don_hang_hien_tai`
--
ALTER TABLE `don_hang_hien_tai`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_dh` (`id_dh`),
  ADD KEY `id_ttd` (`id_ttd`);

--
-- Indexes for table `dvt`
--
ALTER TABLE `dvt`
  ADD PRIMARY KEY (`id_dvt`);

--
-- Indexes for table `gia_ban_hien_tai`
--
ALTER TABLE `gia_ban_hien_tai`
  ADD PRIMARY KEY (`id_lo`,`id_td`),
  ADD KEY `id_td` (`id_td`);

--
-- Indexes for table `gio_hang`
--
ALTER TABLE `gio_hang`
  ADD PRIMARY KEY (`id_gh`),
  ADD UNIQUE KEY `id_tk` (`id_tk`);

--
-- Indexes for table `hang_hoa`
--
ALTER TABLE `hang_hoa`
  ADD PRIMARY KEY (`id_hh`),
  ADD KEY `id_loai2` (`id_loai2`),
  ADD KEY `id_dvt` (`id_dvt`);
ALTER TABLE `hang_hoa` ADD FULLTEXT KEY `ten_hh` (`ten_hh`,`mo_ta_hh`);

--
-- Indexes for table `khuyen_mai`
--
ALTER TABLE `khuyen_mai`
  ADD PRIMARY KEY (`id_km`);

--
-- Indexes for table `loai_hang_hoa`
--
ALTER TABLE `loai_hang_hoa`
  ADD PRIMARY KEY (`id_loai2`),
  ADD KEY `id_dm` (`id_dm`);

--
-- Indexes for table `lo_hang`
--
ALTER TABLE `lo_hang`
  ADD PRIMARY KEY (`id_lo`),
  ADD KEY `id_km` (`id_km`),
  ADD KEY `id_trang_thai_lo` (`id_trang_thai_lo`),
  ADD KEY `idx_lo_hang_fefo` (`id_hh`,`hsd_lo`,`so_luong_con_lai`);

--
-- Indexes for table `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  ADD PRIMARY KEY (`id_nd`);

--
-- Indexes for table `nguyen_lieu`
--
ALTER TABLE `nguyen_lieu`
  ADD PRIMARY KEY (`id_nl`);

--
-- Indexes for table `nha_cung_cap`
--
ALTER TABLE `nha_cung_cap`
  ADD PRIMARY KEY (`id_ncc`);

--
-- Indexes for table `phieu_nhap`
--
ALTER TABLE `phieu_nhap`
  ADD PRIMARY KEY (`id_pn`),
  ADD KEY `id_ncc` (`id_ncc`);

--
-- Indexes for table `phuong_thuc_thanh_toan`
--
ALTER TABLE `phuong_thuc_thanh_toan`
  ADD PRIMARY KEY (`id_pttt`);

--
-- Indexes for table `tai_khoan`
--
ALTER TABLE `tai_khoan`
  ADD PRIMARY KEY (`id_tk`),
  ADD KEY `id_nd` (`id_nd`);

--
-- Indexes for table `thanh_phan`
--
ALTER TABLE `thanh_phan`
  ADD PRIMARY KEY (`id_hh`,`id_nl`),
  ADD KEY `id_nl` (`id_nl`);

--
-- Indexes for table `thoi_diem`
--
ALTER TABLE `thoi_diem`
  ADD PRIMARY KEY (`id_td`);

--
-- Indexes for table `trang_thai_lo_hang`
--
ALTER TABLE `trang_thai_lo_hang`
  ADD PRIMARY KEY (`id_trang_thai_lo`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dia_chi_giao_hang`
--
ALTER TABLE `dia_chi_giao_hang`
  MODIFY `id_dc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `don_hang_hien_tai`
--
ALTER TABLE `don_hang_hien_tai`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chi_tiet_don_hang`
--
ALTER TABLE `chi_tiet_don_hang`
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_1` FOREIGN KEY (`id_dh`) REFERENCES `don_hang` (`id_dh`),
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_2` FOREIGN KEY (`id_hh`) REFERENCES `hang_hoa` (`id_hh`),
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_3` FOREIGN KEY (`id_lo`) REFERENCES `lo_hang` (`id_lo`);

--
-- Constraints for table `chi_tiet_gio_hang`
--
ALTER TABLE `chi_tiet_gio_hang`
  ADD CONSTRAINT `chi_tiet_gio_hang_ibfk_1` FOREIGN KEY (`id_gh`) REFERENCES `gio_hang` (`id_gh`) ON DELETE CASCADE,
  ADD CONSTRAINT `chi_tiet_gio_hang_ibfk_2` FOREIGN KEY (`id_hh`) REFERENCES `hang_hoa` (`id_hh`);

--
-- Constraints for table `chi_tiet_phieu_nhap`
--
ALTER TABLE `chi_tiet_phieu_nhap`
  ADD CONSTRAINT `chi_tiet_phieu_nhap_ibfk_1` FOREIGN KEY (`id_pn`) REFERENCES `phieu_nhap` (`id_pn`),
  ADD CONSTRAINT `chi_tiet_phieu_nhap_ibfk_2` FOREIGN KEY (`id_lo`) REFERENCES `lo_hang` (`id_lo`);

--
-- Constraints for table `dia_chi_giao_hang`
--
ALTER TABLE `dia_chi_giao_hang`
  ADD CONSTRAINT `dia_chi_giao_hang_ibfk_1` FOREIGN KEY (`id_tk`) REFERENCES `tai_khoan` (`id_tk`) ON DELETE CASCADE;

--
-- Constraints for table `don_hang`
--
ALTER TABLE `don_hang`
  ADD CONSTRAINT `don_hang_ibfk_1` FOREIGN KEY (`id_tk`) REFERENCES `tai_khoan` (`id_tk`),
  ADD CONSTRAINT `don_hang_ibfk_2` FOREIGN KEY (`id_pttt`) REFERENCES `phuong_thuc_thanh_toan` (`id_pttt`),
  ADD CONSTRAINT `fk_don_hang_dia_chi` FOREIGN KEY (`id_dc`) REFERENCES `dia_chi_giao_hang` (`id_dc`) ON UPDATE CASCADE;

--
-- Constraints for table `don_hang_hien_tai`
--
ALTER TABLE `don_hang_hien_tai`
  ADD CONSTRAINT `don_hang_hien_tai_ibfk_1` FOREIGN KEY (`id_dh`) REFERENCES `don_hang` (`id_dh`),
  ADD CONSTRAINT `don_hang_hien_tai_ibfk_2` FOREIGN KEY (`id_ttd`) REFERENCES `danh_muc_trang_thai` (`id_ttd`);

--
-- Constraints for table `gia_ban_hien_tai`
--
ALTER TABLE `gia_ban_hien_tai`
  ADD CONSTRAINT `fk_gia_ban_lo_hang` FOREIGN KEY (`id_lo`) REFERENCES `lo_hang` (`id_lo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gia_ban_hien_tai_ibfk_2` FOREIGN KEY (`id_td`) REFERENCES `thoi_diem` (`id_td`) ON DELETE CASCADE;

--
-- Constraints for table `gio_hang`
--
ALTER TABLE `gio_hang`
  ADD CONSTRAINT `gio_hang_ibfk_1` FOREIGN KEY (`id_tk`) REFERENCES `tai_khoan` (`id_tk`);

--
-- Constraints for table `hang_hoa`
--
ALTER TABLE `hang_hoa`
  ADD CONSTRAINT `hang_hoa_ibfk_1` FOREIGN KEY (`id_loai2`) REFERENCES `loai_hang_hoa` (`id_loai2`),
  ADD CONSTRAINT `hang_hoa_ibfk_2` FOREIGN KEY (`id_dvt`) REFERENCES `dvt` (`id_dvt`);

--
-- Constraints for table `loai_hang_hoa`
--
ALTER TABLE `loai_hang_hoa`
  ADD CONSTRAINT `loai_hang_hoa_ibfk_1` FOREIGN KEY (`id_dm`) REFERENCES `danh_muc` (`id_dm`);

--
-- Constraints for table `lo_hang`
--
ALTER TABLE `lo_hang`
  ADD CONSTRAINT `lo_hang_ibfk_1` FOREIGN KEY (`id_hh`) REFERENCES `hang_hoa` (`id_hh`),
  ADD CONSTRAINT `lo_hang_ibfk_2` FOREIGN KEY (`id_km`) REFERENCES `khuyen_mai` (`id_km`),
  ADD CONSTRAINT `lo_hang_ibfk_3` FOREIGN KEY (`id_trang_thai_lo`) REFERENCES `trang_thai_lo_hang` (`id_trang_thai_lo`);

--
-- Constraints for table `phieu_nhap`
--
ALTER TABLE `phieu_nhap`
  ADD CONSTRAINT `phieu_nhap_ibfk_1` FOREIGN KEY (`id_ncc`) REFERENCES `nha_cung_cap` (`id_ncc`);

--
-- Constraints for table `tai_khoan`
--
ALTER TABLE `tai_khoan`
  ADD CONSTRAINT `tai_khoan_ibfk_1` FOREIGN KEY (`id_nd`) REFERENCES `nguoi_dung` (`id_nd`);

--
-- Constraints for table `thanh_phan`
--
ALTER TABLE `thanh_phan`
  ADD CONSTRAINT `thanh_phan_ibfk_1` FOREIGN KEY (`id_hh`) REFERENCES `hang_hoa` (`id_hh`) ON DELETE CASCADE,
  ADD CONSTRAINT `thanh_phan_ibfk_2` FOREIGN KEY (`id_nl`) REFERENCES `nguyen_lieu` (`id_nl`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
