<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">Chi tiết Phiếu nhập: <?= $slip['ID_PN'] ?></h3></div>
                <div class="col-sm-6 text-end">
                    <a href="<?= BASE_PATH ?>/admin/inventories" class="btn btn-secondary">Quay lại</a>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Nhà cung cấp:</strong> <br> <?= $slip['TEN_NCC'] ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Ngày nhập:</strong> <br> <?= date('d/m/Y H:i', strtotime($slip['NGAY_LAP_PHIEU_NHAP'])) ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Chứng từ gốc:</strong> <br> <?= $slip['CHUNG_TU_GOC'] ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-primary text-white">Danh sách sản phẩm</div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Mã SP</th>
                                <th>Tên sản phẩm</th>
                                <th>ĐVT</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Đơn giá</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($details as $item): ?>
                                <tr>
                                    <td><?= $item['ID_HH'] ?></td>
                                    <td><?= $item['TEN_HH'] ?></td>
                                    <td><?= $item['DVT'] ?></td>
                                    <td class="text-center fw-bold"><?= $item['SO_LUONG_NHAP'] ?></td>
                                    <td class="text-end"><?= number_format($item['DON_GIA_NHAP']) ?></td>
                                    <td class="text-end fw-bold"><?= number_format($item['SO_LUONG_NHAP'] * $item['DON_GIA_NHAP']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="5" class="text-end fw-bold">TỔNG CỘNG:</td>
                                <td class="text-end fw-bold text-danger fs-5"><?= number_format($slip['TONG_GIA_TRI_PHIEU_NHAP']) ?> đ</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>