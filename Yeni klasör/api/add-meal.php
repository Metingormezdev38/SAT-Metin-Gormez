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
$diet_plan_id = intval($_POST['diet_plan_id'] ?? 0);
$meal_type = $_POST['meal_type'] ?? '';
$meal_name = $_POST['meal_name'] ?? '';
$day_of_week = intval($_POST['day_of_week'] ?? 0);
$calories = $_POST['calories'] ?? null;
$protein = $_POST['protein'] ?? null;
$carbs = $_POST['carbs'] ?? null;
$fat = $_POST['fat'] ?? null;
$description = $_POST['meal_description'] ?? $_POST['description'] ?? '';

// Validasyon
$errors = [];
if (empty($diet_plan_id) || $diet_plan_id <= 0) {
    $errors[] = 'Diyet planı seçilmedi';
}
if (empty($meal_type)) {
    $errors[] = 'Öğün tipi seçilmedi';
}
if (empty($meal_name)) {
    $errors[] = 'Öğün adı girilmedi';
}
if (empty($day_of_week) || $day_of_week <= 0) {
    $errors[] = 'Gün seçilmedi';
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

try {
    // Planın kullanıcıya ait olduğunu kontrol et
    $stmt = $db->prepare("SELECT id FROM diet_plans WHERE id = ? AND user_id = ?");
    $stmt->execute([$diet_plan_id, $user_id]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Bu plan size ait değil']);
        exit;
    }

    $stmt = $db->prepare("INSERT INTO diet_meals (diet_plan_id, meal_type, meal_name, calories, protein, carbs, fat, description, day_of_week) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$diet_plan_id, $meal_type, $meal_name, $calories, $protein, $carbs, $fat, $description, $day_of_week]);

    echo json_encode([
        'success' => true,
        'message' => 'Öğün başarıyla eklendi',
        'reload' => true
    ]);
} catch (PDOException $e) {
    error_log("Meal addition error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Bir hata oluştu: ' . $e->getMessage()]);
}
?>

