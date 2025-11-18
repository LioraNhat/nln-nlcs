<?php

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class CartModel extends BaseModel {

    // Lấy thông tin JOINs cơ bản cho sản phẩm
    private function getProductJoins() {
        // SỬA LỖI: Bỏ 'FROM' và 'WHERE' ra khỏi hàm join
        return "
            INNER JOIN gia_ban_hien_tai g ON h.ID_HH = g.ID_HH
            INNER JOIN thoi_diem t ON g.ID_TD = t.ID_TD
            LEFT JOIN khuyen_mai km ON h.ID_KM = km.ID_KM 
                AND km.TRANG_THAI_KM = 'Đang diễn ra' 
                AND NOW() BETWEEN km.NGAY_BD_KM AND km.NGAY_KT_KM
        ";
    }

    /**
     * HÀM MỚI: Lấy toàn bộ giỏ hàng cho user ĐÃ ĐĂNG NHẬP
     * Trả về mảng có format GIỐNG HỆT $_SESSION['cart']
     */
    public function getCartContentsForUser($id_gh) {
        $sql = "SELECT 
                    ct.ID_HH, ct.SO_LUONG_SP,
                    h.TEN_HH, h.link_anh,
                    g.GIA_HIEN_TAI, km.PHAN_TRAM_KM
                FROM chi_tiet_gio_hang ct
                INNER JOIN hang_hoa h ON ct.ID_HH = h.ID_HH
                " . $this->getProductJoins() . "
                /* SỬA LỖI: Thêm điều kiện WHERE (thời điểm giá) */
                WHERE ct.ID_GH = ?
                AND h.DUOC_PHEP_BAN = 1
                AND (NOW() BETWEEN t.NGAY_BD_GIA_BAN AND t.NGAY_KT_GIA_BAN)
            ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_gh]);
        $itemsFromDb = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $cartItems = [];
        foreach ($itemsFromDb as $item) {
            $cartItems[$item['ID_HH']] = [
                'id' => $item['ID_HH'], 'name' => $item['TEN_HH'],
                'price' => $item['GIA_HIEN_TAI'], 'image' => $item['link_anh'],
                'quantity' => $item['SO_LUONG_SP'], 'discount_percent' => $item['PHAN_TRAM_KM'] ?? 0
            ];
        }
        return $cartItems;
    }

    /**
     * HÀM MỚI: Thêm/Cập nhật sản phẩm cho user (Logic "UPSERT")
     */
    public function addProductForUser($id_gh, $id_hh, $quantity) {
        $stmt_check = $this->db->prepare("SELECT SO_LUONG_SP FROM chi_tiet_gio_hang WHERE ID_GH = ? AND ID_HH = ?");
        $stmt_check->execute([$id_gh, $id_hh]);
        $existing = $stmt_check->fetch();
        if ($existing) {
            $newQuantity = $existing['SO_LUONG_SP'] + $quantity;
            $stmt_update = $this->db->prepare("UPDATE chi_tiet_gio_hang SET SO_LUONG_SP = ? WHERE ID_GH = ? AND ID_HH = ?");
            return $stmt_update->execute([$newQuantity, $id_gh, $id_hh]);
        } else {
            $stmt_insert = $this->db->prepare("INSERT INTO chi_tiet_gio_hang (ID_GH, ID_HH, SO_LUONG_SP) VALUES (?, ?, ?)");
            return $stmt_insert->execute([$id_gh, $id_hh, $quantity]);
        }
    }

    /**
     * HÀM MỚI: Cập nhật (SET) số lượng cho user
     */
    public function updateProductForUser($id_gh, $id_hh, $quantity) {
        if ($quantity > 0) {
            $stmt = $this->db->prepare("UPDATE chi_tiet_gio_hang SET SO_LUONG_SP = ? WHERE ID_GH = ? AND ID_HH = ?");
            return $stmt->execute([$quantity, $id_gh, $id_hh]);
        } else {
            return $this->removeProductForUser($id_gh, $id_hh);
        }
    }

    /**
     * HÀM MỚI: Xóa 1 sản phẩm cho user
     */
    public function removeProductForUser($id_gh, $id_hh) {
        $stmt = $this->db->prepare("DELETE FROM chi_tiet_gio_hang WHERE ID_GH = ? AND ID_HH = ?");
        return $stmt->execute([$id_gh, $id_hh]);
    }

    /**
     * HÀM MỚI: Xóa TOÀN BỘ giỏ hàng cho user
     */
    public function clearCartForUser($id_gh) {
        $stmt = $this->db->prepare("DELETE FROM chi_tiet_gio_hang WHERE ID_GH = ?");
        return $stmt->execute([$id_gh]);
    }

    /**
     * HÀM MỚI: Lấy tổng SỐ LƯỢNG (SUM) cho user
     */
    public function getCartItemCountForUser($id_gh) {
        $stmt = $this->db->prepare("SELECT SUM(SO_LUONG_SP) as total FROM chi_tiet_gio_hang WHERE ID_GH = ?");
        $stmt->execute([$id_gh]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    }

    /**
     * HÀM MỚI: Lấy tổng SỐ LƯỢNG (SUM) cho KHÁCH (GUEST)
     */
    public function getSessionItemCount($sessionCart) {
        $totalQuantity = 0;
        if (empty($sessionCart)) return 0;
        foreach ($sessionCart as $item) {
            $totalQuantity += $item['quantity'];
        }
        return $totalQuantity;
    }

    /**
     * HÀM MỚI (QUAN TRỌNG): Gộp giỏ hàng Session vào CSDL
     */
    public function mergeSessionCartToDb($id_gh, $sessionCart) {
        if (empty($sessionCart)) return true;
        try {
            foreach ($sessionCart as $id_hh => $item) {
                $this->addProductForUser($id_gh, $id_hh, $item['quantity']);
            }
            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}