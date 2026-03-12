<?php

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class CategoryModel extends BaseModel {

    // ======================================================
    // DANH MỤC (CATEGORY)
    // ======================================================

    public function getAllCategories($search = '') {
        $sql = "SELECT * FROM danh_muc";
        $params = [];

        if (!empty($search)) {
            $sql .= " WHERE ten_dm LIKE ? OR id_dm LIKE ?";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " ORDER BY id_dm ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryById($id) {
        $stmt = $this->db->prepare("SELECT * FROM danh_muc WHERE id_dm = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findCategoryById($categoryId) {
        $stmt = $this->db->prepare("SELECT * FROM danh_muc WHERE id_dm = ?");
        $stmt->execute([$categoryId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createCategory($ten_dm) {
        $stmt = $this->db->query("SELECT MAX(id_dm) as max_id FROM danh_muc");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $maxId = $row['max_id'];

        // VỊ TRÍ SỬA: Kiểm tra bảng rỗng trước khi substr
        if (empty($maxId)) {
            $newId = 'DM01';
        } else {
            $num = (int)filter_var($maxId, FILTER_SANITIZE_NUMBER_INT);
            $newId = 'DM' . str_pad($num + 1, 2, '0', STR_PAD_LEFT);
        }
        return $this->saveCategory($newId, $ten_dm);
    }

    public function createProductType($ten_loai, $id_dm) {
        $stmt = $this->db->query("SELECT MAX(id_loai2) as max_id FROM loai_hang_hoa");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $maxId = $row['max_id'];

        // VỊ TRÍ SỬA: Kiểm tra bảng rỗng
        if (empty($maxId)) {
            $newId = 'LHH01';
        } else {
            $num = (int)filter_var($maxId, FILTER_SANITIZE_NUMBER_INT);
            $newId = 'LHH' . str_pad($num + 1, 2, '0', STR_PAD_LEFT);
        }
        return $this->saveProductType($newId, $ten_loai, $id_dm);
    }

    public function saveCategory($id_dm, $ten_dm) {
        try {
            $stmt = $this->db->prepare("INSERT INTO danh_muc (id_dm, ten_dm) VALUES (?, ?)");
            return $stmt->execute([$id_dm, $ten_dm]);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function updateCategory($id, $ten_dm) {
        try {
            // SET ten_dm = ? (vị trí 1), WHERE id_dm = ? (vị trí 2)
            $stmt = $this->db->prepare("UPDATE danh_muc SET ten_dm = ? WHERE id_dm = ?");
            return $stmt->execute([$ten_dm, $id]); // Đúng thứ tự: tên trước, id sau
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function deleteCategory($id) {

        $stmtCheck = $this->db->prepare("SELECT COUNT(*) as count FROM loai_hang_hoa WHERE id_dm = ?");
        $stmtCheck->execute([$id]);
        $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($row['count'] > 0) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM danh_muc WHERE id_dm = ?");
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            return false;
        }
    }


    // ======================================================
    // LOẠI HÀNG HÓA (PRODUCT TYPES)
    // ======================================================

    public function getAllProductTypes() {
        $stmt = $this->db->query("SELECT * FROM loai_hang_hoa ORDER BY id_loai2 ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findProductTypeById($id) {
        $stmt = $this->db->prepare("SELECT * FROM loai_hang_hoa WHERE id_loai2 = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getProductTypesByCategoryId($categoryId) {
        $stmt = $this->db->prepare("SELECT * FROM loai_hang_hoa WHERE id_dm = ?");
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findProductTypeAndCategoryById($id_loai2) {

        $stmt = $this->db->prepare("
            SELECT 
                lhh.id_loai2,
                lhh.ten_loai,
                dm.id_dm,
                dm.ten_dm
            FROM loai_hang_hoa lhh
            INNER JOIN danh_muc dm 
            ON lhh.id_dm = dm.id_dm
            WHERE lhh.id_loai2 = ?
        ");

        $stmt->execute([$id_loai2]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllProductTypesWithCategory($search = '') {

        $sql = "SELECT 
                    lhh.id_loai2,
                    lhh.ten_loai,
                    dm.ten_dm
                FROM loai_hang_hoa lhh
                LEFT JOIN danh_muc dm 
                ON lhh.id_dm = dm.id_dm";

        $params = [];

        if (!empty($search)) {
            $sql .= " WHERE lhh.ten_loai LIKE ? 
                      OR lhh.id_loai2 LIKE ? 
                      OR dm.ten_dm LIKE ?";

            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " ORDER BY lhh.id_loai2 ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveProductType($id_loai2, $ten_loai, $id_dm) {

        try {
            $stmt = $this->db->prepare("
                INSERT INTO loai_hang_hoa 
                (id_loai2, ten_loai, id_dm) 
                VALUES (?, ?, ?)
            ");

            return $stmt->execute([$id_loai2, $ten_loai, $id_dm]);

        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function updateProductType($id, $ten_loai, $id_dm) {

        try {
            $stmt = $this->db->prepare("
                UPDATE loai_hang_hoa 
                SET ten_loai = ?, id_dm = ?
                WHERE id_loai2 = ?
            ");

            return $stmt->execute([$ten_loai, $id_dm, $id]);

        } catch (\PDOException $e) {
            return false;
        }
    }

    public function deleteProductType($id) {

        $stmtCheck = $this->db->prepare("SELECT COUNT(*) as count FROM hang_hoa WHERE id_loai2 = ?");
        $stmtCheck->execute([$id]);
        $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($row['count'] > 0) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM loai_hang_hoa WHERE id_loai2 = ?");
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            return false;
        }
    }

}