<?php

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class AddressModel extends BaseModel {

    /**
     * Lấy danh sách địa chỉ của User
     */
    public function getAddressesByUserId($userId) {
        $sql = "SELECT * FROM dia_chi_giao_hang WHERE ID_TK = ? ORDER BY IS_DEFAULT DESC, ID_DIA_CHI DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tìm chi tiết 1 địa chỉ theo ID
     */
    public function findAddressById($userId, $addressId) {
        $stmt = $this->db->prepare("SELECT * FROM dia_chi_giao_hang WHERE ID_TK = ? AND ID_DIA_CHI = ?");
        $stmt->execute([$userId, $addressId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Thêm địa chỉ mới
     */
    public function addAddress($userId, $data) {
        if (!empty($data['is_default'])) {
            $this->resetDefault($userId);
        }

        $sql = "INSERT INTO dia_chi_giao_hang 
                (ID_TK, TEN_NGUOI_NHAN, SDT_GH, 
                 ID_TINH_TP, TEN_TINH_TP, 
                 ID_QUAN_HUYEN, TEN_QUAN_HUYEN, 
                 ID_XA_PHUONG, TEN_XA_PHUONG, 
                 DIA_CHI_CHI_TIET, IS_DEFAULT) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $userId,
            $data['ten_nguoi_nhan'],
            $data['sdt_gh'],
            $data['tinh_tp'],       // ID Tỉnh
            $data['ten_tinh_tp'] ?? '', // Tên Tỉnh (nếu có)
            $data['quan_huyen'],    // ID Huyện
            $data['ten_quan_huyen'] ?? '',
            $data['xa_phuong'],     // ID Xã
            $data['ten_xa_phuong'] ?? '',
            $data['dia_chi_chi_tiet'],
            $data['is_default']
        ]);
    }

    /**
     * Cập nhật địa chỉ
     */
    public function updateAddress($id, $userId, $data) {
        if (!empty($data['is_default'])) {
            $this->resetDefault($userId);
        }

        $sql = "UPDATE dia_chi_giao_hang SET 
                TEN_NGUOI_NHAN = ?, 
                SDT_GH = ?, 
                ID_TINH_TP = ?, TEN_TINH_TP = ?, 
                ID_QUAN_HUYEN = ?, TEN_QUAN_HUYEN = ?, 
                ID_XA_PHUONG = ?, TEN_XA_PHUONG = ?, 
                DIA_CHI_CHI_TIET = ?, 
                IS_DEFAULT = ?
                WHERE ID_DIA_CHI = ? AND ID_TK = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['ten_nguoi_nhan'],
            $data['sdt_gh'],
            $data['tinh_tp'], 
            $data['ten_tinh_tp'] ?? '',
            $data['quan_huyen'], 
            $data['ten_quan_huyen'] ?? '',
            $data['xa_phuong'], 
            $data['ten_xa_phuong'] ?? '',
            $data['dia_chi_chi_tiet'],
            $data['is_default'],
            $id,
            $userId
        ]);
    }

    /**
     * Xóa địa chỉ
     */
    public function deleteAddress($id, $userId) {
        $stmt = $this->db->prepare("DELETE FROM dia_chi_giao_hang WHERE ID_DIA_CHI = ? AND ID_TK = ?");
        return $stmt->execute([$id, $userId]);
    }

    /**
     * Thiết lập địa chỉ mặc định
     */
    public function setDefaultAddress($id, $userId) {
        try {
            $this->db->beginTransaction();
            
            // 1. Bỏ mặc định tất cả
            $this->resetDefault($userId);
            
            // 2. Set mặc định cho cái được chọn
            if ($id !== null) {
                $stmt = $this->db->prepare("UPDATE dia_chi_giao_hang SET IS_DEFAULT = 1 WHERE ID_DIA_CHI = ? AND ID_TK = ?");
                $stmt->execute([$id, $userId]);
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Hàm phụ: Reset toàn bộ địa chỉ về không mặc định
     */
    private function resetDefault($userId) {
        $stmt = $this->db->prepare("UPDATE dia_chi_giao_hang SET IS_DEFAULT = 0 WHERE ID_TK = ?");
        $stmt->execute([$userId]);
    }
}