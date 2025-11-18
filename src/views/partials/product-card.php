<div class="product-card">
    
    <?php if (isset($product['PHAN_TRAM_KM']) && $product['PHAN_TRAM_KM'] > 0): ?>
        <div class="product-discount-tag">
            -<?php echo (int)$product['PHAN_TRAM_KM']; ?>%
        </div>
    <?php endif; ?>

    <a href="<?php echo BASE_PATH; ?>/product/detail/<?php echo $product['ID_HH']; ?>" class="product-card-link">
        <img src="<?php echo BASE_PATH; ?>/uploads/<?php echo htmlspecialchars($product['link_anh'] ?? 'default-image.png'); ?>" 
             alt="<?php echo htmlspecialchars($product['TEN_HH']); ?>" 
             class="product-image">
    </a>
    
    <div class="product-info">
        <a href="<?php echo BASE_PATH; ?>/product/detail/<?php echo $product['ID_HH']; ?>" class="product-card-link">
             <h4><?php echo htmlspecialchars($product['TEN_HH']); ?></h4>
        </a>
        
        <div class="product-price-wrapper">
            <?php if (isset($product['PHAN_TRAM_KM']) && $product['PHAN_TRAM_KM'] > 0): ?>
                <?php 
                $discountedPrice = $product['GIA_HIEN_TAI'] * (1 - $product['PHAN_TRAM_KM'] / 100);
                ?>
                <span class="product-price-discounted">
                    <?php echo number_format($discountedPrice); ?> đ
                </span>
                <span class="product-price-original">
                    <?php echo number_format($product['GIA_HIEN_TAI']); ?> đ
                </span>
            <?php else: ?>
                <span class="product-price">
                    <?php echo number_format($product['GIA_HIEN_TAI']); ?> đ
                </span>
            <?php endif; ?>
        </div>
        
        <button type="button" class="btn-add-to-cart-quick" data-id="<?php echo $product['ID_HH']; ?>">
            <img src="<?php echo BASE_PATH; ?>/images/cart-button-icon.png" alt="" class="cart-button-icon">
            Thêm vào giỏ
        </button>
    </div>
</div>