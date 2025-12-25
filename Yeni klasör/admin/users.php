<?php
$page_title = 'Kullanıcı Yönetimi';
require_once '../includes/header.php';
requireAdmin();

// Sayfalama
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Arama
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Kullanıcıları çek
$where = "u.role = 'user'";
$params = [];

if (!empty($search)) {
    $where .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ? OR u.username LIKE ?)";
    $search_param = "%{$search}%";
    $params = [$search_param, $search_param, $search_param, $search_param];
}

// Toplam sayı
$count_sql = "SELECT COUNT(*) FROM users u WHERE {$where}";
$count_stmt = $db->prepare($count_sql);
$count_stmt->execute($params);
$total_users = $count_stmt->fetchColumn();
$total_pages = ceil($total_users / $per_page);

// Kullanıcıları çek
$sql = "SELECT u.*, 
        (SELECT COUNT(*) FROM user_memberships WHERE user_id = u.id AND status = 'active') as active_memberships,
        (SELECT COUNT(*) FROM diet_plans WHERE user_id = u.id) as diet_plans_count
        FROM users u 
        WHERE {$where} 
        ORDER BY u.created_at DESC 
        LIMIT {$per_page} OFFSET {$offset}";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();
?>

<div class="dashboard">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 class="section-title">Kullanıcı Yönetimi</h1>
        <a href="<?php echo SITE_URL; ?>admin/dashboard.php" class="btn btn-secondary">← Dashboard'a Dön</a>
    </div>

    <!-- Arama ve Filtreler -->
    <div class="admin-search-card">
        <div class="search-header">
            <div class="search-icon">🔍</div>
            <h3 class="search-title">Kullanıcı Ara</h3>
        </div>
        <form method="GET" class="search-form">
            <div class="search-input-wrapper">
                <input type="text" id="search" name="search" class="admin-search-input" 
                       placeholder="Ad, soyad, e-posta veya kullanıcı adı ile ara..." 
                       value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="search-btn">
                    <span>Ara</span>
                    <span class="btn-arrow">→</span>
                </button>
                <?php if (!empty($search)): ?>
                <a href="<?php echo SITE_URL; ?>admin/users.php" class="clear-btn">Temizle</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Kullanıcı Listesi -->
    <div class="admin-users-card">
        <div class="users-header">
            <div class="users-header-left">
                <div class="users-icon">👥</div>
                <div>
                    <h2 class="users-title">Kullanıcılar</h2>
                    <p class="users-subtitle"><?php echo $total_users; ?> toplam kullanıcı</p>
                </div>
            </div>
        </div>
        
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kullanıcı</th>
                        <th>İletişim</th>
                        <th>Üyelik</th>
                        <th>Diyet Planı</th>
                        <th>Durum</th>
                        <th>Kayıt</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="8" class="empty-state">
                            <div class="empty-icon">🔍</div>
                            <div class="empty-text">Kullanıcı bulunamadı</div>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($users as $user): ?>
                    <tr class="user-row">
                        <td class="user-id">#<?php echo $user['id']; ?></td>
                        <td class="user-info">
                            <div class="user-avatar">
                                <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                            </div>
                            <div class="user-details">
                                <div class="user-name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></div>
                                <div class="user-username">@<?php echo htmlspecialchars($user['username']); ?></div>
                            </div>
                        </td>
                        <td class="user-contact">
                            <div class="contact-item">
                                <span class="contact-icon">✉️</span>
                                <span class="contact-text"><?php echo htmlspecialchars($user['email']); ?></span>
                            </div>
                            <?php if ($user['phone']): ?>
                            <div class="contact-item">
                                <span class="contact-icon">📞</span>
                                <span class="contact-text"><?php echo htmlspecialchars($user['phone']); ?></span>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td class="user-membership">
                            <?php if ($user['active_memberships'] > 0): ?>
                                <div class="membership-badge active">
                                    <span class="badge-icon">👑</span>
                                    <span class="badge-text">Aktif (<?php echo $user['active_memberships']; ?>)</span>
                                </div>
                            <?php else: ?>
                                <div class="membership-badge inactive">
                                    <span class="badge-text">Yok</span>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="user-diet">
                            <div class="diet-count">
                                <span class="diet-icon">📋</span>
                                <span><?php echo $user['diet_plans_count']; ?></span>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo $user['status']; ?>">
                                <?php echo $user['status'] === 'active' ? 'Aktif' : 'Pasif'; ?>
                            </span>
                        </td>
                        <td class="user-date"><?php echo date('d.m.Y', strtotime($user['created_at'])); ?></td>
                        <td class="user-actions">
                            <div class="actions-group">
                                <button class="action-btn edit-btn" onclick="editUser(<?php echo $user['id']; ?>)" title="Düzenle">
                                    <span class="action-icon">✏️</span>
                                </button>
                                <button class="action-btn toggle-btn" 
                                        onclick="toggleUserStatus(<?php echo $user['id']; ?>, '<?php echo $user['status']; ?>')" 
                                        title="<?php echo $user['status'] === 'active' ? 'Pasif Yap' : 'Aktif Yap'; ?>">
                                    <span class="action-icon"><?php echo $user['status'] === 'active' ? '⏸️' : '▶️'; ?></span>
                                </button>
                                <button class="action-btn delete-btn" onclick="deleteUser(<?php echo $user['id']; ?>)" title="Sil">
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
            <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="btn btn-secondary">← Önceki</a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
            <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
               class="btn <?php echo $i === $page ? 'btn-primary' : 'btn-secondary'; ?>">
                <?php echo $i; ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="btn btn-secondary">Sonraki →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<div id="alert-container"></div>

<style>
/* Arama Kartı */
.admin-search-card {
    background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    border: 2px solid rgba(255, 215, 0, 0.2);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.search-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.search-icon {
    font-size: 2rem;
    filter: drop-shadow(0 0 10px rgba(255, 215, 0, 0.5));
}

.search-title {
    background: linear-gradient(135deg, #ffd700 0%, #ffa500 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
}

.search-form {
    width: 100%;
}

.search-input-wrapper {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.admin-search-input {
    flex: 1;
    background: rgba(0, 0, 0, 0.4);
    border: 2px solid rgba(255, 215, 0, 0.3);
    border-radius: 12px;
    padding: 1rem 1.5rem;
    color: #fff;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.admin-search-input:focus {
    outline: none;
    border-color: #ffd700;
    box-shadow: 0 0 20px rgba(255, 215, 0, 0.2);
}

.search-btn {
    background: linear-gradient(135deg, #ffd700 0%, #ffa500 100%);
    border: none;
    border-radius: 12px;
    padding: 1rem 2rem;
    color: #000;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
}

.search-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 215, 0, 0.4);
}

.clear-btn {
    padding: 1rem 1.5rem;
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    color: #fff;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.clear-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.4);
}

/* Kullanıcılar Kartı */
.admin-users-card {
    background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    border: 2px solid rgba(255, 215, 0, 0.2);
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.users-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid rgba(255, 215, 0, 0.2);
}

.users-header-left {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.users-icon {
    font-size: 3rem;
    filter: drop-shadow(0 0 15px rgba(255, 215, 0, 0.5));
}

.users-title {
    background: linear-gradient(135deg, #ffd700 0%, #ffa500 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-size: 2rem;
    font-weight: 900;
    margin: 0 0 0.25rem 0;
}

.users-subtitle {
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

.user-id {
    color: #aaa;
    font-weight: 600;
    font-size: 0.9rem;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ffd700 0%, #ffa500 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #000;
    font-weight: 700;
    font-size: 1.1rem;
    box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
}

.user-name {
    font-weight: 700;
    color: #fff;
    margin-bottom: 0.25rem;
}

.user-username {
    color: #aaa;
    font-size: 0.85rem;
}

.user-contact {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.contact-icon {
    font-size: 1rem;
}

.contact-text {
    color: #ccc;
    font-size: 0.9rem;
}

.membership-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.85rem;
}

.membership-badge.active {
    background: rgba(74, 222, 128, 0.2);
    color: #4ade80;
    border: 1px solid #4ade80;
}

.membership-badge.inactive {
    background: rgba(255, 255, 255, 0.1);
    color: #aaa;
}

.diet-count {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #fff;
    font-weight: 600;
}

.diet-icon {
    font-size: 1.2rem;
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

.status-inactive {
    background: rgba(255, 107, 107, 0.2);
    color: #ff6b6b;
    border: 2px solid #ff6b6b;
}

.user-date {
    color: #aaa;
    font-size: 0.9rem;
}

.user-actions {
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
}

.action-icon {
    font-size: 1.2rem;
    display: block;
}

.action-btn:hover {
    transform: translateY(-3px) scale(1.1);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
}

.edit-btn:hover {
    border-color: #4ade80;
    background: rgba(74, 222, 128, 0.2);
}

.toggle-btn:hover {
    border-color: #ffd700;
    background: rgba(255, 215, 0, 0.2);
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
    
    .user-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}
</style>

<script>
function toggleUserStatus(userId, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    const confirmMsg = newStatus === 'active' ? 'Kullanıcıyı aktif yapmak istediğinizden emin misiniz?' : 'Kullanıcıyı pasif yapmak istediğinizden emin misiniz?';
    
    if (!confirm(confirmMsg)) return;
    
    const formData = new FormData();
    formData.append('user_id', userId);
    formData.append('status', newStatus);
    
    fetch('<?php echo SITE_URL; ?>api/admin-update-user-status.php', {
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

function deleteUser(userId) {
    if (!confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!')) return;
    
    const formData = new FormData();
    formData.append('user_id', userId);
    
    fetch('<?php echo SITE_URL; ?>api/admin-delete-user.php', {
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

function editUser(userId) {
    window.location.href = '<?php echo SITE_URL; ?>admin/user-edit.php?id=' + userId;
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

