<?php 
require_once 'config/functions.php';
require_once 'config/session.php';
$page_title = 'Beranda - Portal Digital NexaNet';
$current_page = 'index.php';

// Ambil data dari database
$regions = getRegions($pdo);
$announcements = getAnnouncements($pdo, 3);

// Data user jika login
$user = isLoggedIn() ? getCurrentUser($pdo) : null;
$ticket = $user ? getActiveTicket($pdo, $user['id']) : null;
$billing = $user ? getBillingHistory($pdo, $user['id']) : [];
$dataUsage = $user ? getDataUsage($pdo, $user['id']) : [];

include 'includes/header.php'; 
include 'includes/navbar.php'; 
?>
<main class="flex-1">
    <?php renderFlash(); ?>
    
    <!-- HERO -->
    <section class="bg-brand-50 border-b border-brand-100">
        <div class="max-w-container mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-14">
            <div class="grid lg:grid-cols-2 gap-8 items-center">
                <div>
                    <?php 
                    $normalCount = count(array_filter($regions, fn($r) => $r['status'] === 'Normal'));
                    $totalRegions = count($regions);
                    ?>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white text-brand-700 text-xs font-semibold border border-brand-100 mb-4">
                        <span class="w-2 h-2 rounded-full bg-status-normal"></span> 
                        <?php echo $normalCount; ?> dari <?php echo $totalRegions; ?> Wilayah Beroperasi Normal
                    </span>
                    <h1 class="font-display font-extrabold text-3xl sm:text-4xl lg:text-5xl text-ink leading-tight mb-4">
                        <?php echo $user ? 'Halo, ' . htmlspecialchars(explode(' ', $user['nama'])[0]) . '!' : 'Selamat Datang di Portal Digital NexaNet'; ?>
                    </h1>
                    <p class="text-slate-600 text-base lg:text-lg leading-relaxed mb-6 max-w-xl">Pantau status jaringan, ajukan laporan, dan kelola layanan internet Anda dalam satu platform terpadu.</p>
                    <form action="status-tiket.php" method="GET" class="flex flex-col sm:flex-row gap-3 max-w-xl">
                        <div class="relative flex-1">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" name="search" placeholder="Cari nomor tiket atau wilayah..." class="w-full pl-10 pr-4 py-3 rounded-lg border border-slate-300 bg-white text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
                        </div>
                        <button type="submit" class="px-6 py-3 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm rounded-lg shadow-sm">Cari Sekarang</button>
                    </form>
                </div>
                <div class="hidden lg:flex justify-end">
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-100 w-full max-w-md">
                        <div class="flex items-center justify-between mb-4">
                            <div><div class="text-xs text-slate-500">Kecepatan Saat Ini</div><div class="font-display font-bold text-2xl text-ink">98.7 Mbps</div></div>
                            <div class="w-12 h-12 rounded-full bg-brand-50 flex items-center justify-center"><svg class="w-6 h-6 text-brand-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg></div>
                        </div>
                        <?php if (!empty($dataUsage)): ?>
                        <div class="h-24 flex items-end gap-1.5">
                            <?php foreach ($dataUsage as $d): $h = min(($d['gb']/10)*100, 100); ?>
                            <div class="flex-1 bg-brand-100 rounded-t hover:bg-brand-200 transition-colors" style="height:<?php echo $h; ?>%" title="<?php echo $d['hari']; ?>: <?php echo $d['gb']; ?> GB"></div>
                            <?php endforeach; ?>
                        </div>
                        <div class="flex justify-between text-xs text-slate-500 mt-2"><span><?php echo $dataUsage[0]['hari'] ?? ''; ?></span><span><?php echo end($dataUsage)['hari'] ?? ''; ?></span></div>
                        <?php else: ?>
                        <div class="h-24 flex items-center justify-center text-sm text-slate-500">Login untuk melihat data pemakaian</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SUMMARY CARDS -->
    <section class="max-w-container mx-auto px-4 sm:px-6 lg:px-8 -mt-6 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6">
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3"><span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Status Wilayah Anda</span><span class="px-2 py-0.5 rounded-full bg-status-normal/10 text-status-normal text-xs font-semibold">Normal</span></div>
                <div class="font-display font-bold text-xl text-ink mb-1"><?php echo $user && $user['wilayah_id'] ? getRegionById($pdo, $user['wilayah_id'])['kota'] : ($regions[0]['kota'] ?? 'Jakarta Selatan'); ?></div>
                <div class="flex items-center gap-4 text-sm text-slate-600"><span>Latensi: <strong class="text-ink">24ms</strong></span><span>Uptime: <strong class="text-ink">99.4%</strong></span></div>
            </div>
            
            <?php if ($ticket): ?>
            <a href="detail-tiket.php?kode=<?php echo urlencode($ticket['kode_tiket']); ?>" class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm card-hover block">
                <div class="flex items-center justify-between mb-3"><span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Tiket Aktif</span><span class="px-2 py-0.5 rounded-full bg-status-maintenance/10 text-status-maintenance text-xs font-semibold">Proses</span></div>
                <div class="font-display font-bold text-xl text-ink mb-1">#<?php echo $ticket['kode_tiket']; ?></div>
                <div class="text-sm text-slate-600">Progress: <strong class="text-brand-600"><?php echo $ticket['progress']; ?>%</strong></div>
                <div class="mt-3 h-1.5 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-brand-600 rounded-full" style="width:<?php echo $ticket['progress']; ?>%"></div></div>
            </a>
            <?php else: ?>
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3"><span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Tiket Aktif</span><span class="px-2 py-0.5 rounded-full bg-status-normal/10 text-status-normal text-xs font-semibold">Tidak Ada</span></div>
                <div class="font-display font-bold text-xl text-ink mb-1">Semua Normal</div>
                <div class="text-sm text-slate-600">Tidak ada laporan aktif</div>
            </div>
            <?php endif; ?>
            
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3"><span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Tagihan Bulan Ini</span><span class="text-xs text-slate-500"><?php echo date('M Y'); ?></span></div>
                <?php if (!empty($billing) && $billing[0]['status'] !== 'Lunas'): ?>
                <div class="font-display font-bold text-xl text-ink mb-1"><?php echo formatRupiah($billing[0]['nominal']); ?></div>
                <div class="text-sm text-slate-600 mb-3"><?php echo $billing[0]['paket']; ?></div>
                <button onclick="alert('Fitur pembayaran akan segera hadir')" class="w-full py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg transition-colors">Bayar Sekarang</button>
                <?php else: ?>
                <div class="font-display font-bold text-xl text-ink mb-1">Rp0</div>
                <div class="text-sm text-slate-600">Tidak ada tagihan</div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- QUICK ACCESS -->
    <section class="max-w-container mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="mb-6"><h2 class="font-display font-bold text-xl lg:text-2xl text-ink">Akses Cepat Modul Utama</h2><p class="text-sm text-slate-600 mt-1">Pilih layanan yang ingin Anda gunakan</p></div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
            <?php $modules = [
                ['href'=>'peta-jaringan.php','icon'=>'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7','bg'=>'bg-brand-50 text-brand-600','hover'=>'group-hover:bg-brand-600 group-hover:text-white','title'=>'Peta Jaringan','desc'=>'Pantau status jaringan dan transparansi wilayah secara real-time.','cta'=>'Buka Modul'],
                ['href'=>'diagnosis-mandiri.php','icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z','bg'=>'bg-emerald-50 text-emerald-600','hover'=>'group-hover:bg-emerald-600 group-hover:text-white','title'=>'Diagnosis Mandiri','desc'=>'Selesaikan masalah koneksi sendiri dengan wizard 3 langkah.','cta'=>'Mulai Diagnosis'],
                ['href'=>'simulator-bandwidth.php','icon'=>'M13 10V3L4 14h7v7l9-11h-7z','bg'=>'bg-amber-50 text-amber-600','hover'=>'group-hover:bg-amber-600 group-hover:text-white','title'=>'Simulator Bandwidth','desc'=>'Hitung kebutuhan bandwidth dan estimasi biaya paket Anda.','cta'=>'Hitung Sekarang'],
                ['href'=>'layanan-tambahan.php','icon'=>'M12 6v6m0 0v6m0-6h6m-6 0H6','bg'=>'bg-purple-50 text-purple-600','hover'=>'group-hover:bg-purple-600 group-hover:text-white','title'=>'Layanan Tambahan','desc'=>'Speed boost, ganti password, dan layanan mandiri lainnya.','cta'=>'Lihat Layanan']
            ];
            foreach ($modules as $m): ?>
            <a href="<?php echo $m['href']; ?>" class="bg-white rounded-xl border border-slate-200 p-6 card-hover group">
                <div class="w-12 h-12 rounded-lg <?php echo $m['bg']; ?> <?php echo $m['hover']; ?> flex items-center justify-center mb-4 transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo $m['icon']; ?>"/></svg></div>
                <h3 class="font-display font-semibold text-ink mb-1"><?php echo $m['title']; ?></h3>
                <p class="text-sm text-slate-600 leading-relaxed"><?php echo $m['desc']; ?></p>
                <div class="mt-4 text-sm font-semibold text-brand-600 flex items-center gap-1"><?php echo $m['cta']; ?> <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- ANNOUNCEMENTS -->
    <section class="max-w-container mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between"><div><h2 class="font-display font-bold text-lg text-ink">Pengumuman Layanan Resmi</h2><p class="text-xs text-slate-500 mt-0.5">Informasi terbaru seputar jaringan dan layanan NexaNet</p></div></div>
            <div class="divide-y divide-slate-100">
                <?php foreach ($announcements as $p): ?>
                <article class="p-6 hover:bg-slate-50 transition-colors">
                    <div class="flex flex-col lg:flex-row lg:items-start gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2"><span class="px-2 py-0.5 rounded bg-brand-50 text-brand-700 text-xs font-semibold"><?php echo htmlspecialchars($p['kategori']); ?></span><span class="text-xs text-slate-500"><?php echo formatDate($p['tanggal']); ?></span><span class="text-xs text-slate-400">• <?php echo $p['views']; ?> views</span></div>
                            <h3 class="font-display font-semibold text-ink mb-2"><?php echo htmlspecialchars($p['judul']); ?></h3>
                            <p class="text-sm text-slate-600 leading-relaxed"><?php echo htmlspecialchars($p['ringkasan']); ?></p>
                        </div>
                        <a href="detail-pengumuman.php?slug=<?php echo urlencode($p['slug']); ?>" class="shrink-0 text-sm font-semibold text-brand-600 hover:text-brand-700">Baca Selengkapnya &rarr;</a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>
<?php include 'includes/footer.php'; ?>