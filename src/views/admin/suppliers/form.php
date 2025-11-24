<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
$isEdit = $isEdit ?? false;
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0"><?= $isEdit ? "Cập nhật" : "Thêm mới" ?> Nhà cung cấp</h3></div>
                <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="<?= BASE_PATH ?>/admin/dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= BASE_PATH ?>/admin/suppliers">Nhà cung cấp</a></li>
                        <li class="breadcrumb-item active">Form</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-outline card-info">
                <div class="card-body">
                    <form action="<?= BASE_PATH ?>/admin/suppliers/store" method="POST">
                        <?php if ($isEdit): ?>
                            <input type="hidden" name="id" value="<?= $supplier['ID_NCC'] ?>">
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Mã NCC:</label>
                                <input type="text" class="form-control" value="<?= $supplier['ID_NCC'] ?>" disabled>
                            </div>
                        <?php endif; ?>

                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Tên Nhà cung cấp <span class="text-danger">*</span></label>
                            <input type="text" name="ten_ncc" class="form-control" 
                                   value="<?= $isEdit ? htmlspecialchars($supplier['TEN_NCC']) : '' ?>" 
                                   required placeholder="Ví dụ: Công ty C.P Việt Nam">
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="sdt" class="form-control" 
                                       value="<?= $isEdit ? htmlspecialchars($supplier['SDT_NCC']) : '' ?>" required>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" 
                                       value="<?= $isEdit ? htmlspecialchars($supplier['EMAIL_NCC']) : '' ?>" required>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Địa chỉ chi tiết <span class="text-danger">*</span></label>
                            <textarea name="dia_chi" class="form-control" rows="3" required><?= $isEdit ? htmlspecialchars($supplier['DIA_CHI_NCC']) : '' ?></textarea>
                        </div>

                        <div class="card-footer bg-white ps-0 text-end">
                            <a href="<?= BASE_PATH ?>/admin/suppliers" class="btn btn-default border">Thoát</a>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Lưu lại</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>