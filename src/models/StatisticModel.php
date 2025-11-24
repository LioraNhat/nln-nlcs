<?php

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class StatisticModel extends BaseModel {

    /**
     * 1. Sản phẩm bán chạy nhất (Top 10)
     * Logic: Tổng số lượng bán ra trong khoảng thời gian, trừ đơn hủy.
     */
    public function getBestSellingProducts($startDate, $endDate) {
        $sql = "SELECT hh.TEN_HH AS label, SUM(ct.SO_LUONG_BAN_RA) AS value
                FROM chi_tiet_don_hang ct
                JOIN don_hang dh ON ct.ID_DH = dh.ID_DH
                JOIN don_hang_hien_tai dhht ON dh.ID_DH = dhht.ID_DH
                JOIN hang_hoa hh ON ct.ID_HH = hh.ID_HH
                WHERE dh.NGAY_GIO_TAO_DON >= ? 
                  AND dh.NGAY_GIO_TAO_DON <= ?
                  AND dhht.TRANG_THAI_DHHT != 'Đã hủy'
                GROUP BY hh.ID_HH, hh.TEN_HH
                ORDER BY value DESC 
                LIMIT 10";
        
        $stmt = $this->db->prepare($sql);
        // Thêm giờ phút giây để bao trọn ngày kết thúc
        $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 2. Tổng doanh thu (Theo ngày)
     * Logic: Tổng tiền đơn hàng (trừ đơn hủy)
     */
    public function getRevenue($startDate, $endDate) {
        $sql = "SELECT DATE_FORMAT(dh.NGAY_GIO_TAO_DON, '%d/%m/%Y') AS label, 
                       SUM(dh.SO_TIEN_THANH_TOAN) AS value
                FROM don_hang dh
                JOIN don_hang_hien_tai dhht ON dh.ID_DH = dhht.ID_DH
                WHERE dh.NGAY_GIO_TAO_DON >= ? 
                  AND dh.NGAY_GIO_TAO_DON <= ?
                  AND dhht.TRANG_THAI_DHHT != 'Đã hủy'
                GROUP BY DATE(dh.NGAY_GIO_TAO_DON)
                ORDER BY dh.NGAY_GIO_TAO_DON ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 3. Tổng số đơn hàng (Theo ngày)
     */
    public function getOrdersCount($startDate, $endDate) {
        $sql = "SELECT DATE_FORMAT(dh.NGAY_GIO_TAO_DON, '%d/%m/%Y') AS label, 
                       COUNT(*) AS value
                FROM don_hang dh
                WHERE dh.NGAY_GIO_TAO_DON >= ? 
                  AND dh.NGAY_GIO_TAO_DON <= ?
                GROUP BY DATE(dh.NGAY_GIO_TAO_DON)
                ORDER BY dh.NGAY_GIO_TAO_DON ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 4. Đơn hàng đã hủy (Theo ngày)
     */
    public function getCancelledOrders($startDate, $endDate) {
        $sql = "SELECT DATE_FORMAT(dh.NGAY_GIO_TAO_DON, '%d/%m/%Y') AS label, 
                       COUNT(*) AS value
                FROM don_hang dh
                JOIN don_hang_hien_tai dhht ON dh.ID_DH = dhht.ID_DH
                WHERE dhht.TRANG_THAI_DHHT = 'Đã hủy'
                  AND dh.NGAY_GIO_TAO_DON >= ? 
                  AND dh.NGAY_GIO_TAO_DON <= ?
                GROUP BY DATE(dh.NGAY_GIO_TAO_DON)
                ORDER BY dh.NGAY_GIO_TAO_DON ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 5. Khách hàng mua nhiều nhất (Top 5)
     */
    public function getTopCustomers($startDate, $endDate) {
        $sql = "SELECT tk.HO_TEN AS label, SUM(dh.SO_TIEN_THANH_TOAN) AS value
                FROM don_hang dh
                JOIN don_hang_hien_tai dhht ON dh.ID_DH = dhht.ID_DH
                JOIN tai_khoan tk ON dh.ID_TK = tk.ID_TK
                WHERE dh.NGAY_GIO_TAO_DON >= ? 
                  AND dh.NGAY_GIO_TAO_DON <= ?
                  AND dhht.TRANG_THAI_DHHT != 'Đã hủy'
                GROUP BY tk.ID_TK, tk.HO_TEN
                ORDER BY value DESC
                LIMIT 5";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}