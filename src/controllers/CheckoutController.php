<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;
use App\Models\UserModel;
use App\Models\OrderModel;
use App\Models\CartModel;

class CheckoutController extends BaseController {

    private $userModel;
    private $orderModel;
    private $cartModel;

    public function __construct() {
        parent::__construct(); 
        Auth::requireLogin(); 
        
        $this->userModel = new UserModel();
        $this->orderModel = new OrderModel();
        $this->cartModel = new CartModel();
    }

    /**
     * HÀM MỚI (Helper): Tính toán tổng tiền
     */
    private function calculateCartTotals($cartItems) {
        $subtotal = 0;
        $totalDiscount = 0;
        foreach ($cartItems as $item) {
            $discountPercent = $item['discount_percent'] ?? 0;
            $itemTotal = $item['price'] * $item['quantity'];
            $subtotal += $itemTotal;
            $totalDiscount += ($itemTotal * $discountPercent / 100);
        }
        $total = $subtotal - $totalDiscount;
        return [
            'subtotal' => $subtotal,
            'totalDiscount' => $totalDiscount,
            'total' => $total
        ];
    }

    /**
     * Hiển thị trang Thanh toán
     */
    public function index() {
        $userId = Auth::id();
        $userCartId = Auth::cartId(); 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST['selected_items'])) {
                $this->redirect('/cart/index');
                return;
            }
            $fullCart = $this->cartModel->getCartContentsForUser($userCartId);
            $selectedIds = $_POST['selected_items'];
            $cartItems = []; 
            foreach ($selectedIds as $id) {
                if (isset($fullCart[$id])) { 
                    $cartItems[$id] = $fullCart[$id];
                }
            }
            if (empty($cartItems)) {
                $this->redirect('/cart/index');
                return;
            }
            $_SESSION['cart_for_checkout'] = $cartItems;
            $this->redirect('/checkout/index');
            return;
        }

        $cartItems = $_SESSION['cart_for_checkout'] ?? [];
        if (empty($cartItems)) {
            $this->redirect('/cart/index');
            return;
        }
        $user = Auth::user();
        $addresses = $this->userModel->getAllAddressesByUserId($userId);
        $paymentMethods = $this->orderModel->getAllPaymentMethods();
        $totals = $this->calculateCartTotals($cartItems);

        $this->renderView('checkout/index', [
            'title' => 'Thanh toán',
            'user' => $user,
            'addresses' => $addresses,
            'cartItems' => $cartItems,
            'paymentMethods' => $paymentMethods,
            'totals' => $totals
        ]);
    }

    /**
     * SỬA LẠI HOÀN CHỈNH: Xử lý Đặt hàng
     */
    public function placeOrder() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
            return;
        }

        $userId = Auth::id();
        $cartItems = $_SESSION['cart_for_checkout'] ?? [];

        // 1. Kiểm tra giỏ hàng
        if (empty($cartItems)) {
            $this->redirect('/cart/index');
            return;
        }

        // 2. Lấy thông tin từ Form
        $id_pttt = $_POST['payment_method_id']; 
        $selected_address_id = $_POST['selected_address_id']; 

        // 3. Lấy và định dạng địa chỉ
        $address = $this->userModel->findAddressById($userId, $selected_address_id);
        if (!$address) {
            $this->redirect('/checkout/index'); 
            return;
        }
        $formattedAddress = $this->formatAddressString($address);

        // 4. Tính toán tổng tiền
        $totals = $this->calculateCartTotals($cartItems);

        // 5. Chuẩn bị dữ liệu cho bảng 'don_hang'
        $newOrderId = $this->orderModel->generateNewOrderId(); // Lấy ID mới
        $data = [
            'ID_DH' => $newOrderId,
            'ID_PTTT' => $id_pttt,
            'ID_TK' => $userId,
            'DIA_CHI_GIAO_DH' => $formattedAddress,
            'TONG_GIA_TRI_DH' => $totals['subtotal'],
            'TIEN_GIAM_GIA' => $totals['totalDiscount'],
            'SO_TIEN_THANH_TOAN' => $totals['total'],
            'TRANG_THAI_THANH_TOAN' => ($id_pttt == 'PTTT1') ? 'Chưa thanh toán' : 'Đã thanh toán' 
        ];

        // 6. Thực thi lưu vào CSDL
        try {
            // Lưu đơn hàng chính
            $this->orderModel->createOrder($data);
            
            // Lưu chi tiết đơn hàng
            $this->orderModel->addOrderDetails($newOrderId, $cartItems);
            
            // ======================================================
            // THÊM MỚI (BƯỚC QUAN TRỌNG NHẤT): TẠO TRẠNG THÁI "CHỜ XỬ LÝ"
            // (Sử dụng hàm đã thêm vào OrderModel ở lượt trước)
            // ======================================================
            $this->orderModel->createInitialOrderStatus($newOrderId);
            // ======================================================

            // 7. Dọn dẹp Session và Chuyển hướng
            $userCartId = Auth::cartId();

            // Xóa các sản phẩm vừa mua ra khỏi giỏ hàng CHÍNH
            foreach ($cartItems as $id => $item) {
                $this->cartModel->removeProductForUser($userCartId, $id);
            }
            // Xóa giỏ hàng tạm thời
            unset($_SESSION['cart_for_checkout']); 
            
            // Lưu ID để trang success hiển thị
            $_SESSION['last_order_id'] = $newOrderId; 
            
            $this->redirect('/checkout/success');

        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->redirect('/checkout/index');
        }
    }

    /**
     * HÀM MỚI (Helper): Định dạng địa chỉ thành 1 chuỗi
     */
    private function formatAddressString($address) {
        return sprintf(
            "%s (%s)\nĐịa chỉ: %s, %s, %s, %s",
            $address['TEN_NGUOI_NHAN'],
            $address['SDT_GH'],
            $address['DIA_CHI_CHI_TIET'],
            $address['TEN_XA_PHUONG'],
            $address['TEN_QUAN_HUYEN'],
            $address['TEN_TINH_TP']
        );
    }

    /**
     * HÀM MỚI: Trang đặt hàng thành công
     */
    public function success() {
        $lastOrderId = $_SESSION['last_order_id'] ?? null;
        if (!$lastOrderId) {
            $this->redirect('/'); 
            return;
        }
        unset($_SESSION['last_order_id']); 

        $this->renderView('checkout/success', [
            'title' => 'Đặt hàng thành công',
            'lastOrderId' => $lastOrderId
        ]);
    }
}