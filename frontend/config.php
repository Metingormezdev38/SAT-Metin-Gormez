<?php
// API Configuration
define('API_BASE_URL', 'http://localhost:3000/api');
define('API_TIMEOUT', 30);

// Site Configuration
define('SITE_NAME', 'Spor Salonu');
define('SITE_URL', 'http://localhost');

// Helper function to make API calls
function apiCall($endpoint, $method = 'GET', $data = null, $token = null) {
    $url = API_BASE_URL . $endpoint;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, API_TIMEOUT);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    $headers = ['Content-Type: application/json'];
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($data && ($method === 'POST' || $method === 'PUT')) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

// Session helper
function getAuthToken() {
    return isset($_SESSION['auth_token']) ? $_SESSION['auth_token'] : null;
}

function isLoggedIn() {
    return isset($_SESSION['auth_token']) && !empty($_SESSION['auth_token']);
}

function getUserInfo() {
    return isset($_SESSION['user_info']) ? $_SESSION['user_info'] : null;
}
?>
