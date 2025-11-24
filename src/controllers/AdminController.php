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

class AdminController extends BaseController {

    private $orderModel;
    private $productModel;
    private $userModel;
    private $categoryModel;
    private $promotionModel;
    private $inventoryModel;
    private $supplierModel;
    private $statisticModel;

    public function __construct() {
        parent::__construct();
        
        Auth::requireLogin();
        
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
        $this->promotionModel = new PromotionModel();
        $this->inventoryModel = new InventoryModel();
        $this->supplierModel = new SupplierModel();
        $this->statisticModel = new StatisticModel();
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

    // ======================================================
    // QUẢN LÝ NGƯỜI DÙNG (CHỈNH SỬA)
    // ======================================================

    public function users() {
        $search = $_GET['search'] ?? '';
        
        // --- PHẦN 1: KHÁCH HÀNG (Phân trang như cũ) ---
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10; // Giảm xuống 10 để giao diện đỡ dài
        $offset = ($page - 1) * $limit;

        // Gọi hàm mới getUsersByRole với tham số 'KH'
        $customers = $this->userModel->getUsersByRole('KH', $search, $limit, $offset);
        $totalCustomers = $this->userModel->countUsersByRole('KH', $search);
        $totalPages = ceil($totalCustomers / $limit);

        // --- PHẦN 2: QUẢN TRỊ VIÊN (Lấy hết hoặc limit nhiều hơn) ---
        // Gọi hàm mới getUsersByRole với tham số 'AD'
        $admins = $this->userModel->getUsersByRole('AD', $search, 50, 0);

        $this->renderView('admin/users/index', [
            'title' => 'Quản lý Người dùng',
            'user' => Auth::user(),
            'customers' => $customers, // Danh sách khách
            'admins' => $admins,       // Danh sách admin
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
            if ($id == $_SESSION['user']['ID_TK']) {
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

    // Thêm ảnh và mã ảnh là id
    public function storeProduct() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Xác định ID (Mã sản phẩm)
            $id = $_POST['id'] ?? '';
            $isUpdate = !empty($id); // Biến cờ để biết đang sửa hay thêm

            if (!$isUpdate) {
                // Nếu là Thêm mới: Tự sinh ID ngay tại đây để dùng đặt tên ảnh
                $id = $this->productModel->generateProductId();
            }

            // 2. Xử lý Ảnh
            // Mặc định lấy ảnh cũ (nếu đang sửa) hoặc rỗng
            $link_anh = $_POST['old_img'] ?? ''; 

            if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
                $targetDir = __DIR__ . '/../../public/uploads/';
                
                // QUAN TRỌNG: Đặt tên ảnh = Mã sản phẩm + .png
                $fileName = $id . '.png';
                $targetFile = $targetDir . $fileName;
                
                // Di chuyển file upload vào thư mục
                // Lưu ý: Hàm này chỉ đổi tên đuôi file, không convert định dạng ảnh.
                // (Trình duyệt vẫn hiển thị được dù file gốc là jpg nhưng tên là png)
                if (move_uploaded_file($_FILES['img']['tmp_name'], $targetFile)) {
                    $link_anh = $fileName;
                }
            }

            // 3. Gom dữ liệu để lưu
            $data = [
                'id_hh' => $id, // Truyền ID vào (Quan trọng cho hàm create)
                'ten_hh' => $_POST['ten_hh'],
                'id_lhh' => $_POST['id_lhh'],
                'id_dvt' => $_POST['id_dvt'],
                'gia_ban' => $_POST['gia_ban'],
                'so_luong_ton' => $_POST['so_luong_ton'],
                'id_km' => !empty($_POST['id_km']) ? $_POST['id_km'] : null,
                'mo_ta_hh' => $_POST['mo_ta_hh'],
                'hsd' => $_POST['hsd'],
                'duoc_phep_ban' => isset($_POST['duoc_phep_ban']) ? 1 : 0,
                'link_anh' => $link_anh
            ];

            // 4. Gọi Model lưu dữ liệu
            if ($isUpdate) {
                // Cập nhật
                // Lưu ý: Hàm updateProduct trong Model cần hỗ trợ nhận array $data
                $result = $this->productModel->updateProduct($id, $data);
                $msg = "Cập nhật";
            } else {
                // Thêm mới (Cần đảm bảo hàm createProduct trong Model nhận ID từ $data['id_hh'])
                $result = $this->productModel->createProduct($data);
                $msg = "Thêm mới";
            }

            if ($result) {
                $_SESSION['success'] = "$msg sản phẩm thành công! (Mã: $id)";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi $msg sản phẩm.";
            }

            // 5. Quay lại danh sách
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
        
        $today = date('Y-m-d H:i:s');
        foreach ($promotions as $km) {
            $status = $km['TRANG_THAI_KM'];
            // Nếu đã hủy thì bỏ qua, chỉ cập nhật cái đang chạy
            if ($status !== 'Đã hủy') {
                $newStatus = $status;
                if ($today < $km['NGAY_BD_KM']) $newStatus = 'Sắp diễn ra';
                elseif ($today >= $km['NGAY_BD_KM'] && $today <= $km['NGAY_KT_KM']) $newStatus = 'Đang diễn ra';
                elseif ($today > $km['NGAY_KT_KM']) $newStatus = 'Đã kết thúc';
                
                if ($newStatus !== $status) {
                    // Gọi model cập nhật lại status (nếu cần thiết, hoặc chỉ hiển thị)
                    // Ở đây tôi cập nhật vào mảng hiển thị cho đúng thực tế
                    $km['TRANG_THAI_KM'] = $newStatus;
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
        $this->renderView('admin/promotions/form', [
            'title' => 'Thêm khuyến mãi',
            'user' => Auth::user(),
            'isEdit' => false,
            'promotion' => []
        ]);
    }

    public function editPromotion($id) {
        $promotion = $this->promotionModel->getPromotionById($id);
        if (!$promotion) {
            $_SESSION['error'] = "Không tìm thấy khuyến mãi!";
            $this->redirect('/admin/promotions');
        }
        $this->renderView('admin/promotions/form', [
            'title' => 'Cập nhật khuyến mãi',
            'user' => Auth::user(),
            'isEdit' => true,
            'promotion' => $promotion
        ]);
    }

    public function storePromotion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? '';
            
            // Tự động tính trạng thái dựa trên ngày
            $start = $_POST['ngay_bd'];
            $end = $_POST['ngay_kt'];
            $today = date('Y-m-d H:i:s');
            
            $status = 'Sắp diễn ra';
            if ($today >= $start && $today <= $end) $status = 'Đang diễn ra';
            if ($today > $end) $status = 'Đã kết thúc';
            
            // Nếu người dùng chọn "Đã hủy" thủ công thì ưu tiên
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

            if ($id) {
                $result = $this->promotionModel->updatePromotion($id, $data);
                $msg = "Cập nhật";
            } else {
                $result = $this->promotionModel->createPromotion($data);
                $msg = "Thêm mới";
            }

            if ($result) {
                $_SESSION['success'] = "$msg chương trình khuyến mãi thành công!";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra!";
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
        $search = $_GET['search'] ?? '';
        $slips = $this->inventoryModel->getAllImportSlips($search);
        
        $this->renderView('admin/inventories/index', [
            'title' => 'Quản lý phiếu nhập kho',
            'user' => Auth::user(),
            'slips' => $slips,
            'searchKeyword' => $search,
            'success' => $_SESSION['success'] ?? null,
            'error' => $_SESSION['error'] ?? null
        ]);
        unset($_SESSION['success'], $_SESSION['error']);
    }

    public function inventoryDetail($id) {
        $slip = $this->inventoryModel->getImportSlipById($id);
        if (!$slip) {
            $_SESSION['error'] = "Không tìm thấy phiếu nhập!";
            $this->redirect('/admin/inventories');
        }
        $details = $this->inventoryModel->getImportSlipDetails($id);

        $this->renderView('admin/inventories/detail', [
            'title' => 'Chi tiết phiếu nhập ' . $id,
            'user' => Auth::user(),
            'slip' => $slip,
            'details' => $details
        ]);
    }

    public function createInventory() {
        $suppliers = $this->inventoryModel->getAllSuppliers();
        $products = $this->inventoryModel->getAllProducts();

        $this->renderView('admin/inventories/form', [
            'title' => 'Tạo phiếu nhập kho',
            'user' => Auth::user(),
            'suppliers' => $suppliers,
            'products' => $products
        ]);
    }

    public function storeInventory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy dữ liệu chung
            $data = [
                'id_ncc' => $_POST['id_ncc'],
                'ngay_lap' => $_POST['ngay_lap'] ?? date('Y-m-d H:i:s'),
                'chung_tu' => $_POST['chung_tu'] ?? '',
                'tong_tien' => 0
            ];

            // Xử lý chi tiết sản phẩm (Mảng từ form)
            $details = [];
            $product_ids = $_POST['product_id'] ?? [];
            $quantities = $_POST['quantity'] ?? [];
            $prices = $_POST['price'] ?? [];
            $totalAmount = 0;

            for ($i = 0; $i < count($product_ids); $i++) {
                if (!empty($product_ids[$i]) && $quantities[$i] > 0) {
                    $rowTotal = $quantities[$i] * $prices[$i];
                    $totalAmount += $rowTotal;
                    
                    $details[] = [
                        'id_hh' => $product_ids[$i],
                        'so_luong' => $quantities[$i],
                        'don_gia' => $prices[$i]
                    ];
                }
            }
            $data['tong_tien'] = $totalAmount;

            if (empty($details)) {
                $_SESSION['error'] = "Vui lòng chọn ít nhất một sản phẩm để nhập!";
                $this->redirect('/admin/inventories/create');
                return;
            }

            $result = $this->inventoryModel->createImportSlip($data, $details);

            if ($result) {
                $_SESSION['success'] = "Đã nhập kho thành công!";
                $this->redirect('/admin/inventories');
            } else {
                $_SESSION['error'] = "Lỗi khi lưu phiếu nhập!";
                $this->redirect('/admin/inventories/create');
            }
        }
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
        // Lấy danh sách đơn hàng
        $orders = $this->orderModel->getAllOrders();

        $this->renderView('admin/orders/index', [
            'title' => 'Quản lý Đơn hàng',
            'user' => Auth::user(),
            'orders' => $orders,
            'success' => $_SESSION['success'] ?? null,
            'error' => $_SESSION['error'] ?? null
        ]);
        unset($_SESSION['success'], $_SESSION['error']);
    }

    public function orderDetail($id) {
        // Lấy thông tin đơn hàng
        $order = $this->orderModel->getOrderById($id);
        
        if (!$order) {
            $_SESSION['error'] = "Không tìm thấy đơn hàng!";
            $this->redirect('/admin/orders');
        }

        // Lấy danh sách sản phẩm trong đơn
        $items = $this->orderModel->getOrderItems($id);

        $this->renderView('admin/orders/detail', [
            'title' => 'Chi tiết đơn hàng #' . $id,
            'user' => Auth::user(),
            'order' => $order,
            'items' => $items,
            'success' => $_SESSION['success'] ?? null,
            'error' => $_SESSION['error'] ?? null
        ]);
        unset($_SESSION['success'], $_SESSION['error']);
    }

    // Xử lý cập nhật trạng thái (POST)
    public function updateOrderStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id_dh'];
            $status = $_POST['trang_thai'];

            if ($this->orderModel->updateOrderStatus($id, $status)) {
                $_SESSION['success'] = "Đã cập nhật trạng thái đơn hàng #$id thành công!";
            } else {
                $_SESSION['error'] = "Lỗi cập nhật trạng thái!";
            }

            // Quay lại trang chi tiết
            $this->redirect("/admin/order-detail/$id");
        }
    }

    //Admin quản lý người dùng
    /**
     * Hiển thị form sửa khách hàng
     */
    public function editUser($id) {
        // Lấy thông tin user từ Model
        $customer = $this->userModel->getUserById($id);
        
        if (!$customer) {
            $_SESSION['error'] = "Không tìm thấy khách hàng!";
            $this->redirect('/admin/users');
        }

        $this->renderView('admin/users/form', [
            'title' => 'Cập nhật thông tin khách hàng',
            'user' => Auth::user(),
            'customer' => $customer
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
                // Nếu có nhập mật khẩu thì hash, không thì để null
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
            // SỬA ĐOẠN NÀY: Nối thêm câu thông báo của bạn vào trước lỗi từ Model
            $_SESSION['error'] = "Bạn không thể xóa khách hàng này!" . $result;
        }
        $this->redirect('/admin/users');
    }

    // ======================================================
    // BÁO CÁO THỐNG KÊ ADMIN
    // ======================================================
    public function statistics() {
        $statType = $_POST['stat_type'] ?? '';
        // Mặc định lấy từ đầu tháng đến hiện tại
        $dateStart = $_POST['date_start'] ?? date('Y-m-01');
        $dateEnd = $_POST['date_end'] ?? date('Y-m-d');
        
        $data = ['labels' => [], 'values' => []];
        $title = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $statType) {
            switch ($statType) {
                case 'best-selling':
                    $title = 'Sản phẩm bán chạy nhất';
                    $results = $this->statisticModel->getBestSellingProducts($dateStart, $dateEnd);
                    break;
                case 'revenue':
                    $title = 'Tổng doanh thu theo ngày';
                    $results = $this->statisticModel->getRevenue($dateStart, $dateEnd);
                    break;
                case 'orders':
                    $title = 'Tổng số đơn hàng theo ngày';
                    $results = $this->statisticModel->getOrdersCount($dateStart, $dateEnd);
                    break;
                case 'cancelled-orders':
                    $title = 'Đơn hàng đã hủy theo ngày';
                    $results = $this->statisticModel->getCancelledOrders($dateStart, $dateEnd);
                    break;
                case 'top-customers':
                    $title = 'Top 5 Khách hàng mua nhiều nhất';
                    $results = $this->statisticModel->getTopCustomers($dateStart, $dateEnd);
                    break;
                default:
                    $results = [];
            }

            // Chuẩn bị dữ liệu cho Chart.js
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
            'chartTitle' => $title,
            'chartData' => $data
        ]);
    }
}