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

// Aktif üyelik bilgilerini çek
$stmt = $db->prepare("SELECT * FROM user_memberships WHERE user_id = ? AND status = 'active' ORDER BY end_date DESC LIMIT 1");
$stmt->execute([$user_id]);
$membership = $stmt->fetch();

// Üyelik süre metinleri
$period_texts = [
    'monthly' => 'Aylık',
    'quarterly' => '3 Aylık',
    'yearly' => '1 Yıllık'
];

// Üyelik ikonları ve renkleri
$membership_icons = ['Temel' => '💪', 'Premium' => '⭐', 'VIP' => '👑'];
$membership_colors = ['Temel' => '#4ade80', 'Premium' => '#ffd700', 'VIP' => '#ff6b6b'];

// Üyelik bilgilerini hazırla
if ($membership) {
    $membership_icon = $membership_icons[$membership['membership_type']] ?? '⭐';
    $membership_color = $membership_colors[$membership['membership_type']] ?? '#ffd700';
    $membership_days_left = ceil((strtotime($membership['end_date']) - time()) / (60 * 60 * 24));
    $membership_total_days = ceil((strtotime($membership['end_date']) - strtotime($membership['start_date'])) / (60 * 60 * 24));
    $membership_progress = min(100, max(0, (($membership_total_days - $membership_days_left) / $membership_total_days) * 100));
}

// Seçili danışman bilgilerini çek (en son seçilen aktif danışman)
$stmt = $db->prepare("SELECT cb.*, c.specialization, c.experience_years, c.rating, c.price_per_session, u.first_name, u.last_name, u.email 
                      FROM consultant_bookings cb 
                      JOIN consultants c ON cb.consultant_id = c.id 
                      JOIN users u ON c.user_id = u.id 
                      WHERE cb.user_id = ? AND cb.status IN ('pending', 'confirmed') 
                      ORDER BY cb.created_at DESC LIMIT 1");
$stmt->execute([$user_id]);
$selected_consultant = $stmt->fetch();
?>

<div class="dashboard">
    <h1 class="section-title">Hoş Geldiniz, <?php echo htmlspecialchars($user['first_name']); ?>!</h1>
    
    <?php if (isset($_GET['payment']) && $_GET['payment'] === 'success'): ?>
    <div class="form-success" style="background: #4ade80; color: #fff; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
        Ödeme başarıyla tamamlandı! Üyeliğiniz aktif edildi.
    </div>
    <?php endif; ?>
    
    <div class="dashboard-grid">
        <div class="dashboard-card">
            <div class="dashboard-card-label">Aktif Diyet Planları</div>
            <div class="dashboard-card-value"><?php echo $diet_count; ?></div>
        </div>
        <div class="dashboard-card">
            <div class="dashboard-card-label">BMI Değeriniz</div>
            <div class="dashboard-card-value"><?php echo $user['bmi'] ? number_format($user['bmi'], 1) : '-'; ?></div>
        </div>
        <?php if ($membership): ?>
        <div class="membership-dashboard-card" style="--membership-color: <?php echo $membership_color; ?>">
            <div class="membership-card-icon" style="font-size: 3rem; margin-bottom: 1rem; animation: pulse 2s infinite;"><?php echo $membership_icon; ?></div>
            <div class="membership-card-label" style="color: #aaa; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 0.5rem;">Aktif Üyelik</div>
            <div class="membership-card-name" style="background: linear-gradient(135deg, <?php echo $membership_color; ?> 0%, <?php echo $membership_color; ?>dd 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 2rem; font-weight: 900; margin-bottom: 1rem;">
                <?php echo htmlspecialchars($membership['membership_type']); ?>
            </div>
            <div class="membership-card-period" style="color: #fff; font-size: 0.9rem; margin-bottom: 1.5rem; opacity: 0.8;">
                <?php echo $period_texts[$membership['membership_period']] ?? $membership['membership_period']; ?>
            </div>
            <div class="membership-progress-bar" style="width: 100%; height: 6px; background: rgba(255,255,255,0.1); border-radius: 10px; margin-bottom: 1rem; overflow: hidden;">
                <div style="height: 100%; background: linear-gradient(90deg, <?php echo $membership_color; ?> 0%, <?php echo $membership_color; ?>dd 100%); width: <?php echo $membership_progress; ?>%; border-radius: 10px; transition: width 0.3s ease;"></div>
            </div>
            <div class="membership-card-days" style="font-size: 1.1rem; font-weight: 700; color: <?php echo $membership_days_left <= 7 ? '#ff6b6b' : ($membership_days_left <= 30 ? '#ffa500' : '#4ade80'); ?>;">
                <?php 
                if ($membership_days_left > 0) {
                    echo $membership_days_left . ' gün kaldı';
                } elseif ($membership_days_left == 0) {
                    echo 'Bugün bitiyor!';
                } else {
                    echo 'Süresi dolmuş';
                }
                ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($membership): ?>
    <div class="membership-detail-card" style="--membership-color: <?php echo $membership_color; ?>">
        <div class="membership-detail-header">
            <div class="membership-detail-icon" style="font-size: 3.5rem; margin-bottom: 0.5rem;"><?php echo $membership_icon; ?></div>
            <h2 class="membership-detail-title" style="background: linear-gradient(135deg, <?php echo $membership_color; ?> 0%, <?php echo $membership_color; ?>dd 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 2rem; font-weight: 900; margin-bottom: 0.25rem;">
                <?php echo htmlspecialchars($membership['membership_type']); ?> Üyelik
            </h2>
            <p class="membership-detail-subtitle" style="color: #aaa; font-size: 0.95rem;">
                <?php echo $period_texts[$membership['membership_period']] ?? $membership['membership_period']; ?> Paket
            </p>
        </div>
        
        <div class="membership-detail-grid">
            <div class="membership-detail-item">
                <div class="membership-detail-label">📅 Başlangıç</div>
                <div class="membership-detail-value"><?php echo date('d.m.Y', strtotime($membership['start_date'])); ?></div>
            </div>
            <div class="membership-detail-item">
                <div class="membership-detail-label">⏰ Bitiş</div>
                <div class="membership-detail-value" style="color: <?php echo $membership_days_left <= 7 ? '#ff6b6b' : ($membership_days_left <= 30 ? '#ffa500' : '#4ade80'); ?>; font-weight: 700;">
                    <?php echo date('d.m.Y', strtotime($membership['end_date'])); ?>
                </div>
            </div>
            <div class="membership-detail-item">
                <div class="membership-detail-label">⏳ Kalan Süre</div>
                <div class="membership-detail-value" style="color: <?php echo $membership_days_left <= 7 ? '#ff6b6b' : ($membership_days_left <= 30 ? '#ffa500' : '#4ade80'); ?>; font-weight: 700; font-size: 1.3rem;">
                    <?php 
                    if ($membership_days_left > 0) {
                        echo $membership_days_left . ' gün';
                    } elseif ($membership_days_left == 0) {
                        echo 'Bugün bitiyor!';
                    } else {
                        echo 'Süresi dolmuş';
                    }
                    ?>
                </div>
            </div>
        </div>
        
        <div class="membership-detail-action">
            <a href="<?php echo SITE_URL; ?>user/memberships.php" class="btn btn-primary" style="background: linear-gradient(135deg, <?php echo $membership_color; ?> 0%, <?php echo $membership_color; ?>dd 100%); border: none; padding: 0.875rem 2rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
                Yeni Üyelik Al
            </a>
        </div>
    </div>
    <?php else: ?>
    <div class="membership-empty-card">
        <div style="text-align: center; padding: 3rem 2rem;">
            <div style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.5;">💳</div>
            <h2 class="card-title" style="margin-bottom: 0.5rem;">Üyelik Bulunmuyor</h2>
            <p style="color: #888; margin-bottom: 2rem; font-size: 1rem;">Henüz aktif üyeliğiniz bulunmamaktadır. Hemen üyelik paketlerimizi inceleyin!</p>
            <a href="<?php echo SITE_URL; ?>user/memberships.php" class="btn btn-primary" style="font-size: 1rem; padding: 1rem 2.5rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                Üyelik Paketlerini Görüntüle
            </a>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($selected_consultant): ?>
    <div class="consultant-dashboard-card">
        <div class="consultant-card-background"></div>
        <div class="consultant-card-content">
            <div class="consultant-card-header">
                <div class="consultant-avatar-large">
                    <?php echo strtoupper(substr($selected_consultant['first_name'], 0, 1) . substr($selected_consultant['last_name'], 0, 1)); ?>
                </div>
                <div class="consultant-header-info">
                    <h2 class="consultant-card-title">👨‍⚕️ Seçili Danışmanım</h2>
                    <h3 class="consultant-name"><?php echo htmlspecialchars($selected_consultant['first_name'] . ' ' . $selected_consultant['last_name']); ?></h3>
                    <p class="consultant-specialization"><?php echo htmlspecialchars($selected_consultant['specialization']); ?></p>
                </div>
            </div>
            
            <div class="consultant-card-stats">
                <div class="consultant-stat-item">
                    <div class="stat-icon">⭐</div>
                    <div class="stat-content">
                        <div class="stat-label">Puan</div>
                        <div class="stat-value"><?php echo number_format($selected_consultant['rating'], 1); ?></div>
                    </div>
                </div>
                <div class="consultant-stat-item">
                    <div class="stat-icon">💼</div>
                    <div class="stat-content">
                        <div class="stat-label">Deneyim</div>
                        <div class="stat-value"><?php echo $selected_consultant['experience_years']; ?> Yıl</div>
                    </div>
                </div>
                <div class="consultant-stat-item">
                    <div class="stat-icon">💰</div>
                    <div class="stat-content">
                        <div class="stat-label">Seans Ücreti</div>
                        <div class="stat-value"><?php echo number_format($selected_consultant['price_per_session'], 0, ',', '.'); ?> ₺</div>
                    </div>
                </div>
            </div>
            
            <div class="consultant-card-footer">
                <div class="consultant-footer-info">
                    <div class="footer-info-item">
                        <span class="footer-label">Seçim Tarihi:</span>
                        <span class="footer-value"><?php echo date('d.m.Y H:i', strtotime($selected_consultant['created_at'])); ?></span>
                    </div>
                    <div class="footer-info-item">
                        <span class="footer-label">Durum:</span>
                        <?php 
                        $status_text = [
                            'pending' => 'Beklemede',
                            'confirmed' => 'Onaylandı',
                            'completed' => 'Tamamlandı',
                            'cancelled' => 'İptal Edildi'
                        ];
                        $status_class = [
                            'pending' => 'consultant-status-pending',
                            'confirmed' => 'consultant-status-confirmed',
                            'completed' => 'consultant-status-completed',
                            'cancelled' => 'consultant-status-cancelled'
                        ];
                        $status = $selected_consultant['status'];
                        ?>
                        <span class="consultant-status-badge <?php echo $status_class[$status] ?? ''; ?>">
                            <?php echo $status_text[$status] ?? $status; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

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
            <a href="<?php echo SITE_URL; ?>user/bmi-calculator.php" class="btn btn-primary">BMI Hesapla</a>
            <a href="<?php echo SITE_URL; ?>user/memberships.php" class="btn btn-primary">Üyelik Paketleri</a>
        </div>
    </div>
</div>

<style>
/* Üyelik Dashboard Kartı */
.membership-dashboard-card {
    background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    border: 2px solid var(--membership-color, #ffd700);
    border-radius: 16px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 250px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4), 0 0 20px rgba(255, 215, 0, 0.1);
}

.membership-dashboard-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 215, 0, 0.1) 0%, transparent 70%);
    animation: rotate 20s linear infinite;
}

.membership-dashboard-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 12px 48px rgba(0, 0, 0, 0.5), 0 0 30px rgba(255, 215, 0, 0.2);
    border-color: var(--membership-color, #ffd700);
}

.membership-card-icon {
    position: relative;
    z-index: 1;
    filter: drop-shadow(0 0 10px rgba(255, 215, 0, 0.5));
}

.membership-card-name {
    position: relative;
    z-index: 1;
}

/* Üyelik Detay Kartı */
.membership-detail-card {
    background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    border: 2px solid var(--membership-color, #ffd700);
    border-radius: 20px;
    padding: 2.5rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4), 0 0 30px rgba(255, 215, 0, 0.15);
}

.membership-detail-card::before {
    content: '';
    position: absolute;
    top: -100px;
    right: -100px;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, var(--membership-color, #ffd700)15 0%, transparent 70%);
    border-radius: 50%;
    opacity: 0.3;
}

.membership-detail-header {
    text-align: center;
    margin-bottom: 2rem;
    position: relative;
    z-index: 1;
}

.membership-detail-icon {
    filter: drop-shadow(0 0 15px rgba(255, 215, 0, 0.6));
    animation: float 3s ease-in-out infinite;
}

.membership-detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
    position: relative;
    z-index: 1;
}

.membership-detail-item {
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    transition: all 0.3s ease;
}

.membership-detail-item:hover {
    background: rgba(0, 0, 0, 0.5);
    border-color: var(--membership-color, #ffd700);
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
}

.membership-detail-label {
    color: #aaa;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 0.75rem;
    font-weight: 600;
}

.membership-detail-value {
    color: #fff;
    font-size: 1.2rem;
    font-weight: 700;
}

.membership-detail-action {
    text-align: center;
    position: relative;
    z-index: 1;
}

/* Üyelik Boş Kart */
.membership-empty-card {
    background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    border: 2px dashed #444;
    border-radius: 20px;
    padding: 0;
    margin-bottom: 2rem;
    transition: all 0.3s ease;
}

.membership-empty-card:hover {
    border-color: #666;
    background: linear-gradient(135deg, #1f1f1f 0%, #2f2f2f 100%);
}

/* Animasyonlar */
@keyframes pulse {
    0%, 100% {
        transform: scale(1);
        filter: drop-shadow(0 0 10px rgba(255, 215, 0, 0.5));
    }
    50% {
        transform: scale(1.1);
        filter: drop-shadow(0 0 20px rgba(255, 215, 0, 0.8));
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

@keyframes rotate {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .membership-detail-grid {
        grid-template-columns: 1fr;
    }
    
    .membership-dashboard-card {
        min-height: 200px;
        padding: 1.5rem;
    }
    
    .membership-detail-card {
        padding: 1.5rem;
    }
}

/* Danışman Dashboard Kartı */
.consultant-dashboard-card {
    background: linear-gradient(135deg, #1a1a1a 0%, #2a1a1a 100%);
    border: 2px solid #ff6b35;
    border-radius: 20px;
    padding: 0;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4), 0 0 30px rgba(255, 107, 53, 0.15);
}

.consultant-card-background {
    position: absolute;
    top: -100px;
    right: -100px;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255, 107, 53, 0.15) 0%, transparent 70%);
    border-radius: 50%;
}

.consultant-card-content {
    position: relative;
    z-index: 1;
    padding: 2.5rem;
}

.consultant-card-header {
    display: flex;
    align-items: center;
    gap: 2rem;
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 2px solid rgba(255, 107, 53, 0.2);
}

.consultant-avatar-large {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ffd700 0%, #ff6b35 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 2.5rem;
    color: #000;
    flex-shrink: 0;
    box-shadow: 0 8px 30px rgba(255, 107, 53, 0.4);
    animation: consultant-pulse 3s ease-in-out infinite;
}

.consultant-header-info {
    flex: 1;
}

.consultant-card-title {
    color: #ff6b35;
    font-size: 1.2rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.consultant-name {
    color: #fff;
    font-size: 2rem;
    font-weight: 900;
    margin: 0 0 0.5rem 0;
    background: linear-gradient(135deg, #fff 0%, #ffd700 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.consultant-specialization {
    color: #ffd700;
    font-size: 1.1rem;
    font-weight: 500;
    margin: 0;
}

.consultant-card-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.consultant-stat-item {
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 107, 53, 0.2);
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
}

.consultant-stat-item:hover {
    background: rgba(0, 0, 0, 0.5);
    border-color: #ff6b35;
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(255, 107, 53, 0.3);
}

.stat-icon {
    font-size: 2rem;
    filter: drop-shadow(0 2px 8px rgba(255, 215, 0, 0.5));
}

.stat-content {
    flex: 1;
}

.stat-label {
    color: #aaa;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.stat-value {
    color: #fff;
    font-size: 1.3rem;
    font-weight: 700;
}

.consultant-card-footer {
    padding-top: 1.5rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.consultant-footer-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.footer-info-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.footer-label {
    color: #aaa;
    font-size: 0.9rem;
}

.footer-value {
    color: #fff;
    font-size: 0.95rem;
    font-weight: 600;
}

.consultant-status-badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.consultant-status-pending {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.2) 0%, rgba(255, 152, 0, 0.2) 100%);
    color: #ffc107;
    border: 1px solid rgba(255, 193, 7, 0.3);
}

.consultant-status-confirmed {
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.2) 0%, rgba(56, 142, 60, 0.2) 100%);
    color: #4ade80;
    border: 1px solid rgba(76, 175, 80, 0.3);
}

.consultant-status-completed {
    background: linear-gradient(135deg, rgba(33, 150, 243, 0.2) 0%, rgba(25, 118, 210, 0.2) 100%);
    color: #4facfe;
    border: 1px solid rgba(33, 150, 243, 0.3);
}

.consultant-status-cancelled {
    background: linear-gradient(135deg, rgba(244, 67, 54, 0.2) 0%, rgba(211, 47, 47, 0.2) 100%);
    color: #f5576c;
    border: 1px solid rgba(244, 67, 54, 0.3);
}

@keyframes consultant-pulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 8px 30px rgba(255, 107, 53, 0.4);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 12px 40px rgba(255, 107, 53, 0.6);
    }
}

@media (max-width: 768px) {
    .consultant-card-header {
        flex-direction: column;
        text-align: center;
        gap: 1.5rem;
    }
    
    .consultant-card-stats {
        grid-template-columns: 1fr;
    }
    
    .consultant-footer-info {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .consultant-card-content {
        padding: 1.5rem;
    }
    
    .consultant-avatar-large {
        width: 80px;
        height: 80px;
        font-size: 2rem;
    }
    
    .consultant-name {
        font-size: 1.5rem;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?>

