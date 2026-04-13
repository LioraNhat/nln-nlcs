<?php

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class PromotionModel extends BaseModel {

    /**
     * Lấy tất cả khuyến mãi (Có tích hợp tìm kiếm)
     */
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

    /**
     * Lấy 1 khuyến mãi theo ID
     */
    public function getPromotionById($id) {
        $stmt = $this->db->prepare("SELECT * FROM khuyen_mai WHERE id_km = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Thêm mới khuyến mãi (Tự sinh mã KM001, KM002...)
     */
    public function createPromotion($data) {
        // Lấy mã lớn nhất hiện tại để tăng tiến
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
            $stmt->execute([
                $newId,
                $data['ten_km'],
                $data['phan_tram_km'],
                $data['ngay_bd_km'],
                $data['ngay_kt_km'],
                $data['trang_thai_km']
            ]);
            
            return $newId; // Trả về ID mới để Controller sử dụng

        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Cập nhật thông tin khuyến mãi
     */
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
                $data['ngay_bd_km'],
                $data['ngay_kt_km'],
                $data['trang_thai_km'],
                $id
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Cập nhật trạng thái khuyến mãi tự động (Dùng trong Controller index)
     */
    public function updateStatus($id, $status) {
        $sql = "UPDATE khuyen_mai SET trang_thai_km = ? WHERE id_km = ?";
        return $this->db->prepare($sql)->execute([$status, $id]);
    }

    /**
     * Xóa khuyến mãi (Có xử lý gỡ mã khỏi lô hàng để tránh lỗi khóa ngoại)
     */
    public function deletePromotion($id) {
        try {
            // Bắt đầu transaction để đảm bảo an toàn dữ liệu
            $this->db->beginTransaction();

            // 1. Gỡ mã KM khỏi các lô hàng đang áp dụng mã này
            $this->removePromotionFromBatches($id);

            // 2. Xóa khuyến mãi chính
            $stmt = $this->db->prepare("DELETE FROM khuyen_mai WHERE id_km = ?");
            $res = $stmt->execute([$id]);

            $this->db->commit();
            return $res;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Gỡ mã khuyến mãi khỏi tất cả lô hàng (Dùng khi xóa chương trình KM)
     */
    public function removePromotionFromBatches($id_km) {
        $sql = "UPDATE lo_hang SET id_km = NULL WHERE id_km = ?";
        return $this->db->prepare($sql)->execute([$id_km]);
    }

    /**
     * Áp dụng khuyến mãi cho lô hàng của 1 sản phẩm có HSD gần nhất (FEFO)
     */
    public function applyPromotionToCategory($id_km, $id_hh) {
        // 1. Tìm lô hàng của sản phẩm này có HSD gần nhất, còn hàng và chưa hết hạn
        $sql = "SELECT id_lo FROM lo_hang 
                WHERE id_hh = :id_hh 
                AND so_luong_con_lai > 0 
                AND hsd_lo > NOW() 
                ORDER BY hsd_lo ASC 
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_hh' => $id_hh]);
        $batch = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($batch) {
            // 2. Cập nhật mã khuyến mãi cho đúng lô hàng đó
            $updateSql = "UPDATE lo_hang SET id_km = :id_km WHERE id_lo = :id_lo";
            $updateStmt = $this->db->prepare($updateSql);
            return $updateStmt->execute([
                ':id_km' => $id_km,
                ':id_lo' => $batch['id_lo']
            ]);
        }
        return false;
    }

    public function getAvailableBatchesByKeyword($keyword) {
        $sql = "SELECT l.*, h.ten_hh FROM lo_hang l 
                JOIN hang_hoa h ON l.id_hh = h.id_hh 
                WHERE (h.ten_hh LIKE :kw OR h.id_hh LIKE :kw OR l.id_lo LIKE :kw)
                AND l.so_luong_con_lai > 0 AND l.hsd_lo > NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':kw' => "%$keyword%"]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function applyToSpecificBatch($idKm, $idLo) {
        $sql = "UPDATE lo_hang SET id_km = ? WHERE id_lo = ?";
        return $this->db->prepare($sql)->execute([$idKm, $idLo]);
    }
}