<?php
// SỬA LẠI LOGIC TÍNH WAC
$total_qty_remain = 0; // Thay đổi từ total_qty_nhap
$total_value      = 0;
$average_cost     = 0;

if (!empty($lots)) {
    foreach ($lots as $lot) {
        $qty_remain = (float)($lot['so_luong_con_lai'] ?? 0);
        if ($qty_remain <= 0) continue;

        $price = (float)($lot['gia_von_nhap'] ?? 0);

        $total_qty_remain += $qty_remain;
        $total_value      += ($qty_remain * $price); // Giá trị tồn kho hiện tại
    }

    if ($total_qty_remain > 0) {
        $average_cost = $total_value / $total_qty_remain;
    }
}

$suggested_price = $average_cost * (1 + ($margin / 100));
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="app-main">

    <!-- HEADER -->
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h3 class="mb-0">Chi tiết lô hàng</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="<?= BASE_PATH ?>/admin/inventories">Quản lý tồn kho</a>
                            </li>
                            <li class="breadcrumb-item active">Danh sách lô</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-sm-6 text-end">
                    <?php if (!empty($product)): ?>
                    <a href="<?= BASE_PATH ?>/admin/inventories/create?id_hh=<?= $product['id_hh'] ?>"
                       class="btn btn-success me-2">
                        <i class="bi bi-plus-circle"></i> Nhập lô mới
                    </a>
                    <?php endif; ?>
                    <a href="<?= BASE_PATH ?>/admin/inventories" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">

            <?php if (!empty($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= $_SESSION['success'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <?php if (!empty($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= $_SESSION['error'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- ===== THÔNG TIN SẢN PHẨM ===== -->
            <?php if (!empty($product)): ?>
            <div class="card card-outline card-info mb-3 mt-3">
                <div class="card-header"><h3 class="card-title">Thông tin sản phẩm</h3></div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <?php
                                $imgSrc = (!empty($product['link_anh']))
                                    ? BASE_PATH . '/uploads/' . $product['link_anh']
                                    : BASE_PATH . '/admin_assets/assets/img/default-150x150.png';
                            ?>
                            <img src="<?= $imgSrc ?>" class="img-thumbnail shadow-sm"
                                 style="max-height:120px;object-fit:contain;">
                        </div>
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-sm-4">
                                    <p class="text-muted small mb-1">Mã sản phẩm</p>
                                    <p class="fw-bold"><?= $product['id_hh'] ?></p>
                                </div>
                                <div class="col-sm-4">
                                    <p class="text-muted small mb-1">Tên sản phẩm</p>
                                    <p class="fw-bold"><?= htmlspecialchars($product['ten_hh']) ?></p>
                                </div>
                                <div class="col-sm-4">
                                    <p class="text-muted small mb-1">Danh mục</p>
                                    <p class="fw-bold"><?= htmlspecialchars($product['ten_loai'] ?? 'Chưa phân loại') ?></p>
                                </div>
                                <div class="col-sm-4">
                                    <p class="text-muted small mb-1">Đơn vị tính</p>
                                    <p class="fw-bold"><?= htmlspecialchars($product['dvt'] ?? '—') ?></p>
                                </div>

                                <div class="col-sm-4">
                                    <p class="text-muted small mb-1">% Lợi nhuận</p>
                                    <p class="fw-bold text-success"><?= $product['phan_tram_loi_nhuan'] ?? 30 ?>%</p>
                                </div>

                                <div class="col-sm-4">
                                    <p class="text-muted small mb-1">Trạng thái bán</p>
                                    <p class="fw-bold">
                                        <?php if ($product['duoc_phep_ban']): ?>
                                            <span class="badge bg-success">Đang bán</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Ngừng bán</span>
                                        <?php endif; ?>
                                    </p>
                                </div>

                                <div class="col-sm-4">
                                    <p class="text-muted small mb-1">Loại hàng</p>
                                    <p class="fw-bold">
                                        <?php if ($product['la_hang_sx']): ?>
                                            <span class="badge bg-info text-dark">Hàng tự sản xuất</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Hàng mua về</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- ===== DANH SÁCH LÔ ===== -->
            <div class="card card-outline card-secondary shadow-sm">

                <div class="card-header d-flex flex-column bg-light">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <h3 class="card-title fw-bold">Các lô hàng hiện có trong kho</h3>
                        <span class="badge bg-primary"><?= count($lots ?? []) ?> lô</span>
                    </div>

                    <div class="d-flex gap-3 mt-2">
                        <span class="text-dark fw-bold">
                            <i class="bi bi-calculator"></i>
                            Giá vốn bình quân: <?= number_format($average_cost, 0, ',', '.') ?>đ
                        </span>
                        <span class="text-success fw-bold">
                            <i class="bi bi-tag"></i>
                            Giá bán hiện tại: <?= number_format($suggested_price, 0, ',', '.') ?>đ
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-hover text-center align-middle">

                        <thead class="table-dark">
                            <tr>
                                <th>Mã lô</th>
                                <th>Ngày nhập</th>
                                <th>Nhà cung cấp</th>
                                <th>HSD</th>
                                <th>SL nhập</th>
                                <th>Tồn</th>
                                <th>Giá vốn</th>
                                <th>Khuyến mãi</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php if (!empty($lots)): foreach ($lots as $row):
                            $hsd_ts   = strtotime($row['hsd_lo']);
                            $daysLeft = ceil(($hsd_ts - time()) / 86400);
                            $hsdCls   = ($daysLeft <= 7)  ? 'text-danger fw-bold'
                                      : ($daysLeft <= 30  ? 'text-warning fw-bold' : '');

                            $badgeMap = [
                                'TTL01' => 'bg-success',
                                'TTL02' => 'bg-warning text-dark',
                                'TTL03' => 'bg-secondary',
                                'TTL04' => 'bg-danger',
                                'TTL05' => 'bg-dark'
                            ];
                            $badgeCls = $badgeMap[$row['id_trang_thai_lo']] ?? 'bg-light';
                        ?>
                        <tr>
                            <td><code><?= $row['id_lo'] ?></code></td>

                            <td>
                                <?= !empty($row['ngay_lap_phieu_nhap'])
                                    ? date('d/m/Y', strtotime($row['ngay_lap_phieu_nhap']))
                                    : '<span class="text-muted">—</span>' ?>
                            </td>

                            <td class="text-start"><?= htmlspecialchars($row['ten_ncc'] ?? '—') ?></td>

                            <td class="<?= $hsdCls ?>">
                                <?= date('d/m/Y', $hsd_ts) ?>
                                <?php if ($daysLeft <= 30): ?>
                                    <br><small>(<?= $daysLeft < 0 ? 'Đã hết hạn' : 'còn ' . $daysLeft . ' ngày' ?>)</small>
                                <?php endif; ?>
                            </td>

                            <td><?= number_format($row['so_luong_nhap']) ?></td>

                            <td class="fw-bold <?= ($row['so_luong_con_lai'] <= 5) ? 'text-danger' : 'text-primary' ?>">
                                <?= number_format($row['so_luong_con_lai']) ?>
                            </td>

                            <td><?= number_format($row['gia_von_nhap'], 0, ',', '.') ?>đ</td>

                            <td>
                                <?= !empty($row['ten_km'])
                                    ? '<span class="badge bg-warning text-dark">' . htmlspecialchars($row['ten_km']) . ' (-' . $row['phan_tram_km'] . '%)</span>'
                                    : '—' ?>
                            </td>

                            <td>
                                <span class="badge <?= $badgeCls ?>">
                                    <?= $row['ten_trang_thai_lo'] ?>
                                </span>
                            </td>

                            <td>
                                <button type="button" class="btn btn-sm btn-warning btn-edit-batch"
                                    data-id-lo="<?= $row['id_lo'] ?>"
                                    data-hsd="<?= date('Y-m-d\TH:i', $hsd_ts) ?>"
                                    data-stock="<?= $row['so_luong_con_lai'] ?>"
                                    data-status="<?= $row['id_trang_thai_lo'] ?>">
                                    <i class="bi bi-pencil-square"></i> Sửa
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">Chưa có lô hàng</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>

                    </table>
                </div>
            </div>

        </div>
    </div>
</main>

<!-- ====== MODAL SỬA LÔ ====== -->
<div class="modal fade" id="modalEditBatch" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= BASE_PATH ?>/admin/inventories/updateBatch" method="POST">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold">Cập nhật lô: <span id="display-id-lo"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_lo" id="input-id-lo">
                    <input type="hidden" name="id_hh" value="<?= $product['id_hh'] ?? '' ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Hạn sử dụng</label>
                        <input type="datetime-local" name="hsd_lo" id="input-hsd" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Số lượng tồn kho (còn lại)</label>
                        <input type="number" name="so_luong_con_lai" id="input-stock" class="form-control" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Trạng thái lô hàng</label>
                        <select name="id_trang_thai_lo" id="input-status" class="form-select">
                            <option value="TTL01">Còn hàng (Đang bán)</option>
                            <option value="TTL02">Sắp hết</option>
                            <option value="TTL03">Hết hàng</option>
                            <option value="TTL04">Sắp hết hạn</option>
                            <option value="TTL05">Hết hạn / Đã hủy</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-warning fw-bold">Cập nhật thay đổi</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const editModal = new bootstrap.Modal(document.getElementById('modalEditBatch'));
    document.querySelectorAll('.btn-edit-batch').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('display-id-lo').innerText = this.dataset.idLo;
            document.getElementById('input-id-lo').value        = this.dataset.idLo;
            document.getElementById('input-hsd').value          = this.dataset.hsd;
            document.getElementById('input-stock').value        = this.dataset.stock;
            document.getElementById('input-status').value       = this.dataset.status;
            editModal.show();
        });
    });
});
</script>
