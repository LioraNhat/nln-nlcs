<?php

namespace App\Core;

class Router {
    protected $controller = 'App\\Controllers\\HomeController'; // Controller mặc định
    protected $method = 'index'; // Method mặc định
    protected $params = []; // Tham số mặc định
    
    // Mảng lưu trữ các route đã đăng ký thủ công
    protected $routes = [];

    public function __construct() {
        // Khởi tạo mảng routes
        $this->routes = [];
    }

    /**
     * Đăng ký route mới
     * @param string $method GET hoặc POST
     * @param string $path Đường dẫn (ví dụ: /account/cancel-order)
     * @param string $handler Controller@Action (ví dụ: AccountController@cancelOrder)
     */
    public function add($method, $path, $handler) {
        // Chuẩn hóa path: đảm bảo luôn bắt đầu bằng / và không kết thúc bằng /
        $path = '/' . trim($path, '/');
        
        // Chuyển đổi tham số {id} thành regex để so khớp
        // Ví dụ: /account/delete-address/{id} => /account/delete-address/([a-zA-Z0-9_-]+)
        $pathRegex = preg_replace('/\{([a-zA-Z0-9_-]+)\}/', '([a-zA-Z0-9_-]+)', $path);
        $pathRegex = "#^" . $pathRegex . "$#";

        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $pathRegex,
            'handler' => $handler
        ];
    }

    /**
     * Chạy ứng dụng
     */
    public function run() {
        $url = $this->parseUrl();
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        
        // Tạo đường dẫn dạng chuỗi để so khớp (ví dụ: /account/index)
        $urlPath = '/' . implode('/', $url);
        if ($urlPath === '/') $urlPath = ''; // Trang chủ

        // 1. ƯU TIÊN: Kiểm tra trong danh sách route đã đăng ký thủ công
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && preg_match($route['path'], $urlPath, $matches)) {
                // Tìm thấy route khớp!
                array_shift($matches); // Loại bỏ phần tử đầu tiên (là toàn bộ chuỗi khớp)
                $this->params = $matches; // Các phần tử còn lại là tham số (ví dụ ID)

                $this->dispatch($route['handler']);
                return;
            }
        }

        // 2. FALLBACK: Nếu không tìm thấy route thủ công, dùng logic tự động cũ
        // Logic cũ: URL[0] là Controller, URL[1] là Method
        if (!empty($url[0])) {
            $controllerName = ucfirst($url[0]) . 'Controller';
            $controllerPath = 'App\\Controllers\\' . $controllerName;

            if (class_exists($controllerPath)) {
                $this->controller = $controllerPath;
                unset($url[0]);
            }
        }

        if (!empty($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        $this->params = $url ? array_values($url) : [];
        
        // Gọi controller mặc định hoặc tự động
        $this->dispatch($this->controller, $this->method);
    }

    /**
     * Helper: Gọi Controller và Method
     */
    protected function dispatch($handler, $method = null) {
        if ($method === null) {
            // Trường hợp handler là chuỗi "Controller@Method"
            list($controller, $method) = explode('@', $handler);
            $controller = "App\\Controllers\\" . $controller;
        } else {
            // Trường hợp logic tự động (handler là tên class controller)
            $controller = $handler;
        }

        if (class_exists($controller)) {
            $controllerInstance = new $controller;
            if (method_exists($controllerInstance, $method)) {
                call_user_func_array([$controllerInstance, $method], $this->params);
            } else {
                echo "Lỗi Router: Method '$method' không tồn tại trong '$controller'.";
            }
        } else {
            echo "Lỗi Router: Controller '$controller' không tồn tại.";
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