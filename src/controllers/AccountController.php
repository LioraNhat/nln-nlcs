<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth; 
use App\Models\UserModel;
use App\Models\OrderModel;

class AccountController extends BaseController {

    private $userModel;
    private $orderModel; 

    public function __construct() {
        parent::__construct(); 
        Auth::requireLogin();
        $this->userModel = new UserModel(); 
        $this->orderModel = new OrderModel();
    }

    /**
     * Trang Lịch sử đơn hàng (Giữ nguyên)
     */
    public function index() {
        // $orderModel = new \App\Models\OrderModel(); // Đã chuyển lên __construct
        $userId = Auth::id(); 
        $ordersPerPage = 5; $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $searchKeyword = $_GET['search'] ?? ''; $offset = ($currentPage - 1) * $ordersPerPage;
        
        // Sửa: Dùng $this->orderModel
        $totalOrders = $this->orderModel->countOrdersByUserId($userId, $searchKeyword);
        $totalPages = ceil($totalOrders / $ordersPerPage);
        $orders = $this->orderModel->getOrdersByUserId($userId, $searchKeyword, $ordersPerPage, $offset);
        
        // Sửa: Lấy thông báo từ session
        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']); 
        
        $this->renderView('account/index', [
            'title' => 'Lịch sử đơn hàng', 'orders' => $orders, 'user' => Auth::user(),
            'totalPages' => $totalPages, 'currentPage' => $currentPage, 'searchKeyword' => $searchKeyword,
            'offset' => $offset,
            'success' => $success, // Truyền thông báo
            'error' => $error      // Truyền thông báo
        ]);
    }

    /**
     * Trang "Quản lý tài khoản" (Gửi danh sách địa chỉ)
     */
    public function profile() {
        $userId = Auth::id();
        $addresses = $this->userModel->getAllAddressesByUserId($userId); // Lấy TẤT CẢ
        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']); 
        $this->renderView('account/profile', [
            'title' => 'Quản lý tài khoản',
            'user' => Auth::user(),
            'addresses' => $addresses, 
            'success' => $success,
            'error' => $error
        ]);
    }

    /**
     * Xử lý Thêm Địa chỉ Mới (từ Modal)
     */
    public function handleAddAddress() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = Auth::id();
            $is_default = isset($_POST['is_default']) ? 1 : 0;
            
            $data = [
                'ho_ten' => $_POST['ho_ten'],
                'sdt_gh' => $_POST['sdt_gh'],
                'id_tinh_tp' => $_POST['province_code'],
                'ten_tinh_tp' => $_POST['province_name'],
                'id_quan_huyen' => $_POST['district_code'],
                'ten_quan_huyen' => $_POST['district_name'],
                'id_xa_phuong' => $_POST['ward_code'],
                'ten_xa_phuong' => $_POST['ward_name'],
                'dia_chi_chi_tiet' => $_POST['dia_chi_chi_tiet'],
                'is_default' => $is_default
            ];

            if ($is_default == 1) {
                $this->userModel->setDefaultAddress($userId, null); 
            }
            
            $this->userModel->addAddress($userId, $data);
            
            // (Code thêm địa chỉ ở trên...)

            // KIỂM TRA AJAX
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

            if ($isAjax) {
                // Nếu là AJAX (từ Modal):
                // 1. Lấy lại TOÀN BỘ danh sách địa chỉ mới
                $newAddresses = $this->userModel->getAllAddressesByUserId($userId);
                // 2. Trả về JSON
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'newAddresses' => $newAddresses]);
                exit;
            } else {
                // Nếu là form thường (fallback):
                $_SESSION['success'] = 'Thêm địa chỉ mới thành công!';
                $this->redirect('/account/profile');
            }
        }
    }

    /**
     * Xử lý Đặt làm Mặc định (từ Radio button)
     */
    public function handleSetDefaultAddress() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = Auth::id();
            $addressId = $_POST['default_address_id']; 
            $this->userModel->setDefaultAddress($userId, $addressId);
            $_SESSION['success'] = 'Đã cập nhật địa chỉ mặc định!';
            $this->redirect('/account/profile');
        }
    }

    /**
     * Xử lý cập nhật thông tin cá nhân
     */
    public function handleUpdateProfile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = Auth::id();
            $data = [
                'ho_ten' => $_POST['ho_ten'],
                'sdt_tk' => $_POST['sdt_tk'],
                'gioi_tinh' => $_POST['gioi_tinh']
            ];
            $this->userModel->updateProfile($userId, $data);
            $_SESSION['success'] = 'Cập nhật thông tin thành công!';
            $this->redirect('/account/profile');
        }
    }

    /**
     * Xử lý đổi mật khẩu
     */
    public function handleChangePassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = Auth::id();
            $currentPass = $_POST['current_password'];
            $newPass = $_POST['new_password'];
            $confirmPass = $_POST['new_password_confirm'];
            if ($newPass !== $confirmPass) {
                $_SESSION['error'] = 'Mật khẩu mới không khớp.';
                $this->redirect('/account/profile'); return;
            }
            $success = $this->userModel->changePassword($userId, $currentPass, $newPass);
            if ($success) {
                $_SESSION['success'] = 'Đổi mật khẩu thành công!';
            } else {
                $_SESSION['error'] = 'Mật khẩu hiện tại không đúng.';
            }
            $this->redirect('/account/profile');
        }
    }
    
    /**
     * HÀM MỚI (CHO NÚT "SỬA"): Lấy thông tin 1 địa chỉ (cho AJAX)
     */
    public function getAddressJson($addressId) {
        $userId = Auth::id();
        $address = $this->userModel->findAddressById($userId, $addressId);
        
        header('Content-Type: application/json');
        if ($address) {
            echo json_encode(['success' => true, 'data' => $address]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy địa chỉ']);
        }
        exit;
    }

    /**
     * HÀM MỚI (CHO NÚT "SỬA"): Xử lý Cập nhật Địa chỉ (từ Modal)
     */
    public function handleUpdateAddress() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = Auth::id();
            $addressId = $_POST['address_id']; // Lấy ID của địa chỉ đang Sửa
            
            $is_default = isset($_POST['is_default']) ? 1 : 0;
            
            $data = [
                'ho_ten' => $_POST['ho_ten'],
                'sdt_gh' => $_POST['sdt_gh'],
                'id_tinh_tp' => $_POST['province_code'], // Mã tỉnh
                'ten_tinh_tp' => $_POST['province_name'],
                'id_quan_huyen' => $_POST['district_code'], // Mã huyện
                'ten_quan_huyen' => $_POST['district_name'],
                'id_xa_phuong' => $_POST['ward_code'], // Mã xã
                'ten_xa_phuong' => $_POST['ward_name'],
                'dia_chi_chi_tiet' => $_POST['dia_chi_chi_tiet'],
                'is_default' => $is_default
            ];

            if ($is_default == 1) {
                $this->userModel->setDefaultAddress($userId, null); // Set tất cả về 0
            }
            
            $this->userModel->updateAddressDetails($addressId, $userId, $data);
            
            // KIỂM TRA AJAX
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

            if ($isAjax) {
                // Nếu là AJAX (từ Modal):
                // 1. Lấy lại TOÀN BỘ danh sách địa chỉ mới
                $newAddresses = $this->userModel->getAllAddressesByUserId($userId);
                // 2. Trả về JSON
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'newAddresses' => $newAddresses]);
                exit;
            } else {
                // Nếu là form thường (fallback):
                $_SESSION['success'] = 'Cập nhật địa chỉ mới thành công!';
                $this->redirect('/account/profile');
            }
        }
    }
    
    /**
     * HÀM MỚI (CHO NÚT "XÓA"): Xử lý Xóa Địa chỉ
     */
    public function deleteAddress($addressId) {
        $userId = Auth::id();
        $this->userModel->deleteAddress($userId, $addressId);
        $_SESSION['success'] = 'Đã xóa địa chỉ thành công!';

        //Quay lại trang trước
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/account/profile'); 
    }

    // ======================================================
    // THÊM MỚI: Xử lý Hủy đơn hàng
    // ======================================================
    public function cancelOrder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = Auth::id();
            $orderId = $_POST['id_dh'];

            if (empty($orderId)) {
                $_SESSION['error'] = 'Có lỗi xảy ra, không tìm thấy mã đơn hàng.';
                $this->redirect('/account/index');
                return;
            }

            // Gọi Model để hủy (chúng ta sẽ tạo hàm này ở Bước 4)
            $success = $this->orderModel->cancelUserOrder($orderId, $userId);

            if ($success) {
                $_SESSION['success'] = "Đã hủy đơn hàng $orderId thành công.";
            } else {
                $_SESSION['error'] = "Không thể hủy đơn hàng $orderId. Đơn hàng có thể đã được xử lý hoặc không thuộc về bạn.";
            }

            $this->redirect('/account/index');
        }
    }
}