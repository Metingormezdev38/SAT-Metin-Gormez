<?php
session_start();
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'E-posta ve şifre gereklidir';
    } else {
        $response = apiCall('/auth/login', 'POST', [
            'email' => $email,
            'password' => $password
        ]);

        if ($response['status'] === 200 && isset($response['data']['success']) && $response['data']['success']) {
            $_SESSION['auth_token'] = $response['data']['token'];
            $_SESSION['user_info'] = $response['data']['user'];
            header('Location: index.php');
            exit;
        } else {
            $error = $response['data']['message'] ?? 'Giriş başarısız';
        }
    }
}

require_once 'includes/header.php';
$pageTitle = 'Giriş Yap';
?>

<div class="container">
    <div class="form-container">
        <h2 style="text-align: center; margin-bottom: 2rem; color: var(--primary-black);">Giriş Yap</h2>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email">E-posta</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="password">Şifre</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn" style="width: 100%;">Giriş Yap</button>
        </form>

        <p style="text-align: center; margin-top: 1rem;">
            Henüz üye değil misiniz? <a href="register.php" style="color: var(--primary-yellow);">Kayıt olun</a>
        </p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
