<?php
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../config/config.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Oturum açmanız gerekiyor.']);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek metodu.']);
    exit;
}

$consultant_id = $_POST['consultant_id'] ?? null;

if (!$consultant_id) {
    echo json_encode(['success' => false, 'message' => 'Danışman ID gerekli.']);
    exit;
}

try {
    // Danışmanın var olup olmadığını kontrol et
    $stmt = $db->prepare("SELECT c.*, u.first_name, u.last_name FROM consultants c JOIN users u ON c.user_id = u.id WHERE c.id = ? AND c.status = 'available'");
    $stmt->execute([$consultant_id]);
    $consultant = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$consultant) {
        echo json_encode(['success' => false, 'message' => 'Danışman bulunamadı veya müsait değil.']);
        exit;
    }
    
    // Kullanıcının daha önce seçtiği danışmanları kontrol et
    // Eğer aynı danışman zaten seçilmişse, sadece onay mesajı döndür
    $stmt = $db->prepare("SELECT id FROM consultant_bookings WHERE user_id = ? AND consultant_id = ? AND status IN ('pending', 'confirmed') ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$user_id, $consultant_id]);
    $existing_booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing_booking) {
        echo json_encode([
            'success' => true, 
            'message' => 'Bu danışman zaten seçilmiş.',
            'already_selected' => true
        ]);
        exit;
    }
    
    // Yeni danışman seçimi kaydı oluştur
    // Booking date olarak bugünün tarihini kullan
    $booking_date = date('Y-m-d H:i:s');
    
    $stmt = $db->prepare("INSERT INTO consultant_bookings (user_id, consultant_id, booking_date, status, notes) VALUES (?, ?, ?, 'confirmed', ?)");
    $stmt->execute([
        $user_id, 
        $consultant_id, 
        $booking_date,
        'Seçilen danışman: ' . $consultant['first_name'] . ' ' . $consultant['last_name']
    ]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Danışman başarıyla seçildi!',
        'consultant_name' => $consultant['first_name'] . ' ' . $consultant['last_name']
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Bir hata oluştu: ' . $e->getMessage()]);
}

