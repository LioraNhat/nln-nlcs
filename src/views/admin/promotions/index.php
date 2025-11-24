<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">Quản lý Khuyến mãi</h3></div>
                <div class="col-sm-6">
                    <div class="float-sm-end">
                        <a href="<?php echo BASE_PATH; ?>/admin/promotions/create" class="btn btn-primary">
                            <i class="bi bi-plus-lg"></i> Thêm mới
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
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show"><?= $error ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>

            <div class="card card-outline card-primary">
                <div class="card-body">
                    <table id="table-promotions" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Mã KM</th>
                                <th>Tên chương trình</th>
                                <th>Giảm giá</th>
                                <th>Thời gian áp dụng</th>
                                <th>Trạng thái</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($promotions)): ?>
                                <?php foreach ($promotions as $row): ?>
                                    <tr>
                                        <td><?= $row['ID_KM'] ?></td>
                                        <td><?= htmlspecialchars($row['TEN_KM']) ?></td>
                                        <td class="fw-bold text-danger">-<?= number_format($row['PHAN_TRAM_KM'], 0) ?>%</td>
                                        <td>
                                            <small>
                                                BĐ: <?= date('d/m/Y H:i', strtotime($row['NGAY_BD_KM'])) ?> <br>
                                                KT: <?= date('d/m/Y H:i', strtotime($row['NGAY_KT_KM'])) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php 
                                            $statusClass = 'bg-secondary';
                                            if ($row['TRANG_THAI_KM'] == 'Đang diễn ra') $statusClass = 'bg-success';
                                            elseif ($row['TRANG_THAI_KM'] == 'Sắp diễn ra') $statusClass = 'bg-warning text-dark';
                                            elseif ($row['TRANG_THAI_KM'] == 'Đã hủy') $statusClass = 'bg-danger';
                                            ?>
                                            <span class="badge <?= $statusClass ?>"><?= $row['TRANG_THAI_KM'] ?></span>
                                        </td>
                                        <td align="center">
                                            <div class="dropdown">
                                                <button class="btn btn-flat btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">Hành động</button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="<?= BASE_PATH ?>/admin/promotions/edit/<?= $row['ID_KM'] ?>"><i class="bi bi-pencil-square text-primary"></i> Sửa</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item" href="<?= BASE_PATH ?>/admin/promotions/delete/<?= $row['ID_KM'] ?>" onclick="return confirm('Bạn chắc chắn muốn xóa?');"><i class="bi bi-trash text-danger"></i> Xóa</a></li>
                                                </ul>
                                            </div>
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
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<script>
    $(document).ready(function() { $('#table-promotions').DataTable(); });
</script>