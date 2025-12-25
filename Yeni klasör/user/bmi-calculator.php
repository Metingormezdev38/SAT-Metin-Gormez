<?php
$page_title = 'BMI Hesaplayıcı';
require_once '../includes/header.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Kullanıcı profil bilgilerini çek
$stmt = $db->prepare("SELECT height, weight, bmi FROM user_profiles WHERE user_id = ?");
$stmt->execute([$user_id]);
$profile = $stmt->fetch();
?>

<div class="dashboard">
    <h1 class="section-title">BMI (Vücut Kitle İndeksi) Hesaplayıcı</h1>
    
    <div class="bmi-calculator">
        <form id="bmi-form">
            <div class="form-group">
                <label for="height" class="form-label">Boy (cm)</label>
                <input type="number" id="height" name="height" class="form-input" step="0.01" value="<?php echo $profile['height'] ?? ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="weight" class="form-label">Kilo (kg)</label>
                <input type="number" id="weight" name="weight" class="form-input" step="0.01" value="<?php echo $profile['weight'] ?? ''; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Hesapla</button>
        </form>
        
        <div id="bmi-result" class="bmi-result hidden"></div>
    </div>
    
    <div id="alert-container"></div>

    <div class="card">
        <h2 class="card-title">BMI Kategorileri</h2>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>BMI Değeri</th>
                        <th>Kategori</th>
                        <th>Açıklama</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>18.5'den az</td>
                        <td>Zayıf</td>
                        <td>İdeal kilonuzun altındasınız</td>
                    </tr>
                    <tr>
                        <td>18.5 - 24.9</td>
                        <td>Normal</td>
                        <td>İdeal kilonuzdasınız</td>
                    </tr>
                    <tr>
                        <td>25 - 29.9</td>
                        <td>Fazla Kilolu</td>
                        <td>İdeal kilonuzun üzerindesiniz</td>
                    </tr>
                    <tr>
                        <td>30 ve üzeri</td>
                        <td>Obez</td>
                        <td>Sağlık için kilo vermeniz önerilir</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
/* BMI Sonuç Kartı */
.bmi-result-card {
    background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    border: 2px solid var(--bmi-color, #ffd700);
    border-radius: 24px;
    padding: 3rem 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4), 0 0 30px rgba(255, 215, 0, 0.15);
    margin-bottom: 2rem;
}

.bmi-result-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, var(--bmi-color, #ffd700)15 0%, transparent 70%);
    animation: rotate 20s linear infinite;
    opacity: 0.1;
}

.bmi-result-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    position: relative;
    z-index: 1;
    animation: pulse 2s ease-in-out infinite;
    filter: drop-shadow(0 0 10px rgba(255, 215, 0, 0.5));
}

.bmi-result-value {
    font-size: 4.5rem;
    font-weight: 900;
    line-height: 1;
    margin-bottom: 1rem;
    position: relative;
    z-index: 1;
    letter-spacing: -2px;
    text-shadow: 0 0 30px rgba(255, 215, 0, 0.3);
}

.bmi-result-category {
    font-size: 1.5rem;
    color: #fff;
    font-weight: 700;
    margin-bottom: 1rem;
    position: relative;
    z-index: 1;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.bmi-result-description {
    color: #ccc;
    font-size: 1rem;
    line-height: 1.6;
    max-width: 600px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

/* Diyet Planı Öneri Kartı */
.bmi-diet-suggestion-card {
    background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    border: 2px solid #ffd700;
    border-radius: 24px;
    padding: 2.5rem;
    margin-top: 2rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4), 0 0 30px rgba(255, 215, 0, 0.2);
}

.bmi-diet-suggestion-card::before {
    content: '';
    position: absolute;
    top: -100px;
    right: -100px;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255, 215, 0, 0.2) 0%, transparent 70%);
    border-radius: 50%;
}

.diet-suggestion-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    position: relative;
    z-index: 1;
}

.diet-suggestion-icon {
    font-size: 2.5rem;
    animation: float 3s ease-in-out infinite;
    filter: drop-shadow(0 0 10px rgba(255, 215, 0, 0.5));
}

.diet-suggestion-title {
    background: linear-gradient(135deg, #ffd700 0%, #ffa500 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-size: 1.8rem;
    font-weight: 900;
    margin: 0;
    position: relative;
    z-index: 1;
}

.diet-suggestion-text {
    color: #ccc;
    font-size: 1rem;
    line-height: 1.8;
    margin-bottom: 2rem;
    position: relative;
    z-index: 1;
}

.diet-suggestion-text strong {
    color: #ffd700;
    font-weight: 700;
}

.diet-plan-create-btn {
    width: 100%;
    background: linear-gradient(135deg, #ffd700 0%, #ffa500 100%);
    border: none;
    border-radius: 16px;
    padding: 1.25rem 2rem;
    color: #000;
    font-size: 1.1rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    position: relative;
    z-index: 1;
    box-shadow: 0 8px 25px rgba(255, 215, 0, 0.3);
}

.diet-plan-create-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(255, 215, 0, 0.4);
    background: linear-gradient(135deg, #ffed4e 0%, #ffb84d 100%);
}

.diet-plan-create-btn:active {
    transform: translateY(-1px);
}

.diet-plan-create-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

.btn-arrow {
    font-size: 1.5rem;
    transition: transform 0.3s ease;
}

.diet-plan-create-btn:hover .btn-arrow {
    transform: translateX(5px);
}

/* Başarı Mesajı */
.diet-plan-success {
    text-align: center;
    padding: 2rem;
}

.success-icon {
    font-size: 4rem;
    color: #4ade80;
    margin-bottom: 1rem;
    animation: scaleIn 0.5s ease;
}

.success-message {
    color: #4ade80;
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.success-loading {
    color: #aaa;
    font-size: 0.95rem;
    margin-top: 0.5rem;
}

/* Animasyonlar */
@keyframes bmiFadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes dietSuggestionFadeIn {
    from {
        opacity: 0;
        transform: translateY(30px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
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

@keyframes scaleIn {
    from {
        transform: scale(0);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

.hidden {
    display: none !important;
}

/* Responsive */
@media (max-width: 768px) {
    .bmi-result-value {
        font-size: 3.5rem;
    }
    
    .bmi-result-icon {
        font-size: 3rem;
    }
    
    .diet-suggestion-title {
        font-size: 1.4rem;
    }
    
    .bmi-result-card,
    .bmi-diet-suggestion-card {
        padding: 2rem 1.5rem;
    }
}
</style>

<?php
$extra_js = ['assets/js/bmi.js'];
require_once '../includes/footer.php';
?>

