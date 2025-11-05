<?php
$page_title = 'Giriş Yap';
require_once '../includes/header.php';

if (isLoggedIn()) {
    header('Location: ' . SITE_URL . 'user/dashboard.php');
    exit;
}
?>

<div class="form-container">
    <h2 class="form-title">Giriş Yap</h2>
    <div id="alert-container"></div>
    <form id="login-form">
        <div class="form-group">
            <label for="username" class="form-label">Kullanıcı Adı veya E-posta</label>
            <input type="text" id="username" name="username" class="form-input" required>
        </div>
        <div class="form-group">
            <label for="password" class="form-label">Şifre</label>
            <input type="password" id="password" name="password" class="form-input" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Giriş Yap</button>
    </form>
    <p class="text-center mt-3">
        Hesabınız yok mu? <a href="<?php echo SITE_URL; ?>user/register.php" style="color: var(--primary-yellow);">Kayıt Ol</a>
    </p>
</div>

<?php
$extra_js = ['assets/js/auth.js'];
require_once '../includes/footer.php';
?>

