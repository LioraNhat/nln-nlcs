<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\UserModel;
use App\Models\CategoryModel;
use App\Models\PromotionModel;
use App\Models\InventoryModel;
use App\Models\SupplierModel;
use App\Models\StatisticModel;
use App\Models\SettingModel;

class AdminController extends BaseController {

    private $orderModel;
    private $productModel;
    private $userModel;
    private $categoryModel;
    private $promotionModel;
    private $inventoryModel;
    private $supplierModel;
    private $statisticModel;
    private $settingModel;

    public function __construct() {
        parent::__construct();
        
        Auth::requireLogin();
        
        $user = Auth::user();
        if (!$user || $user['id_nd'] !== 'AD') {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này.';
            header('Location: /NLN_NLCS/public/');
            exit;
        }
        
        // Khởi tạo các Model
        $this->orderModel = new OrderModel();
        $this->productModel = new ProductModel();
        $this->userModel = new UserModel();
        $this->categoryModel = new CategoryModel();
        $this->promotionModel = new PromotionModel();
        $this->inventoryModel = new InventoryModel();
        $this->supplierModel = new SupplierModel();
        $this->statisticModel = new StatisticModel();
        $this->settingModel = new SettingModel();
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
    // ======================================================
    // QUẢN LÝ NGƯỜI DÙNG (CHỈNH SỬA)
    // ======================================================

    public function users() {
        $search = $_GET['search'] ?? '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $customers = $this->userModel->getUsersByRole('KH', $search, $limit, $offset);
        $totalCustomers = $this->userModel->countUsersByRole('KH', $search);
        $totalPages = ceil($totalCustomers / $limit);

        $admins = $this->userModel->getUsersByRole('AD', $search, 50, 0);

        $this->renderView('admin/users/index', [
            'title' => 'Quản lý Người dùng',
            'user' => Auth::user(),
            'customers' => $customers,
            'admins' => $admins,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'searchKeyword' => $search,
            'success' => $_SESSION['success'] ?? null,
            'error' => $_SESSION['error'] ?? null
        ]);
        unset($_SESSION['success'], $_SESSION['error']);
    }

    /**
     * HÀM MỚI: Xử lý đổi quyền (POST)
     */
    public function updateUserRole() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['user_id'];
            $role = $_POST['role_id']; // 'AD' hoặc 'KH'

            // Không cho phép tự hạ quyền chính mình để tránh mất quyền Admin
            if ($id == $_SESSION['user']['id_tk']) {
                $_SESSION['error'] = "Bạn không thể tự thay đổi quyền của chính mình!";
                $this->redirect('/admin/users');
                return;
            }

            if ($this->userModel->updateRole($id, $role)) {
                $_SESSION['success'] = "Đã thay đổi phân quyền thành công!";
            } else {
                $_SESSION['error'] = "Lỗi cập nhật quyền!";
            }
            $this->redirect('/admin/users');
        }
    }

    // QUẢN LÝ HÀNG HÓA

    public function products() {
        $productsPerPage = 20;
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $searchKeyword = $_GET['search'] ?? '';
        $offset = ($currentPage - 1) * $productsPerPage;
        
        $totalProducts = $this->productModel->countAllProducts($searchKeyword);
        $totalPages = ceil($totalProducts / $productsPerPage);
        $products = $this->productModel->getAllProducts($searchKeyword, $productsPerPage, $offset);

        $this->renderView('admin/products/index', [
            'title' => 'Quản lý sản phẩm & Lô hàng',
            'user' => Auth::user(),
            'products' => $products,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage,
            'searchKeyword' => $searchKeyword,
            'success' => $_SESSION['success'] ?? null,
            'error' => $_SESSION['error'] ?? null
        ]);
        unset($_SESSION['success'], $_SESSION['error']);
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

    public function storeProduct() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Xác định ID
            $id = $_POST['id'] ?? '';
            $isUpdate = !empty($id);

            if (!$isUpdate) {
                $id = $this->productModel->generateNewId(); // Dùng hàm sinh ID mới trong Model
            }

            // 2. Xử lý Ảnh (Giữ nguyên logic cũ của bạn)
            $link_anh = $_POST['old_img'] ?? ''; 
            if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
                $targetDir = __DIR__ . '/../../public/uploads/';
                $fileName = $id . '.png';
                $targetFile = $targetDir . $fileName;
                if (move_uploaded_file($_FILES['img']['tmp_name'], $targetFile)) {
                    $link_anh = $fileName;
                }
            }

            // 3. LẤY GIÁ BÁN RIÊNG (Quan trọng)
            $gia_ban = $_POST['gia_ban'] ?? 0;

            // 4. Gom dữ liệu bảng HÀNG HÓA (Lưu ý: Đã xóa 'gia_ban' khỏi mảng này)
            $data = [
                'id_hh'        => $id,
                'ten_hh'       => $_POST['ten_hh'],
                'id_loai2'       => $_POST['id_lhh'],
                'id_dvt'       => $_POST['id_dvt'],
                'id_km'        => !empty($_POST['id_km']) ? $_POST['id_km'] : null,
                'mo_ta_hh'     => $_POST['mo_ta_hh'],
                'duoc_phep_ban'=> isset($_POST['duoc_phep_ban']) ? 1 : 0,
                'link_anh'     => $link_anh,
                'la_hang_sx'   => 1,
                'phan_tram_loi_nhuan' => $_POST['phan_tram_loi_nhuan'] ?? 30
            ];

            // 5. GỌI MODEL XỬ LÝ (SỬA ĐOẠN NÀY)
            if ($isUpdate) {
                $this->productModel->updateProduct($id, $data);
                $this->productModel->updatePrice($id, $gia_ban);
                
                $msg = "Cập nhật";
            } else {
                $this->productModel->createProduct($data);
                $this->productModel->insertPrice($id, $gia_ban);
                $msg = "Thêm mới";
            }

            $_SESSION['success'] = "$msg sản phẩm thành công! (Mã: $id)";
            $base = defined('BASE_PATH') ? BASE_PATH : '/NLN_NLCS/public';
            header('Location: ' . $base . '/admin/products');
            exit;
        }
    }

    // ======================================================
    // QUẢN LÝ DANH MỤC (CATEGORIES)
    // ======================================================

    public function categories() {
        $search = $_GET['search'] ?? '';
        
        $categories = $this->categoryModel->getAllCategories($search);
        
        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        $this->renderView('admin/categories/index', [
            'title' => 'Quản lý danh mục',
            'user' => Auth::user(),
            'categories' => $categories,
            'searchKeyword' => $search,
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
            // Lấy ID từ input name="id" trong form
            $id = $_POST['id'] ?? ''; 
            $ten_dm = trim($_POST['ten_dm']);

            if (empty($ten_dm)) {
                $_SESSION['error'] = "Tên danh mục không được để trống!";
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            }

            // Kiểm tra thực tế biến $id có giá trị hay không để quyết định Update/Create
            if (!empty($id)) {
                $result = $this->categoryModel->updateCategory($id, $ten_dm);
                $msg = "Cập nhật";
            } else {
                $result = $this->categoryModel->createCategory($ten_dm);
                $msg = "Thêm mới";
            }

            if ($result) {
                $_SESSION['success'] = "$msg danh mục thành công!";
            } else {
                $_SESSION['error'] = "Lỗi Database: Không thể $msg danh mục!";
            }
            
            // SỬA TẠI ĐÂY: Dùng đường dẫn đầy đủ để tránh lỗi 404
            header('Location: ' . BASE_PATH . '/admin/categories');
            exit;
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

    // ======================================================
    // QUẢN LÝ LOẠI HÀNG HÓA (PRODUCT TYPES / SUB-CATEGORIES)
    // ======================================================

    public function productTypes() {
        $search = $_GET['search'] ?? ''; // 1. Lấy từ khóa
        $productTypes = $this->categoryModel->getAllProductTypesWithCategory($search);
        
        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        $this->renderView('admin/product-types/index', [
            'title' => 'Quản lý loại hàng hóa',
            'user' => Auth::user(),
            'productTypes' => $productTypes,
            'searchKeyword' => $search,
            'success' => $success,
            'error' => $error
        ]);
    }

    // Form thêm mới Loại hàng
    public function createProductType() {
        // Cần lấy danh sách Danh mục để chọn trong dropdown
        $categories = $this->categoryModel->getAllCategories();

        $this->renderView('admin/product-types/form', [
            'title' => 'Thêm loại hàng mới',
            'user' => Auth::user(),
            'isEdit' => false,
            'categories' => $categories,
            'productType' => []
        ]);
    }

    // Form sửa Loại hàng
    public function editProductType($id) {
        $productType = $this->categoryModel->findProductTypeById($id);
        $categories = $this->categoryModel->getAllCategories();

        if (!$productType) {
            $_SESSION['error'] = "Không tìm thấy loại hàng!";
            $this->redirect('/admin/product-types');
        }

        $this->renderView('admin/product-types/form', [
            'title' => 'Cập nhật loại hàng',
            'user' => Auth::user(),
            'isEdit' => true,
            'categories' => $categories,
            'productType' => $productType
        ]);
    }

    // Xử lý Lưu (Chung cho Thêm và Sửa)
    public function storeProductType() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? '';
            $ten_lhh = trim($_POST['ten_lhh']);
            $id_dm = $_POST['id_dm'];

            if (empty($ten_lhh) || empty($id_dm)) {
                $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin!";
                $this->redirect($_SERVER['HTTP_REFERER']);
                return;
            }

            if ($id) {
                // Cập nhật
                $result = $this->categoryModel->updateProductType($id, $ten_lhh, $id_dm);
                $msg = "Cập nhật";
            } else {
                // Thêm mới
                $result = $this->categoryModel->createProductType($ten_lhh, $id_dm);
                $msg = "Thêm mới";
            }

            if ($result) {
                $_SESSION['success'] = "$msg loại hàng thành công!";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra!";
            }

            $this->redirect('/admin/product-types');
        }
    }

    // Xóa Loại hàng
    public function deleteProductType($id) {
        $result = $this->categoryModel->deleteProductType($id);
        if ($result) {
            $_SESSION['success'] = "Đã xóa loại hàng thành công!";
        } else {
            $_SESSION['error'] = "Không thể xóa! Loại hàng này đang chứa sản phẩm.";
        }
        $this->redirect('/admin/product-types');
    }

    // ======================================================
    // QUẢN LÝ KHUYẾN MÃI (PROMOTIONS)
    // ======================================================

    public function promotions() {
        $search = $_GET['search'] ?? '';
        $promotions = $this->promotionModel->getAllPromotions($search);
        
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $today = date('Y-m-d H:i:s');
        foreach ($promotions as $km) {
            $status = $km['trang_thai_km'];
            // Nếu đã hủy thì bỏ qua, chỉ cập nhật cái đang chạy
            if ($status !== 'Đã hủy') {
                $newStatus = $status;
                if ($today < $km['ngay_bd_km']) $newStatus = 'Sắp diễn ra';
                elseif ($today >= $km['ngay_bd_km'] && $today <= $km['ngay_kt_km']) $newStatus = 'Đang diễn ra';
                elseif ($today > $km['ngay_kt_km']) $newStatus = 'Đã kết thúc';
                
                if ($newStatus !== $status) {
                    $km['trang_thai_km'] = $newStatus;
                    // Nếu muốn lưu vào DB luôn thì gọi hàm updateStatus ở Model
                }
            }
        }

        $this->renderView('admin/promotions/index', [
            'title' => 'Quản lý khuyến mãi',
            'user' => Auth::user(),
            'promotions' => $promotions,
            'searchKeyword' => $search,
            'success' => $_SESSION['success'] ?? null,
            'error' => $_SESSION['error'] ?? null
        ]);
        unset($_SESSION['success'], $_SESSION['error']);
    }
    public function createPromotion() { 
        // 1. Lấy danh sách loại hàng để hiển thị dropdown (cho tính năng áp dụng hàng loạt)
        $loai_hang = $this->productModel->getAllLoaiHang(); 

        // 2. Gọi View
        $this->renderView('admin/promotions/form', [
            'title' => 'Thêm khuyến mãi',
            'user' => Auth::user(),
            'isEdit' => false,
            'promotion' => [],
            'loai_hang' => $loai_hang
        ]);
    }

    public function editPromotion($id) {
        $promotion = $this->promotionModel->getPromotionById($id);
        if (!$promotion) {
            $_SESSION['error'] = "Không tìm thấy khuyến mãi!";
            $this->redirect('/admin/promotions');
        }

        // 1. Lấy danh sách loại hàng
        $loai_hang = $this->productModel->getAllLoaiHang();

        // 2. Truyền sang View
        $this->renderView('admin/promotions/form', [
            'title' => 'Cập nhật khuyến mãi',
            'user' => Auth::user(),
            'isEdit' => true,
            'promotion' => $promotion,
            'loai_hang' => $loai_hang // <--- QUAN TRỌNG: Bổ sung dòng này
        ]);
    }

    public function storePromotion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? '';
            
            // Xử lý ngày tháng: Thay thế 'T' thành khoảng trắng để đúng chuẩn MySQL
            $start = str_replace('T', ' ', $_POST['ngay_bd']);
            $end = str_replace('T', ' ', $_POST['ngay_kt']);
            
            date_default_timezone_set('Asia/Ho_Chi_Minh');
            $today = date('Y-m-d H:i:s');
            
            // Tính trạng thái
            $status = 'Sắp diễn ra';
            if ($today >= $start && $today <= $end) $status = 'Đang diễn ra';
            if ($today > $end) $status = 'Đã kết thúc';
            
            if (isset($_POST['trang_thai']) && $_POST['trang_thai'] === 'Đã hủy') {
                $status = 'Đã hủy';
            }

            $data = [
                'ten_km' => $_POST['ten_km'],
                'phan_tram_km' => $_POST['phan_tram_km'],
                'ngay_bd' => $start,
                'ngay_kt' => $end,
                'trang_thai' => $status
            ];

            // --- QUAN TRỌNG: LOGIC LƯU VÀ LẤY ID ---
            if ($id) {
                // Cập nhật
                $result = $this->promotionModel->updatePromotion($id, $data);
                if ($result) $_SESSION['success'] = "Cập nhật khuyến mãi thành công!";
            } else {
                // Thêm mới -> Nhận về ID vừa tạo (ví dụ: KM001)
                $newId = $this->promotionModel->createPromotion($data);
                if ($newId) {
                    $id = $newId; // Gán ID mới để dùng cho việc update hàng loạt bên dưới
                    $_SESSION['success'] = "Thêm mới khuyến mãi thành công (Mã: $id)!";
                } else {
                    $_SESSION['error'] = "Lỗi khi tạo khuyến mãi!";
                    $this->redirect('/admin/promotions/create');
                    return;
                }
            }

            // --- XỬ LÝ ÁP DỤNG CHO LOẠI HÀNG (Nếu có chọn) ---
            if (isset($_POST['scope']) && $_POST['scope'] === 'category') {
                $idLhh = $_POST['id_lhh'] ?? '';
                if (!empty($idLhh) && !empty($id)) {
                    // Gọi hàm trong ProductModel để update hàng loạt
                    $this->promotionModel->applyPromotionToCategory($id, $idLhh);
                    $_SESSION['success'] .= " Đã áp dụng cho toàn bộ nhóm hàng đã chọn.";
                }
            }

            $this->redirect('/admin/promotions');
        }
    }

    public function deletePromotion($id) {
        $result = $this->promotionModel->deletePromotion($id);
        if ($result) {
            $_SESSION['success'] = "Đã xóa khuyến mãi thành công!";
        } else {
            $_SESSION['error'] = "Không thể xóa! Có thể đang có sản phẩm áp dụng mã này.";
        }
        $this->redirect('/admin/promotions');
    }

    // ======================================================
    // QUẢN LÝ TỒN KHO (INVENTORY / IMPORT SLIPS)
    // ======================================================
    public function inventories() {
        $products = $this->inventoryModel->getAllProducts();

        $this->renderView('admin/inventories/index', [
            'title' => 'Quản lý tồn kho',
            'user' => Auth::user(),
            'products' => $products,
            'success' => $_SESSION['success'] ?? null,
            'error' => $_SESSION['error'] ?? null
        ]);
        unset($_SESSION['success'], $_SESSION['error']);
    }

    public function createInventory() {
        $id_hh = $_GET['id_hh'] ?? '';
        if (empty($id_hh)) {
            $_SESSION['error'] = "Thiếu mã sản phẩm!";
            $this->redirect('/admin/inventories');
            return;
        }

        $product = $this->productModel->getProductByIdForAdmin($id_hh);
        if (!$product) {
            $_SESSION['error'] = "Không tìm thấy sản phẩm!";
            $this->redirect('/admin/inventories');
            return;
        }

        $lots = $this->inventoryModel->getLotsByProduct($id_hh);
        $suppliers = $this->inventoryModel->getAllSuppliers();
        $promotions = $this->inventoryModel->getActivePromotions();

        $this->renderView('admin/inventories/form', [
            'title' => 'Nhập lô mới: ' . $product['ten_hh'],
            'user' => Auth::user(),
            'product' => $product,
            'lots' => $lots,
            'suppliers' => $suppliers,
            'promotions' => $promotions,
            'success' => $_SESSION['success'] ?? null,
            'error' => $_SESSION['error'] ?? null
        ]);
        unset($_SESSION['success'], $_SESSION['error']);
    }

    public function storeInventory() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/inventories');
            return;
        }

        $id_hh = $_POST['id_hh'] ?? '';
        $data = [
            'id_ncc'   => $_POST['id_ncc'],
            'id_km'    => $_POST['id_km'] ?? '',
            'hsd_lo'   => $_POST['hsd_lo'],
            'so_luong' => (int)$_POST['so_luong'],
            'don_gia'  => (float)$_POST['don_gia'],
            'gia_ban'  => (float)$_POST['gia_ban'],
        ];

        $result = $this->inventoryModel->createLot($id_hh, $data);

        if ($result) {
            $_SESSION['success'] = "Nhập lô hàng mới thành công!";
        } else {
            $_SESSION['error'] = "Lỗi khi nhập lô hàng!";
        }
        $this->redirect('/admin/inventories/create?id_hh=' . $id_hh);
    }

    public function inventoryDetail($id) {
        $lots = $this->inventoryModel->getLotsByProduct($id);
        $product = $this->productModel->getProductByIdForAdmin($id);

        $this->renderView('admin/inventories/detail', [
            'title' => 'Chi tiết lô hàng: ' . ($product['ten_hh'] ?? $id),
            'user' => Auth::user(),
            'product' => $product,
            'lots' => $lots
        ]);
    }

    // ======================================================
    // QUẢN LÝ NHÀ CUNG CẤP (SUPPLIERS)
    // ======================================================

    public function suppliers() {
        $search = $_GET['search'] ?? '';
        $suppliers = $this->supplierModel->getAllSuppliers($search);
        
        $this->renderView('admin/suppliers/index', [
            'title' => 'Quản lý Nhà cung cấp',
            'user' => Auth::user(),
            'suppliers' => $suppliers,
            'searchKeyword' => $search,
            'success' => $_SESSION['success'] ?? null,
            'error' => $_SESSION['error'] ?? null
        ]);
        unset($_SESSION['success'], $_SESSION['error']);
    }

    public function createSupplier() {
        $this->renderView('admin/suppliers/form', [
            'title' => 'Thêm Nhà cung cấp',
            'user' => Auth::user(),
            'isEdit' => false,
            'supplier' => []
        ]);
    }

    public function editSupplier($id) {
        $supplier = $this->supplierModel->getSupplierById($id);
        if (!$supplier) {
            $_SESSION['error'] = "Không tìm thấy nhà cung cấp!";
            $this->redirect('/admin/suppliers');
        }
        $this->renderView('admin/suppliers/form', [
            'title' => 'Cập nhật Nhà cung cấp',
            'user' => Auth::user(),
            'isEdit' => true,
            'supplier' => $supplier
        ]);
    }

    public function storeSupplier() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? '';
            
            $data = [
                'ten_ncc' => $_POST['ten_ncc'],
                'dia_chi' => $_POST['dia_chi'],
                'sdt' => $_POST['sdt'],
                'email' => $_POST['email']
            ];

            if ($id) {
                $result = $this->supplierModel->updateSupplier($id, $data);
                $msg = "Cập nhật";
            } else {
                $result = $this->supplierModel->createSupplier($data);
                $msg = "Thêm mới";
            }

            if ($result) {
                $_SESSION['success'] = "$msg nhà cung cấp thành công!";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra!";
            }
            $this->redirect('/admin/suppliers');
        }
    }

    public function deleteSupplier($id) {
        $result = $this->supplierModel->deleteSupplier($id);
        if ($result) {
            $_SESSION['success'] = "Đã xóa nhà cung cấp thành công!";
        } else {
            $_SESSION['error'] = "Không thể xóa! Nhà cung cấp này đã có lịch sử nhập hàng.";
        }
        $this->redirect('/admin/suppliers');
    }

    // ======================================================
    // QUẢN LÝ ĐƠN HÀNG (ORDERS)
    // ======================================================

    public function orders() {
        // 1. Cấu hình phân trang
        $ordersPerPage = 20;
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($currentPage - 1) * $ordersPerPage;

        // 2. Lấy từ khóa và trạng thái
        $searchKeyword = $_GET['search'] ?? '';
        $statusFilter = $_GET['status'] ?? '';

        // 3. Gọi Model lấy dữ liệu và Tổng số dòng để phân trang
        $totalOrders = $this->orderModel->countAllOrders($searchKeyword, $statusFilter);
        $totalPages = ceil($totalOrders / $ordersPerPage);
        
        // Truyền tham số limit và offset vào hàm getAllOrders
        $orders = $this->orderModel->getAllOrders($searchKeyword, $statusFilter, $ordersPerPage, $offset);

        // 4. Truyền dữ liệu ra View
        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        $this->renderView('admin/orders/index', [
            'title' => 'Quản lý Đơn hàng',
            'user' => Auth::user(),
            'orders' => $orders,
            'searchKeyword' => $searchKeyword,
            'currentStatus' => $statusFilter,
            'totalPages' => $totalPages,    // Thêm biến này cho view
            'currentPage' => $currentPage,  // Thêm biến này cho view
            'success' => $success,
            'error' => $error
        ]);
    }

    // Xử lý cập nhật trạng thái (POST)
    public function updateOrderStatus() {
        $id = $_POST['id_dh'] ?? '';
        $tenTrangThai = $_POST['trang_thai'] ?? '';

        // Map tên → mã
        $map = [
            'Chờ xử lý'           => 'TTD01',
            'Đã xác nhận'         => 'TTD02',
            'Đang giao hàng'      => 'TTD03',
            'Giao thành công'     => 'TTD04',
            'Đã hủy'              => 'TTD05',
        ];

        $idTrangThai = $map[$tenTrangThai] ?? 'TTD01';

        $result = $this->orderModel->updateOrderStatus($id, $idTrangThai);

        if ($result) {
            $_SESSION['success'] = "Cập nhật trạng thái thành công!";
        } else {
            $_SESSION['error'] = "Lỗi cập nhật trạng thái!";
        }
        $this->redirect('/admin/order-detail/' . $id);
    }

    /**
     * Hiển thị chi tiết đơn hàng
     * Sửa: Nhận $id làm tham số (từ router) thay vì $_GET
     */
    public function orderDetail($id = null) {
        // Fallback: Nếu router không truyền tham số, thử lấy từ $_GET
        if (!$id) {
            $id = $_GET['id'] ?? null;
        }

        if (!$id) {
            $_SESSION['error'] = 'Không tìm thấy mã đơn hàng!';
            $this->redirect('/admin/orders'); // Dùng hàm redirect chuẩn của BaseController
            return;
        }

        // Lấy thông tin đơn hàng
        $order = $this->orderModel->getOrderById($id);
        
        if (!$order) {
            $_SESSION['error'] = 'Đơn hàng không tồn tại!';
            $this->redirect('/admin/orders');
            return;
        }

        // Lấy chi tiết sản phẩm
        $items = $this->orderModel->getOrderItems($id);
        
        // Hiển thị view
        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        $this->renderView('admin/orders/detail', [
            'user' => Auth::user(), // Thêm user để hiển thị sidebar đúng
            'order' => $order,
            'items' => $items,
            'title' => 'Chi tiết đơn hàng ' . $id,
            'success' => $success,
            'error' => $error
        ]);
    }

    /**
     * Xử lý cập nhật trạng thái (POST)
     * Tên hàm khớp với route trong view detail: admin/order-update-status
     */
    public function orderUpdateStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy dữ liệu
            $id = $_POST['id_dh'] ?? '';
            $status = $_POST['trang_thai'] ?? '';

            if (empty($id) || empty($status)) {
                $_SESSION['error'] = 'Dữ liệu không hợp lệ!';
                $this->redirect('/admin/orders');
                return;
            }

            // Gọi Model cập nhật (Dùng $this->orderModel đã khởi tạo ở __construct)
            if ($this->orderModel->updateOrderStatus($id, $status)) {
                $_SESSION['success'] = "Đã cập nhật đơn hàng #$id sang trạng thái: $status";
            } else {
                $_SESSION['error'] = "Lỗi cập nhật trạng thái!";
            }

            // Quay lại trang chi tiết đúng
            $this->redirect("/admin/order-detail/$id");
        } else {
            // Nếu không phải POST thì đá về danh sách
            $this->redirect('/admin/orders');
        }
    }

    // NGƯỜI DÙNG ADMIN
    /**
     * Hiển thị form sửa khách hàng
     */
    public function editUser($id) {
        // 1. Lấy thông tin tài khoản
        $customer = $this->userModel->getUserById($id);
        
        if (!$customer) {
            $_SESSION['error'] = "Không tìm thấy khách hàng!";
            $this->redirect('/admin/users');
        }

        // 2. Lấy danh sách địa chỉ (MỚI THÊM)
        $addresses = $this->userModel->getUserAddresses($id);

        // 3. Truyền cả 2 biến sang View
        $this->renderView('admin/users/form', [
            'title' => 'Chi tiết khách hàng',
            'user' => Auth::user(),
            'customer' => $customer,
            'addresses' => $addresses
        ]);
    }

    /**
     * Xử lý cập nhật thông tin (POST)
     */
    public function updateUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $password = $_POST['password'];
            
            $data = [
                'ho_ten' => $_POST['ho_ten'],
                'email' => $_POST['email'],
                'sdt' => $_POST['sdt'],
                'gioi_tinh' => $_POST['gioi_tinh'],
                'mat_khau' => !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null
            ];

            // Gọi Model cập nhật
            if ($this->userModel->adminUpdateCustomer($id, $data)) {
                $_SESSION['success'] = "Cập nhật khách hàng thành công!";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra!";
            }
            $this->redirect('/admin/users');
        }
    }

    /**
     * Xử lý xóa khách hàng
     */
    public function deleteUser($id) {
        // Gọi Model xóa
        $result = $this->userModel->deleteUser($id);
        
        if ($result === true) {
            $_SESSION['success'] = "Đã xóa khách hàng thành công!";
        } else {
            $_SESSION['error'] = "Bạn không thể xóa khách hàng này!" . $result;
        }
        $this->redirect('/admin/users');
    }

    // ======================================================
    // BÁO CÁO THỐNG KÊ ADMIN
    // ======================================================
    public function statistics() {
        $statType = $_POST['stat_type'] ?? '';
        $dateStart = $_POST['date_start'] ?? date('Y-m-01');
        $dateEnd = $_POST['date_end'] ?? date('Y-m-d');
        
        // 1. Nhận giới hạn từ Form (Mặc định là 5 nếu không chọn)
        $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 5;
        
        $data = ['labels' => [], 'values' => []];
        $title = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $statType) {
            switch ($statType) {
                case 'best-selling':
                    $title = 'Sản phẩm bán chạy'; // Đã bỏ chữ Top 5
                    $results = $this->statisticModel->getBestSellingProducts($dateStart, $dateEnd, $limit);
                    break;
                case 'revenue':
                    $title = 'Tổng doanh thu';
                    $results = $this->statisticModel->getRevenue($dateStart, $dateEnd, $limit);
                    break;
                case 'orders':
                    $title = 'Tổng số đơn hàng';
                    $results = $this->statisticModel->getOrdersCount($dateStart, $dateEnd, $limit);
                    break;
                case 'cancelled-orders':
                    $title = 'Đơn hàng đã hủy';
                    $results = $this->statisticModel->getCancelledOrders($dateStart, $dateEnd, $limit);
                    break;
                case 'top-customers':
                    $title = 'Khách hàng tiêu biểu';
                    $results = $this->statisticModel->getTopCustomers($dateStart, $dateEnd, $limit);
                    break;
                default:
                    $results = [];
            }

            foreach ($results as $row) {
                $data['labels'][] = $row['label'];
                $data['values'][] = (float)$row['value'];
            }
        }

        $this->renderView('admin/statistics/index', [
            'title' => 'Báo cáo thống kê',
            'user' => Auth::user(),
            'statType' => $statType,
            'dateStart' => $dateStart,
            'dateEnd' => $dateEnd,
            'limit' => $limit, // Truyền biến limit sang View
            'chartTitle' => $title,
            'chartData' => $data
        ]);
    }

    // ======================================================
    // CÀI ĐẶT HỆ THỐNG (SETTINGS) ADMIN
    // ======================================================

    public function settings() {
        $settings = $this->settingModel->getAllSettings();

        $this->renderView('admin/settings/index', [
            'title' => 'Cài đặt hệ thống',
            'user' => Auth::user(),
            'settings' => $settings,
            'success' => $_SESSION['success'] ?? null,
            'error' => $_SESSION['error'] ?? null
        ]);
        unset($_SESSION['success'], $_SESSION['error']);
    }

    public function updateSettings() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Cập nhật các thông tin văn bản
            $keys = ['site_title', 'site_email', 'site_phone', 'site_address'];
            
            foreach ($keys as $key) {
                if (isset($_POST[$key])) {
                    $this->settingModel->updateSetting($key, $_POST[$key]);
                }
            }

            // 2. Xử lý upload Logo (Nếu có chọn ảnh mới)
            if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] == 0) {
                $targetDir = __DIR__ . '/../../public/admin_assets/assets/img/';
                $fileName = 'system_logo.png'; // Đặt tên cố định để ghi đè
                $targetFile = $targetDir . $fileName;

                if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $targetFile)) {
                    $this->settingModel->updateSetting('site_logo', $fileName);
                }
            }

            $_SESSION['success'] = "Đã lưu cài đặt thành công!";
            $this->redirect('/admin/settings');
        }
    }

    // ======================================================
    // HỒ SƠ CÁ NHÂN (PROFILE) - ĐÃ CẬP NHẬT
    // ======================================================

    public function profile() {
        $id = $_SESSION['user']['id_tk'];
        
        // 1. Lấy thông tin User
        $user = $this->userModel->getUserById($id);
        
        // 2. Lấy TOÀN BỘ danh sách địa chỉ
        $addresses = $this->userModel->getUserAddresses($id);

        // 3. Lọc ra địa chỉ mặc định để điền vào ô nhập nhanh (giữ tính năng cũ)
        $defaultAddress = [];
        foreach ($addresses as $addr) {
            if ($addr['mac_dinh'] == 1) {
                $defaultAddress = $addr;
                break;
            }
        }

        $this->renderView('admin/profile/index', [
            'title' => 'Hồ sơ cá nhân',
            'user' => Auth::user(),
            'profile' => $user,
            'address' => $defaultAddress,
            'addresses' => $addresses,
            'success' => $_SESSION['success'] ?? null,
            'error' => $_SESSION['error'] ?? null
        ]);
        unset($_SESSION['success'], $_SESSION['error']);
    }

    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_SESSION['user']['id_tk'];
            $password = $_POST['password'];
            $dia_chi = $_POST['dia_chi'];
            
            $data = [
                'ho_ten' => $_POST['ho_ten'],
                'email' => $_POST['email'],
                'sdt' => $_POST['sdt'],
                'gioi_tinh' => $_POST['gioi_tinh'],
                'mat_khau' => !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null
            ];

            // 1. Cập nhật thông tin tài khoản chính
            $updateUser = $this->userModel->adminUpdateCustomer($id, $data);

            // 2. Cập nhật địa chỉ (Nếu người dùng có nhập)
            $updateAddr = true;
            if (!empty($dia_chi)) {
                $updateAddr = $this->userModel->updateUserAddress($id, $data['ho_ten'], $data['sdt'], $dia_chi);
            }

            if ($updateUser && $updateAddr) {
                $_SESSION['user']['ho_ten'] = $data['ho_ten']; // Cập nhật session
                $_SESSION['success'] = "Cập nhật hồ sơ và địa chỉ thành công!";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi cập nhật!";
            }
            $this->redirect('/admin/profile');
        }
    }

    
}