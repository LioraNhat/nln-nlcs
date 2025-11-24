<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Danh sách Loại hàng hóa</h3>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-end">
                        <a href="<?php echo BASE_PATH; ?>/admin/product-types/create" class="btn btn-primary">
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
                    <h3 class="card-title">Danh sách phân loại chi tiết</h3>
                </div>
                <div class="card-body">
                    <table id="table-product-types" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">STT</th>
                                <th>Mã Loại</th>
                                <th>Thuộc Danh Mục</th>
                                <th>Tên Loại Hàng</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($productTypes)): ?>
                                <?php $i = 1; foreach ($productTypes as $row): ?>
                                    <tr>
                                        <td class="text-center"><?php echo $i++; ?></td>
                                        <td><?php echo $row['ID_LHH']; ?></td>
                                        <td><span class="badge bg-info"><?php echo htmlspecialchars($row['TEN_DM'] ?? 'N/A'); ?></span></td>
                                        <td><?php echo htmlspecialchars($row['TEN_LHH']); ?></td>
                                        <td align="center">
                                            <div class="dropdown">
                                                <button class="btn btn-flat btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">Hành động</button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="<?php echo BASE_PATH; ?>/admin/product-types/edit/<?php echo $row['ID_LHH']; ?>"><i class="bi bi-pencil-square text-primary"></i> Sửa</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item" href="<?php echo BASE_PATH; ?>/admin/product-types/delete/<?php echo $row['ID_LHH']; ?>" onclick="return confirm('Bạn chắc chắn muốn xóa?');"><i class="bi bi-trash text-danger"></i> Xóa</a></li>
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

<script>
    $(document).ready(function() { $('#table-product-types').DataTable(); });
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>