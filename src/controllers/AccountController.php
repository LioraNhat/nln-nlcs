<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth; 
use App\Models\UserModel;
use App\Models\OrderModel;
use App\Models\AddressModel;

class AccountController extends BaseController {

    private $userModel;
    private $orderModel;
    private $addressModel; 

    public function __construct() {
        parent::__construct(); 
        Auth::requireLogin();
        
        $this->userModel = new UserModel(); 
        $this->orderModel = new OrderModel();
        $this->addressModel = new AddressModel();
    }

    /**
     * Trang Lịch sử đơn hàng
     */
    public function index() {
        $userId = Auth::id(); 
        $ordersPerPage = 5; 
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $searchKeyword = $_GET['search'] ?? ''; 
        $offset = ($currentPage - 1) * $ordersPerPage;
        
        $totalOrders = $this->orderModel->countOrdersByUserId($userId, $searchKeyword);
        $totalPages = ceil($totalOrders / $ordersPerPage);
        $orders = $this->orderModel->getOrdersByUserId($userId, $searchKeyword, $ordersPerPage, $offset);
        
        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']); 
        
        $this->renderView('account/index', [
            'title' => 'Lịch sử đơn hàng', 
            'orders' => $orders, 
            'user' => Auth::user(),
            'totalPages' => $totalPages, 
            'currentPage' => $currentPage, 
            'searchKeyword' => $searchKeyword,
            'offset' => $offset,
            'success' => $success,
            'error' => $error
        ]);
    }

    /**
     * Trang "Quản lý tài khoản"
     */
    public function profile() {
        $userId = Auth::id();
        // SỬA: Gọi AddressModel để lấy danh sách địa chỉ
        $addresses = $this->addressModel->getAddressesByUserId($userId);
        
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
     * Xử lý Thêm/Sửa Địa chỉ (Chung 1 hàm)
     */
    public function handleAddAddress() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = Auth::id();
            $idDiaChi = $_POST['id_dia_chi'] ?? '';
            
            $data = [
                'ten_nguoi_nhan' => $_POST['ten_nguoi_nhan'],
                'sdt_gh' => $_POST['sdt_gh'],
                'tinh_tp' => $_POST['province'], 
                'quan_huyen' => $_POST['district'],
                'xa_phuong' => $_POST['ward'],
                'ten_tinh_tp' => $_POST['tinh_tp'],   
                'ten_quan_huyen' => $_POST['quan_huyen'],
                'ten_xa_phuong' => $_POST['xa_phuong'],
                'dia_chi_chi_tiet' => $_POST['dia_chi_chi_tiet'],
                'is_default' => isset($_POST['is_default']) ? 1 : 0
            ];

            // THÊM ĐOẠN NÀY: Kiểm tra có phải AJAX không
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
                    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

            if ($idDiaChi) {
                // Cập nhật
                $this->addressModel->updateAddress($idDiaChi, $userId, $data);
                $message = 'Cập nhật địa chỉ thành công!';
            } else {
                // Thêm mới
                $this->addressModel->addAddress($userId, $data);
                $message = 'Thêm địa chỉ mới thành công!';
            }
            
            // QUAN TRỌNG: Xử lý khác nhau giữa AJAX và Form thường
            if ($isAjax) {
                // Nếu là AJAX → Trả JSON
                header('Content-Type: application/json');
                $newAddresses = $this->addressModel->getAddressesByUserId($userId);
                echo json_encode([
                    'success' => true, 
                    'message' => $message,
                    'newAddresses' => $newAddresses
                ]);
                exit;
            } else {
                // Nếu là Form thường → Redirect
                $_SESSION['success'] = $message;
                $this->redirect('/account/profile');
            }
        }
    }

    /**
     * Xử lý Cập nhật Địa chỉ (Dành cho nút Sửa)
     */
    public function handleUpdateAddress() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = Auth::id();
            // Lấy ID từ input hidden name="id_dia_chi"
            $addressId = $_POST['id_dia_chi']; 
            
            // Chuẩn bị dữ liệu (Khớp với name trong form modal)
            $data = [
                'ten_nguoi_nhan' => $_POST['ten_nguoi_nhan'],
                'sdt_gh' => $_POST['sdt_gh'],
                
                // Lấy ID Tỉnh/Huyện/Xã từ thẻ SELECT
                'tinh_tp' => $_POST['province'], 
                'quan_huyen' => $_POST['district'],
                'xa_phuong' => $_POST['ward'],
                
                // Lấy TÊN Tỉnh/Huyện/Xã từ thẻ INPUT HIDDEN
                'ten_tinh_tp' => $_POST['tinh_tp'],
                'ten_quan_huyen' => $_POST['quan_huyen'],
                'ten_xa_phuong' => $_POST['xa_phuong'],
                
                'dia_chi_chi_tiet' => $_POST['dia_chi_chi_tiet'],
                'is_default' => isset($_POST['is_default']) ? 1 : 0
            ];

            // THÊM ĐOẠN NÀY: Kiểm tra có phải AJAX không
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
                && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

            // Gọi Model để cập nhật
            $this->addressModel->updateAddress($addressId, $userId, $data);
            
            // QUAN TRỌNG: Xử lý khác nhau giữa AJAX và Form thường
            if ($isAjax) {
                // Nếu là AJAX → Trả JSON
                header('Content-Type: application/json');
                $newAddresses = $this->addressModel->getAddressesByUserId($userId);
                echo json_encode([
                    'success' => true, 
                    'message' => 'Cập nhật địa chỉ thành công!',
                    'newAddresses' => $newAddresses
                ]);
                exit;
            } else {
                // Nếu là Form thường → Redirect
                $_SESSION['success'] = 'Cập nhật địa chỉ thành công!';
                $this->redirect('/account/profile');
            }
        }
    }

    /**
     * Xử lý Đặt làm Mặc định
     */
    public function handleSetDefaultAddress() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = Auth::id();
            $addressId = $_POST['default_address_id']; 
            
            // SỬA: Gọi AddressModel
            $this->addressModel->setDefaultAddress($addressId, $userId);
            
            $_SESSION['success'] = 'Đã cập nhật địa chỉ mặc định!';
            $this->redirect('/account/profile');
        }
    }

    /**
     * Xử lý Xóa địa chỉ
     */
    public function deleteAddress($addressId) {
        $userId = Auth::id();
        $this->addressModel->deleteAddress($addressId, $userId);
        
        $_SESSION['success'] = 'Đã xóa địa chỉ thành công!';
        $this->redirect('/account/profile');
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
            
            if ($this->userModel->updateProfile($userId, $data)) {
                // QUAN TRỌNG: Cập nhật Session ngay lập tức
                $_SESSION['user']['HO_TEN'] = $data['ho_ten'];
                $_SESSION['user']['SDT_TK'] = $data['sdt_tk'];
                $_SESSION['user']['GIOI_TINH'] = $data['gioi_tinh'];
                
                $_SESSION['success'] = 'Cập nhật thông tin thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại.';
            }
            
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
                $this->redirect('/account/profile'); 
                return;
            }
            
            $result = $this->userModel->changePassword($userId, $currentPass, $newPass);
            
            if ($result === true) {
                $_SESSION['success'] = 'Đổi mật khẩu thành công!';
            } else {
                // result trả về chuỗi lỗi
                $_SESSION['error'] = $result; 
            }
            $this->redirect('/account/profile');
        }
    }
    
    /**
     * API Lấy thông tin 1 địa chỉ (Dành cho JS khi bấm nút Sửa)
     */
    public function getAddressJson($addressId) {
        $userId = Auth::id();
        $address = $this->addressModel->findAddressById($userId, $addressId);
        
        header('Content-Type: application/json');
        if ($address) {
            echo json_encode(['success' => true, 'data' => $address]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy địa chỉ']);
        }
        exit;
    }

    /**
     * Xử lý Hủy đơn hàng
     */
    public function cancelOrder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = Auth::id();
            $orderId = $_POST['id_dh'];

            if ($this->orderModel->cancelUserOrder($orderId, $userId)) {
                $_SESSION['success'] = "Đã hủy đơn hàng $orderId thành công.";
            } else {
                $_SESSION['error'] = "Không thể hủy đơn hàng $orderId.";
            }
            $this->redirect('/account/index');
        }
    }
}