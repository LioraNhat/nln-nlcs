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
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-funnel"></i> Bộ lọc tìm kiếm</h3>
                </div>
                <div class="card-body">
                    <form action="" method="GET">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Từ khóa</label>
                                <input type="text" name="search" class="form-control" placeholder="Mã KM, tên chương trình..." value="<?= htmlspecialchars($searchKeyword ?? '') ?>">
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Trạng thái</label>
                                <select name="status" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <option value="Sắp diễn ra" <?= (isset($_GET['status']) && $_GET['status'] == 'Sắp diễn ra') ? 'selected' : '' ?>>Sắp diễn ra</option>
                                    <option value="Đang diễn ra" <?= (isset($_GET['status']) && $_GET['status'] == 'Đang diễn ra') ? 'selected' : '' ?>>Đang diễn ra</option>
                                    <option value="Đã kết thúc" <?= (isset($_GET['status']) && $_GET['status'] == 'Đã kết thúc') ? 'selected' : '' ?>>Đã kết thúc</option>
                                    <option value="Đã hủy" <?= (isset($_GET['status']) && $_GET['status'] == 'Đã hủy') ? 'selected' : '' ?>>Đã hủy</option>
                                </select>
                            </div>

                            <div class="col-md-5 d-flex align-items-end">
                                <button class="btn btn-primary me-2" type="submit">
                                    <i class="bi bi-search"></i> Lọc dữ liệu
                                </button>
                                <a href="<?= BASE_PATH ?>/admin/promotions" class="btn btn-secondary">
                                    <i class="bi bi-arrow-counterclockwise"></i> Làm mới
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show"><?= $_SESSION['success'] ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show"><?= $_SESSION['error'] ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Danh sách khuyến mãi (Mới nhất lên đầu)</h3>
                </div>
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
                                        <td class="fw-bold"><?= $row['id_km'] ?></td>
                                        <td><?= htmlspecialchars($row['ten_km'] ?? '') ?></td>
                                        <td class="fw-bold text-danger">-<?= number_format($row['phan_tram_km'] ?? 0, 0) ?>%</td>
                                        <td>
                                            <small>
                                                <i class="bi bi-calendar-check text-success"></i> <b>BĐ:</b> <?= !empty($row['ngay_bd_km']) ? date('d/m/Y H:i', strtotime($row['ngay_bd_km'])) : 'N/A' ?> <br>
                                                <i class="bi bi-calendar-x text-danger"></i> <b>KT:</b> <?= !empty($row['ngay_kt_km']) ? date('d/m/Y H:i', strtotime($row['ngay_kt_km'])) : 'N/A' ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php 
                                            $statusValue = $row['trang_thai_km'] ?? '';
                                            $statusClass = 'bg-secondary';
                                            
                                            if ($statusValue == 'Đang diễn ra') $statusClass = 'bg-success';
                                            elseif ($statusValue == 'Sắp diễn ra') $statusClass = 'bg-warning text-dark';
                                            elseif ($statusValue == 'Đã kết thúc') $statusClass = 'bg-dark';
                                            elseif ($statusValue == 'Đã hủy') $statusClass = 'bg-danger';
                                            ?>
                                            <span class="badge <?= $statusClass ?>"><?= $statusValue ?></span>
                                        </td>
                                        <td align="center">
                                            <div class="dropdown">
                                                <button class="btn btn-flat btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">Hành động</button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="<?= BASE_PATH ?>/admin/promotions/edit/<?= $row['id_km'] ?>"><i class="bi bi-pencil-square text-primary"></i> Sửa</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item" href="<?= BASE_PATH ?>/admin/promotions/delete/<?= $row['id_km'] ?>" onclick="return confirm('Bạn chắc chắn muốn xóa? Thao tác này sẽ gỡ KM khỏi các lô hàng liên quan.');"><i class="bi bi-trash text-danger"></i> Xóa</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Không tìm thấy dữ liệu khuyến mãi phù hợp.</td>
                                </tr>
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
    $(document).ready(function() { 
        $('#table-promotions').DataTable({
            "order": [], // Tắt tính năng tự sắp xếp của DataTable để giữ thứ tự từ Model (mới nhất lên đầu)
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Vietnamese.json"
            }
        }); 
    });
</script>