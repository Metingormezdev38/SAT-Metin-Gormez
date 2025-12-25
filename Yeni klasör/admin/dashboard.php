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

    <!-- Yönetim Menüsü -->
    <div class="admin-management-section">
        <h2 class="section-subtitle" style="margin-top: 3rem; margin-bottom: 1.5rem; color: #ffd700; font-size: 1.8rem;">Yönetim Paneli</h2>
        <div class="admin-management-grid">
            <a href="<?php echo SITE_URL; ?>admin/users.php" class="admin-management-card">
                <div class="management-icon">👥</div>
                <h3 class="management-title">Kullanıcı Yönetimi</h3>
                <p class="management-description">Kullanıcıları görüntüle, düzenle, aktif/pasif yap veya sil</p>
            </a>
            <a href="<?php echo SITE_URL; ?>admin/memberships.php" class="admin-management-card">
                <div class="management-icon">💳</div>
                <h3 class="management-title">Üyelik Yönetimi</h3>
                <p class="management-description">Aktif üyelikleri görüntüle ve yönet</p>
            </a>
            <a href="<?php echo SITE_URL; ?>admin/diet-plans.php" class="admin-management-card">
                <div class="management-icon">📋</div>
                <h3 class="management-title">Diyet Planları</h3>
                <p class="management-description">Tüm diyet planlarını görüntüle ve yönet</p>
            </a>
            <a href="<?php echo SITE_URL; ?>admin/consultants.php" class="admin-management-card">
                <div class="management-icon">👨‍⚕️</div>
                <h3 class="management-title">Danışman Yönetimi</h3>
                <p class="management-description">Danışmanları görüntüle ve yönet</p>
            </a>
            <a href="<?php echo SITE_URL; ?>admin/bookings.php" class="admin-management-card">
                <div class="management-icon">📅</div>
                <h3 class="management-title">Randevu Yönetimi</h3>
                <p class="management-description">Randevuları görüntüle ve yönet</p>
            </a>
        </div>
    </div>

    <div class="recent-activity-grid">
        <div class="recent-activity-card recent-users-card">
            <div class="activity-card-header">
                <div class="activity-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">👥</div>
                <h2 class="activity-card-title">Son Kayıt Olan Kullanıcılar</h2>
            </div>
            <div class="activity-table-container">
                <table class="activity-table">
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
                            <td>
                                <div class="user-info-cell">
                                    <div class="user-avatar-mini"><?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?></div>
                                    <span><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></span>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><span class="date-badge"><?php echo date('d.m.Y', strtotime($user['created_at'])); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="recent-activity-card recent-plans-card">
            <div class="activity-card-header">
                <div class="activity-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">📋</div>
                <h2 class="activity-card-title">Son Diyet Planları</h2>
            </div>
            <div class="activity-table-container">
                <table class="activity-table">
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
                            <td>
                                <div class="plan-name-cell">
                                    <span class="plan-icon">🥗</span>
                                    <span><?php echo htmlspecialchars($plan['plan_name']); ?></span>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($plan['first_name'] . ' ' . $plan['last_name']); ?></td>
                            <td><span class="date-badge"><?php echo date('d.m.Y', strtotime($plan['created_at'])); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="recent-activity-card recent-bookings-card">
            <div class="activity-card-header">
                <div class="activity-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">📅</div>
                <h2 class="activity-card-title">Son Randevular</h2>
            </div>
            <div class="activity-table-container">
                <table class="activity-table">
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
                            <td>
                                <div class="consultant-name-cell">
                                    <span class="consultant-icon">👨‍⚕️</span>
                                    <span><?php echo htmlspecialchars($booking['consultant_first_name'] . ' ' . $booking['consultant_last_name']); ?></span>
                                </div>
                            </td>
                            <td><span class="date-badge"><?php echo date('d.m.Y H:i', strtotime($booking['booking_date'])); ?></span></td>
                            <td>
                                <?php 
                                $status_text = [
                                    'pending' => 'Beklemede',
                                    'confirmed' => 'Onaylandı',
                                    'completed' => 'Tamamlandı',
                                    'cancelled' => 'İptal Edildi'
                                ];
                                $status_class = [
                                    'pending' => 'status-pending',
                                    'confirmed' => 'status-confirmed',
                                    'completed' => 'status-completed',
                                    'cancelled' => 'status-cancelled'
                                ];
                                $status = $booking['status'];
                                ?>
                                <span class="status-badge <?php echo $status_class[$status] ?? ''; ?>">
                                    <?php echo $status_text[$status] ?? $status; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.admin-management-section {
    margin-top: 3rem;
}

.admin-management-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.admin-management-card {
    background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    border: 2px solid #444;
    border-radius: 16px;
    padding: 2rem;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.admin-management-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 215, 0, 0.1) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.admin-management-card:hover {
    transform: translateY(-8px);
    border-color: #ffd700;
    box-shadow: 0 12px 40px rgba(255, 215, 0, 0.2);
}

.admin-management-card:hover::before {
    opacity: 1;
}

.management-icon {
    font-size: 3.5rem;
    margin-bottom: 1rem;
    position: relative;
    z-index: 1;
    filter: drop-shadow(0 0 10px rgba(255, 215, 0, 0.3));
    transition: transform 0.3s ease;
}

.admin-management-card:hover .management-icon {
    transform: scale(1.1);
}

.management-title {
    color: #fff;
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    position: relative;
    z-index: 1;
}

.management-description {
    color: #aaa;
    font-size: 0.95rem;
    line-height: 1.6;
    margin: 0;
    position: relative;
    z-index: 1;
}

@media (max-width: 768px) {
    .admin-management-grid {
        grid-template-columns: 1fr;
    }
}

.recent-activity-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.recent-activity-card {
    background: linear-gradient(135deg, #1a1a1a 0%, #2a1a1a 100%);
    border-radius: 20px;
    padding: 0;
    border: 1px solid #333;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
    transition: all 0.3s ease;
    position: relative;
}

.recent-activity-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, transparent, currentColor, transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.recent-users-card::before {
    color: #667eea;
}

.recent-plans-card::before {
    color: #f5576c;
}

.recent-bookings-card::before {
    color: #4facfe;
}

.recent-activity-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 48px rgba(0, 0, 0, 0.6);
    border-color: #555;
}

.recent-activity-card:hover::before {
    opacity: 1;
}

.activity-card-header {
    padding: 2rem;
    background: rgba(255, 255, 255, 0.02);
    border-bottom: 1px solid #333;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.activity-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}

.activity-card-title {
    color: #fff;
    font-size: 1.4rem;
    font-weight: 700;
    margin: 0;
    flex: 1;
}

.activity-table-container {
    padding: 1.5rem;
    max-height: 500px;
    overflow-y: auto;
}

.activity-table-container::-webkit-scrollbar {
    width: 8px;
}

.activity-table-container::-webkit-scrollbar-track {
    background: #1a1a1a;
    border-radius: 4px;
}

.activity-table-container::-webkit-scrollbar-thumb {
    background: #444;
    border-radius: 4px;
}

.activity-table-container::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.activity-table {
    width: 100%;
    border-collapse: collapse;
}

.activity-table thead {
    position: sticky;
    top: 0;
    z-index: 10;
}

.activity-table thead tr {
    background: rgba(255, 255, 255, 0.03);
}

.activity-table th {
    color: #ffd700;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 1rem 0.75rem;
    text-align: left;
    border-bottom: 2px solid rgba(255, 215, 0, 0.2);
}

.activity-table tbody tr {
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    transition: all 0.2s ease;
}

.activity-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.05);
    transform: scale(1.01);
}

.activity-table td {
    color: #ccc;
    padding: 1rem 0.75rem;
    font-size: 0.95rem;
}

.user-info-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.user-avatar-mini {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.85rem;
    color: #fff;
    flex-shrink: 0;
}

.plan-name-cell,
.consultant-name-cell {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.plan-icon,
.consultant-icon {
    font-size: 1.2rem;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
}

.date-badge {
    display: inline-block;
    padding: 0.4rem 0.8rem;
    background: rgba(255, 255, 255, 0.08);
    border-radius: 8px;
    font-size: 0.85rem;
    color: #aaa;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.status-badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-pending {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.2) 0%, rgba(255, 152, 0, 0.2) 100%);
    color: #ffc107;
    border: 1px solid rgba(255, 193, 7, 0.3);
}

.status-confirmed {
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.2) 0%, rgba(56, 142, 60, 0.2) 100%);
    color: #4ade80;
    border: 1px solid rgba(76, 175, 80, 0.3);
}

.status-completed {
    background: linear-gradient(135deg, rgba(33, 150, 243, 0.2) 0%, rgba(25, 118, 210, 0.2) 100%);
    color: #4facfe;
    border: 1px solid rgba(33, 150, 243, 0.3);
}

.status-cancelled {
    background: linear-gradient(135deg, rgba(244, 67, 54, 0.2) 0%, rgba(211, 47, 47, 0.2) 100%);
    color: #f5576c;
    border: 1px solid rgba(244, 67, 54, 0.3);
}

@media (max-width: 768px) {
    .recent-activity-grid {
        grid-template-columns: 1fr;
    }
    
    .activity-table {
        font-size: 0.85rem;
    }
    
    .activity-table th,
    .activity-table td {
        padding: 0.75rem 0.5rem;
    }
}
</style>

<?php
$extra_js = ['assets/js/admin-dashboard.js'];
require_once '../includes/footer.php';
?>

