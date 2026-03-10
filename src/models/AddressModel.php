<?php

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class AddressModel extends BaseModel {
    public function getAddressesByUserId($userId) {
        $sql = "SELECT * FROM dia_chi_giao_hang WHERE id_tk = ? ORDER BY mac_dinh DESC, id_dc DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAddressById($userId, $addressId) {
        $stmt = $this->db->prepare("SELECT * FROM dia_chi_giao_hang WHERE id_tk = ? AND id_dc = ?");
        $stmt->execute([$userId, $addressId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addAddress($userId, $data) {
        if (!empty($data['is_default'])) {
            $this->resetDefault($userId);
        }

        $sql = "INSERT INTO dia_chi_giao_hang 
                (id_tk, ten_nguoi_nhan, sdt_gh, 
                ma_tinh_tp, ten_tinh_tp, 
                ma_quan_huyen, ten_quan_huyen, 
                ma_xa_phuong, ten_xa_phuong, 
                dia_chi_chi_tiet, mac_dinh) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $userId,
            $data['ten_nguoi_nhan'],
            $data['sdt_gh'],
            $data['tinh_tp'],
            $data['ten_tinh_tp'] ?? '',
            $data['quan_huyen'],
            $data['ten_quan_huyen'] ?? '',
            $data['xa_phuong'],
            $data['ten_xa_phuong'] ?? '',
            $data['dia_chi_chi_tiet'],
            !empty($data['is_default']) ? 1 : 0
        ]);
    }

    public function updateAddress($id, $userId, $data) {
        if (!empty($data['is_default'])) {
            $this->resetDefault($userId);
        }

        $sql = "UPDATE dia_chi_giao_hang SET 
                ten_nguoi_nhan = ?, 
                sdt_gh = ?, 
                ma_tinh_tp = ?, ten_tinh_tp = ?, 
                ma_quan_huyen = ?, ten_quan_huyen = ?, 
                ma_xa_phuong = ?, ten_xa_phuong = ?, 
                dia_chi_chi_tiet = ?, 
                mac_dinh = ?
                WHERE id_dc = ? AND id_tk = ?";
        
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
            !empty($data['is_default']) ? 1 : 0,
            $id,
            $userId
        ]);
    }

    public function deleteAddress($id, $userId) {
        $stmt = $this->db->prepare("DELETE FROM dia_chi_giao_hang WHERE id_dc = ? AND id_tk = ?");
        return $stmt->execute([$id, $userId]);
    }

    public function setDefaultAddress($id, $userId) {
        try {
            $this->db->beginTransaction();
            $this->resetDefault($userId);
            if ($id !== null) {
                $stmt = $this->db->prepare("UPDATE dia_chi_giao_hang SET mac_dinh = 1 WHERE id_dc = ? AND id_tk = ?");
                $stmt->execute([$id, $userId]);
            }
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    private function resetDefault($userId) {
        $stmt = $this->db->prepare("UPDATE dia_chi_giao_hang SET mac_dinh = 0 WHERE id_tk = ?");
        $stmt->execute([$userId]);
    }
    
}