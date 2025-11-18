<?php require_once __DIR__ . '/../partials/header.php'; ?>
<div class="container">
    <div class="auth-form-container" id="auth-form">

        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="flash-message flash-<?php echo $_SESSION['flash_message']['type']; ?>">
                <?php echo $_SESSION['flash_message']['message']; ?>
            </div>
            <?php unset($_SESSION['flash_message']); ?>
        <?php endif; ?>

        <h2>Đăng Nhập</h2>

        <?php if (!empty($error)): ?>
            <p class="auth-error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="<?php echo BASE_PATH; ?>/auth/handleLogin" method="POST">
            <div class="form-group">
                <label for="username">Email hoặc Số điện thoại:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" required>
                    <img src="<?php echo BASE_PATH; ?>/images/eyelashes.png" alt="Hiện mật khẩu" class="toggle-password-icon">
                </div>
            </div>

            <button type="submit" class="btn-submit-auth">Đăng Nhập</button>
        </form>

        <p>Chưa có tài khoản? <a href="<?php echo BASE_PATH; ?>/auth/register">Đăng ký ngay</a></p>
    </div>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
