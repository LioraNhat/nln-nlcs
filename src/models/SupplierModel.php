<?php

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class SupplierModel extends BaseModel {

    // Lấy tất cả nhà cung cấp (Có tìm kiếm)
    public function getAllSuppliers($search = '') {
        $sql = "SELECT * FROM nha_cung_cap";
        $params = [];

        if (!empty($search)) {
            $sql .= " WHERE TEN_NCC LIKE ? OR EMAIL_NCC LIKE ? OR SDT_NCC LIKE ? OR ID_NCC LIKE ?";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " ORDER BY ID_NCC ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy 1 nhà cung cấp theo ID
    public function getSupplierById($id) {
        $stmt = $this->db->prepare("SELECT * FROM nha_cung_cap WHERE ID_NCC = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thêm mới (Tự sinh mã NCC01, NCC02...)
    public function createSupplier($data) {
        // 1. Sinh ID tự động
        $stmt = $this->db->query("SELECT MAX(ID_NCC) as max_id FROM nha_cung_cap");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $maxId = $row['max_id'];

        if ($maxId) {
            $num = (int)substr($maxId, 3); // Lấy số sau chữ NCC
            $newNum = $num + 1;
            // ID dạng NCC01, NCC02...
            $newId = 'NCC' . str_pad($newNum, 2, '0', STR_PAD_LEFT); 
        } else {
            $newId = 'NCC01';
        }

        // 2. Thêm vào DB
        try {
            $sql = "INSERT INTO nha_cung_cap (ID_NCC, TEN_NCC, DIA_CHI_NCC, SDT_NCC, EMAIL_NCC) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $newId, 
                $data['ten_ncc'], 
                $data['dia_chi'], 
                $data['sdt'], 
                $data['email']
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    // Cập nhật
    public function updateSupplier($id, $data) {
        try {
            $sql = "UPDATE nha_cung_cap 
                    SET TEN_NCC = ?, DIA_CHI_NCC = ?, SDT_NCC = ?, EMAIL_NCC = ? 
                    WHERE ID_NCC = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $data['ten_ncc'], 
                $data['dia_chi'], 
                $data['sdt'], 
                $data['email'],
                $id
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    // Xóa (Kiểm tra xem NCC này đã có phiếu nhập kho chưa)
    public function deleteSupplier($id) {
        // Kiểm tra bảng phieu_nhap
        $stmtCheck = $this->db->prepare("SELECT COUNT(*) as count FROM phieu_nhap WHERE ID_NCC = ?");
        $stmtCheck->execute([$id]);
        $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($row['count'] > 0) {
            return false; // Không cho xóa nếu đã từng nhập hàng
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM nha_cung_cap WHERE ID_NCC = ?");
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            return false;
        }
    }
}