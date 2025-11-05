<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Giriş yapmanız gerekiyor']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek metodu']);
    exit;
}

$user_id = $_SESSION['user_id'];
$consultant_id = intval($_POST['consultant_id'] ?? 0);
$booking_date = trim($_POST['booking_date'] ?? '');
$notes = $_POST['notes'] ?? '';

// Debug için log
error_log("Booking attempt - consultant_id: $consultant_id, booking_date: $booking_date");

if (empty($consultant_id) || $consultant_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Lütfen bir danışman seçin']);
    exit;
}

if (empty($booking_date)) {
    echo json_encode(['success' => false, 'message' => 'Lütfen randevu tarihi ve saatini seçin']);
    exit;
}

try {
    // Danışmanın müsait olup olmadığını kontrol et
    $stmt = $db->prepare("SELECT status FROM consultants WHERE id = ?");
    $stmt->execute([$consultant_id]);
    $consultant = $stmt->fetch();
    
    if (!$consultant) {
        echo json_encode(['success' => false, 'message' => 'Danışman bulunamadı']);
        exit;
    }

    if ($consultant['status'] !== 'available') {
        echo json_encode(['success' => false, 'message' => 'Bu danışman şu anda müsait değil']);
        exit;
    }

    // Randevu oluştur
    $stmt = $db->prepare("INSERT INTO consultant_bookings (user_id, consultant_id, booking_date, notes, status) VALUES (?, ?, ?, ?, 'pending')");
    $stmt->execute([$user_id, $consultant_id, $booking_date, $notes]);

    echo json_encode([
        'success' => true,
        'message' => 'Randevunuz başarıyla oluşturuldu. Onay bekleniyor.'
    ]);
} catch (PDOException $e) {
    error_log("Booking error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Bir hata oluştu']);
}
?>

