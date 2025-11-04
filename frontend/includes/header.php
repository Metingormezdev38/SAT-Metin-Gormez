<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1><?php echo SITE_NAME; ?></h1>
                </div>
                <nav class="nav">
                    <a href="index.php">Ana Sayfa</a>
                    <a href="classes.php">Ders Programı</a>
                    <a href="bmi.php">BMI Hesapla</a>
                    <a href="contact.php">İletişim</a>
                    <?php if (isLoggedIn()): ?>
                        <a href="reservations.php">Rezervasyonlarım</a>
                        <a href="memberships.php">Üyelik</a>
                        <a href="logout.php" class="btn-logout">Çıkış</a>
                    <?php else: ?>
                        <a href="login.php" class="btn-login">Giriş</a>
                        <a href="register.php" class="btn-register">Kayıt Ol</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>
    <main class="main-content">
