<?php
// HS CAR RENTAL - Configuration
session_start();

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'hs_car_rental');

// Application configuration
define('BASE_URL', 'http://localhost/hscarrental/');
define('ADMIN_EMAIL', 'admin@hscarrental.com');
define('CURRENCY', '₹');

// Timezone
date_default_timezone_set('Asia/Kolkata');

// CSRF token helper
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Sanitize input
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

// JSON response helper
function jsonResponse($status, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode(array_merge(['status' => $status, 'message' => $message], $data));
    exit;
}

// Redirect helper
function redirect($url) {
    header("Location: $url");
    exit;
}
