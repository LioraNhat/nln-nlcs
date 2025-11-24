<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">Cài đặt hệ thống</h3></div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= $success ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h5 class="card-title">Thông tin chung</h5>
                </div>
                
                <form action="<?= BASE_PATH ?>/admin/settings/update" method="POST" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label class="fw-bold">Tên Website (Tiêu đề)</label>
                                    <input type="text" name="site_title" class="form-control" value="<?= htmlspecialchars($settings['site_title'] ?? '') ?>" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="fw-bold">Email liên hệ</label>
                                    <input type="email" name="site_email" class="form-control" value="<?= htmlspecialchars($settings['site_email'] ?? '') ?>">
                                </div>

                                <div class="form-group mb-3">
                                    <label class="fw-bold">Số điện thoại</label>
                                    <input type="text" name="site_phone" class="form-control" value="<?= htmlspecialchars($settings['site_phone'] ?? '') ?>">
                                </div>

                                <div class="form-group mb-3">
                                    <label class="fw-bold">Địa chỉ cửa hàng</label>
                                    <textarea name="site_address" class="form-control" rows="3"><?= htmlspecialchars($settings['site_address'] ?? '') ?></textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3 text-center">
                                    <label class="fw-bold d-block mb-2">Logo Website</label>
                                    <div class="border p-2 mb-2" style="background: #f8f9fa;">
                                        <?php if (!empty($settings['site_logo'])): ?>
                                            <img src="<?= BASE_PATH ?>/admin_assets/assets/img/<?= $settings['site_logo'] ?>?v=<?= time() ?>" 
                                                 alt="Logo" style="max-width: 100%; max-height: 150px;" id="preview-logo">
                                        <?php else: ?>
                                            <img src="<?= BASE_PATH ?>/admin_assets/assets/img/AdminLTELogo.png" 
                                                 alt="Default Logo" style="max-width: 100%; max-height: 150px;" id="preview-logo">
                                        <?php endif; ?>
                                    </div>
                                    <input type="file" name="site_logo" class="form-control" accept="image/*" onchange="previewImage(this)">
                                    <small class="text-muted">Chọn ảnh mới để thay thế.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Lưu cài đặt
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('preview-logo').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>