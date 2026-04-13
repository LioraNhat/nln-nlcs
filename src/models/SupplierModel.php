<?php

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class SupplierModel extends BaseModel {

    /**
     * Lấy tất cả nhà cung cấp
     * Sắp xếp: Mới nhất lên đầu (id_ncc DESC)
     * Bộ lọc: Tên nhà cung cấp và Số điện thoại
     */
    public function getAllSuppliers($search = '', $phone = '') {
        $sql = "SELECT * FROM nha_cung_cap WHERE 1=1";
        $params = [];

        // Lọc theo tên hoặc mã hoặc email (từ ô search chung)
        if (!empty($search)) {
            $sql .= " AND (ten_ncc LIKE ? OR id_ncc LIKE ? OR email_ncc LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        // Lọc riêng theo số điện thoại (từ ô filter SĐT)
        if (!empty($phone)) {
            $sql .= " AND sdt_ncc LIKE ?";
            $params[] = "%$phone%";
        }

        // Sắp xếp: ID mới nhất sẽ nằm ở trên cùng
        $sql .= " ORDER BY id_ncc DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy 1 nhà cung cấp theo ID
     */
    public function getSupplierById($id) {
        $stmt = $this->db->prepare("SELECT * FROM nha_cung_cap WHERE id_ncc = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Thêm mới (Tự sinh mã NCC01, NCC02...)
     */
    public function createSupplier($data) {
        // 1. Sinh ID tự động (Ví dụ: NCC01)
        $stmt = $this->db->query("SELECT MAX(id_ncc) as max_id FROM nha_cung_cap");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $maxId = $row['max_id'];

        if ($maxId) {
            $num = (int)substr($maxId, 3); // Cắt chuỗi 'NCC'
            $newId = 'NCC' . str_pad($num + 1, 2, '0', STR_PAD_LEFT); 
        } else {
            $newId = 'NCC01';
        }

        // 2. Thêm vào DB (Sử dụng tên cột viết thường)
        try {
            $sql = "INSERT INTO nha_cung_cap (id_ncc, ten_ncc, dia_chi_ncc, sdt_ncc, email_ncc) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $newId, 
                $data['ten_ncc'], 
                $data['dia_chi_ncc'], // Khớp với mảng data từ Controller
                $data['sdt_ncc'], 
                $data['email_ncc']
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Cập nhật thông tin
     */
    public function updateSupplier($id, $data) {
        try {
            $sql = "UPDATE nha_cung_cap 
                    SET ten_ncc = ?, dia_chi_ncc = ?, sdt_ncc = ?, email_ncc = ? 
                    WHERE id_ncc = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $data['ten_ncc'], 
                $data['dia_chi_ncc'], 
                $data['sdt_ncc'], 
                $data['email_ncc'],
                $id
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Xóa nhà cung cấp
     * Kiểm tra ràng buộc với bảng lo_hang (vì bảng phieu_nhap của bạn có thể liên kết qua lo_hang)
     */
    public function deleteSupplier($id) {
        // 1. Kiểm tra bảng lo_hang (Sửa ID_NCC thành id_ncc)
        $stmtCheck = $this->db->prepare("SELECT COUNT(*) as count FROM lo_hang WHERE id_ncc = ?");
        $stmtCheck->execute([$id]);
        $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($row['count'] > 0) {
            return false; 
        }

        try {
            // 2. Thực hiện xóa (Sửa ID_NCC thành id_ncc)
            $stmt = $this->db->prepare("DELETE FROM nha_cung_cap WHERE id_ncc = ?");
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            return false;
        }
    }
}