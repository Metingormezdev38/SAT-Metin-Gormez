<?php
session_start();

// Site ayarları
define('SITE_URL', 'http://localhost/');
define('SITE_NAME', 'PowerFit Spor Salonu');

// Zaman dilimi
date_default_timezone_set('Europe/Istanbul');

// Hata raporlama (production'da kapatılmalı)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database bağlantısı
require_once __DIR__ . '/database.php';
$database = new Database();
$db = $database->getConnection();

// Helper fonksiyonlar
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . 'user/login.php');
        exit;
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ' . SITE_URL . 'admin/login.php');
        exit;
    }
}
?>

