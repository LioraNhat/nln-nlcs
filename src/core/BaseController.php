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
     * AUTH + REDIRECT FUNCTIONS (ĐÃ CHỈNH SỬA CHO KHỚP DB)
     * ===============================
     */

    protected function redirect($path) {
        // Chuẩn hóa path
        if ($path !== '/' && isset($path[0]) && $path[0] !== '/') {
            $path = '/' . $path;
        }

        // Sử dụng BASE_PATH đã define ở index.php
        $base = defined('BASE_PATH') ? BASE_PATH : '/NLN_NLCS/public'; // Fallback an toàn
        header('Location: ' . $base . $path);
        exit;
    }

    protected function isLoggedIn() {
        // Kiểm tra xem mảng 'user' có tồn tại trong session không
        return isset($_SESSION['user']) && !empty($_SESSION['user']);
    }

    /**
     * Lấy vai trò người dùng
     * Dựa trên CSDL: ID_ND = 'AD' (Admin) hoặc 'KH' (Khách hàng)
     */
    protected function getRole() {
        if ($this->isLoggedIn() && isset($_SESSION['user']['ID_ND'])) {
            return $_SESSION['user']['ID_ND'];
        }
        return 'guest';
    }

    protected function checkAuth() {
        if (!$this->isLoggedIn()) {
            $this->redirect('/auth/login');
        }
    }

    protected function checkAdmin() {
        // Kiểm tra: Phải đăng nhập VÀ ID_ND phải là 'AD'
        if (!$this->isLoggedIn() || $this->getRole() !== 'AD') {
            $_SESSION['error'] = "Bạn không có quyền truy cập trang quản trị!";
            // Nếu chưa đăng nhập thì về login, nếu là khách thì về trang chủ
            if (!$this->isLoggedIn()) {
                $this->redirect('/auth/login');
            } else {
                $this->redirect('/');
            }
        }
    }
}