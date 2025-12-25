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
$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$height = $_POST['height'] ?? null;
$weight = $_POST['weight'] ?? null;
$age = $_POST['age'] ?? null;
$gender = $_POST['gender'] ?? null;
$activity_level = $_POST['activity_level'] ?? 'moderate';
$goal = $_POST['goal'] ?? 'maintenance';
$status = $_POST['status'] ?? 'active';

if ($user_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz kullanıcı ID']);
    exit;
}

if (empty($first_name) || empty($last_name) || empty($username) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Gerekli alanlar doldurulmalı']);
    exit;
}

// Admin kendisini pasif yapamaz
if ($user_id == $_SESSION['user_id'] && $status === 'inactive') {
    echo json_encode(['success' => false, 'message' => 'Kendi durumunuzu değiştiremezsiniz']);
    exit;
}

try {
    $db->beginTransaction();

    // Kullanıcı bilgilerini güncelle
    $stmt = $db->prepare("UPDATE users SET first_name = ?, last_name = ?, username = ?, email = ?, phone = ?, status = ? WHERE id = ? AND role = 'user'");
    $stmt->execute([$first_name, $last_name, $username, $email, $phone, $status, $user_id]);

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
    echo json_encode(['success' => true, 'message' => 'Kullanıcı bilgileri başarıyla güncellendi']);
} catch (PDOException $e) {
    $db->rollBack();
    error_log("Admin update user error: " . $e->getMessage());
    
    // Kullanıcı adı veya e-posta zaten kullanılıyorsa
    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
        echo json_encode(['success' => false, 'message' => 'Bu kullanıcı adı veya e-posta zaten kullanılıyor']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Bir hata oluştu: ' . $e->getMessage()]);
    }
}
?>

