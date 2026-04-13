<?php 
// Tính toán giá vốn bình quân trước khi hiển thị
$total_quantity = 0;
$total_value = 0;
$average_cost = 0;

if (!empty($lots)) {
    foreach ($lots as $lot) {
        $qty = $lot['so_luong_con_lai'] ?? 0;
        $price = $lot['gia_von_nhap'] ?? 0;
        $total_quantity += $qty;
        $total_value += ($qty * $price);
    }
    if ($total_quantity > 0) {
        $average_cost = $total_value / $total_quantity;
    }
}
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="app-main">
    <div class="app-content">
        <div class="container-fluid">
            <?php if (!empty($success)): ?>
                <div class="alert alert-success mt-3"><?= $success ?></div>
            <?php endif; ?>

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
                            <img src="<?= $imgSrc ?>" alt="Product Image" 
                                 class="img-thumbnail shadow-sm" 
                                 style="max-height: 120px; width: auto; object-fit: contain;">
                        </div>
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-sm-4">
                                    <p class="mb-1 text-muted small">Mã sản phẩm</p>
                                    <p class="fw-bold mb-2"><?= $product['id_hh'] ?></p>
                                </div>
                                <div class="col-sm-4">
                                    <p class="mb-1 text-muted small">Tên sản phẩm</p>
                                    <p class="fw-bold mb-2"><?= htmlspecialchars($product['ten_hh']) ?></p>
                                </div>
                                <div class="col-sm-4">
                                    <p class="mb-1 text-muted small">Danh mục / Loại</p>
                                    <p class="fw-bold mb-2"><?= htmlspecialchars($product['ten_loai'] ?? 'Chưa phân loại') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-primary mb-3">
                <div class="card-header"><h3 class="card-title">Thêm lô hàng mới</h3></div>
                <div class="card-body">
                    <form action="<?= BASE_PATH ?>/admin/inventories/store" method="POST">
                        <input type="hidden" name="id_hh" value="<?= $product['id_hh'] ?>">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nhà cung cấp <span class="text-danger">*</span></label>
                                <select name="id_ncc" class="form-select shadow-sm" required>
                                    <option value="">-- Chọn NCC --</option>
                                    <?php foreach ($suppliers as $s): ?>
                                        <option value="<?= $s['id_ncc'] ?>"><?= htmlspecialchars($s['ten_ncc']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Khuyến mãi áp dụng</label>
                                <select name="id_km" class="form-select shadow-sm">
                                    <option value="">-- Không có --</option>
                                    <?php foreach ($promotions as $km): ?>
                                        <option value="<?= $km['id_km'] ?>"><?= htmlspecialchars($km['ten_km']) ?> (<?= $km['phan_tram_km'] ?>%)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Hạn sử dụng <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="hsd_lo" class="form-control shadow-sm" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Số lượng nhập <span class="text-danger">*</span></label>
                                <input type="number" name="so_luong" class="form-control shadow-sm" min="1" required placeholder="VD: 50">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Giá vốn nhập/đơn vị (VNĐ) <span class="text-danger">*</span></label>
                                <input type="number" name="don_gia" class="form-control shadow-sm" min="0" required placeholder="VD: 30000">
                            </div>
                        </div>

                        <div class="text-end border-top pt-3 mt-2">
                            <a href="<?= BASE_PATH ?>/admin/inventories" class="btn btn-secondary me-2">Quay lại</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-plus-circle"></i> Xác nhận nhập lô
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card-outline card-secondary shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <h3 class="card-title fw-bold">Các lô hàng hiện có trong kho</h3>
                    <div class="ms-auto">
                        <span class="badge bg-dark fs-6 py-2 px-3">
                            <i class="bi bi-calculator me-1"></i> Giá vốn bình quân: <?= number_format($average_cost, 0, ',', '.') ?>đ / Đơn vị
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-sm align-middle mb-0">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>Mã lô</th>
                                    <th>Hạn sử dụng</th>
                                    <th>Tồn kho</th>
                                    <th>Giá vốn nhập</th>
                                    <th>Giá bán hiện tại</th>
                                    <th>Khuyến mãi</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($lots)): foreach ($lots as $lot): ?>
                                <tr>
                                    <td class="text-center font-monospace small"><?= $lot['id_lo'] ?></td>
                                    <td class="text-center"><?= date('d/m/Y', strtotime($lot['hsd_lo'])) ?></td>
                                    <td class="text-center fw-bold text-primary"><?= number_format($lot['so_luong_con_lai']) ?></td>
                                    <td class="text-end pe-3"><?= number_format($lot['gia_von_nhap'], 0, ',', '.') ?>đ</td>
                                    <td class="text-end pe-3 fw-bold text-success"><?= $lot['gia_hien_tai'] ? number_format($lot['gia_hien_tai'], 0, ',', '.') . 'đ' : '<span class="text-muted small italic">Chờ định giá</span>' ?></td>
                                    <td class="text-center">
                                        <?php if($lot['ten_km']): ?>
                                            <span class="text-danger small fw-bold"><?= $lot['ten_km'] ?> (-<?= $lot['phan_tram_km'] ?>%)</span>
                                        <?php else: ?>
                                            <span class="text-muted small">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                            $badgeClass = 'bg-success';
                                            if($lot['id_trang_thai_lo'] == 'TTL02') $badgeClass = 'bg-warning text-dark';
                                            if($lot['id_trang_thai_lo'] == 'TTL03') $badgeClass = 'bg-danger';
                                            if($lot['id_trang_thai_lo'] == 'TTL04') $badgeClass = 'bg-info text-dark';
                                        ?>
                                        <span class="badge <?= $badgeClass ?>" style="min-width: 90px;"><?= $lot['ten_trang_thai_lo'] ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="7" class="text-center text-muted py-4"><i class="bi bi-box-seam d-block fs-2 mb-2"></i> Sản phẩm này chưa có lô hàng nào được nhập.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>