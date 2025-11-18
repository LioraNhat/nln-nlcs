<?php

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class ProductModel extends BaseModel {

    private function getPriceJoins() {
        return " 
            INNER JOIN gia_ban_hien_tai g ON h.ID_HH = g.ID_HH
            INNER JOIN thoi_diem t ON g.ID_TD = t.ID_TD
        ";
    }

    private function getPromoJoins() {
        return "
            LEFT JOIN khuyen_mai km ON h.ID_KM = km.ID_KM 
            AND km.TRANG_THAI_KM = 'Đang diễn ra'
            AND NOW() BETWEEN km.NGAY_BD_KM AND km.NGAY_KT_KM
        ";
    }

    public function getFeaturedProducts($limit = 6) { 
        $sql = "SELECT h.ID_HH, h.TEN_HH, h.link_anh, g.GIA_HIEN_TAI, km.PHAN_TRAM_KM
                FROM hang_hoa h"
               . $this->getPriceJoins() .
               " " . $this->getPromoJoins() .
               " WHERE (NOW() BETWEEN t.NGAY_BD_GIA_BAN AND t.NGAY_KT_GIA_BAN)
                 AND (h.DUOC_PHEP_BAN = 1)
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findProductById($productId) {
        $sql = "SELECT h.*, g.GIA_HIEN_TAI, km.PHAN_TRAM_KM
                FROM hang_hoa h"
               . $this->getPriceJoins() .
               " " . $this->getPromoJoins() .
               " WHERE (NOW() BETWEEN t.NGAY_BD_GIA_BAN AND t.NGAY_KT_GIA_BAN)
                 AND (h.ID_HH = :id_hh)
                 AND (h.DUOC_PHEP_BAN = 1)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_hh' => $productId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    /**
     * SỬA LẠI HOÀN TOÀN: Lấy sản phẩm theo DANH MỤC (DM)
     * Hỗ trợ lọc giá và sắp xếp
     */
    public function getProductsByCategoryId($categoryId, $filters = [], $limit = 12, $offset = 0) {
        
        // 1. Khởi tạo câu lệnh SQL
        $select = "SELECT h.ID_HH, h.TEN_HH, h.link_anh, g.GIA_HIEN_TAI, km.PHAN_TRAM_KM";
        $from = " FROM hang_hoa h
                  INNER JOIN loai_hang_hoa lhh ON h.ID_LHH = lhh.ID_LHH"
               . $this->getPriceJoins() .
               " " . $this->getPromoJoins();
        
        // 2. Xây dựng mệnh đề WHERE
        $whereClauses = [
            "(NOW() BETWEEN t.NGAY_BD_GIA_BAN AND t.NGAY_KT_GIA_BAN)",
            "(lhh.ID_DM = :id_dm)",
            "(h.DUOC_PHEP_BAN = 1)"
        ];
        $params = [':id_dm' => $categoryId];

        // Thêm Lọc giá (nếu có)
        if (!empty($filters['price'])) {
            $priceRange = explode('-', $filters['price']); // Ví dụ: "100000-200000"
            if (count($priceRange) == 2) {
                $whereClauses[] = "(g.GIA_HIEN_TAI >= :min_price AND g.GIA_HIEN_TAI <= :max_price)";
                $params[':min_price'] = $priceRange[0];
                $params[':max_price'] = $priceRange[1];
            } else if ($filters['price'] == 'under_100k') {
                $whereClauses[] = "(g.GIA_HIEN_TAI < 100000)";
            } else if ($filters['price'] == 'over_400k') {
                $whereClauses[] = "(g.GIA_HIEN_TAI > 400000)";
            }
        }
        
        // 3. Xây dựng mệnh đề ORDER BY (Sắp xếp)
        $orderBy = "ORDER BY h.TEN_HH ASC"; // Mặc định
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_asc':
                    $orderBy = "ORDER BY g.GIA_HIEN_TAI ASC";
                    break;
                case 'price_desc':
                    $orderBy = "ORDER BY g.GIA_HIEN_TAI DESC";
                    break;
                case 'name_asc':
                    $orderBy = "ORDER BY h.TEN_HH ASC";
                    break;
                case 'name_desc':
                    $orderBy = "ORDER BY h.TEN_HH DESC";
                    break;
            }
        }

        // 4. Ghép nối và thực thi
        $sql = $select . $from . " WHERE " . implode(" AND ", $whereClauses) 
               . " " . $orderBy 
               . " LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);

        // Gán tham số (params)
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * SỬA LẠI HOÀN TOÀN: Lấy sản phẩm theo LOẠI HÀNG HÓA (LHH)
     * Hỗ trợ lọc giá và sắp xếp (Nâng cấp)
     */
    public function getProductsByProductType($productTypeId, $filters = [], $limit = 12, $offset = 0) {
        
        // 1. Khởi tạo câu lệnh SQL
        $select = "SELECT h.ID_HH, h.TEN_HH, h.link_anh, g.GIA_HIEN_TAI, km.PHAN_TRAM_KM";
        $from = " FROM hang_hoa h
                  INNER JOIN loai_hang_hoa lhh ON h.ID_LHH = lhh.ID_LHH" // Vẫn cần JOIN
               . $this->getPriceJoins() .
               " " . $this->getPromoJoins();
        
        // 2. Xây dựng mệnh đề WHERE
        $whereClauses = [
            "(NOW() BETWEEN t.NGAY_BD_GIA_BAN AND t.NGAY_KT_GIA_BAN)",
            "(h.ID_LHH = :id_lhh)", // <-- SỬA LẠI: Lọc theo ID_LHH
            "(h.DUOC_PHEP_BAN = 1)"
        ];
        $params = [':id_lhh' => $productTypeId]; // <-- SỬA LẠI: Tham số

        // Thêm Lọc giá (nếu có)
        if (!empty($filters['price'])) {
            $priceRange = explode('-', $filters['price']);
            if (count($priceRange) == 2) {
                $whereClauses[] = "(g.GIA_HIEN_TAI >= :min_price AND g.GIA_HIEN_TAI <= :max_price)";
                $params[':min_price'] = $priceRange[0];
                $params[':max_price'] = $priceRange[1];
            } else if ($filters['price'] == 'under_100k') {
                $whereClauses[] = "(g.GIA_HIEN_TAI < 100000)";
            } else if ($filters['price'] == 'over_400k') {
                $whereClauses[] = "(g.GIA_HIEN_TAI > 400000)";
            }
        }
        
        // 3. Xây dựng mệnh đề ORDER BY (Sắp xếp)
        $orderBy = "ORDER BY h.TEN_HH ASC"; // Mặc định
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_asc':
                    $orderBy = "ORDER BY g.GIA_HIEN_TAI ASC";
                    break;
                case 'price_desc':
                    $orderBy = "ORDER BY g.GIA_HIEN_TAI DESC";
                    break;
                case 'name_asc':
                    $orderBy = "ORDER BY h.TEN_HH ASC";
                    break;
                case 'name_desc':
                    $orderBy = "ORDER BY h.TEN_HH DESC";
                    break;
            }
        }

        // 4. Ghép nối và thực thi
        $sql = $select . $from . " WHERE " . implode(" AND ", $whereClauses) 
               . " " . $orderBy 
               . " LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);

        // Gán tham số (params)
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductsOnSale($limit = 6) {
        $sql = "SELECT h.ID_HH, h.TEN_HH, h.link_anh, g.GIA_HIEN_TAI, km.PHAN_TRAM_KM
                FROM hang_hoa h"
               . $this->getPriceJoins() .
               " INNER JOIN khuyen_mai km ON h.ID_KM = km.ID_KM 
               WHERE (NOW() BETWEEN t.NGAY_BD_GIA_BAN AND t.NGAY_KT_GIA_BAN)
                 AND (h.DUOC_PHEP_BAN = 1)
                 AND (km.TRANG_THAI_KM = 'Đang diễn ra') 
                 AND (NOW() BETWEEN km.NGAY_BD_KM AND km.NGAY_KT_KM)
                ORDER BY RAND()
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRelatedProducts($currentProductId, $categoryId, $limit = 6) {
        $sql = "SELECT h.ID_HH, h.TEN_HH, h.link_anh, g.GIA_HIEN_TAI, km.PHAN_TRAM_KM
                FROM hang_hoa h
                INNER JOIN loai_hang_hoa lhh ON h.ID_LHH = lhh.ID_LHH"
               . $this->getPriceJoins() .
               " " . $this->getPromoJoins() .
               " WHERE (NOW() BETWEEN t.NGAY_BD_GIA_BAN AND t.NGAY_KT_GIA_BAN)
                 AND (lhh.ID_LHH = :category_id)
                 AND (h.ID_HH != :current_product_id)
                 AND (h.DUOC_PHEP_BAN = 1)
                ORDER BY RAND()
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':category_id', $categoryId);
        $stmt->bindValue(':current_product_id', $currentProductId);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchProductsByName($searchTerm, $filters = [], $limit = 12, $offset = 0) {
        
        // 1. Khởi tạo câu lệnh SQL
        $select = "SELECT h.ID_HH, h.TEN_HH, h.link_anh, g.GIA_HIEN_TAI, km.PHAN_TRAM_KM";
        $from = " FROM hang_hoa h
                  INNER JOIN loai_hang_hoa lhh ON h.ID_LHH = lhh.ID_LHH"
               . $this->getPriceJoins() .
               " " . $this->getPromoJoins();
        
        // 2. Xây dựng mệnh đề WHERE
        $whereClauses = [
            "(NOW() BETWEEN t.NGAY_BD_GIA_BAN AND t.NGAY_KT_GIA_BAN)",
            "(h.TEN_HH LIKE :search_term)", // <-- ĐÂY LÀ KHÁC BIỆT CHÍNH
            "(h.DUOC_PHEP_BAN = 1)"
        ];
        $params = [':search_term' => '%' . $searchTerm . '%']; // Thêm % cho LIKE

        // Thêm Lọc giá (nếu có)
        if (!empty($filters['price'])) {
            $priceRange = explode('-', $filters['price']);
            if (count($priceRange) == 2) {
                $whereClauses[] = "(g.GIA_HIEN_TAI >= :min_price AND g.GIA_HIEN_TAI <= :max_price)";
                $params[':min_price'] = $priceRange[0];
                $params[':max_price'] = $priceRange[1];
            } else if ($filters['price'] == 'under_100k') {
                $whereClauses[] = "(g.GIA_HIEN_TAI < 100000)";
            } else if ($filters['price'] == 'over_400k') {
                $whereClauses[] = "(g.GIA_HIEN_TAI > 400000)";
            }
        }
        
        // 3. Xây dựng mệnh đề ORDER BY (Sắp xếp)
        $orderBy = "ORDER BY h.TEN_HH ASC"; // Mặc định
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_asc': $orderBy = "ORDER BY g.GIA_HIEN_TAI ASC"; break;
                case 'price_desc': $orderBy = "ORDER BY g.GIA_HIEN_TAI DESC"; break;
                case 'name_asc': $orderBy = "ORDER BY h.TEN_HH ASC"; break;
                case 'name_desc': $orderBy = "ORDER BY h.TEN_HH DESC"; break;
            }
        }

        // 4. Ghép nối và thực thi
        $sql = $select . $from . " WHERE " . implode(" AND ", $whereClauses) 
               . " " . $orderBy 
               . " LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}