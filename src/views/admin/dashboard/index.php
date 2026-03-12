<?php
// 1. Gọi Header
require_once __DIR__ . '/../layouts/header.php';

// 2. Gọi Sidebar
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Tổng quan (Dashboard)</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-primary">
                        <div class="inner">
                            <h3><?php echo $totalOrders ?? 0; ?></h3>
                            <p>Đơn hàng mới</p>
                        </div>
                        <i class="small-box-icon bi bi-cart-fill"></i>
                        <a href="<?php echo BASE_PATH; ?>/admin/orders" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            Xem chi tiết <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-success">
                        <div class="inner">
                            <h3><?php echo number_format($totalRevenue ?? 0, 0, ',', '.'); ?><sup style="font-size: 20px">đ</sup></h3>
                            <p>Doanh thu</p>
                        </div>
                        <i class="small-box-icon bi bi-currency-dollar"></i>
                        <a href="#" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            Xem báo cáo <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-warning">
                        <div class="inner">
                            <h3><?php echo $totalUsers ?? 0; ?></h3>
                            <p>Khách hàng</p>
                        </div>
                        <i class="small-box-icon bi bi-people-fill"></i>
                        <a href="<?php echo BASE_PATH; ?>/admin/users" class="small-box-footer link-dark link-underline-opacity-0 link-underline-opacity-50-hover">
                            Xem chi tiết <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-danger">
                        <div class="inner">
                            <h3><?php echo $totalProducts ?? 0; ?></h3>
                            <p>Sản phẩm</p>
                        </div>
                        <i class="small-box-icon bi bi-box-seam-fill"></i>
                        <a href="<?php echo BASE_PATH; ?>/admin/products" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            Xem kho <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card mb-4">
                        <div class="card-header border-0">
                            <h3 class="card-title">Đơn hàng gần đây</h3>
                            <div class="card-tools">
                                <a href="<?php echo BASE_PATH; ?>/admin/orders" class="btn btn-tool btn-sm">
                                    <i class="bi bi-list"></i> Xem tất cả
                                </a>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Mã ĐH</th>
                                        <th>Khách hàng</th>
                                        <th>Ngày đặt</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recentOrders)): ?>
                                        <?php foreach ($recentOrders as $order): ?>
                                            <tr>
                                                <td>
                                                    <a href="<?php echo BASE_PATH; ?>/admin/orders/view?id=<?php echo $order['id_dh']; ?>" class="text-primary fw-bold">
                                                        #<?php echo $order['id_dh']; ?>
                                                    </a>
                                                </td>
                                                <td><?php echo htmlspecialchars($order['ho_ten'] ?? 'Khách lẻ'); ?></td>
                                                
                                                <td><?php echo date('d/m/Y H:i', strtotime($order['ngay_gio_tao_don'] ?? 'now')); ?></td>
                                                
                                                <td><?php echo number_format($order['thanh_tien'] ?? 0, 0, ',', '.'); ?>đ</td>
                                                
                                                <td>
                                                    <?php 
                                                        // SỬA: TRANG_THAI_DHHT -> ten_trang_thai (Sử dụng cột từ JOIN bảng danh_muc_trang_thai)
                                                        $statusName = $order['ten_trang_thai'] ?? 'Chờ xử lý';
                                                        $statusId = $order['id_ttd'] ?? 'TTD01';
                                                        
                                                        $statusColor = 'secondary';
                                                        if ($statusId == 'TTD01') $statusColor = 'warning';   // Chờ xử lý
                                                        if ($statusId == 'TTD03') $statusColor = 'primary';   // Đang giao
                                                        if ($statusId == 'TTD04') $statusColor = 'success';   // Hoàn thành
                                                        if ($statusId == 'TTD05') $statusColor = 'danger';    // Đã hủy
                                                    ?>
                                                    <span class="badge text-bg-<?php echo $statusColor; ?>">
                                                        <?php echo $statusName; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="<?php echo BASE_PATH; ?>/admin/orders/view?id=<?php echo $order['id_dh']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Chưa có đơn hàng nào.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php
// 3. Gọi Footer
require_once __DIR__ . '/../layouts/footer.php';
?>