<?php

namespace App\Core; // Namespace theo chuẩn PSR-4 đã khai báo

use PDO;
use PDOException;

class Database {
    // Thuộc tính lưu trữ kết nối (instance)
    private static $instance = null;
    
    private $conn;
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $charset; // <-- Thêm thuộc tính này

    // Hàm khởi tạo private để ngăn việc tạo đối tượng từ bên ngoài
    // HÀM KHỞI TẠO (CONSTRUCTOR) - THAY THẾ HÀM CŨ CỦA BẠN BẰNG HÀM NÀY
    private function __construct() {
        // Dùng $_ENV thay vì getenv()
        $this->host = $_ENV['DB_HOST'];
        $this->db_name = $_ENV['DB_NAME'];
        $this->username = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASS'];
        $this->charset = $_ENV['DB_CHARSET'];

        // Cấu hình DSN (Data Source Name)
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=' . $this->charset;

        try {
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Hiển thị lỗi kết nối chi tiết hơn
            echo 'Connection Error: ' . $e->getMessage();
            exit; 
        }
    }

    // Phương thức public static để lấy instance (kết nối)
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Phương thức để lấy đối tượng PDO connection
    public function getConnection() {
        return $this->conn;
    }
}