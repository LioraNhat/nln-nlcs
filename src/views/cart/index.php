<?php 
require_once __DIR__ . '/../partials/header.php'; 
?>

<div class="container cart-page-container">
    <h2>Giỏ hàng của bạn</h2>

    <?php if (empty($cartItems)): ?>
        <p class="cart-empty-message">Giỏ hàng của bạn đang trống.</p>
    <?php else: ?>
        
        <div style="text-align: right; margin-bottom: 15px;">
            <button type="button" id="btn-clear-cart" class="btn-remove-item">
                Làm trống giỏ hàng
            </button>
        </div>
        
        <form action="<?php echo BASE_PATH; ?>/checkout/index" method="POST" id="form-cart-checkout">
            <div class="cart-layout">
            
                <div class="cart-items-list">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th class="cart-checkbox-cell"><input type="checkbox" id="cart-select-all"></th>
                                <th colspan="2">Sản phẩm</th>
                                <th>Giá</th>
                                <th>Số lượng</th>
                                <th>Tạm tính</th>
                                <th>Xóa</th>
                            </tr>
                        </thead>
                        <tbody id="cart-tbody"> 
                            <?php foreach ($cartItems as $item): 
                                $discountPercent = $item['discount_percent'] ?? 0;
                                $discountedPrice = $item['price'] * (1 - $discountPercent / 100);
                            ?>
                                <tr id="cart-item-<?php echo $item['id']; ?>" 
                                    data-price="<?php echo $discountedPrice; ?>" 
                                    data-subtotal="<?php echo $item['price']; ?>"
                                    data-discount-percent="<?php echo $discountPercent; ?>">
                                    
                                    <td class="cart-checkbox-cell">
                                        <input type="checkbox" class="cart-item-select" name="selected_items[]" value="<?php echo $item['id']; ?>">
                                    </td>

                                    <td class="cart-item-image">
                                        <img src="<?php echo BASE_PATH; ?>/uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    </td>
                                    <td class="cart-item-name">
                                        <a href="<?php echo BASE_PATH; ?>/product/detail/<?php echo $item['id']; ?>">
                                            <?php echo htmlspecialchars($item['name']); ?>
                                        </a>
                                        <div class="cart-item-price-mobile">
                                            <?php if ($discountPercent > 0): ?>
                                                <span class="product-price-discounted"><?php echo number_format($discountedPrice); ?> đ</span>
                                                <span class="product-price-original"><?php echo number_format($item['price']); ?> đ</span>
                                            <?php else: ?>
                                                <span class="product-price"><?php echo number_format($item['price']); ?> đ</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    
                                    <td class="cart-item-price">
                                        <?php if ($discountPercent > 0): ?>
                                            <span class="product-price-original"><?php echo number_format($item['price']); ?> đ</span>
                                        <?php else: ?>
                                            <span><?php echo number_format($item['price']); ?> đ</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="cart-item-quantity">
                                        <div class="quantity-input-group">
                                            <button type="button" class="btn-quantity-change" data-id="<?php echo $item['id']; ?>" data-change="-1">−</button>
                                            <input type="text" value="<?php echo $item['quantity']; ?>" class="quantity-field" data-id="<?php echo $item['id']; ?>" readonly>
                                            <button type="button" class="btn-quantity-change" data-id="<?php echo $item['id']; ?>" data-change="1">+</button>
                                        </div>
                                    </td>
                                    
                                    <td class="cart-item-subtotal">
                                        <?php 
                                            $originalItemSubtotal = $item['price'] * $item['quantity'];
                                            $discountedItemSubtotal = $discountedPrice * $item['quantity'];
                                        ?>
                                        <?php if ($discountPercent > 0): ?>
                                            <span class="product-price-discounted"><?php echo number_format($discountedItemSubtotal); ?> đ</span>
                                            <span class="product-price-original"><?php echo number_format($originalItemSubtotal); ?> đ</span>
                                        <?php else: ?>
                                            <span class="product-price"><?php echo number_format($originalItemSubtotal); ?> đ</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td class="cart-item-remove">
                                        <a href="<?php echo BASE_PATH; ?>/cart/remove/<?php echo $item['id']; ?>" class="btn-remove btn-remove-item">X</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="cart-summary">
                    <h4>Tổng cộng giỏ hàng</h4>
                    <div class="summary-row">
                        <span>Tạm tính</span>
                        <span class="summary-price" id="cart-subtotal">0 đ</span>
                    </div>
                    <div class="summary-row">
                        <span>Khuyến mãi</span>
                        <span class="summary-price" id="cart-discount">0 đ</span>
                    </div>
                    <div class="summary-row total-row">
                        <span>Tổng cộng</span>
                        <span class="summary-price total-price" id="cart-total">0 đ</span>
                    </div>
                    <?php if ($isLoggedIn): ?>
                        <button type="submit" class="btn-checkout">
                            Tiến hành Thanh toán
                        </button>
                    <?php else: ?>
                        <a href="<?php echo BASE_PATH; ?>/auth/login" class="btn-checkout" 
                        style="display: block; text-align: center; text-decoration: none;">
                            Đăng nhập để thanh toán
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php 
require_once __DIR__ . '/../partials/footer.php'; 
?>