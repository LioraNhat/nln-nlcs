<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">Quản lý Nhà cung cấp</h3></div>
                <div class="col-sm-6 text-end">
                    <a href="<?= BASE_PATH; ?>/admin/suppliers/create" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Thêm mới
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            
            <div class="card mb-4 card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-funnel"></i> Bộ lọc dữ liệu</h3>
                </div>
                <div class="card-body">
                    <form action="" method="GET">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tìm kiếm chung</label>
                                <input type="text" name="search" class="form-control" placeholder="Tên, mã hoặc email..." value="<?= htmlspecialchars($searchKeyword ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Số điện thoại</label>
                                <input type="text" name="phone" class="form-control" placeholder="Nhập số điện thoại..." value="<?= htmlspecialchars($phoneKeyword ?? '') ?>">
                            </div>
                            <div class="col-md-5 d-flex align-items-end">
                                <button type="submit" class="btn btn-info text-white me-2"><i class="bi bi-search"></i> Lọc dữ liệu</button>
                                <a href="<?= BASE_PATH ?>/admin/suppliers" class="btn btn-secondary"><i class="bi bi-arrow-clockwise"></i> Làm mới</a>
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
                    <h3 class="card-title">Danh sách Nhà cung cấp (Mới nhất lên đầu)</h3>
                </div>
                <div class="card-body">
                    <table id="table-suppliers" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Mã NCC</th>
                                <th>Tên Nhà cung cấp</th>
                                <th>Số điện thoại</th>
                                <th>Email</th>
                                <th>Địa chỉ</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($suppliers)): ?>
                                <?php foreach ($suppliers as $row): ?>
                                    <tr>
                                        <td><span class="badge bg-light text-dark border"><?= $row['id_ncc'] ?></span></td>
                                        <td class="fw-bold text-primary"><?= htmlspecialchars($row['ten_ncc'] ?? '') ?></td>
                                        <td><i class="bi bi-telephone"></i> <?= htmlspecialchars($row['sdt_ncc'] ?? '') ?></td>
                                        <td><i class="bi bi-envelope"></i> <?= htmlspecialchars($row['email_ncc'] ?? '') ?></td>
                                        <td><small><?= htmlspecialchars($row['dia_chi_ncc'] ?? '') ?></small></td>
                                        <td align="center">
                                            <div class="dropdown">
                                                <button class="btn btn-flat btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Hành động
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="<?= BASE_PATH ?>/admin/suppliers/edit/<?= $row['id_ncc'] ?>"><i class="bi bi-pencil-square text-primary"></i> Sửa</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item" href="<?= BASE_PATH ?>/admin/suppliers/delete/<?= $row['id_ncc'] ?>" onclick="return confirm('Bạn chắc chắn muốn xóa? Thao tác này có thể thất bại nếu nhà cung cấp đã có lịch sử nhập hàng.');"><i class="bi bi-trash text-danger"></i> Xóa</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Không tìm thấy nhà cung cấp nào phù hợp.</td>
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
        $('#table-suppliers').DataTable({
            "order": [], // Giữ nguyên thứ tự sắp xếp từ PHP (DESC)
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Vietnamese.json"
            }
        }); 
    });
</script>