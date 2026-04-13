<?php

namespace App\Models;

use App\Core\BaseModel;
use PDO;

class SettingModel extends BaseModel {

    /**
     * Lấy toàn bộ cấu hình
     */
    public function getAllSettings() {
        // Sử dụng try-catch để tránh lỗi sập trang nếu bảng chưa tồn tại
        try {
            $stmt = $this->db->query("SELECT meta_key, meta_value FROM cau_hinh");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $settings = [];
            foreach ($results as $row) {
                $settings[$row['meta_key']] = $row['meta_value'];
            }
            return $settings;
        } catch (\PDOException $e) {
            // Nếu lỗi (ví dụ chưa có bảng), trả về mảng rỗng để không chết trang
            return [];
        }
    }

    /**
     * Cập nhật hoặc thêm mới cấu hình
     */
    public function updateSetting($key, $value) {
        // Sử dụng câu lệnh tối ưu: Nếu trùng Key thì Update, chưa có thì Insert
        $sql = "INSERT INTO cau_hinh (meta_key, meta_value) 
                VALUES (:key, :value) 
                ON DUPLICATE KEY UPDATE meta_value = :value";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':key' => $key,
            ':value' => $value
        ]);
    }
}