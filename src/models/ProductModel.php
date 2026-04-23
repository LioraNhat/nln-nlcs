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
                    AND id_trang_thai_lo NOT IN ('TTL03', 'TTL05')
                    GROUP BY id_hh
                ) fefo ON l1.id_hh = fefo.id_hh AND l1.hsd_lo = fefo.min_hsd
            ) l ON h.id_hh = l.id_hh
            LEFT JOIN gia_ban_hien_tai g ON l.id_lo = g.id_lo
            LEFT JOIN khuyen_mai km ON l.id_km = km.id_km
                AND km.trang_thai_km = 'Đang diễn ra'
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
                    AND l.so_luong_con_lai > 0
                    AND l.id_trang_thai_lo NOT IN ('TTL03', 'TTL05')
                LEFT JOIN gia_ban_hien_tai g ON l.id_lo = g.id_lo
                LEFT JOIN khuyen_mai km ON l.id_km = km.id_km
                    AND km.trang_thai_km = 'Đang diễn ra'
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

    public function countAllProducts($keyword = '', $categoryId = '') {
        $sql = "SELECT COUNT(DISTINCT h.id_hh) as total 
                FROM hang_hoa h
                LEFT JOIN loai_hang_hoa lhh ON h.id_loai2 = lhh.id_loai2
                WHERE 1=1";
        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND (h.id_hh LIKE :keyword OR h.ten_hh LIKE :keyword)";
            $params[':keyword'] = '%' . $keyword . '%';
        }

        if (!empty($categoryId)) {
            $sql .= " AND lhh.id_dm = :category_id"; // ← THÊM lọc danh mục
            $params[':category_id'] = $categoryId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0;
    }

    // ============================================
    // 3. CÁC PHƯƠNG THỨC CHO ADMIN
    // ============================================
    public function getAllProducts($keyword = '', $limit = 20, $offset = 0, $categoryId = '') {
        $sql = "SELECT h.id_hh, h.ten_hh, h.link_anh, h.duoc_phep_ban,
                    lhh.ten_loai, dvt.dvt, g.gia_hien_tai,
                    MIN(l.hsd_lo) as hsd_lo, 
                    SUM(l.so_luong_con_lai) as tong_ton,
                    (SELECT id_lo FROM lo_hang 
                        WHERE id_hh = h.id_hh AND so_luong_con_lai > 0 
                        ORDER BY hsd_lo ASC LIMIT 1) as id_lo
                FROM hang_hoa h
                LEFT JOIN loai_hang_hoa lhh ON h.id_loai2 = lhh.id_loai2
                LEFT JOIN dvt ON h.id_dvt = dvt.id_dvt
                LEFT JOIN lo_hang l ON h.id_hh = l.id_hh AND l.so_luong_con_lai > 0
                LEFT JOIN gia_ban_hien_tai g ON l.id_lo = g.id_lo 
                WHERE 1=1";

        if (!empty($keyword)) {
            $sql .= " AND (h.id_hh LIKE :keyword OR h.ten_hh LIKE :keyword)";
        }

        if (!empty($categoryId)) {
            $sql .= " AND lhh.id_dm = :category_id"; // ← THÊM lọc danh mục
        }

        $sql .= " GROUP BY h.id_hh ORDER BY h.id_hh DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        if (!empty($keyword)) $stmt->bindValue(':keyword', '%' . $keyword . '%');
        if (!empty($categoryId)) $stmt->bindValue(':category_id', $categoryId);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function getProductByIdForAdmin($productId) {
        $sql = "SELECT h.*, 
               g.gia_hien_tai,
               dvt.dvt,
               lhh.ten_loai
        FROM hang_hoa h
        LEFT JOIN dvt ON h.id_dvt = dvt.id_dvt
        LEFT JOIN loai_hang_hoa lhh ON h.id_loai2 = lhh.id_loai2
        LEFT JOIN lo_hang l ON h.id_hh = l.id_hh
        LEFT JOIN gia_ban_hien_tai g ON l.id_lo = g.id_lo 
            AND g.id_td = (SELECT id_td FROM thoi_diem WHERE NOW() BETWEEN ngay_bd_gia_ban AND ngay_kt_gia_ban LIMIT 1)
        WHERE h.id_hh = ?
        ORDER BY l.hsd_lo ASC LIMIT 1"; // Lấy giá của lô sắp hết hạn nhất
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy tất cả các lô hàng còn tồn của 1 sản phẩm (Dùng cho AJAX Modal)
     */
    public function getBatchesByProductId($productId) {
        $sql = "SELECT 
                    lh.*,
                    ttl.ten_trang_thai_lo, 
                    pn.ngay_lap_phieu_nhap,
                    km.ten_km
                FROM lo_hang lh
                LEFT JOIN trang_thai_lo_hang ttl 
                    ON lh.id_trang_thai_lo = ttl.id_trang_thai_lo
                LEFT JOIN chi_tiet_phieu_nhap ct 
                    ON lh.id_lo = ct.id_lo
                LEFT JOIN phieu_nhap pn 
                    ON ct.id_pn = pn.id_pn
                LEFT JOIN khuyen_mai km 
                    ON lh.id_km = km.id_km
                WHERE lh.id_hh = :id_hh 
                    AND lh.so_luong_con_lai > 0
                ORDER BY lh.hsd_lo ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_hh' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================================
    // 3. CÁC PHƯƠNG THỨC ADMIN (CREATE - UPDATE - DELETE) (MỚI)
    // ============================================
    public function generateNewId() {
        $sql = "SELECT MAX(id_hh) as max_id FROM hang_hoa";
        $stmt = $this->db->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $newId = ($row && $row['max_id']) ? (int)$row['max_id'] + 1 : 1;
        return str_pad($newId, 5, '0', STR_PAD_LEFT);
    }

    public function createProduct($data) {
        $sql = "INSERT INTO hang_hoa (id_hh, id_loai2, id_dvt, ten_hh, link_anh, mo_ta_hh, duoc_phep_ban, la_hang_sx) 
                VALUES (:id, :lhh, :dvt, :ten, :anh, :mota, :ban, :hsx)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'   => $data['id_hh'],
            ':lhh'  => $data['id_loai2'], // Đã sửa key từ id_lhh sang id_loai2
            ':dvt'  => $data['id_dvt'],
            ':ten'  => $data['ten_hh'],
            ':anh'  => $data['link_anh'],
            ':mota' => $data['mo_ta_hh'],
            ':ban'  => $data['duoc_phep_ban'],
            ':hsx'  => $data['la_hang_sx']
        ]);
    }
    private function getCurrentTimeId() {
        $stmt = $this->db->prepare("SELECT id_td FROM thoi_diem WHERE NOW() BETWEEN ngay_bd_gia_ban AND ngay_kt_gia_ban LIMIT 1");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['id_td'] : 'TD003'; 
    }
    public function updateProduct($id, $data) {
        $sql = "UPDATE hang_hoa SET 
            id_loai2 = :lhh, id_dvt = :dvt, ten_hh = :ten,
            mo_ta_hh = :mota, duoc_phep_ban = :ban, 
            link_anh = :anh, phan_tram_loi_nhuan = :ptln
        WHERE id_hh = :id";
        return $this->db->prepare($sql)->execute([
            ':lhh'  => $data['id_loai2'],
            ':dvt'  => $data['id_dvt'],
            ':ten'  => $data['ten_hh'],
            ':mota' => $data['mo_ta_hh'],
            ':ban'  => $data['duoc_phep_ban'],
            ':anh'  => $data['link_anh'],
            ':ptln' => $data['phan_tram_loi_nhuan'] ?? 30,
            ':id'   => $id
        ]);
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
        return $this->db->query("SELECT * FROM loai_hang_hoa ORDER BY ten_loai ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllDVT() {
        return $this->db->query("SELECT * FROM dvt ORDER BY dvt ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllKhuyenMai() {
        return $this->db->query("SELECT * FROM khuyen_mai WHERE trang_thai_km != 0 ORDER BY ten_km ASC")->fetchAll(PDO::FETCH_ASSOC);
    }
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
    public function insertPrice($productId, $price) {
        $timeId = $this->getCurrentTimeId();
        
        $stmt = $this->db->prepare("SELECT id_lo FROM lo_hang WHERE id_hh = ? ORDER BY hsd_lo DESC LIMIT 1");
        $stmt->execute([$productId]);
        $lo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$lo) return false;
        
        // SỬA: Dùng INSERT ... ON DUPLICATE KEY UPDATE thay vì INSERT thuần
        $sql = "INSERT INTO gia_ban_hien_tai (id_lo, id_td, gia_hien_tai) 
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE gia_hien_tai = VALUES(gia_hien_tai)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$lo['id_lo'], $timeId, $price]);
    }
    public function updatePrice($productId, $price) {
        $timeId = $this->getCurrentTimeId();
        
        $stmt = $this->db->prepare("SELECT id_lo FROM lo_hang WHERE id_hh = ? ORDER BY hsd_lo DESC LIMIT 1");
        $stmt->execute([$productId]);
        $lo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$lo) return $this->insertPrice($productId, $price);

        $sql = "UPDATE gia_ban_hien_tai SET gia_hien_tai = ? WHERE id_lo = ? AND id_td = ?";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([$price, $lo['id_lo'], $timeId]);

        return ($stmt->rowCount() > 0) ? true : $this->insertPrice($productId, $price);
    }
    public function applyPromotionToCategory($promoId, $categoryId) {
        $sql = "UPDATE hang_hoa SET id_km = ? WHERE id_loai2 = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$promoId, $categoryId]);
    }

    /**
     * Tính giá vốn bình quân của các lô còn hàng
     */
    private function getAverageCostPrice($productId) {
        // Lấy trung bình cộng gia_von_nhap từ các lô hàng còn số lượng
        $sql = "SELECT AVG(gia_von_nhap) as avg_cost 
                FROM lo_hang 
                WHERE id_hh = :id_hh AND so_luong_con_lai > 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_hh' => $productId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Nếu không có giá vốn (NULL), mặc định trả về 0 hoặc một giá trị an toàn
        return $result['avg_cost'] ?? 0;
    }

    public function updatePriceByProfitMargin($productId) {
        // 1. Lấy phần trăm lợi nhuận mong muốn từ bảng hang_hoa
        $stmtHh = $this->db->prepare("SELECT phan_tram_loi_nhuan FROM hang_hoa WHERE id_hh = ?");
        $stmtHh->execute([$productId]);
        $hh = $stmtHh->fetch(PDO::FETCH_ASSOC);
        
        // Mặc định là 30% nếu không có dữ liệu
        $profitMargin = isset($hh['phan_tram_loi_nhuan']) ? ($hh['phan_tram_loi_nhuan'] / 100) : 0.3;

        // 2. Tính giá vốn bình quân
        $avgCost = $this->getAverageCostPrice($productId);
        if ($avgCost <= 0) return false; // Không có giá vốn thì không tính được giá bán

        // 3. Tính giá bán mới: Giá vốn + (Giá vốn * % Lợi nhuận)
        // Hoặc dùng công thức: Giá vốn * (1 + % Lợi nhuận)
        $calculatedPrice = $avgCost * (1 + $profitMargin);

        // Làm tròn đến hàng trăm cho đẹp (Ví dụ: 35678 -> 35700)
        $calculatedPrice = ceil($calculatedPrice / 100) * 100;

        // 4. Tìm lô hàng mới nhất để gắn giá vào (logic cũ của bạn)
        $timeId = $this->getCurrentTimeId();
        $stmtLo = $this->db->prepare("SELECT id_lo FROM lo_hang WHERE id_hh = ? ORDER BY hsd_lo DESC LIMIT 1");
        $stmtLo->execute([$productId]);
        $lo = $stmtLo->fetch(PDO::FETCH_ASSOC);

        if (!$lo) return false;

        // 5. Cập nhật vào bảng gia_ban_hien_tai
        $sql = "INSERT INTO gia_ban_hien_tai (id_lo, id_td, gia_hien_tai) 
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE gia_hien_tai = VALUES(gia_hien_tai)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$lo['id_lo'], $timeId, $calculatedPrice]);
    }
}
