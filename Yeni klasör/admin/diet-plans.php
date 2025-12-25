<?php
$page_title = 'Diyet Planları Yönetimi';
require_once '../includes/header.php';
requireAdmin();

// Sayfalama
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Filtreleme
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Diyet planlarını çek
$where = "1=1";
$params = [];

if ($status_filter !== 'all') {
    $where .= " AND dp.status = ?";
    $params[] = $status_filter;
}

// Toplam sayı
$count_sql = "SELECT COUNT(*) FROM diet_plans dp WHERE {$where}";
$count_stmt = $db->prepare($count_sql);
$count_stmt->execute($params);
$total_plans = $count_stmt->fetchColumn();
$total_pages = ceil($total_plans / $per_page);

// Diyet planlarını çek
$sql = "SELECT dp.*, u.first_name, u.last_name, u.email,
        (SELECT COUNT(*) FROM diet_meals WHERE diet_plan_id = dp.id) as meals_count
        FROM diet_plans dp 
        JOIN users u ON dp.user_id = u.id 
        WHERE {$where} 
        ORDER BY dp.created_at DESC 
        LIMIT {$per_page} OFFSET {$offset}";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$diet_plans = $stmt->fetchAll();

$status_texts = [
    'active' => 'Aktif',
    'completed' => 'Tamamlandı',
    'cancelled' => 'İptal Edildi'
];
?>

<div class="dashboard">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 class="section-title">Diyet Planları Yönetimi</h1>
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
                    <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Tamamlandı</option>
                    <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>İptal Edildi</option>
                </select>
                <button type="submit" class="filter-btn">
                    <span>Filtrele</span>
                    <span class="btn-arrow">→</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Diyet Planları Listesi -->
    <div class="admin-diet-plans-card">
        <div class="diet-plans-header">
            <div class="diet-plans-header-left">
                <div class="diet-plans-icon">📋</div>
                <div>
                    <h2 class="diet-plans-title">Diyet Planları</h2>
                    <p class="diet-plans-subtitle"><?php echo $total_plans; ?> toplam plan</p>
                </div>
            </div>
        </div>
        
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Plan</th>
                        <th>Kullanıcı</th>
                        <th>Kalori</th>
                        <th>Öğün</th>
                        <th>Tarihler</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($diet_plans)): ?>
                    <tr>
                        <td colspan="8" class="empty-state">
                            <div class="empty-icon">📋</div>
                            <div class="empty-text">Diyet planı bulunamadı</div>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($diet_plans as $plan): 
                        $plan_icons = [
                            'Kilo Verme' => '🔥',
                            'Kilo Alma' => '💪',
                            'Kilo Koruma' => '⚖️'
                        ];
                        $plan_name_lower = strtolower($plan['plan_name']);
                        $plan_icon = '📋';
                        foreach ($plan_icons as $key => $icon) {
                            if (stripos($plan_name_lower, strtolower($key)) !== false) {
                                $plan_icon = $icon;
                                break;
                            }
                        }
                    ?>
                    <tr class="diet-plan-row">
                        <td class="plan-id">#<?php echo $plan['id']; ?></td>
                        <td class="plan-info">
                            <div class="plan-icon-badge"><?php echo $plan_icon; ?></div>
                            <div class="plan-details">
                                <div class="plan-name"><?php echo htmlspecialchars($plan['plan_name']); ?></div>
                                <?php if ($plan['description']): ?>
                                <div class="plan-description"><?php echo htmlspecialchars(substr($plan['description'], 0, 50)) . '...'; ?></div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="plan-user">
                            <div class="user-avatar-small">
                                <?php echo strtoupper(substr($plan['first_name'], 0, 1) . substr($plan['last_name'], 0, 1)); ?>
                            </div>
                            <div class="user-info-small">
                                <div class="user-name-small"><?php echo htmlspecialchars($plan['first_name'] . ' ' . $plan['last_name']); ?></div>
                                <div class="user-email-small"><?php echo htmlspecialchars($plan['email']); ?></div>
                            </div>
                        </td>
                        <td class="plan-calories">
                            <div class="calories-badge">
                                <span class="calories-icon">🔥</span>
                                <span class="calories-value"><?php echo $plan['daily_calories'] ?? '-'; ?></span>
                                <span class="calories-unit">kcal</span>
                            </div>
                        </td>
                        <td class="plan-meals">
                            <div class="meals-count">
                                <span class="meals-icon">🍽️</span>
                                <span class="meals-value"><?php echo $plan['meals_count']; ?></span>
                            </div>
                        </td>
                        <td class="plan-dates">
                            <div class="date-item">
                                <span class="date-label">Başlangıç:</span>
                                <span class="date-value"><?php echo $plan['start_date'] ? date('d.m.Y', strtotime($plan['start_date'])) : '-'; ?></span>
                            </div>
                            <div class="date-item">
                                <span class="date-label">Bitiş:</span>
                                <span class="date-value"><?php echo $plan['end_date'] ? date('d.m.Y', strtotime($plan['end_date'])) : '-'; ?></span>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo $plan['status']; ?>">
                                <?php echo $status_texts[$plan['status']] ?? $plan['status']; ?>
                            </span>
                        </td>
                        <td class="plan-actions">
                            <div class="actions-group">
                                <a href="<?php echo SITE_URL; ?>user/diet-plan-detail.php?id=<?php echo $plan['id']; ?>" 
                                   class="action-btn view-btn" title="Görüntüle" target="_blank">
                                    <span class="action-icon">👁️</span>
                                </a>
                                <?php if ($plan['status'] === 'active'): ?>
                                <button class="action-btn cancel-btn" onclick="cancelDietPlan(<?php echo $plan['id']; ?>)" title="İptal Et">
                                    <span class="action-icon">⏸️</span>
                                </button>
                                <?php endif; ?>
                                <button class="action-btn delete-btn" onclick="deleteDietPlan(<?php echo $plan['id']; ?>)" title="Sil">
                                    <span class="action-icon">🗑️</span>
                                </button>
                            </div>
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

/* Diyet Planları Kartı */
.admin-diet-plans-card {
    background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    border: 2px solid rgba(255, 215, 0, 0.2);
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.diet-plans-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid rgba(255, 215, 0, 0.2);
}

.diet-plans-header-left {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.diet-plans-icon {
    font-size: 3rem;
    filter: drop-shadow(0 0 15px rgba(255, 215, 0, 0.5));
}

.diet-plans-title {
    background: linear-gradient(135deg, #ffd700 0%, #ffa500 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-size: 2rem;
    font-weight: 900;
    margin: 0 0 0.25rem 0;
}

.diet-plans-subtitle {
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

.plan-id {
    color: #aaa;
    font-weight: 600;
    font-size: 0.9rem;
}

.plan-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    min-width: 250px;
}

.plan-icon-badge {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, rgba(255, 215, 0, 0.2) 0%, rgba(255, 165, 0, 0.2) 100%);
    border: 2px solid rgba(255, 215, 0, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.plan-name {
    font-weight: 700;
    color: #fff;
    margin-bottom: 0.25rem;
    font-size: 1rem;
}

.plan-description {
    color: #aaa;
    font-size: 0.8rem;
}

.plan-user {
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
    flex-shrink: 0;
}

.user-name-small {
    font-weight: 700;
    color: #fff;
    margin-bottom: 0.25rem;
    font-size: 0.9rem;
}

.user-email-small {
    color: #aaa;
    font-size: 0.8rem;
}

.plan-calories {
    min-width: 120px;
}

.calories-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: rgba(255, 107, 107, 0.1);
    border: 1px solid rgba(255, 107, 107, 0.3);
    border-radius: 10px;
    width: fit-content;
}

.calories-icon {
    font-size: 1rem;
}

.calories-value {
    font-weight: 700;
    color: #ff6b6b;
    font-size: 1rem;
}

.calories-unit {
    color: #aaa;
    font-size: 0.8rem;
}

.plan-meals {
    min-width: 100px;
}

.meals-count {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: rgba(255, 215, 0, 0.1);
    border: 1px solid rgba(255, 215, 0, 0.3);
    border-radius: 10px;
    width: fit-content;
}

.meals-icon {
    font-size: 1rem;
}

.meals-value {
    font-weight: 700;
    color: #ffd700;
    font-size: 1rem;
}

.plan-dates {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    min-width: 150px;
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

.status-completed {
    background: rgba(255, 215, 0, 0.2);
    color: #ffd700;
    border: 2px solid #ffd700;
}

.status-cancelled {
    background: rgba(255, 107, 107, 0.2);
    color: #ff6b6b;
    border: 2px solid #ff6b6b;
}

.plan-actions {
    width: 140px;
}

.actions-group {
    display: flex;
    gap: 0.5rem;
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
    text-decoration: none;
}

.action-icon {
    font-size: 1.2rem;
    display: block;
}

.action-btn:hover {
    transform: translateY(-3px) scale(1.1);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
}

.view-btn:hover {
    border-color: #6495ed;
    background: rgba(100, 149, 237, 0.2);
}

.cancel-btn:hover {
    border-color: #ffa500;
    background: rgba(255, 165, 0, 0.2);
}

.delete-btn:hover {
    border-color: #ff6b6b;
    background: rgba(255, 107, 107, 0.2);
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
    
    .plan-info,
    .plan-user {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}
</style>

<script>
function cancelDietPlan(planId) {
    if (!confirm('Bu diyet planını iptal etmek istediğinizden emin misiniz?')) return;
    
    const formData = new FormData();
    formData.append('plan_id', planId);
    
    fetch('<?php echo SITE_URL; ?>api/admin-cancel-diet-plan.php', {
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

function deleteDietPlan(planId) {
    if (!confirm('Bu diyet planını silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!')) return;
    
    const formData = new FormData();
    formData.append('plan_id', planId);
    
    fetch('<?php echo SITE_URL; ?>api/admin-delete-diet-plan.php', {
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

