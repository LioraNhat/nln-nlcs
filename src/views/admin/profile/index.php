<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">Hồ sơ cá nhân</h3></div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            
            <?php if(isset($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <div class="card card-primary card-outline mb-4">
                <div class="card-header">
                    <h5 class="card-title">Thông tin tài khoản: <strong><?= $profile['ID_TK'] ?></strong></h5>
                </div>
                
                <form action="<?= BASE_PATH ?>/admin/profile/update" method="POST">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Họ và tên</label>
                                <input type="text" name="ho_ten" class="form-control" value="<?= htmlspecialchars($profile['HO_TEN']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($profile['EMAIL']) ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Số điện thoại</label>
                                <input type="text" name="sdt" class="form-control" value="<?= htmlspecialchars($profile['SDT_TK']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Giới tính</label>
                                <select name="gioi_tinh" class="form-select">
                                    <option value="Nam" <?= $profile['GIOI_TINH'] == 'Nam' ? 'selected' : '' ?>>Nam</option>
                                    <option value="Nữ" <?= $profile['GIOI_TINH'] == 'Nữ' ? 'selected' : '' ?>>Nữ</option>
                                    <option value="Khác" <?= $profile['GIOI_TINH'] == 'Khác' ? 'selected' : '' ?>>Khác</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold">Địa chỉ mặc định (Cập nhật nhanh)</label>
                            <textarea name="dia_chi" class="form-control" rows="2" placeholder="Nhập địa chỉ mặc định..."><?= isset($address['DIA_CHI_CHI_TIET']) ? htmlspecialchars($address['DIA_CHI_CHI_TIET']) : '' ?></textarea>
                        </div>

                        <hr>
                        <div class="mb-3">
                            <label class="fw-bold text-danger">Đổi mật khẩu (Bỏ trống nếu không đổi)</label>
                            <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu mới...">
                        </div>
                    </div>

                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>

            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-geo-alt"></i> Sổ địa chỉ của bạn</h3>
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
                                        <td class="fw-bold"><?= htmlspecialchars($addr['TEN_NGUOI_NHAN']) ?></td>
                                        <td><?= htmlspecialchars($addr['SDT_GH']) ?></td>
                                        <td>
                                            <?= htmlspecialchars($addr['DIA_CHI_CHI_TIET']) ?> <br>
                                            <small class="text-muted">
                                                <?= htmlspecialchars($addr['TEN_XA_PHUONG']) ?>, 
                                                <?= htmlspecialchars($addr['TEN_QUAN_HUYEN']) ?>, 
                                                <?= htmlspecialchars($addr['TEN_TINH_TP']) ?>
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($addr['IS_DEFAULT'] == 1): ?>
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
                                        <i class="bi bi-inbox"></i> Bạn chưa lưu địa chỉ nào.
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