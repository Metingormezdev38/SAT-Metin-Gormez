<?php
$page_title = 'Diyet Planı Detayı';
require_once '../includes/header.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$plan_id = $_GET['id'] ?? 0;

// Diyet planı bilgilerini çek
$stmt = $db->prepare("SELECT dp.*, c.first_name as consultant_first_name, c.last_name as consultant_last_name 
                      FROM diet_plans dp 
                      LEFT JOIN users c ON dp.consultant_id = c.id 
                      WHERE dp.id = ? AND dp.user_id = ?");
$stmt->execute([$plan_id, $user_id]);
$plan = $stmt->fetch();

if (!$plan) {
    header('Location: ' . SITE_URL . 'user/diet-plans.php');
    exit;
}

// Plan öğünlerini çek
$stmt = $db->prepare("SELECT * FROM diet_meals WHERE diet_plan_id = ? ORDER BY day_of_week, meal_type");
$stmt->execute([$plan_id]);
$meals = $stmt->fetchAll();

// Günlere göre grupla
$meals_by_day = [];
foreach ($meals as $meal) {
    $day = $meal['day_of_week'];
    if (!isset($meals_by_day[$day])) {
        $meals_by_day[$day] = [];
    }
    $meals_by_day[$day][] = $meal;
}

$days = [1 => 'Pazartesi', 2 => 'Salı', 3 => 'Çarşamba', 4 => 'Perşembe', 5 => 'Cuma', 6 => 'Cumartesi', 7 => 'Pazar'];
$meal_types = ['breakfast' => 'Kahvaltı', 'lunch' => 'Öğle Yemeği', 'dinner' => 'Akşam Yemeği', 'snack' => 'Ara Öğün'];
?>

<div class="dashboard">
    <h1 class="section-title"><?php echo htmlspecialchars($plan['plan_name']); ?></h1>
    
    <div class="card">
        <h2 class="card-title">Plan Bilgileri</h2>
        <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
            <div>
                <strong style="color: var(--primary-yellow);">Günlük Kalori:</strong> 
                <span><?php echo $plan['daily_calories'] ?? '-'; ?> kcal</span>
            </div>
            <div>
                <strong style="color: var(--primary-yellow);">Başlangıç:</strong> 
                <span><?php echo date('d.m.Y', strtotime($plan['start_date'])); ?></span>
            </div>
            <div>
                <strong style="color: var(--primary-yellow);">Bitiş:</strong> 
                <span><?php echo date('d.m.Y', strtotime($plan['end_date'])); ?></span>
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
        </div>
        <?php if ($plan['description']): ?>
        <p style="margin-top: 1rem; color: var(--gray-light);"><?php echo htmlspecialchars($plan['description']); ?></p>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2 class="card-title">Haftalık Öğün Planı</h2>
        <?php if (empty($meals_by_day)): ?>
            <p class="text-center" style="color: var(--gray-light);">Henüz öğün eklenmemiş.</p>
            <button class="btn btn-primary" onclick="showAddMealModal()" style="margin-top: 1rem;">Öğün Ekle</button>
        <?php else: ?>
            <?php foreach ($meals_by_day as $day => $day_meals): ?>
            <div style="margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 1px solid var(--gray-dark);">
                <h3 style="color: var(--primary-yellow); margin-bottom: 1rem;"><?php echo $days[$day]; ?></h3>
                <?php foreach ($day_meals as $meal): ?>
                <div style="background: var(--black-medium); padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
                    <strong style="color: var(--primary-yellow);"><?php echo $meal_types[$meal['meal_type']]; ?>:</strong>
                    <span style="margin-left: 0.5rem;"><?php echo htmlspecialchars($meal['meal_name']); ?></span>
                    <div style="margin-top: 0.5rem; color: var(--gray-light); font-size: 0.9rem;">
                        <?php if ($meal['calories']): ?>Kalori: <?php echo $meal['calories']; ?> kcal<?php endif; ?>
                        <?php if ($meal['protein']): ?> | Protein: <?php echo $meal['protein']; ?>g<?php endif; ?>
                        <?php if ($meal['carbs']): ?> | Karbonhidrat: <?php echo $meal['carbs']; ?>g<?php endif; ?>
                        <?php if ($meal['fat']): ?> | Yağ: <?php echo $meal['fat']; ?>g<?php endif; ?>
                    </div>
                    <?php if ($meal['description']): ?>
                    <p style="margin-top: 0.5rem; color: var(--gray-light); font-size: 0.9rem;"><?php echo htmlspecialchars($meal['description']); ?></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
            <button class="btn btn-primary" onclick="showAddMealModal()">Yeni Öğün Ekle</button>
        <?php endif; ?>
    </div>
</div>

<!-- Öğün Ekleme Modal -->
<div id="meal-modal" class="hidden" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); z-index: 2000; display: flex; align-items: center; justify-content: center; padding: 2rem;">
    <div style="position: relative; width: 90%; max-width: 800px; max-height: 90vh; display: flex; flex-direction: column;">
        <div class="form-container" style="flex: 1; overflow-y: auto; margin-bottom: 0;">
            <button onclick="closeMealModal()" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; color: var(--primary-yellow); font-size: 1.5rem; cursor: pointer; z-index: 10;">&times;</button>
            <h2 class="form-title">Öğün Ekle</h2>
            <div id="meal-alert-container"></div>
            <form id="meal-form">
                <input type="hidden" id="diet_plan_id" name="diet_plan_id" value="<?php echo $plan_id; ?>">
                <div class="form-group">
                    <label for="meal_type" class="form-label">Öğün Tipi</label>
                    <select id="meal_type" name="meal_type" class="form-input form-select" required>
                        <option value="breakfast">Kahvaltı</option>
                        <option value="lunch">Öğle Yemeği</option>
                        <option value="dinner">Akşam Yemeği</option>
                        <option value="snack">Ara Öğün</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="meal_name" class="form-label">Öğün Adı</label>
                    <input type="text" id="meal_name" name="meal_name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="day_of_week" class="form-label">Gün</label>
                    <select id="day_of_week" name="day_of_week" class="form-input form-select" required>
                        <?php foreach ($days as $day_num => $day_name): ?>
                        <option value="<?php echo $day_num; ?>"><?php echo $day_name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="calories" class="form-label">Kalori (kcal)</label>
                    <input type="number" id="calories" name="calories" class="form-input">
                </div>
                <div class="form-group">
                    <label for="protein" class="form-label">Protein (g)</label>
                    <input type="number" id="protein" name="protein" class="form-input" step="0.01">
                </div>
                <div class="form-group">
                    <label for="carbs" class="form-label">Karbonhidrat (g)</label>
                    <input type="number" id="carbs" name="carbs" class="form-input" step="0.01">
                </div>
                <div class="form-group">
                    <label for="fat" class="form-label">Yağ (g)</label>
                    <input type="number" id="fat" name="fat" class="form-input" step="0.01">
                </div>
                <div class="form-group">
                    <label for="meal_description" class="form-label">Açıklama</label>
                    <textarea id="meal_description" name="meal_description" class="form-input" rows="3"></textarea>
                </div>
            </form>
        </div>
        
        <!-- Butonlar - Her zaman görünür -->
        <div style="display: flex; gap: 1rem; padding: 1.5rem; background: var(--black-light); border-top: 2px solid var(--primary-yellow); border-radius: 0 0 10px 10px;">
            <button type="button" onclick="closeMealModal()" class="btn btn-secondary" style="flex: 1;">❌ İptal</button>
            <button type="submit" id="submit-meal-btn" form="meal-form" class="btn btn-primary" style="flex: 2; font-size: 1.1rem; font-weight: bold;">✅ Öğünü Onayla ve Ekle</button>
        </div>
    </div>
</div>

<?php
$extra_js = ['assets/js/diet-plan-detail.js'];
require_once '../includes/footer.php';
?>

