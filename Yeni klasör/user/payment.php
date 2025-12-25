<?php
$page_title = 'Ödeme';
require_once '../includes/header.php';
requireLogin();

// URL parametrelerinden üyelik bilgilerini al
$membership_id = isset($_GET['membership_id']) ? intval($_GET['membership_id']) : 0;
$membership_name = isset($_GET['membership_name']) ? htmlspecialchars($_GET['membership_name']) : '';
$membership_price = isset($_GET['membership_price']) ? floatval($_GET['membership_price']) : 0;
$membership_period = isset($_GET['membership_period']) ? $_GET['membership_period'] : 'monthly';

// Üyelik paketleri
$memberships = [
    1 => [
        'id' => 1,
        'name' => 'Temel',
        'base_price' => 299,
        'prices' => [
            'monthly' => 299,
            'quarterly' => 807,
            'yearly' => 2870
        ],
        'features' => [
            'Sınırsız antrenman erişimi',
            'Temel beslenme planı',
            'BMI hesaplama',
            'Aylık ilerleme takibi',
            'E-posta desteği'
        ]
    ],
    2 => [
        'id' => 2,
        'name' => 'Premium',
        'base_price' => 599,
        'prices' => [
            'monthly' => 599,
            'quarterly' => 1617,
            'yearly' => 5750
        ],
        'features' => [
            'Sınırsız antrenman erişimi',
            'Kişiselleştirilmiş beslenme planı',
            'BMI hesaplama ve takip',
            'Haftalık ilerleme raporları',
            'Kişisel antrenör desteği (2 seans/ay)',
            'Öncelikli e-posta desteği',
            'Özel antrenman programları'
        ]
    ],
    3 => [
        'id' => 3,
        'name' => 'VIP',
        'base_price' => 999,
        'prices' => [
            'monthly' => 999,
            'quarterly' => 2697,
            'yearly' => 9590
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
        ]
    ]
];

// Geçerli üyelik kontrolü
if (!isset($memberships[$membership_id]) || $membership_id == 0) {
    header('Location: ' . SITE_URL . 'user/memberships.php');
    exit;
}

$selected_membership = $memberships[$membership_id];
$period_texts = [
    'monthly' => 'Aylık',
    'quarterly' => '3 Aylık',
    'yearly' => '1 Yıllık'
];

// Seçilen süreye göre fiyatı al
$final_price = $selected_membership['prices'][$membership_period] ?? $selected_membership['prices']['monthly'];
$base_price = $selected_membership['base_price'];
$discount = 0;
$discount_percent = 0;

// İndirim hesaplama
if ($membership_period === 'quarterly') {
    $original_price = $base_price * 3;
    $discount = $original_price - $final_price;
    $discount_percent = 10;
} elseif ($membership_period === 'yearly') {
    $original_price = $base_price * 12;
    $discount = $original_price - $final_price;
    $discount_percent = 20;
}

// Kullanıcı bilgilerini al
$user_id = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<section class="payment-section">
    <div class="container">
        <div class="payment-header">
            <h1 class="section-title">Ödeme</h1>
            <p class="payment-subtitle">Üyelik paketinizi seçtiniz. Ödeme bilgilerinizi girin ve işlemi tamamlayın.</p>
        </div>

        <div class="payment-container">
            <div class="payment-left">
                <div class="payment-summary">
                    <h2>Özet</h2>
                    <div class="summary-item">
                        <div class="summary-label">Seçilen Paket:</div>
                        <div class="summary-value"><?php echo htmlspecialchars($selected_membership['name']); ?> - <?php echo $period_texts[$membership_period] ?? 'Aylık'; ?></div>
                    </div>
                    <?php if ($discount > 0): ?>
                    <div class="summary-item">
                        <div class="summary-label">Normal Fiyat:</div>
                        <div class="summary-value" style="text-decoration: line-through; color: #888;"><?php echo number_format($base_price * ($membership_period === 'quarterly' ? 3 : 12), 2, ',', '.'); ?> ₺</div>
                    </div>
                    <div class="summary-item discount">
                        <div class="summary-label">İndirim (<?php echo $discount_percent; ?>%):</div>
                        <div class="summary-value">-<?php echo number_format($discount, 2, ',', '.'); ?> ₺</div>
                    </div>
                    <?php endif; ?>
                    <div class="summary-item">
                        <div class="summary-label">Fiyat:</div>
                        <div class="summary-value"><?php echo number_format($final_price, 2, ',', '.'); ?> ₺</div>
                    </div>
                    <div class="summary-item total">
                        <div class="summary-label">Toplam:</div>
                        <div class="summary-value"><?php echo number_format($final_price, 2, ',', '.'); ?> ₺</div>
                    </div>
                </div>

                <div class="membership-features-box">
                    <h3>Paket Özellikleri</h3>
                    <ul class="features-list">
                        <?php foreach ($selected_membership['features'] as $feature): ?>
                        <li>
                            <span class="feature-icon">✓</span>
                            <span><?php echo htmlspecialchars($feature); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <div class="payment-right">
                <div class="payment-form-container">
                    <h2>Ödeme Bilgileri</h2>
                    
                    <form id="payment-form" class="payment-form">
                        <div class="form-group">
                            <label for="card-name">Kart Üzerindeki İsim</label>
                            <input type="text" id="card-name" name="card_name" placeholder="Ad Soyad" required>
                        </div>

                        <div class="form-group">
                            <label for="card-number">Kart Numarası</label>
                            <input type="text" id="card-number" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="card-expiry">Son Kullanma Tarihi</label>
                                <input type="text" id="card-expiry" name="card_expiry" placeholder="MM/YY" maxlength="5" required>
                            </div>
                            <div class="form-group">
                                <label for="card-cvv">CVV</label>
                                <input type="text" id="card-cvv" name="card_cvv" placeholder="123" maxlength="3" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="payment-method">Ödeme Yöntemi</label>
                            <select id="payment-method" name="payment_method" required>
                                <option value="">Seçiniz</option>
                                <option value="credit_card">Kredi Kartı</option>
                                <option value="debit_card">Banka Kartı</option>
                                <option value="bank_transfer">Havale/EFT</option>
                            </select>
                        </div>

                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="terms" required>
                                <span>Üyelik sözleşmesini ve <a href="#" target="_blank">kullanım koşullarını</a> okudum ve kabul ediyorum.</span>
                            </label>
                        </div>

                        <div id="payment-alert-container"></div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='<?php echo SITE_URL; ?>user/memberships.php'">Geri Dön</button>
                            <button type="submit" class="btn btn-primary btn-large">Ödemeyi Tamamla</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Kart numarası formatlama
document.getElementById('card-number').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\s/g, '');
    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
    e.target.value = formattedValue;
});

// Son kullanma tarihi formatlama
document.getElementById('card-expiry').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2, 4);
    }
    e.target.value = value;
});

// CVV sadece rakam
document.getElementById('card-cvv').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/\D/g, '');
});

// Ödeme formu gönderimi
document.getElementById('payment-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const alertContainer = document.getElementById('payment-alert-container');
    
    // Basit validasyon
    const cardNumber = formData.get('card_number').replace(/\s/g, '');
    if (cardNumber.length < 16) {
        showPaymentAlert('Kart numarası geçersiz. Lütfen 16 haneli kart numaranızı girin.', 'error');
        return;
    }
    
    // Ödeme işlemi simülasyonu
    alertContainer.innerHTML = '<div class="form-success">Ödeme işlemi gerçekleştiriliyor...</div>';
    
    // Üyelik satın alma verilerini hazırla
    const purchaseData = new FormData();
    purchaseData.append('membership_id', <?php echo $membership_id; ?>);
    purchaseData.append('membership_period', '<?php echo $membership_period; ?>');
    purchaseData.append('price', <?php echo $final_price; ?>);
    
    // Simüle edilmiş ödeme işlemi (gerçek uygulamada ödeme gateway'ine bağlanılır)
    setTimeout(() => {
        // Üyelik satın alma API'sini çağır
        fetch('<?php echo SITE_URL; ?>api/purchase-membership.php', {
            method: 'POST',
            body: purchaseData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alertContainer.innerHTML = '<div class="form-success">Ödeme başarıyla tamamlandı! Üyeliğiniz aktif edildi.</div>';
                
                // Başarılı ödeme sonrası dashboard'a yönlendir
                setTimeout(() => {
                    window.location.href = '<?php echo SITE_URL; ?>user/dashboard.php?payment=success';
                }, 2000);
            } else {
                showPaymentAlert(data.message || 'Bir hata oluştu. Lütfen tekrar deneyin.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showPaymentAlert('Bir hata oluştu. Lütfen tekrar deneyin.', 'error');
        });
    }, 2000);
});

function showPaymentAlert(message, type) {
    const alertContainer = document.getElementById('payment-alert-container');
    const alertClass = type === 'error' ? 'form-error' : 'form-success';
    alertContainer.innerHTML = `<div class="${alertClass}">${message}</div>`;
}
</script>

<?php
$extra_css = '<style>
.payment-section {
    padding: 2rem 0;
    min-height: calc(100vh - 200px);
}

.payment-header {
    text-align: center;
    margin-bottom: 3rem;
}

.payment-subtitle {
    color: #888;
    margin-top: 0.5rem;
}

.payment-container {
    display: grid;
    grid-template-columns: 1fr 1.5fr;
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.payment-summary {
    background: #1a1a1a;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.payment-summary h2 {
    color: #fff;
    margin-bottom: 1.5rem;
    font-size: 1.5rem;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    padding: 1rem 0;
    border-bottom: 1px solid #333;
}

.summary-item:last-child {
    border-bottom: none;
}

.summary-item.total {
    border-top: 2px solid #ffd700;
    padding-top: 1.5rem;
    margin-top: 1rem;
    font-size: 1.2rem;
    font-weight: bold;
}

.summary-item.discount {
    color: #4ade80;
}

.summary-label {
    color: #aaa;
}

.summary-value {
    color: #fff;
    font-weight: 500;
}

.summary-item.total .summary-value {
    color: #ffd700;
    font-size: 1.3rem;
}

.membership-features-box {
    background: #1a1a1a;
    border-radius: 12px;
    padding: 2rem;
}

.membership-features-box h3 {
    color: #fff;
    margin-bottom: 1rem;
}

.features-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.features-list li {
    display: flex;
    align-items: center;
    padding: 0.5rem 0;
    color: #ccc;
}

.feature-icon {
    color: #4ade80;
    margin-right: 0.75rem;
    font-weight: bold;
}

.payment-form-container {
    background: #1a1a1a;
    border-radius: 12px;
    padding: 2rem;
}

.payment-form-container h2 {
    color: #fff;
    margin-bottom: 1.5rem;
    font-size: 1.5rem;
}

.payment-form .form-group {
    margin-bottom: 1.5rem;
}

.payment-form label {
    display: block;
    color: #fff;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.payment-form input,
.payment-form select {
    width: 100%;
    padding: 0.75rem;
    background: #2a2a2a;
    border: 1px solid #444;
    border-radius: 8px;
    color: #fff;
    font-size: 1rem;
    transition: border-color 0.3s;
}

.payment-form input:focus,
.payment-form select:focus {
    outline: none;
    border-color: #ffd700;
}

.payment-form .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.checkbox-group {
    margin-top: 1rem;
}

.checkbox-label {
    display: flex;
    align-items: flex-start;
    color: #ccc;
    font-size: 0.9rem;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    width: auto;
    margin-right: 0.5rem;
    margin-top: 0.2rem;
}

.checkbox-label a {
    color: #ffd700;
    text-decoration: underline;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.form-actions .btn {
    flex: 1;
}

#payment-alert-container {
    margin-top: 1rem;
}

@media (max-width: 768px) {
    .payment-container {
        grid-template-columns: 1fr;
    }
    
    .payment-form .form-row {
        grid-template-columns: 1fr;
    }
}
</style>';
require_once '../includes/footer.php';
?>

