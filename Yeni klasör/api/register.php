<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek metodu']);
    exit;
}

$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

// Validasyon
if (empty($first_name) || empty($last_name) || empty($username) || empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Tüm zorunlu alanları doldurun']);
    exit;
}

if ($password !== $password_confirm) {
    echo json_encode(['success' => false, 'message' => 'Şifreler eşleşmiyor']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Şifre en az 6 karakter olmalıdır']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Geçerli bir e-posta adresi girin']);
    exit;
}

try {
    // Kullanıcı adı ve e-posta kontrolü
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Bu kullanıcı adı veya e-posta zaten kullanılıyor']);
        exit;
    }

    // Kullanıcı oluştur
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (username, email, password, first_name, last_name, phone, role) VALUES (?, ?, ?, ?, ?, ?, 'user')");
    $stmt->execute([$username, $email, $hashed_password, $first_name, $last_name, $phone]);

    echo json_encode([
        'success' => true,
        'message' => 'Kayıt başarılı! Giriş sayfasına yönlendiriliyorsunuz...',
        'redirect' => SITE_URL . 'user/login.php'
    ]);
} catch (PDOException $e) {
    error_log("Register error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Bir hata oluştu: ' . $e->getMessage()]);
}
?>

