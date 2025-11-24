<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Quản lý Danh mục</h3>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-end">
                        <a href="<?php echo BASE_PATH; ?>/admin/categories/create" class="btn btn-primary">
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
                <div class="alert alert-success alert-dismissible fade show">
                    <?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Danh sách Danh mục chính</h3>
                </div>
                <div class="card-body">
                    <table id="table-categories" class="table table-bordered table-striped table-hover">
                        <colgroup>
                            <col width="10%">
                            <col width="20%">
                            <col width="50%">
                            <col width="20%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="text-center">STT</th>
                                <th>Mã DM</th>
                                <th>Tên Danh Mục</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($categories)): ?>
                                <?php $i = 1; foreach ($categories as $row): ?>
                                    <tr>
                                        <td class="text-center"><?php echo $i++; ?></td>
                                        <td><?php echo $row['ID_DM']; ?></td>
                                        <td><?php echo htmlspecialchars($row['TEN_DM']); ?></td>
                                        <td align="center">
                                            <div class="dropdown">
                                                <button class="btn btn-flat btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Hành động
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="<?php echo BASE_PATH; ?>/admin/categories/edit/<?php echo $row['ID_DM']; ?>">
                                                            <i class="bi bi-pencil-square text-primary"></i> Sửa
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item" href="<?php echo BASE_PATH; ?>/admin/categories/delete/<?php echo $row['ID_DM']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này? Nếu có loại hàng bên trong, bạn sẽ không xóa được.');">
                                                            <i class="bi bi-trash text-danger"></i> Xóa
                                                        </a>
                                                    </li>
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

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>

<script>
    $(document).ready(function() {
        $('#table-categories').DataTable({
            "language": {
                "lengthMenu": "Hiển thị _MENU_ dòng",
                "zeroRecords": "Không tìm thấy dữ liệu",
                "info": "Hiển thị trang _PAGE_ / _PAGES_",
                "infoEmpty": "Không có dữ liệu",
                "infoFiltered": "(lọc từ _MAX_ tổng số dòng)",
                "search": "Tìm kiếm:",
                "paginate": { "previous": "Trước", "next": "Sau" }
            }
        });
    });
</script>