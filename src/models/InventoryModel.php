<?php

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class InventoryModel extends BaseModel {

    // Lấy danh sách phiếu nhập kho (Có tìm kiếm)
    public function getAllImportSlips($search = '') {
        $sql = "SELECT pn.*, ncc.TEN_NCC 
                FROM phieu_nhap pn 
                LEFT JOIN nha_cung_cap ncc ON pn.ID_NCC = ncc.ID_NCC";
        
        $params = [];
        if (!empty($search)) {
            $sql .= " WHERE pn.ID_PN LIKE ? OR ncc.TEN_NCC LIKE ? OR pn.CHUNG_TU_GOC LIKE ?";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " ORDER BY pn.NGAY_LAP_PHIEU_NHAP DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy chi tiết 1 phiếu nhập
    public function getImportSlipById($id) {
        $sql = "SELECT pn.*, ncc.TEN_NCC 
                FROM phieu_nhap pn 
                LEFT JOIN nha_cung_cap ncc ON pn.ID_NCC = ncc.ID_NCC 
                WHERE pn.ID_PN = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách sản phẩm trong phiếu nhập (ĐÃ SỬA LỖI JOIN BẢNG DVT)
    public function getImportSlipDetails($id) {
        $sql = "SELECT ct.*, hh.TEN_HH, d.DVT 
                FROM chi_tiet_phieu_nhap ct
                LEFT JOIN hang_hoa hh ON ct.ID_HH = hh.ID_HH
                LEFT JOIN dvt d ON hh.ID_DVT = d.ID_DVT 
                WHERE ct.ID_PN = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy tất cả Nhà cung cấp (Để chọn khi nhập hàng)
    public function getAllSuppliers() {
        return $this->db->query("SELECT * FROM nha_cung_cap")->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy tất cả Sản phẩm (Để chọn khi nhập hàng)
    public function getAllProducts() {
        return $this->db->query("SELECT ID_HH, TEN_HH, SO_LUONG_TON_HH FROM hang_hoa")->fetchAll(PDO::FETCH_ASSOC);
    }

    // === HÀM QUAN TRỌNG: TẠO PHIẾU NHẬP ===
    public function createImportSlip($data, $details) {
        try {
            // 1. Bắt đầu giao dịch (Transaction)
            $this->db->beginTransaction();

            // 2. Sinh mã Phiếu nhập (PN001...)
            $stmtMax = $this->db->query("SELECT MAX(ID_PN) as max_id FROM phieu_nhap");
            $maxId = $stmtMax->fetch(PDO::FETCH_ASSOC)['max_id'];
            if ($maxId) {
                $num = (int)substr($maxId, 2);
                $newId = 'PN' . str_pad($num + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $newId = 'PN001';
            }

            // 3. Insert bảng phieu_nhap
            $sqlPN = "INSERT INTO phieu_nhap (ID_PN, ID_NCC, NGAY_LAP_PHIEU_NHAP, TONG_TIEN_NHAP, VAT, TONG_GIA_TRI_PHIEU_NHAP, CHUNG_TU_GOC) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmtPN = $this->db->prepare($sqlPN);
            $stmtPN->execute([
                $newId,
                $data['id_ncc'],
                $data['ngay_lap'],
                $data['tong_tien'],
                0, // Tạm tính VAT = 0 hoặc tính sau
                $data['tong_tien'], // Tổng giá trị
                $data['chung_tu']
            ]);

            // 4. Insert chi tiết & Cập nhật kho
            $sqlCT = "INSERT INTO chi_tiet_phieu_nhap (ID_PN, ID_HH, SO_LUONG_NHAP, DON_GIA_NHAP) VALUES (?, ?, ?, ?)";
            $stmtCT = $this->db->prepare($sqlCT);

            $sqlUpdateKho = "UPDATE hang_hoa SET SO_LUONG_TON_HH = SO_LUONG_TON_HH + ? WHERE ID_HH = ?";
            $stmtUpdateKho = $this->db->prepare($sqlUpdateKho);

            foreach ($details as $item) {
                // Thêm chi tiết
                $stmtCT->execute([$newId, $item['id_hh'], $item['so_luong'], $item['don_gia']]);
                
                // Cộng tồn kho
                $stmtUpdateKho->execute([$item['so_luong'], $item['id_hh']]);
            }

            // 5. Hoàn tất giao dịch
            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            $this->db->rollBack(); // Nếu lỗi thì hoàn tác tất cả
            error_log($e->getMessage());
            return false;
        }
    }
}