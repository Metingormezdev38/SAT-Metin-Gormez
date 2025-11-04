<?php
session_start();
require_once 'config.php';
require_once 'includes/header.php';

$pageTitle = 'Ana Sayfa';
?>

<div class="hero">
    <div class="container">
        <h1>Sağlıklı Yaşam İçin Yanınızdayız</h1>
        <p>Modern ekipmanlar, uzman eğitmenler ve çeşitli derslerle hedeflerinize ulaşın</p>
        <a href="classes.php" class="btn">Ders Programını Görüntüle</a>
        <?php if (!isLoggedIn()): ?>
            <a href="register.php" class="btn btn-secondary" style="margin-left: 1rem;">Hemen Üye Ol</a>
        <?php endif; ?>
    </div>
</div>

<div class="container">
    <section style="margin: 4rem 0;">
        <h2 style="text-align: center; margin-bottom: 2rem; color: var(--primary-black);">Neden Bizi Seçmelisiniz?</h2>
        <div class="classes-grid">
            <div class="class-card">
                <h3>Uzman Eğitmenler</h3>
                <p>Alanında uzman eğitmenlerimizle güvenli ve etkili antrenmanlar yapın.</p>
            </div>
            <div class="class-card">
                <h3>Modern Ekipmanlar</h3>
                <p>En yeni fitness ekipmanları ile maksimum performans elde edin.</p>
            </div>
            <div class="class-card">
                <h3>Esnek Program</h3>
                <p>Size uygun saatlerde derslere katılın ve rezervasyonlarınızı kolayca yönetin.</p>
            </div>
        </div>
    </section>

    <section style="margin: 4rem 0; text-align: center;">
        <h2 style="margin-bottom: 1rem; color: var(--primary-black);">Hızlı Erişim</h2>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="classes.php" class="btn">Ders Programı</a>
            <a href="bmi.php" class="btn btn-secondary">BMI Hesapla</a>
            <a href="contact.php" class="btn btn-secondary">İletişim</a>
        </div>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?>
