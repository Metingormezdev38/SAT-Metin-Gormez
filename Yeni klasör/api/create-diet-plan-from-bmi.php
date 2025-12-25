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
$bmi = floatval($_POST['bmi'] ?? 0);
$height = floatval($_POST['height'] ?? 0);
$weight = floatval($_POST['weight'] ?? 0);

if ($bmi <= 0 || $height <= 0 || $weight <= 0) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz BMI değerleri']);
    exit;
}

try {
    // Kullanıcı profil bilgilerini al
    $stmt = $db->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $age = $profile['age'] ?? 30; // Varsayılan yaş
    $gender = $profile['gender'] ?? 'male'; // Varsayılan cinsiyet
    
    // BMI kategorisine göre diyet planı belirle
    $plan_data = getDietPlanByBMI($bmi, $weight, $height, $age, $gender);
    
    // Diyet planı oluştur
    $start_date = date('Y-m-d');
    $end_date = date('Y-m-d', strtotime('+30 days')); // 30 günlük plan
    
    $stmt = $db->prepare("INSERT INTO diet_plans (user_id, plan_name, daily_calories, description, start_date, end_date, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
    $stmt->execute([
        $user_id,
        $plan_data['plan_name'],
        $plan_data['daily_calories'],
        $plan_data['description'],
        $start_date,
        $end_date
    ]);
    
    $plan_id = $db->lastInsertId();
    
    // Örnek öğünler oluştur (haftalık plan)
    createSampleMeals($db, $plan_id, $plan_data);
    
    echo json_encode([
        'success' => true,
        'message' => 'BMI\'nize göre diyet planı başarıyla oluşturuldu',
        'plan_id' => $plan_id,
        'plan_name' => $plan_data['plan_name'],
        'redirect' => SITE_URL . 'user/diet-plan-detail.php?id=' . $plan_id
    ]);
} catch (PDOException $e) {
    error_log("Diet plan from BMI error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Bir hata oluştu: ' . $e->getMessage()]);
}

function getDietPlanByBMI($bmi, $weight, $height, $age, $gender) {
    // Bazal Metabolizma Hızı (BMR) hesaplama
    if ($gender === 'female') {
        $bmr = 447.593 + (9.247 * $weight) + (3.098 * $height) - (4.330 * $age);
    } else {
        $bmr = 88.362 + (13.397 * $weight) + (4.799 * $height) - (5.677 * $age);
    }
    
    if ($bmi < 18.5) {
        // Zayıf - Kilo alma
        $daily_calories = round($bmr * 1.4 + 500); // Hafif aktivite + ekstra kalori
        return [
            'plan_name' => 'Kilo Alma Diyet Planı',
            'daily_calories' => $daily_calories,
            'description' => 'BMI değeriniz zayıf kategorisinde. Sağlıklı kilo almak için yüksek kalorili, besleyici bir diyet planı hazırlandı. Protein, karbonhidrat ve sağlıklı yağlar dengeli şekilde içerir.',
            'meals' => getWeightGainMeals($daily_calories)
        ];
    } elseif ($bmi < 25) {
        // Normal - Kilo koruma
        $daily_calories = round($bmr * 1.4); // Hafif aktivite
        return [
            'plan_name' => 'Kilo Koruma Diyet Planı',
            'daily_calories' => $daily_calories,
            'description' => 'BMI değeriniz normal kategorisinde. Mevcut kilonuzu korumak için dengeli bir beslenme planı hazırlandı. Sağlıklı yaşam tarzınızı sürdürmenize yardımcı olacak.',
            'meals' => getMaintenanceMeals($daily_calories)
        ];
    } elseif ($bmi < 30) {
        // Fazla Kilolu - Kilo verme
        $daily_calories = round($bmr * 1.4 - 500); // Hafif aktivite - kalori açığı
        return [
            'plan_name' => 'Kilo Verme Diyet Planı',
            'daily_calories' => $daily_calories,
            'description' => 'BMI değeriniz fazla kilolu kategorisinde. Sağlıklı kilo vermek için düşük kalorili, besleyici bir diyet planı hazırlandı. Protein ağırlıklı, düşük karbonhidratlı bir yaklaşım içerir.',
            'meals' => getWeightLossMeals($daily_calories)
        ];
    } else {
        // Obez - Agresif kilo verme
        $daily_calories = round($bmr * 1.2 - 700); // Düşük aktivite - büyük kalori açığı
        if ($daily_calories < 1200) $daily_calories = 1200; // Minimum güvenli kalori
        return [
            'plan_name' => 'Kilo Verme Diyet Planı (Yoğun)',
            'daily_calories' => $daily_calories,
            'description' => 'BMI değeriniz obez kategorisinde. Sağlıklı ve sürdürülebilir kilo vermek için düşük kalorili, yüksek proteinli bir diyet planı hazırlandı. Mutlaka bir uzmana danışmanızı öneririz.',
            'meals' => getIntenseWeightLossMeals($daily_calories)
        ];
    }
}

function getWeightGainMeals($calories) {
    // Kilo alma için öğünler
    return [
        ['type' => 'breakfast', 'name' => 'Yüksek Proteinli Kahvaltı', 'calories' => round($calories * 0.25), 'protein' => 30, 'carbs' => 60, 'fat' => 20, 'desc' => 'Yumurta, peynir, tam tahıllı ekmek, zeytin, bal'],
        ['type' => 'lunch', 'name' => 'Besleyici Öğle Yemeği', 'calories' => round($calories * 0.35), 'protein' => 40, 'carbs' => 80, 'fat' => 25, 'desc' => 'Tavuk göğsü, pilav, sebze, yoğurt'],
        ['type' => 'dinner', 'name' => 'Protein Ağırlıklı Akşam', 'calories' => round($calories * 0.30), 'protein' => 35, 'carbs' => 50, 'fat' => 20, 'desc' => 'Balık, patates, salata'],
        ['type' => 'snack', 'name' => 'Ara Öğün', 'calories' => round($calories * 0.10), 'protein' => 15, 'carbs' => 30, 'fat' => 10, 'desc' => 'Kuruyemiş, meyve, protein shake']
    ];
}

function getMaintenanceMeals($calories) {
    // Kilo koruma için öğünler
    return [
        ['type' => 'breakfast', 'name' => 'Dengeli Kahvaltı', 'calories' => round($calories * 0.25), 'protein' => 25, 'carbs' => 50, 'fat' => 15, 'desc' => 'Yumurta, peynir, tam tahıllı ekmek, domates, salatalık'],
        ['type' => 'lunch', 'name' => 'Sağlıklı Öğle Yemeği', 'calories' => round($calories * 0.35), 'protein' => 35, 'carbs' => 60, 'fat' => 20, 'desc' => 'Izgara tavuk, bulgur pilavı, sebze yemeği, salata'],
        ['type' => 'dinner', 'name' => 'Hafif Akşam Yemeği', 'calories' => round($calories * 0.30), 'protein' => 30, 'carbs' => 40, 'fat' => 15, 'desc' => 'Balık, sebze, salata'],
        ['type' => 'snack', 'name' => 'Sağlıklı Ara Öğün', 'calories' => round($calories * 0.10), 'protein' => 10, 'carbs' => 20, 'fat' => 5, 'desc' => 'Meyve, yoğurt, kuruyemiş']
    ];
}

function getWeightLossMeals($calories) {
    // Kilo verme için öğünler
    return [
        ['type' => 'breakfast', 'name' => 'Protein Ağırlıklı Kahvaltı', 'calories' => round($calories * 0.25), 'protein' => 30, 'carbs' => 30, 'fat' => 10, 'desc' => 'Yumurta, az yağlı peynir, tam tahıllı ekmek, domates'],
        ['type' => 'lunch', 'name' => 'Düşük Kalorili Öğle', 'calories' => round($calories * 0.35), 'protein' => 40, 'carbs' => 40, 'fat' => 15, 'desc' => 'Izgara tavuk, sebze, salata'],
        ['type' => 'dinner', 'name' => 'Hafif Akşam Yemeği', 'calories' => round($calories * 0.30), 'protein' => 35, 'carbs' => 25, 'fat' => 10, 'desc' => 'Balık, buharda sebze, salata'],
        ['type' => 'snack', 'name' => 'Düşük Kalorili Ara Öğün', 'calories' => round($calories * 0.10), 'protein' => 10, 'carbs' => 10, 'fat' => 5, 'desc' => 'Meyve, yoğurt']
    ];
}

function getIntenseWeightLossMeals($calories) {
    // Yoğun kilo verme için öğünler
    return [
        ['type' => 'breakfast', 'name' => 'Düşük Kalorili Kahvaltı', 'calories' => round($calories * 0.25), 'protein' => 25, 'carbs' => 20, 'fat' => 8, 'desc' => 'Yumurta beyazı, az yağlı peynir, domates, salatalık'],
        ['type' => 'lunch', 'name' => 'Protein Odaklı Öğle', 'calories' => round($calories * 0.35), 'protein' => 45, 'carbs' => 30, 'fat' => 10, 'desc' => 'Izgara tavuk göğsü, sebze, salata'],
        ['type' => 'dinner', 'name' => 'Çok Hafif Akşam', 'calories' => round($calories * 0.30), 'protein' => 30, 'carbs' => 20, 'fat' => 8, 'desc' => 'Buharda balık, sebze, salata'],
        ['type' => 'snack', 'name' => 'Minimal Ara Öğün', 'calories' => round($calories * 0.10), 'protein' => 8, 'carbs' => 8, 'fat' => 3, 'desc' => 'Meyve veya yoğurt']
    ];
}

function createSampleMeals($db, $plan_id, $plan_data) {
    $days = [1 => 'Pazartesi', 2 => 'Salı', 3 => 'Çarşamba', 4 => 'Perşembe', 5 => 'Cuma', 6 => 'Cumartesi', 7 => 'Pazar'];
    $meals = $plan_data['meals'];
    
    // Her gün için öğünleri oluştur
    foreach ($days as $day_num => $day_name) {
        foreach ($meals as $meal) {
            $stmt = $db->prepare("INSERT INTO diet_meals (diet_plan_id, meal_type, meal_name, calories, protein, carbs, fat, description, day_of_week) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $plan_id,
                $meal['type'],
                $meal['name'] . ' - ' . $day_name,
                $meal['calories'],
                $meal['protein'],
                $meal['carbs'],
                $meal['fat'],
                $meal['desc'],
                $day_num
            ]);
        }
    }
}
?>

