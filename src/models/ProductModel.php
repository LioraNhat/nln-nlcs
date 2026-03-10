<?php

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class ProductModel extends BaseModel {
    private function getPriceJoins() {
        return " 
            LEFT JOIN (
                SELECT l1.id_hh, l1.id_lo, l1.id_km, l1.hsd_lo
                FROM lo_hang l1
                INNER JOIN (
                    SELECT id_hh, MIN(hsd_lo) as min_hsd
                    FROM lo_hang
                    WHERE so_luong_con_lai > 0
                    AND hsd_lo > NOW()
                    GROUP BY id_hh
                ) fefo ON l1.id_hh = fefo.id_hh AND l1.hsd_lo = fefo.min_hsd
            ) l ON h.id_hh = l.id_hh
            LEFT JOIN gia_ban_hien_tai g ON l.id_lo = g.id_lo
                AND g.id_td IN (
                    SELECT id_td FROM thoi_diem 
                    WHERE NOW() BETWEEN ngay_bd_gia_ban AND ngay_kt_gia_ban
                )
            LEFT JOIN khuyen_mai km ON l.id_km = km.id_km 
                AND km.trang_thai_km = 1
                AND NOW() BETWEEN km.ngay_bd_km AND km.ngay_kt_km
        ";
    }

    public function getFeaturedProducts($limit = 6) { 
        $sql = "SELECT h.id_hh, h.ten_hh, h.link_anh, g.gia_hien_tai, km.phan_tram_km
                FROM hang_hoa h"
               . $this->getPriceJoins() .
               " WHERE (h.duoc_phep_ban = 1)
                GROUP BY h.id_hh
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách sản phẩm đang khuyến mãi (Cho trang chủ)
     */
    public function getProductsOnSale($limit = 6) {
        $sql = "SELECT h.id_hh, h.ten_hh, h.link_anh, g.gia_hien_tai, km.phan_tram_km
                FROM hang_hoa h"
                . $this->getPriceJoins() .
                " WHERE h.duoc_phep_ban = 1
                AND km.id_km IS NOT NULL
                GROUP BY h.id_hh 
                ORDER BY RAND()
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tìm kiếm sản phẩm (Dùng cho User - Trang Search)
     * Tìm theo: Tên sản phẩm HOẶC Tên loại hàng
     */
    public function searchProducts($searchTerm, $limit = 12, $offset = 0) {
        $search = trim($searchTerm);
        $regex = '[[:<:]]' . $search . '[[:>:]]';

        $sql = "SELECT h.id_hh, h.ten_hh, h.link_anh, h.id_dvt,
                       lhh.ten_loai, dvt.dvt, g.gia_hien_tai, km.phan_tram_km
                FROM hang_hoa h
                LEFT JOIN loai_hang_hoa lhh ON h.id_loai2 = lhh.id_loai2
                LEFT JOIN dvt ON h.id_dvt = dvt.id_dvt
                LEFT JOIN lo_hang l ON h.id_hh = l.id_hh
                LEFT JOIN khuyen_mai km ON l.id_km = km.id_km
                LEFT JOIN gia_ban_hien_tai g ON l.id_lo = g.id_lo 
                AND g.id_td = (
                    SELECT id_td FROM thoi_diem 
                    WHERE (NOW() BETWEEN ngay_bd_gia_ban AND ngay_kt_gia_ban)
                    OR id_td = 'TD003'
                    ORDER BY ngay_bd_gia_ban DESC LIMIT 1
                )
                WHERE h.duoc_phep_ban = 1
                AND (h.ten_hh REGEXP :regex OR lhh.ten_loai REGEXP :regex)
                GROUP BY h.id_hh
                ORDER BY h.ten_hh ASC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':regex', $regex);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findProductById($productId) {
        $sql = "SELECT h.*,
                    g.gia_hien_tai,
                    km.phan_tram_km,
                    km.ten_km,
                    SUM(l.so_luong_con_lai) as so_luong_ton_hh
                FROM hang_hoa h
                LEFT JOIN lo_hang l ON h.id_hh = l.id_hh
                LEFT JOIN gia_ban_hien_tai g ON l.id_lo = g.id_lo
                LEFT JOIN khuyen_mai km ON l.id_km = km.id_km
                    AND km.trang_thai_km = 1
                    AND NOW() BETWEEN km.ngay_bd_km AND km.ngay_kt_km
                WHERE h.id_hh = :id_hh 
                AND h.duoc_phep_ban = 1
                GROUP BY h.id_hh
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_hh' => $productId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getProductsByCategoryId($categoryId, $filters = [], $limit = 12, $offset = 0) {
        $sql = "SELECT h.id_hh, h.ten_hh, h.link_anh, g.gia_hien_tai, km.phan_tram_km
                FROM hang_hoa h
                INNER JOIN loai_hang_hoa lhh ON h.id_loai2 = lhh.id_loai2 
                " . $this->getPriceJoins() . "
                WHERE lhh.id_dm = :id_dm AND h.duoc_phep_ban = 1";

        // Lọc giá
        if (!empty($filters['price'])) {
            switch ($filters['price']) {
                case 'under_100k':
                    $sql .= " AND g.gia_hien_tai < 100000"; break;
                case '100000-200000':
                    $sql .= " AND g.gia_hien_tai BETWEEN 100000 AND 200000"; break;
                case '200000-300000':
                    $sql .= " AND g.gia_hien_tai BETWEEN 200000 AND 300000"; break;
                case '300000-400000':
                    $sql .= " AND g.gia_hien_tai BETWEEN 300000 AND 400000"; break;
                case 'over_400k':
                    $sql .= " AND g.gia_hien_tai > 400000"; break;
            }
        }

        $sql .= " GROUP BY h.id_hh";

        // Sắp xếp
        switch ($filters['sort'] ?? 'name_asc') {
            case 'name_desc': $sql .= " ORDER BY h.ten_hh DESC"; break;
            case 'price_asc': $sql .= " ORDER BY g.gia_hien_tai ASC"; break;
            case 'price_desc': $sql .= " ORDER BY g.gia_hien_tai DESC"; break;
            default: $sql .= " ORDER BY h.ten_hh ASC";
        }

        $sql .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_dm', $categoryId);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // (Giữ các hàm User khác searchProductsByName...)
    
    // ============================================
    // 2. CÁC PHƯƠNG THỨC CHO ADMIN (READ)
    // ============================================

    public function getTotalProducts() {
        $sql = "SELECT COUNT(*) as total FROM hang_hoa";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0;
    }

    public function countAllProducts($keyword = '') {
        $sql = "SELECT COUNT(*) as total FROM hang_hoa h";
        $params = [];
        if (!empty($keyword)) {
            $sql .= " WHERE (h.ID_HH LIKE :keyword OR h.TEN_HH LIKE :keyword)";
            $params[':keyword'] = '%' . $keyword . '%';
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0;
    }

    // ============================================
    // 3. CÁC PHƯƠNG THỨC CHO ADMIN
    // ============================================
    public function getAllProducts($keyword = '', $limit = 20, $offset = 0) {
        $sql = "SELECT h.id_hh, h.ten_hh, h.link_anh, h.duoc_phep_ban,
                       lhh.ten_loai, dvt.dvt, g.gia_hien_tai,
                       SUM(l.so_luong_con_lai) as tong_ton
                FROM hang_hoa h
                LEFT JOIN loai_hang_hoa lhh ON h.id_loai2 = lhh.id_loai2
                LEFT JOIN dvt ON h.id_dvt = dvt.id_dvt
                LEFT JOIN lo_hang l ON h.id_hh = l.id_hh
                LEFT JOIN gia_ban_hien_tai g ON l.id_lo = g.id_lo 
                AND g.id_td = (SELECT id_td FROM thoi_diem WHERE NOW() BETWEEN ngay_bd_gia_ban AND ngay_kt_gia_ban LIMIT 1)";
        
        if (!empty($keyword)) {
            $sql .= " WHERE (h.id_hh LIKE :keyword OR h.ten_hh LIKE :keyword)";
        }

        $sql .= " GROUP BY h.id_hh ORDER BY h.id_hh DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        if (!empty($keyword)) $stmt->bindValue(':keyword', '%' . $keyword . '%');
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductByIdForAdmin($productId) {
        $sql = "SELECT h.*, g.GIA_HIEN_TAI
                FROM hang_hoa h
                LEFT JOIN gia_ban_hien_tai g ON h.ID_HH = g.ID_HH 
                AND g.ID_TD = (SELECT ID_TD FROM thoi_diem WHERE NOW() BETWEEN NGAY_BD_GIA_BAN AND NGAY_KT_GIA_BAN LIMIT 1)
                WHERE h.ID_HH = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ============================================
    // 3. CÁC PHƯƠNG THỨC ADMIN (CREATE - UPDATE - DELETE) (MỚI)
    // ============================================

    /**
     * Tự động sinh ID mới (VD: 00129 -> 00130)
     */
    public function generateNewId() {
        $sql = "SELECT MAX(ID_HH) as max_id FROM hang_hoa";
        $stmt = $this->db->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row && $row['max_id']) {
            // Lấy phần số và tăng lên 1
            $maxId = (int)$row['max_id']; 
            $newId = $maxId + 1;
        } else {
            $newId = 1;
        }
        
        // Format thành 5 chữ số (00130)
        return str_pad($newId, 5, '0', STR_PAD_LEFT);
    }

    public function createProduct($data) {
    try {
        $this->db->beginTransaction();

        $idHH = $data['id_hh']; 
        $idTD = $this->getCurrentTimeId(); 
        // Tạo mã lô hàng tự động (VD: LO + Mã HH + A)
        $idLo = 'LO' . $idHH . 'A';

        // 1. Chèn vào bảng hang_hoa (Bỏ các cột SL_TON, HSD, ID_KM vì đã sang bảng LO_HANG)
        $sqlHH = "INSERT INTO hang_hoa (ID_HH, ID_LOAI2, ID_DVT, TEN_HH, link_anh, MO_TA_HH, DUOC_PHEP_BAN, LA_HANG_SX) 
                VALUES (:id, :lhh, :dvt, :ten, :anh, :mota, :ban, :hsx)";
        
        $stmtHH = $this->db->prepare($sqlHH);
        $stmtHH->execute([
            ':id'   => $idHH,
            ':lhh'  => $data['id_lhh'],
            ':dvt'  => $data['id_dvt'],
            ':ten'  => $data['ten_hh'],
            ':anh'  => $data['link_anh'],
            ':mota' => $data['mo_ta_hh'],
            ':ban'  => isset($data['duoc_phep_ban']) ? 1 : 0,
            ':hsx'  => isset($data['la_hang_sx']) ? 1 : 0
        ]);

        // 2. Chèn vào bảng lo_hang (Quản lý SL và HSD ở đây)
        $sqlLo = "INSERT INTO lo_hang (ID_LO, ID_HH, ID_KM, ID_TRANG_THAI_LO, HSD_LO, SO_LUONG_NHAP, SO_LUONG_CON_LAI) 
                VALUES (:id_lo, :id_hh, :id_km, 'TTL01', :hsd, :sl, :sl)";
        $stmtLo = $this->db->prepare($sqlLo);
        $stmtLo->execute([
            ':id_lo' => $idLo,
            ':id_hh' => $idHH,
            ':id_km' => !empty($data['id_km']) ? $data['id_km'] : NULL,
            ':hsd'   => $data['hsd'],
            ':sl'    => $data['so_luong_ton']
        ]);

        // 3. Chèn vào bảng gia_ban_hien_tai (Liên kết qua ID_LO thay vì ID_HH)
        if (!empty($data['gia_ban'])) {
            $sqlGia = "INSERT INTO gia_ban_hien_tai (ID_LO, ID_TD, GIA_HIEN_TAI) VALUES (:id_lo, :td, :gia)";
            $stmtGia = $this->db->prepare($sqlGia);
            $stmtGia->execute([
                ':id_lo' => $idLo,
                ':td'    => $idTD,
                ':gia'   => $data['gia_ban']
            ]);
        }

        $this->db->commit();
        return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Lỗi CreateProduct: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy ID Thời điểm hiện tại (Để lưu giá)
     */
    private function getCurrentTimeId() {
        $stmt = $this->db->prepare("SELECT id_td FROM thoi_diem WHERE NOW() BETWEEN ngay_bd_gia_ban AND ngay_kt_gia_ban LIMIT 1");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['id_td'] : 'TD003'; 
    }

    /**
     * Xóa sản phẩm (Thực chất là Ngừng kinh doanh để bảo toàn lịch sử đơn hàng)
     */
    public function updateProduct($id, $data) {
        try {
            $this->db->beginTransaction();
            $sqlHH = "UPDATE hang_hoa SET id_loai2 = :lhh, id_dvt = :dvt, ten_hh = :ten, mo_ta_hh = :mota, duoc_phep_ban = :ban WHERE id_hh = :id";
            $this->db->prepare($sqlHH)->execute([
                ':lhh' => $data['id_lhh'], ':dvt' => $data['id_dvt'], ':ten' => $data['ten_hh'],
                ':mota' => $data['mo_ta_hh'], ':ban' => isset($data['duoc_phep_ban']) ? 1 : 0, ':id' => $id
            ]);

            $sqlLo = "UPDATE lo_hang SET id_km = :id_km, hsd_lo = :hsd, so_luong_con_lai = :sl WHERE id_hh = :id_hh ORDER BY hsd_lo DESC LIMIT 1";
            $this->db->prepare($sqlLo)->execute([':id_km' => !empty($data['id_km']) ? $data['id_km'] : NULL, ':hsd' => $data['hsd'], ':sl' => $data['so_luong_ton'], ':id_hh' => $id]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function deleteProduct($id) {
        try {
            $this->db->beginTransaction();
            $hasOrders = $this->db->prepare("SELECT COUNT(*) FROM chi_tiet_don_hang WHERE id_hh = ?");
            $hasOrders->execute([$id]);
            if ($hasOrders->fetchColumn() > 0) {
                $this->db->prepare("UPDATE hang_hoa SET duoc_phep_ban = 0 WHERE id_hh = ?")->execute([$id]);
            } else {
                $this->db->prepare("DELETE FROM chi_tiet_gio_hang WHERE id_hh = ?")->execute([$id]);
                $this->db->prepare("DELETE g FROM gia_ban_hien_tai g JOIN lo_hang l ON g.id_lo = l.id_lo WHERE l.id_hh = ?")->execute([$id]);
                $this->db->prepare("DELETE FROM lo_hang WHERE id_hh = ?")->execute([$id]);
                $this->db->prepare("DELETE FROM hang_hoa WHERE id_hh = ?")->execute([$id]);
            }
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Lấy sản phẩm liên quan (cùng loại)
     */
    public function getRelatedProducts($currentProductId, $categoryId, $limit = 6) {
        $sql = "SELECT h.id_hh, h.ten_hh, h.link_anh, g.gia_hien_tai, km.phan_tram_km
                FROM hang_hoa h"
               . $this->getPriceJoins() .
               " WHERE h.id_loai2 = :category_id
                 AND h.id_hh != :current_product_id
                 AND h.duoc_phep_ban = 1
                GROUP BY h.id_hh
                ORDER BY RAND()
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':category_id', $categoryId);
        $stmt->bindValue(':current_product_id', $currentProductId);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// Lấy sản phẩm theo Loại Hàng Hóa (User)
    public function getProductsByProductType($productTypeId, $filters = [], $limit = 12, $offset = 0) {
        $sql = "SELECT h.id_hh, h.ten_hh, h.link_anh, g.gia_hien_tai, km.phan_tram_km
                FROM hang_hoa h"
               . $this->getPriceJoins() .
               " WHERE h.id_loai2 = :id_lhh AND h.duoc_phep_ban = 1
                GROUP BY h.id_hh
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_lhh', $productTypeId);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllLoaiHang() {
        return $this->db->query("SELECT * FROM loai_hang_hoa ORDER BY TEN_LHH ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllDVT() {
        return $this->db->query("SELECT * FROM dvt ORDER BY DVT ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllKhuyenMai() {
        // Chỉ lấy khuyến mãi đang diễn ra hoặc sắp diễn ra
        return $this->db->query("SELECT * FROM khuyen_mai WHERE TRANG_THAI_KM != 'Đã hủy' ORDER BY TEN_KM ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Hàm sinh mã tự động (VD: 00001, 00002...)
     */
    public function generateProductId() {
        $stmt = $this->db->query("SELECT MAX(ID_HH) as max_id FROM hang_hoa");
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $maxId = $row['max_id'];

        if ($maxId) {
            $num = intval($maxId) + 1;
        } else {
            $num = 1;
        }
        return str_pad($num, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Thêm giá mới cho sản phẩm (Vào bảng GIA_BAN_HIEN_TAI)
     */
    public function insertPrice($productId, $price) {
        $timeId = $this->getCurrentTimeId(); 
        
        $sql = "INSERT INTO gia_ban_hien_tai (ID_HH, ID_TD, GIA_HIEN_TAI) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$productId, $timeId, $price]);
    }

    /**
     * Cập nhật giá sản phẩm (Nếu chưa có thì thêm mới)
     */
    public function updatePrice($productId, $price) {
        $timeId = $this->getCurrentTimeId();
        
        // Kiểm tra xem sản phẩm đã có giá ở thời điểm này chưa
        $check = $this->db->prepare("SELECT 1 FROM gia_ban_hien_tai WHERE ID_HH = ? AND ID_TD = ?");
        $check->execute([$productId, $timeId]);
        
        if ($check->rowCount() > 0) {
            // Nếu có rồi -> Update
            $sql = "UPDATE gia_ban_hien_tai SET GIA_HIEN_TAI = ? WHERE ID_HH = ? AND ID_TD = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$price, $productId, $timeId]);
        } else {
            // Nếu chưa có -> Insert mới
            return $this->insertPrice($productId, $price);
        }
    }

    /**
     * Áp dụng mã khuyến mãi cho toàn bộ sản phẩm thuộc 1 loại hàng
     */
    public function applyPromotionToCategory($promoId, $categoryId) {
        $sql = "UPDATE hang_hoa SET ID_KM = ? WHERE ID_LHH = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$promoId, $categoryId]);
    }
}
