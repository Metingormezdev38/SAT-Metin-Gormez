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

$membership_id = isset($_POST['membership_id']) ? intval($_POST['membership_id']) : 0;

if ($membership_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz üyelik ID']);
    exit;
}

try {
    $stmt = $db->prepare("UPDATE user_memberships SET status = 'cancelled' WHERE id = ? AND status = 'active'");
    $stmt->execute([$membership_id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Üyelik başarıyla iptal edildi']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Üyelik bulunamadı veya zaten iptal edilmiş']);
    }
} catch (PDOException $e) {
    error_log("Admin cancel membership error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Bir hata oluştu']);
}
?>

