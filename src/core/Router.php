<?php

namespace App\Core;

class Router {
    protected $controller = 'App\\Controllers\\HomeController'; // Controller mặc định
    protected $method = 'index'; // Method mặc định
    protected $params = []; // Tham số mặc định

    public function __construct() {
        $url = $this->parseUrl();

        // 1. Xử lý Controller
        if (!empty($url[0])) {
            // Tên file controller: (ví dụ: 'Product' -> 'ProductController')
            $controllerName = ucfirst($url[0]) . 'Controller';
            // Namespace đầy đủ: (ví dụ: 'App\Controllers\ProductController')
            $controllerPath = 'App\\Controllers\\' . $controllerName;

            if (class_exists($controllerPath)) {
                $this->controller = $controllerPath;
                unset($url[0]);
            }
        }

        // 2. Xử lý Method
        if (!empty($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // 3. Xử lý Tham số (Params)
        $this->params = $url ? array_values($url) : [];
    }

    /**
     * Chạy ứng dụng
     */
    public function run() {
        try {
            // Khởi tạo controller
            $controllerInstance = new $this->controller;

            // Gọi method với các tham số
            call_user_func_array(
                [$controllerInstance, $this->method],
                $this->params
            );
        } catch (\Exception $e) {
            echo "Lỗi Router: " . $e->getMessage();
            // (Nên tạo trang 404)
        }
    }

    /**
     * Phân tích URL từ $_GET['url']
     */
    protected function parseUrl() {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        return [];
    }
}