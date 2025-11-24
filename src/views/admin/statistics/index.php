<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">Báo cáo thống kê</h3></div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            
            <div class="card card-primary card-outline mb-4">
                <div class="card-header">
                    <h5 class="card-title">Bộ lọc dữ liệu</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= BASE_PATH ?>/admin/statistics">
                        <div class="row align-items-end">
                            <div class="form-group col-md-3 mb-3">
                                <label for="stat-type" class="fw-bold">Chọn loại thống kê:</label>
                                <select id="stat-type" name="stat_type" class="form-select" required>
                                    <option value="" disabled <?= empty($statType) ? 'selected' : '' ?>>-- Vui lòng chọn --</option>
                                    <option value="revenue" <?= $statType == 'revenue' ? 'selected' : '' ?>>Tổng doanh thu</option>
                                    <option value="best-selling" <?= $statType == 'best-selling' ? 'selected' : '' ?>>Sản phẩm bán chạy</option>
                                    <option value="orders" <?= $statType == 'orders' ? 'selected' : '' ?>>Tổng số đơn hàng</option>
                                    <option value="cancelled-orders" <?= $statType == 'cancelled-orders' ? 'selected' : '' ?>>Đơn hàng đã hủy</option>
                                    <option value="top-customers" <?= $statType == 'top-customers' ? 'selected' : '' ?>>Khách hàng VIP</option>
                                </select>
                            </div>

                            <div class="form-group col-md-3 mb-3">
                                <label for="date_start" class="fw-bold">Từ ngày:</label>
                                <input type="date" class="form-control" name="date_start" value="<?= $dateStart ?>" required>
                            </div>

                            <div class="form-group col-md-3 mb-3">
                                <label for="date_end" class="fw-bold">Đến ngày:</label>
                                <input type="date" class="form-control" name="date_end" value="<?= $dateEnd ?>" required>
                            </div>

                            <div class="form-group col-md-3 mb-3 d-flex gap-2">
                                <button class="btn btn-primary w-50" type="submit"><i class="bi bi-filter"></i> Xem</button>
                                <button class="btn btn-success w-50" type="button" id="printBTN"><i class="bi bi-printer"></i> In</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (!empty($chartData['labels'])): ?>
                <div id="printableArea">
                    <div class="text-center mb-4 d-none d-print-block">
                        <h3>BÁO CÁO THỐNG KÊ - NLN FOOD</h3>
                        <p>Từ ngày <?= date('d/m/Y', strtotime($dateStart)) ?> đến <?= date('d/m/Y', strtotime($dateEnd)) ?></p>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="card">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="card-title mb-0"><?= $chartTitle ?> (Biểu đồ)</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="statChart" style="min-height: 300px; max-height: 400px; width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="card-title mb-0">Dữ liệu chi tiết</h5>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-bordered table-striped text-center mb-0">
                                        <thead class="table-dark">
                                            <tr>
                                                <th width="10%">STT</th>
                                                <th>
                                                    <?= ($statType === 'revenue' || $statType === 'orders' || $statType === 'cancelled-orders') ? 'Thời gian' : 
                                                        ($statType === 'top-customers' ? 'Tên Khách hàng' : 'Tên Sản phẩm') ?>
                                                </th>
                                                <th>
                                                    <?= $statType === 'orders' ? 'Số đơn hàng' : 
                                                        ($statType === 'revenue' ? 'Doanh thu (VNĐ)' : 
                                                        ($statType === 'top-customers' ? 'Tổng chi tiêu (VNĐ)' : 
                                                        ($statType === 'cancelled-orders' ? 'Số đơn hủy' : 'Số lượng bán'))) ?>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($chartData['labels'] as $i => $label): ?>
                                                <tr>
                                                    <td><?= $i + 1 ?></td>
                                                    <td class="text-start px-4"><?= htmlspecialchars($label) ?></td>
                                                    <td class="fw-bold">
                                                        <?= ($statType === 'revenue' || $statType === 'top-customers') 
                                                            ? number_format($chartData['values'][$i]) . ' đ' 
                                                            : number_format($chartData['values'][$i]) ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <div class="alert alert-warning text-center">
                    <i class="bi bi-exclamation-triangle"></i> Không có dữ liệu nào trong khoảng thời gian này.
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // 1. Cấu hình Biểu đồ
    <?php if (!empty($chartData['labels'])): ?>
    const ctx = document.getElementById('statChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar', // Có thể đổi thành 'line' nếu muốn biểu đồ đường
        data: {
            labels: <?= json_encode($chartData['labels']) ?>,
            datasets: [{
                label: <?= json_encode($chartTitle) ?>,
                data: <?= json_encode($chartData['values']) ?>,
                backgroundColor: 'rgba(13, 110, 253, 0.7)', // Màu xanh Bootstrap
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            },
            plugins: {
                legend: { position: 'top' }
            }
        }
    });
    <?php endif; ?>

    // 2. Cấu hình Nút In
    document.getElementById('printBTN').addEventListener('click', function() {
        var printContents = document.getElementById('printableArea').innerHTML;
        var originalContents = document.body.innerHTML;

        // Tạo cửa sổ in đơn giản
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload(); // Load lại để phục hồi các sự kiện JS
    });
</script>