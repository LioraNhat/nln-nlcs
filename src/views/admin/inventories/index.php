<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">Quản lý Phiếu nhập kho</h3></div>
                <div class="col-sm-6">
                    <div class="float-sm-end">
                        <a href="<?php echo BASE_PATH; ?>/admin/inventories/create" class="btn btn-primary">
                            <i class="bi bi-plus-lg"></i> Nhập hàng mới
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show"><?= $success ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>

            <div class="card card-outline card-primary">
                <div class="card-body">
                    <table id="table-inventories" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Mã Phiếu</th>
                                <th>Nhà cung cấp</th>
                                <th>Ngày nhập</th>
                                <th>Tổng tiền</th>
                                <th>Chứng từ gốc</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($slips as $row): ?>
                                <tr>
                                    <td><a href="<?= BASE_PATH ?>/admin/inventories/detail/<?= $row['ID_PN'] ?>" class="fw-bold"><?= $row['ID_PN'] ?></a></td>
                                    <td><?= htmlspecialchars($row['TEN_NCC'] ?? 'N/A') ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($row['NGAY_LAP_PHIEU_NHAP'])) ?></td>
                                    <td class="fw-bold text-success"><?= number_format($row['TONG_GIA_TRI_PHIEU_NHAP'], 0) ?>đ</td>
                                    <td><?= htmlspecialchars($row['CHUNG_TU_GOC'] ?? '-') ?></td>
                                    <td align="center">
                                        <a href="<?= BASE_PATH ?>/admin/inventories/detail/<?= $row['ID_PN'] ?>" class="btn btn-sm btn-info text-white">
                                            <i class="bi bi-eye"></i> Xem
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
    $(document).ready(function() { $('#table-inventories').DataTable(); });
</script>