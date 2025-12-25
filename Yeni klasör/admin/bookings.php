<?php
$page_title = 'Randevu Yönetimi';
require_once '../includes/header.php';
requireAdmin();

// Randevuları çek
$stmt = $db->query("SELECT cb.*, 
                    u.first_name as user_first_name, u.last_name as user_last_name, u.email as user_email,
                    c.first_name as consultant_first_name, c.last_name as consultant_last_name
                    FROM consultant_bookings cb
                    JOIN users u ON cb.user_id = u.id
                    JOIN consultants con ON cb.consultant_id = con.id
                    JOIN users c ON con.user_id = c.id
                    ORDER BY cb.booking_date DESC");
$bookings = $stmt->fetchAll();

$status_texts = [
    'pending' => 'Beklemede',
    'confirmed' => 'Onaylandı',
    'completed' => 'Tamamlandı',
    'cancelled' => 'İptal Edildi'
];
?>

<div class="dashboard">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 class="section-title">Randevu Yönetimi</h1>
        <a href="<?php echo SITE_URL; ?>admin/dashboard.php" class="btn btn-secondary">← Dashboard'a Dön</a>
    </div>

    <div class="admin-bookings-card">
        <div class="bookings-header">
            <div class="bookings-header-left">
                <div class="bookings-icon">📅</div>
                <div>
                    <h2 class="bookings-title">Randevular</h2>
                    <p class="bookings-subtitle"><?php echo count($bookings); ?> toplam randevu</p>
                </div>
            </div>
        </div>
        
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kullanıcı</th>
                        <th>Danışman</th>
                        <th>Randevu Tarihi</th>
                        <th>Durum</th>
                        <th>Notlar</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                    <tr>
                        <td colspan="7" class="empty-state">
                            <div class="empty-icon">📅</div>
                            <div class="empty-text">Randevu bulunamadı</div>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($bookings as $booking): ?>
                    <tr class="booking-row">
                        <td class="booking-id">#<?php echo $booking['id']; ?></td>
                        <td class="booking-user">
                            <div class="user-avatar-small">
                                <?php echo strtoupper(substr($booking['user_first_name'], 0, 1) . substr($booking['user_last_name'], 0, 1)); ?>
                            </div>
                            <div class="user-info-small">
                                <div class="user-name-small"><?php echo htmlspecialchars($booking['user_first_name'] . ' ' . $booking['user_last_name']); ?></div>
                                <div class="user-email-small"><?php echo htmlspecialchars($booking['user_email']); ?></div>
                            </div>
                        </td>
                        <td class="booking-consultant">
                            <div class="consultant-badge">
                                <span class="consultant-icon">👨‍⚕️</span>
                                <span class="consultant-name-text"><?php echo htmlspecialchars($booking['consultant_first_name'] . ' ' . $booking['consultant_last_name']); ?></span>
                            </div>
                        </td>
                        <td class="booking-date">
                            <div class="date-time-badge">
                                <div class="date-icon">📆</div>
                                <div class="date-time-info">
                                    <div class="date-value"><?php echo date('d.m.Y', strtotime($booking['booking_date'])); ?></div>
                                    <div class="time-value"><?php echo date('H:i', strtotime($booking['booking_date'])); ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo $booking['status']; ?>">
                                <?php echo $status_texts[$booking['status']] ?? $booking['status']; ?>
                            </span>
                        </td>
                        <td class="booking-notes">
                            <div class="notes-text">
                                <?php echo htmlspecialchars($booking['notes'] ?? '-'); ?>
                            </div>
                        </td>
                        <td class="booking-actions">
                            <div class="actions-group">
                                <?php if ($booking['status'] === 'pending'): ?>
                                <button class="action-btn confirm-btn" onclick="updateBookingStatus(<?php echo $booking['id']; ?>, 'confirmed')" title="Onayla">
                                    <span class="action-icon">✓</span>
                                </button>
                                <?php endif; ?>
                                <?php if ($booking['status'] !== 'cancelled' && $booking['status'] !== 'completed'): ?>
                                <button class="action-btn cancel-btn" onclick="updateBookingStatus(<?php echo $booking['id']; ?>, 'cancelled')" title="İptal Et">
                                    <span class="action-icon">✕</span>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="alert-container"></div>

<style>
/* Randevular Kartı */
.admin-bookings-card {
    background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    border: 2px solid rgba(255, 215, 0, 0.2);
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.bookings-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid rgba(255, 215, 0, 0.2);
}

.bookings-header-left {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.bookings-icon {
    font-size: 3rem;
    filter: drop-shadow(0 0 15px rgba(255, 215, 0, 0.5));
}

.bookings-title {
    background: linear-gradient(135deg, #ffd700 0%, #ffa500 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-size: 2rem;
    font-weight: 900;
    margin: 0 0 0.25rem 0;
}

.bookings-subtitle {
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

.booking-id {
    color: #aaa;
    font-weight: 600;
    font-size: 0.9rem;
}

.booking-user {
    display: flex;
    align-items: center;
    gap: 1rem;
    min-width: 200px;
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

.booking-consultant {
    min-width: 180px;
}

.consultant-badge {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0.75rem;
    background: rgba(100, 149, 237, 0.1);
    border: 1px solid rgba(100, 149, 237, 0.3);
    border-radius: 10px;
    width: fit-content;
}

.consultant-icon {
    font-size: 1.2rem;
}

.consultant-name-text {
    font-weight: 600;
    color: #6495ed;
    font-size: 0.9rem;
}

.booking-date {
    min-width: 160px;
}

.date-time-badge {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0.75rem;
    background: rgba(255, 215, 0, 0.1);
    border: 1px solid rgba(255, 215, 0, 0.3);
    border-radius: 10px;
    width: fit-content;
}

.date-icon {
    font-size: 1.2rem;
}

.date-time-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.date-value {
    font-weight: 700;
    color: #ffd700;
    font-size: 0.95rem;
}

.time-value {
    color: #aaa;
    font-size: 0.85rem;
}

.booking-notes {
    max-width: 200px;
}

.notes-text {
    color: #ccc;
    font-size: 0.9rem;
    line-height: 1.4;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 700;
    display: inline-block;
}

.status-pending {
    background: rgba(255, 215, 0, 0.2);
    color: #ffd700;
    border: 2px solid #ffd700;
}

.status-confirmed {
    background: rgba(74, 222, 128, 0.2);
    color: #4ade80;
    border: 2px solid #4ade80;
}

.status-completed {
    background: rgba(100, 149, 237, 0.2);
    color: #6495ed;
    border: 2px solid #6495ed;
}

.status-cancelled {
    background: rgba(255, 107, 107, 0.2);
    color: #ff6b6b;
    border: 2px solid #ff6b6b;
}

.booking-actions {
    width: 120px;
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
    font-size: 1.3rem;
    font-weight: 700;
}

.action-icon {
    display: block;
    line-height: 1;
}

.action-btn:hover {
    transform: translateY(-3px) scale(1.1);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
}

.confirm-btn:hover {
    border-color: #4ade80;
    background: rgba(74, 222, 128, 0.2);
    color: #4ade80;
}

.cancel-btn:hover {
    border-color: #ff6b6b;
    background: rgba(255, 107, 107, 0.2);
    color: #ff6b6b;
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
    
    .booking-user,
    .booking-consultant {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}
</style>

<script>
function updateBookingStatus(bookingId, status) {
    const statusTexts = {
        'confirmed': 'onaylamak',
        'cancelled': 'iptal etmek'
    };
    
    if (!confirm(`Bu randevuyu ${statusTexts[status]} istediğinizden emin misiniz?`)) return;
    
    const formData = new FormData();
    formData.append('booking_id', bookingId);
    formData.append('status', status);
    
    fetch('<?php echo SITE_URL; ?>api/admin-update-booking-status.php', {
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

