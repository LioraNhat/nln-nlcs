<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h3 class="mb-0">Chi tiết lô hàng</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?= BASE_PATH ?>/admin/inventories">Quản lý tồn kho</a></li>
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
                    <?= $_SESSION['success'] ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <?php if (!empty($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= $_SESSION['error'] ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- ✅ SỬA: dùng $product thay vì $products[0] -->
            <?php if (!empty($product)): ?>
            <div class="card card-outline card-info shadow-sm mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-auto">
                            <?php if (!empty($product['link_anh'])): ?>
                            <img src="<?= BASE_PATH ?>/uploads/<?= htmlspecialchars($product['link_anh']) ?>"
                                 class="rounded border shadow-sm"
                                 style="width:80px;height:80px;object-fit:cover;">
                            <?php else: ?>
                            <div class="rounded border bg-light d-flex align-items-center justify-content-center"
                                 style="width:80px;height:80px;">
                                <i class="bi bi-box fs-2 text-muted"></i>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="col">
                            <h4 class="fw-bold mb-1"><?= htmlspecialchars($product['ten_hh']) ?></h4>
                            <p class="text-muted mb-0">
                                Mã SP: <strong><?= $product['id_hh'] ?></strong>
                                <?php if (!empty($product['ten_loai'])): ?>
                                    | Loại: <strong><?= htmlspecialchars($product['ten_loai']) ?></strong>
                                <?php endif; ?>
                                <?php if (!empty($product['dvt'])): ?>
                                    | ĐVT: <strong><?= htmlspecialchars($product['dvt']) ?></strong>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-auto text-end">
                            <div class="text-muted small">Tổng tồn kho</div>
                            <?php $tongTon = array_sum(array_column($lots ?? [], 'so_luong_con_lai')); ?>
                            <h2 class="fw-bold text-primary mb-0"><?= number_format($tongTon) ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- ✅ SỬA: dùng $lots thay vì $products -->
            <div class="card card-outline card-primary shadow">
                <div class="card-header">
                    <h3 class="card-title">Lịch sử nhập & Trạng thái các lô</h3>
                    <div class="card-tools">
                        <span class="badge bg-primary"><?= count($lots ?? []) ?> lô</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center">Mã Lô</th>
                                    <th>Ngày nhập</th>
                                    <th>Nhà cung cấp</th>
                                    <th>Hạn sử dụng</th>
                                    <th class="text-center">SL nhập</th>
                                    <th class="text-center">Tồn</th>
                                    <th class="text-center">Trạng thái</th>
                                    <th class="text-end">Giá vốn nhập</th>
                                    <th class="text-end">Giá bán</th>
                                    <th>Khuyến mãi</th>
                                    <th class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($lots)): ?>
                                    <tr>
                                        <td colspan="11" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                            Chưa có lô hàng nào.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                <?php foreach ($lots as $row): ?>
                                <?php
                                    $hsd_ts   = strtotime($row['hsd_lo']);
                                    $daysLeft = ceil(($hsd_ts - time()) / 86400);
                                    $hsdCls   = ($daysLeft <= 7) ? 'text-danger fw-bold' : (($daysLeft <= 30) ? 'text-warning' : '');

                                    $gia       = isset($row['gia_hien_tai']) ? (float)$row['gia_hien_tai'] : null;
                                    $giaZero   = ($gia !== null && $gia === 0.0);

                                    $badgeMap  = [
                                        'TTL01' => 'bg-success',
                                        'TTL02' => 'bg-warning text-dark',
                                        'TTL03' => 'bg-secondary',
                                        'TTL04' => 'bg-danger',
                                        'TTL05' => 'bg-dark'
                                    ];
                                    $badgeCls  = $badgeMap[$row['id_trang_thai_lo']] ?? 'bg-light';
                                ?>
                                    <tr>
                                        <td class="text-center">
                                            <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2">
                                                <?= $row['id_lo'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= $row['ngay_lap_phieu_nhap']
                                                ? date('d/m/Y', strtotime($row['ngay_lap_phieu_nhap']))
                                                : '<span class="text-muted">—</span>' ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($row['ten_ncc'] ?? '—') ?>
                                        </td>
                                        <td>
                                            <div class="<?= $hsdCls ?>">
                                                <i class="bi bi-calendar3"></i>
                                                <?= date('d/m/Y', $hsd_ts) ?>
                                                <br>
                                                <small>
                                                    <?php if ($daysLeft < 0): ?>
                                                        <span class="text-danger">(Đã hết hạn)</span>
                                                    <?php else: ?>
                                                        (Còn <?= $daysLeft ?> ngày)
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        </td>
                                        <td class="text-center"><?= number_format($row['so_luong_nhap'] ?? 0) ?></td>
                                        <td class="text-center">
                                            <span class="fw-bold fs-5 <?= ($row['so_luong_con_lai'] <= 5) ? 'text-danger' : 'text-success' ?>">
                                                <?= number_format($row['so_luong_con_lai']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge <?= $badgeCls ?>"><?= $row['ten_trang_thai_lo'] ?></span>
                                        </td>
                                        <td class="text-end fw-bold">
                                            <?= number_format($row['gia_von_nhap'] ?? 0) ?>đ
                                        </td>
                                        <!-- ✅ Cột giá bán: cảnh báo nếu = 0 -->
                                        <td class="text-end fw-bold">
                                            <?php if ($giaZero): ?>
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-exclamation-triangle-fill"></i> 0đ - Cần cập nhật
                                                </span>
                                            <?php elseif ($gia === null): ?>
                                                <span class="text-muted fst-italic">Chưa có giá</span>
                                            <?php else: ?>
                                                <span class="text-success"><?= number_format($gia) ?>đ</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($row['ten_km'])): ?>
                                                <span class="badge bg-warning text-dark">
                                                    <?= htmlspecialchars($row['ten_km']) ?>
                                                    (-<?= $row['phan_tram_km'] ?>%)
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-warning btn-edit-batch"
                                                data-id-lo="<?= $row['id_lo'] ?>"
                                                data-hsd="<?= date('Y-m-d\TH:i', $hsd_ts) ?>"
                                                data-stock="<?= $row['so_luong_con_lai'] ?>"
                                                data-status="<?= $row['id_trang_thai_lo'] ?>">
                                                <i class="bi bi-pencil-square"></i> Sửa
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<!-- ====== MODAL SỬA LÔ ====== -->
<div class="modal fade" id="modalEditBatch" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= BASE_PATH ?>/admin/inventories/update-batch" method="POST">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold">Cập nhật lô: <span id="display-id-lo"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_lo" id="input-id-lo">
                    <!-- ✅ Truyền id_hh để redirect về đúng trang sau khi update -->
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
