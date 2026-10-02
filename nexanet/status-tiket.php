<?php 
require_once 'config/functions.php';
require_once 'config/session.php';
$page_title = 'Status Tiket - NexaNet';
$current_page = 'status-tiket.php';

// Tidak perlu requireLogin() - halaman ini bisa diakses publik
$user = isLoggedIn() ? getCurrentUser($pdo) : null;
$myTickets = $user ? getUserTickets($pdo, $user['id']) : [];
$activeTicket = $user ? getActiveTicket($pdo, $user['id']) : null;

include 'includes/header.php'; 
include 'includes/navbar.php'; 
?>
<main class="flex-1">
    <?php renderFlash(); ?>
    <section class="bg-white border-b border-slate-200">
        <div class="max-w-container mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="font-display font-bold text-2xl lg:text-3xl text-ink">Pelacakan Laporan Gangguan & Status Teknisi</h1>
            <p class="text-sm text-slate-600 mt-1">Pantau progress perbaikan dan posisi teknisi yang menangani laporan Anda.</p>
        </div>
    </section>
    <section class="max-w-container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-xl border border-slate-200 p-5 mb-6">
            <label class="text-sm font-medium text-ink mb-2 block">Cari ID Tiket</label>
            <div class="flex gap-3">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" id="ticketSearch" placeholder="Masukkan ID tiket (contoh: TCK-88291)" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
                </div>
                <button onclick="const v=document.getElementById('ticketSearch').value.trim(); if(v.length>=3) window.location.href='detail-tiket.php?kode='+encodeURIComponent(v); else alert('Masukkan minimal 3 karakter')" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm rounded-lg transition-colors">Cari</button>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-4">
                <?php if ($user && !empty($myTickets)): ?>
                <h2 class="font-display font-semibold text-lg text-ink mb-2">Daftar Tiket Saya</h2>
                <?php foreach ($myTickets as $t): 
                    $statusColor = $t['status'] === 'Selesai' ? 'bg-status-normal/10 text-status-normal' : ($t['status'] === 'Dalam Perbaikan' ? 'bg-status-maintenance/10 text-status-maintenance' : 'bg-slate-100 text-slate-600');
                ?>
                <a href="detail-tiket.php?kode=<?php echo urlencode($t['kode_tiket']); ?>" class="bg-white rounded-xl border border-slate-200 p-5 card-hover block">
                    <div class="flex items-start justify-between gap-4 mb-3">
                        <div>
                            <div class="font-display font-semibold text-ink mb-1">#<?php echo $t['kode_tiket']; ?></div>
                            <div class="text-sm text-slate-600 line-clamp-1"><?php echo htmlspecialchars($t['judul']); ?></div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold shrink-0 <?php echo $statusColor; ?>"><?php echo $t['status']; ?></span>
                    </div>
                    <div class="flex items-center justify-between text-xs text-slate-500">
                        <span><?php echo timeAgo($t['tanggal_lapor']); ?></span>
                        <span>Progress: <strong class="text-brand-600"><?php echo $t['progress']; ?>%</strong></span>
                    </div>
                    <div class="mt-2 h-1.5 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-brand-600 rounded-full" style="width:<?php echo $t['progress']; ?>%"></div></div>
                </a>
                <?php endforeach; ?>
                <?php else: ?>
                <div class="bg-white rounded-xl border border-slate-200 p-8 text-center">
                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p class="text-slate-600 mb-4">
                        <?php echo $user ? 'Anda belum memiliki tiket laporan.' : 'Login untuk melihat daftar tiket Anda, atau cari tiket dengan ID di atas.'; ?>
                    </p>
                    <?php if (!$user): ?>
                    <a href="login.php?redirect=status-tiket.php" class="inline-block px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm rounded-lg transition-colors">Login untuk Melihat Tiket</a>
                    <?php else: ?>
                    <a href="diagnosis-mandiri.php" class="inline-block px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm rounded-lg transition-colors">Buat Tiket Baru</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <aside class="space-y-4">
                <?php if ($activeTicket): ?>
                <div class="bg-white rounded-xl border-2 border-brand-500 p-5">
                    <div class="flex items-center gap-2 mb-3"><span class="w-2.5 h-2.5 rounded-full bg-brand-600 animate-pulse"></span><span class="text-xs font-semibold text-brand-600 uppercase tracking-wide">Tiket Aktif</span></div>
                    <h3 class="font-display font-bold text-lg text-ink mb-1">#<?php echo $activeTicket['kode_tiket']; ?></h3>
                    <p class="text-sm text-slate-600 mb-4 line-clamp-2"><?php echo htmlspecialchars($activeTicket['judul']); ?></p>
                    <div class="space-y-3 mb-4">
                        <div class="flex justify-between text-sm"><span class="text-slate-600">Progress</span><span class="font-semibold text-ink"><?php echo $activeTicket['progress']; ?>%</span></div>
                        <div class="h-2 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-brand-600 rounded-full" style="width:<?php echo $activeTicket['progress']; ?>%"></div></div>
                        <?php if ($activeTicket['teknisi_nama']): ?>
                        <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                            <div class="w-8 h-8 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-semibold text-xs"><?php echo strtoupper(substr($activeTicket['teknisi_nama'], 0, 1)); ?></div>
                            <div class="text-xs">
                                <div class="font-semibold text-ink"><?php echo htmlspecialchars($activeTicket['teknisi_nama']); ?></div>
                                <div class="text-slate-500"><?php echo htmlspecialchars($activeTicket['kode_teknisi']); ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <a href="detail-tiket.php?kode=<?php echo urlencode($activeTicket['kode_tiket']); ?>" class="block w-full py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm rounded-lg text-center transition-colors">Lihat Detail</a>
                </div>
                <?php endif; ?>

                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <h3 class="font-display font-semibold text-ink mb-3">Butuh Bantuan?</h3>
                    <p class="text-sm text-slate-600 mb-4">Jika mengalami kendala, gunakan fitur diagnosis mandiri untuk solusi cepat.</p>
                    <a href="diagnosis-mandiri.php" class="block w-full py-2.5 border border-brand-600 text-brand-600 hover:bg-brand-50 font-semibold text-sm rounded-lg text-center transition-colors">Mulai Diagnosis</a>
                </div>
            </aside>
        </div>
    </section>
</main>
<?php include 'includes/footer.php'; ?>