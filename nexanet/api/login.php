<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/functions.php';
require_once '../config/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$email = sanitize($data['email'] ?? '');
$password = $data['password'] ?? '';
$remember = $data['remember'] ?? false;

if (!validateEmail($email) || strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Format email atau password tidak valid']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nama'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        // Update last login
        $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);

        // Remember me
        if ($remember) {
            setcookie('nexanet_remember', $user['email'], time() + (86400 * 30), '/');
        }

        echo json_encode([
            'success' => true, 
            'message' => 'Login berhasil',
            'redirect' => 'index.php',
            'user' => [
                'id' => $user['id'],
                'nama' => $user['nama'],
                'email' => $user['email']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Email atau password salah']);
    }
} catch (PDOException $e) {
    error_log("Login Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem']);
}
?>