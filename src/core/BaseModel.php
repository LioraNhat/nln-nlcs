<?php
namespace App\Core;
use PDO;

abstract class BaseModel {
    
    protected $db; // Thuộc tính để lưu đối tượng PDO

    public function __construct() {
        // Tự động lấy kết nối CSDL khi một Model được khởi tạo
        $this->db = Database::getInstance()->getConnection();
    }
}