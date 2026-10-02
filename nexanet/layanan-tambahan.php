<?php 
require_once 'config/functions.php';
require_once 'config/session.php';
$page_title = 'Layanan Tambahan - NexaNet';
$current_page = 'layanan-tambahan.php';

$addons = getAddons($pdo);
$user = isLoggedIn() ? getCurrentUser($pdo) : null;
$myAddons = $user ? getUserAddons($pdo, $user['id']) : [];
$myAddonIds = array_column($myAddons, 'addon_id');

include 'includes/header.php'; 
include 'includes/navbar.php'; 
?>
<main class="flex-1">
    <?php renderFlash(); ?>
    <section class="bg-white border-b border-slate-200">
        <div class="max-w-container mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="font-display font-bold text-2xl lg:text-3xl text-ink">Layanan Tambahan & Fitur Mandiri</h1>
            <p class="text-sm text-slate-600 mt-1">Kelola layanan tambahan Anda dengan mudah. Aktifkan, nonaktifkan, atau ajukan permintaan baru.</p>
        </div>
    </section>
    <section class="max-w-container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid md:grid-cols-3 gap-5 mb-8">
            <div class="bg-white rounded-xl border border-slate-200 p-6 card-hover">
                <div class="w-12 h-12 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center mb-4"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg></div>
                <h3 class="font-display font-semibold text-ink mb-2">Speed Boost 24 Jam</h3>
                <p class="text-sm text-slate-600 mb-4 leading-relaxed">Tingkatkan kecepatan internet hingga 2x lipat selama 24 jam untuk kebutuhan mendesak.</p>
                <div class="flex items-center justify-between"><span class="font-display font-bold text-lg text-ink">Rp25.000</span><button data-modal-target="speedBoostModal" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm rounded-lg transition-colors">Ajukan</button></div>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-6 card-hover">
                <div class="w-12 h-12 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></div>
                <h3 class="font-display font-semibold text-ink mb-2">Ganti Password WiFi</h3>
                <p class="text-sm text-slate-600 mb-4 leading-relaxed">Ubah password WiFi Anda secara mandiri tanpa perlu menunggu kunjungan teknisi.</p>
                <div class="flex items-center justify-between"><span class="font-display font-bold text-lg text-status-normal">Gratis</span><button onclick="alert('Fitur ganti password WiFi akan segera tersedia. Silakan hubungi CS.')" class="px-4 py-2 border border-brand-600 text-brand-600 hover:bg-brand-50 font-semibold text-sm rounded-lg transition-colors">Ubah Sekarang</button></div>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-6 card-hover">
                <div class="w-12 h-12 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center mb-4"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg></div>
                <h3 class="font-display font-semibold text-ink mb-2">Pindah Router</h3>
                <p class="text-sm text-slate-600 mb-4 leading-relaxed">Layanan relokasi router ke posisi baru dalam rumah Anda oleh teknisi profesional.</p>
                <div class="flex items-center justify-between"><span class="font-display font-bold text-lg text-ink">Rp150.000</span><button onclick="alert('Silakan login untuk menjadwalkan pindah router')" class="px-4 py-2 border border-brand-600 text-brand-600 hover:bg-brand-50 font-semibold text-sm rounded-lg transition-colors">Jadwalkan</button></div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between"><div><h2 class="font-display font-semibold text-lg text-ink">Daftar Layanan Tambahan</h2><p class="text-xs text-slate-500 mt-0.5">Kelola semua layanan tambahan yang tersedia</p></div></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600"><tr><th class="text-left px-6 py-3 font-semibold">ID Layanan</th><th class="text-left px-6 py-3 font-semibold">Nama Layanan</th><th class="text-left px-6 py-3 font-semibold">Kategori</th><th class="text-left px-6 py-3 font-semibold">Biaya/Bulan</th><th class="text-left px-6 py-3 font-semibold">Status</th><th class="text-left px-6 py-3 font-semibold">Aksi</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($addons as $a): 
                            $isActivated = in_array($a['id'], $myAddonIds);
                        ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-mono text-xs text-slate-600"><?php echo htmlspecialchars($a['kode_addon']); ?></td>
                            <td class="px-6 py-4"><div class="font-medium text-ink"><?php echo htmlspecialchars($a['nama']); ?></div><div class="text-xs text-slate-500 mt-0.5 max-w-xs"><?php echo htmlspecialchars($a['deskripsi']); ?></div></td>
                            <td class="px-6 py-4"><span class="px-2 py-1 rounded bg-slate-100 text-slate-700 text-xs font-medium"><?php echo htmlspecialchars($a['kategori']); ?></span></td>
                            <td class="px-6 py-4 font-semibold text-ink"><?php echo $a['harga'] === 0 ? 'Gratis' : formatRupiah($a['harga']); ?></td>
                            <td class="px-6 py-4"><span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold <?php echo $isActivated ? 'bg-status-normal/10 text-status-normal' : 'bg-slate-100 text-slate-500'; ?>"><span class="w-1.5 h-1.5 rounded-full <?php echo $isActivated ? 'bg-status-normal' : 'bg-slate-400'; ?>"></span><?php echo $isActivated ? 'Aktif' : 'Nonaktif'; ?></span></td>
                            <td class="px-6 py-4">
                                <?php if ($user): ?>
                                    <button class="addon-toggle text-xs font-semibold <?php echo $isActivated ? 'text-status-outage hover:underline' : 'text-brand-600 hover:underline'; ?>" data-addon-id="<?php echo $a['id']; ?>" data-action="<?php echo $isActivated ? 'deactivate' : 'activate'; ?>"><?php echo $isActivated ? 'Nonaktifkan' : 'Aktifkan'; ?></button>
                                <?php else: ?>
                                    <a href="login.php?redirect=layanan-tambahan.php" class="text-xs font-semibold text-brand-600 hover:underline">Login</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>

<!-- Modal Speed Boost -->
<div id="speedBoostModal" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
    <div class="modal-content bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div><h3 class="font-display font-bold text-lg text-ink">Pengajuan Speed Boost 24 Jam</h3><p class="text-xs text-slate-500 mt-0.5">Tingkatkan kecepatan hingga 2x lipat</p></div>
            <button data-modal-close class="p-1.5 rounded-md hover:bg-slate-100 text-slate-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form class="p-6 space-y-4">
            <div><label class="text-sm font-medium text-ink mb-1.5 block">Paket Saat Ini</label><input type="text" value="Home Business - 100 Mbps" readonly class="w-full px-3 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-600"></div>
            <div><label class="text-sm font-medium text-ink mb-1.5 block">Kecepatan Boost</label><select class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none"><option>200 Mbps (2x lipat) - Rp25.000</option><option>300 Mbps (3x lipat) - Rp40.000</option><option>500 Mbps (5x lipat) - Rp65.000</option></select></div>
            <div><label class="text-sm font-medium text-ink mb-1.5 block">Durasi Aktif</label><select class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none"><option>24 Jam</option><option>12 Jam</option><option>6 Jam</option></select></div>
            <div><label class="text-sm font-medium text-ink mb-1.5 block">Alasan Pengajuan (Opsional)</label><textarea rows="3" placeholder="Contoh: Meeting penting, upload file besar..." class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none resize-none"></textarea></div>
            <div class="bg-brand-50 border border-brand-100 rounded-lg p-4 text-sm">
                <div class="flex justify-between mb-1"><span class="text-slate-600">Biaya Layanan</span><span class="font-semibold text-ink">Rp25.000</span></div>
                <div class="flex justify-between mb-1"><span class="text-slate-600">PPN 11%</span><span class="font-semibold text-ink">Rp2.750</span></div>
                <div class="border-t border-brand-200 my-2"></div>
                <div class="flex justify-between"><span class="font-semibold text-ink">Total</span><span class="font-display font-bold text-brand-700">Rp27.750</span></div>
            </div>
            <div class="flex gap-3 pt-2"><button type="button" data-modal-close class="flex-1 px-4 py-2.5 border border-slate-300 text-slate-700 font-semibold text-sm rounded-lg hover:bg-slate-50 transition-colors">Batal</button><button type="submit" class="flex-1 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm rounded-lg transition-colors">Konfirmasi & Bayar</button></div>
        </form>
    </div>
</div>
<?php include 'includes/footer.php'; ?>