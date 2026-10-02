<?php
require_once 'config/functions.php';
require_once 'config/session.php';
requireLogin();

$kode = sanitize($_GET['kode'] ?? '');
if (!$kode) { header('Location: status-tiket.php'); exit; }

$ticket = getTicketByCode($pdo, $kode);
if (!$ticket || $ticket['user_id'] != $_SESSION['user_id']) { 
    setFlash('error', 'Tiket tidak ditemukan atau bukan milik Anda');
    header('Location: status-tiket.php'); 
    exit; 
}

$page_title = 'Detail Tiket #' . $ticket['kode_tiket'] . ' - NexaNet';
$current_page = 'detail-tiket.php';

include 'includes/header.php';
include 'includes/navbar.php';
?>
<main class="flex-1">
    <?php renderFlash(); ?>
    <div class="bg-white border-b border-slate-200">
        <div class="max-w-container mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <nav class="text-sm text-slate-600 mb-2">
                <ol class="flex items-center gap-2 flex-wrap"><li><a href="index.php" class="hover:text-brand-600">Beranda</a></li><li><span class="text-slate-400">/</span></li><li><a href="status-tiket.php" class="hover:text-brand-600">Status Tiket</a></li><li><span class="text-slate-400">/</span></li><li class="text-ink font-medium truncate max-w-xs">#<?php echo $ticket['kode_tiket']; ?></li></ol>
            </nav>
        </div>
    </div>

    <section class="max-w-container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-6">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h1 class="font-display font-bold text-xl text-ink mb-1">Tiket #<?php echo $ticket['kode_tiket']; ?></h1>
                        <p class="text-sm text-slate-600"><?php echo htmlspecialchars($ticket['judul']); ?></p>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-status-maintenance/10 text-status-maintenance text-xs font-semibold shrink-0"><?php echo $ticket['status']; ?></span>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6 p-4 bg-slate-50 rounded-lg">
                    <div><div class="text-xs text-slate-500">Kategori</div><div class="font-semibold text-ink text-sm"><?php echo htmlspecialchars($ticket['kategori']); ?></div></div>
                    <div><div class="text-xs text-slate-500">Prioritas</div><div class="font-semibold text-sm <?php echo $ticket['prioritas'] === 'Tinggi' ? 'text-status-outage' : 'text-ink'; ?>"><?php echo $ticket['prioritas']; ?></div></div>
                    <div><div class="text-xs text-slate-500">Dilaporkan</div><div class="font-semibold text-ink text-sm"><?php echo timeAgo($ticket['tanggal_lapor']); ?></div></div>
                    <div><div class="text-xs text-slate-500">Progress</div><div class="font-semibold text-brand-600 text-sm"><?php echo $ticket['progress']; ?>%</div></div>
                </div>

                <?php if ($ticket['deskripsi']): ?>
                <div class="mb-6">
                    <h3 class="font-display font-semibold text-ink mb-2">Deskripsi Kendala</h3>
                    <p class="text-sm text-slate-600 leading-relaxed bg-slate-50 p-4 rounded-lg"><?php echo nl2br(htmlspecialchars($ticket['deskripsi'])); ?></p>
                </div>
                <?php endif; ?>

                <h3 class="font-display font-semibold text-ink mb-4">Progress Perbaikan</h3>
                <div class="space-y-0">
                    <?php foreach ($ticket['steps'] as $i => $step):
                        $isDone = ($step['status'] === 'selesai'); $isActive = ($step['status'] === 'aktif');
                    ?>
                    <div class="stepper-item relative pl-12 pb-6">
                        <div class="stepper-line"></div>
                        <div class="absolute left-0 top-0 w-8 h-8 rounded-full flex items-center justify-center <?php echo $isDone ? 'bg-status-normal text-white' : ($isActive ? 'bg-brand-600 text-white ring-4 ring-brand-100' : 'bg-slate-200 text-slate-500'); ?>">
                            <?php if ($isDone): ?><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <?php elseif ($isActive): ?><span class="w-2.5 h-2.5 rounded-full bg-white animate-pulse"></span>
                            <?php else: ?><span class="text-xs font-semibold"><?php echo $i + 1; ?></span><?php endif; ?>
                        </div>
                        <div><div class="font-semibold text-ink text-sm"><?php echo htmlspecialchars($step['label']); ?></div><div class="text-xs text-slate-500 mt-0.5"><?php echo htmlspecialchars($step['waktu']); ?></div>
                            <?php if ($isActive): ?><div class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-brand-50 text-brand-700 text-xs font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-brand-600 animate-pulse"></span> Sedang berlangsung</div><?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-2"><span class="text-sm font-medium text-ink">Progress Keseluruhan</span><span class="text-sm font-bold text-brand-600"><?php echo $ticket['progress']; ?>%</span></div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-brand-600 rounded-full" style="width: <?php echo $ticket['progress']; ?>%"></div></div>
                </div>
            </div>

            <aside class="space-y-4">
                <?php if ($ticket['teknisi_nama']): ?>
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <h3 class="font-display font-semibold text-ink mb-4">Teknisi Penanggung Jawab</h3>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-14 h-14 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-lg"><?php echo strtoupper(substr($ticket['teknisi_nama'], 0, 1)); ?></div>
                        <div><div class="font-semibold text-ink"><?php echo htmlspecialchars($ticket['teknisi_nama']); ?></div><div class="text-xs text-slate-500"><?php echo htmlspecialchars($ticket['kode_teknisi']); ?></div>
                            <div class="flex items-center gap-1 mt-1"><svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.293z"/></svg><span class="text-xs font-semibold text-ink"><?php echo $ticket['rating']; ?></span><span class="text-xs text-slate-500">(<?php echo $ticket['jumlah_ulasan']; ?> ulasan)</span></div>
                        </div>
                    </div>
                    <?php if ($ticket['eta_tiba']): ?><div class="text-sm mb-3"><span class="text-slate-600">ETA Tiba:</span> <span class="font-semibold text-ink"><?php echo $ticket['eta_tiba']; ?></span></div><?php endif; ?>
                    <button onclick="alert('Fitur chat dengan teknisi akan segera hadir. Untuk saat ini silakan hubungi CS.')" class="w-full py-2 border border-brand-600 text-brand-600 hover:bg-brand-50 font-semibold text-sm rounded-lg transition-colors">Hubungi Teknisi</button>
                </div>
                <?php endif; ?>

                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <h3 class="font-display font-semibold text-ink mb-3">Aksi Cepat</h3>
                    <div class="space-y-2">
                        <button onclick="window.print()" class="w-full py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold text-sm rounded-lg transition-colors">Cetak Laporan</button>
                        <button onclick="navigator.clipboard.writeText('<?php echo $ticket['kode_tiket']; ?>').then(() => alert('Kode tiket disalin!'))" class="w-full py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold text-sm rounded-lg transition-colors">Salin Kode Tiket</button>
                        <?php if ($ticket['status'] !== 'Selesai'): ?>
                        <button onclick="if(confirm('Yakin ingin membatalkan tiket ini? Tindakan ini tidak dapat dibatalkan.')){alert('Permintaan pembatalan telah dikirim ke admin.')}" class="w-full py-2 border border-status-outage text-status-outage hover:bg-status-outage/5 font-semibold text-sm rounded-lg transition-colors">Batalkan Tiket</button>
                        <?php endif; ?>
                    </div>
                </div>
            </aside>
        </div>
    </section>
</main>
<?php include 'includes/footer.php'; ?>