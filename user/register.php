<?php
$page_title = 'Kayıt Ol';
require_once '../includes/header.php';

if (isLoggedIn()) {
    header('Location: ' . SITE_URL . 'user/dashboard.php');
    exit;
}
?>

<div class="form-container">
    <h2 class="form-title">Kayıt Ol</h2>
    <div id="alert-container"></div>
    <form id="register-form">
        <div class="form-group">
            <label for="first_name" class="form-label">Ad</label>
            <input type="text" id="first_name" name="first_name" class="form-input" required>
        </div>
        <div class="form-group">
            <label for="last_name" class="form-label">Soyad</label>
            <input type="text" id="last_name" name="last_name" class="form-input" required>
        </div>
        <div class="form-group">
            <label for="username" class="form-label">Kullanıcı Adı</label>
            <input type="text" id="username" name="username" class="form-input" required>
        </div>
        <div class="form-group">
            <label for="email" class="form-label">E-posta</label>
            <input type="email" id="email" name="email" class="form-input" required>
        </div>
        <div class="form-group">
            <label for="phone" class="form-label">Telefon</label>
            <input type="tel" id="phone" name="phone" class="form-input">
        </div>
        <div class="form-group">
            <label for="password" class="form-label">Şifre</label>
            <input type="password" id="password" name="password" class="form-input" required minlength="6">
        </div>
        <div class="form-group">
            <label for="password_confirm" class="form-label">Şifre Tekrar</label>
            <input type="password" id="password_confirm" name="password_confirm" class="form-input" required minlength="6">
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Kayıt Ol</button>
    </form>
    <p class="text-center mt-3">
        Zaten hesabınız var mı? <a href="<?php echo SITE_URL; ?>user/login.php" style="color: var(--primary-yellow);">Giriş Yap</a>
    </p>
</div>

<?php
$extra_js = ['assets/js/auth.js'];
require_once '../includes/footer.php';
?>

