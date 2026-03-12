<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';

$isEdit = $isEdit ?? false;
?>

<main class="app-main">

    <!-- Header -->
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">

                <div class="col-sm-6">
                    <h3 class="mb-0">
                        <?php echo $isEdit ? "Cập nhật" : "Thêm mới"; ?> Danh mục
                    </h3>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <a href="<?php echo BASE_PATH; ?>/admin/dashboard">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?php echo BASE_PATH; ?>/admin/categories">Danh mục</a>
                        </li>
                        <li class="breadcrumb-item active">Form</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>


    <!-- Content -->
    <div class="app-content">
        <div class="container-fluid">

            <div class="card card-outline card-info">

                <div class="card-header">
                    <h3 class="card-title">Thông tin danh mục</h3>
                </div>

                <div class="card-body">

                    <form action="<?php echo BASE_PATH; ?>/admin/categories/store" method="POST">

                        <?php if ($isEdit): ?>
                            <input type="hidden" name="id" value="<?php echo $category['id_dm']; ?>">

                            <div class="mb-3">
                                <label class="form-label fw-bold">Mã danh mục</label>
                                <input type="text"
                                       class="form-control"
                                       value="<?php echo $category['id_dm']; ?>"
                                       disabled>
                            </div>
                        <?php endif; ?>


                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                Tên danh mục <span class="text-danger">*</span>
                            </label>

                            <input type="text"
                                   name="ten_dm"
                                   class="form-control"
                                   required
                                   value="<?php echo isset($category['ten_dm']) ? htmlspecialchars($category['ten_dm']) : ''; ?>"
                                   placeholder="Ví dụ: Hải sản, Rau củ...">
                        </div>


                        <!-- Buttons -->
                        <div class="text-end">
                            <a href="<?php echo BASE_PATH; ?>/admin/categories"
                               class="btn btn-secondary">
                                Thoát
                            </a>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Lưu
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>