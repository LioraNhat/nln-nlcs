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
                                    // Lấy giá trị số để sắp xếp (mặc định là 99 nếu không tìm thấy)
                                    $sortStatus = $mapStatus[$row['TRANG_THAI_DHHT']] ?? 99; 
                                ?>
                                
                                <tr>
                                    <td><?= $row['ID_DH'] ?></td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($row['HO_TEN']) ?></div>
                                        <small class="text-muted">ID: <?= $row['ID_TK'] ?></small>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($row['NGAY_GIO_TAO_DON'])) ?></td>
                                    <td class="text-end fw-bold text-danger"><?= number_format($row['SO_TIEN_THANH_TOAN']) ?>đ</td>
                                    
                                    <td data-order="<?= $sortStatus ?>">
                                        <span class="badge <?= getBadgeClass($row['TRANG_THAI_DHHT']) ?>"><?= $row['TRANG_THAI_DHHT'] ?></span>
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
    // Sử dụng Event Listener 'DOMContentLoaded' để đảm bảo HTML tải xong
    document.addEventListener('DOMContentLoaded', function() {
        // Kiểm tra xem jQuery đã được tải chưa
        if (typeof $ !== 'undefined') {
            $('#table-orders').DataTable({
                "paging": false,       // Tắt phân trang client (vì đã limit ở server)
                "lengthChange": false,
                "searching": false,    // Tắt search client (vì đã có form search server)
                "ordering": false,     // QUAN TRỌNG: Tắt sắp xếp client để giữ thứ tự từ SQL
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