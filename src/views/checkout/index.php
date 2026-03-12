<?php 
require_once __DIR__ . '/../partials/header.php'; 
?>

<div class="container checkout-page-container">
    <h2>Thanh toán Đơn hàng</h2>
    
    <form action="<?php echo BASE_PATH; ?>/checkout/placeOrder" method="POST">
        <div class="checkout-layout">
        
            <div class="checkout-info">
                <h3>1. Thông tin giao hàng</h3>
                
                <div class="address-list-checkout">
                    <?php if (!empty($addresses)): // Đã sửa thành $addresses (số nhiều) ?>
                        
                        <?php foreach($addresses as $index => $addr): // Đã thêm vòng lặp ?>
                        <div class="form-group-radio">
                            <input type="radio" 
                                id="addr_<?php echo $addr['id_dc']; ?>" 
                                name="selected_address_id" 
                                value="<?php echo $addr['id_dc']; ?>"
                                <?php 
                                        if ($addr['mac_dinh'] == 1 || ($index == 0 && !array_filter($addresses, fn($a) => $a['mac_dinh'] == 1))) {
                                            echo 'checked';
                                        }
                                ?>
                            >
                            <label for="addr_<?php echo $addr['id_dc']; ?>" class="radio-label">
                                <strong>
                                <?php echo htmlspecialchars($addr['ten_nguoi_nhan'] ?? ''); ?>
                                <?php if ($addr['mac_dinh'] == 1): ?>
                                    <span class="default-tag-small">Mặc định</span>
                                <?php endif; ?>
                                </strong><br>
                                SĐT: <?php echo htmlspecialchars($addr['sdt_gh'] ?? ''); ?><br>
                                ĐC: <?php echo htmlspecialchars($addr['dia_chi_chi_tiet'] ?? ''); ?>, 
                                <?php echo htmlspecialchars($addr['ten_xa_phuong'] ?? ''); ?>, 
                                <?php echo htmlspecialchars($addr['ten_quan_huyen'] ?? ''); ?>, 
                                <?php echo htmlspecialchars($addr['ten_tinh_tp'] ?? ''); ?>
                            </label>
                        </div>
                        <?php endforeach; ?>

                    <?php else: ?>
                        <p style="color: red; font-weight: 600;">Bạn chưa có địa chỉ nào!</p>
                        <p>Vui lòng quản lý sổ địa chỉ để thêm địa chỉ mới trước khi đặt hàng.</p>
                    <?php endif; ?>
                </div>

                <div class="checkout-address-actions">
                    <button type="button" class="btn-primary" id="btn-show-add-modal">
                        + Thêm địa chỉ mới
                    </button>
                </div>

                <hr class="checkout-divider">

                <h3>2. Phương thức thanh toán</h3>
                <div class="payment-methods">
                    <?php foreach ($paymentMethods as $index => $method): ?>
                        <div class="form-group-radio">
                            <input type="radio" 
                                id="pttt_<?php echo $method['id_pttt']; ?>" <?php // Sửa thành id_pttt ?>
                                name="payment_method_id" 
                                value="<?php echo $method['id_pttt']; ?>" <?php // Sửa thành id_pttt ?>
                                <?php if ($index == 0) echo 'checked'; ?>
                            >
                            <label for="pttt_<?php echo $method['id_pttt']; ?>" class="radio-label"> <?php // Sửa thành id_pttt ?>
                                <?php echo htmlspecialchars($method['ten_pttt'] ?? ''); ?> <?php // Sửa thành ten_pttt ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="checkout-summary">
                <div class="cart-summary">
                    <h4>Tóm tắt đơn hàng</h4>
                    
                    <div class="checkout-item-list">
                        <?php foreach ($cartItems as $item): 
                            // Lấy dữ liệu từ mảng đã định nghĩa trong CartModel
                            $price = $item['price'] ?? 0;
                            $quantity = $item['quantity'] ?? 0;
                            $discountPercent = $item['discount_percent'] ?? 0;
                            
                            // Tính toán giá sau khi giảm
                            $discountedPrice = $price * (1 - $discountPercent / 100);
                        ?>
                            <div class="checkout-item">
                                <img src="<?php echo BASE_PATH; ?>/uploads/<?php echo htmlspecialchars($item['image'] ?? ''); ?>" alt="">
                                <div class="item-info">
                                    <span class="item-name">
                                        <?php echo htmlspecialchars($item['name'] ?? 'Sản phẩm'); ?> 
                                        (x<?php echo $quantity; ?>)
                                    </span>
                                    <span class="item-price">
                                        <?php echo number_format($discountedPrice * $quantity); ?> đ
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="summary-row">
                        <span>Tạm tính</span>
                        <span class="summary-price"><?php echo number_format($totals['subtotal']); ?> đ</span>
                    </div>
                    <div class="summary-row">
                        <span>Khuyến mãi</span>
                        <span class="summary-price">-<?php echo number_format($totals['totalDiscount']); ?> đ</span>
                    </div>
                    <div class="summary-row total-row">
                        <span>Tổng cộng</span>
                        <span class="summary-price total-price"><?php echo number_format($totals['total']); ?> đ</span>
                    </div>
                    
                    <button type="submit" class="btn-checkout" 
                        <?php if (empty($addresses)) echo 'disabled'; ?>
                    >
                        <?php if (empty($addresses)): ?>
                            Vui lòng thêm địa chỉ
                        <?php else: ?>
                            ĐẶT HÀNG
                        <?php endif; ?>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php
require_once __DIR__ . '/../partials/address-modal.php';
require_once __DIR__ . '/../partials/footer.php'; 
?>