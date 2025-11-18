<?php

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class UserModel extends BaseModel {

    // (Hàm getNextNumericId từ BaseController)

    // (Các hàm findByEmail, findByPhone, register, login... giữ nguyên)
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT tk.*, nd.PHAN_QUYEN_TK FROM tai_khoan tk INNER JOIN nguoi_dung nd ON tk.ID_ND = nd.ID_ND WHERE tk.EMAIL = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function findByPhone($sdt) {
        $stmt = $this->db->prepare("SELECT tk.*, nd.PHAN_QUYEN_TK FROM tai_khoan tk INNER JOIN nguoi_dung nd ON tk.ID_ND = nd.ID_ND WHERE tk.SDT_TK = ?");
        $stmt->execute([$sdt]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function register($data) {
        // Dùng code an toàn (3 bước, không tắt FK_CHECKS)
        $id_tk = 'TK' . substr(uniqid(), 0, 13);
        $id_gh = 'GH' . substr(uniqid(), -3);
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        try {
            $this->db->beginTransaction();
            // 1. Tạo giỏ hàng (ID_TK = NULL)
            $stmt_cart = $this->db->prepare("INSERT INTO gio_hang (ID_GH, ID_TK, NGAY_TAO_GH, NGAY_CAP_NHAT_GH) VALUES (?, NULL, NOW(), NOW())");
            $stmt_cart->execute([$id_gh]);
            // 2. Tạo tài khoản
            $stmt_user = $this->db->prepare("INSERT INTO tai_khoan (ID_TK, ID_GH, ID_ND, HO_TEN, GIOI_TINH, SDT_TK, EMAIL, MAT_KHAU, NGAY_GIO_TAO_TK, NGAY_GIO_CAP_NHAT) VALUES (?, ?, 'KH', ?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt_user->execute([$id_tk, $id_gh, $data['ho_ten'], $data['gioi_tinh'], $data['sdt_tk'], $data['email'], $hashedPassword]);
            // 3. Cập nhật giỏ hàng (gán ID_TK)
            $stmt_update_cart = $this->db->prepare("UPDATE gio_hang SET ID_TK = ? WHERE ID_GH = ?");
            $stmt_update_cart->execute([$id_tk, $id_gh]);
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
    public function login($username, $password) {
        $user = false;
        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $user = $this->findByEmail($username);
        } else {
            $user = $this->findByPhone($username);
        }
        if (!$user) { return false; }
        if (password_verify($password, $user['MAT_KHAU'])) {
            return $user;
        }
        return false;
    }


    /**
     * Lấy TẤT CẢ địa chỉ (cho Sổ địa chỉ)
     */
    public function getAllAddressesByUserId($userId) {
        $stmt = $this->db->prepare("SELECT * FROM dia_chi_giao_hang WHERE ID_TK = ? ORDER BY IS_DEFAULT DESC, ID_DIA_CHI DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cập nhật thông tin cơ bản (Họ tên, SĐT, Giới tính)
     */
    public function updateProfile($userId, $data) {
        $sql = "UPDATE tai_khoan SET HO_TEN = ?, SDT_TK = ?, GIOI_TINH = ?, NGAY_GIO_CAP_NHAT = NOW() WHERE ID_TK = ?";
        $stmt = $this->db->prepare($sql);
        $_SESSION['user_name'] = $data['ho_ten'];
        $_SESSION['user_phone'] = $data['sdt_tk'];
        return $stmt->execute([$data['ho_ten'], $data['sdt_tk'], $data['gioi_tinh'], $userId]);
    }

    /**
     * Thêm một địa chỉ MỚI
     */
    public function addAddress($userId, $data) {
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
            $data['ho_ten'], $data['sdt_gh'],
            $data['id_tinh_tp'], $data['ten_tinh_tp'],
            $data['id_quan_huyen'], $data['ten_quan_huyen'],
            $data['id_xa_phuong'], $data['ten_xa_phuong'],
            $data['dia_chi_chi_tiet'], $data['is_default']
        ]);
    }

    /**
     * Đặt 1 địa chỉ làm mặc định
     */
    public function setDefaultAddress($userId, $addressId) {
        try {
            $this->db->beginTransaction();
            $stmt1 = $this->db->prepare("UPDATE dia_chi_giao_hang SET IS_DEFAULT = 0 WHERE ID_TK = ?");
            $stmt1->execute([$userId]);
            if ($addressId !== null) {
                $stmt2 = $this->db->prepare("UPDATE dia_chi_giao_hang SET IS_DEFAULT = 1 WHERE ID_TK = ? AND ID_DIA_CHI = ?");
                $stmt2->execute([$userId, $addressId]);
            }
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // (Hàm changePassword - giữ nguyên)
    public function changePassword($userId, $currentPassword, $newPassword) {
        $stmt = $this->db->prepare("SELECT MAT_KHAU FROM tai_khoan WHERE ID_TK = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) { return false; }
        if (!password_verify($currentPassword, $user['MAT_KHAU'])) { return false; }
        $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateStmt = $this->db->prepare("UPDATE tai_khoan SET MAT_KHAU = ? WHERE ID_TK = ?");
        return $updateStmt->execute([$newHashedPassword, $userId]);
    }

    /**
     * HÀM MỚI (CHO NÚT "SỬA"): Tìm 1 địa chỉ bằng ID của nó
     */
    public function findAddressById($userId, $addressId) {
        $stmt = $this->db->prepare("
            SELECT * FROM dia_chi_giao_hang 
            WHERE ID_TK = ? AND ID_DIA_CHI = ?
        ");
        $stmt->execute([$userId, $addressId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * HÀM MỚI (CHO NÚT "SỬA"): Cập nhật 1 địa chỉ
     */
    public function updateAddressDetails($addressId, $userId, $data) {
        $sql = "UPDATE dia_chi_giao_hang SET
                    TEN_NGUOI_NHAN = ?,
                    SDT_GH = ?,
                    ID_TINH_TP = ?,
                    TEN_TINH_TP = ?,
                    ID_QUAN_HUYEN = ?,
                    TEN_QUAN_HUYEN = ?,
                    ID_XA_PHUONG = ?,
                    TEN_XA_PHUONG = ?,
                    DIA_CHI_CHI_TIET = ?,
                    IS_DEFAULT = ?
                WHERE ID_DIA_CHI = ? AND ID_TK = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['ho_ten'],
            $data['sdt_gh'],
            $data['id_tinh_tp'],
            $data['ten_tinh_tp'],
            $data['id_quan_huyen'],
            $data['ten_quan_huyen'],
            $data['id_xa_phuong'],
            $data['ten_xa_phuong'],
            $data['dia_chi_chi_tiet'],
            $data['is_default'],
            $addressId,
            $userId
        ]);
    }
    
    /**
     * HÀM MỚI (CHO NÚT "XÓA"): Xóa 1 địa chỉ
     */
    public function deleteAddress($userId, $addressId) {
        $stmt = $this->db->prepare("DELETE FROM dia_chi_giao_hang WHERE ID_TK = ? AND ID_DIA_CHI = ?");
        return $stmt->execute([$userId, $addressId]);
    }
}