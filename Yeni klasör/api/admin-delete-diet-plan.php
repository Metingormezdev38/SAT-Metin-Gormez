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

$plan_id = isset($_POST['plan_id']) ? intval($_POST['plan_id']) : 0;

if ($plan_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz plan ID']);
    exit;
}

try {
    // CASCADE ile diet_meals da silinecek
    $stmt = $db->prepare("DELETE FROM diet_plans WHERE id = ?");
    $stmt->execute([$plan_id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Diyet planı başarıyla silindi']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Diyet planı bulunamadı']);
    }
} catch (PDOException $e) {
    error_log("Admin delete diet plan error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Bir hata oluştu: ' . $e->getMessage()]);
}
?>

