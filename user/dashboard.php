<?php
$page_title = 'Kullanıcı Paneli';
require_once '../includes/header.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Kullanıcı profil bilgilerini çek
$stmt = $db->prepare("SELECT u.*, up.height, up.weight, up.age, up.gender, up.bmi, up.goal FROM users u LEFT JOIN user_profiles up ON u.id = up.user_id WHERE u.id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// İstatistikler
$diet_plans_count = $db->prepare("SELECT COUNT(*) FROM diet_plans WHERE user_id = ?");
$diet_plans_count->execute([$user_id]);
$diet_count = $diet_plans_count->fetchColumn();

$consultant_bookings_count = $db->prepare("SELECT COUNT(*) FROM consultant_bookings WHERE user_id = ?");
$consultant_bookings_count->execute([$user_id]);
$booking_count = $consultant_bookings_count->fetchColumn();
?>

<div class="dashboard">
    <h1 class="section-title">Hoş Geldiniz, <?php echo htmlspecialchars($user['first_name']); ?>!</h1>
    
    <div class="dashboard-grid">
        <div class="dashboard-card">
            <div class="dashboard-card-label">Aktif Diyet Planları</div>
            <div class="dashboard-card-value"><?php echo $diet_count; ?></div>
        </div>
        <div class="dashboard-card">
            <div class="dashboard-card-label">Danışman Randevuları</div>
            <div class="dashboard-card-value"><?php echo $booking_count; ?></div>
        </div>
        <div class="dashboard-card">
            <div class="dashboard-card-label">BMI Değeriniz</div>
            <div class="dashboard-card-value"><?php echo $user['bmi'] ? number_format($user['bmi'], 1) : '-'; ?></div>
        </div>
    </div>

    <div class="card">
        <h2 class="card-title">Profil Bilgilerim</h2>
        <div class="form-group">
            <label class="form-label">Ad Soyad:</label>
            <p><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
        </div>
        <div class="form-group">
            <label class="form-label">E-posta:</label>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
        </div>
        <?php if ($user['height']): ?>
        <div class="form-group">
            <label class="form-label">Boy:</label>
            <p><?php echo $user['height']; ?> cm</p>
        </div>
        <?php endif; ?>
        <?php if ($user['weight']): ?>
        <div class="form-group">
            <label class="form-label">Kilo:</label>
            <p><?php echo $user['weight']; ?> kg</p>
        </div>
        <?php endif; ?>
        <div class="form-group">
            <a href="<?php echo SITE_URL; ?>user/profile.php" class="btn btn-primary">Profili Düzenle</a>
        </div>
    </div>

    <div class="card">
        <h2 class="card-title">Hızlı Erişim</h2>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="<?php echo SITE_URL; ?>user/diet-plans.php" class="btn btn-primary">Diyet Listelerim</a>
            <a href="<?php echo SITE_URL; ?>user/consultants.php" class="btn btn-primary">Danışman Seç</a>
            <a href="<?php echo SITE_URL; ?>user/bmi-calculator.php" class="btn btn-primary">BMI Hesapla</a>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

