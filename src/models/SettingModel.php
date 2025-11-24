<?php

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class SettingModel extends BaseModel {

    /**
     * Lấy toàn bộ cấu hình dưới dạng mảng [key => value]
     * Ví dụ: echo $settings['site_title'];
     */
    public function getAllSettings() {
        $stmt = $this->db->query("SELECT * FROM cau_hinh");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['meta_key']] = $row['meta_value'];
        }
        return $settings;
    }

    /**
     * Cập nhật (hoặc thêm mới nếu chưa có) một cấu hình
     */
    public function updateSetting($key, $value) {
        // Kiểm tra xem key đã tồn tại chưa
        $check = $this->db->prepare("SELECT meta_key FROM cau_hinh WHERE meta_key = ?");
        $check->execute([$key]);
        
        if ($check->rowCount() > 0) {
            $sql = "UPDATE cau_hinh SET meta_value = ? WHERE meta_key = ?";
        } else {
            $sql = "INSERT INTO cau_hinh (meta_value, meta_key) VALUES (?, ?)";
        }
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$value, $key]);
    }
}