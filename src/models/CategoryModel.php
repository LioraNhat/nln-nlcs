<?php

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class CategoryModel extends BaseModel {

    /**
     * Lấy tất cả danh mục (Categories)
     */
    public function getAllCategories() {
        $stmt = $this->db->query("SELECT * FROM danh_muc ORDER BY ID_DM ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy 1 danh mục theo ID (Để sửa)
     */
    public function getCategoryById($id) {
        $stmt = $this->db->prepare("SELECT * FROM danh_muc WHERE ID_DM = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Thêm danh mục mới (Tự động sinh mã DMxx)
     */
    public function createCategory($ten_dm) {
        // 1. Lấy ID lớn nhất hiện tại
        $stmt = $this->db->query("SELECT MAX(ID_DM) as max_id FROM danh_muc");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $maxId = $row['max_id'];

        // 2. Tự động tăng số (VD: DM05 -> DM06)
        if ($maxId) {
            $num = (int)substr($maxId, 2); // Lấy số sau chữ DM
            $newNum = $num + 1;
            $newId = 'DM' . str_pad($newNum, 2, '0', STR_PAD_LEFT);
        } else {
            $newId = 'DM01';
        }

        // 3. Thêm vào CSDL
        try {
            $stmt = $this->db->prepare("INSERT INTO danh_muc (ID_DM, TEN_DM) VALUES (?, ?)");
            return $stmt->execute([$newId, $ten_dm]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Lấy tất cả loại sản phẩm (Cho dropdown menu)
     */
    public function getAllProductTypes() {
        $stmt = $this->db->query("SELECT * FROM loai_hang_hoa ORDER BY ID_LHH ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy 1 loại hàng theo ID
     */
    public function findProductTypeById($id) {
        $stmt = $this->db->prepare("SELECT * FROM loai_hang_hoa WHERE ID_LHH = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Thêm loại hàng mới (Tự động sinh mã LHHxx)
     */
    public function createProductType($ten_lhh, $id_dm) {
        // 1. Lấy ID lớn nhất
        $stmt = $this->db->query("SELECT MAX(ID_LHH) as max_id FROM loai_hang_hoa");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $maxId = $row['max_id'];

        // 2. Tự động tăng ID (VD: LHH09 -> LHH10)
        if ($maxId) {
            $num = (int)substr($maxId, 3); // Lấy số sau chữ LHH
            $newNum = $num + 1;
            $newId = 'LHH' . str_pad($newNum, 2, '0', STR_PAD_LEFT);
        } else {
            $newId = 'LHH01';
        }

        // 3. Insert
        try {
            $stmt = $this->db->prepare("INSERT INTO loai_hang_hoa (ID_LHH, TEN_LHH, ID_DM) VALUES (?, ?, ?)");
            return $stmt->execute([$newId, $ten_lhh, $id_dm]);
        } catch (\PDOException $e) {
            return false;
        }
    }



    /**
     * Lấy loại sản phẩm theo ID danh mục
     */
    public function getProductTypesByCategoryId($categoryId) {
        $stmt = $this->db->prepare("SELECT * FROM loai_hang_hoa WHERE ID_DM = ?");
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * HÀM MỚI: Tìm một Danh mục (DM) bằng ID
     */
    public function findCategoryById($categoryId) {
        $stmt = $this->db->prepare("SELECT * FROM danh_muc WHERE ID_DM = ?");
        $stmt->execute([$categoryId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * HÀM MỚI: Tìm thông tin Loại Hàng Hóa VÀ Danh Mục cha của nó
     */
    public function findProductTypeAndCategoryById($id_lhh) {
        $stmt = $this->db->prepare("
            SELECT 
                lhh.ID_LHH, lhh.TEN_LHH,
                dm.ID_DM, dm.TEN_DM
            FROM loai_hang_hoa lhh
            INNER JOIN danh_muc dm ON lhh.ID_DM = dm.ID_DM
            WHERE lhh.ID_LHH = ?
        ");
        $stmt->execute([$id_lhh]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ////ADMIN

    /**
     * HÀM MỚI: Lưu một Danh mục (DM) mới
     * Trả về true nếu thành công, false nếu thất bại
     */
    public function saveCategory($id_dm, $ten_dm)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO danh_muc (ID_DM, TEN_DM) VALUES (?, ?)");
            return $stmt->execute([$id_dm, $ten_dm]);
        } catch (\PDOException $e) {
            error_log($e->getMessage()); // Ghi lại lỗi để debug
            return false;
        }
    }

    /**
     * HÀM MỚI: Cập nhật một Danh mục (DM)
     * Trả về true nếu thành công, false nếu thất bại
     */
    public function updateCategory($id, $ten_dm)
    {
        try {
            $stmt = $this->db->prepare("UPDATE danh_muc SET TEN_DM = ? WHERE ID_DM = ?");
            return $stmt->execute([$ten_dm, $id]);
        } catch (\PDOException $e) {
            error_log($e->getMessage()); // Ghi lại lỗi để debug
            return false;
        }
    }

    /**
     * Xóa danh mục (Kiểm tra ràng buộc)
     */
    public function deleteCategory($id) {
        // Kiểm tra xem có loại hàng nào đang dùng danh mục này không
        $stmtCheck = $this->db->prepare("SELECT COUNT(*) as count FROM loai_hang_hoa WHERE ID_DM = ?");
        $stmtCheck->execute([$id]);
        $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($row['count'] > 0) {
            return false; // Không cho xóa vì còn chứa loại hàng
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM danh_muc WHERE ID_DM = ?");
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * =============================================
     * HÀM MỚI: CRUD CHO LOẠI HÀNG HÓA (LHH)
     * =============================================
     */

    /**
     * HÀM MỚI: Lưu một Loại Hàng Hóa (LHH) mới
     */
    public function saveProductType($id_lhh, $ten_lhh, $id_dm)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO loai_hang_hoa (ID_LHH, TEN_LHH, ID_DM) VALUES (?, ?, ?)");
            return $stmt->execute([$id_lhh, $ten_lhh, $id_dm]);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật loại hàng
     */
    public function updateProductType($id, $ten_lhh, $id_dm) {
        try {
            $stmt = $this->db->prepare("UPDATE loai_hang_hoa SET TEN_LHH = ?, ID_DM = ? WHERE ID_LHH = ?");
            return $stmt->execute([$ten_lhh, $id_dm, $id]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Xóa loại hàng (Kiểm tra ràng buộc sản phẩm)
     */
    public function deleteProductType($id) {
        // Kiểm tra xem có sản phẩm nào thuộc loại hàng này không
        $stmtCheck = $this->db->prepare("SELECT COUNT(*) as count FROM hang_hoa WHERE ID_LHH = ?");
        $stmtCheck->execute([$id]);
        $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($row['count'] > 0) {
            return false; // Không cho xóa nếu đã có sản phẩm
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM loai_hang_hoa WHERE ID_LHH = ?");
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    // ======================================================
    // PHẦN LOẠI HÀNG HÓA (SUB-CATEGORIES / PRODUCT TYPES)
    // ======================================================

    /**
     * Lấy tất cả loại hàng kèm tên danh mục cha
     */
    public function getAllProductTypesWithCategory() {
        $sql = "SELECT lhh.ID_LHH, lhh.TEN_LHH, dm.TEN_DM 
                FROM loai_hang_hoa lhh
                LEFT JOIN danh_muc dm ON lhh.ID_DM = dm.ID_DM
                ORDER BY lhh.ID_LHH ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * =============================================
     * HÀM MỚI: LẤY SỐ LIỆU THỐNG KÊ (DASHBOARD)
     * =============================================
     */

    /**
     * HÀM MỚI: Đếm tổng số Hàng Hóa (Sản phẩm)
     */
    public function getTotalProducts()
    {
        // Đếm tổng số hàng hóa trong CSDL mới
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM hang_hoa");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    /**
     * HÀM MỚI: Đếm số đơn hàng Chờ xử lý
     */
    public function getPendingOrdersCount()
    {
        // Đếm dựa trên bảng don_hang_hien_tai của CSDL mới
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM don_hang_hien_tai WHERE TRANG_THAI_DHHT = 'Chờ xử lý'");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    /**
     * HÀM MỚI: Tính tổng doanh số (của các đơn Chờ xử lý)
     */
    public function getPendingSalesTotal()
    {
        // Logic này khớp với code cũ (tính tổng tiền các đơn CHƯA xử lý)
        $stmt = $this->db->query("
            SELECT SUM(SO_TIEN_THANH_TOAN) as total 
            FROM don_hang 
            WHERE ID_DH IN (
                SELECT ID_DH FROM don_hang_hien_tai WHERE TRANG_THAI_DHHT = 'Chờ xử lý'
            )
        ");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }
}
