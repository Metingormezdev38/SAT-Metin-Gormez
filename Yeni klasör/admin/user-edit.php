<?php
$page_title = 'Kullanıcı Düzenle';
require_once '../includes/header.php';
requireAdmin();

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($user_id <= 0) {
    header('Location: ' . SITE_URL . 'admin/users.php');
    exit;
}

// Kullanıcı bilgilerini çek
$stmt = $db->prepare("SELECT u.*, up.height, up.weight, up.age, up.gender, up.activity_level, up.goal, up.bmi FROM users u LEFT JOIN user_profiles up ON u.id = up.user_id WHERE u.id = ? AND u.role = 'user'");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: ' . SITE_URL . 'admin/users.php');
    exit;
}
?>

<div class="dashboard">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 class="section-title">Kullanıcı Düzenle</h1>
        <a href="<?php echo SITE_URL; ?>admin/users.php" class="btn btn-secondary">← Geri Dön</a>
    </div>

    <div class="admin-edit-card">
        <div class="edit-header">
            <div class="edit-user-avatar">
                <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
            </div>
            <div>
                <h2 class="edit-title"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
                <p class="edit-subtitle">@<?php echo htmlspecialchars($user['username']); ?></p>
            </div>
        </div>

        <div id="alert-container"></div>

        <form id="edit-user-form" class="edit-form">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            
            <div class="form-section">
                <h3 class="section-heading">Kişisel Bilgiler</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="first_name" class="form-label">Ad</label>
                        <input type="text" id="first_name" name="first_name" class="form-input" 
                               value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name" class="form-label">Soyad</label>
                        <input type="text" id="last_name" name="last_name" class="form-input" 
                               value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="username" class="form-label">Kullanıcı Adı</label>
                        <input type="text" id="username" name="username" class="form-input" 
                               value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">E-posta</label>
                        <input type="email" id="email" name="email" class="form-input" 
                               value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone" class="form-label">Telefon</label>
                        <input type="tel" id="phone" name="phone" class="form-input" 
                               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-heading">Profil Bilgileri</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="height" class="form-label">Boy (cm)</label>
                        <input type="number" id="height" name="height" class="form-input" step="0.01" 
                               value="<?php echo $user['height'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="weight" class="form-label">Kilo (kg)</label>
                        <input type="number" id="weight" name="weight" class="form-input" step="0.01" 
                               value="<?php echo $user['weight'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="age" class="form-label">Yaş</label>
                        <input type="number" id="age" name="age" class="form-input" 
                               value="<?php echo $user['age'] ?? ''; ?>">
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
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-heading">Hesap Durumu</h3>
                <div class="form-group">
                    <label for="status" class="form-label">Durum</label>
                    <select id="status" name="status" class="form-input">
                        <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>Aktif</option>
                        <option value="inactive" <?php echo $user['status'] === 'inactive' ? 'selected' : ''; ?>>Pasif</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="window.location.href='<?php echo SITE_URL; ?>admin/users.php'">İptal</button>
                <button type="submit" class="btn btn-primary">Kaydet</button>
            </div>
        </form>
    </div>
</div>

<style>
.admin-edit-card {
    background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    border: 2px solid rgba(255, 215, 0, 0.2);
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.edit-header {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 2.5rem;
    padding-bottom: 2rem;
    border-bottom: 2px solid rgba(255, 215, 0, 0.2);
}

.edit-user-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ffd700 0%, #ffa500 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #000;
    font-weight: 700;
    font-size: 2rem;
    box-shadow: 0 4px 20px rgba(255, 215, 0, 0.4);
}

.edit-title {
    background: linear-gradient(135deg, #ffd700 0%, #ffa500 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-size: 2rem;
    font-weight: 900;
    margin: 0 0 0.25rem 0;
}

.edit-subtitle {
    color: #aaa;
    font-size: 1rem;
    margin: 0;
}

.form-section {
    margin-bottom: 2.5rem;
}

.section-heading {
    color: #ffd700;
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid rgba(255, 215, 0, 0.2);
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.form-group {
    margin-bottom: 0;
}

.form-label {
    display: block;
    color: #fff;
    margin-bottom: 0.5rem;
    font-weight: 600;
    font-size: 0.95rem;
}

.form-input {
    width: 100%;
    padding: 0.875rem 1rem;
    background: rgba(0, 0, 0, 0.4);
    border: 2px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    color: #fff;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-input:focus {
    outline: none;
    border-color: #ffd700;
    box-shadow: 0 0 15px rgba(255, 215, 0, 0.2);
    background: rgba(0, 0, 0, 0.6);
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 2.5rem;
    padding-top: 2rem;
    border-top: 2px solid rgba(255, 215, 0, 0.2);
}

.btn {
    padding: 0.875rem 2rem;
    border-radius: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    text-decoration: none;
    display: inline-block;
}

.btn-primary {
    background: linear-gradient(135deg, #ffd700 0%, #ffa500 100%);
    color: #000;
    box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 215, 0, 0.4);
}

.btn-secondary {
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
    border: 2px solid rgba(255, 255, 255, 0.2);
}

.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.4);
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .edit-header {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<script>
document.getElementById('edit-user-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Kaydediliyor...';
    
    fetch('<?php echo SITE_URL; ?>api/admin-update-user.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('alert-container', data.message, 'success');
            setTimeout(() => {
                window.location.href = '<?php echo SITE_URL; ?>admin/users.php';
            }, 1500);
        } else {
            showAlert('alert-container', data.message, 'error');
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('alert-container', 'Bir hata oluştu', 'error');
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
});

function showAlert(containerId, message, type) {
    const container = document.getElementById(containerId);
    const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
    container.innerHTML = `<div class="${alertClass}" style="padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">${message}</div>`;
}
</script>

<?php require_once '../includes/footer.php'; ?>

