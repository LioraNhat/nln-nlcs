<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">Quản lý Đơn hàng</h3></div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show"><?= $success ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show"><?= $error ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>

            <div class="card card-outline card-primary">
                <div class="card-body">
                    <table id="table-orders" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Mã ĐH</th>
                                <th>Khách hàng</th>
                                <th>Ngày đặt</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái xử lý</th>
                                <th>Thanh toán</th>
                                <th class="text-center">Xem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $row): ?>
                                <tr>
                                    <td><a href="<?= BASE_PATH ?>/admin/order-detail/<?= $row['ID_DH'] ?>" class="fw-bold"><?= $row['ID_DH'] ?></a></td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($row['HO_TEN']) ?></div>
                                        <small class="text-muted">ID: <?= $row['ID_TK'] ?></small>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($row['NGAY_GIO_TAO_DON'])) ?></td>
                                    <td class="text-end fw-bold text-danger"><?= number_format($row['SO_TIEN_THANH_TOAN']) ?>đ</td>
                                    <td class="text-center">
                                        <?php 
                                        $stt = $row['TRANG_THAI_DHHT'];
                                        $badge = 'bg-secondary';
                                        if ($stt == 'Chờ xử lý') $badge = 'bg-warning text-dark';
                                        elseif ($stt == 'Đang giao hàng') $badge = 'bg-info text-dark';
                                        elseif ($stt == 'Giao hàng thành công') $badge = 'bg-success';
                                        elseif ($stt == 'Đã hủy') $badge = 'bg-danger';
                                        ?>
                                        <span class="badge <?= $badge ?>"><?= $stt ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($row['TRANG_THAI_THANH_TOAN'] == 'Đã thanh toán'): ?>
                                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Đã TT</span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-dark border">Chưa TT</span>
                                        <?php endif; ?>
                                    </td>
                                    <td align="center">
                                        <a href="<?= BASE_PATH ?>/admin/order-detail/<?= $row['ID_DH'] ?>" class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
<script>
    $(document).ready(function() { 
        $('#table-orders').DataTable({
            "order": [[ 2, "desc" ]] // Sắp xếp mặc định theo ngày đặt (cột thứ 3)
        }); 
    });
</script>