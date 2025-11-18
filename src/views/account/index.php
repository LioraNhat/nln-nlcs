<?php 
require_once __DIR__ . '/../partials/header.php'; 
?>

<div class="container account-page-container">
    
    <div class="account-header">
        <h2>Đơn hàng của bạn</h2>
        <a href="<?php echo BASE_PATH; ?>/account/profile" class="btn-secondary">
            Quản lý tài khoản
        </a>
    </div>

    <form action="<?php echo BASE_PATH; ?>/account/index" method="GET" class="account-search-form">
        <input type="text" name="search" placeholder="Tìm theo mã đơn hàng..." value="<?php echo htmlspecialchars($searchKeyword); ?>">
        <button type="submit">Tìm kiếm</button>
    </form>
    
    <!-- =============================================== -->
    <!-- THÊM MỚI: Thông báo (nếu có) -->
    <!-- =============================================== -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Ngày đặt</th>
                    <th>Mã đơn hàng</th>
                    <th>Thành tiền</th>
                    <th>Trạng thái đơn hàng</th>
                    <!-- =============================================== -->
                    <!-- SỬA: Thêm cột "Thao tác" -->
                    <!-- =============================================== -->
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <!-- SỬA: colspan="6" (vì thêm 1 cột) -->
                        <td colspan="6" style="text-align: center; padding: 20px;">
                            <?php if (!empty($searchKeyword)): ?>
                                Không tìm thấy đơn hàng nào khớp với "<?php echo htmlspecialchars($searchKeyword); ?>".
                            <?php else: ?>
                                Bạn chưa có đơn hàng nào.
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $index => $order): ?>
                        <tr>
                            <td><?php echo $offset + $index + 1; ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($order['NGAY_GIO_TAO_DON'])); ?></td>
                            <td><?php echo htmlspecialchars($order['ID_DH']); ?></td>
                            <td><?php echo number_format($order['SO_TIEN_THANH_TOAN']); ?> đ</td>
                            <td>
                                <span class="status-badge">
                                    <?php 
                                    // SỬA: Dùng biến $status để tái sử dụng
                                    $status = htmlspecialchars($order['TRANG_THAI_DHHT'] ?? 'Chờ xử lý'); 
                                    echo $status;
                                    ?>
                                </span>
                            </td>
                            <!-- =============================================== -->
                            <!-- THÊM MỚI: Cột "Thao tác" với logic Hủy đơn -->
                            <!-- =============================================== -->
                            <td>
                                <?php if ($status === 'Chờ xử lý'): ?>
                                    <form action="<?php echo BASE_PATH; ?>/account/cancelOrder" method="POST" 
                                          onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?');">
                                        <input type="hidden" name="id_dh" value="<?php echo $order['ID_DH']; ?>">
                                        <button type="submit" class="btn-cancel-order">Hủy đơn</button>
                                    </form>
                                <?php else: ?>
                                    <!-- Hiển thị gạch ngang nếu không thể hủy -->
                                    <span>--</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- ... (Code phân trang giữ nguyên) ... -->
</div>

<?php 
require_once __DIR__ . '/../partials/footer.php'; 
?>