<?php
$page_title = 'Üyelikler';
require_once '../includes/header.php';

// Üyelik paketleri - Her paket için aylık, 3 aylık ve yıllık seçenekler
$memberships = [
    [
        'id' => 1,
        'name' => 'Temel',
        'base_price' => 299, // Aylık fiyat
        'prices' => [
            'monthly' => 299,      // Aylık
            'quarterly' => 807,    // 3 Aylık (%10 indirim: 299 * 3 * 0.9 = 807)
            'yearly' => 2870       // Yıllık (%20 indirim: 299 * 12 * 0.8 = 2870)
        ],
        'features' => [
            'Sınırsız antrenman erişimi',
            'Temel beslenme planı',
            'BMI hesaplama',
            'Aylık ilerleme takibi',
            'E-posta desteği'
        ],
        'popular' => false,
        'icon' => '💪'
    ],
    [
        'id' => 2,
        'name' => 'Premium',
        'base_price' => 599, // Aylık fiyat
        'prices' => [
            'monthly' => 599,      // Aylık
            'quarterly' => 1617,   // 3 Aylık (%10 indirim: 599 * 3 * 0.9 = 1617)
            'yearly' => 5750       // Yıllık (%20 indirim: 599 * 12 * 0.8 = 5750)
        ],
        'features' => [
            'Sınırsız antrenman erişimi',
            'Kişiselleştirilmiş beslenme planı',
            'BMI hesaplama ve takip',
            'Haftalık ilerleme raporları',
            'Kişisel antrenör desteği (2 seans/ay)',
            'Öncelikli e-posta desteği',
            'Özel antrenman programları'
        ],
        'popular' => true,
        'icon' => '⭐'
    ],
    [
        'id' => 3,
        'name' => 'VIP',
        'base_price' => 999, // Aylık fiyat
        'prices' => [
            'monthly' => 999,      // Aylık
            'quarterly' => 2697,   // 3 Aylık (%10 indirim: 999 * 3 * 0.9 = 2697)
            'yearly' => 9590       // Yıllık (%20 indirim: 999 * 12 * 0.8 = 9590)
        ],
        'features' => [
            'Sınırsız antrenman erişimi',
            'Tam kişiselleştirilmiş beslenme planı',
            'Günlük BMI takibi',
            'Günlük ilerleme raporları',
            'Sınırsız kişisel antrenör desteği',
            '7/24 öncelikli destek',
            'Özel antrenman programları',
            'Beslenme danışmanı erişimi',
            'Özel etkinliklere davet'
        ],
        'popular' => false,
        'icon' => '👑'
    ]
];
?>

<section class="memberships-section">
    <div class="container">
        <div class="memberships-header">
            <h1 class="section-title">Üyelik Paketlerimiz</h1>
            <p class="memberships-subtitle">Hedeflerinize uygun paketi seçin ve sağlıklı yaşam yolculuğunuza başlayın</p>
        </div>

        <div class="memberships-grid">
            <?php foreach ($memberships as $membership): ?>
            <div class="membership-card <?php echo $membership['popular'] ? 'popular' : ''; ?>">
                <?php if ($membership['popular']): ?>
                <div class="popular-badge">En Popüler</div>
                <?php endif; ?>
                
                <div class="membership-header">
                    <div class="membership-icon"><?php echo $membership['icon']; ?></div>
                    <h2 class="membership-name"><?php echo htmlspecialchars($membership['name']); ?></h2>
                </div>

                <!-- Süre Seçimi -->
                <div class="membership-period-selector">
                    <div class="period-options">
                        <label class="period-option active" data-period="monthly" data-price="<?php echo $membership['prices']['monthly']; ?>">
                            <input type="radio" name="period_<?php echo $membership['id']; ?>" value="monthly" checked>
                            <span>Aylık</span>
                        </label>
                        <label class="period-option" data-period="quarterly" data-price="<?php echo $membership['prices']['quarterly']; ?>">
                            <input type="radio" name="period_<?php echo $membership['id']; ?>" value="quarterly">
                            <span>3 Aylık</span>
                            <small class="discount-badge">%10 İndirim</small>
                        </label>
                        <label class="period-option" data-period="yearly" data-price="<?php echo $membership['prices']['yearly']; ?>">
                            <input type="radio" name="period_<?php echo $membership['id']; ?>" value="yearly">
                            <span>1 Yıllık</span>
                            <small class="discount-badge">%20 İndirim</small>
                        </label>
                    </div>
                </div>

                <div class="membership-price">
                    <span class="price-amount" id="price-<?php echo $membership['id']; ?>"><?php echo number_format($membership['prices']['monthly'], 0, ',', '.'); ?> ₺</span>
                    <span class="price-period" id="period-<?php echo $membership['id']; ?>">/ aylık</span>
                </div>

                <ul class="membership-features">
                    <?php foreach ($membership['features'] as $feature): ?>
                    <li class="feature-item">
                        <span class="feature-icon">✓</span>
                        <span class="feature-text"><?php echo htmlspecialchars($feature); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>

                <div class="membership-action">
                    <?php if (isLoggedIn()): ?>
                        <button class="btn btn-primary btn-block membership-btn" 
                                data-membership-id="<?php echo $membership['id']; ?>" 
                                data-membership-name="<?php echo htmlspecialchars($membership['name']); ?>" 
                                data-membership-price="<?php echo $membership['prices']['monthly']; ?>"
                                data-membership-period="monthly">
                            Paketi Seç
                        </button>
                    <?php else: ?>
                        <a href="<?php echo SITE_URL; ?>user/register.php" class="btn btn-primary btn-block membership-btn">
                            Hemen Başla
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="memberships-info">
            <div class="info-card">
                <h3>💳 Ödeme Seçenekleri</h3>
                <p>Kredi kartı, banka kartı ve havale/EFT ile ödeme yapabilirsiniz. Tüm ödemeler güvenli SSL sertifikası ile korunmaktadır.</p>
            </div>
            <div class="info-card">
                <h3>🔄 İptal ve İade</h3>
                <p>Üyeliğinizi istediğiniz zaman iptal edebilirsiniz. İlk 7 gün içinde tam iade garantisi sunuyoruz.</p>
            </div>
            <div class="info-card">
                <h3>🎁 Özel Teklifler</h3>
                <p>Yıllık ödeme yaparak %20 indirim kazanın. Öğrenciler ve 65+ yaş için özel fiyatlandırma mevcuttur.</p>
            </div>
        </div>
    </div>
</section>

<?php
$extra_js = ['assets/js/memberships.js'];
require_once '../includes/footer.php';
?>

