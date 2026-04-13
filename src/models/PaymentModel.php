<?php
namespace App\Models;
use App\Core\BaseModel;
use PDO;

class PaymentModel extends BaseModel {

    public function updateMaGiaoDich($id_dh, $ma_giao_dich) {
        $sql = "UPDATE don_hang SET ma_giao_dich = ? WHERE id_dh = ?";
        return $this->db->prepare($sql)->execute([$ma_giao_dich, $id_dh]);
    }

    public function updateThanhToan($id_dh) {
        $sql = "UPDATE don_hang SET trang_thai_thanh_toan = 1, ngay_thanh_toan = NOW() WHERE id_dh = ?";
        return $this->db->prepare($sql)->execute([$id_dh]);
    }

    public function getOrderById($id_dh) {
        $stmt = $this->db->prepare("SELECT * FROM don_hang WHERE id_dh = ?");
        $stmt->execute([$id_dh]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}