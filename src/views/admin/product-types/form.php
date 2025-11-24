<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';

$isEdit = $isEdit ?? false;
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0"><?php echo $isEdit ? "Cập nhật" : "Thêm mới"; ?> Loại hàng hóa</h3>
                </div>
                <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="<?php echo BASE_PATH; ?>/admin/dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo BASE_PATH; ?>/admin/product-types">Loại hàng</a></li>
                        <li class="breadcrumb-item active">Form</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">Thông tin loại hàng</h3>
                </div>
                <div class="card-body">
                    <form action="<?php echo BASE_PATH; ?>/admin/product-types/store" method="POST">
                        <?php if ($isEdit): ?>
                            <input type="hidden" name="id" value="<?php echo $productType['ID_LHH']; ?>">
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Mã Loại:</label>
                                <input type="text" class="form-control" value="<?php echo $productType['ID_LHH']; ?>" disabled>
                            </div>
                        <?php endif; ?>

                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Chọn Danh mục cha <span class="text-danger">*</span></label>
                            <select name="id_dm" class="form-select" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['ID_DM']; ?>" 
                                        <?php echo (isset($productType['ID_DM']) && $productType['ID_DM'] == $cat['ID_DM']) ? 'selected' : ''; ?>>
                                        <?php echo $cat['TEN_DM']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Tên Loại hàng <span class="text-danger">*</span></label>
                            <input type="text" name="ten_lhh" class="form-control" 
                                   value="<?php echo isset($productType['TEN_LHH']) ? htmlspecialchars($productType['TEN_LHH']) : ''; ?>" 
                                   placeholder="Nhập tên loại (Ví dụ: Món mặn, Rau lá...)" required>
                        </div>

                        <div class="card-footer bg-white ps-0 text-end">
                             <a href="<?php echo BASE_PATH; ?>/admin/product-types" class="btn btn-default border">Thoát</a>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Lưu lại</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>