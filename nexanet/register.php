<?php
require_once 'config/functions.php';
require_once 'config/session.php';

$page_title = 'Daftar Akun - NexaNet';
$current_page = 'register.php';
$error = '';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitize($_POST['nama'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $telepon = sanitize($_POST['telepon'] ?? '');
    $alamat = sanitize($_POST['alamat'] ?? '');
    $wilayah_id = (int)($_POST['wilayah_id'] ?? 0);
    
    if (strlen($nama) < 3) $error = 'Nama minimal 3 karakter';
    elseif (!validateEmail($email)) $error = 'Format email tidak valid';
    elseif (strlen($password) < 6) $error = 'Password minimal 6 karakter';
    elseif ($password !== $password2) $error = 'Konfirmasi password tidak cocok';
    elseif ($wilayah_id < 1) $error = 'Pilih wilayah Anda';
    else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email sudah terdaftar';
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, telepon, alamat, wilayah_id) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$nama, $email, $hashed, $telepon, $alamat, $wilayah_id])) {
                setFlash('success', 'Registrasi berhasil! Silakan login.');
                header('Location: login.php');
                exit;
            } else {
                $error = 'Gagal mendaftar. Coba lagi.';
            }
        }
    }
}

$regions = getRegions($pdo);
include 'includes/header.php';
?>
<div class="bg-surface min-h-screen flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <a href="index.php" class="inline-flex items-center gap-2 mb-4">
                <div class="w-12 h-12 rounded-xl bg-brand-600 flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <span class="font-display font-bold text-2xl text-ink">Nexa<span class="text-brand-600">Net</span></span>
            </a>
            <h1 class="font-display font-bold text-2xl text-ink">Buat Akun Baru</h1>
            <p class="text-sm text-slate-600 mt-1">Bergabung dengan ribuan pelanggan NexaNet</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <?php if ($error): ?>
            <div class="mb-4 p-3 rounded-lg bg-status-outage/10 text-status-outage text-sm border border-status-outage/20"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-ink mb-1.5 block">Nama Lengkap *</label>
                    <input type="text" name="nama" required value="<?php echo sanitize($_POST['nama'] ?? ''); ?>" class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
                </div>
                <div>
                    <label class="text-sm font-medium text-ink mb-1.5 block">Email *</label>
                    <input type="email" name="email" required value="<?php echo sanitize($_POST['email'] ?? ''); ?>" class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
                </div>
                <div>
                    <label class="text-sm font-medium text-ink mb-1.5 block">Nomor Telepon</label>
                    <input type="tel" name="telepon" value="<?php echo sanitize($_POST['telepon'] ?? ''); ?>" placeholder="08xxxxxxxxxx" class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
                </div>
                <div>
                    <label class="text-sm font-medium text-ink mb-1.5 block">Alamat *</label>
                    <textarea name="alamat" required rows="2" class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none resize-none"><?php echo sanitize($_POST['alamat'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label class="text-sm font-medium text-ink mb-1.5 block">Wilayah *</label>
                    <select name="wilayah_id" required class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
                        <option value="">-- Pilih Wilayah --</option>
                        <?php foreach ($regions as $r): ?>
                        <option value="<?php echo $r['id']; ?>" <?php echo (isset($_POST['wilayah_id']) && $_POST['wilayah_id'] == $r['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($r['kota']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm font-medium text-ink mb-1.5 block">Password *</label>
                        <input type="password" name="password" required class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-ink mb-1.5 block">Konfirmasi *</label>
                        <input type="password" name="password2" required class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
                    </div>
                </div>
                <button type="submit" class="w-full py-3 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm rounded-lg transition-colors shadow-sm">Daftar Sekarang</button>
            </form>

            <div class="mt-6 text-center text-sm text-slate-600">
                Sudah punya akun? <a href="login.php" class="font-semibold text-brand-600 hover:text-brand-700">Login di sini</a>
            </div>
        </div>

        <p class="text-center text-xs text-slate-500 mt-6">&copy; 2026 NexaNet. Semua hak dilindungi.</p>
    </div>
</div>
<?php include 'includes/footer.php'; ?>