<?php
$page_title = 'İlerleme Takibi';
require_once '../includes/header.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Kullanıcı profil bilgilerini çek
$stmt = $db->prepare("SELECT u.*, up.height, up.weight, up.age, up.gender, up.bmi, up.goal FROM users u LEFT JOIN user_profiles up ON u.id = up.user_id WHERE u.id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Diyet planlarını çek
$stmt = $db->prepare("SELECT * FROM diet_plans WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$diet_plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="feature-page">
    <div class="container">
        <div class="feature-header">
            <div class="feature-header-icon">📈</div>
            <h1 class="section-title">İlerleme Takibi</h1>
            <p class="feature-subtitle">Kilo, boy ve diğer ölçümlerinizi takip ederek ilerlemenizi görün.</p>
        </div>

        <div class="feature-content">
            <div class="progress-stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📏</div>
                    <div class="stat-label">Boy</div>
                    <div class="stat-value"><?php echo $user['height'] ? $user['height'] . ' cm' : '-'; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">⚖️</div>
                    <div class="stat-label">Kilo</div>
                    <div class="stat-value"><?php echo $user['weight'] ? $user['weight'] . ' kg' : '-'; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📊</div>
                    <div class="stat-label">BMI</div>
                    <div class="stat-value"><?php echo $user['bmi'] ? number_format($user['bmi'], 1) : '-'; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🎯</div>
                    <div class="stat-label">Hedef</div>
                    <div class="stat-value">
                        <?php
                        $goals = [
                            'weight_loss' => 'Kilo Verme',
                            'muscle_gain' => 'Kas Kazanma',
                            'maintenance' => 'Kilo Koruma',
                            'endurance' => 'Dayanıklılık'
                        ];
                        echo $user['goal'] ? ($goals[$user['goal']] ?? '-') : '-';
                        ?>
                    </div>
                </div>
            </div>

            <div class="feature-info-card">
                <h2>Profil Bilgilerinizi Güncelleyin</h2>
                <p style="color: #ccc; margin-bottom: 1.5rem;">İlerlemenizi doğru takip edebilmek için profil bilgilerinizi güncel tutun.</p>
                <a href="<?php echo SITE_URL; ?>user/profile.php" class="btn btn-primary">Profili Düzenle</a>
            </div>

            <?php if (!empty($diet_plans)): ?>
            <div class="diet-plans-section">
                <h2>Aktif Diyet Planlarım</h2>
                <div class="diet-plans-grid">
                    <?php foreach ($diet_plans as $plan): ?>
                    <div class="diet-plan-card">
                        <h3><?php echo htmlspecialchars($plan['plan_name']); ?></h3>
                        <div class="plan-details">
                            <?php if ($plan['daily_calories']): ?>
                            <div class="plan-detail-item">
                                <span class="detail-label">Günlük Kalori:</span>
                                <span class="detail-value"><?php echo $plan['daily_calories']; ?> kcal</span>
                            </div>
                            <?php endif; ?>
                            <div class="plan-detail-item">
                                <span class="detail-label">Durum:</span>
                                <span class="detail-value status-<?php echo $plan['status']; ?>">
                                    <?php
                                    $statuses = [
                                        'active' => 'Aktif',
                                        'completed' => 'Tamamlandı',
                                        'cancelled' => 'İptal Edildi'
                                    ];
                                    echo $statuses[$plan['status']] ?? $plan['status'];
                                    ?>
                                </span>
                            </div>
                            <?php if ($plan['start_date']): ?>
                            <div class="plan-detail-item">
                                <span class="detail-label">Başlangıç:</span>
                                <span class="detail-value"><?php echo date('d.m.Y', strtotime($plan['start_date'])); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        <a href="<?php echo SITE_URL; ?>user/diet-plan-detail.php?id=<?php echo $plan['id']; ?>" class="btn btn-secondary btn-block">Detayları Gör</a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="info-message">
                <p>Henüz aktif diyet planınız bulunmamaktadır.</p>
                <a href="<?php echo SITE_URL; ?>user/diet-plans.php" class="btn btn-primary" style="margin-top: 1rem;">Diyet Planı Oluştur</a>
            </div>
            <?php endif; ?>

            <div class="feature-info-card">
                <h2>BMI Hesaplayıcı</h2>
                <p style="color: #ccc; margin-bottom: 1.5rem;">Vücut kitle indeksinizi hesaplayarak sağlık durumunuzu takip edin.</p>
                <a href="<?php echo SITE_URL; ?>user/bmi-calculator.php" class="btn btn-primary">BMI Hesapla</a>
            </div>
        </div>
    </div>
</section>

<?php
$extra_css = '<style>
.feature-page {
    padding: 2rem 0;
    min-height: calc(100vh - 200px);
}

.feature-header {
    text-align: center;
    margin-bottom: 3rem;
}

.feature-header-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.feature-subtitle {
    color: #888;
    font-size: 1.1rem;
    margin-top: 0.5rem;
}

.feature-content {
    max-width: 1200px;
    margin: 0 auto;
}

.progress-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.stat-card {
    background: #1a1a1a;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    border: 1px solid #333;
    transition: all 0.3s;
}

.stat-card:hover {
    border-color: #ffd700;
    transform: translateY(-5px);
}

.stat-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.stat-label {
    color: #aaa;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.stat-value {
    color: #ffd700;
    font-size: 1.5rem;
    font-weight: bold;
}

.feature-info-card {
    background: #1a1a1a;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 3rem;
}

.feature-info-card h2 {
    color: #ffd700;
    margin-bottom: 1rem;
    font-size: 1.8rem;
}

.diet-plans-section h2 {
    color: #fff;
    margin-bottom: 2rem;
    font-size: 1.8rem;
}

.diet-plans-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.diet-plan-card {
    background: #1a1a1a;
    border-radius: 12px;
    padding: 2rem;
    border: 1px solid #333;
}

.diet-plan-card h3 {
    color: #fff;
    margin-bottom: 1.5rem;
    font-size: 1.3rem;
}

.plan-details {
    margin-bottom: 1.5rem;
}

.plan-detail-item {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid #333;
}

.plan-detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    color: #aaa;
}

.detail-value {
    color: #fff;
    font-weight: 500;
}

.status-active {
    color: #4ade80;
}

.status-completed {
    color: #ffd700;
}

.status-cancelled {
    color: #ef4444;
}

.info-message {
    background: #1a1a1a;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    color: #ccc;
    margin-bottom: 3rem;
}

@media (max-width: 768px) {
    .progress-stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .diet-plans-grid {
        grid-template-columns: 1fr;
    }
}
</style>';
require_once '../includes/footer.php';
?>

