    <!-- Auth Modal -->
    <div id="authModal" class="modal" style="display: none;">
        <div class="modal-content auth-modal-content">
            <button class="modal-close" onclick="closeAuthModal()" aria-label="Kapat">&times;</button>
            
            <!-- Login Form -->
            <div id="loginModal" class="auth-modal-form" style="display: none;">
                <div class="auth-form-header">
                    <h2 class="auth-form-title">Giriş Yap</h2>
                    <p class="auth-form-subtitle">Hesabınıza giriş yaparak devam edin</p>
                </div>
                
                <div id="login-alert-container"></div>
                
                <form id="modal-login-form" class="auth-form">
                    <div class="form-group">
                        <label for="modal-username" class="form-label">
                            <span class="label-icon">👤</span>
                            Kullanıcı Adı veya E-posta
                        </label>
                        <input 
                            type="text" 
                            id="modal-username" 
                            name="username" 
                            class="form-input" 
                            placeholder="kullaniciadi@email.com"
                            required
                            autocomplete="username"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="modal-password" class="form-label">
                            <span class="label-icon">🔒</span>
                            Şifre
                        </label>
                        <input 
                            type="password" 
                            id="modal-password" 
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
                        <a href="#" class="auth-link" onclick="switchAuthModal('register'); return false;">
                            <strong>Kayıt Ol</strong>
                        </a>
                    </p>
                </div>
            </div>
            
            <!-- Register Form -->
            <div id="registerModal" class="auth-modal-form" style="display: none;">
                <div class="auth-form-header">
                    <h2 class="auth-form-title">Kayıt Ol</h2>
                    <p class="auth-form-subtitle">Ücretsiz hesap oluşturun ve başlayın</p>
                </div>
                
                <div id="register-alert-container"></div>
                
                <form id="modal-register-form" class="auth-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="modal-first_name" class="form-label">
                                <span class="label-icon">👤</span>
                                Ad
                            </label>
                            <input 
                                type="text" 
                                id="modal-first_name" 
                                name="first_name" 
                                class="form-input" 
                                placeholder="Adınız"
                                required
                                autocomplete="given-name"
                            >
                        </div>
                        
                        <div class="form-group">
                            <label for="modal-last_name" class="form-label">
                                <span class="label-icon">👤</span>
                                Soyad
                            </label>
                            <input 
                                type="text" 
                                id="modal-last_name" 
                                name="last_name" 
                                class="form-input" 
                                placeholder="Soyadınız"
                                required
                                autocomplete="family-name"
                            >
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="modal-register-username" class="form-label">
                            <span class="label-icon">@</span>
                            Kullanıcı Adı
                        </label>
                        <input 
                            type="text" 
                            id="modal-register-username" 
                            name="username" 
                            class="form-input" 
                            placeholder="kullaniciadi"
                            required
                            autocomplete="username"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="modal-email" class="form-label">
                            <span class="label-icon">📧</span>
                            E-posta
                        </label>
                        <input 
                            type="email" 
                            id="modal-email" 
                            name="email" 
                            class="form-input" 
                            placeholder="ornek@email.com"
                            required
                            autocomplete="email"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="modal-phone" class="form-label">
                            <span class="label-icon">📱</span>
                            Telefon (İsteğe Bağlı)
                        </label>
                        <input 
                            type="tel" 
                            id="modal-phone" 
                            name="phone" 
                            class="form-input" 
                            placeholder="05XX XXX XX XX"
                            autocomplete="tel"
                        >
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="modal-register-password" class="form-label">
                                <span class="label-icon">🔒</span>
                                Şifre
                            </label>
                            <input 
                                type="password" 
                                id="modal-register-password" 
                                name="password" 
                                class="form-input" 
                                placeholder="En az 6 karakter"
                                required
                                minlength="6"
                                autocomplete="new-password"
                            >
                        </div>
                        
                        <div class="form-group">
                            <label for="modal-password_confirm" class="form-label">
                                <span class="label-icon">🔒</span>
                                Şifre Tekrar
                            </label>
                            <input 
                                type="password" 
                                id="modal-password_confirm" 
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
                        <a href="#" class="auth-link" onclick="switchAuthModal('login'); return false;">
                            <strong>Giriş Yap</strong>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <p class="footer-text">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Tüm hakları saklıdır.</p>
            <div class="footer-links">
                <a href="<?php echo SITE_URL; ?>" class="footer-link">Ana Sayfa</a>
                <a href="<?php echo SITE_URL; ?>user/memberships.php" class="footer-link">Üyelikler</a>
                <a href="<?php echo SITE_URL; ?>user/bmi-calculator.php" class="footer-link">BMI Hesapla</a>
                <?php if (isLoggedIn() && isAdmin()): ?>
                    <a href="<?php echo SITE_URL; ?>admin/dashboard.php" class="footer-link">Admin Panel</a>
                <?php endif; ?>
            </div>
        </div>
    </footer>
    <?php if (isset($extra_css)): ?>
        <?php echo $extra_css; ?>
    <?php endif; ?>
    <script src="<?php echo SITE_URL; ?>assets/js/main.js"></script>
    <script src="<?php echo SITE_URL; ?>assets/js/auth-modal.js"></script>
    <?php if (isset($extra_js)): ?>
        <?php foreach ($extra_js as $js_file): ?>
            <script src="<?php echo SITE_URL . $js_file; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>

