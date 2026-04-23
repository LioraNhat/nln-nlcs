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

// Giá bán dự kiến
$suggested_price = $average_cost * (1 + ($margin / 100));
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="app-main">
    <div class="app-content">
        <div class="container-fluid">

            <?php if (!empty($success)): ?>
                <div class="alert alert-success mt-3"><?= $success ?></div>
            <?php endif; ?>

            <!-- ===== THÔNG TIN SẢN PHẨM ===== -->
            <div class="card card-outline card-info mb-3 mt-3">
                <div class="card-header">
                    <h3 class="card-title">Thông tin sản phẩm</h3>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <?php 
                                $imgSrc = (!empty($product['link_anh'])) 
                                    ? BASE_PATH . '/uploads/' . $product['link_anh'] 
                                    : BASE_PATH . '/admin_assets/assets/img/default-150x150.png'; 
                            ?>
                            <img src="<?= $imgSrc ?>" class="img-thumbnail shadow-sm" style="max-height:120px;object-fit:contain;">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== FORM NHẬP LÔ ===== -->
            <div class="card card-outline card-primary mb-3">
                <div class="card-header">
                    <h3 class="card-title">Thêm lô hàng mới</h3>
                </div>

                <div class="card-body">
                    <form action="<?= BASE_PATH ?>/admin/inventories/store" method="POST">
                        <input type="hidden" name="id_hh" value="<?= $product['id_hh'] ?>">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nhà cung cấp *</label>
                                <select name="id_ncc" class="form-select" required>
                                    <option value="">-- Chọn NCC --</option>
                                    <?php foreach ($suppliers as $s): ?>
                                        <option value="<?= $s['id_ncc'] ?>">
                                            <?= htmlspecialchars($s['ten_ncc']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Khuyến mãi</label>
                                <select name="id_km" class="form-select">
                                    <option value="">-- Không có --</option>
                                    <?php foreach ($promotions as $km): ?>
                                        <option value="<?= $km['id_km'] ?>">
                                            <?= htmlspecialchars($km['ten_km']) ?> (<?= $km['phan_tram_km'] ?>%)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Hạn sử dụng *</label>
                                <input type="datetime-local" name="hsd_lo" class="form-control" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Số lượng *</label>
                                <input type="number" name="so_luong" class="form-control" min="1" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Giá vốn *</label>
                                <input type="number" name="don_gia" class="form-control" min="0" required>
                            </div>
                        </div>

                        <div class="text-end">
                            <button class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Nhập lô
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ===== DANH SÁCH LÔ ===== -->
            <div class="card card-outline card-secondary shadow-sm">

                <div class="card-header d-flex flex-column bg-light">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <h3 class="card-title fw-bold">Các lô hàng hiện có trong kho</h3>
                    </div>

                    <div class="d-flex gap-3 mt-2">
                        <span class="text-dark fw-bold">
                            <i class="bi bi-calculator"></i> 
                            Giá vốn bình quân: <?= number_format($average_cost, 0, ',', '.') ?>đ
                        </span>

                        <span class="text-success fw-bold">
                            <i class="bi bi-tag"></i> 
                            Giá bán dự kiến: <?= number_format($suggested_price, 0, ',', '.') ?>đ
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-hover text-center align-middle">
                        
                        <!-- THEAD ĐÃ SỬA -->
                        <thead class="table-dark">
                            <tr>
                                <th>Mã lô</th>
                                <th>HSD</th>
                                <th>Tồn</th>
                                <th>Giá vốn</th>
                                <th>Khuyến mãi</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>

                        <!-- TBODY ĐÃ SỬA -->
                        <tbody>
                        <?php if (!empty($lots)): foreach ($lots as $lot): ?>
                        <tr>
                            <td><?= $lot['id_lo'] ?></td>
                            <td><?= date('d/m/Y', strtotime($lot['hsd_lo'])) ?></td>
                            <td class="fw-bold text-primary"><?= number_format($lot['so_luong_con_lai']) ?></td>
                            <td><?= number_format($lot['gia_von_nhap'], 0, ',', '.') ?>đ</td>

                            <td>
                                <?= $lot['ten_km'] 
                                    ? '<span class="badge bg-warning text-dark">-' . $lot['phan_tram_km'] . '%</span>' 
                                    : '—' ?>
                            </td>

                            <td>
                                <span class="badge bg-success">
                                    <?= $lot['ten_trang_thai_lo'] ?>
                                </span>
                            </td>
                            <!-- THÊM CỘT NÀY -->
                            <td>
                                <button type="button" class="btn btn-sm btn-warning btn-edit-batch"
                                    data-id-lo="<?= $lot['id_lo'] ?>"
                                    data-hsd="<?= date('Y-m-d\TH:i', strtotime($lot['hsd_lo'])) ?>"
                                    data-stock="<?= $lot['so_luong_con_lai'] ?>"
                                    data-status="<?= $lot['id_trang_thai_lo'] ?>">
                                    <i class="bi bi-pencil-square"></i> Sửa
                                </button>
                            </td>
                        </tr>

                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Chưa có lô hàng
                                </td>
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
<div class="modal fade" id="modalEditBatch" tabindex="-1"></div>
    <div class="modal-dialog">
        <form action="<?= BASE_PATH ?>/admin/inventories/updateBatch" method="POST">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold">Cập nhật lô: <span id="display-id-lo"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_lo" id="input-id-lo">
                    <!-- QUAN TRỌNG: truyền id_hh để redirect đúng trang -->
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const editModal = new bootstrap.Modal(document.getElementById('modalEditBatch'));
    document.querySelectorAll('.btn-edit-batch').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('display-id-lo').innerText = this.dataset.idLo;
            document.getElementById('input-id-lo').value       = this.dataset.idLo;
            document.getElementById('input-hsd').value         = this.dataset.hsd;
            document.getElementById('input-stock').value       = this.dataset.stock;
            document.getElementById('input-status').value      = this.dataset.status;
            editModal.show();
        });
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>