<?php

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class UserModel extends BaseModel {

    // ============================================
    // 1. CÁC PHƯƠNG THỨC CƠ BẢN (LOGIN/REGISTER)
    // ============================================

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

    public function login($username, $password) {
        $user = false;
        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $user = $this->findByEmail($username);
        } else {
            $user = $this->findByPhone($username);
        }
        
        if (!$user) { 
            return false; 
        }
        
        if (password_verify($password, $user['MAT_KHAU'])) {
            return $user;
        }
        
        return false;
    }

    public function register($data) {
        // Tạo ID ngẫu nhiên (Lưu ý: Nếu hàm generateNewId chưa có, dùng uniqid tạm)
        $id_tk = 'TK' . substr(uniqid(), 0, 13);
        $id_gh = 'GH' . substr(uniqid(), -3);
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        try {
            $this->db->beginTransaction();
            
            // 1. Tạo giỏ hàng trước
            $stmt_cart = $this->db->prepare("INSERT INTO gio_hang (ID_GH, ID_TK, NGAY_TAO_GH, NGAY_CAP_NHAT_GH) VALUES (?, NULL, NOW(), NOW())");
            $stmt_cart->execute([$id_gh]);
            
            // 2. Tạo tài khoản
            $stmt_user = $this->db->prepare("INSERT INTO tai_khoan (ID_TK, ID_GH, ID_ND, HO_TEN, GIOI_TINH, SDT_TK, EMAIL, MAT_KHAU, NGAY_GIO_TAO_TK, NGAY_GIO_CAP_NHAT) VALUES (?, ?, 'KH', ?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt_user->execute([$id_tk, $id_gh, $data['ho_ten'], $data['gioi_tinh'], $data['sdt_tk'], $data['email'], $hashedPassword]);
            
            // 3. Cập nhật lại ID_TK vào giỏ hàng
            $stmt_update_cart = $this->db->prepare("UPDATE gio_hang SET ID_TK = ? WHERE ID_GH = ?");
            $stmt_update_cart->execute([$id_tk, $id_gh]);
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Cập nhật thông tin cá nhân
     */
    public function updateProfile($id, $hoTen, $sdt, $gioiTinh) {
        try {
            $sql = "UPDATE tai_khoan 
                    SET HO_TEN = ?, SDT_TK = ?, GIOI_TINH = ?, NGAY_GIO_CAP_NHAT = NOW() 
                    WHERE ID_TK = ?";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$hoTen, $sdt, $gioiTinh, $id]);
            
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function changePassword($userId, $currentPassword, $newPassword) {
        $stmt = $this->db->prepare("SELECT MAT_KHAU FROM tai_khoan WHERE ID_TK = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) { 
            return "Tài khoản không tồn tại."; 
        }
        
        if (!password_verify($currentPassword, $user['MAT_KHAU'])) { 
            return "Mật khẩu hiện tại không đúng."; 
        }
        
        $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateStmt = $this->db->prepare("UPDATE tai_khoan SET MAT_KHAU = ? WHERE ID_TK = ?");
        
        if ($updateStmt->execute([$newHashedPassword, $userId])) {
            return true;
        }
        return "Lỗi hệ thống, không thể đổi mật khẩu.";
    }

    /**
     * Cập nhật mật khẩu mới (HÀM BẠN ĐANG THIẾU)
     * Hàm này chỉ nhận mật khẩu đã mã hóa và lưu vào DB
     */
    public function updatePassword($id, $newPassHash) {
        try {
            $sql = "UPDATE tai_khoan 
                    SET MAT_KHAU = ?, NGAY_GIO_CAP_NHAT = NOW() 
                    WHERE ID_TK = ?";
            
            $stmt = $this->db->prepare($sql);
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
            $sql .= " WHERE (tk.ID_TK LIKE :keyword OR tk.HO_TEN LIKE :keyword 
                      OR tk.EMAIL LIKE :keyword OR tk.SDT_TK LIKE :keyword)";
            $params[':keyword'] = '%' . $keyword . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Lấy tất cả người dùng (Admin - Có phân trang & tìm kiếm)
     */
    public function getAllUsers($keyword = '', $limit = 20, $offset = 0) {
        $sql = "SELECT 
                    tk.ID_TK,
                    tk.HO_TEN,
                    tk.DIA_CHI_AVT,
                    tk.GIOI_TINH,
                    tk.SDT_TK,
                    tk.EMAIL,
                    tk.NGAY_GIO_TAO_TK,
                    tk.NGAY_GIO_CAP_NHAT,
                    nd.ID_ND,
                    nd.PHAN_QUYEN_TK,
                    COUNT(DISTINCT dh.ID_DH) as TONG_DON_HANG,
                    COALESCE(SUM(dh.SO_TIEN_THANH_TOAN), 0) as TONG_CHI_TIEU
                FROM tai_khoan tk
                INNER JOIN nguoi_dung nd ON tk.ID_ND = nd.ID_ND
                LEFT JOIN don_hang dh ON tk.ID_TK = dh.ID_TK";
        
        $params = [];

        if (!empty($keyword)) {
            $sql .= " WHERE (tk.ID_TK LIKE :keyword OR tk.HO_TEN LIKE :keyword 
                      OR tk.EMAIL LIKE :keyword OR tk.SDT_TK LIKE :keyword)";
            $params[':keyword'] = '%' . $keyword . '%';
        }

        $sql .= " GROUP BY tk.ID_TK, tk.HO_TEN, tk.GIOI_TINH, tk.SDT_TK, tk.EMAIL, 
                  tk.NGAY_GIO_TAO_TK, tk.NGAY_GIO_CAP_NHAT, nd.ID_ND, nd.PHAN_QUYEN_TK
                  ORDER BY tk.NGAY_GIO_TAO_TK DESC 
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
        // Query này lấy đủ thông tin cần thiết (bao gồm MAT_KHAU để check)
        $sql = "SELECT 
                    tk.*,
                    nd.PHAN_QUYEN_TK
                FROM tai_khoan tk
                INNER JOIN nguoi_dung nd ON tk.ID_ND = nd.ID_ND
                WHERE tk.ID_TK = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }



    /**
     * Lấy danh sách địa chỉ giao hàng của User (Admin xem)
     */
    public function getUserAddresses($userId) {
        $sql = "SELECT * FROM dia_chi_giao_hang 
                WHERE ID_TK = ? 
                ORDER BY IS_DEFAULT DESC"; // Ưu tiên địa chỉ mặc định lên đầu
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cập nhật địa chỉ cho User (Trong bảng dia_chi_giao_hang)
     */
    public function updateUserAddress($userId, $name, $phone, $addressDetail) {
        try {
            // Kiểm tra xem user đã có địa chỉ mặc định chưa
            $current = $this->getUserAddresses($userId);
            
            if ($current) {
                // Nếu có rồi thì Update
                $sql = "UPDATE dia_chi_giao_hang 
                        SET DIA_CHI_CHI_TIET = ?, TEN_NGUOI_NHAN = ?, SDT_GH = ? 
                        WHERE ID_DIA_CHI = ?";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$addressDetail, $name, $phone, $current['ID_DIA_CHI']]);
            } else {
                // Nếu chưa có thì Insert mới
                $sql = "INSERT INTO dia_chi_giao_hang (ID_TK, TEN_NGUOI_NHAN, SDT_GH, DIA_CHI_CHI_TIET, IS_DEFAULT) 
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
        $sql = "SELECT 
                    dh.ID_DH,
                    dh.NGAY_GIO_TAO_DON,
                    dh.SO_TIEN_THANH_TOAN,
                    dh.TRANG_THAI_THANH_TOAN,
                    dht.TRANG_THAI_DHHT
                FROM don_hang dh
                LEFT JOIN don_hang_hien_tai dht ON dh.ID_DH = dht.ID_DH
                WHERE dh.ID_TK = ?
                ORDER BY dh.NGAY_GIO_TAO_DON DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $userId);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function toggleUserStatus($userId, $status) {
        // Giả sử bạn thêm cột TRANG_THAI vào bảng tai_khoan
        $sql = "UPDATE tai_khoan SET TRANG_THAI = ? WHERE ID_TK = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $userId]);
    }

    /**
     * Xóa người dùng (Admin - Cẩn thận với Foreign Key)
     */
    public function deleteUser($userId) {
        try {
            $this->db->beginTransaction();
            
            // Xóa địa chỉ
            $stmt1 = $this->db->prepare("DELETE FROM dia_chi_giao_hang WHERE ID_TK = ?");
            $stmt1->execute([$userId]);
            
            // Xóa giỏ hàng (nếu không có ràng buộc ON DELETE CASCADE)
            $stmt2 = $this->db->prepare("DELETE FROM gio_hang WHERE ID_TK = ?");
            $stmt2->execute([$userId]);
            
            // Xóa tài khoản
            $stmt3 = $this->db->prepare("DELETE FROM tai_khoan WHERE ID_TK = ?");
            $stmt3->execute([$userId]);
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("UserModel::deleteUser Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Hàm cập nhật thông tin User dành cho Admin (Bao gồm cả Email và Mật khẩu)
     */
    public function adminUpdateCustomer($id, $data) {
        try {
            if (!empty($data['mat_khau'])) {
                $sql = "UPDATE tai_khoan SET HO_TEN=?, SDT_TK=?, GIOI_TINH=?, EMAIL=?, MAT_KHAU=?, NGAY_GIO_CAP_NHAT=NOW() WHERE ID_TK=?";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$data['ho_ten'], $data['sdt'], $data['gioi_tinh'], $data['email'], $data['mat_khau'], $id]);
            } else {
                $sql = "UPDATE tai_khoan SET HO_TEN=?, SDT_TK=?, GIOI_TINH=?, EMAIL=?, NGAY_GIO_CAP_NHAT=NOW() WHERE ID_TK=?";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$data['ho_ten'], $data['sdt'], $data['gioi_tinh'], $data['email'], $id]);
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    // ... (Các hàm cũ giữ nguyên) ...

    /**
     * HÀM MỚI: Lấy danh sách theo vai trò (Để tách KH và AD)
     * $roleId: 'KH' hoặc 'AD'
     */
    public function getUsersByRole($roleId, $search = '', $limit = 100, $offset = 0) {
        $sql = "SELECT tk.*, nd.PHAN_QUYEN_TK 
                FROM tai_khoan tk
                INNER JOIN nguoi_dung nd ON tk.ID_ND = nd.ID_ND
                WHERE tk.ID_ND = ?";
        
        $params = [$roleId];

        if (!empty($search)) {
            $sql .= " AND (tk.HO_TEN LIKE ? OR tk.EMAIL LIKE ? OR tk.SDT_TK LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " ORDER BY tk.NGAY_GIO_TAO_TK DESC LIMIT $limit OFFSET $offset";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * HÀM MỚI: Đếm số lượng theo vai trò (Để phân trang)
     */
    public function countUsersByRole($roleId, $search = '') {
        $sql = "SELECT COUNT(*) as total FROM tai_khoan WHERE ID_ND = ?";
        $params = [$roleId];

        if (!empty($search)) {
            $sql .= " AND (HO_TEN LIKE ? OR EMAIL LIKE ? OR SDT_TK LIKE ?)";
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
            // $newRoleId sẽ là 'AD' hoặc 'KH'
            $sql = "UPDATE tai_khoan SET ID_ND = ? WHERE ID_TK = ?";
            $stmt = $this->db->prepare($sql);
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

        $sql = "SELECT 
                    MONTH(NGAY_GIO_TAO_TK) as thang,
                    COUNT(*) as so_luong
                FROM tai_khoan
                WHERE YEAR(NGAY_GIO_TAO_TK) = ?
                GROUP BY MONTH(NGAY_GIO_TAO_TK)
                ORDER BY thang";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$year]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
     * Lấy top khách hàng chi tiêu nhiều nhất
     */
    public function getTopSpendingUsers($limit = 10) {
        $sql = "SELECT 
                    tk.ID_TK,
                    tk.HO_TEN,
                    tk.EMAIL,
                    tk.SDT_TK,
                    COUNT(DISTINCT dh.ID_DH) as TONG_DON_HANG,
                    COALESCE(SUM(dh.SO_TIEN_THANH_TOAN), 0) as TONG_CHI_TIEU
                FROM tai_khoan tk
                LEFT JOIN don_hang dh ON tk.ID_TK = dh.ID_TK
                GROUP BY tk.ID_TK, tk.HO_TEN, tk.EMAIL, tk.SDT_TK
                HAVING TONG_CHI_TIEU > 0
                ORDER BY TONG_CHI_TIEU DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}