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
        
        // Model->getAllProducts() lúc này phải trả về SUM(so_luong_con_lai) và MIN(hsd_lo)
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
                $id = $this->productModel->generateNewId(); 
            }

            // 2. Xử lý Ảnh
            $link_anh = $_POST['old_img'] ?? ''; 
            if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
                $targetDir = __DIR__ . '/../../public/uploads/';
                $fileName = $id . '.png';
                $targetFile = $targetDir . $fileName;
                if (move_uploaded_file($_FILES['img']['tmp_name'], $targetFile)) {
                    $link_anh = $fileName;
                }
            }

            // 3. Gom dữ liệu bảng HÀNG HÓA
            // Đã loại bỏ hoàn toàn việc xử lý $gia_ban tại đây
            $data = [
                'id_hh'               => $id,
                'ten_hh'              => $_POST['ten_hh'],
                'id_loai2'            => $_POST['id_lhh'],
                'id_dvt'              => $_POST['id_dvt'],
                'mo_ta_hh'            => $_POST['mo_ta_hh'],
                'duoc_phep_ban'       => isset($_POST['duoc_phep_ban']) ? 1 : 0,
                'link_anh'            => $link_anh,
                'la_hang_sx'          => 1, // Mặc định là hàng sản xuất/chế biến
                'phan_tram_loi_nhuan' => $_POST['phan_tram_loi_nhuan'] ?? 30
            ];

            // 4. GỌI MODEL XỬ LÝ
            if ($isUpdate) {
                $this->productModel->updateProduct($id, $data);
                $msg = "Cập nhật sản phẩm thành công!";
                $redirectUrl = '/admin/products'; // Cập nhật thì quay về danh sách
            } else {
                $this->productModel->createProduct($data);
                $msg = "Thêm sản phẩm thành công! Vui lòng nhập lô hàng đầu tiên để xác định giá bán.";
                // Thêm mới thì chuyển đến trang nhập lô
                $redirectUrl = '/admin/inventories/create?id_hh=' . $id; 
            }

            $_SESSION['success'] = $msg;
            $base = defined('BASE_PATH') ? BASE_PATH : '/NLN_NLCS/public';
            header('Location: ' . $base . $redirectUrl);
            exit;
        }
    }

    /**
     * API trả về danh sách các lô hàng còn tồn của một sản phẩm
     * Phục vụ cho việc hiển thị Modal chi tiết lô hàng
     */
    public function getBatches() {
        if (ob_get_length()) ob_clean(); 

        $id_hh = $_GET['id_hh'] ?? '';
        
        try {
            $batches = $this->productModel->getBatchesByProductId($id_hh);
            
            foreach ($batches as &$b) {
                $b['hsd_lo'] = date('d/m/Y', strtotime($b['hsd_lo']));
                
                // Cột này hiện tại trong SQL Model đã là 'ngay_lap_phieu_nhap'
                $b['ngay_nhap_hien_thi'] = !empty($b['ngay_lap_phieu_nhap']) 
                    ? date('d/m/Y', strtotime($b['ngay_lap_phieu_nhap'])) 
                    : 'N/A';
                
                $b['color'] = 'success'; 
                if ($b['id_trang_thai_lo'] == 'TTL02') $b['color'] = 'warning';
                if ($b['id_trang_thai_lo'] == 'TTL03') $b['color'] = 'danger';
                if ($b['id_trang_thai_lo'] == 'TTL04') $b['color'] = 'secondary';
                
                // Thêm dòng này để JS không bị lỗi nếu bạn dùng 'ten_trang_thai' ở giao diện
                $b['ten_trang_thai'] = $b['ten_trang_thai_lo'] ?? ''; 
            }

            header('Content-Type: application/json');
            echo json_encode($batches);
        } catch (\Exception $e) {
            header('Content-Type: application/json', true, 500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
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

/**
     * Hiển thị danh sách khuyến mãi và cập nhật trạng thái tự động
     */
    public function promotions() {
        $search = $_GET['search'] ?? '';
        // Lấy danh sách từ Model
        $promotions = $this->promotionModel->getAllPromotions($search);
        
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $today = date('Y-m-d H:i:s');

        foreach ($promotions as &$km) {
            $status = $km['trang_thai_km'];
            
            // Chỉ cập nhật trạng thái tự động nếu chương trình chưa bị "Đã hủy" thủ công
            if ($status !== 'Đã hủy') {
                $newStatus = $status;
                if ($today < $km['ngay_bd_km']) {
                    $newStatus = 'Sắp diễn ra';
                } elseif ($today >= $km['ngay_bd_km'] && $today <= $km['ngay_kt_km']) {
                    $newStatus = 'Đang diễn ra';
                } elseif ($today > $km['ngay_kt_km']) {
                    $newStatus = 'Đã kết thúc';
                }
                
                // Nếu trạng thái thời gian thực khác với DB thì cập nhật lại
                if ($newStatus !== $status) {
                    $km['trang_thai_km'] = $newStatus;
                    $this->promotionModel->updateStatus($km['id_km'], $newStatus);
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

    /**
     * Giao diện thêm mới khuyến mãi
     */
    public function createPromotion() { 
        // Lấy danh sách sản phẩm để người dùng chọn áp dụng khuyến mãi cho lô hàng
        $loai_hang = $this->productModel->getAllProducts(); 

        $this->renderView('admin/promotions/form', [
            'title' => 'Thêm khuyến mãi',
            'user' => Auth::user(),
            'isEdit' => false,
            'promotion' => [],
            'loai_hang' => $loai_hang
        ]);
    }

    /**
     * Giao diện chỉnh sửa khuyến mãi
     */
    public function editPromotion($id) {
        $promotion = $this->promotionModel->getPromotionById($id);
        if (!$promotion) {
            $_SESSION['error'] = "Không tìm thấy chương trình khuyến mãi!";
            $this->redirect('/admin/promotions');
            return;
        }

        $loai_hang = $this->productModel->getAllProducts();

        $this->renderView('admin/promotions/form', [
            'title' => 'Cập nhật khuyến mãi',
            'user' => Auth::user(),
            'isEdit' => true,
            'promotion' => $promotion,
            'loai_hang' => $loai_hang
        ]);
    }

    /**
     * Xử lý Lưu (Thêm mới hoặc Cập nhật)
     */
    public function storePromotion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? '';
            
            // Xử lý định dạng thời gian từ input datetime-local
            $start = str_replace('T', ' ', $_POST['ngay_bd']);
            $end = str_replace('T', ' ', $_POST['ngay_kt']);
            
            date_default_timezone_set('Asia/Ho_Chi_Minh');
            $today = date('Y-m-d H:i:s');
            
            // Logic tính trạng thái dựa trên thời gian
            $status = 'Sắp diễn ra';
            if ($today >= $start && $today <= $end) $status = 'Đang diễn ra';
            if ($today > $end) $status = 'Đã kết thúc';
            
            // Ưu tiên trạng thái hủy nếu người dùng tích chọn
            if (isset($_POST['trang_thai']) && $_POST['trang_thai'] === 'Đã hủy') {
                $status = 'Đã hủy';
            }

            $data = [
                'ten_km' => $_POST['ten_km'],
                'phan_tram_km' => $_POST['phan_tram_km'],
                'ngay_bd_km' => $start,
                'ngay_kt_km' => $end,
                'trang_thai_km' => $status
            ];

            if ($id) {
                // Thực hiện cập nhật
                $result = $this->promotionModel->updatePromotion($id, $data);
                if ($result) $_SESSION['success'] = "Cập nhật khuyến mãi thành công!";
            } else {
                // Thực hiện thêm mới (ID sẽ được tự sinh KMxxx trong Model)
                $newId = $this->promotionModel->createPromotion($data);
                if ($newId) {
                    $id = $newId;
                    $_SESSION['success'] = "Thêm mới khuyến mãi thành công!";
                } else {
                    $_SESSION['error'] = "Lỗi hệ thống khi tạo khuyến mãi!";
                    $this->redirect('/admin/promotions/create');
                    return;
                }
            }

            // --- CHÈN LOGIC XỬ LÝ PHẠM VI ÁP DỤNG TẠI ĐÂY ---
            if (isset($_POST['scope'])) {
                // Trường hợp 1: Gán thủ công cho các lô hàng đã tích chọn qua AJAX search
                if ($_POST['scope'] === 'manual' && !empty($_POST['selected_batches'])) {
                    foreach ($_POST['selected_batches'] as $idLo) {
                        $this->promotionModel->applyToSpecificBatch($id, $idLo);
                    }
                    $_SESSION['success'] .= " Đã gán cho các lô hàng được chọn.";
                } 
                // Trường hợp 2: Tự động gán cho lô hàng cận date của sản phẩm được chọn
                elseif ($_POST['scope'] === 'category') {
                    $idHh = $_POST['id_lhh'] ?? '';
                    if (!empty($idHh) && !empty($id)) {
                        $applied = $this->promotionModel->applyPromotionToCategory($id, $idHh);
                        if ($applied) {
                            $_SESSION['success'] .= " Đã gán mã giảm giá vào lô hàng có HSD gần nhất.";
                        } else {
                            $_SESSION['error'] = "Khuyến mãi đã tạo nhưng không tìm thấy lô hàng khả dụng để áp dụng tự động.";
                        }
                    }
                }
            }

            $this->redirect('/admin/promotions');
        }
    }

    /**
     * Xóa khuyến mãi
     */
    public function deletePromotion($id) {
        // Model đã xử lý việc gỡ mã KM khỏi lô hàng trước khi xóa
        $result = $this->promotionModel->deletePromotion($id);
        if ($result) {
            $_SESSION['success'] = "Đã xóa chương trình khuyến mãi thành công!";
        } else {
            $_SESSION['error'] = "Lỗi: Không thể xóa chương trình khuyến mãi này!";
        }
        $this->redirect('/admin/promotions');
    }

    // ======================================================
    // QUẢN LÝ TỒN KHO và LÔ (INVENTORY / IMPORT SLIPS)
    // ======================================================

    // Hàm phục vụ AJAX tìm kiếm lô hàng
    public function searchBatches() {
        $query   = $_GET['query'] ?? '';
        $batches = $this->promotionModel->getAvailableBatchesByKeyword($query);

        if (empty($batches)) {
            echo '<div class="p-2 text-muted">Không tìm thấy lô hàng nào phù hợp.</div>';
            return;
        }

        echo '<table class="table table-sm mb-0">
                <thead class="table-light">
                    <tr><th>Chọn</th><th>Mã Lô</th><th>Sản phẩm</th><th>HSD</th><th>Tồn</th></tr>
                </thead>
                <tbody>';
        foreach ($batches as $b) {
            echo "<tr>
                    <td><input type='checkbox' name='selected_batches[]' value='{$b['id_lo']}'></td>
                    <td>{$b['id_lo']}</td>
                    <td>{$b['ten_hh']}</td>
                    <td>" . date('d/m/Y', strtotime($b['hsd_lo'])) . "</td>
                    <td>{$b['so_luong_con_lai']}</td>
                </tr>";
        }
        echo '</tbody></table>';
    }

    public function inventories() {
        $products = $this->inventoryModel->getAllProducts();

        $this->renderView('admin/inventories/index', [
            'title'    => 'Quản lý tồn kho',
            'user'     => Auth::user(),
            'products' => $products,
            'success'  => $_SESSION['success'] ?? null,
            'error'    => $_SESSION['error'] ?? null
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

        $lots       = $this->inventoryModel->getLotsByProduct($id_hh);
        $suppliers  = $this->inventoryModel->getAllSuppliers();
        $promotions = $this->inventoryModel->getActivePromotions();

        $this->renderView('admin/inventories/form', [
            'title'      => 'Nhập lô mới: ' . $product['ten_hh'],
            'user'       => Auth::user(),
            'product'    => $product,
            'lots'       => $lots,
            'suppliers'  => $suppliers,
            'promotions' => $promotions,
            'success'    => $_SESSION['success'] ?? null,
            'error'      => $_SESSION['error'] ?? null
        ]);
        unset($_SESSION['success'], $_SESSION['error']);
    }

    public function storeInventory() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/inventories');
            return;
        }

        $id_hh = $_POST['id_hh'] ?? '';
        $data  = [
            'id_ncc'   => $_POST['id_ncc'],
            'id_km'    => $_POST['id_km'] ?? '',
            'hsd_lo'   => $_POST['hsd_lo'],
            'so_luong' => (int)$_POST['so_luong'],
            'don_gia'  => (float)$_POST['don_gia'],
        ];

        $result = $this->inventoryModel->createLot($id_hh, $data);

        if ($result) {
            $_SESSION['success'] = "Nhập lô hàng mới thành công! Giá bán đã được tự động tính toán.";
        } else {
            $_SESSION['error'] = "Lỗi khi nhập lô hàng! Vui lòng kiểm tra lại dữ liệu.";
        }
        $this->redirect('/admin/inventories/create?id_hh=' . $id_hh);
    }

    // Chi tiết sản phẩm + lô
    public function inventoryDetail($id) {
        $lots    = $this->inventoryModel->getLotsByProduct($id);
        $product = $this->productModel->getProductByIdForAdmin($id);

        $this->renderView('admin/inventories/detail', [
            'title'   => 'Chi tiết lô hàng: ' . ($product['ten_hh'] ?? $id),
            'user'    => Auth::user(),
            'product' => $product,
            'lots'    => $lots
        ]);
    }

    // JSON cho modal
    public function getBatchesJson() {
        $id_hh   = $_GET['id_hh'] ?? '';
        $batches = $this->inventoryModel->getBatchesByProductId($id_hh);

        $badgeMap = [
            'TTL01' => 'bg-success',
            'TTL02' => 'bg-warning text-dark',
            'TTL03' => 'bg-secondary',
            'TTL04' => 'bg-danger',
            'TTL05' => 'bg-dark'
        ];

        foreach ($batches as &$b) {
            $b['badge_class'] = $badgeMap[$b['id_trang_thai_lo']] ?? 'bg-light';
            $b['hsd_f']       = date('d/m/Y', strtotime($b['hsd_lo']));
            $b['nhap_f']      = $b['ngay_lap_phieu_nhap']
                                ? date('d/m/Y', strtotime($b['ngay_lap_phieu_nhap']))
                                : '—';
            $b['gia_canh_bao'] = (isset($b['gia_hien_tai']) && (float)$b['gia_hien_tai'] === 0.0);
        }

        header('Content-Type: application/json');
        echo json_encode($batches);
        exit;
    }

    // Cập nhật lô
    public function updateBatch() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/inventories');
            return;
        }

        $id_lo = $_POST['id_lo'] ?? '';
        $data  = [
            'hsd_lo'           => $_POST['hsd_lo'],
            'so_luong_con_lai' => (int)$_POST['so_luong_con_lai'],
            'id_trang_thai_lo' => $_POST['id_trang_thai_lo']
        ];

        if (empty($id_lo)) {
            $_SESSION['error'] = "Không tìm thấy mã lô hàng!";
            $this->redirect('/admin/inventories');
            return;
        }

        $result = $this->inventoryModel->updateLotInfo($id_lo, $data);

        if ($result) {
            $_SESSION['success'] = "Cập nhật lô hàng $id_lo thành công!";
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra khi cập nhật dữ liệu.";
        }

        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/admin/inventories');
    }

    // Xóa lô
    public function deleteBatch() {
        $id_lo = $_GET['id_lo'] ?? '';
        $id_hh = $_GET['id_hh'] ?? '';

        if (empty($id_lo)) {
            $_SESSION['error'] = "Không tìm thấy mã lô!";
            $this->redirect('admin/inventories');
            return;
        }

        if ($this->inventoryModel->deleteLot($id_lo)) {
            $_SESSION['success'] = "Đã xóa lô hàng $id_lo thành công.";
        } else {
            $_SESSION['error'] = "Lỗi: Không thể xóa lô này.";
        }

        if (!empty($id_hh)) {
            $this->redirect('admin/inventories/detail/' . $id_hh);
        } else {
            $this->redirect('admin/inventories');
        }
    }

    // ======================================================
    // QUẢN LÝ NHÀ CUNG CẤP (SUPPLIERS)
    // ======================================================

    /**
     * Danh sách nhà cung cấp: Hỗ trợ lọc theo tên, SĐT và sắp xếp mới nhất
     */
    public function suppliers() {
        // Tiếp nhận từ khóa lọc từ URL
        $search = $_GET['search'] ?? '';
        $phone = $_GET['phone'] ?? '';
        
        // Gọi Model xử lý lọc và sắp xếp (ORDER BY id_ncc DESC)
        $suppliers = $this->supplierModel->getAllSuppliers($search, $phone);
        
        $this->renderView('admin/suppliers/index', [
            'title' => 'Quản lý Nhà cung cấp',
            'user' => Auth::user(),
            'suppliers' => $suppliers,
            'searchKeyword' => $search,
            'phoneKeyword' => $phone, // Truyền thêm biến lọc SĐT sang View
            'success' => $_SESSION['success'] ?? null,
            'error' => $_SESSION['error'] ?? null
        ]);
        unset($_SESSION['success'], $_SESSION['error']);
    }

    /**
     * Form thêm mới
     */
    public function createSupplier() {
        $this->renderView('admin/suppliers/form', [
            'title' => 'Thêm Nhà cung cấp',
            'user' => Auth::user(),
            'isEdit' => false,
            'supplier' => []
        ]);
    }

    /**
     * Form chỉnh sửa
     */
    public function editSupplier($id) {
        $supplier = $this->supplierModel->getSupplierById($id);
        if (!$supplier) {
            $_SESSION['error'] = "Không tìm thấy nhà cung cấp!";
            $this->redirect('/admin/suppliers');
            return;
        }

        $this->renderView('admin/suppliers/form', [
            'title' => 'Cập nhật Nhà cung cấp',
            'user' => Auth::user(),
            'isEdit' => true,
            'supplier' => $supplier
        ]);
    }

    /**
     * Xử lý Lưu (Thêm mới/Cập nhật)
     */
    public function storeSupplier() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? '';
            
            // Chuẩn bị dữ liệu đồng bộ với tên cột trong CSDL
            $data = [
                'ten_ncc' => $_POST['ten_ncc'],
                'dia_chi_ncc' => $_POST['dia_chi'],
                'sdt_ncc' => $_POST['sdt'],
                'email_ncc' => $_POST['email']
            ];

            if (!empty($id)) {
                // Trường hợp Cập nhật
                $result = $this->supplierModel->updateSupplier($id, $data);
                $msg = "Cập nhật";
            } else {
                // Trường hợp Thêm mới
                $result = $this->supplierModel->createSupplier($data);
                $msg = "Thêm mới";
            }

            if ($result) {
                $_SESSION['success'] = "$msg nhà cung cấp thành công!";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra trong quá trình $msg!";
            }
            $this->redirect('/admin/suppliers');
        }
    }

    /**
     * Xử lý xóa
     */
    public function deleteSupplier($id) {
        $result = $this->supplierModel->deleteSupplier($id);
        if ($result) {
            $_SESSION['success'] = "Đã xóa nhà cung cấp thành công!";
        } else {
            // Lỗi thường do ràng buộc khóa ngoại với bảng lo_hang (nhập hàng)
            $_SESSION['error'] = "Không thể xóa! Nhà cung cấp này đang liên kết với các lô hàng trong kho.";
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
        // Lấy dữ liệu từ POST hoặc thiết lập mặc định
        $statType = $_POST['stat_type'] ?? 'best-selling'; // Mặc định hiện sản phẩm bán chạy
        $dateStart = $_POST['date_start'] ?? date('Y-m-01');
        $dateEnd = $_POST['date_end'] ?? date('Y-m-d');
        
        // 1. Nhận giới hạn từ Form (Mặc định là 5 nếu không chọn)
        $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 5;
        
        $data = ['labels' => [], 'values' => []];
        $title = '';
        $results = [];

        // Xử lý Switch Case để gọi đúng hàm trong Model
        switch ($statType) {
            case 'best-selling':
                $title = 'Sản phẩm bán chạy';
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

        // Chuyển đổi dữ liệu trả về từ SQL thành dạng mảng cho Chart.js
        if (!empty($results)) {
            foreach ($results as $row) {
                $data['labels'][] = $row['label'];
                $data['values'][] = (float)$row['value'];
            }
        }

        // Render ra view cùng với các biến cần thiết
        $this->renderView('admin/statistics/index', [
            'title' => 'Báo cáo thống kê',
            'user' => Auth::user(),
            'statType' => $statType,
            'dateStart' => $dateStart,
            'dateEnd' => $dateEnd,
            'limit' => $limit,
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