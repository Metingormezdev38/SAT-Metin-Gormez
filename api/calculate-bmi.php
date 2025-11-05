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
$height = floatval($_POST['height'] ?? 0);
$weight = floatval($_POST['weight'] ?? 0);

if ($height <= 0 || $weight <= 0) {
    echo json_encode(['success' => false, 'message' => 'Boy ve kilo değerleri geçerli olmalıdır']);
    exit;
}

try {
    $height_m = $height / 100;
    $bmi = $weight / ($height_m * $height_m);
    
    // BMI kategorisi
    if ($bmi < 18.5) {
        $category = 'Zayıf';
        $description = 'İdeal kilonuzun altındasınız. Sağlıklı kilo almak için bir uzmana danışmanızı öneririz.';
    } elseif ($bmi < 25) {
        $category = 'Normal';
        $description = 'İdeal kilonuzdasınız! Sağlıklı yaşam tarzınızı devam ettirin.';
    } elseif ($bmi < 30) {
        $category = 'Fazla Kilolu';
        $description = 'İdeal kilonuzun üzerindesiniz. Sağlıklı beslenme ve düzenli egzersiz ile ideal kilonuza ulaşabilirsiniz.';
    } else {
        $category = 'Obez';
        $description = 'Sağlık için kilo vermeniz önerilir. Bir uzmana danışmanızı öneririz.';
    }

    // Profilde güncelle
    $stmt = $db->prepare("SELECT id FROM user_profiles WHERE user_id = ?");
    $stmt->execute([$user_id]);
    
    if ($stmt->fetch()) {
        $stmt = $db->prepare("UPDATE user_profiles SET height = ?, weight = ?, bmi = ? WHERE user_id = ?");
        $stmt->execute([$height, $weight, $bmi, $user_id]);
    } else {
        $stmt = $db->prepare("INSERT INTO user_profiles (user_id, height, weight, bmi) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $height, $weight, $bmi]);
    }

    echo json_encode([
        'success' => true,
        'bmi' => round($bmi, 2),
        'category' => $category,
        'description' => $description
    ]);
} catch (PDOException $e) {
    error_log("BMI calculation error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Bir hata oluştu']);
}
?>

