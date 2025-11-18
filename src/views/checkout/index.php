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
                                   id="addr_<?php echo $addr['ID_DIA_CHI']; ?>" 
                                   name="selected_address_id"  /* (TÊN INPUT ĐÚNG) */
                                   value="<?php echo $addr['ID_DIA_CHI']; ?>"
                                   <?php 
                                        // Tự động check radio:
                                        // 1. Nếu là mặc định
                                        // 2. Hoặc nếu là cái đầu tiên (khi không có cái nào mặc định)
                                        if ($addr['IS_DEFAULT'] == 1 || ($index == 0 && !array_filter($addresses, fn($a) => $a['IS_DEFAULT'] == 1))) {
                                            echo 'checked';
                                        }
                                   ?>
                            >
                            <label for="addr_<?php echo $addr['ID_DIA_CHI']; ?>" class="radio-label">
                                <strong>
                                <?php echo htmlspecialchars($addr['TEN_NGUOI_NHAN']); ?>
                                <?php if ($addr['IS_DEFAULT'] == 1): ?>
                                    <span class="default-tag-small">Mặc định</span>
                                <?php endif; ?>

                                <span class="address-actions">
                                    <a href="#" class="address-action-link btn-edit-address" 
                                       data-id="<?php echo $addr['ID_DIA_CHI']; ?>">
                                       Sửa
                                    </a>
                                </span>
                                </strong><br>
                                SĐT: <?php echo htmlspecialchars($addr['SDT_GH']); ?><br>
                                ĐC: <?php echo htmlspecialchars($addr['DIA_CHI_CHI_TIET']); ?>, 
                                <?php echo htmlspecialchars($addr['TEN_XA_PHUONG']); ?>, 
                                <?php echo htmlspecialchars($addr['TEN_QUAN_HUYEN']); ?>, 
                                <?php echo htmlspecialchars($addr['TEN_TINH_TP']); ?>
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
                            <input type="radio" id="pttt_<?php echo $method['ID_PTTT']; ?>" 
                                   name="payment_method_id" 
                                   value="<?php echo $method['ID_PTTT']; ?>" 
                                   <?php if ($index == 0) echo 'checked'; ?>
                            >
                            <label for="pttt_<?php echo $method['ID_PTTT']; ?>" class="radio-label">
                                <?php echo htmlspecialchars($method['TEN_PTTT']); ?>
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
                            $discountPercent = $item['discount_percent'] ?? 0;
                            $discountedPrice = $item['price'] * (1 - $discountPercent / 100);
                        ?>
                            <div class="checkout-item">
                                <img src="<?php echo BASE_PATH; ?>/uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="">
                                <div class="item-info">
                                    <span class="item-name"><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)</span>
                                    <span class="item-price"><?php echo number_format($discountedPrice * $item['quantity']); ?> đ</span>
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