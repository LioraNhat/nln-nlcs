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

                            <div class="form-group col-md-2 mb-3" id="topLimitGroup" style="display: none;">
                                <label for="top-limit" class="fw-bold">Số lượng hiển thị:</label>
                                <select id="top-limit" name="limit" class="form-select">
                                    <option value="5" <?= (isset($limit) && $limit == 5) ? 'selected' : '' ?>>Top 5</option>
                                    <option value="10" <?= (isset($limit) && $limit == 10) ? 'selected' : '' ?>>Top 10</option>
                                    <option value="15" <?= (isset($limit) && $limit == 15) ? 'selected' : '' ?>>Top 15</option>
                                    <option value="20" <?= (isset($limit) && $limit == 20) ? 'selected' : '' ?>>Top 20</option>
                                </select>
                            </div>

                            <div class="form-group col-md-2 mb-3">
                                <label for="date_start" class="fw-bold">Từ ngày:</label>
                                <input type="date" class="form-control" name="date_start" value="<?= $dateStart ?>" required>
                            </div>

                            <div class="form-group col-md-2 mb-3">
                                <label for="date_end" class="fw-bold">Đến ngày:</label>
                                <input type="date" class="form-control" name="date_end" value="<?= $dateEnd ?>" required>
                            </div>

                            <div class="form-group col-md-3 mb-3 d-flex gap-2">
                                <button class="btn btn-primary w-50" type="submit"><i class="bi bi-filter"></i> Xem</button>
                                <!-- <button class="btn btn-success w-50" type="button" id="printBTN"><i class="bi bi-printer"></i> In</button> -->
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (!empty($chartData['labels'])): ?>
                <div id="printableArea">
                    <div class="text-center mb-4 d-none d-print-block">
                        <h2 style="text-transform: uppercase; font-weight: bold;">BÁO CÁO THỐNG KÊ - NLN FOOD</h2>
                        <h4><?= $chartTitle ?></h4>
                        <p style="font-size: 14px;">Từ ngày <strong><?= date('d/m/Y', strtotime($dateStart)) ?></strong> đến <strong><?= date('d/m/Y', strtotime($dateEnd)) ?></strong></p>
                        <hr style="border: 2px solid #000; width: 50%; margin: 20px auto;">
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="card">
                                <div class="card-header bg-dark text-white d-print-none">
                                    <h5 class="card-title mb-0"><?= $chartTitle ?> (Biểu đồ)</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="statChart" style="min-height: 300px; max-height: 400px; width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-secondary text-white d-print-none">
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

                    <!-- Phần chữ ký cho in -->
                    <div class="signature-section d-none d-print-block mt-5">
                        <div class="row">
                            <div class="col-6"></div>
                            <div class="col-6 text-center">
                                <p style="font-style: italic; margin-bottom: 5px;">
                                    Ngày <?= date('d') ?> tháng <?= date('m') ?> năm <?= date('Y') ?>
                                </p>
                                <p style="font-weight: bold; margin-bottom: 80px;">NGƯỜI LẬP</p>
                                <p style="font-weight: bold; text-decoration: underline;">
                                    <?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : '.............................' ?>
                                </p>
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

<style>
    @media print {
        /* Ẩn các phần không cần thiết khi in */
        .app-header, .app-sidebar, .app-content-header, 
        .card-primary, .btn, .d-print-none {
            display: none !important;
        }

        /* Hiển thị phần chỉ dành cho in */
        .d-none.d-print-block {
            display: block !important;
        }

        /* Định dạng cho trang in */
        body {
            background: white !important;
        }

        .app-main {
            margin: 0 !important;
            padding: 20px !important;
        }

        .card {
            border: 1px solid #000 !important;
            box-shadow: none !important;
            page-break-inside: avoid;
        }

        .table {
            font-size: 12px;
        }

        .table thead {
            background-color: #333 !important;
            color: white !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Đảm bảo biểu đồ được in */
        canvas {
            max-height: 350px !important;
            page-break-inside: avoid;
        }

        /* Định dạng chữ ký */
        .signature-section {
            page-break-inside: avoid;
            margin-top: 50px;
        }
    }

    /* Style cho phần chữ ký */
    .signature-section p {
        margin: 0;
        line-height: 1.5;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // 1. Hiển thị/Ẩn dropdown Top limit
    const statTypeSelect = document.getElementById('stat-type');
    const topLimitGroup = document.getElementById('topLimitGroup');

    function toggleTopLimit() {
        const selectedType = statTypeSelect.value;
        if (selectedType === 'best-selling' || selectedType === 'top-customers') {
            topLimitGroup.style.display = 'block';
        } else {
            topLimitGroup.style.display = 'none';
        }
    }

    statTypeSelect.addEventListener('change', toggleTopLimit);
    // Gọi khi trang load để hiển thị đúng
    toggleTopLimit();

    // 2. Cấu hình Biểu đồ
    <?php if (!empty($chartData['labels'])): ?>
    let chartInstance = null;
    const ctx = document.getElementById('statChart').getContext('2d');
    chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chartData['labels']) ?>,
            datasets: [{
                label: <?= json_encode($chartTitle) ?>,
                data: <?= json_encode($chartData['values']) ?>,
                backgroundColor: 'rgba(13, 110, 253, 0.7)',
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
            },
            animation: {
                duration: 750
            }
        }
    });
    <?php endif; ?>

    // 3. Cấu hình Nút In với biểu đồ
    document.getElementById('printBTN').addEventListener('click', function() {
        if (chartInstance) {
            // Chuyển biểu đồ thành hình ảnh trước khi in
            const chartImage = chartInstance.toBase64Image();
            const canvas = document.getElementById('statChart');
            
            // Lưu canvas gốc
            const originalCanvas = canvas.cloneNode(true);
            
            // Tạo thẻ img từ biểu đồ
            const img = document.createElement('img');
            img.src = chartImage;
            img.style.width = '100%';
            img.style.maxHeight = '400px';
            
            // Thay thế canvas bằng img
            canvas.parentNode.replaceChild(img, canvas);
            
            // In trang
            setTimeout(() => {
                window.print();
                
                // Khôi phục lại canvas sau khi in
                img.parentNode.replaceChild(originalCanvas, img);
                
                // Tạo lại biểu đồ
                const newCtx = originalCanvas.getContext('2d');
                chartInstance = new Chart(newCtx, {
                    type: 'bar',
                    data: {
                        labels: <?= json_encode($chartData['labels'] ?? []) ?>,
                        datasets: [{
                            label: <?= json_encode($chartTitle ?? '') ?>,
                            data: <?= json_encode($chartData['values'] ?? []) ?>,
                            backgroundColor: 'rgba(13, 110, 253, 0.7)',
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
            }, 500);
        } else {
            window.print();
        }
    });
</script>