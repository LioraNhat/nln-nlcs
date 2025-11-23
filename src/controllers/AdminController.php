<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\UserModel;
use App\Models\CategoryModel;

class AdminController extends BaseController {

    private $orderModel;
    private $productModel;
    private $userModel;
    private $categoryModel;

    public function __construct() {
        parent::__construct();
        
        // Kiểm tra quyền Admin thủ công (thay vì Auth::requireAdmin())
        Auth::requireLogin(); // Đảm bảo đã đăng nhập
        
        $user = Auth::user();
        if (!$user || $user['ID_ND'] !== 'AD') {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này.';
            header('Location: /NLN_NLCS/public/');
            exit;
        }
        
        // Khởi tạo các Model
        $this->orderModel = new OrderModel();
        $this->productModel = new ProductModel();
        $this->userModel = new UserModel();
        $this->categoryModel = new CategoryModel();
    }

    /**
     * Trang Dashboard Admin
     */
    public function dashboard() {
        // Lấy dữ liệu thống kê
        $totalOrders = $this->orderModel->getTotalOrders();
        $totalRevenue = $this->orderModel->getTotalRevenue();
        $totalProducts = $this->productModel->getTotalProducts();
        $totalUsers = $this->userModel->getTotalUsers();
        
        // Lấy đơn hàng gần đây
        $recentOrders = $this->orderModel->getRecentOrders(10);
        
        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);
        
        // Sửa: Đường dẫn view đúng với cấu trúc thư mục
        $this->renderView('admin/dashboard/index', [
            'title' => 'Dashboard - Quản trị',
            'user' => Auth::user(),
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'totalProducts' => $totalProducts,
            'totalUsers' => $totalUsers,
            'recentOrders' => $recentOrders,
            'success' => $success,
            'error' => $error
        ]);
    }

    /**
     * Trang quản lý đơn hàng
     */
    public function orders() {
        $ordersPerPage = 20;
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $searchKeyword = $_GET['search'] ?? '';
        $statusFilter = $_GET['status'] ?? '';
        $offset = ($currentPage - 1) * $ordersPerPage;
        
        $totalOrders = $this->orderModel->countAllOrders($searchKeyword, $statusFilter);
        $totalPages = ceil($totalOrders / $ordersPerPage);
        $orders = $this->orderModel->getAllOrders($searchKeyword, $statusFilter, $ordersPerPage, $offset);
        
        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);
        
        $this->renderView('admin/orders/index', [
            'title' => 'Quản lý đơn hàng',
            'user' => Auth::user(),
            'orders' => $orders,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage,
            'searchKeyword' => $searchKeyword,
            'statusFilter' => $statusFilter,
            'success' => $success,
            'error' => $error
        ]);
    }

    /**
     * Xem chi tiết đơn hàng
     */
    public function orderDetail($orderId) {
        $order = $this->orderModel->getOrderById($orderId);
        
        if (!$order) {
            $_SESSION['error'] = 'Không tìm thấy đơn hàng.';
            $this->redirect('/admin/orders');
            return;
        }
        
        $orderItems = $this->orderModel->getOrderItems($orderId);
        
        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);
        
        $this->renderView('admin/orders/detail', [
            'title' => 'Chi tiết đơn hàng #' . $orderId,
            'user' => Auth::user(),
            'order' => $order,
            'orderItems' => $orderItems,
            'success' => $success,
            'error' => $error
        ]);
    }

    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function updateOrderStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = $_POST['id_dh'];
            $newStatus = $_POST['trang_thai_dh'];
            
            $success = $this->orderModel->updateOrderStatus($orderId, $newStatus);
            
            if ($success) {
                $_SESSION['success'] = "Đã cập nhật trạng thái đơn hàng $orderId thành công.";
            } else {
                $_SESSION['error'] = "Không thể cập nhật trạng thái đơn hàng $orderId.";
            }
            
            // Quay lại trang chi tiết hoặc danh sách
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/admin/orders');
        }
    }

    /**
     * Trang quản lý sản phẩm
     */
    public function products() {
        $productsPerPage = 20;
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $searchKeyword = $_GET['search'] ?? '';
        $offset = ($currentPage - 1) * $productsPerPage;
        
        $totalProducts = $this->productModel->countAllProducts($searchKeyword);
        $totalPages = ceil($totalProducts / $productsPerPage);
        $products = $this->productModel->getAllProducts($searchKeyword, $productsPerPage, $offset);
        
        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);
        
        $this->renderView('admin/products/index', [
            'title' => 'Quản lý sản phẩm',
            'user' => Auth::user(),
            'products' => $products,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage,
            'searchKeyword' => $searchKeyword,
            'success' => $success,
            'error' => $error
        ]);
    }

    /**
     * Trang quản lý người dùng
     */
    public function users() {
        $usersPerPage = 20;
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $searchKeyword = $_GET['search'] ?? '';
        $offset = ($currentPage - 1) * $usersPerPage;
        
        $totalUsers = $this->userModel->countAllUsers($searchKeyword);
        $totalPages = ceil($totalUsers / $usersPerPage);
        $users = $this->userModel->getAllUsers($searchKeyword, $usersPerPage, $offset);
        
        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);
        
        $this->renderView('admin/users/index', [
            'title' => 'Quản lý người dùng',
            'user' => Auth::user(),
            'users' => $users,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage,
            'searchKeyword' => $searchKeyword,
            'success' => $success,
            'error' => $error
        ]);
    }

    /**
     * Xử lý xóa sản phẩm
     */
    public function deleteProduct($id) {
        $result = $this->productModel->deleteProduct($id);

        if ($result) {
            $_SESSION['success'] = "Đã xóa hoặc ngừng kinh doanh sản phẩm $id thành công!";
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra, không thể xử lý sản phẩm $id.";
        }

        $base = defined('BASE_PATH') ? BASE_PATH : '/NLN_NLCS/public';
        header('Location: ' . $base . '/admin/products');
        exit;
    }

    /**
     * Hiển thị form Thêm mới
     */
    public function createProduct() {
        $this->renderView('admin/products/form', [
            'title' => 'Thêm sản phẩm mới',
            'user' => Auth::user(),
            'isEdit' => false,
            'product' => [], // Mảng rỗng
            'loai_hang' => $this->productModel->getAllLoaiHang(),
            'dvt' => $this->productModel->getAllDVT(),
            'khuyen_mai' => $this->productModel->getAllKhuyenMai()
        ]);
    }

    /**
     * Hiển thị form Sửa
     */
    public function editProduct($id) {
        $product = $this->productModel->getProductByIdForAdmin($id);
        
        if (!$product) {
            $_SESSION['error'] = "Không tìm thấy sản phẩm!";
            $base = defined('BASE_PATH') ? BASE_PATH : '/NLN_NLCS/public';
            header('Location: ' . $base . '/admin/products');
            exit;
        }

        $this->renderView('admin/products/form', [
            'title' => 'Cập nhật sản phẩm',
            'user' => Auth::user(),
            'isEdit' => true,
            'product' => $product,
            'loai_hang' => $this->productModel->getAllLoaiHang(),
            'dvt' => $this->productModel->getAllDVT(),
            'khuyen_mai' => $this->productModel->getAllKhuyenMai()
        ]);
    }

    /**
     * Xử lý Lưu dữ liệu (Dùng chung cho cả Thêm và Sửa)
     */
    public function storeProduct() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? ''; // Nếu có ID là sửa, không có là thêm
            
            // Lấy dữ liệu từ form
            $data = [
                'ten_hh' => $_POST['ten_hh'],
                'id_lhh' => $_POST['id_lhh'],
                'id_dvt' => $_POST['id_dvt'],
                'gia_ban' => $_POST['gia_ban'],
                'so_luong_ton' => $_POST['so_luong_ton'],
                'id_km' => !empty($_POST['id_km']) ? $_POST['id_km'] : null,
                'mo_ta_hh' => $_POST['mo_ta_hh'],
                'hsd' => $_POST['hsd'],
                'duoc_phep_ban' => isset($_POST['duoc_phep_ban']) ? 1 : 0,
                'link_anh' => $_POST['old_img'] ?? '' // Mặc định lấy ảnh cũ
            ];

            // Xử lý Upload Ảnh
            if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
                $targetDir = __DIR__ . '/../../public/uploads/';
                // Tạo tên file ngẫu nhiên để tránh trùng
                $fileName = time() . '_' . basename($_FILES['img']['name']);
                $targetFile = $targetDir . $fileName;
                
                if (move_uploaded_file($_FILES['img']['tmp_name'], $targetFile)) {
                    $data['link_anh'] = $fileName; // Cập nhật tên ảnh mới
                }
            }

            // Gọi Model để lưu
            if ($id) {
                // Cập nhật
                $result = $this->productModel->updateProduct($id, $data);
                $msg = "Cập nhật";
            } else {
                // Thêm mới
                $result = $this->productModel->createProduct($data);
                $msg = "Thêm mới";
            }

            if ($result) {
                $_SESSION['success'] = "$msg sản phẩm thành công!";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi $msg sản phẩm.";
            }

            $base = defined('BASE_PATH') ? BASE_PATH : '/NLN_NLCS/public';
            header('Location: ' . $base . '/admin/products');
            exit;
        }
    }

    // ======================================================
    // QUẢN LÝ DANH MỤC (CATEGORIES)
    // ======================================================

    public function categories() {
        // Bỏ chú thích dòng khai báo model trong __construct nếu chưa làm
        // $this->categoryModel = new CategoryModel(); 
        
        $categories = $this->categoryModel->getAllCategories();
        
        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        $this->renderView('admin/categories/index', [
            'title' => 'Quản lý danh mục',
            'user' => Auth::user(),
            'categories' => $categories,
            'success' => $success,
            'error' => $error
        ]);
    }

    // Hiển thị form thêm mới
    public function createCategory() {
        $this->renderView('admin/categories/form', [
            'title' => 'Thêm danh mục mới',
            'user' => Auth::user(),
            'isEdit' => false,
            'category' => []
        ]);
    }

    // Hiển thị form sửa
    public function editCategory($id) {
        $category = $this->categoryModel->getCategoryById($id);
        if (!$category) {
            $_SESSION['error'] = "Không tìm thấy danh mục!";
            $this->redirect('/admin/categories');
        }

        $this->renderView('admin/categories/form', [
            'title' => 'Cập nhật danh mục',
            'user' => Auth::user(),
            'isEdit' => true,
            'category' => $category
        ]);
    }

    // Xử lý Lưu (Thêm/Sửa)
    public function storeCategory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? '';
            $ten_dm = trim($_POST['ten_dm']);

            if (empty($ten_dm)) {
                $_SESSION['error'] = "Tên danh mục không được để trống!";
                $this->redirect($_SERVER['HTTP_REFERER']);
                return;
            }

            if ($id) {
                // Cập nhật
                $result = $this->categoryModel->updateCategory($id, $ten_dm);
                $msg = "Cập nhật";
            } else {
                // Thêm mới
                $result = $this->categoryModel->createCategory($ten_dm);
                $msg = "Thêm mới";
            }

            if ($result) {
                $_SESSION['success'] = "$msg danh mục thành công!";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra!";
            }

            $this->redirect('/admin/categories');
        }
    }

    // Xóa danh mục
    public function deleteCategory($id) {
        $result = $this->categoryModel->deleteCategory($id);
        if ($result) {
            $_SESSION['success'] = "Đã xóa danh mục thành công!";
        } else {
            $_SESSION['error'] = "Không thể xóa! Có thể danh mục này đang chứa loại hàng hóa.";
        }
        $this->redirect('/admin/categories');
    }
}