<?php
$page_title = 'Diyet Listelerim';
require_once '../includes/header.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Kullanıcının diyet planlarını çek
$stmt = $db->prepare("SELECT dp.*, c.first_name as consultant_first_name, c.last_name as consultant_last_name 
                      FROM diet_plans dp 
                      LEFT JOIN users c ON dp.consultant_id = c.id 
                      WHERE dp.user_id = ? 
                      ORDER BY dp.created_at DESC");
$stmt->execute([$user_id]);
$diet_plans = $stmt->fetchAll();
?>

<div class="dashboard">
    <h1 class="section-title">Diyet Listelerim</h1>
    
    <div style="margin-bottom: 2rem;">
        <button class="btn btn-primary" onclick="showCreateDietPlanModal()">Yeni Diyet Planı Oluştur</button>
    </div>

    <?php if (empty($diet_plans)): ?>
        <div class="card">
            <p class="text-center" style="color: var(--gray-light);">Henüz diyet planınız bulunmamaktadır.</p>
        </div>
    <?php else: ?>
        <?php foreach ($diet_plans as $plan): ?>
        <div class="card">
            <h2 class="card-title"><?php echo htmlspecialchars($plan['plan_name']); ?></h2>
            <p style="color: var(--gray-light); margin-bottom: 1rem;"><?php echo htmlspecialchars($plan['description']); ?></p>
            <div style="display: flex; gap: 2rem; margin-bottom: 1rem; flex-wrap: wrap;">
                <div>
                    <strong style="color: var(--primary-yellow);">Günlük Kalori:</strong> 
                    <span><?php echo $plan['daily_calories'] ?? '-'; ?> kcal</span>
                </div>
                <div>
                    <strong style="color: var(--primary-yellow);">Durum:</strong> 
                    <span><?php 
                        $status_text = [
                            'active' => 'Aktif',
                            'completed' => 'Tamamlandı',
                            'cancelled' => 'İptal Edildi'
                        ];
                        echo $status_text[$plan['status']] ?? $plan['status'];
                    ?></span>
                </div>
                <?php if ($plan['consultant_first_name']): ?>
                <div>
                    <strong style="color: var(--primary-yellow);">Danışman:</strong> 
                    <span><?php echo htmlspecialchars($plan['consultant_first_name'] . ' ' . $plan['consultant_last_name']); ?></span>
                </div>
                <?php endif; ?>
            </div>
            <div style="margin-top: 1rem;">
                <a href="<?php echo SITE_URL; ?>user/diet-plan-detail.php?id=<?php echo $plan['id']; ?>" class="btn btn-primary">Detayları Gör</a>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Diyet Planı Oluşturma Modal -->
<div id="diet-plan-modal" class="hidden" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); z-index: 2000; display: flex; align-items: center; justify-content: center;">
    <div class="form-container" style="position: relative; width: 90%; max-width: 800px; max-height: 90vh; overflow-y: auto;">
        <button onclick="closeDietPlanModal()" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; color: var(--primary-yellow); font-size: 1.5rem; cursor: pointer;">&times;</button>
        <h2 class="form-title">Yeni Diyet Planı Oluştur</h2>
        <div id="diet-plan-alert-container"></div>
        <form id="diet-plan-form">
            <div class="form-group">
                <label for="plan_name" class="form-label">Plan Adı</label>
                <input type="text" id="plan_name" name="plan_name" class="form-input" required>
            </div>
            <div class="form-group">
                <label for="daily_calories" class="form-label">Günlük Kalori Hedefi</label>
                <input type="number" id="daily_calories" name="daily_calories" class="form-input" required>
            </div>
            <div class="form-group">
                <label for="description" class="form-label">Açıklama</label>
                <textarea id="description" name="description" class="form-input" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label for="start_date" class="form-label">Başlangıç Tarihi</label>
                <input type="date" id="start_date" name="start_date" class="form-input" required>
            </div>
            <div class="form-group">
                <label for="end_date" class="form-label">Bitiş Tarihi</label>
                <input type="date" id="end_date" name="end_date" class="form-input" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Oluştur</button>
        </form>
    </div>
</div>

<?php
$extra_js = ['assets/js/diet-plans.js'];
require_once '../includes/footer.php';
?>

