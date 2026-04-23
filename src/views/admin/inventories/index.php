<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">Quản lý tồn kho</h3></div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">

            <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= $success ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= $error ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- FILTER -->
            <div class="card card-outline card-secondary mb-3">
                <div class="card-body">
                    <form method="GET">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" placeholder="Tìm theo tên sản phẩm..."
                                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button class="btn btn-primary w-100">Lọc</button>
                                <a href="<?= BASE_PATH ?>/admin/inventories" class="btn btn-secondary w-100">Xóa</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- TABLE -->
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-dark text-center">
                            <tr>
                                <th style="width:90px">Mã HH</th>
                                <th class="text-start">Tên hàng hóa</th>
                                <th>Loại</th>
                                <th>Số lô</th>
                                <th>HSD gần nhất</th>
                                <th>Tồn kho</th>
                                <th>Giá bán hiện tại</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($products as $row): ?>
                        <?php
                            $gia = isset($row['gia_hien_tai']) ? (float)$row['gia_hien_tai'] : null;
                            $giaZero = ($gia !== null && $gia === 0.0);
                        ?>
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-secondary"><?= $row['id_hh'] ?></span>
                            </td>

                            <td class="fw-bold">
                                <?= htmlspecialchars($row['ten_hh']) ?>
                            </td>

                            <td class="text-center">
                                <span class="badge bg-info-subtle text-info border border-info-subtle">
                                    <?= htmlspecialchars($row['ten_loai'] ?? '—') ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <button type="button" class="btn btn-outline-primary btn-sm btn-view-batches"
                                        data-id-hh="<?= $row['id_hh'] ?>"
                                        data-name="<?= htmlspecialchars($row['ten_hh']) ?>">
                                    <i class="bi bi-stack"></i> <?= $row['so_luong_lo'] ?> lô
                                </button>
                            </td>

                            <td class="text-center">
                                <?php if ($row['hsd_gan_nhat']): ?>
                                    <?php
                                        $daysLeft = ceil((strtotime($row['hsd_gan_nhat']) - time()) / 86400);
                                        $cls = $daysLeft <= 7 ? 'text-danger fw-bold' : ($daysLeft <= 30 ? 'text-warning fw-bold' : '');
                                    ?>
                                    <span class="<?= $cls ?>">
                                        <?= date('d/m/Y', strtotime($row['hsd_gan_nhat'])) ?>
                                        <?php if ($daysLeft <= 30): ?>
                                            <br><small>(còn <?= $daysLeft ?> ngày)</small>
                                        <?php endif; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>

                            <td class="text-center fw-bold <?= ((int)$row['tong_ton_kho'] <= 10) ? 'text-danger' : 'text-primary' ?>">
                                <?= number_format($row['tong_ton_kho'] ?? 0) ?> <?= $row['dvt'] ?>
                            </td>

                            <td class="text-end fw-bold">
                                <?php if ($giaZero): ?>
                                    <!-- ✅ Giá = 0 hiển thị cảnh báo -->
                                    <span class="badge bg-danger">
                                        <i class="bi bi-exclamation-triangle-fill"></i> Giá = 0đ
                                    </span>
                                <?php elseif ($gia === null): ?>
                                    <span class="text-muted">Chưa có giá</span>
                                <?php else: ?>
                                    <span class="text-success"><?= number_format($gia) ?>đ</span>
                                <?php endif; ?>
                            </td>

                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="<?= BASE_PATH ?>/admin/inventories/create?id_hh=<?= $row['id_hh'] ?>"
                                       class="btn btn-sm btn-success">
                                       <i class="bi bi-plus-circle"></i> Nhập lô
                                    </a>
                                    <a href="<?= BASE_PATH ?>/admin/inventories/detail/<?= $row['id_hh'] ?>"
                                       class="btn btn-sm btn-outline-secondary">
                                       <i class="bi bi-eye"></i> Chi tiết
                                    </a>
                                </div>
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

<!-- ====== MODAL XEM LÔ HÀNG ====== -->
<div class="modal fade" id="modalBatchList" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-primary text-white d-flex flex-column align-items-start">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h5 class="modal-title">Chi tiết lô: <span id="modal-product-name"></span></h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="d-flex gap-3 mt-2">
                    <span class="fw-bold text-white">
                        <i class="bi bi-calculator"></i> Giá vốn bình quân: <span id="modal-avg-cost">—</span>
                    </span>
                    <span class="fw-bold text-warning">
                        <i class="bi bi-tag"></i> Giá bán dự kiến: <span id="modal-suggested-price">—</span>
                    </span>
                </div>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>Mã lô</th>
                                <th>HSD</th>
                                <th>Tồn</th>
                                <th>Giá vốn</th>
                                <th>Khuyến mãi</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="batch-list-body">
                            <tr><td colspan="7" class="text-center text-muted py-3">Đang tải...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ====== MODAL SỬA LÔ (dùng cho cả Index lẫn Detail) ====== -->
<div class="modal fade" id="modalEditBatch" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= BASE_PATH ?>/admin/inventories/update-batch" method="POST">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold">Cập nhật lô: <span id="display-id-lo"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_lo" id="input-id-lo">
                    <input type="hidden" name="id_hh" id="input-id-hh">

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
// ✅ Hàm openEditModal dùng chung cho cả Index và Detail
function openEditModal(idLo, hsdLo, stock, status, idHh) {
    document.getElementById('display-id-lo').innerText = idLo;
    document.getElementById('input-id-lo').value        = idLo;
    document.getElementById('input-id-hh').value        = idHh ?? '';  // ← thêm
    const hsdFormatted = hsdLo.replace(' ', 'T').substring(0, 16);
    document.getElementById('input-hsd').value    = hsdFormatted;
    document.getElementById('input-stock').value  = stock;
    document.getElementById('input-status').value = status;
    new bootstrap.Modal(document.getElementById('modalEditBatch')).show();
}

// Xem danh sách lô theo sản phẩm (modal)
document.querySelectorAll('.btn-view-batches').forEach(btn => {
    btn.addEventListener('click', function () {
        const idHH = this.dataset.idHh;
        document.getElementById('modal-product-name').innerText = this.dataset.name;

        const tbody = document.getElementById('batch-list-body');
        tbody.innerHTML = '<tr><td colspan="11" class="text-center py-3"><div class="spinner-border spinner-border-sm"></div> Đang tải...</td></tr>';

        new bootstrap.Modal(document.getElementById('modalBatchList')).show();

        fetch(`<?= BASE_PATH ?>/admin/inventories/get-batches-json?id_hh=${idHH}`)
            .then(res => res.json())
            .then(data => {
                if (!data.length) {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-3">Chưa có lô hàng nào.</td></tr>';
                    document.getElementById('modal-avg-cost').innerText = '—';
                    document.getElementById('modal-suggested-price').innerText = '—';
                    return;
                }

                // Tính WAC: Σ(so_luong_nhap × gia_von_nhap) / Σ(so_luong_nhap) — chỉ lô còn tồn
                let totalQty = 0, totalValue = 0, margin = 30;
                data.forEach(item => {
                    if (item.so_luong_con_lai > 0) {
                        const qty = parseFloat(item.so_luong_nhap) || 0;
                        const price = parseFloat(item.gia_von_nhap) || 0;
                        totalQty  += qty;
                        totalValue += qty * price;
                    }
                    // Lấy margin từ item nếu có (cần trả về từ API)
                    if (item.phan_tram_loi_nhuan) margin = parseFloat(item.phan_tram_loi_nhuan);
                });

                const avgCost = totalQty > 0 ? totalValue / totalQty : 0;
                const suggestedPrice = avgCost * (1 + margin / 100);

                document.getElementById('modal-avg-cost').innerText =
                    avgCost.toLocaleString('vi-VN', {maximumFractionDigits: 0}) + 'đ';
                document.getElementById('modal-suggested-price').innerText =
                    suggestedPrice.toLocaleString('vi-VN', {maximumFractionDigits: 0}) + 'đ';

                const badgeMap = {
                    TTL01: 'bg-success', TTL02: 'bg-warning text-dark',
                    TTL03: 'bg-secondary', TTL04: 'bg-danger', TTL05: 'bg-dark'
                };

                let html = '';
                data.forEach(item => {
                    const badgeCls = badgeMap[item.id_trang_thai_lo] ?? 'bg-light';
                    const kmHtml = item.ten_km
                        ? `<span class="badge bg-warning text-dark">-${item.phan_tram_km}%</span>`
                        : '—';

                    html += `
                        <tr class="text-center">
                            <td><code>${item.id_lo}</code></td>
                            <td>${item.hsd_f}</td>
                            <td class="fw-bold ${item.so_luong_con_lai <= 5 ? 'text-danger' : 'text-primary'}">
                                ${Number(item.so_luong_con_lai).toLocaleString('vi-VN')}
                            </td>
                            <td>${item.gia_von_nhap ? Number(item.gia_von_nhap).toLocaleString('vi-VN') + 'đ' : '—'}</td>
                            <td>${kmHtml}</td>
                            <td><span class="badge ${badgeCls}">${item.ten_trang_thai_lo}</span></td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-xs btn-warning"
                                        onclick="openEditModal('${item.id_lo}', '${item.hsd_lo}', ${item.so_luong_con_lai}, '${item.id_trang_thai_lo}', '${idHH}')"
                                        <i class="bi bi-pencil"></i> Sửa
                                    </button>
                                    <a href="<?= BASE_PATH ?>/admin/inventories/delete-batch?id_lo=${item.id_lo}&id_hh=${idHH}"
                                        class="btn btn-xs btn-danger"
                                        onclick="return confirm('Xác nhận xóa lô ${item.id_lo}?')">
                                        <i class="bi bi-trash"></i> Xóa
                                    </a>
                                </div>
                            </td>
                        </tr>`;
                });
                tbody.innerHTML = html;
            })
            .catch(() => {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger py-3">Lỗi khi tải dữ liệu.</td></tr>';
            });
    });
});
</script>
