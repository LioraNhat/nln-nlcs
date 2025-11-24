<?php
// Lấy đường dẫn hiện tại để so sánh và active menu
$currentUri = $_SERVER['REQUEST_URI'];

// Hàm hỗ trợ kiểm tra active (nếu đường dẫn chứa từ khóa thì trả về 'active')
function menuActive($uri, $keyword) {
    return strpos($uri, $keyword) !== false ? 'active' : '';
}
?>

<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="<?php echo BASE_PATH; ?>/admin/dashboard" class="brand-link">
            <img src="<?php echo BASE_PATH; ?>/admin_assets/assets/img/AdminLTELogo.png" alt="Logo" class="brand-image opacity-75 shadow" />
            <span class="brand-text fw-light">NLN Food Admin</span>
        </a>
    </div>
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                
                <li class="nav-item">
                    <a href="<?php echo BASE_PATH; ?>/admin/dashboard" class="nav-link <?php echo menuActive($currentUri, '/admin/dashboard'); ?>">
                        <i class="nav-icon bi bi-speedometer"></i>
                        <p>Tổng quan</p>
                    </a>
                </li>

                <li class="nav-header">QUẢN LÝ</li>

                <li class="nav-item">
                    <a href="<?php echo BASE_PATH; ?>/admin/products" class="nav-link <?php echo menuActive($currentUri, '/admin/products'); ?>">
                        <i class="nav-icon bi bi-box-seam"></i>
                        <p>Hàng hóa</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo BASE_PATH; ?>/admin/categories" class="nav-link <?php echo menuActive($currentUri, '/admin/categories'); ?>">
                        <i class="nav-icon bi bi-bookmark"></i>
                        <p>Danh mục</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo BASE_PATH; ?>/admin/product-types" class="nav-link <?php echo menuActive($currentUri, '/admin/product-types'); ?>">
                        <i class="nav-icon bi bi-tags"></i>
                        <p>Loại hàng hóa</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo BASE_PATH; ?>/admin/promotions" class="nav-link <?php echo menuActive($currentUri, '/admin/promotions'); ?>">
                        <i class="nav-icon bi bi-ticket-perforated"></i>
                        <p>Khuyến mãi</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo BASE_PATH; ?>/admin/inventories" class="nav-link <?php echo menuActive($currentUri, '/admin/inventories'); ?>">
                        <i class="nav-icon bi bi-boxes"></i>
                        <p>Tồn kho</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo BASE_PATH; ?>/admin/suppliers" class="nav-link">
                        <i class="nav-icon bi bi-building"></i> <p>Nhà cung cấp</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo BASE_PATH; ?>/admin/orders" class="nav-link <?php echo menuActive($currentUri, '/admin/orders'); ?>">
                        <i class="nav-icon bi bi-cart-check"></i>
                        <p>Đơn hàng</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo BASE_PATH; ?>/admin/users" class="nav-link <?php echo menuActive($currentUri, '/admin/users'); ?>">
                        <i class="nav-icon bi bi-people"></i>
                        <p>Khách hàng</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo BASE_PATH; ?>/admin/statistics" class="nav-link <?php echo menuActive($currentUri, '/admin/users'); ?>">
                        <i class="nav-icon bi bi-people"></i>
                        <p>Thống kê</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo BASE_PATH; ?>/admin/settings" class="nav-link <?php echo menuActive($currentUri, '/admin/settings'); ?>">
                        <i class="nav-icon bi bi-gear"></i>
                        <p>Cài đặt</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>