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
$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$height = $_POST['height'] ?? null;
$weight = $_POST['weight'] ?? null;
$age = $_POST['age'] ?? null;
$gender = $_POST['gender'] ?? null;
$activity_level = $_POST['activity_level'] ?? 'moderate';
$goal = $_POST['goal'] ?? 'maintenance';

try {
    $db->beginTransaction();

    // Kullanıcı bilgilerini güncelle
    $stmt = $db->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ? WHERE id = ?");
    $stmt->execute([$first_name, $last_name, $email, $phone, $user_id]);

    // BMI hesapla
    $bmi = null;
    if ($height && $weight && $height > 0) {
        $height_m = $height / 100;
        $bmi = $weight / ($height_m * $height_m);
    }

    // Profil bilgilerini güncelle veya oluştur
    $stmt = $db->prepare("SELECT id FROM user_profiles WHERE user_id = ?");
    $stmt->execute([$user_id]);
    
    if ($stmt->fetch()) {
        $stmt = $db->prepare("UPDATE user_profiles SET height = ?, weight = ?, age = ?, gender = ?, activity_level = ?, goal = ?, bmi = ? WHERE user_id = ?");
        $stmt->execute([$height, $weight, $age, $gender, $activity_level, $goal, $bmi, $user_id]);
    } else {
        $stmt = $db->prepare("INSERT INTO user_profiles (user_id, height, weight, age, gender, activity_level, goal, bmi) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $height, $weight, $age, $gender, $activity_level, $goal, $bmi]);
    }

    $db->commit();
    echo json_encode(['success' => true, 'message' => 'Profil başarıyla güncellendi', 'bmi' => $bmi]);
} catch (PDOException $e) {
    $db->rollBack();
    error_log("Profile update error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Bir hata oluştu']);
}
?>

