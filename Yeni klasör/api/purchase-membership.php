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
$membership_id = isset($_POST['membership_id']) ? intval($_POST['membership_id']) : 0;
$membership_period = isset($_POST['membership_period']) ? $_POST['membership_period'] : 'monthly';
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0;

// Üyelik paketleri
$memberships = [
    1 => 'Temel',
    2 => 'Premium',
    3 => 'VIP'
];

// Geçerli üyelik kontrolü
if (!isset($memberships[$membership_id]) || $membership_id == 0) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz üyelik paketi']);
    exit;
}

$membership_type = $memberships[$membership_id];

// Geçerli süre kontrolü
$valid_periods = ['monthly', 'quarterly', 'yearly'];
if (!in_array($membership_period, $valid_periods)) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz üyelik süresi']);
    exit;
}

try {
    $db->beginTransaction();

    // Eski aktif üyelikleri expired olarak işaretle
    $stmt = $db->prepare("UPDATE user_memberships SET status = 'expired' WHERE user_id = ? AND status = 'active'");
    $stmt->execute([$user_id]);

    // Başlangıç ve bitiş tarihlerini hesapla
    $start_date = date('Y-m-d');
    
    switch ($membership_period) {
        case 'monthly':
            $end_date = date('Y-m-d', strtotime('+1 month'));
            break;
        case 'quarterly':
            $end_date = date('Y-m-d', strtotime('+3 months'));
            break;
        case 'yearly':
            $end_date = date('Y-m-d', strtotime('+1 year'));
            break;
        default:
            $end_date = date('Y-m-d', strtotime('+1 month'));
    }

    // Yeni üyelik kaydı oluştur
    $stmt = $db->prepare("INSERT INTO user_memberships (user_id, membership_type, membership_period, price, start_date, end_date, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
    $stmt->execute([$user_id, $membership_type, $membership_period, $price, $start_date, $end_date]);

    $db->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Üyelik başarıyla satın alındı',
        'membership' => [
            'type' => $membership_type,
            'period' => $membership_period,
            'start_date' => $start_date,
            'end_date' => $end_date
        ]
    ]);
} catch (PDOException $e) {
    $db->rollBack();
    error_log("Membership purchase error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Bir hata oluştu: ' . $e->getMessage()]);
}
?>

