<?php 
require_once __DIR__ . '/../partials/header.php'; 

// Đảm bảo lấy đúng dữ liệu user từ session
$user = $_SESSION['user'] ?? [];
?>

<div class="container account-page-container">
    <div class="account-header">
        <h2>Quản lý tài khoản</h2>
        <a href="<?php echo BASE_PATH; ?>/account/index" class="btn-secondary">&lt;&lt; Quay lại Đơn hàng</a>
    </div>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
                echo $_SESSION['success']; 
                unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <div class="profile-layout"> 
        <div class="auth-form-container">
            <h3>Thông tin cá nhân</h3>
            <form action="<?php echo BASE_PATH; ?>/account/handleUpdateProfile" method="POST">
                
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" value="<?php echo htmlspecialchars($user['email_tk'] ?? ''); ?>" disabled>
                    <small>Bạn không thể thay đổi Email.</small>
                </div>
                
                <div class="form-group">
                    <label for="ho_ten">Họ và Tên:</label>
                    <input type="text" id="ho_ten" name="ho_ten" value="<?php echo htmlspecialchars($user['ho_ten'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="sdt_tk">Liên hệ (SĐT):</label>
                    <input type="tel" id="sdt_tk" name="sdt_tk" value="<?php echo htmlspecialchars($user['sdt_tk'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="gioi_tinh">Giới tính:</label>
                    <select id="gioi_tinh" name="gioi_tinh">
                        <?php $gender = $user['gioi_tinh'] ?? 'Nam'; ?>
                        <option value="Nam" <?php echo ($gender == 'Nam') ? 'selected' : ''; ?>>Nam</option>
                        <option value="Nữ" <?php echo ($gender == 'Nữ') ? 'selected' : ''; ?>>Nữ</option>
                        <option value="Khác" <?php echo ($gender == 'Khác') ? 'selected' : ''; ?>>Khác</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-submit-auth">Cập nhật thông tin</button>
            </form>
        </div>

        <div class="auth-form-container">
            <h3>Đổi mật khẩu</h3>
            <form action="<?php echo BASE_PATH; ?>/account/handleChangePassword" method="POST">
                <div class="form-group">
                    <label for="current_password">Mật khẩu hiện tại:</label>
                    <div class="password-wrapper">
                        <input type="password" id="current_password" name="current_password" required>
                        <img src="<?php echo BASE_PATH; ?>/images/eyelashes.png" alt="Hiện" class="toggle-password-icon">
                    </div>
                </div>

                <div class="form-group">
                    <label for="new_password">Mật khẩu mới:</label>
                    <div class="password-wrapper">
                        <input type="password" id="new_password" name="new_password" required>
                        <img src="<?php echo BASE_PATH; ?>/images/eyelashes.png" alt="Hiện" class="toggle-password-icon">
                    </div>
                </div>

                <div class="form-group">
                    <label for="new_password_confirm">Xác nhận mật khẩu mới:</label>
                    <div class="password-wrapper">
                        <input type="password" id="new_password_confirm" name="new_password_confirm" required>
                        <img src="<?php echo BASE_PATH; ?>/images/eyelashes.png" alt="Hiện" class="toggle-password-icon">
                    </div>
                </div>

                <button type="submit" class="btn-submit-auth">Đổi mật khẩu</button>
            </form>
        </div>
    </div> 

    <div class="address-section">
        <div class="address-header">
            <h3>Sổ địa chỉ</h3>
            <button type="button" class="btn-primary" id="btn-show-add-modal"> + Thêm địa chỉ mới</button>
        </div>

        <div class="address-list">
            <form action="<?php echo BASE_PATH; ?>/account/handleSetDefaultAddress" method="POST" id="form-set-default-address">

                <?php if (empty($addresses)): ?>
                    <p>Bạn chưa có địa chỉ nào.</p>
                <?php else: ?>

                    <?php foreach ($addresses as $addr): ?>

                        <div class="form-group-radio address-item">

                            <input 
                                type="radio"
                                id="addr_<?php echo $addr['id_dc']; ?>"
                                name="default_address_id"
                                value="<?php echo $addr['id_dc']; ?>"
                                class="address-radio"
                                <?php if ($addr['mac_dinh']) echo 'checked'; ?>
                            >

                            <label for="addr_<?php echo $addr['id_dc']; ?>" class="radio-label">

                                <strong>
                                    <?php echo htmlspecialchars($addr['ten_nguoi_nhan']); ?>
                                    |
                                    <?php echo htmlspecialchars($addr['sdt_gh']); ?>

                                    <span class="address-actions">

                                        <a href="#"
                                           class="address-action-link btn-edit-address"
                                           data-id="<?php echo $addr['id_dc']; ?>"
                                           data-name="<?php echo htmlspecialchars($addr['ten_nguoi_nhan']); ?>"
                                           data-phone="<?php echo htmlspecialchars($addr['sdt_gh']); ?>"
                                           data-detail="<?php echo htmlspecialchars($addr['dia_chi_chi_tiet']); ?>"
                                           data-province="<?php echo $addr['ma_tinh_tp']; ?>"
                                           data-district="<?php echo $addr['ma_quan_huyen']; ?>"
                                           data-ward="<?php echo $addr['ma_xa_phuong']; ?>"
                                           data-default="<?php echo $addr['mac_dinh']; ?>"
                                        >
                                            Sửa
                                        </a>

                                        <a href="javascript:void(0);" 
                                           class="address-action-link delete-address-btn" 
                                           data-id="<?php echo $addr['id_dc']; ?>">
                                            Xóa
                                        </a>

                                    </span>
                                </strong>

                                <?php if ($addr['mac_dinh']): ?>
                                    <span class="status-badge">(Mặc định)</span>
                                <?php endif; ?>

                                <p><?php echo htmlspecialchars($addr['dia_chi_chi_tiet']); ?></p>

                                <p>
                                    <?php echo htmlspecialchars($addr['ten_xa_phuong']); ?>,
                                    <?php echo htmlspecialchars($addr['ten_quan_huyen']); ?>,
                                    <?php echo htmlspecialchars($addr['ten_tinh_tp']); ?>
                                </p>

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