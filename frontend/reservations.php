<?php
session_start();
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

require_once 'includes/header.php';
$pageTitle = 'Rezervasyonlarım';
?>

<div class="container">
    <h1 style="text-align: center; margin-bottom: 2rem; color: var(--primary-black);">Rezervasyonlarım</h1>
    
    <div id="reservations-container" class="reservations-list">
        <p style="text-align: center;">Yükleniyor...</p>
    </div>
</div>

<script>
// Store token in localStorage for JavaScript API calls
<?php if (isLoggedIn()): ?>
localStorage.setItem('auth_token', '<?php echo getAuthToken(); ?>');
<?php endif; ?>

// Load reservations on page load
document.addEventListener('DOMContentLoaded', function() {
    loadReservations();
});
</script>

<?php require_once 'includes/footer.php'; ?>
