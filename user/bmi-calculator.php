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
        
        <div id="bmi-result" class="bmi-result hidden">
            <div class="bmi-value" id="bmi-value"></div>
            <div class="bmi-category" id="bmi-category"></div>
            <p style="margin-top: 1rem; color: var(--gray-light);" id="bmi-description"></p>
        </div>
    </div>

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

<?php
$extra_js = ['assets/js/bmi.js'];
require_once '../includes/footer.php';
?>

