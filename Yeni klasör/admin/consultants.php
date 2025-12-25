<?php
$page_title = 'Danışman Yönetimi';
require_once '../includes/header.php';
requireAdmin();

// Danışmanları çek
$stmt = $db->query("SELECT c.*, u.first_name, u.last_name, u.email, u.status as user_status 
                    FROM consultants c 
                    JOIN users u ON c.user_id = u.id 
                    ORDER BY c.created_at DESC");
$consultants = $stmt->fetchAll();
?>

<div class="dashboard">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 class="section-title">Danışman Yönetimi</h1>
        <a href="<?php echo SITE_URL; ?>admin/dashboard.php" class="btn btn-secondary">← Dashboard'a Dön</a>
    </div>

    <div class="admin-consultants-card">
        <div class="consultants-header">
            <div class="consultants-header-left">
                <div class="consultants-icon">👨‍⚕️</div>
                <div>
                    <h2 class="consultants-title">Danışmanlar</h2>
                    <p class="consultants-subtitle"><?php echo count($consultants); ?> toplam danışman</p>
                </div>
            </div>
        </div>
        
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Danışman</th>
                        <th>Uzmanlık</th>
                        <th>Tecrübe</th>
                        <th>Fiyat</th>
                        <th>Rating</th>
                        <th>Durum</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($consultants)): ?>
                    <tr>
                        <td colspan="7" class="empty-state">
                            <div class="empty-icon">👨‍⚕️</div>
                            <div class="empty-text">Danışman bulunamadı</div>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($consultants as $consultant): ?>
                    <tr class="consultant-row">
                        <td class="consultant-id">#<?php echo $consultant['id']; ?></td>
                        <td class="consultant-info">
                            <div class="consultant-avatar">
                                <?php echo strtoupper(substr($consultant['first_name'], 0, 1) . substr($consultant['last_name'], 0, 1)); ?>
                            </div>
                            <div class="consultant-details">
                                <div class="consultant-name"><?php echo htmlspecialchars($consultant['first_name'] . ' ' . $consultant['last_name']); ?></div>
                                <div class="consultant-email"><?php echo htmlspecialchars($consultant['email']); ?></div>
                                <?php if ($consultant['bio']): ?>
                                <div class="consultant-bio"><?php echo htmlspecialchars(substr($consultant['bio'], 0, 60)) . '...'; ?></div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="consultant-specialization">
                            <div class="specialization-badge">
                                <span class="specialization-icon">🎯</span>
                                <span class="specialization-text"><?php echo htmlspecialchars($consultant['specialization'] ?? '-'); ?></span>
                            </div>
                        </td>
                        <td class="consultant-experience">
                            <div class="experience-badge">
                                <span class="experience-icon">⭐</span>
                                <span class="experience-value"><?php echo $consultant['experience_years'] ?? 0; ?></span>
                                <span class="experience-unit">yıl</span>
                            </div>
                        </td>
                        <td class="consultant-price">
                            <div class="price-badge">
                                <span class="price-value"><?php echo number_format($consultant['price_per_session'] ?? 0, 0, ',', '.'); ?></span>
                                <span class="price-currency">₺</span>
                                <div class="price-label">/ seans</div>
                            </div>
                        </td>
                        <td class="consultant-rating">
                            <div class="rating-badge">
                                <span class="rating-stars">
                                    <?php 
                                    $rating = floatval($consultant['rating'] ?? 0);
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= floor($rating)) {
                                            echo '<span class="star filled">⭐</span>';
                                        } elseif ($i - 0.5 <= $rating) {
                                            echo '<span class="star half">⭐</span>';
                                        } else {
                                            echo '<span class="star">⭐</span>';
                                        }
                                    }
                                    ?>
                                </span>
                                <span class="rating-value"><?php echo number_format($rating, 1); ?></span>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo $consultant['status'] ?? 'unavailable'; ?>">
                                <?php 
                                $status_texts = [
                                    'available' => 'Müsait',
                                    'busy' => 'Meşgul',
                                    'unavailable' => 'Müsait Değil'
                                ];
                                echo $status_texts[$consultant['status']] ?? $consultant['status'];
                                ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
/* Danışmanlar Kartı */
.admin-consultants-card {
    background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    border: 2px solid rgba(255, 215, 0, 0.2);
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.consultants-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid rgba(255, 215, 0, 0.2);
}

.consultants-header-left {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.consultants-icon {
    font-size: 3rem;
    filter: drop-shadow(0 0 15px rgba(255, 215, 0, 0.5));
}

.consultants-title {
    background: linear-gradient(135deg, #ffd700 0%, #ffa500 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-size: 2rem;
    font-weight: 900;
    margin: 0 0 0.25rem 0;
}

.consultants-subtitle {
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

.consultant-id {
    color: #aaa;
    font-weight: 600;
    font-size: 0.9rem;
}

.consultant-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    min-width: 250px;
}

.consultant-avatar {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ffd700 0%, #ffa500 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #000;
    font-weight: 700;
    font-size: 1.2rem;
    box-shadow: 0 4px 20px rgba(255, 215, 0, 0.4);
    flex-shrink: 0;
}

.consultant-name {
    font-weight: 700;
    color: #fff;
    margin-bottom: 0.25rem;
    font-size: 1rem;
}

.consultant-email {
    color: #aaa;
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
}

.consultant-bio {
    color: #888;
    font-size: 0.8rem;
    font-style: italic;
}

.consultant-specialization {
    min-width: 180px;
}

.specialization-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: rgba(255, 215, 0, 0.1);
    border: 1px solid rgba(255, 215, 0, 0.3);
    border-radius: 10px;
    width: fit-content;
}

.specialization-icon {
    font-size: 1rem;
}

.specialization-text {
    font-weight: 600;
    color: #ffd700;
    font-size: 0.9rem;
}

.consultant-experience {
    min-width: 120px;
}

.experience-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: rgba(100, 149, 237, 0.1);
    border: 1px solid rgba(100, 149, 237, 0.3);
    border-radius: 10px;
    width: fit-content;
}

.experience-icon {
    font-size: 1rem;
}

.experience-value {
    font-weight: 700;
    color: #6495ed;
    font-size: 1.1rem;
}

.experience-unit {
    color: #aaa;
    font-size: 0.85rem;
}

.consultant-price {
    min-width: 140px;
}

.price-badge {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    padding: 0.5rem 0.75rem;
    background: rgba(74, 222, 128, 0.1);
    border: 1px solid rgba(74, 222, 128, 0.3);
    border-radius: 10px;
    width: fit-content;
}

.price-value {
    font-weight: 700;
    color: #4ade80;
    font-size: 1.2rem;
    line-height: 1;
}

.price-currency {
    color: #4ade80;
    font-size: 1rem;
    margin-left: 0.25rem;
}

.price-label {
    color: #aaa;
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

.consultant-rating {
    min-width: 140px;
}

.rating-badge {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
}

.rating-stars {
    display: flex;
    gap: 0.2rem;
}

.star {
    font-size: 1.1rem;
    opacity: 0.3;
    filter: grayscale(100%);
}

.star.filled {
    opacity: 1;
    filter: none;
    color: #ffd700;
}

.star.half {
    opacity: 0.6;
    filter: grayscale(50%);
}

.rating-value {
    font-weight: 700;
    color: #ffd700;
    font-size: 1.1rem;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 700;
    display: inline-block;
}

.status-available {
    background: rgba(74, 222, 128, 0.2);
    color: #4ade80;
    border: 2px solid #4ade80;
}

.status-busy {
    background: rgba(255, 165, 0, 0.2);
    color: #ffa500;
    border: 2px solid #ffa500;
}

.status-unavailable {
    background: rgba(255, 107, 107, 0.2);
    color: #ff6b6b;
    border: 2px solid #ff6b6b;
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
    
    .consultant-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?>

