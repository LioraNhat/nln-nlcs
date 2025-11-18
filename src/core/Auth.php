<?php

namespace App\Core;
class Auth {
    
    /**
     * Kiểm tra đã đăng nhập chưa
     */
    public static function check() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Lấy thông tin user hiện tại
     */
    public static function user() {
        if (!self::check()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'] ?? null,
            'name' => $_SESSION['user_name'] ?? '',
            'email' => $_SESSION['user_email'] ?? '',
            'phone' => $_SESSION['user_phone'] ?? '',
            'gender' => $_SESSION['user_gender'] ?? '',
            'role_id' => $_SESSION['user_role_id'] ?? '',      // 'AD' hoặc 'KH'
            'role' => $_SESSION['user_role'] ?? '',          // 'Admin' hoặc 'Khách hàng'
            'cart_id' => $_SESSION['cart_id'] ?? null
        ];
    }
    
    /**
     * Lấy ID user
     */
    public static function id() {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Kiểm tra có phải Admin không
     */
    public static function isAdmin() {
        return self::check() && $_SESSION['user_role_id'] === 'AD';
    }
    
    /**
     * Kiểm tra có phải Khách hàng không
     */
    public static function isCustomer() {
        return self::check() && $_SESSION['user_role_id'] === 'KH';
    }
    
    /**
     * Yêu cầu đăng nhập (dùng trong Controller)
     */
    public static function requireLogin() {
        if (!self::check()) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để tiếp tục';
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            header('Location: /auth/login');
            exit;
        }
    }
    
    /**
     * Yêu cầu quyền Admin (dùng trong AdminController)
     */
    public static function requireAdmin() {
        self::requireLogin();
        
        if (!self::isAdmin()) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập khu vực này';
            header('Location: /');
            exit;
        }
    }
    
    /**
     * Đăng xuất
     */
    public static function logout() {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header('Location: /auth/login');
        exit;
    }

    /**
     * HÀM MỚI (QUAN TRỌNG): Lấy ID Giỏ Hàng của user
     */
    public static function cartId() {
         return $_SESSION['cart_id'] ?? null;
    }

    /**
     * HÀM MỚI (BỊ THIẾU): Kiểm tra xem người dùng đã đăng nhập chưa
     */
    public static function isLoggedIn() {
        // Chúng ta dựa vào việc 'user_id' có tồn tại trong session hay không
        return isset($_SESSION['user_id']);
    }

}