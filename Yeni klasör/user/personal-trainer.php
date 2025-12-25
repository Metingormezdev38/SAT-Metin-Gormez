<?php
$page_title = 'Kişisel Antrenör';
require_once '../includes/header.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Kullanıcı bilgilerini al
$stmt = $db->prepare("SELECT u.*, up.height, up.weight, up.age, up.gender, up.goal FROM users u LEFT JOIN user_profiles up ON u.id = up.user_id WHERE u.id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Danışmanları çek
$stmt = $db->prepare("SELECT c.*, u.first_name, u.last_name, u.email, u.phone FROM consultants c JOIN users u ON c.user_id = u.id WHERE c.status = 'available' ORDER BY c.rating DESC");
$stmt->execute();
$consultants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="feature-page">
    <div class="container">
        <div class="feature-header">
            <div class="feature-header-icon">💪</div>
            <h1 class="section-title">Kişisel Antrenör</h1>
            <p class="feature-subtitle">Uzman antrenörlerimizle birlikte size özel antrenman programları hazırlıyoruz.</p>
        </div>

        <div class="feature-content">
            <div class="feature-info-card">
                <h2>Neler Sunuyoruz?</h2>
                <ul class="feature-list">
                    <li>✅ Size özel antrenman programları</li>
                    <li>✅ Profesyonel antrenör desteği</li>
                    <li>✅ Kişiselleştirilmiş egzersiz planları</li>
                    <li>✅ Düzenli ilerleme takibi</li>
                    <li>✅ Motivasyon ve rehberlik</li>
                </ul>
            </div>

            <?php if (!empty($consultants)): ?>
            <div class="consultants-section">
                <h2>Uzman Antrenörlerimiz</h2>
                <div class="consultants-grid">
                    <?php foreach ($consultants as $consultant): ?>
                    <div class="consultant-card">
                        <div class="consultant-header">
                            <div class="consultant-avatar"><?php echo strtoupper(substr($consultant['first_name'], 0, 1) . substr($consultant['last_name'], 0, 1)); ?></div>
                            <div class="consultant-info">
                                <h3><?php echo htmlspecialchars($consultant['first_name'] . ' ' . $consultant['last_name']); ?></h3>
                                <p class="consultant-specialization"><?php echo htmlspecialchars($consultant['specialization']); ?></p>
                            </div>
                        </div>
                        <div class="consultant-details">
                            <div class="consultant-rating">
                                <span class="rating-stars">⭐</span>
                                <span class="rating-value"><?php echo number_format($consultant['rating'], 1); ?></span>
                            </div>
                            <div class="consultant-experience">
                                <span>💼 <?php echo $consultant['experience_years']; ?> Yıl Deneyim</span>
                            </div>
                            <div class="consultant-price">
                                <span class="price-label">Seans Ücreti:</span>
                                <span class="price-value"><?php echo number_format($consultant['price_per_session'], 0, ',', '.'); ?> ₺</span>
                            </div>
                            <?php if ($consultant['bio']): ?>
                            <p class="consultant-bio"><?php echo htmlspecialchars($consultant['bio']); ?></p>
                            <?php endif; ?>
                        </div>
                        <button class="btn btn-primary btn-block" onclick="bookConsultant(<?php echo $consultant['id']; ?>)">
                            Antrenör Seç
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="info-message">
                <p>Şu anda müsait antrenör bulunmamaktadır. Lütfen daha sonra tekrar kontrol edin.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<div id="alert-container"></div>

<script>
function bookConsultant(consultantId) {
    if (confirm('Bu antrenörü seçmek istediğinizden emin misiniz?')) {
        const formData = new FormData();
        formData.append('consultant_id', consultantId);
        
        fetch('<?php echo SITE_URL; ?>api/select-consultant.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Başarılı', data.message, 'success');
                setTimeout(() => {
                    window.location.href = '<?php echo SITE_URL; ?>user/profile.php';
                }, 1500);
            } else {
                showAlert('Hata', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Hata', 'Bir hata oluştu. Lütfen tekrar deneyin.', 'error');
        });
    }
}

function showAlert(title, message, type) {
    const alertContainer = document.getElementById('alert-container');
    const alertClass = type === 'success' ? 'form-success' : type === 'error' ? 'form-error' : 'form-info';
    alertContainer.innerHTML = `<div class="${alertClass}" style="margin: 1rem auto; max-width: 600px;">${message}</div>`;
    setTimeout(() => {
        alertContainer.innerHTML = '';
    }, 5000);
}
</script>

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
    margin: 0;
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

.consultants-section h2 {
    color: #fff;
    margin-bottom: 2rem;
    font-size: 1.8rem;
}

.consultants-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
}

.consultant-card {
    background: #1a1a1a;
    border-radius: 12px;
    padding: 2rem;
    border: 1px solid #333;
    transition: all 0.3s;
}

.consultant-card:hover {
    border-color: #ffd700;
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(255, 215, 0, 0.2);
}

.consultant-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.consultant-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ffd700, #ff6b35);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
    color: #000;
}

.consultant-info h3 {
    color: #fff;
    margin: 0 0 0.25rem 0;
    font-size: 1.3rem;
}

.consultant-specialization {
    color: #ffd700;
    margin: 0;
    font-size: 0.9rem;
}

.consultant-details {
    margin-bottom: 1.5rem;
}

.consultant-rating,
.consultant-experience,
.consultant-price {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
    color: #ccc;
}

.rating-stars {
    font-size: 1.2rem;
}

.rating-value {
    font-weight: bold;
    color: #ffd700;
}

.price-label {
    color: #aaa;
}

.price-value {
    color: #4ade80;
    font-weight: bold;
    font-size: 1.1rem;
}

.consultant-bio {
    color: #aaa;
    font-size: 0.95rem;
    line-height: 1.6;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #333;
}

.info-message {
    background: #1a1a1a;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    color: #ccc;
}

@media (max-width: 768px) {
    .consultants-grid {
        grid-template-columns: 1fr;
    }
}
</style>';
require_once '../includes/footer.php';
?>

