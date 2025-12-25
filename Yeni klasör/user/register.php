<?php
$page_title = 'Kayıt Ol';
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
                    <h1 class="auth-welcome-title">PowerFit'e Katılın!</h1>
                    <p class="auth-welcome-text">
                        Sağlıklı yaşam yolculuğunuza bugün başlayın. Ücretsiz kayıt olun ve uzman ekibimizle hedeflerinize ulaşın.
                    </p>
                    
                    <div class="auth-features">
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">🎯</div>
                            <div class="auth-feature-text">
                                <h3>Kişisel Hedefler</h3>
                                <p>Kilo verme, kas kazanma veya dayanıklılık hedeflerinize ulaşın</p>
                            </div>
                        </div>
                        
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">👨‍⚕️</div>
                            <div class="auth-feature-text">
                                <h3>Uzman Danışmanlar</h3>
                                <p>Beslenme danışmanlarımızdan profesyonel destek alın</p>
                            </div>
                        </div>
                        
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">📈</div>
                            <div class="auth-feature-text">
                                <h3>Detaylı Takip</h3>
                                <p>Kilo, boy ve diğer ölçümlerinizi takip edin</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sağ Taraf - Kayıt Formu -->
            <div class="auth-form-wrapper">
                <div class="auth-form-card">
                    <div class="auth-form-header">
                        <h2 class="auth-form-title">Kayıt Ol</h2>
                        <p class="auth-form-subtitle">Ücretsiz hesap oluşturun ve başlayın</p>
                    </div>
                    
                    <div id="alert-container"></div>
                    
                    <form id="register-form" class="auth-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name" class="form-label">
                                    <span class="label-icon">👤</span>
                                    Ad
                                </label>
                                <input 
                                    type="text" 
                                    id="first_name" 
                                    name="first_name" 
                                    class="form-input" 
                                    placeholder="Adınız"
                                    required
                                    autocomplete="given-name"
                                >
                            </div>
                            
                            <div class="form-group">
                                <label for="last_name" class="form-label">
                                    <span class="label-icon">👤</span>
                                    Soyad
                                </label>
                                <input 
                                    type="text" 
                                    id="last_name" 
                                    name="last_name" 
                                    class="form-input" 
                                    placeholder="Soyadınız"
                                    required
                                    autocomplete="family-name"
                                >
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="username" class="form-label">
                                <span class="label-icon">@</span>
                                Kullanıcı Adı
                            </label>
                            <input 
                                type="text" 
                                id="username" 
                                name="username" 
                                class="form-input" 
                                placeholder="kullaniciadi"
                                required
                                autocomplete="username"
                            >
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">
                                <span class="label-icon">📧</span>
                                E-posta
                            </label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                class="form-input" 
                                placeholder="ornek@email.com"
                                required
                                autocomplete="email"
                            >
                        </div>
                        
                        <div class="form-group">
                            <label for="phone" class="form-label">
                                <span class="label-icon">📱</span>
                                Telefon (İsteğe Bağlı)
                            </label>
                            <input 
                                type="tel" 
                                id="phone" 
                                name="phone" 
                                class="form-input" 
                                placeholder="05XX XXX XX XX"
                                autocomplete="tel"
                            >
                        </div>
                        
                        <div class="form-row">
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
                                    placeholder="En az 6 karakter"
                                    required
                                    minlength="6"
                                    autocomplete="new-password"
                                >
                            </div>
                            
                            <div class="form-group">
                                <label for="password_confirm" class="form-label">
                                    <span class="label-icon">🔒</span>
                                    Şifre Tekrar
                                </label>
                                <input 
                                    type="password" 
                                    id="password_confirm" 
                                    name="password_confirm" 
                                    class="form-input" 
                                    placeholder="Şifrenizi tekrar girin"
                                    required
                                    minlength="6"
                                    autocomplete="new-password"
                                >
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block btn-large">
                                <span>Kayıt Ol</span>
                                <span class="btn-icon">→</span>
                            </button>
                        </div>
                    </form>
                    
                    <div class="auth-form-footer">
                        <p class="auth-form-footer-text">
                            Zaten hesabınız var mı? 
                            <a href="<?php echo SITE_URL; ?>user/login.php" class="auth-link">
                                <strong>Giriş Yap</strong>
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

