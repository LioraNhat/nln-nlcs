<?php 
// 1. Tải Header (Full-width)
require_once __DIR__ . '/../partials/header.php'; 
?>

<div class="container">
    <div class="product-detail-layout">
        
        <div class="product-gallery">
            <div class="product-main-image">
                <img src="<?php echo BASE_PATH; ?>/uploads/<?php echo htmlspecialchars($product['link_anh'] ?? 'default-image.png'); ?>" 
                     alt="<?php echo htmlspecialchars($product['TEN_HH']); ?>" 
                     style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
            </div>
        </div>
        
        <div class="product-info-main">
            <?php 
            if (isset($product) && !empty($product)): 
            ?>
                <h1><?php echo htmlspecialchars($product['TEN_HH']); ?></h1>
                
                <div class="product-detail-price-wrapper">
                    <?php if (isset($product['PHAN_TRAM_KM']) && $product['PHAN_TRAM_KM'] > 0): ?>
                        <?php 
                        // 1. Tính toán giá đã giảm
                        $discountedPrice = $product['GIA_HIEN_TAI'] * (1 - $product['PHAN_TRAM_KM'] / 100);
                        ?>
                        <span class="product-detail-price-new">
                            <?php echo number_format($discountedPrice); ?> đ
                        </span>
                        <span class="product-detail-price-old">
                            <?php echo number_format($product['GIA_HIEN_TAI']); ?> đ
                        </span>
                    <?php else: ?>
                        <span class="product-detail-price-new">
                            <?php echo number_format($product['GIA_HIEN_TAI']); ?> đ
                        </span>
                    <?php endif; ?>
                </div>
                <p class="product-stock">
                    Tình trạng: <strong>Còn <?php echo (int)$product['SO_LUONG_TON_HH']; ?> sản phẩm</strong>
                </p>

                <form class="add-to-cart-form" action="<?php echo BASE_PATH; ?>/cart/add" method="POST">
                    <input type="hidden" name="id_hh" value="<?php echo $product['ID_HH']; ?>">
                    
                    <div class="quantity-selector">
                        <label for="quantity">Số lượng:</label>
                        <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo (int)$product['SO_LUONG_TON_HH']; ?>">
                    </div>
                    
                    <button type="submit" class="btn-add-to-cart">
                        Thêm vào giỏ hàng
                    </button>
                </form>

                <div class="product-accordion">
                    
                    <div class="accordion-item active"> <div class="accordion-header">
                            <h3>Mô tả sản phẩm</h3>
                            <button type="button" class="accordion-toggle">−</button> </div>
                        
                        <div class="accordion-content" style="max-height: 500px;"> 
                            <p>
                                <?php echo nl2br(htmlspecialchars($product['MO_TA_HH'])); ?>
                            </p>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <div class="accordion-header">
                            <h3>CHÍNH SÁCH BÁN HÀNG</h3>
                            <button type="button" class="accordion-toggle">+</button> </div>
                        
                        <div class="accordion-content">
                            <p><strong>Cam kết thực phẩm “GreenMeal”:</strong> Sạch từ nông trại – Sạch qua quá trình sơ chế, chế biến - Sạch đến bàn ăn.</p>
                            <p><strong>Miễn phí vận chuyển</strong> đối với đơn hàng trên 10,00,000VND khi đặt hàng trên website. Phí giao hàng tiêu chuẩn: 30,000VND đơn hàng dưới < 500,000VND. Tùy khu vực, phí sẽ có sự thay đổi tăng hoặc giảm.</p>
                            <p><strong>Hotline hỗ trợ:</strong> 0999999999 - <strong>Email:</strong> greenmeal@gmail.com</p>
                        </div>
                    </div>

                </div>
            <?php
            else:
                echo "<h1>Sản phẩm không tồn tại.</h1>";
            endif; 
            ?>
        </div>
    </div>
</div>

<div class="container">
    <section class="product-section">
        <h2 class="section-title green-title">Sản Phẩm Tương Tự</h2>
        <div class="product-grid">
            <?php 
            if (isset($relatedProducts) && !empty($relatedProducts)):
                foreach ($relatedProducts as $product):
                    require __DIR__ . '/../partials/product-card.php';
                endforeach; 
            endif; 
            ?>
        </div>
    </section>
</div>

<?php 
require_once __DIR__ . '/../partials/footer.php'; 
?>