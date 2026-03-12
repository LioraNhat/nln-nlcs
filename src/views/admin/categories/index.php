<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<main class="app-main">

    <!-- Header -->
    <div class="app-content-header">
        <div class="container-fluid">

            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Quản lý Danh mục</h3>
                </div>

                <div class="col-sm-6 text-end">
                    <a href="<?php echo BASE_PATH; ?>/admin/categories/create" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Thêm mới
                    </a>
                </div>
            </div>

            <!-- Search -->
            <div class="row mt-3">
                <div class="col-md-6">
                    <form method="GET">
                        <div class="input-group">

                            <input type="text"
                                   name="search"
                                   class="form-control"
                                   placeholder="Nhập từ khóa tìm kiếm..."
                                   value="<?= isset($searchKeyword) ? htmlspecialchars($searchKeyword) : '' ?>">

                            <button class="btn btn-primary">
                                <i class="bi bi-search"></i> Tìm
                            </button>

                            <?php if (!empty($searchKeyword)): ?>
                                <a href="?" class="btn btn-secondary">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                            <?php endif; ?>

                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>


    <!-- Content -->
    <div class="app-content">
        <div class="container-fluid">

            <!-- Success message -->
            <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Error message -->
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>


            <!-- Table -->
            <div class="card card-outline card-primary">

                <div class="card-header">
                    <h3 class="card-title">Danh sách Danh mục</h3>
                </div>

                <div class="card-body">

                    <table id="table-categories" class="table table-bordered table-hover table-striped">

                        <thead>
                            <tr>
                                <th width="15%">Mã DM</th>
                                <th>Tên Danh Mục</th>
                                <th width="20%" class="text-center">Hành động</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php if (!empty($categories)): ?>

                            <?php foreach ($categories as $row): ?>
                                <tr>

                                    <td>
                                        <strong><?php echo $row['id_dm']; ?></strong>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row['ten_dm']); ?>
                                    </td>

                                    <td class="text-center">

                                        <a href="<?php echo BASE_PATH; ?>/admin/categories/edit/<?php echo $row['id_dm']; ?>"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i> Sửa
                                        </a>

                                        <a href="<?php echo BASE_PATH; ?>/admin/categories/delete/<?php echo $row['id_dm']; ?>"
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Bạn chắc chắn muốn xóa?')">
                                            <i class="bi bi-trash"></i> Xóa
                                        </a>

                                    </td>

                                </tr>
                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="3" class="text-center">
                                    Không có danh mục nào
                                </td>
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
$(document).ready(function () {

    $('#table-categories').DataTable({
        language: {
            lengthMenu: "Hiển thị _MENU_ dòng",
            zeroRecords: "Không tìm thấy dữ liệu",
            info: "Trang _PAGE_ / _PAGES_",
            infoEmpty: "Không có dữ liệu",
            infoFiltered: "(lọc từ _MAX_ dòng)",
            search: "Tìm kiếm:",
            paginate: {
                previous: "Trước",
                next: "Sau"
            }
        }
    });

});
</script>