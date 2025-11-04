<?php
session_start();
require_once 'config.php';
require_once 'includes/header.php';

$pageTitle = 'Ders Programı';
?>

<div class="container">
    <h1 style="text-align: center; margin-bottom: 2rem; color: var(--primary-black);">Ders Programı</h1>
    <p style="text-align: center; color: var(--text-light); margin-bottom: 2rem;">
        Aşağıdaki derslerimize katılabilir ve rezervasyon yapabilirsiniz.
    </p>
    
    <div id="classes-container" class="classes-grid">
        <p style="text-align: center; width: 100%;">Yükleniyor...</p>
    </div>
</div>

<script>
// Load classes on page load
document.addEventListener('DOMContentLoaded', function() {
    loadClasses();
});
</script>

<?php require_once 'includes/footer.php'; ?>
