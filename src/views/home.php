<?php 
// 1. Tải Header (Bố cục mới)
require_once __DIR__ . '/partials/header.php'; 
?>

<div class="container">
    <section class="hero-slider">
        <div class="slider-slide active">
            <img src="<?php echo BASE_PATH; ?>/images/banner-1.jpg" alt="Banner 1">
        </div>
        <div class="slider-slide">
            <img src="<?php echo BASE_PATH; ?>/images/banner-2.jpg" alt="Banner 2">
        </div>
    </section>
</div>

<section class="usp-bar">
    <div class="container">
        <div class="usp-item">
            <div class="usp-icon-wrapper" style="background-color: #317a68;">
                <img src="<?php echo BASE_PATH; ?>/images/error1.png" alt="1 đổi 1 sản phẩm lỗi">
            </div>
            <span>1 ĐỔI 1 SẢN PHẨM LỖI</span>
        </div>
        
        <div class="usp-item">
            <div class="usp-icon-wrapper" style="background-color: #e85a21;">
                <img src="<?php echo BASE_PATH; ?>/images/shipping.png" alt="Giao hàng trong ngày">
            </div>
            <span>GIAO HÀNG TRONG NGÀY<br><small>(Miễn phí vận chuyển)*</small></span>
        </div>

        <div class="usp-item">
            <div class="usp-icon-wrapper" style="background-color: #0f4991;">
                <img src="<?php echo BASE_PATH; ?>/images/origin.png" alt="Nguồn gốc rõ ràng">
            </div>
            <span>NGUỒN GỐC XUẤT XỨ RÕ RÀNG</span>
        </div>
        
        <div class="usp-item">
            <div class="usp-icon-wrapper" style="background-color: #a7885b;">
                <img src="<?php echo BASE_PATH; ?>/images/quality.png" alt="Sản phẩm đạt chuẩn">
            </div>
            <span>100% SẢN PHẨM ĐẠT CHUẨN</span>
        </div>
    </div>
</section>


<div class="container">
    <section class="product-section">
        <h2 class="section-title hotdeal-title">
             <img src="<?php echo BASE_PATH; ?>/images/hotdeal.jpg" alt="Hot Deals" style="width: 40%; border-radius: 8px; display: block; margin: 0 auto 10px auto;">
        </h2>
        
        <div class="product-grid">
            <?php 
            if (isset($hotdealProducts) && !empty($hotdealProducts)):
                foreach ($hotdealProducts as $product):
                    require __DIR__ . '/partials/product-card.php';
                endforeach; 
            endif; 
            ?>
        </div>
    </section>
</div>


<?php
// Dùng $homeCategorySections từ HomeController
if (isset($homeCategorySections) && !empty($homeCategorySections)):
    foreach ($homeCategorySections as $section):
        $categoryInfo = $section['info'];
        $categoryProducts = $section['products'];
?>

    <div class="container">
        <section class="promo-banner">
            <img src="<?php echo BASE_PATH; ?>/images/banner-<?php echo $categoryInfo['ID_DM']; ?>.jpg" 
                 alt="<?php echo htmlspecialchars($categoryInfo['TEN_DM']); ?>" 
                 style="width: 100%; border-radius: 8px;">
        </section>
    </div>

    <div class="container">
        <section class="product-section">
            <h2 class="section-title green-title">
                <?php echo htmlspecialchars($categoryInfo['TEN_DM']); ?>
            </h2>
            
            <div class="product-grid">
                <?php 
                if (!empty($categoryProducts)):
                    foreach ($categoryProducts as $product): 
                        require __DIR__ . '/partials/product-card.php';
                    endforeach; 
                else:
                ?>
                    <p style="text-align: center; grid-column: 1 / -1;">Chưa có sản phẩm cho danh mục này.</p>
                <?php 
                endif; 
                ?>
            </div>
            
            <a href="<?php echo BASE_PATH; ?>/product/category/<?php echo $categoryInfo['ID_DM']; ?>" class="btn-view-more">
                &lt;&lt;&lt;Xem thêm <?php echo htmlspecialchars(strtolower($categoryInfo['TEN_DM'])); ?> &gt;&gt;&gt;
            </a>
        </section>
    </div>

<?php
    endforeach; 
endif; 
?>
<?php 
// Tải Footer (Giữ nguyên)
require_once __DIR__ . '/partials/footer.php'; 
?>