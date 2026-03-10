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
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
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
                        <?php 
                            $status = $order['ten_trang_thai'] ?? 'Chờ xử lý';
                        ?>
                        <tr>
                            <td><?php echo $offset + $index + 1; ?></td>

                            <td>
                                <?php echo date('Y-m-d H:i', strtotime($order['ngay_gio_tao_don'])); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($order['id_dh']); ?>
                            </td>

                            <td>
                                <?php echo number_format($order['thanh_tien']); ?> đ
                            </td>
                            
                            <td>
                                <span class="status-badge">
                                    <?php echo htmlspecialchars($status); ?>
                                </span>
                            </td>

                            <td>
                                <div style="display:flex; gap:10px; align-items:center;">

                                    <a href="<?php echo BASE_PATH; ?>/account/orderDetail/<?php echo $order['id_dh']; ?>" 
                                       class="btn-view-order"
                                       style="text-decoration:none;color:#007bff;font-weight:bold;">
                                        Xem
                                    </a>

                                    <?php if ($order['id_ttd'] === 'TTD01'): ?>
                                        <form action="<?php echo BASE_PATH; ?>/account/cancel-order" 
                                              method="POST"
                                              onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?');"
                                              style="margin:0;">

                                            <input type="hidden" name="id_dh" value="<?php echo $order['id_dh']; ?>">

                                            <button type="submit"
                                                    class="btn-cancel-order"
                                                    style="color:red;border:none;background:none;cursor:pointer;font-weight:bold;">
                                                Hủy
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span style="color:#aaa;">--</span>
                                    <?php endif; ?>

                                </div>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php 
require_once __DIR__ . '/../partials/footer.php'; 
?>