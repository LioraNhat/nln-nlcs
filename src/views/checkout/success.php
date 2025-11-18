<?php 
require_once __DIR__ . '/../partials/header.php'; 
?>

<div class="container" style="text-align: center; padding: 60px 20px;">
    
    <h1 style="color: var(--primary-color); font-size: 2rem;">Đặt hàng thành công!</h1>
    
    <p style="font-size: 1.1rem; margin-top: 15px; color: var(--text-color-secondary);">
        Cảm ơn bạn đã mua hàng. Mã đơn hàng của bạn là:
    </p>
    
    <h2 style="font-size: 2.2rem; margin-top: 10px; color: var(--text-color); background-color: var(--bg-light-gray); padding: 10px 20px; display: inline-block; border-radius: 8px;">
        <?php echo htmlspecialchars($lastOrderId); ?>
    </h2>

    <p style="margin-top: 30px;">
        <a href="<?php echo BASE_PATH; ?>/" class="btn-filter-submit" style="text-decoration: none; padding: 15px 30px; font-size: 1rem;">
            Tiếp tục mua sắm
        </a>
    </p>
    
    <p style="margin-top: 15px;">
        <a href="<?php echo BASE_PATH; ?>/account/index" style="color: var(--primary-color); font-weight: 600;">
            Xem lịch sử đơn hàng
        </a>
    </p>

</div>

<?php 
require_once __DIR__ . '/../partials/footer.php'; 
?>