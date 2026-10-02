<?php 
require_once 'config/functions.php';
require_once 'config/session.php';
$page_title = 'Peta Status Jaringan - NexaNet';
$current_page = 'peta-jaringan.php';

$regions = getRegions($pdo);
$maintenance = getMaintenanceSchedule($pdo);

include 'includes/header.php'; 
include 'includes/navbar.php';

function statusColor($s){ 
    return match($s){
        'Normal'=>'bg-status-normal','Pemeliharaan'=>'bg-status-maintenance','Gangguan'=>'bg-status-outage',default=>'bg-slate-400'
    }; 
}
function statusBadge($s){ 
    return match($s){
        'Normal'=>'bg-status-normal/10 text-status-normal','Pemeliharaan'=>'bg-status-maintenance/10 text-status-maintenance','Gangguan'=>'bg-status-outage/10 text-status-outage',default=>'bg-slate-100 text-slate-600'
    }; 
}
?>
<main class="flex-1">
    <section class="bg-white border-b border-slate-200">
        <div class="max-w-container mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div><h1 class="font-display font-bold text-2xl lg:text-3xl text-ink">Peta Status Jaringan & Transparansi Wilayah</h1><p class="text-sm text-slate-600 mt-1">Pantau kondisi jaringan di wilayah Anda secara real-time</p></div>
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="w-2 h-2 rounded-full bg-status-normal"></span> Normal 
                    <span class="w-2 h-2 rounded-full bg-status-maintenance ml-2"></span> Pemeliharaan 
                    <span class="w-2 h-2 rounded-full bg-status-outage ml-2"></span> Gangguan
                </div>
            </div>
        </div>
    </section>
    <section class="max-w-container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-4">
                <h2 class="font-display font-semibold text-lg text-ink mb-2">Status Wilayah Regional</h2>
                <?php foreach ($regions as $w): ?>
                <div class="bg-white rounded-xl border border-slate-200 p-5 card-hover">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="relative w-3 h-3"><span class="status-dot absolute inset-0 <?php echo statusColor($w['status']); ?>"></span></div>
                            <div><h3 class="font-display font-semibold text-ink"><?php echo htmlspecialchars($w['kota']); ?></h3><span class="text-xs text-slate-500">Paket: <?php echo htmlspecialchars($w['paket']); ?></span></div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?php echo statusBadge($w['status']); ?>"><?php echo htmlspecialchars($w['status']); ?></span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                        <div><div class="text-xs text-slate-500 mb-1">Latensi</div><div class="font-semibold text-ink"><?php echo htmlspecialchars($w['latensi']); ?></div></div>
                        <div><div class="text-xs text-slate-500 mb-1">Pengguna Terdampak</div><div class="font-semibold text-ink"><?php echo number_format($w['pengguna']); ?></div></div>
                        <div><div class="text-xs text-slate-500 mb-1">Uptime</div><div class="font-semibold text-ink"><?php echo htmlspecialchars($w['uptime']); ?></div></div>
                        <div><div class="text-xs text-slate-500 mb-1">Estimasi Perbaikan</div><div class="font-semibold text-ink"><?php echo htmlspecialchars($w['estimasi_perbaikan']); ?></div></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <aside class="space-y-4">
                <?php 
                $gangguan = array_filter($regions, fn($r) => $r['status'] === 'Gangguan');
                if (!empty($gangguan)): 
                    $depok = reset($gangguan);
                ?>
                <div class="bg-white rounded-xl border-2 border-status-outage/30 p-5">
                    <div class="flex items-center gap-2 mb-3"><span class="w-2.5 h-2.5 rounded-full bg-status-outage animate-pulse"></span><span class="text-xs font-semibold text-status-outage uppercase tracking-wide">Gangguan Massal Terdeteksi</span></div>
                    <h3 class="font-display font-bold text-lg text-ink mb-1">Wilayah <?php echo htmlspecialchars($depok['kota']); ?></h3>
                    <p class="text-sm text-slate-600 mb-4">Gangguan pada backbone fiber optik utama. Tim teknisi sedang dalam perjalanan.</p>
                    <div class="space-y-3 mb-5">
                        <div class="flex justify-between text-sm"><span class="text-slate-600">Progress Perbaikan</span><span class="font-semibold text-ink">70%</span></div>
                        <div class="h-2 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-brand-600 rounded-full" style="width:70%"></div></div>
                        <div class="grid grid-cols-3 gap-2 pt-2">
                            <div class="text-center p-2 bg-slate-50 rounded-lg"><div class="font-bold text-ink text-lg"><?php echo number_format($depok['pengguna']); ?></div><div class="text-xs text-slate-500">Terdampak</div></div>
                            <div class="text-center p-2 bg-slate-50 rounded-lg"><div class="font-bold text-ink text-lg">1.281</div><div class="text-xs text-slate-500">Lapor Masuk</div></div>
                            <div class="text-center p-2 bg-slate-50 rounded-lg"><div class="font-bold text-ink text-lg">2</div><div class="text-xs text-slate-500">Regu Aktif</div></div>
                        </div>
                    </div>
                    <button onclick="alert('Silakan login untuk melaporkan gangguan')" class="w-full py-2.5 bg-status-outage hover:bg-red-700 text-white font-semibold text-sm rounded-lg">Laporkan Gangguan</button>
                </div>
                <?php endif; ?>
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <h4 class="font-display font-semibold text-ink mb-3">Peta Sebaran Gangguan</h4>
                    <div class="aspect-square bg-brand-50 rounded-lg border border-brand-100 relative overflow-hidden">
                        <svg viewBox="0 0 200 200" class="w-full h-full">
                            <circle cx="60" cy="70" r="8" fill="#16A34A" opacity="0.8"/>
                            <circle cx="130" cy="50" r="8" fill="#16A34A" opacity="0.8"/>
                            <circle cx="100" cy="110" r="12" fill="#DC2626" opacity="0.8"/>
                            <circle cx="150" cy="140" r="8" fill="#16A34A" opacity="0.8"/>
                            <circle cx="50" cy="150" r="6" fill="#D97706" opacity="0.8"/>
                            <line x1="60" y1="70" x2="100" y2="110" stroke="#CBD5E1" stroke-width="1" stroke-dasharray="3"/>
                            <line x1="130" y1="50" x2="100" y2="110" stroke="#CBD5E1" stroke-width="1" stroke-dasharray="3"/>
                            <line x1="100" y1="110" x2="150" y2="140" stroke="#CBD5E1" stroke-width="1" stroke-dasharray="3"/>
                        </svg>
                        <div class="absolute bottom-2 left-2 right-2 bg-white/90 backdrop-blur rounded px-2 py-1 text-xs text-slate-600"><?php echo count($gangguan); ?> titik gangguan aktif</div>
                    </div>
                </div>
            </aside>
        </div>
        <div class="mt-8 bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100"><h2 class="font-display font-semibold text-lg text-ink">Jadwal Pemeliharaan Jaringan</h2><p class="text-xs text-slate-500 mt-1">Informasi pemeliharaan terencana yang mungkin mempengaruhi layanan Anda</p></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-6 py-3 font-semibold">Tanggal</th><th class="text-left px-6 py-3 font-semibold">Waktu</th><th class="text-left px-6 py-3 font-semibold">Wilayah</th><th class="text-left px-6 py-3 font-semibold">Jenis Pekerjaan</th><th class="text-left px-6 py-3 font-semibold">Status</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($maintenance as $j): ?>
                        <tr class="hover:bg-slate-50"><td class="px-6 py-4 font-medium text-ink"><?php echo htmlspecialchars($j['tanggal']); ?></td><td class="px-6 py-4 text-slate-600"><?php echo htmlspecialchars($j['waktu']); ?></td><td class="px-6 py-4 text-slate-600"><?php echo htmlspecialchars($j['wilayah']); ?></td><td class="px-6 py-4 text-slate-600"><?php echo htmlspecialchars($j['jenis_pekerjaan']); ?></td><td class="px-6 py-4"><span class="px-2 py-1 rounded-full bg-status-maintenance/10 text-status-maintenance text-xs font-semibold"><?php echo $j['status']; ?></span></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>
<?php include 'includes/footer.php'; ?>