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
                cttt.id_ttd, -- Lấy từ bảng chi_tiet_trang_thai
                dmt.ten_trang_thai
            FROM don_hang dh
            LEFT JOIN don_hang_hien_tai dht ON dh.id_dh = dht.id_dh
            LEFT JOIN chi_tiet_trang_thai cttt ON dht.id_log = cttt.id_log -- JOIN trung gian
            LEFT JOIN danh_muc_trang_thai dmt ON cttt.id_ttd = dmt.id_ttd
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
                    (id_dh, id_pttt, id_tk, id_dc, ngay_gio_tao_don,
                    tong_gia_tri_don, tien_giam_gia, thanh_tien, trang_thai_thanh_toan)
                VALUES 
                    (:id_dh, :id_pttt, :id_tk, :id_dc, NOW(),
                    :tong_gia_tri_don, :tien_giam_gia, :thanh_tien, :trang_thai_thanh_toan)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_dh'                 => $data['id_dh'],
            ':id_pttt'               => $data['id_pttt'],
            ':id_tk'                 => $data['id_tk'],
            ':id_dc'                 => $data['id_dc'],
            ':tong_gia_tri_don'      => $data['tong_gia_tri_don'],
            ':tien_giam_gia'         => $data['tien_giam_gia'],
            ':thanh_tien'            => $data['thanh_tien'],
            ':trang_thai_thanh_toan' => $data['trang_thai_thanh_toan']
        ]);
    }

    public function addOrderDetails($orderId, $cartItems) {
        try {
            $this->db->beginTransaction();

            // Lấy id_td đang hiệu lực
            $stmtTd = $this->db->query("
                SELECT id_td FROM thoi_diem 
                WHERE NOW() BETWEEN ngay_bd_gia_ban AND ngay_kt_gia_ban 
                LIMIT 1
            ");
            $td = $stmtTd->fetch(PDO::FETCH_ASSOC);
            $id_td = $td['id_td'] ?? null;

            foreach ($cartItems as $itemId => $item) {
                $quantity = (int)($item['quantity'] ?? 0);

                // 1. Tìm lô HSD gần nhất còn đủ hàng (FEFO)
                $stmtLo = $this->db->prepare("
                    SELECT id_lo, so_luong_con_lai, gia_von_nhap
                    FROM lo_hang
                    WHERE id_hh = ? 
                    AND so_luong_con_lai >= ?
                    AND id_trang_thai_lo NOT IN ('TTL03', 'TTL05')
                    ORDER BY hsd_lo ASC
                    LIMIT 1
                ");
                $stmtLo->execute([$itemId, $quantity]);
                $lot = $stmtLo->fetch(PDO::FETCH_ASSOC);

                if (!$lot) {
                    throw new \Exception("Sản phẩm {$item['name']} không đủ hàng trong kho.");
                }

                $id_lo = $lot['id_lo'];

                // 2. Lấy giá bán + % KM tại thời điểm đặt
                $stmtGia = $this->db->prepare("
                    SELECT g.gia_hien_tai, km.phan_tram_km
                    FROM gia_ban_hien_tai g
                    LEFT JOIN lo_hang l ON g.id_lo = l.id_lo
                    LEFT JOIN khuyen_mai km ON l.id_km = km.id_km
                    WHERE g.id_lo = ? AND g.id_td = ?
                    LIMIT 1
                ");
                $stmtGia->execute([$id_lo, $id_td]);
                $giaInfo = $stmtGia->fetch(PDO::FETCH_ASSOC);

                $don_gia   = (float)($giaInfo['gia_hien_tai'] ?? 0);
                $phan_tram = (float)($giaInfo['phan_tram_km'] ?? 0);
                $gia_sau_km = $don_gia * (1 - $phan_tram / 100);

                // 3. Lưu chi tiết đơn hàng (kèm giá tại thời điểm đặt)
                $this->db->prepare("
                    INSERT INTO chi_tiet_don_hang 
                        (id_dh, id_hh, id_lo, so_luong_ban_ra, don_gia, gia_sau_km) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ")->execute([$orderId, $itemId, $id_lo, $quantity, $don_gia, $gia_sau_km]);

                // 4. Trừ tồn kho lô
                $this->db->prepare("
                    UPDATE lo_hang 
                    SET so_luong_con_lai = so_luong_con_lai - ?
                    WHERE id_lo = ?
                ")->execute([$quantity, $id_lo]);

                // 5. Cập nhật trạng thái lô sau khi trừ
                $this->db->prepare("
                    UPDATE lo_hang SET id_trang_thai_lo = 
                        CASE 
                            WHEN so_luong_con_lai = 0 THEN 'TTL03'
                            WHEN so_luong_con_lai <= 5 THEN 'TTL02'
                            ELSE id_trang_thai_lo
                        END
                    WHERE id_lo = ?
                ")->execute([$id_lo]);
            }

            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Lỗi lưu chi tiết đơn hàng: " . $e->getMessage());
            throw $e; // ném lại để CheckoutController bắt được
        }
    }


    public function createInitialOrderStatus($orderId) {
        try {
            $this->db->beginTransaction();

            // 1. Tạo bản ghi trong don_hang_hien_tai
            $sqlLog = "INSERT INTO don_hang_hien_tai (id_dh, ghi_chu) VALUES (?, 'Đơn hàng mới tạo')";
            $stmtLog = $this->db->prepare($sqlLog);
            $stmtLog->execute([$orderId]);
            
            // Lấy id_log vừa tự động tăng (AUTO_INCREMENT)
            $idLog = $this->db->lastInsertId();

            // 2. Tạo bản ghi trong chi_tiet_trang_thai (nơi chứa id_ttd)
            $sqlStatus = "INSERT INTO chi_tiet_trang_thai (id_ttd, id_log, CTTT_ThoiDiem) VALUES ('TTD01', ?, NOW())";
            $stmtStatus = $this->db->prepare($sqlStatus);
            $stmtStatus->execute([$idLog]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }


    public function cancelUserOrder($orderId, $userId) {
        try {
            $this->db->beginTransaction();

            // Kiểm tra đơn hàng có thuộc user và đang ở trạng thái 'Chờ xử lý' (TTD01) không
            $sql_check = "SELECT dht.id_log 
                        FROM don_hang dh
                        JOIN don_hang_hien_tai dht ON dh.id_dh = dht.id_dh
                        JOIN chi_tiet_trang_thai cttt ON dht.id_log = cttt.id_log
                        WHERE dh.id_dh = ? AND dh.id_tk = ? AND cttt.id_ttd = 'TTD01'
                        FOR UPDATE";

            $stmt_check = $this->db->prepare($sql_check);
            $stmt_check->execute([$orderId, $userId]);
            $row = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                $this->db->rollBack();
                return false;
            }

            $idLog = $row['id_log'];

            // Cập nhật mã trạng thái sang TTD05 (Đã hủy)
            $sql_update_status = "UPDATE chi_tiet_trang_thai 
                                SET id_ttd = 'TTD05', CTTT_ThoiDiem = NOW() 
                                WHERE id_log = ?";
            $this->db->prepare($sql_update_status)->execute([$idLog]);

            // Cập nhật lại ghi chú bên bảng log
            $sql_update_log = "UPDATE don_hang_hien_tai SET ghi_chu = 'Khách hủy đơn' WHERE id_log = ?";
            $this->db->prepare($sql_update_log)->execute([$idLog]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
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
        // Chúng ta cần JOIN qua chi_tiet_trang_thai để lấy được id_ttd
        $sql = "SELECT SUM(dh.thanh_tien) as total 
                FROM don_hang dh
                LEFT JOIN don_hang_hien_tai dht ON dh.id_dh = dht.id_dh
                LEFT JOIN chi_tiet_trang_thai cttt ON dht.id_log = cttt.id_log
                WHERE cttt.id_ttd != 'TTD05'";

        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'] ?? 0;
    }


    public function getRecentOrders($limit = 5) {
        $sql = "SELECT dh.*, tk.ho_ten, dmt.ten_trang_thai
                FROM don_hang dh
                LEFT JOIN tai_khoan tk ON dh.id_tk = tk.id_tk
                LEFT JOIN don_hang_hien_tai dht ON dh.id_dh = dht.id_dh
                -- JOIN thêm bảng chi_tiet_trang_thai để lấy id_ttd
                LEFT JOIN chi_tiet_trang_thai cttt ON dht.id_log = cttt.id_log
                -- Sau đó mới JOIN đến bảng danh mục để lấy tên
                LEFT JOIN danh_muc_trang_thai dmt ON cttt.id_ttd = dmt.id_ttd
                ORDER BY dh.ngay_gio_tao_don DESC 
                LIMIT " . (int)$limit;

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

        // VỊ TRÍ SỬA 1: Thay dht.id_ttd thành cttt.id_ttd
        if (!empty($statusFilter)) {
            $sqlWhere .= " AND cttt.id_ttd = :status";
            $params[':status'] = $statusFilter;
        }

        // VỊ TRÍ SỬA 2: Cập nhật SELECT và các phép JOIN
        $sql = "SELECT 
                    dh.id_dh,
                    dh.ngay_gio_tao_don,
                    dh.thanh_tien,
                    dh.trang_thai_thanh_toan,
                    cttt.id_ttd,            -- Lấy từ bảng chi tiết
                    cttt.CTTT_ThoiDiem,     -- Lấy thời điểm từ bảng mới
                    dmt.ten_trang_thai,
                    tk.ho_ten,
                    tk.sdt_tk,
                    tk.id_tk
                FROM don_hang dh
                -- Join qua bảng trung gian để lấy log hiện tại
                LEFT JOIN don_hang_hien_tai dht ON dh.id_dh = dht.id_dh
                LEFT JOIN chi_tiet_trang_thai cttt ON dht.id_log = cttt.id_log
                -- Join để lấy tên trạng thái
                LEFT JOIN danh_muc_trang_thai dmt ON cttt.id_ttd = dmt.id_ttd
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
                    cttt.id_ttd,            -- Lấy từ chi_tiet_trang_thai
                    dmt.ten_trang_thai,
                    cttt.CTTT_ThoiDiem as thoi_gian, -- Tên cột đúng trong SQL
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
                LEFT JOIN chi_tiet_trang_thai cttt ON dht.id_log = cttt.id_log -- JOIN trung gian
                LEFT JOIN danh_muc_trang_thai dmt ON cttt.id_ttd = dmt.id_ttd
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
                -- JOIN lấy giá dựa trên lô hàng và thời điểm hiện tại
                LEFT JOIN gia_ban_hien_tai g ON l.id_lo = g.id_lo
                LEFT JOIN thoi_diem td ON g.id_td = td.id_td
                WHERE ctdh.id_dh = ? 
                AND (NOW() BETWEEN td.ngay_bd_gia_ban AND td.ngay_kt_gia_ban OR td.id_td IS NULL)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function updateOrderStatus($id, $status) {
        try {
            $this->db->beginTransaction();

            // 1. Kiểm tra xem đơn hàng đã có log trong don_hang_hien_tai chưa
            $check = $this->db->prepare("SELECT id_log FROM don_hang_hien_tai WHERE id_dh = ?");
            $check->execute([$id]);
            $log = $check->fetch(PDO::FETCH_ASSOC);

            if ($log) {
                $id_log = $log['id_log'];
                // Cập nhật trạng thái mới vào chi_tiet_trang_thai
                $sql = "UPDATE chi_tiet_trang_thai 
                        SET id_ttd = ?, CTTT_ThoiDiem = NOW() 
                        WHERE id_log = ?";
                $this->db->prepare($sql)->execute([$status, $id_log]);
            } else {
                // Nếu chưa có (trường hợp hy hữu), tạo mới cả 2 bảng
                $this->db->prepare("INSERT INTO don_hang_hien_tai (id_dh) VALUES (?)")->execute([$id]);
                $id_log = $this->db->lastInsertId();
                
                $this->db->prepare("INSERT INTO chi_tiet_trang_thai (id_ttd, id_log, CTTT_ThoiDiem) VALUES (?, ?, NOW())")
                        ->execute([$status, $id_log]);
            }

            // 2. Nếu trạng thái là 'Giao thành công' (TTD04), cập nhật bảng don_hang
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
            error_log("Lỗi cập nhật trạng thái: " . $e->getMessage());
            return false;
        }
    }
}