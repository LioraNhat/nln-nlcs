<?php // File: src/views/admin/index.php (Đã "Tách" code) ?>

<?php
// Ghi chú: Biến $title, $user, và $stats
// được truyền từ AdminController::index()
?>

<h1>Chào mừng bạn đến với Trang Quản Trị</h1>
<hr>
<div class="row">
    <!-- Box 1: Tổng Hàng Hóa -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-light elevation-1"><i class="fas fa-box-open"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Tổng Hàng Hóa (Sản phẩm)</span>
                <span class="info-box-number">
                    <?php 
                        // Đã tách: Dùng biến $stats từ Controller
                        echo number_format($stats['totalProducts'] ?? 0);
                    ?>
                </span>
            </div>
        </div>
    </div>
    
    <!-- Box 2: Chờ xử lý -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-th-list"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Đơn hàng Chờ xử lý</span>
                <span class="info-box-number">
                    <?php 
                        // Đã tách: Dùng biến $stats từ Controller
                        echo number_format($stats['pendingOrders'] ?? 0);
                    ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Box 3: Tổng doanh số (Chờ xử lý) -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Doanh số (Chờ xử lý)</span>
                <span class="info-box-number">
                    <?php 
                        // Đã tách: Dùng biến $stats từ Controller
                        echo number_format($stats['pendingSales'] ?? 0);
                    ?>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <p>Các chức năng đã "tách":</p>
    <ul>
        <li><a href="/admin/categories">Quản lý Danh mục</a></li>
        <li><a href="/admin/productTypes">Quản lý Loại Hàng Hóa</a></li>
    </ul>
</div>

<?php 
// GHI CHÚ: Phần Carousel (slideshow ảnh) từ file admin/home.php cũ
// đã được tạm thời bỏ qua vì nó khá phức tạp và cần truy vấn CSDL
// theo cách khác. Chúng ta có thể "tách" nó sau nếu bạn muốn.
?>