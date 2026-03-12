<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">Nhập lô mới</h3></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="<?= BASE_PATH ?>/admin/inventories">Tồn kho</a></li>
                        <li class="breadcrumb-item active">Nhập lô</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">

            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <!-- THÔNG TIN SẢN PHẨM -->
            <div class="card card-outline card-info mb-3">
                <div class="card-header"><h3 class="card-title">Thông tin sản phẩm</h3></div>
                <div class="card-body">
                    <p><strong>Mã:</strong> <?= $product['id_hh'] ?></p>
                    <p><strong>Tên:</strong> <?= htmlspecialchars($product['ten_hh']) ?></p>
                    <p><strong>Loại:</strong> <?= htmlspecialchars($product['ten_loai'] ?? '') ?></p>
                </div>
            </div>

            <!-- FORM NHẬP LÔ MỚI -->
            <div class="card card-outline card-primary mb-3">
                <div class="card-header"><h3 class="card-title">Thêm lô hàng mới</h3></div>
                <div class="card-body">
                    <form action="<?= BASE_PATH ?>/admin/inventories/store" method="POST">
                        <input type="hidden" name="id_hh" value="<?= $product['id_hh'] ?>">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nhà cung cấp <span class="text-danger">*</span></label>
                                <select name="id_ncc" class="form-select" required>
                                    <option value="">-- Chọn NCC --</option>
                                    <?php foreach ($suppliers as $s): ?>
                                        <option value="<?= $s['id_ncc'] ?>"><?= htmlspecialchars($s['ten_ncc']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Khuyến mãi áp dụng</label>
                                <select name="id_km" class="form-select">
                                    <option value="">-- Không có --</option>
                                    <?php foreach ($promotions as $km): ?>
                                        <option value="<?= $km['id_km'] ?>"><?= htmlspecialchars($km['ten_km']) ?> (<?= $km['phan_tram_km'] ?>%)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Hạn sử dụng <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="hsd_lo" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Số lượng nhập <span class="text-danger">*</span></label>
                                <input type="number" name="so_luong" class="form-control" min="1" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Giá vốn nhập (VNĐ) <span class="text-danger">*</span></label>
                                <input type="number" name="don_gia" class="form-control" min="0" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Giá bán (VNĐ) <span class="text-danger">*</span></label>
                                <input type="number" name="gia_ban" class="form-control" min="0" required>
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="<?= BASE_PATH ?>/admin/inventories" class="btn btn-secondary me-2">Hủy</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Lưu lô hàng
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- DANH SÁCH LÔ HIỆN CÓ -->
            <div class="card card-outline card-secondary">
                <div class="card-header"><h3 class="card-title">Các lô hàng hiện có</h3></div>
                <div class="card-body">
                    <table class="table table-bordered table-sm align-middle">
                        <thead>
                            <tr class="text-center">
                                <th>Mã lô</th>
                                <th>HSD</th>
                                <th>Tồn</th>
                                <th>Giá vốn</th>
                                <th>Giá bán</th>
                                <th>KM</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($lots)): foreach ($lots as $lot): ?>
                            <tr>
                                <td><?= $lot['id_lo'] ?></td>
                                <td class="text-center"><?= date('d/m/Y', strtotime($lot['hsd_lo'])) ?></td>
                                <td class="text-center"><?= $lot['so_luong_con_lai'] ?></td>
                                <td class="text-end"><?= $lot['gia_von_nhap'] ? number_format($lot['gia_von_nhap'],0,',','.').'đ' : '—' ?></td>
                                <td class="text-end"><?= $lot['gia_hien_tai'] ? number_format($lot['gia_hien_tai'],0,',','.').'đ' : '—' ?></td>
                                <td class="text-center"><?= $lot['ten_km'] ? $lot['ten_km'].' ('.$lot['phan_tram_km'].'%)' : '—' ?></td>
                                <td class="text-center"><span class="badge bg-info"><?= $lot['ten_trang_thai_lo'] ?></span></td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="7" class="text-center text-muted">Chưa có lô nào.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>