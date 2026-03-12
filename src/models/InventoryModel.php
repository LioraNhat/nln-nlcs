<?php
namespace App\Models;
use App\Core\BaseModel;
use PDO;

class InventoryModel extends BaseModel {

    // Lấy danh sách hàng hóa kèm lô hàng gần hết hạn nhất
    public function getAllProducts() {
        $sql = "SELECT h.id_hh, h.ten_hh, h.link_anh,
                    lhh.ten_loai, dvt.dvt,
                    l.id_lo, l.hsd_lo, l.so_luong_con_lai, l.id_trang_thai_lo,
                    ttl.ten_trang_thai_lo,
                    g.gia_hien_tai
                FROM hang_hoa h
                LEFT JOIN loai_hang_hoa lhh ON h.id_loai2 = lhh.id_loai2
                LEFT JOIN dvt ON h.id_dvt = dvt.id_dvt
                LEFT JOIN lo_hang l ON h.id_hh = l.id_hh
                    AND l.hsd_lo = (
                        SELECT MIN(hsd_lo) FROM lo_hang 
                        WHERE id_hh = h.id_hh AND so_luong_con_lai > 0
                    )
                LEFT JOIN trang_thai_lo_hang ttl ON l.id_trang_thai_lo = ttl.id_trang_thai_lo
                LEFT JOIN gia_ban_hien_tai g ON l.id_lo = g.id_lo
                    AND g.id_td = (
                        SELECT id_td FROM thoi_diem 
                        WHERE NOW() BETWEEN ngay_bd_gia_ban AND ngay_kt_gia_ban 
                        LIMIT 1
                    )
                WHERE h.duoc_phep_ban = 1
                GROUP BY h.id_hh
                ORDER BY l.hsd_lo ASC";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy tất cả lô hàng của 1 sản phẩm
    public function getLotsByProduct($id_hh) {
        $sql = "SELECT l.*, ttl.ten_trang_thai_lo, g.gia_hien_tai,
                    km.ten_km, km.phan_tram_km
                FROM lo_hang l
                LEFT JOIN trang_thai_lo_hang ttl ON l.id_trang_thai_lo = ttl.id_trang_thai_lo
                LEFT JOIN gia_ban_hien_tai g ON l.id_lo = g.id_lo
                    AND g.id_td = (
                        SELECT id_td FROM thoi_diem 
                        WHERE NOW() BETWEEN ngay_bd_gia_ban AND ngay_kt_gia_ban 
                        LIMIT 1
                    )
                LEFT JOIN khuyen_mai km ON l.id_km = km.id_km
                WHERE l.id_hh = :id_hh
                ORDER BY l.hsd_lo ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_hh' => $id_hh]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy tất cả nhà cung cấp
    public function getAllSuppliers() {
        return $this->db->query("SELECT * FROM nha_cung_cap ORDER BY ten_ncc ASC")
                        ->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy tất cả khuyến mãi đang hoạt động
    public function getActivePromotions() {
        return $this->db->query("SELECT * FROM khuyen_mai WHERE trang_thai_km = 1")
                        ->fetchAll(PDO::FETCH_ASSOC);
    }

    // Sinh mã lô mới cho 1 sản phẩm
    public function generateLotId($id_hh) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as total FROM lo_hang WHERE id_hh = ?"
        );
        $stmt->execute([$id_hh]);
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        $suffix = chr(65 + $count); // A, B, C...
        return $id_hh . 'B' . date('ymd') . $suffix;
    }

    // Thêm lô hàng mới + phiếu nhập
    public function createLot($id_hh, $data) {
        try {
            $this->db->beginTransaction();

            // 1. Sinh mã phiếu nhập
            $stmt = $this->db->query("SELECT MAX(id_pn) as max_id FROM phieu_nhap");
            $maxId = $stmt->fetch(PDO::FETCH_ASSOC)['max_id'];
            $num = $maxId ? (int)substr($maxId, 2) + 1 : 1;
            $id_pn = 'PN' . str_pad($num, 7, '0', STR_PAD_LEFT);

            // 2. Insert phieu_nhap
            $this->db->prepare(
                "INSERT INTO phieu_nhap (id_pn, id_ncc, ngay_lap_phieu_nhap, tong_tien_nhap) 
                 VALUES (?, ?, NOW(), ?)"
            )->execute([$id_pn, $data['id_ncc'], $data['don_gia'] * $data['so_luong']]);

            // 3. Sinh mã lô
            $id_lo = $this->generateLotId($id_hh);

            // 4. Insert lo_hang
            $this->db->prepare(
                "INSERT INTO lo_hang (id_lo, id_hh, id_pn, id_km, id_trang_thai_lo, 
                                      hsd_lo, so_luong_nhap, so_luong_con_lai, gia_von_nhap)
                 VALUES (?, ?, ?, ?, 'TTL01', ?, ?, ?, ?)"
            )->execute([
                $id_lo, $id_hh, $id_pn,
                !empty($data['id_km']) ? $data['id_km'] : null,
                $data['hsd_lo'],
                $data['so_luong'],
                $data['so_luong'],
                $data['don_gia']
            ]);

            // 5. Insert chi_tiet_phieu_nhap
            $this->db->prepare(
                "INSERT INTO chi_tiet_phieu_nhap (id_pn, id_hh, id_lo, so_luong_nhap_lo, don_gia_nhap_lo)
                 VALUES (?, ?, ?, ?, ?)"
            )->execute([$id_pn, $id_hh, $id_lo, $data['so_luong'], $data['don_gia']]);

            // 6. Insert gia_ban_hien_tai
            $stmt = $this->db->prepare(
                "SELECT id_td FROM thoi_diem 
                 WHERE NOW() BETWEEN ngay_bd_gia_ban AND ngay_kt_gia_ban LIMIT 1"
            );
            $stmt->execute();
            $td = $stmt->fetch(PDO::FETCH_ASSOC);
            $id_td = $td ? $td['id_td'] : 'TD005';

            $this->db->prepare(
                "INSERT INTO gia_ban_hien_tai (id_lo, id_td, gia_hien_tai) VALUES (?, ?, ?)"
            )->execute([$id_lo, $id_td, $data['gia_ban']]);

            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
}