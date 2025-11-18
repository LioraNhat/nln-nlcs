<?php
session_start(); // Bắt đầu session cho giỏ hàng, đăng nhập...

// Dòng này tự động lấy đường dẫn gốc, (ví dụ: /NLN_NLCS/public)
define('BASE_PATH', dirname($_SERVER['SCRIPT_NAME']));

// 1. Tải Autoloader của Composer
require_once __DIR__ . '/../vendor/autoload.php';

// 2. Tải và khởi chạy DotEnv
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// 3. Khởi chạy Router
// Đây là dòng code duy nhất thực thi logic của toàn bộ ứng dụng
$router = new App\Core\Router();
$router->run();

// Xóa tất cả các code test "echo" hoặc "var_dump" ở đây