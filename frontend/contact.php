<?php
session_start();
require_once 'config.php';
require_once 'includes/header.php';

$pageTitle = 'İletişim';
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';

    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Tüm alanlar doldurulmalıdır';
    } else {
        // Burada gerçek bir e-posta gönderimi yapılabilir
        // Şimdilik sadece başarı mesajı gösteriyoruz
        $success = 'Mesajınız başarıyla gönderildi! En kısa sürede size dönüş yapacağız.';
    }
}
?>

<div class="container">
    <h1 style="text-align: center; margin-bottom: 2rem; color: var(--primary-black);">İletişim</h1>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 3rem; margin-top: 2rem;">
        <div class="form-container" style="margin: 0;">
            <h2 style="margin-bottom: 1.5rem; color: var(--primary-black);">Bize Ulaşın</h2>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Ad Soyad</label>
                    <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="email">E-posta</label>
                    <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="message">Mesaj</label>
                    <textarea id="message" name="message" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn" style="width: 100%;">Gönder</button>
            </form>
        </div>

        <div>
            <div class="contact-info">
                <div class="contact-card">
                    <h3>Adres</h3>
                    <p>Örnek Mahalle, Örnek Sokak No: 123<br>İstanbul, Türkiye</p>
                </div>
                <div class="contact-card">
                    <h3>Telefon</h3>
                    <p>+90 (XXX) XXX XX XX</p>
                </div>
                <div class="contact-card">
                    <h3>E-posta</h3>
                    <p>info@sporsalonu.com</p>
                </div>
                <div class="contact-card">
                    <h3>Çalışma Saatleri</h3>
                    <p>Pazartesi - Cuma: 06:00 - 23:00<br>Cumartesi - Pazar: 08:00 - 20:00</p>
                </div>
            </div>

            <div class="map-container">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d385396.3210459932!2d28.682534875000002!3d41.0053705!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14caa7040068086b%3A0xe1ccfe98bc01b0d0!2zxLBzdGFuYnVs!5e0!3m2!1str!2str!4v1234567890123!5m2!1str!2str"
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
