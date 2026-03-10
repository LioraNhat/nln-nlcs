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
            $sql .= " WHERE ten_km LIKE ? OR id_km LIKE ?";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " ORDER BY id_km DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Lấy 1 khuyến mãi theo ID
    public function getPromotionById($id) {
        $stmt = $this->db->prepare("SELECT * FROM khuyen_mai WHERE id_km = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // Thêm mới khuyến mãi (tự sinh mã KM001...)
    public function createPromotion($data) {

        $stmt = $this->db->query("SELECT MAX(id_km) as max_id FROM khuyen_mai");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $maxId = $row['max_id'];

        if ($maxId) {
            $num = (int)substr($maxId, 2);
            $newId = 'KM' . str_pad($num + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newId = 'KM001';
        }

        try {
            $sql = "INSERT INTO khuyen_mai 
                    (id_km, ten_km, phan_tram_km, ngay_bd_km, ngay_kt_km, trang_thai_km) 
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


    // Cập nhật khuyến mãi
    public function updatePromotion($id, $data) {

        try {

            $sql = "UPDATE khuyen_mai 
                    SET ten_km = ?, 
                        phan_tram_km = ?, 
                        ngay_bd_km = ?, 
                        ngay_kt_km = ?, 
                        trang_thai_km = ? 
                    WHERE id_km = ?";

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


    // Xóa khuyến mãi
    public function deletePromotion($id) {

        // Khuyến mãi liên kết với bảng lo_hang
        $stmtCheck = $this->db->prepare("SELECT COUNT(*) as count FROM lo_hang WHERE id_km = ?");
        $stmtCheck->execute([$id]);

        $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($row['count'] > 0) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM khuyen_mai WHERE id_km = ?");
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            return false;
        }
    }


    // Áp dụng khuyến mãi cho loại hàng
    public function applyPromotionToCategory($promoId, $categoryId) {

        try {

            $sql = "UPDATE lo_hang l
                    INNER JOIN hang_hoa h ON l.id_hh = h.id_hh
                    SET l.id_km = ?
                    WHERE h.id_loai2 = ?";

            $stmt = $this->db->prepare($sql);

            return $stmt->execute([$promoId, $categoryId]);

        } catch (\PDOException $e) {
            return false;
        }
    }

}