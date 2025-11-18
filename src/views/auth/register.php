<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <div class="auth-form-container" id="auth-form">
        <h2>Đăng Ký</h2>

        <form action="<?php echo BASE_PATH; ?>/auth/handleRegister" method="POST">
            
            <div class="form-group">
                <label for="ho_ten">Họ và Tên:</label>
                <input type="text" id="ho_ten" name="ho_ten" required 
                       value="<?php echo htmlspecialchars($old['ho_ten'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>">
                
                <?php if (isset($errors['email'])): ?>
                    <p class="auth-error"><?php echo $errors['email']; ?></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="sdt_tk">Số điện thoại:</label>
                <input type="tel" id="sdt_tk" name="sdt_tk" required 
                       value="<?php echo htmlspecialchars($old['sdt_tk'] ?? ''); ?>">
                
                <?php if (isset($errors['sdt'])): ?>
                    <p class="auth-error"><?php echo $errors['sdt']; ?></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="gioi_tinh">Giới tính:</label>
                <select id="gioi_tinh" name="gioi_tinh">
                    <option value="Nam" <?php echo (($old['gioi_tinh'] ?? '') === 'Nam') ? 'selected' : ''; ?>>Nam</option>
                    <option value="Nữ" <?php echo (($old['gioi_tinh'] ?? '') === 'Nữ') ? 'selected' : ''; ?>>Nữ</option>
                    <option value="Khác" <?php echo (($old['gioi_tinh'] ?? '') === 'Khác') ? 'selected' : ''; ?>>Khác</option>
                </select>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <div class="password-wrapper"> 
                    <input type="password" id="password" name="password" required>
                    <img src="<?php echo BASE_PATH; ?>/images/eyelashes.png" alt="Hiện mật khẩu" class="toggle-password-icon">
                </div>
                <?php if (isset($errors['password'])): ?><p class="auth-error"><?php echo $errors['password']; ?></p><?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="password_confirm">Nhập lại mật khẩu:</label>
                <div class="password-wrapper"> 
                    <input type="password" id="password_confirm" name="password_confirm" required>
                    <img src="<?php echo BASE_PATH; ?>/images/eyelashes.png" alt="Hiện mật khẩu" class="toggle-password-icon">
                </div>
            </div>

            <?php if (isset($errors['server'])): ?>
                <p class="auth-error"><?php echo $errors['server']; ?></p>
            <?php endif; ?>

            <button type="submit" class="btn-submit-auth">Đăng Ký</button>
        </form>

        <p>Đã có tài khoản? <a href="<?php echo BASE_PATH; ?>/auth/login">Đăng nhập</a></p>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>