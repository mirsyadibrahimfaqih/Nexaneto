<?php
require_once __DIR__ . '/database.php';

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function generateSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

function generateTicketCode() {
    return 'TCK-' . str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT);
}

function formatRupiah($angka) {
    return 'Rp' . number_format($angka, 0, ',', '.');
}

function formatDate($date) {
    $bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $timestamp = strtotime($date);
    return date('d', $timestamp) . ' ' . $bulan[date('n', $timestamp) - 1] . ' ' . date('Y', $timestamp);
}

function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    if ($diff < 60) return 'Baru saja';
    if ($diff < 3600) return floor($diff/60) . ' menit lalu';
    if ($diff < 86400) return floor($diff/3600) . ' jam lalu';
    return floor($diff/86400) . ' hari lalu';
}

function getRegions($pdo) {
    $stmt = $pdo->query("SELECT * FROM regions ORDER BY kota");
    return $stmt->fetchAll();
}

function getRegionById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM regions WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getActiveTicket($pdo, $userId) {
    $stmt = $pdo->prepare("
        SELECT t.*, tec.nama as teknisi_nama, tec.kode_teknisi, tec.rating, tec.jumlah_ulasan, tec.kendaraan
        FROM tickets t
        LEFT JOIN technicians tec ON t.teknisi_id = tec.id
        WHERE t.user_id = ? AND t.status != 'Selesai' AND t.status != 'Dibatalkan'
        ORDER BY t.tanggal_lapor DESC LIMIT 1
    ");
    $stmt->execute([$userId]);
    $ticket = $stmt->fetch();
    if ($ticket) {
        $stmtSteps = $pdo->prepare("SELECT * FROM ticket_steps WHERE ticket_id = ? ORDER BY id");
        $stmtSteps->execute([$ticket['id']]);
        $ticket['steps'] = $stmtSteps->fetchAll();
    }
    return $ticket;
}

function getTicketByCode($pdo, $kode) {
    $stmt = $pdo->prepare("
        SELECT t.*, u.nama as user_nama, tec.nama as teknisi_nama, tec.kode_teknisi, tec.rating, tec.jumlah_ulasan, tec.kendaraan, tec.telepon
        FROM tickets t
        LEFT JOIN users u ON t.user_id = u.id
        LEFT JOIN technicians tec ON t.teknisi_id = tec.id
        WHERE t.kode_tiket = ?
    ");
    $stmt->execute([$kode]);
    $ticket = $stmt->fetch();
    if ($ticket) {
        $stmtSteps = $pdo->prepare("SELECT * FROM ticket_steps WHERE ticket_id = ? ORDER BY id");
        $stmtSteps->execute([$ticket['id']]);
        $ticket['steps'] = $stmtSteps->fetchAll();
    }
    return $ticket;
}

function getUserTickets($pdo, $userId, $limit = 10) {
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE user_id = ? ORDER BY tanggal_lapor DESC LIMIT ?");
    $stmt->execute([$userId, $limit]);
    return $stmt->fetchAll();
}

function getBillingHistory($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT * FROM billing_history WHERE user_id = ? ORDER BY bulan DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function getDataUsage($pdo, $userId, $days = 7) {
    $stmt = $pdo->prepare("
        SELECT DAYNAME(tanggal) as hari, pemakaian_gb as gb, tanggal
        FROM data_usage 
        WHERE user_id = ? 
        ORDER BY tanggal DESC 
        LIMIT ?
    ");
    $stmt->execute([$userId, $days]);
    return array_reverse($stmt->fetchAll());
}

function getAddons($pdo) {
    $stmt = $pdo->query("SELECT * FROM addons ORDER BY nama");
    return $stmt->fetchAll();
}

function getUserAddons($pdo, $userId) {
    $stmt = $pdo->prepare("
        SELECT ua.*, a.nama, a.kode_addon, a.kategori, a.harga, a.deskripsi
        FROM user_addons ua
        JOIN addons a ON ua.addon_id = a.id
        WHERE ua.user_id = ? AND ua.status = 'aktif'
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function getMaintenanceSchedule($pdo) {
    $stmt = $pdo->query("SELECT * FROM maintenance_schedule WHERE status = 'Terjadwal' ORDER BY tanggal LIMIT 10");
    return $stmt->fetchAll();
}

function getAnnouncements($pdo, $limit = 5) {
    $stmt = $pdo->prepare("SELECT * FROM announcements WHERE aktif = TRUE ORDER BY tanggal DESC LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

function getAnnouncementBySlug($pdo, $slug) {
    $stmt = $pdo->prepare("UPDATE announcements SET views = views + 1 WHERE slug = ?");
    $stmt->execute([$slug]);
    $stmt = $pdo->prepare("SELECT * FROM announcements WHERE slug = ? AND aktif = TRUE");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

function getBandwidthPackages($pdo) {
    $stmt = $pdo->query("SELECT * FROM bandwidth_packages WHERE aktif = TRUE ORDER BY harga");
    return $stmt->fetchAll();
}

function getTechnicians($pdo) {
    $stmt = $pdo->query("SELECT * FROM technicians ORDER BY rating DESC");
    return $stmt->fetchAll();
}

function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function renderFlash() {
    $flash = getFlash();
    if ($flash) {
        $colors = [
            'success' => 'bg-status-normal/10 text-status-normal border-status-normal/20',
            'error' => 'bg-status-outage/10 text-status-outage border-status-outage/20',
            'warning' => 'bg-status-maintenance/10 text-status-maintenance border-status-maintenance/20',
            'info' => 'bg-brand-50 text-brand-700 border-brand-100'
        ];
        $color = $colors[$flash['type']] ?? $colors['info'];
        echo '<div class="max-w-container mx-auto px-4 sm:px-6 lg:px-8 mt-4"><div class="p-4 rounded-lg border ' . $color . ' text-sm font-medium">' . sanitize($flash['message']) . '</div></div>';
    }
}
?>