<?php
namespace App\Models;
use App\Core\BaseModel;
use PDO;

class InventoryModel extends BaseModel {

    // ✅ SỬA: subquery gia_hien_tai lọc đúng thoi_diem hiện tại + tồn > 0
    public function getAllProducts($search = '') {
        $sql = "SELECT h.id_hh, h.ten_hh, h.link_anh, lhh.ten_loai, dvt.dvt,
                    COUNT(l.id_lo) as so_luong_lo,
                    MIN(CASE WHEN l.so_luong_con_lai > 0 THEN l.hsd_lo ELSE NULL END) as hsd_gan_nhat,
                    SUM(l.so_luong_con_lai) as tong_ton_kho,
                    (SELECT g.gia_hien_tai 
                        FROM gia_ban_hien_tai g 
                        JOIN lo_hang l2 ON g.id_lo = l2.id_lo
                        WHERE l2.id_hh = h.id_hh 
                        AND l2.so_luong_con_lai > 0
                        ORDER BY l2.hsd_lo ASC 
                        LIMIT 1
                    ) as gia_hien_tai
                FROM hang_hoa h
                LEFT JOIN loai_hang_hoa lhh ON h.id_loai2 = lhh.id_loai2
                LEFT JOIN dvt ON h.id_dvt = dvt.id_dvt
                LEFT JOIN lo_hang l ON h.id_hh = l.id_hh
                WHERE h.duoc_phep_ban = 1";

        // ← THÊM điều kiện tìm kiếm
        if (!empty($search)) {
            $sql .= " AND (h.ten_hh LIKE :search OR h.id_hh LIKE :search)";
        }

        $sql .= " GROUP BY h.id_hh, h.ten_hh, h.link_anh, lhh.ten_loai, dvt.dvt
                ORDER BY h.id_hh DESC";

        $stmt = $this->db->prepare($sql);

        if (!empty($search)) {
            $stmt->bindValue(':search', '%' . $search . '%');
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách lô cho AJAX (modal trang Index)
    public function getBatchesByProductId($id_hh) {
        $sql = "SELECT l.*, 
                    ttl.ten_trang_thai_lo,
                    g.gia_hien_tai,
                    km.ten_km, km.phan_tram_km,
                    pn.ngay_lap_phieu_nhap,
                    ncc.ten_ncc,
                    h.phan_tram_loi_nhuan
                FROM lo_hang l

                -- ✅ JOIN thêm bảng hàng hóa để lấy % lợi nhuận
                JOIN hang_hoa h ON l.id_hh = h.id_hh

                JOIN trang_thai_lo_hang ttl 
                    ON l.id_trang_thai_lo = ttl.id_trang_thai_lo

                LEFT JOIN gia_ban_hien_tai g 
                    ON l.id_lo = g.id_lo
                    AND g.id_td = (
                        SELECT id_td 
                        FROM thoi_diem 
                        WHERE NOW() BETWEEN ngay_bd_gia_ban AND ngay_kt_gia_ban 
                        LIMIT 1
                    )

                LEFT JOIN khuyen_mai km 
                    ON l.id_km = km.id_km

                LEFT JOIN phieu_nhap pn 
                    ON l.id_pn = pn.id_pn

                LEFT JOIN nha_cung_cap ncc 
                    ON pn.id_ncc = ncc.id_ncc

                WHERE l.id_hh = :id_hh
                ORDER BY l.hsd_lo ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_hh' => $id_hh]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Xóa lô hàng
    public function deleteLot($id_lo) {
        try {
            $this->db->beginTransaction();
            $this->db->prepare("DELETE FROM gia_ban_hien_tai WHERE id_lo = ?")->execute([$id_lo]);
            $this->db->prepare("DELETE FROM chi_tiet_phieu_nhap WHERE id_lo = ?")->execute([$id_lo]);
            $res = $this->db->prepare("DELETE FROM lo_hang WHERE id_lo = ?")->execute([$id_lo]);
            $this->db->commit();
            return $res;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Cập nhật lô từ Modal (Index page)
    public function updateLotInfo($id_lo, $data) {
        try {
            $sql = "UPDATE lo_hang 
                    SET hsd_lo = :hsd, 
                        so_luong_con_lai = :qty, 
                        id_trang_thai_lo = :status 
                    WHERE id_lo = :id_lo";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':hsd'    => $data['hsd_lo'],
                ':qty'    => $data['so_luong_con_lai'],
                ':status' => $data['id_trang_thai_lo'],
                ':id_lo'  => $id_lo
            ]);
        } catch (\PDOException $e) {
            error_log("Lỗi UpdateLotInfo: " . $e->getMessage());
            return false;
        }
    }

    // Lấy lô theo sản phẩm cho trang Detail
    public function getLotsByProduct($id_hh) {
        $sql = "SELECT l.*,
                    ttl.ten_trang_thai_lo,
                    g.gia_hien_tai,
                    km.ten_km, km.phan_tram_km,
                    pn.ngay_lap_phieu_nhap,
                    ncc.ten_ncc
                FROM lo_hang l
                LEFT JOIN trang_thai_lo_hang ttl ON l.id_trang_thai_lo = ttl.id_trang_thai_lo
                LEFT JOIN gia_ban_hien_tai g ON l.id_lo = g.id_lo
                    AND g.id_td = (
                        SELECT id_td FROM thoi_diem 
                        WHERE NOW() BETWEEN ngay_bd_gia_ban AND ngay_kt_gia_ban 
                        LIMIT 1
                    )
                LEFT JOIN khuyen_mai km ON l.id_km = km.id_km
                LEFT JOIN phieu_nhap pn ON l.id_pn = pn.id_pn
                LEFT JOIN nha_cung_cap ncc ON pn.id_ncc = ncc.id_ncc
                WHERE l.id_hh = :id_hh
                ORDER BY l.hsd_lo ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_hh' => $id_hh]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllSuppliers() {
        return $this->db->query("SELECT * FROM nha_cung_cap ORDER BY ten_ncc ASC")
                        ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActivePromotions() {
        return $this->db->query("SELECT * FROM khuyen_mai ORDER BY ngay_bd_km DESC")
                        ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function generateLotId($id_hh) {
        $short_id_hh = substr($id_hh, -3);
        $separator   = 'B';
        $time_part   = date('dHi');
        return $short_id_hh . $separator . $time_part;
    }

    public function createLot($id_hh, $data) {
        try {
            $this->db->beginTransaction();

            // 1. Lấy % lợi nhuận
            $stmtHh = $this->db->prepare("SELECT phan_tram_loi_nhuan FROM hang_hoa WHERE id_hh = ?");
            $stmtHh->execute([$id_hh]);
            $productInfo  = $stmtHh->fetch(PDO::FETCH_ASSOC);
            $profitMargin = $productInfo ? (float)$productInfo['phan_tram_loi_nhuan'] : 30.0;

            // 2. Tính giá bán
            $calculatedPrice = $data['don_gia'] * (1 + ($profitMargin / 100));

            // 3. Sinh mã phiếu nhập
            $stmtPn = $this->db->query("SELECT id_pn FROM phieu_nhap ORDER BY id_pn DESC LIMIT 1");
            $lastPn = $stmtPn->fetch(PDO::FETCH_ASSOC);
            $num    = $lastPn ? (int)substr($lastPn['id_pn'], 2) + 1 : 1;
            $id_pn  = 'PN' . str_pad($num, 7, '0', STR_PAD_LEFT);

            // 4. Lưu phiếu nhập
            $tong_tien = $data['don_gia'] * $data['so_luong'];
            $this->db->prepare("INSERT INTO phieu_nhap (id_pn, id_ncc, ngay_lap_phieu_nhap, tong_tien_nhap) VALUES (?, ?, NOW(), ?)")
                     ->execute([$id_pn, $data['id_ncc'], $tong_tien]);

            // 5. Lưu lô hàng
            $id_lo = $this->generateLotId($id_hh);
            $this->db->prepare("INSERT INTO lo_hang (id_lo, id_hh, id_pn, id_km, id_trang_thai_lo, hsd_lo, so_luong_nhap, so_luong_con_lai, gia_von_nhap) VALUES (?, ?, ?, ?, 'TTL01', ?, ?, ?, ?)")
                     ->execute([
                         $id_lo, $id_hh, $id_pn,
                         !empty($data['id_km']) ? $data['id_km'] : null,
                         $data['hsd_lo'], $data['so_luong'], $data['so_luong'], $data['don_gia']
                     ]);

            // 6. Chi tiết phiếu nhập
            $this->db->prepare("INSERT INTO chi_tiet_phieu_nhap (id_pn, id_hh, id_lo, so_luong_nhap_lo, don_gia_nhap_lo) VALUES (?, ?, ?, ?, ?)")
                     ->execute([$id_pn, $id_hh, $id_lo, $data['so_luong'], $data['don_gia']]);

            // 7. Lấy thời điểm hiện tại
            $stmtTd = $this->db->prepare("SELECT id_td FROM thoi_diem WHERE NOW() BETWEEN ngay_bd_gia_ban AND ngay_kt_gia_ban LIMIT 1");
            $stmtTd->execute();
            $td = $stmtTd->fetch(PDO::FETCH_ASSOC);
            if (!$td) {
                $td = $this->db->query("SELECT id_td FROM thoi_diem ORDER BY ngay_bd_gia_ban DESC LIMIT 1")
                               ->fetch(PDO::FETCH_ASSOC);
            }
            $id_td = $td ? $td['id_td'] : 'TD001';

            // 8. Lưu giá bán
            $this->db->prepare("INSERT INTO gia_ban_hien_tai (id_lo, id_td, gia_hien_tai) VALUES (?, ?, ?)")
                     ->execute([$id_lo, $id_td, $calculatedPrice]);

            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Lỗi CreateLot: " . $e->getMessage());
            return false;
        }
    }

    public function updateLot($id_lo, $data) {
        try {
            $sql = "UPDATE lo_hang 
                    SET hsd_lo = :hsd, 
                        so_luong_con_lai = :qty, 
                        gia_von_nhap = :price 
                    WHERE id_lo = :id_lo";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':hsd'   => $data['hsd_lo'],
                ':qty'   => $data['so_luong_con_lai'],
                ':price' => $data['gia_von_nhap'],
                ':id_lo' => $id_lo
            ]);
        } catch (\PDOException $e) {
            error_log("Lỗi UpdateLot: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tự động tính và cập nhật giá bán dựa trên Giá vốn bình quân và % Lợi nhuận
     */
    /**
     * Tự động tính và cập nhật giá bán dựa trên Giá vốn bình quân (WAC) của TỒN KHO
     */
    public function updatePriceForProduct($id_hh) {
        try {
            $this->db->beginTransaction();

            // 1. Lấy % lợi nhuận
            $stmt = $this->db->prepare("SELECT phan_tram_loi_nhuan FROM hang_hoa WHERE id_hh = ?");
            $stmt->execute([$id_hh]);
            $margin = $stmt->fetchColumn();
            $margin = ($margin !== false) ? (float)$margin : 30.0;

            // 2. TÍNH WAC DỰA TRÊN SỐ LƯỢNG CÒN LẠI (SỬA Ở ĐÂY)
            // Thay vì dùng so_luong_nhap, ta dùng so_luong_con_lai
            $stmt = $this->db->prepare("
                SELECT SUM(so_luong_con_lai * gia_von_nhap) / SUM(so_luong_con_lai) as avg_cost 
                FROM lo_hang 
                WHERE id_hh = ? AND so_luong_con_lai > 0
            ");
            $stmt->execute([$id_hh]);
            $avg_cost = (float)$stmt->fetchColumn();

            if ($avg_cost > 0) {
                $new_price = $avg_cost * (1 + ($margin / 100));

                // 3. Lấy thời điểm hiệu lực
                $stmt_td = $this->db->query("
                    SELECT id_td FROM thoi_diem 
                    WHERE NOW() BETWEEN ngay_bd_gia_ban AND ngay_kt_gia_ban 
                    LIMIT 1
                ");
                $current_td = $stmt_td->fetchColumn();

                if (!$current_td) {
                    $current_td = $this->db->query("
                        SELECT id_td FROM thoi_diem 
                        ORDER BY ngay_bd_gia_ban DESC LIMIT 1
                    ")->fetchColumn();
                }

                if ($current_td) {
                    // 4. Cập nhật giá bán hiện tại
                    // Xóa giá cũ của sản phẩm này
                    $this->db->prepare("
                        DELETE g FROM gia_ban_hien_tai g
                        JOIN lo_hang l ON g.id_lo = l.id_lo
                        WHERE l.id_hh = ?
                    ")->execute([$id_hh]);

                    // Insert giá mới cho tất cả các lô còn tồn
                    $this->db->prepare("
                        INSERT INTO gia_ban_hien_tai (id_lo, id_td, gia_hien_tai)
                        SELECT id_lo, ?, ?
                        FROM lo_hang
                        WHERE id_hh = ? AND so_luong_con_lai > 0
                    ")->execute([$current_td, $new_price, $id_hh]);
                }
            }
            
            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Error in updatePriceForProduct: " . $e->getMessage());
            return false;
        }
    }

    public function getLotById($id_lo) {
        $stmt = $this->db->prepare("SELECT id_hh FROM lo_hang WHERE id_lo = ?");
        $stmt->execute([$id_lo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //  Tự động cập nhật trạng thái khi load trang
    public function autoUpdateLotStatus() {
        try {
            $now = date('Y-m-d H:i:s');
            
            // Hết hạn → TTL05
            $this->db->prepare("
                UPDATE lo_hang 
                SET id_trang_thai_lo = 'TTL05'
                WHERE hsd_lo < ?
                AND id_trang_thai_lo NOT IN ('TTL03', 'TTL05')
            ")->execute([$now]);

            // Sắp hết hạn (còn <= 7 ngày) → TTL04
            $this->db->prepare("
                UPDATE lo_hang 
                SET id_trang_thai_lo = 'TTL04'
                WHERE hsd_lo >= ?
                AND hsd_lo <= DATE_ADD(?, INTERVAL 7 DAY)
                AND id_trang_thai_lo NOT IN ('TTL03', 'TTL05')
            ")->execute([$now, $now]);

            // Sắp hết hàng (còn <= 5) → TTL02
            $this->db->prepare("
                UPDATE lo_hang
                SET id_trang_thai_lo = 'TTL02'
                WHERE so_luong_con_lai > 0
                AND so_luong_con_lai <= 5
                AND hsd_lo >= DATE_ADD(?, INTERVAL 7 DAY)
                AND id_trang_thai_lo NOT IN ('TTL03', 'TTL04', 'TTL05')
            ")->execute([$now]);

            // Hết hàng → TTL03
            $this->db->prepare("
                UPDATE lo_hang
                SET id_trang_thai_lo = 'TTL03'
                WHERE so_luong_con_lai = 0
                AND id_trang_thai_lo NOT IN ('TTL05')
            ")->execute([]);

            return true;
        } catch (\Exception $e) {
            error_log("autoUpdateLotStatus: " . $e->getMessage());
            return false;
        }
    }
    public function updateWACAndPrice($id_hh) {
        // 1. Tính toán WAC (Giống như logic bạn đã viết)
        $lots = $this->db->query("SELECT so_luong_con_lai, gia_von_nhap FROM lo_hang WHERE id_hh = ? AND so_luong_con_lai > 0", [$id_hh]);
        
        $total_qty_remain = 0;
        $total_value = 0;
        
        foreach ($lots as $lot) {
            $total_qty_remain += $lot['so_luong_con_lai'];
            $total_value      += ($lot['so_luong_con_lai'] * $lot['gia_von_nhap']);
        }

        if ($total_qty_remain > 0) {
            $average_cost = $total_value / $total_qty_remain;
            
            // 2. Lấy % lợi nhuận từ bảng hàng hóa
            $product = $this->db->query("SELECT phan_tram_loi_nhuan FROM hang_hoa WHERE id_hh = ?", [$id_hh])[0];
            $margin = (float)($product['phan_tram_loi_nhuan'] ?? 30);
            
            // 3. Tính giá bán mới
            $new_price = $average_cost * (1 + ($margin / 100));

            // 4. UPDATE vào bảng giá hiện tại
            // Giả sử bạn cập nhật vào bảng gia_ban_hien_tai
            $this->db->execute("UPDATE gia_ban_hien_tai SET gia_hien_tai = ? WHERE id_lo IN (SELECT id_lo FROM lo_hang WHERE id_hh = ?)", 
                                [$new_price, $id_hh]);
            return true;
        }
        return false;
    }

    
}
