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

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
$nama = sanitize($data['nama'] ?? '');
$email = sanitize($data['email'] ?? '');
$password = $data['password'] ?? '';
$password2 = $data['password2'] ?? '';
$telepon = sanitize($data['telepon'] ?? '');
$alamat = sanitize($data['alamat'] ?? '');
$wilayah_id = (int)($data['wilayah_id'] ?? 0);

// Validation
if (strlen($nama) < 3) {
    echo json_encode(['success' => false, 'message' => 'Nama minimal 3 karakter']);
    exit;
}

if (!validateEmail($email)) {
    echo json_encode(['success' => false, 'message' => 'Format email tidak valid']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password minimal 6 karakter']);
    exit;
}

if ($password !== $password2) {
    echo json_encode(['success' => false, 'message' => 'Konfirmasi password tidak cocok']);
    exit;
}

if ($wilayah_id < 1) {
    echo json_encode(['success' => false, 'message' => 'Pilih wilayah Anda']);
    exit;
}

// Check if email already exists
try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email sudah terdaftar']);
        exit;
    }

    // Hash password
    $hashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

    // Insert user
    $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, telepon, alamat, wilayah_id) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$nama, $email, $hashed, $telepon, $alamat, $wilayah_id])) {
        echo json_encode([
            'success' => true, 
            'message' => 'Registrasi berhasil! Silakan login.',
            'redirect' => 'login.php'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mendaftar. Coba lagi.']);
    }
} catch (PDOException $e) {
    error_log("Register Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem']);
}
?>