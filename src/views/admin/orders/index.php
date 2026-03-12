<?php
// 1. Gọi Header & Sidebar
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';

// Hàm helper lấy class badge
function getBadgeClass($status) {
    switch($status) {
        case 'Chờ xử lý': return 'bg-warning text-dark';
        case 'Đã xác nhận': return 'bg-primary';
        case 'Đang giao hàng': return 'bg-info text-dark';
        case 'Giao hàng thành công': return 'bg-success';
        case 'Đã hủy': return 'bg-danger';
        default: return 'bg-secondary';
    }
}

// Mảng ánh xạ trạng thái sang số thứ tự để sắp xếp (ưu tiên đơn cần xử lý)
$mapStatus = [
    'Chờ xử lý' => 1,
    'Đã xác nhận' => 2,
    'Đang giao hàng' => 3,
    'Giao hàng thành công' => 4,
    'Đã hủy' => 5,
];
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">Quản lý Đơn hàng</h3></div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <form action="" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Nhập từ khóa tìm kiếm..." value="<?= isset($searchKeyword) ? htmlspecialchars($searchKeyword) : '' ?>">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Tìm kiếm</button>
                            <?php if(isset($searchKeyword) && $searchKeyword != ''): ?>
                                <a href="?" class="btn btn-secondary" title="Xóa tìm kiếm"><i class="bi bi-x-lg"></i></a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
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
                                <?php 
                                    // Sử dụng ten_trang_thai để lấy class sắp xếp
                                    $sortStatus = $mapStatus[$row['ten_trang_thai']] ?? 99; 
                                ?>
                                
                                <tr>
                                    <td><?= $row['id_dh'] ?></td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($row['ho_ten']) ?></div>
                                        <small class="text-muted">ID: <?= $row['id_tk'] ?></small>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($row['ngay_gio_tao_don'])) ?></td>
                                    <td class="text-end fw-bold text-danger"><?= number_format($row['thanh_tien']) ?>đ</td>
                                    
                                    <td data-order="<?= $sortStatus ?>">
                                        <span class="badge <?= getBadgeClass($row['ten_trang_thai']) ?>"><?= $row['ten_trang_thai'] ?></span>
                                    </td>

                                    <td class="text-center">
                                        <?php if ($row['trang_thai_thanh_toan'] == 'Đã thanh toán'): ?>
                                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Đã TT</span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-dark border">Chưa TT</span>
                                        <?php endif; ?>
                                    </td>
                                    <td align="center">
                                        <a href="<?= BASE_PATH ?>/admin/order-detail/<?= $row['id_dh'] ?>" class="btn btn-sm btn-primary">
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
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $ !== 'undefined') {
            $('#table-orders').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": false,
                "info": false,
                "autoWidth": false,
                "responsive": true,
                "language": {
                    "emptyTable": "Không có dữ liệu đơn hàng"
                }
            });
        } else {
            console.error("Lỗi: jQuery chưa được tải. Hãy kiểm tra file layouts/footer.php");
        }
    });
</script>