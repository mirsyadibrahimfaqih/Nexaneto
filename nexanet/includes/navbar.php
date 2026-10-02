<?php
$active = basename($_SERVER['PHP_SELF']);
$nav_items = [
    ['file'=>'index.php','label'=>'Beranda'],
    ['file'=>'peta-jaringan.php','label'=>'Peta Jaringan'],
    ['file'=>'diagnosis-mandiri.php','label'=>'Diagnosis Mandiri'],
    ['file'=>'simulator-bandwidth.php','label'=>'Simulator Bandwidth'],
    ['file'=>'status-tiket.php','label'=>'Status Tiket'],
    ['file'=>'layanan-tambahan.php','label'=>'Layanan Tambahan']
];
$user = isLoggedIn() ? getCurrentUser($pdo) : null;
$initials = $user ? strtoupper(substr($user['nama'], 0, 1)) . (strpos($user['nama'], ' ') ? strtoupper(substr(strstr($user['nama'], ' '), 1, 1)) : '') : 'G';
?>
<header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm">
    <div class="max-w-container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="index.php" class="flex items-center gap-2 shrink-0">
                <div class="w-9 h-9 rounded-lg bg-brand-600 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <span class="font-display font-bold text-lg text-ink">Nexa<span class="text-brand-600">Net</span></span>
            </a>
            <nav class="hidden lg:flex items-center gap-1">
                <?php foreach ($nav_items as $item):
                    $is_active = ($item['file'] === $active); ?>
                    <a href="<?php echo $item['file']; ?>" class="px-3 py-2 text-sm font-medium rounded-md transition-colors <?php echo $is_active ? 'text-brand-700 bg-brand-50' : 'text-slate-600 hover:text-brand-600 hover:bg-slate-50'; ?>"><?php echo $item['label']; ?></a>
                <?php endforeach; ?>
            </nav>
            <div class="flex items-center gap-3">
                <?php if ($user): ?>
                <div class="hidden sm:flex items-center gap-2 pl-3 border-l border-slate-200">
                    <div class="w-8 h-8 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-semibold text-sm"><?php echo $initials; ?></div>
                    <div class="hidden md:block leading-tight">
                        <div class="text-sm font-semibold text-ink"><?php echo htmlspecialchars(explode(' ', $user['nama'])[0]); ?></div>
                        <div class="text-xs text-slate-500">Pelanggan</div>
                    </div>
                    <a href="profil.php" class="ml-2 p-1.5 rounded-md hover:bg-slate-100 text-slate-600" title="Profil">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </a>
                    <a href="logout.php" class="p-1.5 rounded-md hover:bg-slate-100 text-slate-600" title="Logout">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </a>
                </div>
                <?php else: ?>
                <a href="login.php" class="hidden sm:inline-flex px-4 py-2 text-sm font-semibold text-brand-600 hover:bg-brand-50 rounded-lg transition-colors">Masuk</a>
                <a href="register.php" class="hidden sm:inline-flex px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">Daftar</a>
                <?php endif; ?>
                <button id="mobileMenuBtn" class="lg:hidden p-2 rounded-md text-slate-600 hover:bg-slate-100">
                    <svg id="iconOpen" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg id="iconClose" class="w-6 h-6 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </div>
    <div id="mobileDrawer" class="lg:hidden hidden border-t border-slate-200 bg-white">
        <nav class="max-w-container mx-auto px-4 py-3 flex flex-col gap-1">
            <?php foreach ($nav_items as $item):
                $is_active = ($item['file'] === $active); ?>
                <a href="<?php echo $item['file']; ?>" class="px-3 py-2.5 text-sm font-medium rounded-md transition-colors <?php echo $is_active ? 'text-brand-700 bg-brand-50' : 'text-slate-700 hover:bg-slate-50'; ?>"><?php echo $item['label']; ?></a>
            <?php endforeach; ?>
            <div class="border-t border-slate-100 my-2"></div>
            <?php if ($user): ?>
                <a href="profil.php" class="px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-md">Profil Saya</a>
                <a href="logout.php" class="px-3 py-2.5 text-sm font-medium text-status-outage hover:bg-status-outage/5 rounded-md">Keluar</a>
            <?php else: ?>
                <a href="login.php" class="px-3 py-2.5 text-sm font-medium text-brand-600 hover:bg-brand-50 rounded-md">Masuk</a>
                <a href="register.php" class="px-3 py-2.5 text-sm font-medium bg-brand-600 text-white rounded-md text-center">Daftar</a>
            <?php endif; ?>
        </nav>
    </div>
</header>