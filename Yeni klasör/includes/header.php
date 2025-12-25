<?php
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <?php $css_version = filemtime(__DIR__ . '/../assets/css/style.css'); ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css?v=<?php echo $css_version; ?>">
    <script>
        const SITE_URL = '<?php echo SITE_URL; ?>';
    </script>
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <a href="<?php echo SITE_URL; ?>" class="logo">PowerFit</a>
            
            <!-- Mobile Menu Toggle -->
            <button class="menu-toggle" onclick="toggleMobileMenu()" aria-label="Menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <!-- Navigation Menu -->
            <ul class="nav-menu" id="navMenu">
                <li><a href="<?php echo SITE_URL; ?>" class="nav-link">Ana Sayfa</a></li>
                <li><a href="<?php echo SITE_URL; ?>user/memberships.php" class="nav-link">Üyelikler</a></li>
                <li><a href="<?php echo SITE_URL; ?>user/diet-plans.php" class="nav-link">Diyet Listeleri</a></li>
                <li><a href="<?php echo SITE_URL; ?>user/bmi-calculator.php" class="nav-link">BMI Hesapla</a></li>
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <li><a href="<?php echo SITE_URL; ?>admin/dashboard.php" class="nav-link">Admin Panel</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo SITE_URL; ?>user/dashboard.php" class="nav-link">Panelim</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo SITE_URL; ?>api/logout.php" class="btn btn-secondary btn-small">Çıkış</a></li>
                <?php else: ?>
                    <li><a href="#" class="nav-link" onclick="openAuthModal('login'); return false;">Giriş</a></li>
                    <li><a href="#" class="btn btn-primary btn-small" onclick="openAuthModal('register'); return false;">Kayıt Ol</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('navMenu');
            const toggle = document.querySelector('.menu-toggle');
            menu.classList.toggle('active');
            toggle.classList.toggle('active');
        }
        
        // Menü dışına tıklanınca kapat
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('navMenu');
            const toggle = document.querySelector('.menu-toggle');
            const navbar = document.querySelector('.navbar');
            
            if (menu && toggle && !navbar.contains(event.target) && menu.classList.contains('active')) {
                menu.classList.remove('active');
                toggle.classList.remove('active');
            }
        });
    </script>

