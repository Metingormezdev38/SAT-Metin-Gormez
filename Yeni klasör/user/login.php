<?php
$page_title = 'Giriş Yap';
require_once '../includes/header.php';

if (isLoggedIn()) {
    header('Location: ' . SITE_URL . 'user/dashboard.php');
    exit;
}
?>

<section class="auth-section">
    <div class="container">
        <div class="auth-wrapper">
            <!-- Sol Taraf - Bilgilendirme -->
            <div class="auth-info">
                <div class="auth-info-content">
                    <h1 class="auth-welcome-title">PowerFit'e Hoş Geldiniz!</h1>
                    <p class="auth-welcome-text">
                        Sağlıklı yaşam yolculuğunuzda yanınızdayız. Modern ekipmanlar, uzman antrenörler ve kişiselleştirilmiş programlarla hedeflerinize ulaşın.
                    </p>
                    
                    <div class="auth-features">
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">💪</div>
                            <div class="auth-feature-text">
                                <h3>Kişisel Antrenör</h3>
                                <p>Uzman antrenörlerimizle birlikte size özel antrenman programları</p>
                            </div>
                        </div>
                        
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">🥗</div>
                            <div class="auth-feature-text">
                                <h3>Beslenme Planı</h3>
                                <p>Hedeflerinize uygun kişiselleştirilmiş beslenme planları</p>
                            </div>
                        </div>
                        
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">📊</div>
                            <div class="auth-feature-text">
                                <h3>İlerleme Takibi</h3>
                                <p>BMI hesaplama ve detaylı ilerleme takip sistemi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sağ Taraf - Giriş Formu -->
            <div class="auth-form-wrapper">
                <div class="auth-form-card">
                    <div class="auth-form-header">
                        <h2 class="auth-form-title">Giriş Yap</h2>
                        <p class="auth-form-subtitle">Hesabınıza giriş yaparak devam edin</p>
                    </div>
                    
                    <div id="alert-container"></div>
                    
                    <form id="login-form" class="auth-form">
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
                                placeholder="kullaniciadi@email.com"
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
                            Hesabınız yok mu? 
                            <a href="<?php echo SITE_URL; ?>user/register.php" class="auth-link">
                                <strong>Kayıt Ol</strong>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$extra_js = ['assets/js/auth.js'];
require_once '../includes/footer.php';
?>

