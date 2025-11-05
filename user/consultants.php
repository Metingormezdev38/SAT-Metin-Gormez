<?php
$page_title = 'Danışmanlar';
require_once '../includes/header.php';
requireLogin();

// Danışmanları çek
$stmt = $db->prepare("SELECT c.*, u.first_name, u.last_name, u.email, u.phone FROM consultants c JOIN users u ON c.user_id = u.id WHERE c.status = 'available' ORDER BY c.rating DESC");
$stmt->execute();
$consultants = $stmt->fetchAll();
?>

<div class="dashboard">
    <h1 class="section-title">Beslenme ve Fitness Danışmanlarımız</h1>
    
    <div style="text-align: center; margin-bottom: 2rem;">
        <button onclick="openBookingModal()" class="btn btn-primary" style="font-size: 1.1rem; padding: 1rem 2rem;">
            📅 Randevu Al
        </button>
    </div>
    
    <div class="consultants-grid">
        <?php foreach ($consultants as $consultant): ?>
        <div class="consultant-card" 
             data-consultant-id="<?php echo $consultant['id']; ?>"
             data-consultant-name="<?php echo htmlspecialchars($consultant['first_name'] . ' ' . $consultant['last_name']); ?>"
             data-consultant-specialization="<?php echo htmlspecialchars($consultant['specialization']); ?>">
            <h3 class="consultant-name"><?php echo htmlspecialchars($consultant['first_name'] . ' ' . $consultant['last_name']); ?></h3>
            <p class="consultant-specialization"><?php echo htmlspecialchars($consultant['specialization']); ?></p>
            <p class="consultant-rating">⭐ <?php echo number_format($consultant['rating'], 1); ?> / 5.0</p>
            <p style="color: var(--gray-light); margin-bottom: 1rem;"><?php echo htmlspecialchars($consultant['bio']); ?></p>
            <p style="color: var(--primary-yellow); font-weight: bold; margin-bottom: 1rem;"><?php echo number_format($consultant['price_per_session'], 2); ?> TL / Seans</p>
            <p style="color: var(--gray-light); font-size: 0.9rem; margin-bottom: 1rem;">Tecrübe: <?php echo $consultant['experience_years']; ?> yıl</p>
            <button class="btn btn-primary book-consultant-btn" data-consultant-id="<?php echo $consultant['id']; ?>">Randevu Al</button>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($consultants)): ?>
        <div class="card">
            <p class="text-center" style="color: var(--gray-light);">Şu anda müsait danışman bulunmamaktadır.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Randevu Modal -->
<div id="booking-modal" class="hidden" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); z-index: 2000; display: flex; align-items: center; justify-content: center;">
    <div class="form-container" style="position: relative; width: 90%; max-width: 800px;">
        <button onclick="closeBookingModal()" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; color: var(--primary-yellow); font-size: 1.5rem; cursor: pointer;">&times;</button>
        <h2 class="form-title">Randevu Al</h2>
        <div id="booking-alert-container"></div>
        
        <form id="booking-form">
            <div class="form-group">
                <label for="consultant_select" class="form-label">Danışman Seçin</label>
                <select id="consultant_select" name="consultant_id" class="form-input form-select" required>
                    <option value="">-- Danışman Seçin --</option>
                    <?php foreach ($consultants as $consultant): ?>
                    <option value="<?php echo $consultant['id']; ?>" 
                            data-name="<?php echo htmlspecialchars($consultant['first_name'] . ' ' . $consultant['last_name']); ?>"
                            data-specialization="<?php echo htmlspecialchars($consultant['specialization']); ?>">
                        <?php echo htmlspecialchars($consultant['first_name'] . ' ' . $consultant['last_name']); ?> - <?php echo htmlspecialchars($consultant['specialization']); ?> (⭐ <?php echo number_format($consultant['rating'], 1); ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
                <small style="color: var(--gray-light); font-size: 0.85rem; display: block; margin-top: 0.5rem;">Lütfen bir danışman seçin</small>
            </div>
            
            <!-- Seçilen Danışman Detay Bilgisi -->
            <div id="selected-consultant-details" style="background: var(--black-medium); padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem; border: 2px solid var(--primary-yellow); display: none;">
                <p style="color: var(--gray-light); margin-bottom: 0.5rem;">Seçilen Danışman Detayları:</p>
                <p id="consultant-detail-display" style="color: var(--primary-yellow); font-size: 1rem;"></p>
            </div>
            
            <div class="form-group">
                <label for="booking_date" class="form-label">Randevu Tarihi ve Saati</label>
                <input type="datetime-local" id="booking_date" name="booking_date" class="form-input" required>
                <small style="color: var(--gray-light); font-size: 0.85rem; display: block; margin-top: 0.5rem;">Lütfen tarih ve saati seçin</small>
            </div>
            <div class="form-group">
                <label for="notes" class="form-label">Notlar (İsteğe bağlı)</label>
                <textarea id="notes" name="notes" class="form-input" rows="4"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Randevu Al</button>
        </form>
    </div>
</div>

<?php
$extra_js = ['assets/js/consultants.js'];
require_once '../includes/footer.php';
?>

