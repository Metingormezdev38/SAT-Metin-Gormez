<?php
$page_title = 'Hedef Belirleme';
require_once '../includes/header.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Kullanıcı profil bilgilerini çek
$stmt = $db->prepare("SELECT u.*, up.height, up.weight, up.age, up.gender, up.activity_level, up.goal FROM users u LEFT JOIN user_profiles up ON u.id = up.user_id WHERE u.id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$goals = [
    'weight_loss' => [
        'name' => 'Kilo Verme',
        'icon' => '🔥',
        'description' => 'Sağlıklı bir şekilde kilo vererek ideal kilonuza ulaşın.',
        'features' => [
            'Kişiselleştirilmiş kalori açığı planı',
            'Kardiyovasküler egzersiz programları',
            'Beslenme danışmanlığı',
            'Düzenli ilerleme takibi'
        ]
    ],
    'muscle_gain' => [
        'name' => 'Kas Kazanma',
        'icon' => '💪',
        'description' => 'Kas kütlenizi artırarak güçlü ve fit bir vücuda sahip olun.',
        'features' => [
            'Kuvvet antrenman programları',
            'Protein odaklı beslenme planı',
            'İlerleme takibi ve ölçümler',
            'Antrenör desteği'
        ]
    ],
    'maintenance' => [
        'name' => 'Kilo Koruma',
        'icon' => '⚖️',
        'description' => 'Mevcut kilonuzu koruyarak sağlıklı yaşam tarzınızı sürdürün.',
        'features' => [
            'Dengeli beslenme planı',
            'Düzenli egzersiz programı',
            'Sağlık takibi',
            'Yaşam tarzı rehberliği'
        ]
    ],
    'endurance' => [
        'name' => 'Dayanıklılık',
        'icon' => '🏃',
        'description' => 'Kardiyovasküler dayanıklılığınızı artırarak performansınızı yükseltin.',
        'features' => [
            'Kardiyovasküler antrenman programları',
            'Dayanıklılık egzersizleri',
            'Beslenme optimizasyonu',
            'Performans takibi'
        ]
    ]
];
?>

<section class="feature-page">
    <div class="container">
        <div class="feature-header">
            <div class="feature-header-icon">🎯</div>
            <h1 class="section-title">Hedef Belirleme</h1>
            <p class="feature-subtitle">Kilo verme, kas kazanma veya dayanıklılık hedeflerinize ulaşın.</p>
        </div>

        <div class="feature-content">
            <?php if ($user['goal']): ?>
            <div class="current-goal-card">
                <h2>Mevcut Hedefiniz</h2>
                <div class="current-goal">
                    <div class="goal-icon-large"><?php echo $goals[$user['goal']]['icon']; ?></div>
                    <div class="goal-info">
                        <h3><?php echo $goals[$user['goal']]['name']; ?></h3>
                        <p><?php echo $goals[$user['goal']]['description']; ?></p>
                    </div>
                </div>
                <a href="<?php echo SITE_URL; ?>user/profile.php" class="btn btn-secondary">Hedefi Değiştir</a>
            </div>
            <?php endif; ?>

            <div class="goals-section">
                <h2><?php echo $user['goal'] ? 'Diğer Hedefler' : 'Hedefinizi Seçin'; ?></h2>
                <div class="goals-grid">
                    <?php foreach ($goals as $goal_key => $goal_data): ?>
                    <div class="goal-card <?php echo ($user['goal'] === $goal_key) ? 'active' : ''; ?>">
                        <div class="goal-icon"><?php echo $goal_data['icon']; ?></div>
                        <h3><?php echo $goal_data['name']; ?></h3>
                        <p class="goal-description"><?php echo $goal_data['description']; ?></p>
                        <ul class="goal-features">
                            <?php foreach ($goal_data['features'] as $feature): ?>
                            <li>✓ <?php echo htmlspecialchars($feature); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php if ($user['goal'] !== $goal_key): ?>
                        <a href="<?php echo SITE_URL; ?>user/profile.php" class="btn btn-primary btn-block">Bu Hedefi Seç</a>
                        <?php else: ?>
                        <div class="goal-active-badge">Aktif Hedef</div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="feature-info-card">
                <h2>Hedefinize Ulaşmak İçin</h2>
                <ul class="feature-list">
                    <li>✅ Profil bilgilerinizi güncel tutun</li>
                    <li>✅ Düzenli olarak ölçümlerinizi kaydedin</li>
                    <li>✅ Beslenme planlarınıza uyun</li>
                    <li>✅ Antrenman programlarınızı takip edin</li>
                    <li>✅ İlerlemenizi düzenli olarak kontrol edin</li>
                </ul>
                <div style="margin-top: 2rem; display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="<?php echo SITE_URL; ?>user/profile.php" class="btn btn-primary">Profili Düzenle</a>
                    <a href="<?php echo SITE_URL; ?>user/progress-tracking.php" class="btn btn-secondary">İlerlemeyi Gör</a>
                    <a href="<?php echo SITE_URL; ?>user/diet-plans.php" class="btn btn-secondary">Diyet Planları</a>
                </div>
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

.current-goal-card {
    background: #1a1a1a;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 3rem;
    border: 2px solid #ffd700;
}

.current-goal-card h2 {
    color: #ffd700;
    margin-bottom: 1.5rem;
    font-size: 1.8rem;
}

.current-goal {
    display: flex;
    align-items: center;
    gap: 2rem;
    margin-bottom: 1.5rem;
}

.goal-icon-large {
    font-size: 4rem;
}

.goal-info h3 {
    color: #fff;
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.goal-info p {
    color: #ccc;
    margin: 0;
}

.goals-section h2 {
    color: #fff;
    margin-bottom: 2rem;
    font-size: 1.8rem;
}

.goals-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.goal-card {
    background: #1a1a1a;
    border-radius: 12px;
    padding: 2rem;
    border: 1px solid #333;
    transition: all 0.3s;
    text-align: center;
}

.goal-card:hover {
    border-color: #ffd700;
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(255, 215, 0, 0.2);
}

.goal-card.active {
    border-color: #ffd700;
    background: linear-gradient(135deg, #1a1a1a 0%, #2a1a00 100%);
}

.goal-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.goal-card h3 {
    color: #fff;
    margin-bottom: 1rem;
    font-size: 1.3rem;
}

.goal-description {
    color: #aaa;
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.goal-features {
    list-style: none;
    padding: 0;
    margin: 0 0 1.5rem 0;
    text-align: left;
}

.goal-features li {
    color: #ccc;
    padding: 0.5rem 0;
    font-size: 0.9rem;
}

.goal-active-badge {
    background: #ffd700;
    color: #000;
    padding: 0.75rem;
    border-radius: 8px;
    font-weight: bold;
    text-align: center;
}

.feature-info-card {
    background: #1a1a1a;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 3rem;
}

.feature-info-card h2 {
    color: #ffd700;
    margin-bottom: 1.5rem;
    font-size: 1.8rem;
}

.feature-list {
    list-style: none;
    padding: 0;
    margin: 0 0 1.5rem 0;
}

.feature-list li {
    color: #ccc;
    padding: 0.75rem 0;
    font-size: 1.1rem;
    border-bottom: 1px solid #333;
}

.feature-list li:last-child {
    border-bottom: none;
}

@media (max-width: 768px) {
    .current-goal {
        flex-direction: column;
        text-align: center;
    }
    
    .goals-grid {
        grid-template-columns: 1fr;
    }
}
</style>';
require_once '../includes/footer.php';
?>

