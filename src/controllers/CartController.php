<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\ProductModel;
use App\Models\CartModel; 
use App\Core\Auth; 

class CartController extends BaseController {
    
    private $productModel;
    private $cartModel;

    public function __construct() {
        parent::__construct(); 
        $this->productModel = new ProductModel();
        $this->cartModel = new CartModel();
    }

    /**
     * HÀM ADD (Đã sửa)
     */
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('/');

        if (empty($_POST['id_hh'])) {
            // Trả về lỗi JSON nếu là Ajax hoặc redirect
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                 echo json_encode(['success' => false, 'message' => 'Lỗi: Không tìm thấy sản phẩm']);
                 exit;
            }
            $this->redirect('/');
        }

        $id_hh = $_POST['id_hh'];
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        $totalCount = 0;

        if (Auth::isLoggedIn()) {
            $userCartId = Auth::cartId();
            $this->cartModel->addProductForUser($userCartId, $id_hh, $quantity);
            $totalCount = $this->cartModel->getCartItemCountForUser($userCartId);
        } else {
            if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
            $product = $this->productModel->findProductById($id_hh);
            if ($product && $quantity > 0) {
                if (isset($_SESSION['cart'][$id_hh])) {
                    $_SESSION['cart'][$id_hh]['quantity'] += $quantity;
                } else {
                    $_SESSION['cart'][$id_hh] = [
                        'id' => $product['ID_HH'], 'name' => $product['TEN_HH'],
                        'price' => $product['GIA_HIEN_TAI'], 'image' => $product['link_anh'],
                        'quantity' => $quantity, 'discount_percent' => $product['PHAN_TRAM_KM'] ?? 0 
                    ];
                }
            }
            $totalCount = $this->cartModel->getSessionItemCount($_SESSION['cart']);
        }

        $_SESSION['cart_count'] = $totalCount;
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'cartCount' => $totalCount]);
            exit; 
        }
        $this->redirect('/cart/index');
    }

    /**
     * HÀM INDEX (Đã sửa lỗi Reset về 0)
     */
    public function index() {
        $cartItems = [];
        $totalCount = 0; // Thêm biến
        $isLoggedIn = Auth::isLoggedIn();

        if ($isLoggedIn) {
            $userCartId = Auth::cartId(); 
            $cartItems = $this->cartModel->getCartContentsForUser($userCartId);
            $totalCount = $this->cartModel->getCartItemCountForUser($userCartId);
        } else {
            $cartItems = $_SESSION['cart'] ?? [];
            $totalCount = $this->cartModel->getSessionItemCount($cartItems);
        }
        $_SESSION['cart_count'] = $totalCount;
        $totals = $this->calculateCartTotals($cartItems);
        
        $this->renderView('cart/index', [
            'cartItems' => $cartItems, 
            'subtotal' => $totals['subtotal'],
            'totalDiscount' => $totals['totalDiscount'], 
            'total' => $totals['total'],
            'isLoggedIn' => $isLoggedIn
        ]);
    }

    /**
     * HÀM UPDATE
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('/');

        if (empty($_POST['id_hh']) || !isset($_POST['quantity'])) {
             echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
             exit;
        }

        $id_hh = $_POST['id_hh'];
        $quantity = (int)$_POST['quantity'];
        $totalCount = 0; $cartItems = [];

        if (Auth::isLoggedIn()) {
            $userCartId = Auth::cartId();
            $this->cartModel->updateProductForUser($userCartId, $id_hh, $quantity);
            $totalCount = $this->cartModel->getCartItemCountForUser($userCartId);
            $cartItems = $this->cartModel->getCartContentsForUser($userCartId);
        } else {
            if (isset($_SESSION['cart'][$id_hh])) {
                if ($quantity > 0) $_SESSION['cart'][$id_hh]['quantity'] = $quantity;
                else unset($_SESSION['cart'][$id_hh]);
            }
            $cartItems = $_SESSION['cart'] ?? [];
            $totalCount = $this->cartModel->getSessionItemCount($cartItems);
        }
        $_SESSION['cart_count'] = $totalCount;
        $totals = $this->calculateCartTotals($cartItems);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'cartCount' => $totalCount,
            // ... (phần còn lại giữ nguyên) ...
        ]);
        exit;
    }

    /**
     * HÀM REMOVE (Đã sửa)
     */
    public function remove($id_hh) {
        $totalCount = 0; $cartItems = [];
        if (Auth::isLoggedIn()) {
            $userCartId = Auth::cartId();
            $this->cartModel->removeProductForUser($userCartId, $id_hh);
            $totalCount = $this->cartModel->getCartItemCountForUser($userCartId);
            $cartItems = $this->cartModel->getCartContentsForUser($userCartId);
        } else {
            unset($_SESSION['cart'][$id_hh]);
            $cartItems = $_SESSION['cart'] ?? [];
            $totalCount = $this->cartModel->getSessionItemCount($cartItems);
        }
        
        // ===============================================
        // SỬA LỖI: Lưu vào Session
        // ===============================================
        $_SESSION['cart_count'] = $totalCount;
        // ===============================================

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $totals = $this->calculateCartTotals($cartItems);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true, 'cartCount' => $totalCount,
                // ... (phần còn lại giữ nguyên) ...
            ]);
            exit;
        }
        $this->redirect('/cart/index');
    }

    /**
     * HÀM CLEAR (Đã sửa)
     */
    public function clear() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('/');
        if (Auth::isLoggedIn()) {
            $userCartId = Auth::cartId();
            $this->cartModel->clearCartForUser($userCartId);
        } else {
            $_SESSION['cart'] = [];
        }

        // ===============================================
        // SỬA LỖI: Lưu vào Session
        // ===============================================
        $_SESSION['cart_count'] = 0;
        // ===============================================

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Giỏ hàng đã được làm trống.', 'cartCount' => 0]);
        exit;
    }
    
    private function calculateCartTotals($cartItems) {
        $subtotal = 0; $totalDiscount = 0;
        foreach ($cartItems as $item) {
            $itemTotal = $item['price'] * $item['quantity'];
            $subtotal += $itemTotal;
            // Sửa lỗi nhỏ: Kiểm tra 'discount_percent' tồn tại
            if (isset($item['discount_percent']) && $item['discount_percent'] > 0) {
                $totalDiscount += ($item['price'] * $item['discount_percent'] / 100) * $item['quantity'];
            }
        }
        $total = $subtotal - $totalDiscount;
        return ['subtotal' => $subtotal, 'totalDiscount' => $totalDiscount, 'total' => $total];
    }
}