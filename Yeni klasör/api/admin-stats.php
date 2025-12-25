<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim']);
    exit;
}

try {
    $stats = [];

    // Toplam kullanıcı sayısı
    $stmt = $db->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
    $stats['total_users'] = $stmt->fetchColumn();

    // Toplam danışman sayısı
    $stmt = $db->query("SELECT COUNT(*) FROM consultants");
    $stats['total_consultants'] = $stmt->fetchColumn();

    // Toplam diyet planı sayısı
    $stmt = $db->query("SELECT COUNT(*) FROM diet_plans");
    $stats['total_diet_plans'] = $stmt->fetchColumn();

    // Toplam randevu sayısı
    $stmt = $db->query("SELECT COUNT(*) FROM consultant_bookings");
    $stats['total_bookings'] = $stmt->fetchColumn();

    // Aktif kullanıcılar (son 30 gün)
    $stmt = $db->query("SELECT COUNT(DISTINCT user_id) FROM user_profiles WHERE updated_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $stats['active_users'] = $stmt->fetchColumn();

    // Son kayıtlar
    $stmt = $db->query("SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND role = 'user'");
    $stats['new_users_week'] = $stmt->fetchColumn();

    echo json_encode(['success' => true, 'stats' => $stats]);
} catch (PDOException $e) {
    error_log("Admin stats error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Bir hata oluştu']);
}
?>

