<?php
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
    <script>
        const SITE_URL = '<?php echo SITE_URL; ?>';
    </script>
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <a href="<?php echo SITE_URL; ?>" class="logo">PowerFit</a>
            <ul class="nav-menu">
                <li><a href="<?php echo SITE_URL; ?>" class="nav-link">Ana Sayfa</a></li>
                <li><a href="<?php echo SITE_URL; ?>user/diet-plans.php" class="nav-link">Diyet Listeleri</a></li>
                <li><a href="<?php echo SITE_URL; ?>user/consultants.php" class="nav-link">Danışmanlar</a></li>
                <li><a href="<?php echo SITE_URL; ?>user/bmi-calculator.php" class="nav-link">BMI Hesapla</a></li>
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <li><a href="<?php echo SITE_URL; ?>admin/dashboard.php" class="nav-link">Admin Panel</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo SITE_URL; ?>user/dashboard.php" class="nav-link">Panelim</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo SITE_URL; ?>api/logout.php" class="btn btn-secondary">Çıkış</a></li>
                <?php else: ?>
                    <li><a href="<?php echo SITE_URL; ?>user/login.php" class="nav-link">Giriş</a></li>
                    <li><a href="<?php echo SITE_URL; ?>user/register.php" class="btn btn-primary">Kayıt Ol</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

