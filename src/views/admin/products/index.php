<?php
require_once __DIR__ . '/../layouts/header.php'; 
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Quản lý Sản phẩm</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="<?php echo BASE_PATH; ?>/admin/dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Sản phẩm</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Danh sách Hàng hóa</h3>
                    <div class="card-tools">
                        <a href="<?php echo BASE_PATH; ?>/admin/products/create" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-lg"></i> Thêm mới
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle">
                            <colgroup>
                                <col width="5%">
                                <col width="10%">
                                <col width="30%">
                                <col width="15%">
                                <col width="10%">
                                <col width="15%">
                                <col width="15%">
                            </colgroup>
                            <thead>
                                <tr class="text-center align-middle">
                                    <th>STT</th>
                                    <th>Hình ảnh</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Danh mục</th>
                                    <th>ĐVT</th>
                                    <th>Giá bán</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if (!empty($products)):
                                    // Tính số thứ tự (STT) dựa trên trang hiện tại
                                    $i = ($currentPage - 1) * 20 + 1;
                                    
                                    foreach ($products as $row):
                                        // Xử lý đường dẫn ảnh
                                        $imgSrc = !empty($row['link_anh']) 
                                            ? BASE_PATH . '/uploads/' . $row['link_anh'] 
                                            : BASE_PATH . '/admin_assets/assets/img/default-150x150.png';
                                        
                                        // Xử lý hiển thị giá
                                        $giaHienTai = isset($row['GIA_HIEN_TAI']) ? number_format($row['GIA_HIEN_TAI'], 0, ',', '.') . 'đ' : 'Chưa có giá';
                                ?>
                                    <tr>
                                        <td class="text-center"><?php echo $i++; ?></td>
                                        
                                        <td class="text-center">
                                            <img src="<?php echo $imgSrc; ?>" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;" alt="SP">
                                        </td>
                                        
                                        <td>
                                            <p class="mb-0 fw-bold"><?php echo htmlspecialchars($row['TEN_HH']); ?></p>
                                            <small class="text-muted">Mã: <?php echo $row['ID_HH']; ?></small>
                                            <?php if ($row['DUOC_PHEP_BAN'] == 0): ?>
                                                <br><span class="badge text-bg-danger">Ngừng kinh doanh</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <td><?php echo htmlspecialchars($row['TEN_LHH'] ?? 'Chưa phân loại'); ?></td>
                                        
                                        <td class="text-center"><?php echo htmlspecialchars($row['DVT'] ?? ''); ?></td>

                                        <td class="text-end fw-bold text-success">
                                            <?php echo $giaHienTai; ?>
                                        </td>
                                        
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="<?php echo BASE_PATH; ?>/admin/products/edit/<?php echo $row['ID_HH']; ?>" class="btn btn-sm btn-info" title="Sửa">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <a href="javascript:void(0);" onclick="confirmDelete('<?php echo $row['ID_HH']; ?>')" class="btn btn-sm btn-danger" title="Xóa">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            Chưa có sản phẩm nào trong kho.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="card-footer clearfix">
                    <?php if ($totalPages > 1): ?>
                    <ul class="pagination pagination-sm m-0 float-end">
                        <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?controller=admin&action=products&page=<?php echo $currentPage - 1; ?>&search=<?php echo $searchKeyword; ?>">&laquo;</a>
                        </li>
                        
                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <li class="page-item <?php echo $p == $currentPage ? 'active' : ''; ?>">
                                <a class="page-link" href="?controller=admin&action=products&page=<?php echo $p; ?>&search=<?php echo $searchKeyword; ?>">
                                    <?php echo $p; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?controller=admin&action=products&page=<?php echo $currentPage + 1; ?>&search=<?php echo $searchKeyword; ?>">&raquo;</a>
                        </li>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>

<script>
    function confirmDelete(id) {
        if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')) {
            window.location.href = '<?php echo BASE_PATH; ?>/admin/products/delete/' + id;
        }
    }
</script>