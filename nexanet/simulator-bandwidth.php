<?php 
require_once 'config/functions.php';
require_once 'config/session.php';
$page_title = 'Simulator Bandwidth - NexaNet';
$current_page = 'simulator-bandwidth.php';

$paket_bandwidth = getBandwidthPackages($pdo);
$user = isLoggedIn() ? getCurrentUser($pdo) : null;
$pemakaian_7hari = $user ? getDataUsage($pdo, $user['id']) : [
    ['hari'=>'Sen','gb'=>4.2],['hari'=>'Sel','gb'=>5.8],['hari'=>'Rab','gb'=>3.1],
    ['hari'=>'Kam','gb'=>6.4],['hari'=>'Jum','gb'=>7.2],['hari'=>'Sab','gb'=>9.5],['hari'=>'Min','gb'=>8.1]
];
$riwayat_tagihan = $user ? getBillingHistory($pdo, $user['id']) : [
    ['bulan'=>'Oktober 2026','paket'=>'Home Business 100 Mbps','nominal'=>599000,'status'=>'Belum Tagih','tanggal'=>'01 Okt 2026'],
    ['bulan'=>'September 2026','paket'=>'Home Business 100 Mbps','nominal'=>599000,'status'=>'Lunas','tanggal'=>'05 Sep 2026'],
    ['bulan'=>'Agustus 2026','paket'=>'Home Business 100 Mbps','nominal'=>599000,'status'=>'Lunas','tanggal'=>'03 Agu 2026'],
    ['bulan'=>'Juli 2026','paket'=>'Home Starter 50 Mbps','nominal'=>399000,'status'=>'Lunas','tanggal'=>'02 Jul 2026']
];

include 'includes/header.php'; 
include 'includes/navbar.php'; 
?>
<main class="flex-1">
    <section class="bg-white border-b border-slate-200"><div class="max-w-container mx-auto px-4 sm:px-6 lg:px-8 py-8"><h1 class="font-display font-bold text-2xl lg:text-3xl text-ink">Kalkulator Kebutuhan Bandwidth dan Biaya Paket</h1><p class="text-sm text-slate-600 mt-1 max-w-2xl">Simulasikan kebutuhan bandwidth berdasarkan jumlah perangkat dan aktivitas.</p></div></section>
    <section class="max-w-container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="font-display font-semibold text-lg text-ink mb-6">Parameter Perkiraan Jaringan</h2>
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-3"><label class="text-sm font-medium text-ink">Jumlah Perangkat Terhubung</label><span id="deviceCount" class="px-3 py-1 rounded-full bg-brand-50 text-brand-700 text-sm font-semibold">5 perangkat</span></div>
                    <input type="range" id="deviceSlider" min="1" max="15" value="5" class="brand-slider w-full">
                    <div class="flex justify-between text-xs text-slate-500 mt-2"><span>1</span><span>5</span><span>10</span><span>15</span></div>
                </div>
                <div>
                    <label class="text-sm font-medium text-ink mb-3 block">Pilih Aktivitas yang Sering Dilakukan</label>
                    <div class="space-y-2.5">
                        <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50"><input type="checkbox" class="activity-check w-4 h-4 text-brand-600 rounded" data-mbps="5" checked><div class="flex-1"><div class="text-sm font-medium text-ink">Streaming Video HD (Netflix, YouTube)</div><div class="text-xs text-slate-500">+5 Mbps per perangkat</div></div></label>
                        <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50"><input type="checkbox" class="activity-check w-4 h-4 text-brand-600 rounded" data-mbps="3"><div class="flex-1"><div class="text-sm font-medium text-ink">Video Conference (Zoom, Teams)</div><div class="text-xs text-slate-500">+3 Mbps per perangkat</div></div></label>
                        <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50"><input type="checkbox" class="activity-check w-4 h-4 text-brand-600 rounded" data-mbps="10" checked><div class="flex-1"><div class="text-sm font-medium text-ink">Gaming Online</div><div class="text-xs text-slate-500">+10 Mbps per perangkat</div></div></label>
                        <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50"><input type="checkbox" class="activity-check w-4 h-4 text-brand-600 rounded" data-mbps="2"><div class="flex-1"><div class="text-sm font-medium text-ink">Browsing & Media Sosial</div><div class="text-xs text-slate-500">+2 Mbps per perangkat</div></div></label>
                        <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50"><input type="checkbox" class="activity-check w-4 h-4 text-brand-600 rounded" data-mbps="8"><div class="flex-1"><div class="text-sm font-medium text-ink">Upload/Download File Besar</div><div class="text-xs text-slate-500">+8 Mbps per perangkat</div></div></label>
                    </div>
                </div>
                <div class="mt-6 p-5 bg-brand-50 rounded-lg border border-brand-100">
                    <div class="grid grid-cols-2 gap-4">
                        <div><div class="text-xs text-slate-600 mb-1">Rekomendasi Kecepatan</div><div id="recMbps" class="font-display font-bold text-2xl text-brand-700">~103 Mbps</div></div>
                        <div><div class="text-xs text-slate-600 mb-1">Estimasi Biaya/Bulan</div><div id="recCost" class="font-display font-bold text-2xl text-brand-700">Rp25.750</div></div>
                    </div>
                </div>
            </div>
            <div class="space-y-4">
                <div class="bg-white rounded-xl border-2 border-brand-500 p-6 relative">
                    <span class="absolute -top-3 right-6 px-3 py-1 bg-brand-600 text-white text-xs font-semibold rounded-full">Rekomendasi Terbaik</span>
                    <h3 class="font-display font-bold text-lg text-ink">NexaNet Ultra Business Home</h3>
                    <div class="font-display font-extrabold text-3xl text-brand-600 my-2">100 Mbps</div>
                    <div class="flex items-baseline gap-1 mb-4"><span class="font-display font-bold text-2xl text-ink">Rp599.000</span><span class="text-sm text-slate-500">/bulan</span></div>
                    <ul class="space-y-2 text-sm text-slate-600 mb-5">
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-status-normal" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Unlimited kuota bulanan</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-status-normal" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Router WiFi 6 gratis</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-status-normal" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Support prioritas 24/7</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-status-normal" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Garansi uptime 99.5%</li>
                    </ul>
                    <button onclick="alert('Silakan login untuk berlangganan paket ini')" class="w-full py-3 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm rounded-lg transition-colors">Pilih Paket Ini</button>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <h4 class="font-display font-semibold text-ink mb-3">Pilihan Paket Lainnya</h4>
                    <div class="space-y-2">
                        <?php foreach ($paket_bandwidth as $p): ?>
                        <div class="flex items-center justify-between p-3 rounded-lg hover:bg-slate-50 transition-colors cursor-pointer border border-transparent hover:border-slate-200">
                            <div><div class="font-semibold text-ink text-sm"><?php echo htmlspecialchars($p['nama_paket']); ?></div><div class="text-xs text-slate-500"><?php echo htmlspecialchars($p['kecepatan']); ?></div></div>
                            <div class="text-right"><div class="font-semibold text-ink text-sm"><?php echo formatRupiah($p['harga']); ?></div><div class="text-xs text-slate-500">/bulan</div></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid lg:grid-cols-2 gap-6 mt-6">
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h3 class="font-display font-semibold text-ink mb-4">Pemakaian Data 7 Hari Terakhir</h3>
                <div class="flex items-end gap-3 h-48">
                    <?php foreach ($pemakaian_7hari as $d): $h = ($d['gb'] / 10) * 100; ?>
                    <div class="flex-1 flex flex-col items-center gap-2"><div class="w-full chart-bar bg-brand-500 rounded-t transition-all" data-height="<?php echo $h; ?>" style="height: 0%"></div><span class="text-xs text-slate-500"><?php echo $d['hari']; ?></span><span class="text-xs font-semibold text-ink"><?php echo $d['gb']; ?> GB</span></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100"><h3 class="font-display font-semibold text-ink">Riwayat Tagihan & Pembayaran</h3></div>
                <div class="divide-y divide-slate-100">
                    <?php foreach ($riwayat_tagihan as $r): ?>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div><div class="font-medium text-ink text-sm"><?php echo htmlspecialchars($r['bulan']); ?></div><div class="text-xs text-slate-500"><?php echo htmlspecialchars($r['paket']); ?></div></div>
                        <div class="text-right"><div class="font-semibold text-ink text-sm"><?php echo formatRupiah($r['nominal']); ?></div><span class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-semibold <?php echo $r['status'] === 'Lunas' ? 'bg-status-normal/10 text-status-normal' : 'bg-slate-100 text-slate-600'; ?>"><?php echo htmlspecialchars($r['status']); ?></span></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
</main>
<?php include 'includes/footer.php'; ?>