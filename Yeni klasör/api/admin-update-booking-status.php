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

$booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';

if ($booking_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz randevu ID']);
    exit;
}

if (!in_array($status, ['pending', 'confirmed', 'completed', 'cancelled'])) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz durum']);
    exit;
}

try {
    $stmt = $db->prepare("UPDATE consultant_bookings SET status = ? WHERE id = ?");
    $stmt->execute([$status, $booking_id]);
    
    if ($stmt->rowCount() > 0) {
        $status_texts = [
            'confirmed' => 'onaylandı',
            'cancelled' => 'iptal edildi',
            'completed' => 'tamamlandı'
        ];
        $message = 'Randevu başarıyla ' . ($status_texts[$status] ?? 'güncellendi');
        echo json_encode(['success' => true, 'message' => $message]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Randevu bulunamadı']);
    }
} catch (PDOException $e) {
    error_log("Admin update booking status error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Bir hata oluştu']);
}
?>

