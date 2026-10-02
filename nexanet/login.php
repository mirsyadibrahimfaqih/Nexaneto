<?php
require_once 'config/functions.php';
require_once 'config/session.php';

$page_title = 'Login - NexaNet';
$current_page = 'login.php';
$error = '';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if (!validateEmail($email) || strlen($password) < 6) {
        $error = 'Format email atau password tidak valid';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nama'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);

            if ($remember) {
                setcookie('nexanet_remember', $user['email'], time() + (86400 * 30), '/');
            }

            $redirect = $_GET['redirect'] ?? 'index.php';
            setFlash('success', 'Selamat datang kembali, ' . explode(' ', $user['nama'])[0] . '!');
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = 'Email atau password salah';
        }
    }
}

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
            <h1 class="font-display font-bold text-2xl text-ink">Selamat Datang Kembali</h1>
            <p class="text-sm text-slate-600 mt-1">Login untuk mengakses portal Anda</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <?php if ($error): ?>
            <div class="mb-4 p-3 rounded-lg bg-status-outage/10 text-status-outage text-sm border border-status-outage/20"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php renderFlash(); ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-ink mb-1.5 block">Email</label>
                    <input type="email" name="email" required value="<?php echo sanitize($_POST['email'] ?? ''); ?>" class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-sm font-medium text-ink">Password</label>
                        <a href="#" class="text-xs text-brand-600 hover:text-brand-700">Lupa password?</a>
                    </div>
                    <input type="password" name="password" required class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" class="w-4 h-4 text-brand-600 rounded">
                    Ingat saya selama 30 hari
                </label>
                <button type="submit" class="w-full py-3 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm rounded-lg transition-colors shadow-sm">Masuk</button>
            </form>

            <div class="mt-6 text-center text-sm text-slate-600">
                Belum punya akun? <a href="register.php" class="font-semibold text-brand-600 hover:text-brand-700">Daftar sekarang</a>
            </div>

            <div class="mt-6 pt-6 border-t border-slate-100">
                <div class="text-xs text-slate-500 text-center mb-2">Demo Akun:</div>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="p-2 bg-slate-50 rounded text-center">
                        <div class="font-semibold text-ink">User</div>
                        <div class="text-slate-600">budi@nexanet.id</div>
                    </div>
                    <div class="p-2 bg-slate-50 rounded text-center">
                        <div class="font-semibold text-ink">Password</div>
                        <div class="text-slate-600">nexanet123</div>
                    </div>
                </div>
            </div>
        </div>

        <p class="text-center text-xs text-slate-500 mt-6">&copy; 2026 NexaNet. Semua hak dilindungi.</p>
    </div>
</div>
<?php include 'includes/footer.php'; ?>