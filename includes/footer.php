    <footer class="footer">
        <div class="footer-content">
            <p class="footer-text">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Tüm hakları saklıdır.</p>
            <div class="footer-links">
                <a href="<?php echo SITE_URL; ?>" class="footer-link">Ana Sayfa</a>
                <a href="<?php echo SITE_URL; ?>user/consultants.php" class="footer-link">Danışmanlar</a>
                <a href="<?php echo SITE_URL; ?>user/bmi-calculator.php" class="footer-link">BMI Hesapla</a>
                <?php if (isLoggedIn() && isAdmin()): ?>
                    <a href="<?php echo SITE_URL; ?>admin/dashboard.php" class="footer-link">Admin Panel</a>
                <?php endif; ?>
            </div>
        </div>
    </footer>
    <script src="<?php echo SITE_URL; ?>assets/js/main.js"></script>
    <?php if (isset($extra_js)): ?>
        <?php foreach ($extra_js as $js_file): ?>
            <script src="<?php echo SITE_URL . $js_file; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>

