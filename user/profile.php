<?php
$page_title = 'Profil Düzenle';
require_once '../includes/header.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Kullanıcı profil bilgilerini çek
$stmt = $db->prepare("SELECT u.*, up.height, up.weight, up.age, up.gender, up.activity_level, up.goal FROM users u LEFT JOIN user_profiles up ON u.id = up.user_id WHERE u.id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<div class="form-container" style="max-width: 700px;">
    <h2 class="form-title">Profil Bilgilerimi Düzenle</h2>
    <div id="alert-container"></div>
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
        <button type="submit" class="btn btn-primary" style="width: 100%;">Kaydet</button>
    </form>
</div>

<?php
$extra_js = ['assets/js/profile.js'];
require_once '../includes/footer.php';
?>

