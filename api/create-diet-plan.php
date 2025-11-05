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
$plan_name = $_POST['plan_name'] ?? '';
$daily_calories = intval($_POST['daily_calories'] ?? 0);
$description = $_POST['description'] ?? '';
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';

if (empty($plan_name) || empty($daily_calories) || empty($start_date) || empty($end_date)) {
    echo json_encode(['success' => false, 'message' => 'Tüm zorunlu alanları doldurun']);
    exit;
}

try {
    $stmt = $db->prepare("INSERT INTO diet_plans (user_id, plan_name, daily_calories, description, start_date, end_date, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
    $stmt->execute([$user_id, $plan_name, $daily_calories, $description, $start_date, $end_date]);
    
    $plan_id = $db->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Diyet planı başarıyla oluşturuldu',
        'plan_id' => $plan_id,
        'redirect' => SITE_URL . 'user/diet-plan-detail.php?id=' . $plan_id
    ]);
} catch (PDOException $e) {
    error_log("Diet plan creation error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Bir hata oluştu']);
}
?>

