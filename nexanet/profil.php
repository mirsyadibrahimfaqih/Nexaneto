<?php
require_once 'config/functions.php';
require_once 'config/session.php';
requireLogin();

$user = getCurrentUser($pdo);
$page_title = 'Profil Saya - NexaNet';
$current_page = 'profil.php';
$regions = getRegions($pdo);
$myTickets = getUserTickets($pdo, $user['id'], 5);
$myBilling = getBillingHistory($pdo, $user['id']);
$myAddons = getUserAddons($pdo, $user['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $nama = sanitize($_POST['nama']);
    $telepon = sanitize($_POST['telepon']);
    $alamat = sanitize($_POST['alamat']);
    $wilayah_id = (int)$_POST['wilayah_id'];
    
    $stmt = $pdo->prepare("UPDATE users SET nama=?, telepon=?, alamat=?, wilayah_id=? WHERE id=?");
    if ($stmt->execute([$nama, $telepon, $alamat, $wilayah_id, $user['id']])) {
        $_SESSION['user_name'] = $nama;
        setFlash('success', 'Profil berhasil diperbarui');
        header('Location: profil.php');
        exit;
    } else {
        setFlash('error', 'Gagal memperbarui profil');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $old_pass = $_POST['old_password'];
    $new_pass = $_POST['new_password'];
    $new_pass2 = $_POST['new_password2'];
    
    if ($new_pass !== $new_pass2) {
        setFlash('error', 'Konfirmasi password tidak cocok');
    } elseif (strlen($new_pass) < 6) {
        setFlash('error', 'Password baru minimal 6 karakter');
    } elseif (!password_verify($old_pass, $user['password'])) {
        setFlash('error', 'Password lama salah');
    } else {
        $hashed = password_hash($new_pass, PASSWORD_BCRYPT, ['cost' => 12]);
        $pdo->prepare("UPDATE users SET password=? WHERE id=?")->execute([$hashed, $user['id']]);
        setFlash('success', 'Password berhasil diubah');
    }
    header('Location: profil.php');
    exit;
}

include 'includes/header.php';
include 'includes/navbar.php';
?>
<main class="flex-1">
    <?php renderFlash(); ?>
    <div class="max-w-container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="font-display font-bold text-2xl text-ink mb-6">Profil Saya</h1>
        
        <div class="grid lg:grid-cols-3 gap-6">
            <aside class="space-y-4">
                <div class="bg-white rounded-xl border border-slate-200 p-5 text-center">
                    <div class="w-20 h-20 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-2xl mx-auto mb-3"><?php echo strtoupper(substr($user['nama'], 0, 1)); ?></div>
                    <h2 class="font-display font-semibold text-ink"><?php echo htmlspecialchars($user['nama']); ?></h2>
                    <p class="text-sm text-slate-500"><?php echo htmlspecialchars($user['email']); ?></p>
                    <p class="text-xs text-slate-400 mt-2">Bergabung sejak <?php echo formatDate($user['created_at']); ?></p>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <h3 class="font-display font-semibold text-ink mb-3">Statistik</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-slate-600">Total Tiket</span><span class="font-semibold text-ink"><?php echo count($myTickets); ?></span></div>
                        <div class="flex justify-between"><span class="text-slate-600">Layanan Aktif</span><span class="font-semibold text-ink"><?php echo count($myAddons); ?></span></div>
                        <div class="flex justify-between"><span class="text-slate-600">Tagihan Lunas</span><span class="font-semibold text-status-normal"><?php echo count(array_filter($myBilling, fn($b) => $b['status'] === 'Lunas')); ?></span></div>
                    </div>
                </div>
            </aside>

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl border border-slate-200 p-6">
                    <h3 class="font-display font-semibold text-ink mb-4">Informasi Pribadi</h3>
                    <form method="POST" class="space-y-4">
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div><label class="text-sm font-medium text-ink mb-1.5 block">Nama Lengkap</label><input type="text" name="nama" value="<?php echo htmlspecialchars($user['nama']); ?>" required class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none"></div>
                            <div><label class="text-sm font-medium text-ink mb-1.5 block">Email (tidak bisa diubah)</label><input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled class="w-full px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-500"></div>
                            <div><label class="text-sm font-medium text-ink mb-1.5 block">Telepon</label><input type="tel" name="telepon" value="<?php echo htmlspecialchars($user['telepon'] ?? ''); ?>" class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none"></div>
                            <div><label class="text-sm font-medium text-ink mb-1.5 block">Wilayah</label><select name="wilayah_id" required class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none"><?php foreach ($regions as $r): ?><option value="<?php echo $r['id']; ?>" <?php echo $r['id'] == $user['wilayah_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($r['kota']); ?></option><?php endforeach; ?></select></div>
                        </div>
                        <div><label class="text-sm font-medium text-ink mb-1.5 block">Alamat</label><textarea name="alamat" rows="2" class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none resize-none"><?php echo htmlspecialchars($user['alamat'] ?? ''); ?></textarea></div>
                        <button type="submit" name="update_profile" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm rounded-lg transition-colors">Simpan Perubahan</button>
                    </form>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 p-6">
                    <h3 class="font-display font-semibold text-ink mb-4">Ubah Password</h3>
                    <form method="POST" class="space-y-4 max-w-md">
                        <div><label class="text-sm font-medium text-ink mb-1.5 block">Password Lama</label><input type="password" name="old_password" required class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none"></div>
                        <div><label class="text-sm font-medium text-ink mb-1.5 block">Password Baru</label><input type="password" name="new_password" required minlength="6" class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none"></div>
                        <div><label class="text-sm font-medium text-ink mb-1.5 block">Konfirmasi Password Baru</label><input type="password" name="new_password2" required minlength="6" class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none"></div>
                        <button type="submit" name="change_password" class="px-6 py-2.5 border border-brand-600 text-brand-600 hover:bg-brand-50 font-semibold text-sm rounded-lg transition-colors">Ubah Password</button>
                    </form>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100"><h3 class="font-display font-semibold text-ink">Tiket Terbaru</h3></div>
                    <div class="divide-y divide-slate-100">
                        <?php if (empty($myTickets)): ?>
                        <div class="p-6 text-center text-sm text-slate-500">Belum ada tiket</div>
                        <?php else: foreach ($myTickets as $t): ?>
                        <a href="detail-tiket.php?kode=<?php echo urlencode($t['kode_tiket']); ?>" class="px-6 py-4 flex items-center justify-between hover:bg-slate-50">
                            <div><div class="font-medium text-ink text-sm">#<?php echo $t['kode_tiket']; ?></div><div class="text-xs text-slate-500"><?php echo htmlspecialchars($t['judul']); ?></div></div>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold <?php echo $t['status'] === 'Selesai' ? 'bg-status-normal/10 text-status-normal' : 'bg-status-maintenance/10 text-status-maintenance'; ?>"><?php echo $t['status']; ?></span>
                        </a>
                        <?php endforeach; endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php include 'includes/footer.php'; ?>