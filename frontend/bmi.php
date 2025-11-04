<?php
session_start();
require_once 'config.php';
require_once 'includes/header.php';

$pageTitle = 'BMI Hesapla';
?>

<div class="container">
    <h1 style="text-align: center; margin-bottom: 2rem; color: var(--primary-black);">BMI Hesaplayıcı</h1>
    
    <div class="bmi-calculator">
        <p style="text-align: center; color: var(--text-light); margin-bottom: 2rem;">
            Vücut Kitle Endeksinizi (BMI) hesaplayarak sağlıklı kilo aralığınızı öğrenin.
        </p>

        <form id="bmi-form" onsubmit="event.preventDefault(); calculateBMI();">
            <div class="form-group">
                <label for="height">Boy (cm)</label>
                <input type="number" id="height" name="height" min="50" max="250" required placeholder="Örn: 175">
            </div>

            <div class="form-group">
                <label for="weight">Kilo (kg)</label>
                <input type="number" id="weight" name="weight" min="20" max="300" required placeholder="Örn: 70">
            </div>

            <button type="submit" class="btn" style="width: 100%;">Hesapla</button>
        </form>

        <div id="bmi-result" style="display: none;"></div>
    </div>
</div>

<script>
// Store token in localStorage for JavaScript API calls
<?php if (isLoggedIn()): ?>
localStorage.setItem('auth_token', '<?php echo getAuthToken(); ?>');
<?php endif; ?>
</script>

<?php require_once 'includes/footer.php'; ?>
