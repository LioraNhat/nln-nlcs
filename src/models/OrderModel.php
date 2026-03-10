<?php

namespace App\Models;

use App\Core\BaseModel;
use PDO;
use Exception;

class OrderModel extends BaseModel {

    // =========================================
    // USER ORDER
    // =========================================

    public function getOrdersByUserId($userId, $keyword, $limit, $offset) {

        $sqlWhere = "WHERE dh.id_tk = :userId";
        $params = [':userId' => $userId];

        if (!empty($keyword)) {
            $sqlWhere .= " AND dh.id_dh LIKE :keyword";
            $params[':keyword'] = '%' . $keyword . '%';
        }

        $sql = "
            SELECT 
                dh.id_dh, 
                dh.ngay_gio_tao_don, 
                dh.thanh_tien,
                dh.trang_thai_thanh_toan,
                dht.id_ttd,
                dmt.ten_trang_thai
            FROM don_hang dh
            LEFT JOIN don_hang_hien_tai dht ON dh.id_dh = dht.id_dh
            LEFT JOIN danh_muc_trang_thai dmt ON dht.id_ttd = dmt.id_ttd
            $sqlWhere
            ORDER BY dh.ngay_gio_tao_don DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function countOrdersByUserId($userId, $keyword) {

        $sqlWhere = "WHERE dh.id_tk = :userId";
        $params = [':userId' => $userId];

        if (!empty($keyword)) {
            $sqlWhere .= " AND dh.id_dh LIKE :keyword";
            $params[':keyword'] = '%' . $keyword . '%';
        }

        $sql = "SELECT COUNT(dh.id_dh) as total FROM don_hang dh $sqlWhere";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? (int)$result['total'] : 0;
    }


    public function getAllPaymentMethods() {

        $stmt = $this->db->query("SELECT * FROM phuong_thuc_thanh_toan");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function generateNewOrderId() {

        do {

            $newId = 'DH' . str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);

            $stmt = $this->db->prepare("SELECT 1 FROM don_hang WHERE id_dh = ?");
            $stmt->execute([$newId]);

            $exists = $stmt->fetch();

        } while ($exists);

        return $newId;
    }


    public function createOrder($data) {

        $sql = "INSERT INTO don_hang 
                    (id_dh, id_pttt, id_tk, id_dc,
                     ngay_gio_tao_don,
                     tong_gia_tri_don, tien_giam_gia, thanh_tien,
                     trang_thai_thanh_toan)
                VALUES 
                    (:id_dh, :id_pttt, :id_tk, :id_dc,
                     NOW(),
                     :tong_gia_tri, :giam_gia, :thanh_tien,
                     :trang_thai_tt)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id_dh' => $data['id_dh'],
            ':id_pttt' => $data['id_pttt'],
            ':id_tk' => $data['id_tk'],
            ':id_dc' => $data['id_dc'],
            ':tong_gia_tri' => $data['tong_gia_tri_don'],
            ':giam_gia' => $data['tien_giam_gia'],
            ':thanh_tien' => $data['thanh_tien'],
            ':trang_thai_tt' => $data['trang_thai_thanh_toan']
        ]);
    }


    public function addOrderDetails($orderId, $cartItems) {

        $sql = "INSERT INTO chi_tiet_don_hang (id_dh, id_hh, id_lo, so_luong_ban_ra) VALUES (?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);

        try {

            $this->db->beginTransaction();

            foreach ($cartItems as $itemId => $item) {

                $quantity = $item['quantity'] ?? $item['so_luong'] ?? 0;

                $id_lo = $item['id_lo'] ?? null;

                $stmt->execute([$orderId, $itemId, $id_lo, $quantity]);
            }

            $this->db->commit();

            return true;

        } catch (Exception $e) {

            $this->db->rollBack();

            error_log($e->getMessage());

            return false;
        }
    }


    public function createInitialOrderStatus($orderId) {

        $sql = "INSERT INTO don_hang_hien_tai (id_dh, id_ttd, thoi_gian, ghi_chu) 
                VALUES (?, 'TTD01', NOW(), 'Đơn hàng mới tạo')";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([$orderId]);
    }


    public function cancelUserOrder($orderId, $userId) {

        try {

            $this->db->beginTransaction();

            $sql_check = "SELECT dh.id_dh 
                          FROM don_hang dh
                          JOIN don_hang_hien_tai dht ON dh.id_dh = dht.id_dh
                          WHERE dh.id_dh = ? AND dh.id_tk = ? AND dht.id_ttd = 'TTD01'
                          FOR UPDATE";

            $stmt_check = $this->db->prepare($sql_check);

            $stmt_check->execute([$orderId, $userId]);

            if (!$stmt_check->fetch()) {

                $this->db->rollBack();

                return false;
            }

            $sql_update = "UPDATE don_hang_hien_tai 
                           SET id_ttd = 'TTD05', thoi_gian = NOW(), ghi_chu = 'Khách hủy đơn'
                           WHERE id_dh = ?";

            $this->db->prepare($sql_update)->execute([$orderId]);

            $this->db->commit();

            return true;

        } catch (Exception $e) {

            $this->db->rollBack();

            return false;
        }
    }


    // =========================================
    // DASHBOARD
    // =========================================

    public function getTotalOrders() {

        $stmt = $this->db->query("SELECT COUNT(*) as total FROM don_hang");

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }


    public function getTotalRevenue() {

        $sql = "SELECT SUM(dh.thanh_tien) as total 
                FROM don_hang dh
                LEFT JOIN don_hang_hien_tai dht ON dh.id_dh = dht.id_dh
                WHERE dht.id_ttd != 'TTD05'";

        $stmt = $this->db->query($sql);

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }


    public function getRecentOrders($limit = 5) {

        $sql = "SELECT dh.*, tk.ho_ten, dmt.ten_trang_thai
                FROM don_hang dh
                LEFT JOIN tai_khoan tk ON dh.id_tk = tk.id_tk
                LEFT JOIN don_hang_hien_tai dht ON dh.id_dh = dht.id_dh
                LEFT JOIN danh_muc_trang_thai dmt ON dht.id_ttd = dmt.id_ttd
                ORDER BY dh.ngay_gio_tao_don DESC LIMIT " . (int)$limit;

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }


    // =========================================
    // ADMIN ORDERS
    // =========================================

    public function countAllOrders($keyword = '', $statusFilter = '') {

        $sqlWhere = "WHERE 1=1";

        $params = [];

        if (!empty($keyword)) {

            $sqlWhere .= " AND (
                dh.id_dh LIKE :keyword 
                OR tk.ho_ten LIKE :keyword 
                OR tk.sdt_tk LIKE :keyword
                OR CAST(dh.thanh_tien AS CHAR) LIKE :keyword
            )";

            $params[':keyword'] = '%' . $keyword . '%';
        }

        if (!empty($statusFilter)) {

            $sqlWhere .= " AND dht.id_ttd = :status";

            $params[':status'] = $statusFilter;
        }

        $sql = "SELECT COUNT(*) as total 
                FROM don_hang dh
                LEFT JOIN don_hang_hien_tai dht ON dh.id_dh = dht.id_dh
                LEFT JOIN tai_khoan tk ON dh.id_tk = tk.id_tk
                $sqlWhere";

        $stmt = $this->db->prepare($sql);

        $stmt->execute($params);

        return (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    }


    public function getAllOrders($keyword = '', $statusFilter = '', $limit = 20, $offset = 0) {

        $sqlWhere = "WHERE 1=1";

        $params = [];

        if (!empty($keyword)) {

            $sqlWhere .= " AND (
                dh.id_dh LIKE :keyword 
                OR tk.ho_ten LIKE :keyword 
                OR tk.sdt_tk LIKE :keyword
                OR CAST(dh.thanh_tien AS CHAR) LIKE :keyword
            )";

            $params[':keyword'] = '%' . $keyword . '%';
        }

        if (!empty($statusFilter)) {

            $sqlWhere .= " AND dht.id_ttd = :status";

            $params[':status'] = $statusFilter;
        }

        $sql = "SELECT 
                    dh.id_dh,
                    dh.ngay_gio_tao_don,
                    dh.thanh_tien,
                    dh.trang_thai_thanh_toan,
                    dht.id_ttd,
                    dmt.ten_trang_thai,
                    tk.ho_ten,
                    tk.sdt_tk,
                    tk.id_tk
                FROM don_hang dh
                LEFT JOIN don_hang_hien_tai dht ON dh.id_dh = dht.id_dh
                LEFT JOIN danh_muc_trang_thai dmt ON dht.id_ttd = dmt.id_ttd
                LEFT JOIN tai_khoan tk ON dh.id_tk = tk.id_tk
                $sqlWhere
                ORDER BY dh.ngay_gio_tao_don DESC
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


    // =========================================
    // ORDER DETAIL
    // =========================================

    public function getOrderById($orderId) {

        $sql = "SELECT 
                    dh.*,
                    dht.id_ttd,
                    dmt.ten_trang_thai,
                    dht.thoi_gian,
                    tk.ho_ten,
                    tk.sdt_tk,
                    tk.email_tk,
                    pttt.ten_pttt,
                    dc.ten_nguoi_nhan,
                    dc.sdt_gh,
                    dc.dia_chi_chi_tiet,
                    dc.ten_xa_phuong,
                    dc.ten_quan_huyen,
                    dc.ten_tinh_tp
                FROM don_hang dh
                LEFT JOIN don_hang_hien_tai dht ON dh.id_dh = dht.id_dh
                LEFT JOIN danh_muc_trang_thai dmt ON dht.id_ttd = dmt.id_ttd
                LEFT JOIN tai_khoan tk ON dh.id_tk = tk.id_tk
                LEFT JOIN phuong_thuc_thanh_toan pttt ON dh.id_pttt = pttt.id_pttt
                LEFT JOIN dia_chi_giao_hang dc ON dh.id_dc = dc.id_dc
                WHERE dh.id_dh = ?";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([$orderId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getOrderItems($orderId) {

        $sql = "SELECT 
                    ctdh.id_hh,
                    ctdh.so_luong_ban_ra,
                    hh.ten_hh,
                    hh.link_anh,
                    d.dvt,
                    g.gia_hien_tai as don_gia,
                    (ctdh.so_luong_ban_ra * g.gia_hien_tai) as thanh_tien
                FROM chi_tiet_don_hang ctdh
                LEFT JOIN hang_hoa hh ON ctdh.id_hh = hh.id_hh
                LEFT JOIN dvt d ON hh.id_dvt = d.id_dvt
                LEFT JOIN lo_hang l ON ctdh.id_lo = l.id_lo
                LEFT JOIN gia_ban_hien_tai g ON l.id_lo = g.id_lo
                    AND g.id_td IN (
                        SELECT id_td FROM thoi_diem
                        WHERE NOW() BETWEEN ngay_bd_gia_ban AND ngay_kt_gia_ban
                    )
                WHERE ctdh.id_dh = ?";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([$orderId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function updateOrderStatus($id, $status) {

        try {

            $this->db->beginTransaction();

            $check = $this->db->prepare("SELECT id_dh FROM don_hang_hien_tai WHERE id_dh = ?");

            $check->execute([$id]);

            if ($check->rowCount() > 0) {

                $sql = "UPDATE don_hang_hien_tai 
                        SET id_ttd = ?, thoi_gian = NOW() 
                        WHERE id_dh = ?";

                $this->db->prepare($sql)->execute([$status, $id]);

            } else {

                $sql = "INSERT INTO don_hang_hien_tai 
                        (id_dh, id_ttd, thoi_gian) 
                        VALUES (?, ?, NOW())";

                $this->db->prepare($sql)->execute([$id, $status]);
            }

            if ($status === 'TTD04') {

                $sql = "UPDATE don_hang 
                        SET trang_thai_thanh_toan = 1,
                            ngay_thanh_toan = NOW()
                        WHERE id_dh = ?";

                $this->db->prepare($sql)->execute([$id]);
            }

            $this->db->commit();

            return true;

        } catch (\Exception $e) {

            $this->db->rollBack();

            return false;
        }
    }
}