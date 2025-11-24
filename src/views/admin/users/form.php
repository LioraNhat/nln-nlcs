<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">Cập nhật khách hàng</h3></div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <form action="<?= BASE_PATH ?>/admin/users/update" method="POST">
                    <input type="hidden" name="id" value="<?= $customer['ID_TK'] ?>">
                    
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="fw-bold">ID Tài khoản</label>
                            <input type="text" class="form-control" value="<?= $customer['ID_TK'] ?>" disabled>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="fw-bold">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="ho_ten" class="form-control" value="<?= htmlspecialchars($customer['HO_TEN']) ?>" required>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="fw-bold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($customer['EMAIL']) ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="sdt" class="form-control" value="<?= htmlspecialchars($customer['SDT_TK']) ?>" required>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="fw-bold">Giới tính</label>
                                <select name="gioi_tinh" class="form-select">
                                    <option value="Nam" <?= $customer['GIOI_TINH'] == 'Nam' ? 'selected' : '' ?>>Nam</option>
                                    <option value="Nữ" <?= $customer['GIOI_TINH'] == 'Nữ' ? 'selected' : '' ?>>Nữ</option>
                                    <option value="Khác" <?= $customer['GIOI_TINH'] == 'Khác' ? 'selected' : '' ?>>Khác</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="fw-bold text-danger">Đặt lại Mật khẩu (Để trống nếu không đổi)</label>
                            <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu mới cho khách hàng...">
                        </div>
                    </div>

                    <div class="card-footer text-end">
                        <a href="<?= BASE_PATH ?>/admin/users" class="btn btn-secondary">Hủy</a>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>