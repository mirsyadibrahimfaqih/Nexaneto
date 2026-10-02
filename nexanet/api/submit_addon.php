<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/functions.php';
require_once '../config/session.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$addonId = (int)($data['addon_id'] ?? 0);
$action = sanitize($data['action'] ?? 'activate');

if ($addonId < 1) {
    echo json_encode(['success' => false, 'message' => 'Addon tidak valid']);
    exit;
}

if (!in_array($action, ['activate', 'deactivate'])) {
    echo json_encode(['success' => false, 'message' => 'Aksi tidak valid']);
    exit;
}

try {
    if ($action === 'activate') {
        // Check if already active
        $stmt = $pdo->prepare("SELECT id FROM user_addons WHERE user_id = ? AND addon_id = ? AND status = 'aktif'");
        $stmt->execute([$_SESSION['user_id'], $addonId]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Layanan sudah aktif']);
            exit;
        }

        // Activate addon
        $stmt = $pdo->prepare("INSERT INTO user_addons (user_id, addon_id, tanggal_aktif, status) VALUES (?, ?, CURDATE(), 'aktif') ON DUPLICATE KEY UPDATE status='aktif', tanggal_nonaktif=NULL, tanggal_aktif=CURDATE()");
        $stmt->execute([$_SESSION['user_id'], $addonId]);
        
        echo json_encode(['success' => true, 'message' => 'Layanan berhasil diaktifkan']);
    } else {
        // Deactivate addon
        $stmt = $pdo->prepare("UPDATE user_addons SET status='nonaktif', tanggal_nonaktif=CURDATE() WHERE user_id=? AND addon_id=? AND status='aktif'");
        $stmt->execute([$_SESSION['user_id'], $addonId]);
        
        echo json_encode(['success' => true, 'message' => 'Layanan berhasil dinonaktifkan']);
    }
} catch (PDOException $e) {
    error_log("Submit Addon Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Gagal memproses layanan']);
}
?>