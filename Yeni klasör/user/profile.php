<?php
$page_title = 'Profil Düzenle';
require_once '../includes/header.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Kullanıcı profil bilgilerini çek
$stmt = $db->prepare("SELECT u.*, up.height, up.weight, up.age, up.gender, up.activity_level, up.goal FROM users u LEFT JOIN user_profiles up ON u.id = up.user_id WHERE u.id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Aktif üyelik bilgilerini çek
$stmt = $db->prepare("SELECT * FROM user_memberships WHERE user_id = ? AND status = 'active' ORDER BY end_date DESC LIMIT 1");
$stmt->execute([$user_id]);
$membership = $stmt->fetch();

// Seçili danışman bilgilerini çek (en son seçilen aktif danışman)
$stmt = $db->prepare("SELECT cb.*, c.specialization, c.experience_years, c.rating, c.price_per_session, u.first_name, u.last_name, u.email 
                      FROM consultant_bookings cb 
                      JOIN consultants c ON cb.consultant_id = c.id 
                      JOIN users u ON c.user_id = u.id 
                      WHERE cb.user_id = ? AND cb.status IN ('pending', 'confirmed') 
                      ORDER BY cb.created_at DESC LIMIT 1");
$stmt->execute([$user_id]);
$selected_consultant = $stmt->fetch();

// Üyelik süre metinleri
$period_texts = [
    'monthly' => 'Aylık',
    'quarterly' => '3 Aylık',
    'yearly' => '1 Yıllık'
];
?>

<section class="auth-section">
    <div class="container">
        <div class="auth-form-wrapper">
            <div class="form-container" style="max-width: 700px; margin: 0 auto;">
                <div class="auth-form-header">
                    <h2 class="form-title">Profil Bilgilerimi Düzenle</h2>
                    <p class="auth-form-subtitle">Kişisel bilgilerinizi güncelleyin</p>
                </div>
                
                <?php if ($membership): ?>
                <div class="membership-info-box" style="background: #1a1a1a; border-radius: 12px; padding: 2rem; margin-bottom: 2rem; border: 2px solid #ffd700;">
                    <h3 style="color: #ffd700; margin-bottom: 1rem; font-size: 1.3rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span>👑</span>
                        <span>Aktif Üyelik Bilgileri</span>
                    </h3>
                    <div style="color: #fff;">
                        <div style="margin-bottom: 0.75rem; font-size: 1.1rem;">
                            <strong style="color: #aaa;">Üyelik Tipi:</strong>
                            <span style="color: #ffd700; font-weight: bold; margin-left: 0.5rem;"><?php echo htmlspecialchars($membership['membership_type']); ?> - <?php echo $period_texts[$membership['membership_period']] ?? $membership['membership_period']; ?></span>
                        </div>
                        <div style="margin-bottom: 0.75rem;">
                            <strong style="color: #aaa;">Başlangıç Tarihi:</strong>
                            <span style="margin-left: 0.5rem;"><?php echo date('d.m.Y', strtotime($membership['start_date'])); ?></span>
                        </div>
                        <div style="margin-bottom: 0.75rem;">
                            <strong style="color: #aaa;">Bitiş Tarihi:</strong>
                            <span style="margin-left: 0.5rem; <?php 
                                $days_left = (strtotime($membership['end_date']) - time()) / (60 * 60 * 24);
                                if ($days_left <= 7) {
                                    echo 'color: #ff6b6b; font-weight: bold;';
                                } elseif ($days_left <= 30) {
                                    echo 'color: #ffa500;';
                                } else {
                                    echo 'color: #4ade80;';
                                }
                            ?>">
                                <?php echo date('d.m.Y', strtotime($membership['end_date'])); ?>
                                <?php 
                                $days_left = ceil((strtotime($membership['end_date']) - time()) / (60 * 60 * 24));
                                if ($days_left > 0) {
                                    echo ' <span style="color: #888;">(' . $days_left . ' gün kaldı)</span>';
                                } elseif ($days_left == 0) {
                                    echo ' <span style="color: #ff6b6b; font-weight: bold;">(Bugün bitiyor!)</span>';
                                } else {
                                    echo ' <span style="color: #ff6b6b; font-weight: bold;">(Süresi dolmuş)</span>';
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #333;">
                        <a href="<?php echo SITE_URL; ?>user/memberships.php" class="btn btn-primary" style="display: inline-block;">
                            Yeni Üyelik Al
                        </a>
                    </div>
                </div>
                <?php else: ?>
                <div class="membership-info-box" style="background: #1a1a1a; border-radius: 12px; padding: 2rem; margin-bottom: 2rem; border: 2px solid #444;">
                    <h3 style="color: #aaa; margin-bottom: 1rem; font-size: 1.2rem;">Üyelik Durumu</h3>
                    <p style="color: #888; margin-bottom: 1rem;">Aktif üyeliğiniz bulunmamaktadır.</p>
                    <a href="<?php echo SITE_URL; ?>user/memberships.php" class="btn btn-primary" style="display: inline-block;">
                        Üyelik Satın Al
                    </a>
                </div>
                <?php endif; ?>
                
                <?php if ($selected_consultant): ?>
                <div class="consultant-info-box" style="background: linear-gradient(135deg, #1a1a1a 0%, #2a1a1a 100%); border-radius: 12px; padding: 2rem; margin-bottom: 2rem; border: 2px solid #ff6b35; box-shadow: 0 4px 15px rgba(255, 107, 53, 0.2);">
                    <h3 style="color: #ff6b35; margin-bottom: 1.5rem; font-size: 1.3rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span>👨‍⚕️</span>
                        <span>Seçili Danışmanım</span>
                    </h3>
                    <div style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div style="width: 70px; height: 70px; border-radius: 50%; background: linear-gradient(135deg, #ffd700, #ff6b35); display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.5rem; color: #000; flex-shrink: 0;">
                            <?php echo strtoupper(substr($selected_consultant['first_name'], 0, 1) . substr($selected_consultant['last_name'], 0, 1)); ?>
                        </div>
                        <div style="flex: 1;">
                            <h4 style="color: #fff; margin: 0 0 0.5rem 0; font-size: 1.4rem;">
                                <?php echo htmlspecialchars($selected_consultant['first_name'] . ' ' . $selected_consultant['last_name']); ?>
                            </h4>
                            <p style="color: #ff6b35; margin: 0 0 0.5rem 0; font-size: 1rem; font-weight: 500;">
                                <?php echo htmlspecialchars($selected_consultant['specialization']); ?>
                            </p>
                            <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                                <div style="display: flex; align-items: center; gap: 0.3rem; color: #ffd700;">
                                    <span>⭐</span>
                                    <span style="font-weight: bold;"><?php echo number_format($selected_consultant['rating'], 1); ?></span>
                                </div>
                                <div style="color: #aaa; font-size: 0.9rem;">
                                    💼 <?php echo $selected_consultant['experience_years']; ?> Yıl Deneyim
                                </div>
                                <div style="color: #4ade80; font-weight: bold;">
                                    💰 <?php echo number_format($selected_consultant['price_per_session'], 0, ',', '.'); ?> ₺
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="padding-top: 1rem; border-top: 1px solid #333;">
                        <div style="color: #aaa; font-size: 0.9rem; margin-bottom: 0.5rem;">
                            <strong>Seçim Tarihi:</strong> <?php echo date('d.m.Y H:i', strtotime($selected_consultant['created_at'])); ?>
                        </div>
                        <div style="color: #4ade80; font-size: 0.9rem; font-weight: 500;">
                            Durum: <?php 
                                $status_text = [
                                    'pending' => 'Beklemede',
                                    'confirmed' => 'Onaylandı',
                                    'completed' => 'Tamamlandı',
                                    'cancelled' => 'İptal Edildi'
                                ];
                                echo $status_text[$selected_consultant['status']] ?? $selected_consultant['status'];
                            ?>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="consultant-info-box" style="background: #1a1a1a; border-radius: 12px; padding: 2rem; margin-bottom: 2rem; border: 2px solid #444;">
                    <h3 style="color: #aaa; margin-bottom: 1rem; font-size: 1.2rem;">Danışman Durumu</h3>
                    <p style="color: #888; margin-bottom: 1rem;">Henüz bir danışman seçmediniz.</p>
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <a href="<?php echo SITE_URL; ?>user/nutrition-consultant.php" class="btn btn-primary" style="display: inline-block;">
                            Beslenme Danışmanı Seç
                        </a>
                        <a href="<?php echo SITE_URL; ?>user/personal-trainer.php" class="btn btn-primary" style="display: inline-block;">
                            Antrenör Seç
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                
                <div id="alert-container"></div>
                <?php if (isset($_GET['membership']) && $_GET['membership'] === 'success'): ?>
                <div class="form-success" style="background: #4ade80; color: #fff; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    Üyeliğiniz başarıyla satın alındı ve aktif edildi!
                </div>
                <?php endif; ?>
                <form id="profile-form">
        <div class="form-group">
            <label for="first_name" class="form-label">Ad</label>
            <input type="text" id="first_name" name="first_name" class="form-input" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="last_name" class="form-label">Soyad</label>
            <input type="text" id="last_name" name="last_name" class="form-input" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email" class="form-label">E-posta</label>
            <input type="email" id="email" name="email" class="form-input" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="phone" class="form-label">Telefon</label>
            <input type="tel" id="phone" name="phone" class="form-input" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="height" class="form-label">Boy (cm)</label>
            <input type="number" id="height" name="height" class="form-input" step="0.01" value="<?php echo $user['height'] ?? ''; ?>">
        </div>
        <div class="form-group">
            <label for="weight" class="form-label">Kilo (kg)</label>
            <input type="number" id="weight" name="weight" class="form-input" step="0.01" value="<?php echo $user['weight'] ?? ''; ?>">
        </div>
        <div class="form-group">
            <label for="age" class="form-label">Yaş</label>
            <input type="number" id="age" name="age" class="form-input" value="<?php echo $user['age'] ?? ''; ?>">
        </div>
        <div class="form-group">
            <label for="gender" class="form-label">Cinsiyet</label>
            <select id="gender" name="gender" class="form-input">
                <option value="">Seçiniz</option>
                <option value="male" <?php echo ($user['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>Erkek</option>
                <option value="female" <?php echo ($user['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Kadın</option>
                <option value="other" <?php echo ($user['gender'] ?? '') === 'other' ? 'selected' : ''; ?>>Diğer</option>
            </select>
        </div>
        <div class="form-group">
            <label for="activity_level" class="form-label">Aktivite Seviyesi</label>
            <select id="activity_level" name="activity_level" class="form-input">
                <option value="sedentary" <?php echo ($user['activity_level'] ?? '') === 'sedentary' ? 'selected' : ''; ?>>Hareketsiz</option>
                <option value="light" <?php echo ($user['activity_level'] ?? '') === 'light' ? 'selected' : ''; ?>>Hafif Aktif</option>
                <option value="moderate" <?php echo ($user['activity_level'] ?? '') === 'moderate' ? 'selected' : ''; ?>>Orta Aktif</option>
                <option value="active" <?php echo ($user['activity_level'] ?? '') === 'active' ? 'selected' : ''; ?>>Aktif</option>
                <option value="very_active" <?php echo ($user['activity_level'] ?? '') === 'very_active' ? 'selected' : ''; ?>>Çok Aktif</option>
            </select>
        </div>
        <div class="form-group">
            <label for="goal" class="form-label">Hedef</label>
            <select id="goal" name="goal" class="form-input">
                <option value="weight_loss" <?php echo ($user['goal'] ?? '') === 'weight_loss' ? 'selected' : ''; ?>>Kilo Verme</option>
                <option value="muscle_gain" <?php echo ($user['goal'] ?? '') === 'muscle_gain' ? 'selected' : ''; ?>>Kas Kazanma</option>
                <option value="maintenance" <?php echo ($user['goal'] ?? '') === 'maintenance' ? 'selected' : ''; ?>>Kilo Koruma</option>
                <option value="endurance" <?php echo ($user['goal'] ?? '') === 'endurance' ? 'selected' : ''; ?>>Dayanıklılık</option>
            </select>
        </div>
                    <button type="submit" class="btn btn-primary btn-block btn-large">
                        <span>Kaydet</span>
                        <span class="btn-icon">→</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php
$extra_js = ['assets/js/profile.js'];
require_once '../includes/footer.php';
?>

