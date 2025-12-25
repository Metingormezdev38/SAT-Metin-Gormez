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

if ($user_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz kullanıcı ID']);
    exit;
}

// Admin kendisini silemez
if ($user_id == $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'Kendinizi silemezsiniz']);
    exit;
}

try {
    // Kullanıcının admin olmadığından emin ol
    $stmt = $db->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Kullanıcı bulunamadı']);
        exit;
    }
    
    if ($user['role'] === 'admin') {
        echo json_encode(['success' => false, 'message' => 'Admin kullanıcıları silinemez']);
        exit;
    }
    
    // Kullanıcıyı sil (CASCADE ile ilgili kayıtlar da silinecek)
    $stmt = $db->prepare("DELETE FROM users WHERE id = ? AND role = 'user'");
    $stmt->execute([$user_id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Kullanıcı başarıyla silindi']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Kullanıcı silinemedi']);
    }
} catch (PDOException $e) {
    error_log("Admin delete user error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Bir hata oluştu: ' . $e->getMessage()]);
}
?>

