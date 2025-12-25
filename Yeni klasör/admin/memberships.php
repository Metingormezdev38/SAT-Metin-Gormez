<?php
$page_title = 'Üyelik Yönetimi';
require_once '../includes/header.php';
requireAdmin();

// Sayfalama
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Filtreleme
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Üyelikleri çek
$where = "1=1";
$params = [];

if ($status_filter !== 'all') {
    $where .= " AND um.status = ?";
    $params[] = $status_filter;
}

// Toplam sayı
$count_sql = "SELECT COUNT(*) FROM user_memberships um WHERE {$where}";
$count_stmt = $db->prepare($count_sql);
$count_stmt->execute($params);
$total_memberships = $count_stmt->fetchColumn();
$total_pages = ceil($total_memberships / $per_page);

// Üyelikleri çek
$sql = "SELECT um.*, u.first_name, u.last_name, u.email 
        FROM user_memberships um 
        JOIN users u ON um.user_id = u.id 
        WHERE {$where} 
        ORDER BY um.created_at DESC 
        LIMIT {$per_page} OFFSET {$offset}";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$memberships = $stmt->fetchAll();

$period_texts = [
    'monthly' => 'Aylık',
    'quarterly' => '3 Aylık',
    'yearly' => '1 Yıllık'
];
?>

<div class="dashboard">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 class="section-title">Üyelik Yönetimi</h1>
        <a href="<?php echo SITE_URL; ?>admin/dashboard.php" class="btn btn-secondary">← Dashboard'a Dön</a>
    </div>

    <!-- Filtreler -->
    <div class="admin-filter-card">
        <div class="filter-header">
            <div class="filter-icon">🔍</div>
            <h3 class="filter-title">Filtrele</h3>
        </div>
        <form method="GET" class="filter-form">
            <div class="filter-input-wrapper">
                <label for="status" class="filter-label">Durum</label>
                <select id="status" name="status" class="admin-filter-select">
                    <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>Tümü</option>
                    <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Aktif</option>
                    <option value="expired" <?php echo $status_filter === 'expired' ? 'selected' : ''; ?>>Süresi Dolmuş</option>
                    <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>İptal Edilmiş</option>
                </select>
                <button type="submit" class="filter-btn">
                    <span>Filtrele</span>
                    <span class="btn-arrow">→</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Üyelik Listesi -->
    <div class="admin-memberships-card">
        <div class="memberships-header">
            <div class="memberships-header-left">
                <div class="memberships-icon">💳</div>
                <div>
                    <h2 class="memberships-title">Üyelikler</h2>
                    <p class="memberships-subtitle"><?php echo $total_memberships; ?> toplam üyelik</p>
                </div>
            </div>
        </div>
        
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kullanıcı</th>
                        <th>Üyelik</th>
                        <th>Fiyat</th>
                        <th>Tarihler</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($memberships)): ?>
                    <tr>
                        <td colspan="7" class="empty-state">
                            <div class="empty-icon">💳</div>
                            <div class="empty-text">Üyelik bulunamadı</div>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($memberships as $membership): 
                        $days_left = ceil((strtotime($membership['end_date']) - time()) / (60 * 60 * 24));
                        $membership_icons = ['Temel' => '💪', 'Premium' => '⭐', 'VIP' => '👑'];
                        $membership_colors = ['Temel' => '#4ade80', 'Premium' => '#ffd700', 'VIP' => '#ff6b6b'];
                        $icon = $membership_icons[$membership['membership_type']] ?? '💳';
                        $color = $membership_colors[$membership['membership_type']] ?? '#ffd700';
                    ?>
                    <tr class="membership-row">
                        <td class="membership-id">#<?php echo $membership['id']; ?></td>
                        <td class="membership-user">
                            <div class="user-avatar-small">
                                <?php echo strtoupper(substr($membership['first_name'], 0, 1) . substr($membership['last_name'], 0, 1)); ?>
                            </div>
                            <div class="user-info-small">
                                <div class="user-name-small"><?php echo htmlspecialchars($membership['first_name'] . ' ' . $membership['last_name']); ?></div>
                                <div class="user-email-small"><?php echo htmlspecialchars($membership['email']); ?></div>
                            </div>
                        </td>
                        <td class="membership-type">
                            <div class="membership-type-badge" style="--membership-color: <?php echo $color; ?>">
                                <span class="membership-type-icon"><?php echo $icon; ?></span>
                                <div>
                                    <div class="membership-type-name"><?php echo htmlspecialchars($membership['membership_type']); ?></div>
                                    <div class="membership-type-period"><?php echo $period_texts[$membership['membership_period']] ?? $membership['membership_period']; ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="membership-price">
                            <div class="price-amount"><?php echo number_format($membership['price'], 2, ',', '.'); ?> ₺</div>
                        </td>
                        <td class="membership-dates">
                            <div class="date-item">
                                <span class="date-label">Başlangıç:</span>
                                <span class="date-value"><?php echo date('d.m.Y', strtotime($membership['start_date'])); ?></span>
                            </div>
                            <div class="date-item">
                                <span class="date-label">Bitiş:</span>
                                <span class="date-value end-date" style="color: <?php echo $membership['status'] === 'active' ? ($days_left <= 7 ? '#ff6b6b' : ($days_left <= 30 ? '#ffa500' : '#4ade80')) : '#aaa'; ?>;">
                                    <?php echo date('d.m.Y', strtotime($membership['end_date'])); ?>
                                    <?php if ($membership['status'] === 'active'): ?>
                                        <span class="days-left">(<?php echo $days_left > 0 ? $days_left . ' gün kaldı' : 'Süresi dolmuş'; ?>)</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo $membership['status']; ?>">
                                <?php 
                                $status_texts = [
                                    'active' => 'Aktif',
                                    'expired' => 'Süresi Dolmuş',
                                    'cancelled' => 'İptal Edilmiş'
                                ];
                                echo $status_texts[$membership['status']] ?? $membership['status'];
                                ?>
                            </span>
                        </td>
                        <td class="membership-actions">
                            <?php if ($membership['status'] === 'active'): ?>
                            <button class="action-btn cancel-btn" onclick="cancelMembership(<?php echo $membership['id']; ?>)" title="İptal Et">
                                <span class="action-icon">⏸️</span>
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Sayfalama -->
        <?php if ($total_pages > 1): ?>
        <div style="display: flex; justify-content: center; gap: 0.5rem; margin-top: 2rem;">
            <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>&status=<?php echo $status_filter; ?>" class="btn btn-secondary">← Önceki</a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
            <a href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>" 
               class="btn <?php echo $i === $page ? 'btn-primary' : 'btn-secondary'; ?>">
                <?php echo $i; ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>&status=<?php echo $status_filter; ?>" class="btn btn-secondary">Sonraki →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<div id="alert-container"></div>

<style>
/* Filtre Kartı */
.admin-filter-card {
    background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    border: 2px solid rgba(255, 215, 0, 0.2);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.filter-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.filter-icon {
    font-size: 2rem;
    filter: drop-shadow(0 0 10px rgba(255, 215, 0, 0.5));
}

.filter-title {
    background: linear-gradient(135deg, #ffd700 0%, #ffa500 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
}

.filter-form {
    width: 100%;
}

.filter-input-wrapper {
    display: flex;
    gap: 1rem;
    align-items: end;
}

.filter-label {
    display: block;
    color: #fff;
    margin-bottom: 0.5rem;
    font-weight: 600;
    font-size: 0.95rem;
}

.admin-filter-select {
    background: rgba(0, 0, 0, 0.4);
    border: 2px solid rgba(255, 215, 0, 0.3);
    border-radius: 12px;
    padding: 0.875rem 1rem;
    color: #fff;
    font-size: 1rem;
    transition: all 0.3s ease;
    min-width: 200px;
}

.admin-filter-select:focus {
    outline: none;
    border-color: #ffd700;
    box-shadow: 0 0 20px rgba(255, 215, 0, 0.2);
}

.filter-btn {
    background: linear-gradient(135deg, #ffd700 0%, #ffa500 100%);
    border: none;
    border-radius: 12px;
    padding: 0.875rem 2rem;
    color: #000;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
}

.filter-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 215, 0, 0.4);
}

/* Üyelikler Kartı */
.admin-memberships-card {
    background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    border: 2px solid rgba(255, 215, 0, 0.2);
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.memberships-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid rgba(255, 215, 0, 0.2);
}

.memberships-header-left {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.memberships-icon {
    font-size: 3rem;
    filter: drop-shadow(0 0 15px rgba(255, 215, 0, 0.5));
}

.memberships-title {
    background: linear-gradient(135deg, #ffd700 0%, #ffa500 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-size: 2rem;
    font-weight: 900;
    margin: 0 0 0.25rem 0;
}

.memberships-subtitle {
    color: #aaa;
    font-size: 0.95rem;
    margin: 0;
}

/* Tablo */
.admin-table-wrapper {
    overflow-x: auto;
}

.admin-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.admin-table thead th {
    background: rgba(255, 215, 0, 0.1);
    color: #ffd700;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 1px;
    padding: 1.25rem 1rem;
    text-align: left;
    border-bottom: 2px solid rgba(255, 215, 0, 0.3);
}

.admin-table tbody tr {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.admin-table tbody tr:hover {
    background: rgba(255, 215, 0, 0.05);
    transform: scale(1.01);
}

.admin-table tbody td {
    padding: 1.5rem 1rem;
    color: #fff;
}

.membership-id {
    color: #aaa;
    font-weight: 600;
    font-size: 0.9rem;
}

.membership-user {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-avatar-small {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ffd700 0%, #ffa500 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #000;
    font-weight: 700;
    font-size: 0.9rem;
    box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
}

.user-name-small {
    font-weight: 700;
    color: #fff;
    margin-bottom: 0.25rem;
}

.user-email-small {
    color: #aaa;
    font-size: 0.85rem;
}

.membership-type-badge {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    background: rgba(255, 255, 255, 0.05);
    border: 2px solid var(--membership-color, #ffd700);
    border-radius: 12px;
    max-width: 200px;
}

.membership-type-icon {
    font-size: 1.5rem;
}

.membership-type-name {
    font-weight: 700;
    color: var(--membership-color, #ffd700);
    font-size: 1rem;
}

.membership-type-period {
    color: #aaa;
    font-size: 0.8rem;
    margin-top: 0.25rem;
}

.membership-price {
    font-weight: 700;
    font-size: 1.1rem;
    color: #ffd700;
}

.membership-dates {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.date-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.date-label {
    color: #aaa;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.date-value {
    color: #fff;
    font-weight: 600;
    font-size: 0.9rem;
}

.days-left {
    font-size: 0.85rem;
    margin-left: 0.5rem;
    font-weight: 700;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 700;
    display: inline-block;
}

.status-active {
    background: rgba(74, 222, 128, 0.2);
    color: #4ade80;
    border: 2px solid #4ade80;
}

.status-expired {
    background: rgba(255, 107, 107, 0.2);
    color: #ff6b6b;
    border: 2px solid #ff6b6b;
}

.status-cancelled {
    background: rgba(255, 165, 0, 0.2);
    color: #ffa500;
    border: 2px solid #ffa500;
}

.membership-actions {
    width: 80px;
}

.action-btn {
    background: rgba(255, 255, 255, 0.05);
    border: 2px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    padding: 0.75rem;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.action-icon {
    font-size: 1.2rem;
    display: block;
}

.action-btn:hover {
    transform: translateY(-3px) scale(1.1);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
}

.cancel-btn:hover {
    border-color: #ffa500;
    background: rgba(255, 165, 0, 0.2);
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-text {
    color: #aaa;
    font-size: 1.1rem;
}

@media (max-width: 1200px) {
    .admin-table {
        font-size: 0.9rem;
    }
    
    .membership-user {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}
</style>

<script>
function cancelMembership(membershipId) {
    if (!confirm('Bu üyeliği iptal etmek istediğinizden emin misiniz?')) return;
    
    const formData = new FormData();
    formData.append('membership_id', membershipId);
    
    fetch('<?php echo SITE_URL; ?>api/admin-cancel-membership.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('alert-container', data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert('alert-container', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('alert-container', 'Bir hata oluştu', 'error');
    });
}

function showAlert(containerId, message, type) {
    const container = document.getElementById(containerId);
    const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
    container.innerHTML = `<div class="${alertClass}">${message}</div>`;
    setTimeout(() => {
        container.innerHTML = '';
    }, 5000);
}
</script>

<?php require_once '../includes/footer.php'; ?>

