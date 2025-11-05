<?php
$page_title = 'Ana Sayfa';
require_once 'includes/header.php';
?>

<section class="hero">
    <div class="hero-container">
        <div class="hero-content">
            <h1 class="hero-title">PowerFit Spor Salonu</h1>
            <p class="hero-subtitle">Sağlıklı yaşam yolculuğunuzda yanınızdayız</p>
            <p class="hero-description">Modern ekipmanlar, uzman antrenörler ve kişiselleştirilmiş programlarla hedeflerinize ulaşın.</p>
            <div class="hero-buttons">
                <?php if (!isLoggedIn()): ?>
                    <a href="user/register.php" class="btn btn-primary">Hemen Başla</a>
                    <a href="user/login.php" class="btn btn-secondary">Giriş Yap</a>
                <?php else: ?>
                    <a href="user/dashboard.php" class="btn btn-primary">Panelime Git</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="hero-image">
            <?php if (file_exists(__DIR__ . '/assets/images/gym-hero.jpg')): ?>
                <img src="<?php echo SITE_URL; ?>assets/images/gym-hero.jpg" alt="PowerFit Spor Salonu" class="hero-img">
            <?php else: ?>
                <div class="hero-placeholder">
                    <div class="hero-placeholder-icon">💪</div>
                    <div class="hero-placeholder-text">PowerFit</div>
                    <p style="color: var(--gray-light); margin-top: 1rem; font-size: 0.9rem;">Fotoğrafınızı assets/images/gym-hero.jpg olarak ekleyin</p>
                </div>
            <?php endif; ?>
            <div class="hero-image-overlay"></div>
        </div>
    </div>
</section>

<section class="features">
    <h2 class="section-title">Özelliklerimiz</h2>
    <div class="features-grid">
        <div class="feature-card fade-in">
            <div class="feature-icon">💪</div>
            <h3 class="feature-title">Kişisel Antrenör</h3>
            <p class="feature-description">Uzman antrenörlerimizle birlikte size özel antrenman programları hazırlıyoruz.</p>
        </div>
        <div class="feature-card fade-in">
            <div class="feature-icon">🥗</div>
            <h3 class="feature-title">Beslenme Planı</h3>
            <p class="feature-description">Hedeflerinize uygun kişiselleştirilmiş beslenme planları oluşturuyoruz.</p>
        </div>
        <div class="feature-card fade-in">
            <div class="feature-icon">📊</div>
            <h3 class="feature-title">BMI Takibi</h3>
            <p class="feature-description">Vücut kitle indeksinizi hesaplayın ve sağlık durumunuzu takip edin.</p>
        </div>
        <div class="feature-card fade-in">
            <div class="feature-icon">👨‍⚕️</div>
            <h3 class="feature-title">Beslenme Danışmanı</h3>
            <p class="feature-description">Uzman beslenme danışmanlarımızdan profesyonel destek alın.</p>
        </div>
        <div class="feature-card fade-in">
            <div class="feature-icon">📈</div>
            <h3 class="feature-title">İlerleme Takibi</h3>
            <p class="feature-description">Kilo, boy ve diğer ölçümlerinizi takip ederek ilerlemenizi görün.</p>
        </div>
        <div class="feature-card fade-in">
            <div class="feature-icon">🎯</div>
            <h3 class="feature-title">Hedef Belirleme</h3>
            <p class="feature-description">Kilo verme, kas kazanma veya dayanıklılık hedeflerinize ulaşın.</p>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

