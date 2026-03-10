<?php

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class CartModel extends BaseModel {
    private function getProductJoins() {
        return "
            LEFT JOIN lo_hang l ON h.id_hh = l.id_hh
            LEFT JOIN gia_ban_hien_tai g ON l.id_lo = g.id_lo
            LEFT JOIN thoi_diem t ON g.id_td = t.id_td
            LEFT JOIN khuyen_mai km ON l.id_km = km.id_km
                AND km.trang_thai_km = 1
                AND NOW() BETWEEN km.ngay_bd_km AND km.ngay_kt_km
        ";
    }

    public function getCartByUserId($id_tk) {
    $stmt = $this->db->prepare("SELECT id_gh FROM gio_hang WHERE id_tk = ?");
    $stmt->execute([$id_tk]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

    /**
     * Lấy toàn bộ giỏ hàng cho user ĐÃ ĐĂNG NHẬP
     */
    public function getCartContentsForUser($id_gh) {
        $sql = "SELECT 
                    ct.id_hh, ct.so_luong,
                    h.ten_hh, h.link_anh,
                    g.gia_hien_tai,
                    IFNULL(km.phan_tram_km, 0) as phan_tram_km
                FROM chi_tiet_gio_hang ct
                INNER JOIN hang_hoa h ON ct.id_hh = h.id_hh
                " . $this->getProductJoins() . "
                WHERE ct.id_gh = ?
                AND h.duoc_phep_ban = 1
                GROUP BY ct.id_hh";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_gh]);
        $itemsFromDb = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $cartItems = [];
        foreach ($itemsFromDb as $item) {
            $cartItems[$item['id_hh']] = [
                'id'               => $item['id_hh'],
                'name'             => $item['ten_hh'],
                'price'            => $item['gia_hien_tai'],
                'image'            => $item['link_anh'],
                'quantity'         => $item['so_luong'],
                'discount_percent' => $item['phan_tram_km']
            ];
        }
        return $cartItems;
    }

    /**
     * Thêm/Cập nhật sản phẩm cho user (Logic "UPSERT")
     */
    public function addProductForUser($id_gh, $id_hh, $quantity) {
        $stmt_check = $this->db->prepare("SELECT so_luong FROM chi_tiet_gio_hang WHERE id_gh = ? AND id_hh = ?");
        $stmt_check->execute([$id_gh, $id_hh]);
        $existing = $stmt_check->fetch();
        if ($existing) {
            $newQuantity = $existing['so_luong'] + $quantity;
            $stmt = $this->db->prepare("UPDATE chi_tiet_gio_hang SET so_luong = ? WHERE id_gh = ? AND id_hh = ?");
            return $stmt->execute([$newQuantity, $id_gh, $id_hh]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO chi_tiet_gio_hang (id_gh, id_hh, so_luong) VALUES (?, ?, ?)");
            return $stmt->execute([$id_gh, $id_hh, $quantity]);
        }
    }

    /**
     * HÀM MỚI: Cập nhật (SET) số lượng cho user
     */
    public function updateProductForUser($id_gh, $id_hh, $quantity) {
        if ($quantity > 0) {
            $stmt = $this->db->prepare("UPDATE chi_tiet_gio_hang SET so_luong = ? WHERE id_gh = ? AND id_hh = ?");
            return $stmt->execute([$quantity, $id_gh, $id_hh]);
        } else {
            return $this->removeProductForUser($id_gh, $id_hh);
        }
    }

    /**
     * HÀM MỚI: Xóa 1 sản phẩm cho user
     */
    public function removeProductForUser($id_gh, $id_hh) {
        $stmt = $this->db->prepare("DELETE FROM chi_tiet_gio_hang WHERE id_gh = ? AND id_hh = ?");
        return $stmt->execute([$id_gh, $id_hh]);
    }

    public function clearCartForUser($id_gh) {
        $stmt = $this->db->prepare("DELETE FROM chi_tiet_gio_hang WHERE id_gh = ?");
        return $stmt->execute([$id_gh]);
    }

    public function getCartItemCountForUser($id_gh) {
        $stmt = $this->db->prepare("SELECT SUM(so_luong) as total FROM chi_tiet_gio_hang WHERE id_gh = ?");
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