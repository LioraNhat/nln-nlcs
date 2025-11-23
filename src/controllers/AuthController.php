<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\UserModel;
use App\Models\CartModel;
use App\Core\Auth; 

class AuthController extends BaseController {

    private $userModel;

    public function __construct() {
        parent::__construct(); 
        $this->userModel = new UserModel();
    }

    // Hiển thị form đăng nhập
    public function login() {
        $this->renderView('auth/login', ['error' => '']);
    }

    /**
     * SỬA: Xử lý POST từ form đăng nhập (dùng SĐT/Email)
     */
    public function handleLogin() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $username = $_POST['username'];
            $password = $_POST['password'];
            $user = $this->userModel->login($username, $password); 
            
            if ($user) {
                // ĐĂNG NHẬP THÀNH CÔNG
                $_SESSION['user_id'] = $user['ID_TK'];
                $_SESSION['user_name'] = $user['HO_TEN'];
                $_SESSION['user_email'] = $user['EMAIL'];
                $_SESSION['user_phone'] = $user['SDT_TK'];
                $_SESSION['user_gender'] = $user['GIOI_TINH'];
                $_SESSION['cart_id'] = $user['ID_GH'];
                $_SESSION['user_role_id'] = $user['ID_ND'];
                $_SESSION['user_role'] = $user['PHAN_QUYEN_TK'];
                $_SESSION['user'] = $user;

                // Logic phân quyền
                if ($user['ID_ND'] === 'AD') {
                    $this->redirect('/admin/dashboard');
                } else {
                    // ===============================================
                    // BƯỚC 3: GỘP GIỎ HÀNG (CODE SẠCH)
                    // ===============================================
                    $cartModel = new CartModel(); // Luôn cần CartModel
                    if (!empty($_SESSION['cart'])) {
                        $cartModel->mergeSessionCartToDb($user['ID_GH'], $_SESSION['cart']);
                        unset($_SESSION['cart']); 
                    }
                    
                    // ===============================================
                    // SỬA LỖI ICON: TẠO SESSION COUNT KHI ĐĂNG NHẬP
                    // ===============================================
                    // Lấy tổng số lượng TỪ CSDL sau khi gộp
                    $_SESSION['cart_count'] = $cartModel->getCartItemCountForUser($user['ID_GH']);
                    // ===============================================
                    
                    $this->redirect('/'); 
                }
            } else {
                $this->renderView('auth/login', ['error' => 'Email/SĐT hoặc mật khẩu không đúng.']); 
            }
        } else {
            $this->redirect('/auth/login'); 
        }
    }

    // Hiển thị form đăng ký
    public function register() {
        $this->renderView('auth/register', ['errors' => []]); 
    }

    /**
     * Xử lý POST từ form đăng ký (Kiểm tra SĐT và Email)
     */
    public function handleRegister() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $email = $_POST['email'];
            $sdt = $_POST['sdt_tk'];
            $password = $_POST['password']; // Lấy password
            $errors = []; 

            if ($this->userModel->findByEmail($email)) {
                $errors['email'] = 'Email này đã được sử dụng.';
            }
            if ($this->userModel->findByPhone($sdt)) {
                $errors['sdt'] = 'Số điện thoại này đã được sử dụng.';
            }
            if (strlen($password) < 6) { // Thêm check độ dài
                $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự.';
            }
            if ($_POST['password'] !== $_POST['password_confirm']) {
                $errors['password'] = 'Mật khẩu không khớp.';
            }

            if (!empty($errors)) {
                $this->renderView('auth/register', ['errors' => $errors, 'old' => $_POST]); 
                return;
            }
            
            $data = [
                'ho_ten' => $_POST['ho_ten'], 'email' => $email,
                'password' => $password, // $password đã lấy ở trên
                'sdt_tk' => $sdt,
                'gioi_tinh' => $_POST['gioi_tinh']
            ];

            if ($this->userModel->register($data)) {
                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'message' => 'Bạn đã đăng ký thành công, hãy đăng nhập lại.'
                ];
                $this->redirect('/auth/login'); 
            } else {
                // Sửa: Gửi lại 'old' data cả khi lỗi server
                $this->renderView('auth/register', [
                    'errors' => ['server' => 'Đã có lỗi xảy ra. Vui lòng thử lại.'],
                    'old' => $_POST 
                ]);
            }
        } else {
            $this->redirect('/auth/register'); 
        }
    }

    // Xử lý Đăng xuất
    public function logout() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
            return;
        }
        
        $_SESSION = [];
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        session_destroy();
        $this->redirect('/auth/login');
    }
}