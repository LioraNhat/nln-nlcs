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
                    <input type="hidden" name="id" value="<?= $customer['id_tk'] ?>">
                    
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="fw-bold">ID Tài khoản</label>
                            <input type="text" class="form-control" value="<?= $customer['id_tk'] ?>" disabled>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="fw-bold">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="ho_ten" class="form-control" value="<?= htmlspecialchars($customer['ho_ten'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="fw-bold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($customer['email_tk'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="sdt" class="form-control" value="<?= htmlspecialchars($customer['sdt_tk'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="fw-bold">Giới tính</label>
                                <select name="gioi_tinh" class="form-select">
                                    <option value="Nam" <?= ($customer['gioi_tinh'] ?? '') == 'Nam' ? 'selected' : '' ?>>Nam</option>
                                    <option value="Nữ" <?= ($customer['gioi_tinh'] ?? '') == 'Nữ' ? 'selected' : '' ?>>Nữ</option>
                                    <option value="Khác" <?= ($customer['gioi_tinh'] ?? '') == 'Khác' ? 'selected' : '' ?>>Khác</option>
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

            <div class="card card-outline card-info mt-4">
                <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-geo-alt"></i> Sổ địa chỉ nhận hàng</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 50px">#</th>
                                <th style="width: 200px">Người nhận</th>
                                <th style="width: 150px">Số điện thoại</th>
                                <th>Địa chỉ chi tiết</th>
                                <th class="text-center" style="width: 120px">Loại</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($addresses)): ?>
                                <?php foreach ($addresses as $index => $addr): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td class="fw-bold"><?= htmlspecialchars($addr['ten_nguoi_nhan'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($addr['sdt_gh'] ?? '') ?></td>
                                        <td>
                                            <?= htmlspecialchars($addr['dia_chi_chi_tiet'] ?? '') ?> <br>
                                            <small class="text-muted">
                                                <?= htmlspecialchars($addr['ten_xa_phuong'] ?? '') ?>, 
                                                <?= htmlspecialchars($addr['ten_quan_huyen'] ?? '') ?>, 
                                                <?= htmlspecialchars($addr['ten_tinh_tp'] ?? '') ?>
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <?php if (($addr['mac_dinh'] ?? 0) == 1): ?>
                                                <span class="badge bg-success">Mặc định</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Phụ</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">
                                        <i class="bi bi-inbox"></i> Khách hàng này chưa lưu địa chỉ nào.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>