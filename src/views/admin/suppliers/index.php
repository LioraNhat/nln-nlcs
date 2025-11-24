<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">Quản lý Nhà cung cấp</h3></div>
                <div class="col-sm-6">
                    <div class="float-sm-end">
                        <a href="<?php echo BASE_PATH; ?>/admin/suppliers/create" class="btn btn-primary">
                            <i class="bi bi-plus-lg"></i> Thêm mới
                        </a>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <form action="" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Nhập từ khóa tìm kiếm..." value="<?= isset($searchKeyword) ? htmlspecialchars($searchKeyword) : '' ?>">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Tìm kiếm</button>
                            <?php if(isset($searchKeyword) && $searchKeyword != ''): ?>
                                <a href="?" class="btn btn-secondary" title="Xóa tìm kiếm"><i class="bi bi-x-lg"></i></a>
                            <?php endif; ?>
                        </div>
                    </form>
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
                                        <td><?= $row['ID_NCC'] ?></td>
                                        <td class="fw-bold"><?= htmlspecialchars($row['TEN_NCC']) ?></td>
                                        <td><?= htmlspecialchars($row['SDT_NCC']) ?></td>
                                        <td><?= htmlspecialchars($row['EMAIL_NCC']) ?></td>
                                        <td><?= htmlspecialchars($row['DIA_CHI_NCC']) ?></td>
                                        <td align="center">
                                            <div class="dropdown">
                                                <button class="btn btn-flat btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">Hành động</button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="<?= BASE_PATH ?>/admin/suppliers/edit/<?= $row['ID_NCC'] ?>"><i class="bi bi-pencil-square text-primary"></i> Sửa</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item" href="<?= BASE_PATH ?>/admin/suppliers/delete/<?= $row['ID_NCC'] ?>" onclick="return confirm('Bạn chắc chắn muốn xóa?');"><i class="bi bi-trash text-danger"></i> Xóa</a></li>
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
    $(document).ready(function() { $('#table-suppliers').DataTable(); });
</script>