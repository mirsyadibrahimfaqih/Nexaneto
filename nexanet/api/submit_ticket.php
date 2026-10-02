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

$judul = sanitize($data['judul'] ?? '');
$kategori = sanitize($data['kategori'] ?? 'Gangguan Jaringan');
$prioritas = sanitize($data['prioritas'] ?? 'Sedang');
$deskripsi = sanitize($data['deskripsi'] ?? '');

// Validate
if (strlen($judul) < 5) {
    echo json_encode(['success' => false, 'message' => 'Judul tiket minimal 5 karakter']);
    exit;
}

if (strlen($deskripsi) < 10) {
    echo json_encode(['success' => false, 'message' => 'Deskripsi minimal 10 karakter']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Generate ticket code
    $kode = generateTicketCode();

    // Insert ticket
    $stmt = $pdo->prepare("INSERT INTO tickets (kode_tiket, user_id, judul, kategori, prioritas, status, progress, deskripsi) VALUES (?, ?, ?, ?, ?, 'Baru', 0, ?)");
    $stmt->execute([$kode, $_SESSION['user_id'], $judul, $kategori, $prioritas, $deskripsi]);
    $ticketId = $pdo->lastInsertId();

    // Insert ticket steps
    $steps = [
        ['Laporan Diterima', 'selesai', date('d M, H:i')],
        ['Verifikasi Lapangan', 'pending', '-'],
        ['Teknisi Menuju Lokasi', 'pending', '-'],
        ['Selesai', 'pending', '-']
    ];

    $stmtStep = $pdo->prepare("INSERT INTO ticket_steps (ticket_id, label, status, waktu) VALUES (?, ?, ?, ?)");
    foreach ($steps as $s) {
        $stmtStep->execute([$ticketId, $s[0], $s[1], $s[2]]);
    }

    $pdo->commit();

    echo json_encode([
        'success' => true, 
        'message' => 'Tiket berhasil dibuat',
        'kode_tiket' => $kode,
        'redirect' => 'detail-tiket.php?kode=' . urlencode($kode)
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Submit Ticket Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Gagal membuat tiket']);
}
?>