<div class="product-card">
    
    <?php 
        // Xử lý dữ liệu ban đầu để tránh lỗi null và sai lệch Key
        $id_hh = $product['id_hh'] ?? $product['ID_HH'] ?? '';
        $ten_hh = (string)($product['ten_hh'] ?? $product['TEN_HH'] ?? 'Sản phẩm chưa có tên');
        $gia_hien_tai = (float)($product['gia_hien_tai'] ?? 0);
        $phan_tram_km = (float)($product['phan_tram_km'] ?? 0);
        $link_anh = (string)($product['link_anh'] ?? $product['LINK_ANH'] ?? 'default-image.png');
    ?>

    <?php if ($phan_tram_km > 0): ?>
        <div class="product-discount-tag">
            -<?php echo $phan_tram_km; ?>%
        </div>
    <?php endif; ?>

    <a href="<?php echo BASE_PATH; ?>/product/detail/<?php echo $id_hh; ?>" class="product-card-link">
        <img src="<?php echo BASE_PATH; ?>/uploads/<?php echo htmlspecialchars($link_anh === "" ? 'default-image.png' : $link_anh); ?>" 
             alt="<?php echo htmlspecialchars($ten_hh); ?>" 
             class="product-image">
    </a>
    
    <div class="product-info">
        <a href="<?php echo BASE_PATH; ?>/product/detail/<?php echo $id_hh; ?>" class="product-card-link">
             <h4><?php echo htmlspecialchars($ten_hh); ?></h4>
        </a>
        
        <div class="product-price-wrapper">
            <?php if ($phan_tram_km > 0): ?>
                <?php $discountedPrice = $gia_hien_tai * (1 - $phan_tram_km / 100); ?>
                <span class="product-price-discounted">
                    <?php echo number_format($discountedPrice); ?> đ
                </span>
                <span class="product-price-original">
                    <?php echo number_format($gia_hien_tai); ?> đ
                </span>
            <?php else: ?>
                <span class="product-price">
                    <?php echo number_format($gia_hien_tai); ?> đ
                </span>
            <?php endif; ?>
        </div>
        
        <button type="button" class="btn-add-to-cart-quick" data-id="<?php echo $id_hh; ?>">
            <img src="<?php echo BASE_PATH; ?>/images/cart-button-icon.png" alt="" class="cart-button-icon">
            Thêm vào giỏ
        </button>
    </div>
</div>