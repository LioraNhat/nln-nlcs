<?php 
// src/views/admin/categories/form.php
$base = BASE_PATH;
$isEdit = $isEdit ?? false;
?>

<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0"><?= $isEdit ? "Cập nhật" : "Thêm mới" ?> Danh mục</h3>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">Thông tin danh mục</h3>
            </div>
            <div class="card-body">
                <form action="<?= $base ?>/admin/categories/store" method="POST" id="category-form">
                    
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="id" value="<?= $category['ID_DM'] ?>">
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Mã Danh Mục:</label>
                            <input type="text" class="form-control" value="<?= $category['ID_DM'] ?>" disabled>
                            <small class="text-muted">Mã danh mục không thể thay đổi.</small>
                        </div>
                    <?php endif; ?>

                    <div class="form-group mb-3">
                        <label for="ten_dm" class="form-label fw-bold">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" name="ten_dm" id="ten_dm" class="form-control" 
                               value="<?= isset($category['TEN_DM']) ? htmlspecialchars($category['TEN_DM']) : '' ?>" 
                               placeholder="Nhập tên danh mục (Ví dụ: Hải sản, Rau củ...)" required>
                    </div>

                    <div class="card-footer bg-white ps-0">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Lưu lại
                        </button>
                        <a href="<?= $base ?>/admin/categories" class="btn btn-default border">
                            Thoát
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>