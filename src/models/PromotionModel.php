<?php

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class PromotionModel extends BaseModel {

    // Lấy tất cả khuyến mãi (Có tìm kiếm)
    public function getAllPromotions($search = '') {
        $sql = "SELECT * FROM khuyen_mai";
        $params = [];

        if (!empty($search)) {
            $sql .= " WHERE TEN_KM LIKE ? OR ID_KM LIKE ?";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " ORDER BY ID_KM DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy 1 khuyến mãi theo ID
    public function getPromotionById($id) {
        $stmt = $this->db->prepare("SELECT * FROM khuyen_mai WHERE ID_KM = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thêm mới (Tự sinh mã KMxxx)
    public function createPromotion($data) {
        // 1. Sinh ID tự động (KM001, KM002...)
        $stmt = $this->db->query("SELECT MAX(ID_KM) as max_id FROM khuyen_mai");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $maxId = $row['max_id'];

        if ($maxId) {
            $num = (int)substr($maxId, 2); // Lấy số sau chữ KM
            $newNum = $num + 1;
            $newId = 'KM' . str_pad($newNum, 3, '0', STR_PAD_LEFT); // 3 số (001)
        } else {
            $newId = 'KM001';
        }

        // 2. Thêm vào DB
        try {
            $sql = "INSERT INTO khuyen_mai (ID_KM, TEN_KM, PHAN_TRAM_KM, NGAY_BD_KM, NGAY_KT_KM, TRANG_THAI_KM) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $newId, 
                $data['ten_km'], 
                $data['phan_tram_km'], 
                $data['ngay_bd'], 
                $data['ngay_kt'], 
                $data['trang_thai']
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    // Cập nhật
    public function updatePromotion($id, $data) {
        try {
            $sql = "UPDATE khuyen_mai 
                    SET TEN_KM = ?, PHAN_TRAM_KM = ?, NGAY_BD_KM = ?, NGAY_KT_KM = ?, TRANG_THAI_KM = ? 
                    WHERE ID_KM = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $data['ten_km'], 
                $data['phan_tram_km'], 
                $data['ngay_bd'], 
                $data['ngay_kt'], 
                $data['trang_thai'],
                $id
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    // Xóa (Có kiểm tra ràng buộc với Hàng hóa)
    public function deletePromotion($id) {
        // Kiểm tra xem có sản phẩm nào đang dùng mã KM này không
        $stmtCheck = $this->db->prepare("SELECT COUNT(*) as count FROM hang_hoa WHERE ID_KM = ?");
        $stmtCheck->execute([$id]);
        $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($row['count'] > 0) {
            return false; // Không cho xóa
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM khuyen_mai WHERE ID_KM = ?");
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            return false;
        }
    }
}