<?php

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class ProductModel extends BaseModel {

    // ============================================
    // 1. CÁC PHƯƠNG THỨC CHO USER (GIỮ NGUYÊN)
    // ============================================

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

    /**
     * Lấy danh sách sản phẩm đang khuyến mãi (Cho trang chủ)
     */
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

    /**
     * Tìm kiếm sản phẩm theo tên (Cho trang tìm kiếm User)
     * (Tôi thêm hàm này luôn để tránh lỗi tiếp theo nếu bạn tìm kiếm)
     */
    public function searchProductsByName($searchTerm, $filters = [], $limit = 12, $offset = 0) {
        $select = "SELECT h.ID_HH, h.TEN_HH, h.link_anh, g.GIA_HIEN_TAI, km.PHAN_TRAM_KM";
        $from = " FROM hang_hoa h
                  INNER JOIN loai_hang_hoa lhh ON h.ID_LHH = lhh.ID_LHH"
               . $this->getPriceJoins() .
               " " . $this->getPromoJoins();
        
        $whereClauses = [
            "(NOW() BETWEEN t.NGAY_BD_GIA_BAN AND t.NGAY_KT_GIA_BAN)",
            "(h.TEN_HH LIKE :search_term)",
            "(h.DUOC_PHEP_BAN = 1)"
        ];
        $params = [':search_term' => '%' . $searchTerm . '%'];

        // Logic sắp xếp giá (nếu có)
        $orderBy = "ORDER BY h.TEN_HH ASC";
        
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

    public function getProductsByCategoryId($categoryId, $filters = [], $limit = 12, $offset = 0) {
        $select = "SELECT h.ID_HH, h.TEN_HH, h.link_anh, g.GIA_HIEN_TAI, km.PHAN_TRAM_KM";
        $from = " FROM hang_hoa h
                  INNER JOIN loai_hang_hoa lhh ON h.ID_LHH = lhh.ID_LHH"
               . $this->getPriceJoins() .
               " " . $this->getPromoJoins();
        
        $whereClauses = [
            "(NOW() BETWEEN t.NGAY_BD_GIA_BAN AND t.NGAY_KT_GIA_BAN)",
            "(lhh.ID_DM = :id_dm)",
            "(h.DUOC_PHEP_BAN = 1)"
        ];
        $params = [':id_dm' => $categoryId];

        // ... (Giữ nguyên logic filter cũ của bạn) ...
        // Để code gọn mình ẩn bớt phần filter đã có, bạn giữ nguyên như cũ nhé
        
        $sql = $select . $from . " WHERE " . implode(" AND ", $whereClauses) 
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

    // (Giữ các hàm User khác như getProductsByProductType, searchProductsByName...)
    
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

    public function getAllProducts($keyword = '', $limit = 20, $offset = 0) {
        // Query đã được tối ưu để lấy cả giá bán
        $sql = "SELECT 
                    h.ID_HH, h.TEN_HH, h.link_anh, h.SO_LUONG_TON_HH, h.DUOC_PHEP_BAN, h.HSD,
                    lhh.TEN_LHH,
                    dvt.DVT,
                    g.GIA_HIEN_TAI
                FROM hang_hoa h
                LEFT JOIN loai_hang_hoa lhh ON h.ID_LHH = lhh.ID_LHH
                LEFT JOIN dvt ON h.ID_DVT = dvt.ID_DVT
                -- Join để lấy giá hiện tại (Giả sử TD003 là thời điểm hiện tại hoặc lấy mới nhất)
                LEFT JOIN gia_ban_hien_tai g ON h.ID_HH = g.ID_HH 
                    AND g.ID_TD = (SELECT ID_TD FROM thoi_diem WHERE NOW() BETWEEN NGAY_BD_GIA_BAN AND NGAY_KT_GIA_BAN LIMIT 1)";
        
        $params = [];
        if (!empty($keyword)) {
            $sql .= " WHERE (h.ID_HH LIKE :keyword OR h.TEN_HH LIKE :keyword)";
            $params[':keyword'] = '%' . $keyword . '%';
        }

        $sql .= " ORDER BY h.ID_HH DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
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

            // 1. Lấy ID từ Controller gửi sang (Đã đồng bộ với tên ảnh)
            // KHÔNG tự sinh ID ở đây nữa
            $idHH = $data['id_hh']; 

            // 2. Lấy ID Thời điểm hiện tại (TD003...)
            $idTD = $this->getCurrentTimeId(); 
            
            // Nếu không tìm thấy thời điểm giá phù hợp, báo lỗi hoặc dùng mặc định
            if (!$idTD) {
                // Tùy chọn: throw new Exception("Chưa thiết lập Thời điểm giá cho ngày hôm nay!");
                // Hoặc gán cứng nếu muốn test:
                $idTD = 'TD003'; 
            }

            // 3. Insert vào bảng HANG_HOA
            $sqlHH = "INSERT INTO hang_hoa (ID_HH, ID_LHH, ID_DVT, ID_KM, TEN_HH, link_anh, MO_TA_HH, SO_LUONG_TON_HH, DUOC_PHEP_BAN, LA_HANG_SX, HSD) 
                      VALUES (:id, :lhh, :dvt, :km, :ten, :anh, :mota, :sl, :ban, :hsx, :hsd)";
            
            $stmtHH = $this->db->prepare($sqlHH);
            $stmtHH->execute([
                ':id' => $idHH, // Dùng biến $idHH
                ':lhh' => $data['id_lhh'],
                ':dvt' => $data['id_dvt'],
                ':km' => !empty($data['id_km']) ? $data['id_km'] : NULL,
                ':ten' => $data['ten_hh'],
                ':anh' => $data['link_anh'],
                ':mota' => $data['mo_ta_hh'],
                ':sl' => $data['so_luong_ton'],
                ':ban' => isset($data['duoc_phep_ban']) ? 1 : 0,
                ':hsx' => isset($data['la_hang_sx']) ? 1 : 0,
                ':hsd' => $data['hsd']
            ]);

            // 4. Insert vào bảng GIA_BAN_HIEN_TAI
            if (!empty($data['gia_ban'])) {
                $sqlGia = "INSERT INTO gia_ban_hien_tai (ID_HH, ID_TD, GIA_HIEN_TAI) VALUES (:id, :td, :gia)";
                $stmtGia = $this->db->prepare($sqlGia);
                $stmtGia->execute([
                    ':id' => $idHH,
                    ':td' => $idTD,
                    ':gia' => $data['gia_ban']
                ]);
            }

            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            // error_log($e->getMessage()); // Nên bật log để debug
            return false;
        }
    }

    /**
     * Lấy ID Thời điểm hiện tại (Để lưu giá)
     */
    private function getCurrentTimeId() {
        // Lấy giờ hiện tại của server PHP (Chính xác theo múi giờ bạn cài đặt)
        $today = date('Y-m-d H:i:s'); 
        
        $sql = "SELECT ID_TD FROM thoi_diem WHERE ? BETWEEN NGAY_BD_GIA_BAN AND NGAY_KT_GIA_BAN LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$today]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Trả về ID tìm thấy, hoặc mặc định TD003
        return $row ? $row['ID_TD'] : 'TD003'; 
    }

    /**
     * Cập nhật sản phẩm
     */
    public function updateProduct($id, $data) {
        try {
            $this->db->beginTransaction();

            // 1. Update bảng HANG_HOA
            $sqlHH = "UPDATE hang_hoa SET 
                        ID_LHH = :lhh, 
                        ID_DVT = :dvt, 
                        ID_KM = :km, 
                        TEN_HH = :ten, 
                        MO_TA_HH = :mota, 
                        SO_LUONG_TON_HH = :sl, 
                        DUOC_PHEP_BAN = :ban, 
                        LA_HANG_SX = :hsx, 
                        HSD = :hsd
                      WHERE ID_HH = :id";
            
            $params = [
                ':lhh' => $data['id_lhh'],
                ':dvt' => $data['id_dvt'],
                ':km' => !empty($data['id_km']) ? $data['id_km'] : NULL,
                ':ten' => $data['ten_hh'],
                ':mota' => $data['mo_ta_hh'],
                ':sl' => $data['so_luong_ton'],
                ':ban' => isset($data['duoc_phep_ban']) ? 1 : 0,
                ':hsx' => isset($data['la_hang_sx']) ? 1 : 0,
                ':hsd' => $data['hsd'],
                ':id' => $id
            ];

            // Nếu có ảnh mới thì cập nhật, không thì giữ nguyên
            if (!empty($data['link_anh'])) {
                $sqlHH = str_replace("WHERE ID_HH", ", link_anh = :anh WHERE ID_HH", $sqlHH);
                $params[':anh'] = $data['link_anh'];
            }

            $stmtHH = $this->db->prepare($sqlHH);
            $stmtHH->execute($params);

            // 2. Update bảng GIA_BAN_HIEN_TAI (Hoặc insert nếu chưa có)
            if (isset($data['gia_ban'])) {
                $idTD = $this->getCurrentTimeId();
                
                // Kiểm tra xem đã có giá chưa
                $check = $this->db->prepare("SELECT * FROM gia_ban_hien_tai WHERE ID_HH = ? AND ID_TD = ?");
                $check->execute([$id, $idTD]);
                
                if ($check->rowCount() > 0) {
                    $sqlUpdGia = "UPDATE gia_ban_hien_tai SET GIA_HIEN_TAI = ? WHERE ID_HH = ? AND ID_TD = ?";
                    $this->db->prepare($sqlUpdGia)->execute([$data['gia_ban'], $id, $idTD]);
                } else {
                    $sqlInsGia = "INSERT INTO gia_ban_hien_tai (ID_HH, ID_TD, GIA_HIEN_TAI) VALUES (?, ?, ?)";
                    $this->db->prepare($sqlInsGia)->execute([$id, $idTD, $data['gia_ban']]);
                }
            }

            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Xóa sản phẩm (Thực chất là Ngừng kinh doanh để bảo toàn lịch sử đơn hàng)
     */
    public function deleteProduct($id) {
        try {
            // Cách 1: Thử xóa vĩnh viễn (nếu sản phẩm MỚI TINH chưa có dữ liệu liên quan)
            // Xóa giá trước vì nó ràng buộc với hàng hóa
            $this->db->prepare("DELETE FROM gia_ban_hien_tai WHERE ID_HH = ?")->execute([$id]);
            $this->db->prepare("DELETE FROM chi_tiet_gio_hang WHERE ID_HH = ?")->execute([$id]);
            
            // Thử xóa hàng hóa
            $sql = "DELETE FROM hang_hoa WHERE ID_HH = ?";
            $stmt = $this->db->prepare($sql);
            
            if ($stmt->execute([$id])) {
                return true; // Xóa thành công (bay màu hẳn)
            }
        } catch (\Exception $e) {
            // Nếu lỗi (do dính Đơn hàng), ta chuyển sang phương án 2: ẨN SẢN PHẨM
        }

        // Cách 2: Xóa mềm (Ẩn đi) nếu không xóa cứng được
        $sqlSoft = "UPDATE hang_hoa SET DUOC_PHEP_BAN = 0 WHERE ID_HH = ?";
        $stmtSoft = $this->db->prepare($sqlSoft);
        return $stmtSoft->execute([$id]);
    }

    public function updateProductStatus($productId, $status) {
        $sql = "UPDATE hang_hoa SET DUOC_PHEP_BAN = ? WHERE ID_HH = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $productId]);
    }

    /**
     * Lấy sản phẩm liên quan (cùng loại)
     */
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

    /**
     * Lấy sản phẩm theo Loại Hàng Hóa (User)
     */
    public function getProductsByProductType($productTypeId, $filters = [], $limit = 12, $offset = 0) {
        $select = "SELECT h.ID_HH, h.TEN_HH, h.link_anh, g.GIA_HIEN_TAI, km.PHAN_TRAM_KM";
        $from = " FROM hang_hoa h
                  INNER JOIN loai_hang_hoa lhh ON h.ID_LHH = lhh.ID_LHH"
               . $this->getPriceJoins() .
               " " . $this->getPromoJoins();
        
        $whereClauses = [
            "(NOW() BETWEEN t.NGAY_BD_GIA_BAN AND t.NGAY_KT_GIA_BAN)",
            "(h.ID_LHH = :id_lhh)",
            "(h.DUOC_PHEP_BAN = 1)"
        ];
        $params = [':id_lhh' => $productTypeId];

        // Xử lý lọc giá
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
        
        // Xử lý sắp xếp
        $orderBy = "ORDER BY h.TEN_HH ASC";
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_asc': $orderBy = "ORDER BY g.GIA_HIEN_TAI ASC"; break;
                case 'price_desc': $orderBy = "ORDER BY g.GIA_HIEN_TAI DESC"; break;
                case 'name_asc': $orderBy = "ORDER BY h.TEN_HH ASC"; break;
                case 'name_desc': $orderBy = "ORDER BY h.TEN_HH DESC"; break;
            }
        }

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
     * Dùng để đặt tên ảnh trước khi lưu DB
     */
    public function generateProductId() {
        $stmt = $this->db->query("SELECT MAX(ID_HH) as max_id FROM hang_hoa");
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $maxId = $row['max_id'];

        if ($maxId) {
            // Tăng lên 1 đơn vị (VD: "00005" -> 6)
            $num = intval($maxId) + 1;
        } else {
            $num = 1;
        }

        // Format thành chuỗi 5 ký tự (VD: 00006)
        return str_pad($num, 5, '0', STR_PAD_LEFT);
    }
}
