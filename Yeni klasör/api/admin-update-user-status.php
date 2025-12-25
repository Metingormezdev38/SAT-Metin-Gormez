<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek metodu']);
    exit;
}

$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';

if ($user_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz kullanıcı ID']);
    exit;
}

if (!in_array($status, ['active', 'inactive'])) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz durum']);
    exit;
}

// Admin kendisini pasif yapamaz
if ($user_id == $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'Kendi durumunuzu değiştiremezsiniz']);
    exit;
}

try {
    $stmt = $db->prepare("UPDATE users SET status = ? WHERE id = ? AND role = 'user'");
    $stmt->execute([$status, $user_id]);
    
    if ($stmt->rowCount() > 0) {
        $status_text = $status === 'active' ? 'aktif' : 'pasif';
        echo json_encode(['success' => true, 'message' => "Kullanıcı durumu {$status_text} olarak güncellendi"]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Kullanıcı bulunamadı veya güncellenemedi']);
    }
} catch (PDOException $e) {
    error_log("Admin update user status error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Bir hata oluştu']);
}
?>

