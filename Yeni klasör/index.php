<?php
$page_title = 'Ana Sayfa';
require_once 'includes/header.php';
?>

<section class="hero">
    <div class="hero-container">
        <div class="hero-grid">
            <div class="hero-left">
                <div class="hero-badge">Yeni Nesil Spor Deneyimi</div>
                <h1 class="hero-title">PowerFit Spor Salonu</h1>
                <p class="hero-description">Modern ekipmanlar, uzman antrenörler ve kişiselleştirilmiş programlarla hedeflerinize ulaşın.</p>

                <div class="hero-actions">
                    <?php if (!isLoggedIn()): ?>
                        <a href="#" class="btn btn-primary btn-large" onclick="openAuthModal('register'); return false;">Hemen Başla</a>
                        <a href="#" class="btn btn-secondary btn-large" onclick="openAuthModal('login'); return false;">Giriş Yap</a>
                    <?php else: ?>
                        <a href="<?php echo SITE_URL; ?>user/dashboard.php" class="btn btn-primary btn-large">Panelime Git</a>
                    <?php endif; ?>
                    <a href="<?php echo SITE_URL; ?>user/memberships.php" class="btn btn-secondary btn-large">Üyelik Paketleri</a>
                </div>

                <div class="hero-stats">
                    <div class="hero-stat">
                        <div class="hero-stat-value">24/7</div>
                        <div class="hero-stat-label">Açık Salon</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-value">+30</div>
                        <div class="hero-stat-label">Uzman Antrenör</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-value">#1</div>
                        <div class="hero-stat-label">Kişisel Planlar</div>
                    </div>
                </div>
            </div>

            <div class="hero-right">
                <div class="hero-image-card">
                    <div class="hero-tag">Hedefine Odaklan</div>
                    <div class="hero-slider">
                        <div class="hero-slider-container">
                            <?php
                            // Görsel listesi - assets/images/ klasörüne ekleyebilirsiniz
                            $hero_images = [
                                'gym-hero.jpg',
                                'gym-hero-2.jpg',
                                'gym-hero-3.jpg',
                                'gym-hero-4.jpg'
                            ];
                            
                            $active_images = [];
                            foreach ($hero_images as $img) {
                                if (file_exists(__DIR__ . '/assets/images/' . $img)) {
                                    $active_images[] = $img;
                                }
                            }
                            
                            // Eğer hiç görsel yoksa placeholder göster
                            if (empty($active_images)):
                            ?>
                                <div class="hero-slide active">
                                    <div class="hero-placeholder">
                                        <div class="hero-placeholder-icon">💪</div>
                                        <div class="hero-placeholder-text">PowerFit</div>
                                        <p class="hero-placeholder-note">Görsellerinizi assets/images/ klasörüne ekleyin</p>
                                    </div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($active_images as $index => $img): ?>
                                <div class="hero-slide <?php echo $index === 0 ? 'active' : ''; ?>">
                                    <img src="<?php echo SITE_URL; ?>assets/images/<?php echo $img; ?>" alt="PowerFit Spor Salonu" class="hero-img">
                                    <div class="hero-image-overlay"></div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Slider Navigation Dots -->
                        <?php if (!empty($active_images) && count($active_images) > 1): ?>
                        <div class="hero-slider-dots">
                            <?php foreach ($active_images as $index => $img): ?>
                            <button class="slider-dot <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>" aria-label="Slide <?php echo $index + 1; ?>"></button>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Slider Navigation Arrows -->
                        <button class="slider-arrow slider-prev" aria-label="Önceki görsel">‹</button>
                        <button class="slider-arrow slider-next" aria-label="Sonraki görsel">›</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="features">
    <h2 class="section-title">Özelliklerimiz</h2>
    <div class="features-grid">
        <a href="<?php echo SITE_URL; ?>user/personal-trainer.php" class="feature-card fade-in">
            <div class="feature-icon">💪</div>
            <h3 class="feature-title">Kişisel Antrenör</h3>
            <p class="feature-description">Uzman antrenörlerimizle birlikte size özel antrenman programları hazırlıyoruz.</p>
        </a>
        <a href="<?php echo SITE_URL; ?>user/diet-plans.php" class="feature-card fade-in">
            <div class="feature-icon">🥗</div>
            <h3 class="feature-title">Beslenme Planı</h3>
            <p class="feature-description">Hedeflerinize uygun kişiselleştirilmiş beslenme planları oluşturuyoruz.</p>
        </a>
        <a href="<?php echo SITE_URL; ?>user/bmi-calculator.php" class="feature-card fade-in">
            <div class="feature-icon">📊</div>
            <h3 class="feature-title">BMI Takibi</h3>
            <p class="feature-description">Vücut kitle indeksinizi hesaplayın ve sağlık durumunuzu takip edin.</p>
        </a>
        <a href="<?php echo SITE_URL; ?>user/nutrition-consultant.php" class="feature-card fade-in">
            <div class="feature-icon">👨‍⚕️</div>
            <h3 class="feature-title">Beslenme Danışmanı</h3>
            <p class="feature-description">Uzman beslenme danışmanlarımızdan profesyonel destek alın.</p>
        </a>
        <a href="<?php echo SITE_URL; ?>user/progress-tracking.php" class="feature-card fade-in">
            <div class="feature-icon">📈</div>
            <h3 class="feature-title">İlerleme Takibi</h3>
            <p class="feature-description">Kilo, boy ve diğer ölçümlerinizi takip ederek ilerlemenizi görün.</p>
        </a>
        <a href="<?php echo SITE_URL; ?>user/goal-setting.php" class="feature-card fade-in">
            <div class="feature-icon">🎯</div>
            <h3 class="feature-title">Hedef Belirleme</h3>
            <p class="feature-description">Kilo verme, kas kazanma veya dayanıklılık hedeflerinize ulaşın.</p>
        </a>
    </div>
</section>

<?php
$extra_js = ['assets/js/hero-slider.js'];
require_once 'includes/footer.php';
?>

