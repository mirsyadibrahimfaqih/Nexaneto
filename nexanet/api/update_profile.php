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

$nama = sanitize($data['nama'] ?? '');
$telepon = sanitize($data['telepon'] ?? '');
$alamat = sanitize($data['alamat'] ?? '');
$wilayah_id = (int)($data['wilayah_id'] ?? 0);

// Validate
if (strlen($nama) < 3) {
    echo json_encode(['success' => false, 'message' => 'Nama minimal 3 karakter']);
    exit;
}

if ($wilayah_id < 1) {
    echo json_encode(['success' => false, 'message' => 'Pilih wilayah Anda']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE users SET nama=?, telepon=?, alamat=?, wilayah_id=? WHERE id=?");
    if ($stmt->execute([$nama, $telepon, $alamat, $wilayah_id, $_SESSION['user_id']])) {
        // Update session
        $_SESSION['user_name'] = $nama;
        
        echo json_encode([
            'success' => true, 
            'message' => 'Profil berhasil diperbarui',
            'user' => [
                'nama' => $nama,
                'telepon' => $telepon,
                'alamat' => $alamat,
                'wilayah_id' => $wilayah_id
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui profil']);
    }
} catch (PDOException $e) {
    error_log("Update Profile Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem']);
}
?>