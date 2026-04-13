<?php

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class StatisticModel extends BaseModel {

    /**
     * 1. Sản phẩm bán chạy nhất
     */
    public function getBestSellingProducts($startDate, $endDate, $limit = 5) {
        // Sửa: TEN_HH -> ten_hh, SO_LUONG_BAN_RA -> so_luong_ban_ra, TRANG_THAI_DHHT -> ghi_chu
        $sql = "SELECT hh.ten_hh AS label, SUM(ct.so_luong_ban_ra) AS value
                FROM chi_tiet_don_hang ct
                JOIN don_hang dh ON ct.id_dh = dh.id_dh
                JOIN don_hang_hien_tai dhht ON dh.id_dh = dhht.id_dh
                JOIN hang_hoa hh ON ct.id_hh = hh.id_hh
                WHERE dh.ngay_gio_tao_don >= ? 
                  AND dh.ngay_gio_tao_don <= ?
                  AND dhht.ghi_chu NOT LIKE '%hủy%'
                GROUP BY hh.id_hh, hh.ten_hh
                ORDER BY value DESC 
                LIMIT " . (int)$limit;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 2. Tổng doanh thu (Lấy Top những ngày doanh thu cao nhất)
     */
    public function getRevenue($startDate, $endDate, $limit = 5) {
        // Sửa: SO_TIEN_THANH_TOAN -> thanh_tien
        $sql = "SELECT DATE_FORMAT(dh.ngay_gio_tao_don, '%d/%m/%Y') AS label, 
                       SUM(dh.thanh_tien) AS value
                FROM don_hang dh
                JOIN don_hang_hien_tai dhht ON dh.id_dh = dhht.id_dh
                WHERE dh.ngay_gio_tao_don >= ? 
                  AND dh.ngay_gio_tao_don <= ?
                  AND dhht.ghi_chu NOT LIKE '%hủy%'
                GROUP BY DATE(dh.ngay_gio_tao_don)
                ORDER BY value DESC
                LIMIT " . (int)$limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 3. Tổng số đơn hàng (Lấy Top những ngày nhiều đơn nhất)
     */
    public function getOrdersCount($startDate, $endDate, $limit = 5) {
        $sql = "SELECT DATE_FORMAT(dh.ngay_gio_tao_don, '%d/%m/%Y') AS label, 
                       COUNT(*) AS value
                FROM don_hang dh
                WHERE dh.ngay_gio_tao_don >= ? 
                  AND dh.ngay_gio_tao_don <= ?
                GROUP BY DATE(dh.ngay_gio_tao_don)
                ORDER BY value DESC
                LIMIT " . (int)$limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 4. Đơn hàng đã hủy
     */
    public function getCancelledOrders($startDate, $endDate, $limit = 5) {
        // Sửa: Lọc theo ghi chu có chữ 'hủy'
        $sql = "SELECT DATE_FORMAT(dh.ngay_gio_tao_don, '%d/%m/%Y') AS label, 
                       COUNT(*) AS value
                FROM don_hang dh
                JOIN don_hang_hien_tai dhht ON dh.id_dh = dhht.id_dh
                WHERE dhht.ghi_chu LIKE '%hủy%'
                  AND dh.ngay_gio_tao_don >= ? 
                  AND dh.ngay_gio_tao_don <= ?
                GROUP BY DATE(dh.ngay_gio_tao_don)
                ORDER BY value DESC
                LIMIT " . (int)$limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 5. Khách hàng mua nhiều nhất (Dựa trên số tiền đã thanh toán)
     */
    public function getTopCustomers($startDate, $endDate, $limit = 5) {
        // Sửa: HO_TEN -> ho_ten, ID_TK -> id_tk
        $sql = "SELECT tk.ho_ten AS label, SUM(dh.thanh_tien) AS value
                FROM don_hang dh
                JOIN don_hang_hien_tai dhht ON dh.id_dh = dhht.id_dh
                JOIN tai_khoan tk ON dh.id_tk = tk.id_tk
                WHERE dh.ngay_gio_tao_don >= ? 
                  AND dh.ngay_gio_tao_don <= ?
                  AND dhht.ghi_chu NOT LIKE '%hủy%'
                GROUP BY tk.id_tk, tk.ho_ten
                ORDER BY value DESC
                LIMIT " . (int)$limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}