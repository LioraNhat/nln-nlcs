<?php 
// src/views/admin/categories/index.php 
$base = BASE_PATH; 
?>

<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Danh sách danh mục</h3>
            </div>
            <div class="col-sm-6">
                <div class="float-sm-end">
                    <a href="<?= $base ?>/admin/categories/create" class="btn btn-primary">
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
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card card-outline card-primary">
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
                                    <td class="text-center"><?= $i++ ?></td>
                                    <td><?= $row['ID_DM'] ?></td>
                                    <td><?= htmlspecialchars($row['TEN_DM']) ?></td>
                                    <td align="center">
                                        <div class="dropdown">
                                            <button class="btn btn-flat btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Hành động
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="<?= $base ?>/admin/categories/edit/<?= $row['ID_DM'] ?>">
                                                        <i class="bi bi-pencil-square text-primary"></i> Sửa
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item" href="<?= $base ?>/admin/categories/delete/<?= $row['ID_DM'] ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này? Nếu có loại hàng bên trong, bạn sẽ không xóa được.');">
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">

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
                "paginate": {
                    "previous": "Trước",
                    "next": "Sau"
                }
            }
        });
    });
</script>