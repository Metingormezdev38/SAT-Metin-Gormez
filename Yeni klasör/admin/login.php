<?php
$page_title = 'Admin Girişi';
require_once '../includes/header.php';

if (isAdmin()) {
    header('Location: ' . SITE_URL . 'admin/dashboard.php');
    exit;
}
?>

<section class="auth-section admin-auth">
    <div class="container">
        <div class="auth-wrapper admin-auth-wrapper">
            <div class="auth-form-card">
                <div class="auth-form-header">
                    <span class="admin-badge">Admin</span>
                    <h2 class="auth-form-title">Admin Girişi</h2>
                    <p class="auth-form-subtitle">Yetkili kullanıcı girişi</p>
                </div>

                <div id="alert-container"></div>

                <form id="admin-login-form" class="auth-form">
                    <div class="form-group">
                        <label for="username" class="form-label">
                            <span class="label-icon">👤</span>
                            Kullanıcı Adı veya E-posta
                        </label>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            class="form-input"
                            placeholder="admin@powerfit.com"
                            required
                            autocomplete="username"
                        >
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <span class="label-icon">🔒</span>
                            Şifre
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        >
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block btn-large">
                            <span>Giriş Yap</span>
                            <span class="btn-icon">→</span>
                        </button>
                    </div>
                </form>

                <div class="auth-form-footer">
                    <p class="auth-form-footer-text">
                        <a href="<?php echo SITE_URL; ?>" class="auth-link"><strong>Ana Sayfaya Dön</strong></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$extra_js = ['assets/js/admin-auth.js'];
require_once '../includes/footer.php';
?>

