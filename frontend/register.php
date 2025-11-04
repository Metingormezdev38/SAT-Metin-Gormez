<?php
session_start();
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Tüm alanlar doldurulmalıdır';
    } elseif ($password !== $confirmPassword) {
        $error = 'Şifreler eşleşmiyor';
    } elseif (strlen($password) < 6) {
        $error = 'Şifre en az 6 karakter olmalıdır';
    } else {
        $response = apiCall('/auth/register', 'POST', [
            'name' => $name,
            'email' => $email,
            'password' => $password
        ]);

        if ($response['status'] === 201 && isset($response['data']['success']) && $response['data']['success']) {
            $_SESSION['auth_token'] = $response['data']['token'];
            $_SESSION['user_info'] = $response['data']['user'];
            $success = 'Kayıt başarılı! Yönlendiriliyorsunuz...';
            header('Refresh: 2; url=index.php');
        } else {
            $error = $response['data']['message'] ?? 'Kayıt sırasında bir hata oluştu';
        }
    }
}

require_once 'includes/header.php';
$pageTitle = 'Kayıt Ol';
?>

<div class="container">
    <div class="form-container">
        <h2 style="text-align: center; margin-bottom: 2rem; color: var(--primary-black);">Üye Ol</h2>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Ad Soyad</label>
                <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="email">E-posta</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="password">Şifre</label>
                <input type="password" id="password" name="password" required minlength="6">
            </div>

            <div class="form-group">
                <label for="confirm_password">Şifre Tekrar</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
            </div>

            <button type="submit" class="btn" style="width: 100%;">Kayıt Ol</button>
        </form>

        <p style="text-align: center; margin-top: 1rem;">
            Zaten üye misiniz? <a href="login.php" style="color: var(--primary-yellow);">Giriş yapın</a>
        </p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
