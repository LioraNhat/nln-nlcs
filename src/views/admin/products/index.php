<?php 
require_once __DIR__ . '/../layouts/header.php'; 
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">Quản lý Sản phẩm</h3></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="<?php echo BASE_PATH; ?>/admin/dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Sản phẩm</li>
                    </ol>
                </div>
            </div>
            <div class="row mb-3 mt-3">
                <div class="col-md-6">
                    <form action="" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Tìm tên hoặc mã..." value="<?= htmlspecialchars($searchKeyword ?? '') ?>">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
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
                        <a href="<?php echo BASE_PATH; ?>/admin/products/create" class="btn btn-primary btn-sm">+ Thêm mới</a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle">
                            <thead>
                                <tr class="text-center">
                                    <th>STT</th>
                                    <th>Hình ảnh</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Danh mục</th>
                                    <th>ĐVT</th>
                                    <th>Lô & HSD</th>
                                    <th>Giá bán</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>

                            <tbody>
                            <?php 
                            if (!empty($products)):
                                $i = ($currentPage - 1) * 20 + 1;

                                foreach ($products as $row):

                                    $imgSrc = !empty($row['link_anh']) 
                                        ? BASE_PATH . '/uploads/' . $row['link_anh'] 
                                        : BASE_PATH . '/admin_assets/assets/img/default-150x150.png';

                                    $giaHienTai = isset($row['gia_hien_tai']) 
                                        ? number_format($row['gia_hien_tai'], 0, ',', '.') . 'đ' 
                                        : 'Chưa có giá';
                            ?>

                            <tr>
                                <td class="text-center"><?php echo $i++; ?></td>
                                <td class="text-center">
                                    <img src="<?php echo $imgSrc; ?>" 
                                        class="img-thumbnail" 
                                        style="width:50px;height:50px;object-fit:cover;">
                                </td>
                                <td>
                                    <p class="mb-0 fw-bold"><?php echo htmlspecialchars($row['ten_hh'] ?? ''); ?></p>
                                    <small class="text-muted">Mã: <?php echo $row['id_hh'] ?? ''; ?></small>

                                    <?php if (($row['duoc_phep_ban'] ?? 0) == 0): ?>
                                        <br><span class="badge text-bg-danger">Ngừng bán</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['ten_loai'] ?? 'Chưa phân loại'); ?></td>
                                <td class="text-center"><?php echo htmlspecialchars($row['dvt'] ?? ''); ?></td>
                                <!-- LÔ HÀNG -->
                                <td>
                                    <?php if(!empty($row['id_lo'])): ?>
                                        <span class="badge bg-info"><?php echo $row['id_lo']; ?></span><br>
                                        <?php if(!empty($row['hsd_lo'])): ?>
                                            <small>HSD: <?php echo date('d/m/Y', strtotime($row['hsd_lo'])); ?></small><br>
                                        <?php endif; ?>
                                        <small class="text-success">
                                            Tồn: <?php echo $row['ton_lo'] ?? 0; ?>
                                        </small>
                                    <?php else: ?>
                                        <span class="text-danger">Hết hàng / Chưa nhập lô</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    <?php echo $giaHienTai; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="<?php echo BASE_PATH; ?>/admin/inventories/create?id_hh=<?php echo $row['id_hh']; ?>" 
                                        class="btn btn-sm btn-success"
                                        title="Nhập lô mới">
                                        <i class="bi bi-plus-circle"></i>
                                        </a>

                                        <a href="<?php echo BASE_PATH; ?>/admin/products/edit/<?php echo $row['id_hh']; ?>" class="btn btn-sm btn-info">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <a href="javascript:void(0);" 
                                        onclick="confirmDelete('<?php echo $row['id_hh']; ?>')" 
                                        class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php 
                                endforeach;
                            else: 
                            ?>
                            <tr>
                                <td colspan="8" class="text-center">Trống.</td>
                            </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                </div>
        </div>
    </div>
</main>

<script>
function confirmDelete(id) {
    if (confirm('Bạn có chắc muốn xóa sản phẩm ' + id + '?')) {
        window.location.href = '<?= BASE_PATH ?>/admin/products/delete/' + id;
    }
}
</script>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>