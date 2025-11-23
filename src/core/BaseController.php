<?php

namespace App\Core;

use App\Models\CategoryModel;

abstract class BaseController {

    /**
     * Chứa dữ liệu dùng chung cho mọi view
     */
    protected $viewData = [];

    /**
     * __construct() chạy cho tất cả controller
     */
    public function __construct() {
        // 1. Đảm bảo Session luôn được bật
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 2. Tự động tải danh mục (Có kiểm tra lỗi để tránh sập web nếu chưa có Model)
        if (class_exists('App\Models\CategoryModel')) {
            try {
                $categoryModel = new CategoryModel();
                // Chỉ gọi hàm nếu nó tồn tại
                if (method_exists($categoryModel, 'getAllCategories')) {
                    $this->viewData['categories'] = $categoryModel->getAllCategories();
                }
                if (method_exists($categoryModel, 'getAllProductTypes')) {
                    $this->viewData['productTypes'] = $categoryModel->getAllProductTypes();
                }
            } catch (\Exception $e) {
                // Nếu lỗi DB, gán mảng rỗng để web vẫn chạy được
                $this->viewData['categories'] = [];
            }
        }
    }

    /**
     * HÀM RENDERVIEW NÂNG CẤP (Hỗ trợ layout)
     */
    protected function renderView($view, $data = [], $layout = null) {
        
        // Gộp dữ liệu chung + dữ liệu riêng
        $mergedData = array_merge($this->viewData, $data);

        // Giải nén biến cho view
        extract($mergedData);

        // File view
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            echo "Error: View '$view' not found!";
            return;
        }

        // Nếu KHÔNG dùng layout → render trực tiếp
        if ($layout === null) {
            require $viewFile;
            return;
        }

        // DÙNG LAYOUT → bắt nội dung view vào buffer
        ob_start();
        require $viewFile;
        $content = ob_get_clean();  // Nội dung view được lưu vào $content

        // Logic tìm kiếm layout thông minh
        $layoutFile = '';
        $baseViewsPath = __DIR__ . '/../views/';

        if (strpos($layout, '/') === false) {
            // VD: $layout = 'admin' -> tìm /views/layouts/admin.php
            $layoutFile = $baseViewsPath . 'layouts/' . $layout . '.php';
        } else {
            // VD: $layout = 'admin/layouts/admin' -> tìm đúng đường dẫn đó
            $layoutFile = $baseViewsPath . $layout . '.php';
        }
        
        // Tải file layout
        if (file_exists($layoutFile)) {
            require $layoutFile;   // Layout có thể sử dụng $content
        } else {
            $searchPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $layoutFile);
            echo "Error: Layout '$layout' not found! (Searched at: $searchPath)";
        }
    }

    /**
     * ===============================
     * AUTH + REDIRECT FUNCTIONS
     * ===============================
     */

    protected function redirect($path) {
        // Chuẩn hóa path
        if ($path !== '/' && isset($path[0]) && $path[0] !== '/') {
            $path = '/' . $path;
        }

        // Sử dụng BASE_PATH đã define ở index.php
        $base = defined('BASE_PATH') ? BASE_PATH : '';
        header('Location: ' . $base . $path);
        exit;
    }

    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    /**
     * SỬA QUAN TRỌNG Ở ĐÂY:
     * Phải lấy 'user_role_id' (chứa 'AD' hoặc 'KH') để so sánh
     */
    protected function getRole() {
        return $_SESSION['user_role_id'] ?? 'guest';
    }

    protected function checkAuth() {
        if (!$this->isLoggedIn()) {
            $this->redirect('/auth/login');
        }
    }

    protected function checkAdmin() {
        // Kiểm tra đăng nhập VÀ kiểm tra Role ID phải là 'AD'
        if (!$this->isLoggedIn() || $this->getRole() !== 'AD') {
            $_SESSION['error'] = "Bạn không có quyền truy cập!";
            $this->redirect('/');
        }
    }
}