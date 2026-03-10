<?php

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class UserModel extends BaseModel {

    // ============================================
    // 1. CÁC PHƯƠNG THỨC CƠ BẢN (LOGIN/REGISTER)
    // ============================================

    // Sửa EMAIL -> EMAIL_TK
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT tk.*, nd.phan_quyen_tk FROM tai_khoan tk INNER JOIN nguoi_dung nd ON tk.id_nd = nd.id_nd WHERE tk.email_tk = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByPhone($sdt) {
        $stmt = $this->db->prepare("SELECT tk.*, nd.phan_quyen_tk FROM tai_khoan tk INNER JOIN nguoi_dung nd ON tk.id_nd = nd.id_nd WHERE tk.sdt_tk = ?");
        $stmt->execute([$sdt]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function login($username, $password) {
        $user = false;
        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $user = $this->findByEmail($username);
        } else {
            $user = $this->findByPhone($username);
        }

        if (!$user) return false;

        // Hỗ trợ cả mật khẩu hash mới VÀ md5 cũ
        if (password_verify($password, $user['mat_khau_tk'])) {
            return $user;
        }
        if ($user['mat_khau_tk'] === md5($password)) {
            return $user;
        }

        return false;
    }

    public function register($data) {
        $id_tk = 'TK' . substr(uniqid(), 0, 13);
        $id_gh = 'GH' . substr(uniqid(), -2);
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        try {
            $this->db->beginTransaction();

            $stmt_user = $this->db->prepare("INSERT INTO tai_khoan (id_tk, id_nd, ho_ten, gioi_tinh, sdt_tk, email_tk, mat_khau_tk, ngay_gio_tao_tk) VALUES (?, 'KH', ?, ?, ?, ?, ?, NOW())");
            $stmt_user->execute([$id_tk, $data['ho_ten'], $data['gioi_tinh'], $data['sdt_tk'], $data['email'], $hashedPassword]);

            $stmt_cart = $this->db->prepare("INSERT INTO gio_hang (id_gh, id_tk, ngay_tao_gh) VALUES (?, ?, NOW())");
            $stmt_cart->execute([$id_gh, $id_tk]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function changePassword($userId, $currentPassword, $newPassword) {
        $stmt = $this->db->prepare("SELECT mat_khau_tk FROM tai_khoan WHERE id_tk = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) return "Tài khoản không tồn tại.";
        if (!password_verify($currentPassword, $user['mat_khau_tk'])) return "Mật khẩu hiện tại không đúng.";

        $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateStmt = $this->db->prepare("UPDATE tai_khoan SET mat_khau_tk = ? WHERE id_tk = ?");
        if ($updateStmt->execute([$newHashedPassword, $userId])) return true;
        return "Lỗi hệ thống, không thể đổi mật khẩu.";
    }

    public function updateProfile($id, $hoTen, $sdt, $gioiTinh) {
        try {
            $stmt = $this->db->prepare("UPDATE tai_khoan SET ho_ten = ?, sdt_tk = ?, gioi_tinh = ? WHERE id_tk = ?");
            return $stmt->execute([$hoTen, $sdt, $gioiTinh, $id]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function updatePassword($id, $newPassHash) {
        try {
            $stmt = $this->db->prepare("UPDATE tai_khoan SET mat_khau_tk = ? WHERE id_tk = ?");
            return $stmt->execute([$newPassHash, $id]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    // ============================================
    // CÁC PHƯƠNG THỨC CHO ADMIN (MỚI THÊM)
    // ============================================

    /**
     * Lấy tổng số người dùng (Dashboard)
     */
    public function getTotalUsers() {
        $sql = "SELECT COUNT(*) as total FROM tai_khoan";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Đếm tổng số người dùng (Admin - Có tìm kiếm)
     */
    public function countAllUsers($keyword = '') {
        $sql = "SELECT COUNT(*) as total FROM tai_khoan tk";
        $params = [];
        if (!empty($keyword)) {
            $sql .= " WHERE (tk.id_tk LIKE :keyword OR tk.ho_ten LIKE :keyword 
                    OR tk.email_tk LIKE :keyword OR tk.sdt_tk LIKE :keyword)";
            $params[':keyword'] = '%' . $keyword . '%';
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    }

    /**
     * Lấy tất cả người dùng (Admin - Có phân trang & tìm kiếm)
     */
    public function getAllUsers($keyword = '', $limit = 20, $offset = 0) {
        $sql = "SELECT 
                    tk.id_tk, tk.ho_ten, tk.dia_chi_avt,
                    tk.gioi_tinh, tk.sdt_tk, tk.email_tk,
                    tk.ngay_gio_tao_tk,
                    nd.id_nd, nd.phan_quyen_tk,
                    COUNT(DISTINCT dh.id_dh) as tong_don_hang,
                    COALESCE(SUM(dh.thanh_tien), 0) as tong_chi_tieu
                FROM tai_khoan tk
                INNER JOIN nguoi_dung nd ON tk.id_nd = nd.id_nd
                LEFT JOIN don_hang dh ON tk.id_tk = dh.id_tk";
        $params = [];
        if (!empty($keyword)) {
            $sql .= " WHERE (tk.id_tk LIKE :keyword OR tk.ho_ten LIKE :keyword 
                    OR tk.email_tk LIKE :keyword OR tk.sdt_tk LIKE :keyword)";
            $params[':keyword'] = '%' . $keyword . '%';
        }
        $sql .= " GROUP BY tk.id_tk, tk.ho_ten, tk.gioi_tinh, tk.sdt_tk, 
                tk.email_tk, tk.ngay_gio_tao_tk, nd.id_nd, nd.phan_quyen_tk
                ORDER BY tk.ngay_gio_tao_tk DESC 
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy thông tin 1 người dùng (Dùng chung cho Admin và Check mật khẩu cũ)
     */
    public function getUserById($userId) {
        $stmt = $this->db->prepare("SELECT tk.*, nd.phan_quyen_tk FROM tai_khoan tk INNER JOIN nguoi_dung nd ON tk.id_nd = nd.id_nd WHERE tk.id_tk = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }



    /**
     * Lấy danh sách địa chỉ giao hàng của User (Admin xem)
     */
    // IS_DEFAULT đổi thành MAC_DINH
    public function getUserAddresses($userId) {
        $stmt = $this->db->prepare("SELECT * FROM dia_chi_giao_hang WHERE id_tk = ? ORDER BY mac_dinh DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cập nhật địa chỉ cho User (Trong bảng dia_chi_giao_hang)
     */
    public function updateUserAddress($userId, $name, $phone, $addressDetail) {
        try {
            $current = $this->getUserAddresses($userId);
            if ($current) {
                $sql = "UPDATE dia_chi_giao_hang 
                        SET dia_chi_chi_tiet = ?, ten_nguoi_nhan = ?, sdt_gh = ? 
                        WHERE id_dc = ?";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$addressDetail, $name, $phone, $current[0]['id_dc']]);
            } else {
                $sql = "INSERT INTO dia_chi_giao_hang (id_tk, ten_nguoi_nhan, sdt_gh, dia_chi_chi_tiet, mac_dinh) 
                        VALUES (?, ?, ?, ?, 1)";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$userId, $name, $phone, $addressDetail]);
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Lấy đơn hàng của người dùng (Admin)
     */
    public function getUserOrders($userId, $limit = 10) {
        $sql = "SELECT dh.id_dh, dh.ngay_gio_tao_don, dh.thanh_tien,
                    dh.trang_thai_thanh_toan, dht.id_ttd
                FROM don_hang dh
                LEFT JOIN don_hang_hien_tai dht ON dh.id_dh = dht.id_dh
                WHERE dh.id_tk = ?
                ORDER BY dh.ngay_gio_tao_don DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $userId);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Xóa người dùng (Admin - Cẩn thận với Foreign Key)
     */
    public function deleteUser($userId) {
        try {
            $this->db->beginTransaction();
            $this->db->prepare("DELETE FROM dia_chi_giao_hang WHERE id_tk = ?")->execute([$userId]);
            $this->db->prepare("DELETE FROM gio_hang WHERE id_tk = ?")->execute([$userId]);
            $this->db->prepare("DELETE FROM tai_khoan WHERE id_tk = ?")->execute([$userId]);
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Hàm cập nhật thông tin User dành cho Admin (Bao gồm cả Email và Mật khẩu)
     */
    public function adminUpdateCustomer($id, $data) {
        try {
            if (!empty($data['mat_khau'])) {
                $sql = "UPDATE tai_khoan SET ho_ten=?, sdt_tk=?, gioi_tinh=?, email_tk=?, mat_khau_tk=? WHERE id_tk=?";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$data['ho_ten'], $data['sdt'], $data['gioi_tinh'], $data['email'], $data['mat_khau'], $id]);
            } else {
                $sql = "UPDATE tai_khoan SET ho_ten=?, sdt_tk=?, gioi_tinh=?, email_tk=? WHERE id_tk=?";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$data['ho_ten'], $data['sdt'], $data['gioi_tinh'], $data['email'], $id]);
            }
        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * HÀM MỚI: Lấy danh sách theo vai trò (Để tách KH và AD)
     * $roleId: 'KH' hoặc 'AD'
     */
    public function getUsersByRole($roleId, $search = '', $limit = 100, $offset = 0) {
        $sql = "SELECT tk.*, nd.phan_quyen_tk FROM tai_khoan tk
                INNER JOIN nguoi_dung nd ON tk.id_nd = nd.id_nd
                WHERE tk.id_nd = ?";
        $params = [$roleId];
        if (!empty($search)) {
            $sql .= " AND (tk.ho_ten LIKE ? OR tk.email_tk LIKE ? OR tk.sdt_tk LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        $sql .= " ORDER BY tk.ngay_gio_tao_tk DESC LIMIT $limit OFFSET $offset";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * HÀM MỚI: Đếm số lượng theo vai trò (Để phân trang)
     */
    public function countUsersByRole($roleId, $search = '') {
        $sql = "SELECT COUNT(*) as total FROM tai_khoan WHERE id_nd = ?";
        $params = [$roleId];
        if (!empty($search)) {
            $sql .= " AND (ho_ten LIKE ? OR email_tk LIKE ? OR sdt_tk LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    /**
     * HÀM MỚI: Cập nhật phân quyền (Đổi từ KH -> AD hoặc ngược lại)
     */
    public function updateRole($userId, $newRoleId) {
        try {
            $stmt = $this->db->prepare("UPDATE tai_khoan SET id_nd = ? WHERE id_tk = ?");
            return $stmt->execute([$newRoleId, $userId]);
        } catch (\Exception $e) {
            return false;
        }
    }

    // THỐNG KÊ ADMIN
    /**
     * Thống kê người dùng mới theo tháng (Dashboard)
     */
    public function getNewUsersStatsByMonth($year = null) {
        if ($year === null) {
            $year = date('Y');
        }

        $sql = "SELECT MONTH(ngay_gio_tao_tk) as thang, COUNT(*) as so_luong
                FROM tai_khoan
                WHERE YEAR(ngay_gio_tao_tk) = ?
                GROUP BY MONTH(ngay_gio_tao_tk)
                ORDER BY thang";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$year]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
     * Lấy top khách hàng chi tiêu nhiều nhất
     */
    public function getTopSpendingUsers($limit = 10) {
        $sql = "SELECT tk.id_tk, tk.ho_ten, tk.email_tk, tk.sdt_tk,
                    COUNT(DISTINCT dh.id_dh) as tong_don_hang,
                    COALESCE(SUM(dh.thanh_tien), 0) as tong_chi_tieu
                FROM tai_khoan tk
                LEFT JOIN don_hang dh ON tk.id_tk = dh.id_tk
                GROUP BY tk.id_tk
                ORDER BY tong_chi_tieu DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}