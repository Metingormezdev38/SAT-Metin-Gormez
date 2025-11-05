<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek metodu']);
    exit;
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Kullanıcı adı ve şifre gereklidir']);
    exit;
}

try {
    $stmt = $db->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND role = 'admin' AND status = 'active'");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Admin kullanıcısı bulunamadı']);
        exit;
    }

    // Şifre kontrolü
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];

        echo json_encode([
            'success' => true,
            'message' => 'Giriş başarılı',
            'redirect' => SITE_URL . 'admin/dashboard.php'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Şifre hatalı. setup-passwords.php scriptini çalıştırarak şifreleri güncelleyin.']);
    }
} catch (PDOException $e) {
    error_log("Admin login error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Bir hata oluştu: ' . $e->getMessage()]);
}
?>

