<?php

namespace App\Core;

class Auth {
    
    /**
     * Kiểm tra đã đăng nhập chưa
     */
    public static function check() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return isset($_SESSION['user']);
    }
    
    /**
     * Lấy thông tin user hiện tại (Trả về đúng mảng từ CSDL)
     */
    public static function user() {
        if (!self::check()) {
            return null;
        }
        // Trả về nguyên bản $_SESSION['user'] để khớp với AdminController
        return $_SESSION['user'];
    }
    
    /**
     * Lấy ID user (ID_TK)
     */
    public static function id() {
        // Lấy từ mảng user hoặc lấy từ session lẻ đều được
        return $_SESSION['user_id'] ?? ($_SESSION['user']['ID_TK'] ?? null);
    }
    
    /**
     * Kiểm tra có phải Admin không
     */
    public static function isAdmin() {
        // Kiểm tra key ID_ND trong mảng user
        return self::check() && isset($_SESSION['user']['ID_ND']) && $_SESSION['user']['ID_ND'] === 'AD';
    }
    
    /**
     * Kiểm tra có phải Khách hàng không
     */
    public static function isCustomer() {
        return self::check() && isset($_SESSION['user']['ID_ND']) && $_SESSION['user']['ID_ND'] === 'KH';
    }
    
    /**
     * Helper: Lấy đường dẫn gốc (Fix lỗi Index of /)
     */
    private static function getBasePath() {
        return defined('BASE_PATH') ? BASE_PATH : '/NLN_NLCS/public';
    }

    /**
     * Yêu cầu đăng nhập (dùng trong Controller)
     */
    public static function requireLogin() {
        if (!self::check()) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để tiếp tục';
            // Sửa đường dẫn redirect cho đúng
            header('Location: ' . self::getBasePath() . '/auth/login');
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
            // Sửa đường dẫn redirect về trang chủ dự án
            header('Location: ' . self::getBasePath() . '/');
            exit;
        }
    }
    
    /**
     * Đăng xuất
     */
    public static function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        
        header('Location: ' . self::getBasePath() . '/auth/login');
        exit;
    }

    /**
     * Lấy ID Giỏ Hàng của user
     */
    public static function cartId() {
         return $_SESSION['cart_id'] ?? ($_SESSION['user']['ID_GH'] ?? null);
    }

    /**
     * Kiểm tra đăng nhập (Alias cho check)
     */
    public static function isLoggedIn() {
        return self::check();
    }
}