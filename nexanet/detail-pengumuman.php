<?php
require_once 'config/functions.php';
require_once 'config/session.php';

$slug = sanitize($_GET['slug'] ?? '');
if (!$slug) { header('Location: index.php'); exit; }

$announcement = getAnnouncementBySlug($pdo, $slug);
if (!$announcement) { header('Location: index.php'); exit; }

$page_title = htmlspecialchars($announcement['judul']) . ' - NexaNet';
$current_page = 'detail-pengumuman.php';

$stmt = $pdo->prepare("SELECT * FROM announcements WHERE aktif = TRUE AND id != ? ORDER BY tanggal DESC LIMIT 3");
$stmt->execute([$announcement['id']]);
$related = $stmt->fetchAll();

include 'includes/header.php';
include 'includes/navbar.php';
?>
<main class="flex-1">
    <div class="bg-white border-b border-slate-200">
        <div class="max-w-container mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <nav class="text-sm text-slate-600 mb-2" aria-label="Breadcrumb">
                <ol class="flex items-center gap-2 flex-wrap">
                    <li><a href="index.php" class="hover:text-brand-600">Beranda</a></li>
                    <li><span class="text-slate-400">/</span></li>
                    <li><a href="index.php" class="hover:text-brand-600">Pengumuman</a></li>
                    <li><span class="text-slate-400">/</span></li>
                    <li class="text-ink font-medium truncate max-w-xs"><?php echo htmlspecialchars($announcement['judul']); ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="max-w-container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid lg:grid-cols-3 gap-8">
            <article class="lg:col-span-2">
                <div class="bg-white rounded-xl border border-slate-200 p-6 lg:p-8">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="px-2.5 py-1 rounded bg-brand-50 text-brand-700 text-xs font-semibold"><?php echo htmlspecialchars($announcement['kategori']); ?></span>
                        <span class="text-xs text-slate-500"><?php echo formatDate($announcement['tanggal']); ?></span>
                    </div>
                    <h1 class="font-display font-bold text-2xl lg:text-3xl text-ink mb-6 leading-tight"><?php echo htmlspecialchars($announcement['judul']); ?></h1>
                    <div class="prose prose-slate max-w-none text-slate-700 leading-relaxed space-y-4">
                        <?php echo $announcement['konten']; ?>
                    </div>
                    <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-between">
                        <div class="text-xs text-slate-500">Dilihat <?php echo $announcement['views']; ?> kali</div>
                        <a href="index.php" class="text-sm font-semibold text-brand-600 hover:text-brand-700">&larr; Kembali ke Beranda</a>
                    </div>
                </div>
            </article>

            <aside class="space-y-4">
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <h3 class="font-display font-semibold text-ink mb-4">Pengumuman Lainnya</h3>
                    <div class="space-y-3">
                        <?php foreach ($related as $r): ?>
                        <a href="detail-pengumuman.php?slug=<?php echo urlencode($r['slug']); ?>" class="block p-3 rounded-lg hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-200">
                            <div class="text-xs text-brand-600 font-semibold mb-1"><?php echo htmlspecialchars($r['kategori']); ?></div>
                            <div class="text-sm font-medium text-ink line-clamp-2"><?php echo htmlspecialchars($r['judul']); ?></div>
                            <div class="text-xs text-slate-500 mt-1"><?php echo formatDate($r['tanggal']); ?></div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="bg-brand-50 rounded-xl border border-brand-100 p-5">
                    <h3 class="font-display font-semibold text-brand-800 mb-2">Butuh Bantuan?</h3>
                    <p class="text-sm text-brand-700 mb-4">Jika ada pertanyaan terkait pengumuman ini, silakan hubungi tim support kami.</p>
                    <a href="status-tiket.php" class="block w-full py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm rounded-lg text-center transition-colors">Hubungi Support</a>
                </div>
            </aside>
        </div>
    </section>
</main>
<?php include 'includes/footer.php'; ?>