<?php

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class OrderModel extends BaseModel {

    /**
     * SỬA LẠI: Lấy đơn hàng (có hỗ trợ Tìm kiếm và Phân trang)
     */
    public function getOrdersByUserId($userId, $keyword, $limit, $offset) {
        
        // 1. Khởi tạo
        $sqlWhere = "WHERE dh.ID_TK = :userId";
        $params = [':userId' => $userId];

        // 2. Thêm logic tìm kiếm (nếu có keyword)
        if (!empty($keyword)) {
            $sqlWhere .= " AND dh.ID_DH LIKE :keyword"; // Tìm theo Mã đơn hàng
            $params[':keyword'] = '%' . $keyword . '%';
        }

        // 3. Câu lệnh SQL
        $sql = "
            SELECT 
                dh.ID_DH, 
                dh.NGAY_GIO_TAO_DON, 
                dh.SO_TIEN_THANH_TOAN, 
                dht.TRANG_THAI_DHHT
            FROM don_hang dh
            -- SỬA: Luôn LEFT JOIN don_hang_hien_tai
            LEFT JOIN don_hang_hien_tai dht ON dh.ID_DH = dht.ID_DH
            $sqlWhere
            ORDER BY dh.NGAY_GIO_TAO_DON DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->db->prepare($sql);

        // Gán tham số (params)
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        // Gán LIMIT/OFFSET (phải là INT)
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * HÀM MỚI: Đếm tổng số đơn hàng (để phân trang)
     */
    public function countOrdersByUserId($userId, $keyword) {
        // ... (Code của bạn giữ nguyên) ...
        $sqlWhere = "WHERE dh.ID_TK = :userId";
        $params = [':userId' => $userId];
        if (!empty($keyword)) {
            $sqlWhere .= " AND dh.ID_DH LIKE :keyword";
            $params[':keyword'] = '%' . $keyword . '%';
        }
        $sql = "
            SELECT COUNT(dh.ID_DH) as total
            FROM don_hang dh
            $sqlWhere
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * HÀM MỚI: Lấy tất cả Phương thức thanh toán (PTTT)
     */
    public function getAllPaymentMethods() {
        // ... (Code của bạn giữ nguyên) ...
        $stmt = $this->db->query("SELECT * FROM phuong_thuc_thanh_toan");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * HÀM MỚI (Helper): Tạo ID Đơn hàng
     */
    public function generateNewOrderId() {
        // ... (Code của bạn giữ nguyên) ...
        do {
            $newId = 'DH' . substr(strtoupper(uniqid()), -3);
            $stmt = $this->db->prepare("SELECT 1 FROM don_hang WHERE ID_DH = ?");
            $stmt->execute([$newId]);
            $exists = $stmt->fetch();
        } while ($exists);
        return $newId;
    }

    /**
     * HÀM MỚI: Tạo đơn hàng (Lưu vào bảng don_hang)
     */
    public function createOrder($data) {
        // ... (Code của bạn giữ nguyên) ...
        $sql = "INSERT INTO don_hang 
                    (ID_DH, ID_PTTT, ID_TK, DIA_CHI_GIAO_DH, 
                     NGAY_GIO_TAO_DON, NGAY_DU_KIEN_GIAO, 
                     TONG_GIA_TRI_DH, TIEN_GIAM_GIA, SO_TIEN_THANH_TOAN,
                     TRANG_THAI_THANH_TOAN, TRANG_THAI_BL)
                VALUES 
                    (:id_dh, :id_pttt, :id_tk, :dia_chi, 
                     NOW(), DATE_ADD(NOW(), INTERVAL 3 DAY), 
                     :tong_gia_tri, :giam_gia, :thanh_toan,
                     :trang_thai_tt, 'Chưa đánh giá')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_dh' => $data['ID_DH'],
            ':id_pttt' => $data['ID_PTTT'],
            ':id_tk' => $data['ID_TK'],
            ':dia_chi' => $data['DIA_CHI_GIAO_DH'],
            ':tong_gia_tri' => $data['TONG_GIA_TRI_DH'],
            ':giam_gia' => $data['TIEN_GIAM_GIA'],
            ':thanh_toan' => $data['SO_TIEN_THANH_TOAN'],
            ':trang_thai_tt' => $data['TRANG_THAI_THANH_TOAN']
        ]);
    }

    /**
     * HÀM MỚI: Thêm chi tiết đơn hàng (Lưu vào chi_tiet_don_hang)
     */
    public function addOrderDetails($orderId, $cartItems) {
        // ... (Code của bạn giữ nguyên) ...
        $sql = "INSERT INTO chi_tiet_don_hang (ID_DH, ID_HH, SO_LUONG_BAN_RA) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        try {
            $this->db->beginTransaction();
            foreach ($cartItems as $itemId => $item) {
                $stmt->execute([ $orderId, $itemId, $item['quantity'] ]);
            }
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    // ======================================================
    // THÊM MỚI: TẠO TRẠNG THÁI "CHỜ XỬ LÝ" BAN ĐẦU
    // ======================================================
    public function createInitialOrderStatus($orderId) {
        // Hàm này sẽ được gọi TỪ CheckoutController NGAY SAU KHI tạo đơn
        $sql = "INSERT INTO don_hang_hien_tai (ID_DH, TRANG_THAI_DHHT, NGAY_GIO_CAP_NHAT) 
                VALUES (?, 'Chờ xử lý', NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$orderId]);
    }

    // ======================================================
    // THÊM MỚI: HÀM HỦY ĐƠN HÀNG (CHO KHÁCH HÀNG)
    // ======================================================
    public function cancelUserOrder($orderId, $userId) {
        // Hàm này được gọi TỪ AccountController
        try {
            $this->db->beginTransaction();

            // Bước 1: Kiểm tra 2 điều kiện an toàn:
            // 1. Đơn hàng này có phải của User (ID_TK) này không?
            // 2. Trạng thái hiện tại có phải là 'Chờ xử lý' không?
            $sql_check = "SELECT dh.ID_DH 
                          FROM don_hang dh
                          JOIN don_hang_hien_tai dht ON dh.ID_DH = dht.ID_DH
                          WHERE dh.ID_DH = ? 
                            AND dh.ID_TK = ? 
                            AND dht.TRANG_THAI_DHHT = 'Chờ xử lý'
                          FOR UPDATE";
                          
            $stmt_check = $this->db->prepare($sql_check);
            $stmt_check->execute([$orderId, $userId]);
            $orderToCancel = $stmt_check->fetch();

            // Nếu không tìm thấy (vì không_phải_của_user hoặc không_phải_chờ_xử_lý)
            if (!$orderToCancel) {
                $this->db->rollBack();
                return false; // Báo thất bại
            }

            // Bước 2: Nếu tìm thấy, cập nhật trạng thái
            $sql_update = "UPDATE don_hang_hien_tai 
                           SET TRANG_THAI_DHHT = 'Đã hủy', NGAY_GIO_CAP_NHAT = NOW() 
                           WHERE ID_DH = ?";
            $stmt_update = $this->db->prepare($sql_update);
            $stmt_update->execute([$orderId]);
            
            // (Trong một hệ thống thực tế, chúng ta sẽ cộng lại số lượng tồn kho ở đây)

            $this->db->commit();
            return true; // Báo thành công

        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Lỗi khi hủy đơn hàng (OrderModel): " . $e->getMessage());
            return false;
        }
    }
}