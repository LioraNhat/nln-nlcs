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
        // Tự động tải danh mục + loại sản phẩm cho mọi trang (menu, sidebar...)
        $categoryModel = new CategoryModel();
        $this->viewData['categories']     = $categoryModel->getAllCategories();
        $this->viewData['productTypes']   = $categoryModel->getAllProductTypes();
    }

    /**
     * HÀM RENDERVIEW NÂNG CẤP (Hỗ trợ layout)
     *
     * @param string $view      file view (VD: 'admin/category/index')
     * @param array  $data      dữ liệu cho view
     * @param string|null $layout tên layout (VD: 'admin')
     */
    protected function renderView($view, $data = [], $layout = null) {
        
        // Gộp dữ liệu chung + dữ liệu riêng
        $mergedData = array_merge($this->viewData, $data);

        // Giải nén biến cho view: $title, $categories, $productTypes...
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

        // ==========================================================
        // BẮT ĐẦU THAY ĐỔI
        // Logic tìm kiếm layout thông minh
        // ==========================================================
        $layoutFile = '';
        $baseViewsPath = __DIR__ . '/../views/';

        if (strpos($layout, '/') === false) {
            // 1. KHÔNG CÓ DẤU '/' (Code cũ)
            // VD: $layout = 'admin' -> tìm /views/layouts/admin.php
            $layoutFile = $baseViewsPath . 'layouts/' . $layout . '.php';
        } else {
            // 2. CÓ DẤU '/' (Code mới)
            // VD: $layout = 'admin/layouts/admin' -> tìm /views/admin/layouts/admin.php
            $layoutFile = $baseViewsPath . $layout . '.php';
        }
        
        // Tải file layout
        if (file_exists($layoutFile)) {
            require $layoutFile;   // Layout có thể sử dụng $content
        } else {
            // Thông báo lỗi chi tiết hơn
            $searchPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $layoutFile);
            echo "Error: Layout '$layout' not found! (Searched at: $searchPath)";
        }
        // ==========================================================
        // KẾT THÚC THAY ĐỔI
        // ==========================================================
    }

    /**
     * ===============================
     * AUTH + REDIRECT FUNCTIONS
     * ===============================
     */

    protected function redirect($path) {
        if ($path[0] !== '/') {
            $path = '/' . $path;
        }

        // Nếu không định nghĩa BASE_PATH, tránh gây lỗi
        $base = defined('BASE_PATH') ? BASE_PATH : '';
        header('Location: ' . $base . $path);
        exit;
    }

    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    protected function getRole() {
        return $_SESSION['user_role'] ?? 'guest';
    }

    protected function checkAuth() {
        if (!$this->isLoggedIn()) {
            $this->redirect('/auth/login');
        }
    }

    protected function checkAdmin() {
        if (!$this->isLoggedIn() || $this->getRole() !== 'AD') {
            $this->redirect('/');
        }
    }
}