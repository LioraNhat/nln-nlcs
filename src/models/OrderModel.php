<?php

namespace App\Models;

use App\Core\BaseModel;
use PDO;
use Exception;

class OrderModel extends BaseModel {

    // ============================================
    // CÁC PHƯƠNG THỨC CHO USER (GIỮ NGUYÊN)
    // ============================================
    
    /**
     * Lấy danh sách đơn hàng theo User ID (Có phân trang & tìm kiếm)
     */
    public function getOrdersByUserId($userId, $keyword, $limit, $offset) {
        $sqlWhere = "WHERE dh.ID_TK = :userId";
        $params = [':userId' => $userId];

        if (!empty($keyword)) {
            $sqlWhere .= " AND dh.ID_DH LIKE :keyword";
            $params[':keyword'] = '%' . $keyword . '%';
        }

        $sql = "
            SELECT 
                dh.ID_DH, 
                dh.NGAY_GIO_TAO_DON, 
                dh.SO_TIEN_THANH_TOAN, 
                dht.TRANG_THAI_DHHT
            FROM don_hang dh
            LEFT JOIN don_hang_hien_tai dht ON dh.ID_DH = dht.ID_DH
            $sqlWhere
            ORDER BY dh.NGAY_GIO_TAO_DON DESC
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

    /**
     * Đếm tổng số đơn hàng của User
     */
    public function countOrdersByUserId($userId, $keyword) {
        $sqlWhere = "WHERE dh.ID_TK = :userId";
        $params = [':userId' => $userId];

        if (!empty($keyword)) {
            $sqlWhere .= " AND dh.ID_DH LIKE :keyword";
            $params[':keyword'] = '%' . $keyword . '%';
        }

        $sql = "SELECT COUNT(dh.ID_DH) as total FROM don_hang dh $sqlWhere";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Lấy phương thức thanh toán
     */
    public function getAllPaymentMethods() {
        $stmt = $this->db->query("SELECT * FROM phuong_thuc_thanh_toan");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tạo mã đơn hàng ngẫu nhiên
     */
    public function generateNewOrderId() {
        do {
            $newId = 'DH' . substr(strtoupper(uniqid()), -3);
            $stmt = $this->db->prepare("SELECT 1 FROM don_hang WHERE ID_DH = ?");
            $stmt->execute([$newId]);
            $exists = $stmt->fetch();
        } while ($exists);
        return $newId;
    }

    /**
     * Tạo mới đơn hàng
     */
    public function createOrder($data) {
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
     * Thêm chi tiết sản phẩm vào đơn hàng (ĐÃ SỬA: Lưu thêm đơn giá bán)
     */
    public function addOrderDetails($orderId, $cartItems) {
        // [SỬA LỖI QUAN TRỌNG] Thêm cột `don_gia_ban` vào câu lệnh INSERT
        $sql = "INSERT INTO chi_tiet_don_hang (ID_DH, ID_HH, SO_LUONG_BAN_RA, don_gia_ban) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        try {
            $this->db->beginTransaction();
            foreach ($cartItems as $itemId => $item) {
                // Tính giá bán sau giảm giá (nếu có) để lưu vào lịch sử
                $finalPrice = $item['price'];
                if (isset($item['discount_percent']) && $item['discount_percent'] > 0) {
                    $finalPrice = $item['price'] * (1 - $item['discount_percent'] / 100);
                }
                
                // Thực thi lệnh insert
                $stmt->execute([$orderId, $itemId, $item['quantity'], $finalPrice]);
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Tạo trạng thái ban đầu cho đơn hàng
     */
    public function createInitialOrderStatus($orderId) {
        $sql = "INSERT INTO don_hang_hien_tai (ID_DH, TRANG_THAI_DHHT, NGAY_GIO_CAP_NHAT) 
                VALUES (?, 'Chờ xử lý', NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$orderId]);
    }

    /**
     * Hủy đơn hàng (User)
     */
    public function cancelUserOrder($orderId, $userId) {
        try {
            $this->db->beginTransaction();

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

            if (!$orderToCancel) {
                $this->db->rollBack();
                return false;
            }

            $sql_update = "UPDATE don_hang_hien_tai 
                           SET TRANG_THAI_DHHT = 'Đã hủy', NGAY_GIO_CAP_NHAT = NOW() 
                           WHERE ID_DH = ?";
            $stmt_update = $this->db->prepare($sql_update);
            $stmt_update->execute([$orderId]);

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("OrderModel::cancelUserOrder Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * =================================================================
     * PHẦN 3: CÁC HÀM THỐNG KÊ (DÙNG CHO DASHBOARD)
     * =================================================================
     */

    public function getTotalOrders() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM don_hang");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    public function getTotalRevenue() {
        // Chỉ tính tổng tiền các đơn KHÔNG BỊ HỦY
        $sql = "SELECT SUM(dh.SO_TIEN_THANH_TOAN) as total 
                FROM don_hang dh
                LEFT JOIN don_hang_hien_tai dht ON dh.ID_DH = dht.ID_DH
                WHERE dht.TRANG_THAI_DHHT != 'Đã hủy'";
        $stmt = $this->db->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    public function getRecentOrders($limit = 5) {
        $sql = "SELECT dh.*, tk.HO_TEN, dht.TRANG_THAI_DHHT 
                FROM don_hang dh
                LEFT JOIN tai_khoan tk ON dh.ID_TK = tk.ID_TK
                LEFT JOIN don_hang_hien_tai dht ON dh.ID_DH = dht.ID_DH
                ORDER BY dh.NGAY_GIO_TAO_DON DESC LIMIT " . (int)$limit;
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Đếm tổng số đơn hàng (Admin - Có filter TẤT CẢ thuộc tính)
     */
    public function countAllOrders($keyword = '', $statusFilter = '') {
        $sqlWhere = "WHERE 1=1";
        $params = [];

        if (!empty($keyword)) {
            // Tìm kiếm trên nhiều cột: Mã ĐH, Tên KH, SĐT, Email, Địa chỉ, Trạng thái, Số tiền
            $sqlWhere .= " AND (
                dh.ID_DH LIKE :keyword 
                OR tk.HO_TEN LIKE :keyword 
                OR tk.SDT_TK LIKE :keyword 
                OR tk.EMAIL LIKE :keyword
                OR dh.DIA_CHI_GIAO_DH LIKE :keyword
                OR dht.TRANG_THAI_DHHT LIKE :keyword
                OR dh.TRANG_THAI_THANH_TOAN LIKE :keyword
                OR CAST(dh.SO_TIEN_THANH_TOAN AS CHAR) LIKE :keyword
            )";
            $params[':keyword'] = '%' . $keyword . '%';
        }

        if (!empty($statusFilter)) {
            $sqlWhere .= " AND dht.TRANG_THAI_DHHT = :status";
            $params[':status'] = $statusFilter;
        }

        $sql = "SELECT COUNT(*) as total 
                FROM don_hang dh
                LEFT JOIN don_hang_hien_tai dht ON dh.ID_DH = dht.ID_DH
                LEFT JOIN tai_khoan tk ON dh.ID_TK = tk.ID_TK
                $sqlWhere";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Lấy danh sách đơn hàng (Có phân trang & filter TẤT CẢ thuộc tính)
     */
    public function getAllOrders($keyword = '', $statusFilter = '', $limit = 20, $offset = 0) {
        $sqlWhere = "WHERE 1=1";
        $params = [];

        if (!empty($keyword)) {
            // Tìm kiếm trên nhiều cột: Mã ĐH, Tên KH, SĐT, Email, Địa chỉ, Trạng thái, Số tiền
            $sqlWhere .= " AND (
                dh.ID_DH LIKE :keyword 
                OR tk.HO_TEN LIKE :keyword 
                OR tk.SDT_TK LIKE :keyword 
                OR tk.EMAIL LIKE :keyword
                OR dh.DIA_CHI_GIAO_DH LIKE :keyword
                OR dht.TRANG_THAI_DHHT LIKE :keyword
                OR dh.TRANG_THAI_THANH_TOAN LIKE :keyword
                OR CAST(dh.SO_TIEN_THANH_TOAN AS CHAR) LIKE :keyword
            )";
            $params[':keyword'] = '%' . $keyword . '%';
        }

        if (!empty($statusFilter)) {
            $sqlWhere .= " AND dht.TRANG_THAI_DHHT = :status";
            $params[':status'] = $statusFilter;
        }

        $sql = "SELECT 
                    dh.ID_DH,
                    dh.NGAY_GIO_TAO_DON,
                    dh.SO_TIEN_THANH_TOAN,
                    dh.TRANG_THAI_THANH_TOAN,
                    dht.TRANG_THAI_DHHT,
                    tk.HO_TEN,
                    tk.SDT_TK,
                    tk.ID_TK,
                    dh.DIA_CHI_GIAO_DH
                FROM don_hang dh
                LEFT JOIN don_hang_hien_tai dht ON dh.ID_DH = dht.ID_DH
                LEFT JOIN tai_khoan tk ON dh.ID_TK = tk.ID_TK
                $sqlWhere
                ORDER BY 
                    FIELD(dht.TRANG_THAI_DHHT, 
                        'Chờ xử lý', 
                        'Đã xác nhận', 
                        'Đang giao hàng', 
                        'Giao hàng thành công', 
                        'Đã hủy', 
                        'Khách hủy') ASC, 
                    dh.NGAY_GIO_TAO_DON DESC
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
     * =================================================================
     * PHẦN 2: DÙNG CHO CHI TIẾT ĐƠN HÀNG & CẬP NHẬT
     * =================================================================
     */
    public function getOrderById($orderId) {
        $sql = "SELECT 
                    dh.*,
                    dht.TRANG_THAI_DHHT,
                    dht.NGAY_GIO_CAP_NHAT,
                    tk.HO_TEN,
                    tk.SDT_TK,
                    tk.EMAIL,
                    pttt.TEN_PTTT
                FROM don_hang dh
                LEFT JOIN don_hang_hien_tai dht ON dh.ID_DH = dht.ID_DH
                LEFT JOIN tai_khoan tk ON dh.ID_TK = tk.ID_TK
                LEFT JOIN phuong_thuc_thanh_toan pttt ON dh.ID_PTTT = pttt.ID_PTTT
                WHERE dh.ID_DH = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách sản phẩm (ĐÃ SỬA: Lấy giá gốc lúc mua + ĐVT)
     */
    public function getOrderItems($orderId) {
        $sql = "SELECT 
                    ctdh.*,
                    hh.TEN_HH,
                    hh.link_anh,
                    d.DVT,
                    ctdh.don_gia_ban as DON_GIA, -- Lấy giá lúc mua trong bảng chi tiết
                    (ctdh.SO_LUONG_BAN_RA * ctdh.don_gia_ban) as THANH_TIEN
                FROM chi_tiet_don_hang ctdh
                LEFT JOIN hang_hoa hh ON ctdh.ID_HH = hh.ID_HH
                LEFT JOIN dvt d ON hh.ID_DVT = d.ID_DVT -- Join thêm bảng ĐVT
                WHERE ctdh.ID_DH = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function updateOrderStatus($id, $status) {
        try {
            // Bắt đầu giao dịch để đảm bảo tính toàn vẹn dữ liệu
            $this->db->beginTransaction();

            // 1. Cập nhật trạng thái xử lý (Bảng don_hang_hien_tai)
            $check = $this->db->prepare("SELECT ID_DH FROM don_hang_hien_tai WHERE ID_DH = ?");
            $check->execute([$id]);
            
            if ($check->rowCount() > 0) {
                $sql = "UPDATE don_hang_hien_tai SET TRANG_THAI_DHHT = ?, NGAY_GIO_CAP_NHAT = NOW() WHERE ID_DH = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$status, $id]);
            } else {
                $sql = "INSERT INTO don_hang_hien_tai (ID_DH, TRANG_THAI_DHHT, NGAY_GIO_CAP_NHAT) VALUES (?, ?, NOW())";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$id, $status]);
            }

            // 2. LOGIC MỚI: Nếu giao thành công -> Tự động set Đã thanh toán
            if ($status === 'Giao hàng thành công') {
                $sqlPayment = "UPDATE don_hang 
                               SET TRANG_THAI_THANH_TOAN = 'Đã thanh toán', 
                                   NGAY_THANH_TOAN = NOW() 
                               WHERE ID_DH = ?";
                $stmtPayment = $this->db->prepare($sqlPayment);
                $stmtPayment->execute([$id]);
            }

            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}