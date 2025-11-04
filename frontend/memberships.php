<?php
session_start();
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

require_once 'includes/header.php';
$pageTitle = 'Üyelik Paketleri';
$userInfo = getUserInfo();
?>

<div class="container">
    <h1 style="text-align: center; margin-bottom: 2rem; color: var(--primary-black);">Üyelik Paketleri</h1>
    
    <?php if ($userInfo && isset($userInfo['membershipType'])): ?>
        <div class="success-message" style="max-width: 600px; margin: 0 auto 2rem;">
            <strong>Mevcut Üyeliğiniz:</strong> <?php 
                $membershipNames = ['basic' => 'Temel Üyelik', 'premium' => 'Premium Üyelik', 'vip' => 'VIP Üyelik'];
                echo $membershipNames[$userInfo['membershipType']] ?? 'Yok';
            ?>
        </div>
    <?php endif; ?>

    <div id="memberships-container" class="memberships-grid">
        <p style="text-align: center; width: 100%;">Yükleniyor...</p>
    </div>
</div>

<script>
// Store token in localStorage for JavaScript API calls
<?php if (isLoggedIn()): ?>
localStorage.setItem('auth_token', '<?php echo getAuthToken(); ?>');
<?php endif; ?>
</script>

<?php require_once 'includes/footer.php'; ?>
