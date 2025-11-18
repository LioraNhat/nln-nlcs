<?php 
require_once __DIR__ . '/../partials/header.php'; 
?>

<div class="container account-page-container">
    <div class="account-header"><h2>Quản lý tài khoản</h2><a href="<?php echo BASE_PATH; ?>/account/index" class="btn-secondary">&lt;&lt; Quay lại Đơn hàng</a></div>
    <?php if (isset($success)): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
    <?php if (isset($error)): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>

    <div class="profile-layout"> 
        <div class="auth-form-container">
            <h3>Thông tin cá nhân</h3>
            <form action="<?php echo BASE_PATH; ?>/account/handleUpdateProfile" method="POST">
                <div class="form-group"><label>Email:</label><input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled><small>Bạn không thể thay đổi Email.</small></div>
                <div class="form-group"><label for="ho_ten">Họ và Tên:</label><input type="text" id="ho_ten" name="ho_ten" value="<?php echo htmlspecialchars($user['name']); ?>" required></div>
                <div class="form-group"><label for="sdt_tk">Liên hệ (SĐT):</label><input type="tel" id="sdt_tk" name="sdt_tk" value="<?php echo htmlspecialchars($user['phone']); ?>" required></div>
                <div class="form-group"><label for="gioi_tinh">Giới tính:</label><select id="gioi_tinh" name="gioi_tinh">
                        <option value="Nam" <?php if ($user['gender'] == 'Nam') echo 'selected'; ?>>Nam</option>
                        <option value="Nữ" <?php if ($user['gender'] == 'Nữ') echo 'selected'; ?>>Nữ</option>
                        <option value="Khác" <?php if ($user['gender'] == 'Khác') echo 'selected'; ?>>Khác</option>
                </select></div>
                <button type="submit" class="btn-submit-auth">Cập nhật thông tin</button>
            </form>
        </div>
        <div class="auth-form-container">
            <h3>Đổi mật khẩu</h3>
            <form action="<?php echo BASE_PATH; ?>/account/handleChangePassword" method="POST">
                <div class="form-group"><label for="current_password">Mật khẩu hiện tại:</label><div class="password-wrapper"><input type="password" id="current_password" name="current_password" required><img src="<?php echo BASE_PATH; ?>/images/eyelashes.png" alt="Hiện mật khẩu" class="toggle-password-icon"></div></div>
                <div class="form-group"><label for="new_password">Mật khẩu mới:</label><div class="password-wrapper"><input type="password" id="new_password" name="new_password" required><img src="<?php echo BASE_PATH; ?>/images/eyelashes.png" alt="Hiện mật khẩu" class="toggle-password-icon"></div></div>
                <div class="form-group"><label for="new_password_confirm">Xác nhận mật khẩu mới:</label><div class="password-wrapper"><input type="password" id="new_password_confirm" name="new_password_confirm" required><img src="<?php echo BASE_PATH; ?>/images/eyelashes.png" alt="Hiện mật khẩu" class="toggle-password-icon"></div></div>
                <button type="submit" class="btn-submit-auth">Đổi mật khẩu</button>
            </form>
        </div>
    </div> 

    <div class="address-section">
        <div class="address-header">
            <h3>Sổ địa chỉ</h3>
            <button type="button" class="btn-primary" id="btn-show-add-modal"> + Thêm địa chỉ mới
            </button>
        </div>
        <div class="address-list">
            <form action="<?php echo BASE_PATH; ?>/account/handleSetDefaultAddress" method="POST" id="form-set-default-address">
                <?php if (empty($addresses)): ?>
                    <p>Bé iu chưa có địa chỉ nào.</p>
                <?php else: ?>
                    <?php foreach ($addresses as $addr): ?>
                        <div class="form-group-radio address-item">
                            <input type="radio" id="addr_<?php echo $addr['ID_DIA_CHI']; ?>" name="default_address_id" value="<?php echo $addr['ID_DIA_CHI']; ?>" class="address-radio" <?php if ($addr['IS_DEFAULT']) echo 'checked'; ?>>
                            <label for="addr_<?php echo $addr['ID_DIA_CHI']; ?>" class="radio-label">
                                <strong>
                                    <?php echo htmlspecialchars($addr['TEN_NGUOI_NHAN']); ?> | <?php echo htmlspecialchars($addr['SDT_GH']); ?>
                                    
                                    <span class="address-actions">
                                        <a href="#" class="address-action-link btn-edit-address" 
                                           data-id="<?php echo $addr['ID_DIA_CHI']; ?>">
                                           Sửa
                                        </a>
                                        |
                                        <a href="<?php echo BASE_PATH; ?>/account/deleteAddress/<?php echo $addr['ID_DIA_CHI']; ?>" 
                                           class="address-action-link delete-link" 
                                           onclick="return confirm('Bé iu có chắc muốn xóa địa chỉ này?');">
                                           Xóa
                                        </a>
                                    </span>
                                </strong>
                                <?php if ($addr['IS_DEFAULT']): ?><span class="status-badge">(Mặc định)</span><?php endif; ?>
                                <p><?php echo htmlspecialchars($addr['DIA_CHI_CHI_TIET']); ?></p>
                                <p><?php echo htmlspecialchars($addr['TEN_XA_PHUONG']); ?>, <?php echo htmlspecialchars($addr['TEN_QUAN_HUYEN']); ?>, <?php echo htmlspecialchars($addr['TEN_TINH_TP']); ?></p>
                            </label>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>



<?php 
require_once __DIR__ . '/../partials/address-modal.php';
require_once __DIR__ . '/../partials/footer.php'; 
?>