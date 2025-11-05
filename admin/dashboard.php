<?php
$page_title = 'Admin Paneli';
require_once '../includes/header.php';
requireAdmin();

$user_id = $_SESSION['user_id'];

// Kullanıcıları çek
$stmt = $db->query("SELECT u.*, up.bmi FROM users u LEFT JOIN user_profiles up ON u.id = up.user_id WHERE u.role = 'user' ORDER BY u.created_at DESC LIMIT 10");
$recent_users = $stmt->fetchAll();

// Diyet planlarını çek
$stmt = $db->query("SELECT dp.*, u.first_name, u.last_name FROM diet_plans dp JOIN users u ON dp.user_id = u.id ORDER BY dp.created_at DESC LIMIT 10");
$recent_diet_plans = $stmt->fetchAll();

// Randevuları çek
$stmt = $db->query("SELECT cb.*, u.first_name as user_first_name, u.last_name as user_last_name, c.first_name as consultant_first_name, c.last_name as consultant_last_name FROM consultant_bookings cb JOIN users u ON cb.user_id = u.id JOIN consultants con ON cb.consultant_id = con.id JOIN users c ON con.user_id = c.id ORDER BY cb.created_at DESC LIMIT 10");
$recent_bookings = $stmt->fetchAll();
?>

<div class="dashboard">
    <h1 class="section-title">Admin Paneli</h1>
    <p class="text-center" style="color: var(--gray-light); margin-bottom: 2rem;">Hoş geldiniz, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
    
    <div class="dashboard-grid" id="stats-container">
        <div class="dashboard-card">
            <div class="dashboard-card-label">Toplam Kullanıcı</div>
            <div class="dashboard-card-value" id="stat-total-users">-</div>
        </div>
        <div class="dashboard-card">
            <div class="dashboard-card-label">Toplam Danışman</div>
            <div class="dashboard-card-value" id="stat-total-consultants">-</div>
        </div>
        <div class="dashboard-card">
            <div class="dashboard-card-label">Toplam Diyet Planı</div>
            <div class="dashboard-card-value" id="stat-total-diet-plans">-</div>
        </div>
        <div class="dashboard-card">
            <div class="dashboard-card-label">Toplam Randevu</div>
            <div class="dashboard-card-value" id="stat-total-bookings">-</div>
        </div>
        <div class="dashboard-card">
            <div class="dashboard-card-label">Aktif Kullanıcılar (30 gün)</div>
            <div class="dashboard-card-value" id="stat-active-users">-</div>
        </div>
        <div class="dashboard-card">
            <div class="dashboard-card-label">Yeni Kullanıcılar (7 gün)</div>
            <div class="dashboard-card-value" id="stat-new-users">-</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem; margin-top: 2rem;">
        <div class="card">
            <h2 class="card-title">Son Kayıt Olan Kullanıcılar</h2>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Ad Soyad</th>
                            <th>E-posta</th>
                            <th>Tarih</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo date('d.m.Y', strtotime($user['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <h2 class="card-title">Son Diyet Planları</h2>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Plan Adı</th>
                            <th>Kullanıcı</th>
                            <th>Tarih</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_diet_plans as $plan): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($plan['plan_name']); ?></td>
                            <td><?php echo htmlspecialchars($plan['first_name'] . ' ' . $plan['last_name']); ?></td>
                            <td><?php echo date('d.m.Y', strtotime($plan['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <h2 class="card-title">Son Randevular</h2>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kullanıcı</th>
                            <th>Danışman</th>
                            <th>Tarih</th>
                            <th>Durum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_bookings as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['user_first_name'] . ' ' . $booking['user_last_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['consultant_first_name'] . ' ' . $booking['consultant_last_name']); ?></td>
                            <td><?php echo date('d.m.Y H:i', strtotime($booking['booking_date'])); ?></td>
                            <td>
                                <?php 
                                $status_text = [
                                    'pending' => 'Beklemede',
                                    'confirmed' => 'Onaylandı',
                                    'completed' => 'Tamamlandı',
                                    'cancelled' => 'İptal Edildi'
                                ];
                                echo $status_text[$booking['status']] ?? $booking['status'];
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$extra_js = ['assets/js/admin-dashboard.js'];
require_once '../includes/footer.php';
?>

