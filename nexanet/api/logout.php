<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/session.php';

// Destroy session
session_destroy();

// Clear remember me cookie
if (isset($_COOKIE['nexanet_remember'])) {
    setcookie('nexanet_remember', '', time() - 3600, '/');
}

echo json_encode([
    'success' => true, 
    'message' => 'Logout berhasil',
    'redirect' => 'login.php'
]);
?>