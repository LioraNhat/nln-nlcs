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
     * Lấy tất cả loại sản phẩm (Product Types)
     */
    public function getAllProductTypes() {
        $stmt = $this->db->query("SELECT * FROM loai_hang_hoa ORDER BY ID_LHH ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    public function updateCategory($id_dm, $ten_dm)
    {
        try {
            $stmt = $this->db->prepare("UPDATE danh_muc SET TEN_DM = ? WHERE ID_DM = ?");
            return $stmt->execute([$ten_dm, $id_dm]);
        } catch (\PDOException $e) {
            error_log($e->getMessage()); // Ghi lại lỗi để debug
            return false;
        }
    }

    /**
     * HÀM MỚI: Xóa một Danh mục (DM)
     * Trả về true nếu thành công, false nếu thất bại
     */
    public function deleteCategory($id_dm)
    {
        try {
            // QUAN TRỌNG: Cần kiểm tra xem danh mục này có
            // đang được sử dụng trong 'loai_hang_hoa' không trước khi xóa.
            // Tạm thời chúng ta sẽ xóa trực tiếp, nhưng bạn nên thêm logic kiểm tra
            
            $stmt = $this->db->prepare("DELETE FROM danh_muc WHERE ID_DM = ?");
            return $stmt->execute([$id_dm]);
        } catch (\PDOException $e) {
            error_log($e->getMessage()); // Ghi lại lỗi để debug
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
     * HÀM MỚI: Cập nhật một Loại Hàng Hóa (LHH)
     */
    public function updateProductType($id_lhh, $ten_lhh, $id_dm)
    {
        try {
            $stmt = $this->db->prepare("UPDATE loai_hang_hoa SET TEN_LHH = ?, ID_DM = ? WHERE ID_LHH = ?");
            return $stmt->execute([$ten_lhh, $id_dm, $id_lhh]);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * HÀM MỚI: Xóa một Loại Hàng Hóa (LHH)
     */
    public function deleteProductType($id_lhh)
    {
        try {
            // Cảnh báo: Cần kiểm tra xem LHH này
            // có đang được sử dụng trong 'hang_hoa' không trước khi xóa.
            
            $stmt = $this->db->prepare("DELETE FROM loai_hang_hoa WHERE ID_LHH = ?");
            return $stmt->execute([$id_lhh]);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * HÀM MỚI: Lấy tất cả LHH KÈM THEO Tên Danh mục cha
     */
    public function getAllProductTypesWithCategory()
    {
        try {
            $stmt = $this->db->query("
                SELECT lhh.ID_LHH, lhh.TEN_LHH, dm.TEN_DM 
                FROM loai_hang_hoa lhh
                LEFT JOIN danh_muc dm ON lhh.ID_DM = dm.ID_DM
                ORDER BY lhh.ID_LHH ASC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
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
