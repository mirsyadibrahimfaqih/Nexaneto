<?php 
require_once 'config/functions.php';
require_once 'config/session.php';
$page_title = 'Diagnosis Mandiri - NexaNet';
$current_page = 'diagnosis-mandiri.php';
include 'includes/header.php'; 
include 'includes/navbar.php'; 
?>
<main class="flex-1">
    <section class="bg-white border-b border-slate-200"><div class="max-w-container mx-auto px-4 sm:px-6 lg:px-8 py-8"><h1 class="font-display font-bold text-2xl lg:text-3xl text-ink">Bantuan Mandiri dan Diagnosis Kendala</h1><p class="text-sm text-slate-600 mt-1 max-w-2xl">Selesaikan masalah koneksi Anda sendiri dengan panduan langkah demi langkah.</p></div></section>
    <section class="max-w-container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl border border-slate-200 p-4"><div class="text-xs text-slate-500 mb-1">Tingkat Keberhasilan</div><div class="font-display font-bold text-2xl text-ink">99.4%</div></div>
            <div class="bg-white rounded-xl border border-slate-200 p-4"><div class="text-xs text-slate-500 mb-1">Rata-rata Waktu Selesai</div><div class="font-display font-bold text-2xl text-ink">3.2 <span class="text-sm font-normal text-slate-500">menit</span></div></div>
            <div class="bg-white rounded-xl border border-slate-200 p-4"><div class="text-xs text-slate-500 mb-1">Tiket Terhindari</div><div class="font-display font-bold text-2xl text-ink">1,248</div></div>
            <div class="bg-white rounded-xl border border-slate-200 p-4"><div class="text-xs text-slate-500 mb-1">Kepuasan Pengguna</div><div class="font-display font-bold text-2xl text-ink">4.8<span class="text-sm text-amber-500">/5</span></div></div>
        </div>
        <div class="grid lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1 space-y-4">
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <h3 class="font-display font-semibold text-ink mb-3 flex items-center gap-2"><svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Panduan Restart Router</h3>
                    <ol class="space-y-3 text-sm text-slate-600">
                        <li class="flex gap-3"><span class="shrink-0 w-6 h-6 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center font-semibold text-xs">1</span><span>Matikan router dengan menekan tombol power atau cabut adaptor listrik.</span></li>
                        <li class="flex gap-3"><span class="shrink-0 w-6 h-6 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center font-semibold text-xs">2</span><span>Tunggu selama 30 detik hingga semua lampu padam sepenuhnya.</span></li>
                        <li class="flex gap-3"><span class="shrink-0 w-6 h-6 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center font-semibold text-xs">3</span><span>Nyalakan kembali router dan tunggu 2-3 menit hingga lampu PON stabil hijau.</span></li>
                        <li class="flex gap-3"><span class="shrink-0 w-6 h-6 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center font-semibold text-xs">4</span><span>Uji koneksi dengan membuka halaman web atau streaming video.</span></li>
                    </ol>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <h3 class="font-display font-semibold text-ink mb-3">Penjelasan Lampu Indikator</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-start gap-3"><span class="shrink-0 w-3 h-3 rounded-full bg-status-normal mt-1"></span><div><div class="font-semibold text-ink">PON Hijau Stabil</div><div class="text-slate-600">Koneksi fiber optik normal dan terhubung ke server.</div></div></div>
                        <div class="flex items-start gap-3"><span class="shrink-0 w-3 h-3 rounded-full bg-status-outage mt-1"></span><div><div class="font-semibold text-ink">LOS Merah Berkedip</div><div class="text-slate-600">Sinyal fiber terputus. Periksa kabel atau hubungi teknisi.</div></div></div>
                        <div class="flex items-start gap-3"><span class="shrink-0 w-3 h-3 rounded-full bg-status-maintenance mt-1"></span><div><div class="font-semibold text-ink">LAN Amber</div><div class="text-slate-600">Perangkat terhubung namun tidak ada aktivitas data.</div></div></div>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl border border-slate-200 p-6 lg:p-8">
                    <h2 class="font-display font-bold text-xl text-ink mb-6">Troubleshooting Wizard 3 Langkah</h2>
                    <div class="flex items-center gap-3 mb-8">
                        <div class="step-indicator w-8 h-8 rounded-full bg-brand-600 text-white flex items-center justify-center text-sm font-semibold">1</div><div class="flex-1 h-0.5 bg-slate-200"></div>
                        <div class="step-indicator w-8 h-8 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-sm font-semibold">2</div><div class="flex-1 h-0.5 bg-slate-200"></div>
                        <div class="step-indicator w-8 h-8 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-sm font-semibold">3</div>
                    </div>
                    <div class="wizard-step">
                        <h3 class="font-display font-semibold text-lg text-ink mb-4">Langkah 1: Pilih Jenis Kendala</h3>
                        <div class="grid sm:grid-cols-2 gap-3 mb-6">
                            <label class="flex items-start gap-3 p-4 border border-slate-200 rounded-lg cursor-pointer hover:border-brand-500 hover:bg-brand-50 transition-colors has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50"><input type="radio" name="kendala" value="no_internet" class="mt-1" checked><div><div class="font-semibold text-ink text-sm">Tidak Ada Koneksi Internet</div><div class="text-xs text-slate-600 mt-1">Sama sekali tidak bisa mengakses internet.</div></div></label>
                            <label class="flex items-start gap-3 p-4 border border-slate-200 rounded-lg cursor-pointer hover:border-brand-500 hover:bg-brand-50 transition-colors has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50"><input type="radio" name="kendala" value="lambat" class="mt-1"><div><div class="font-semibold text-ink text-sm">Koneksi Lambat / Latensi Tinggi</div><div class="text-xs text-slate-600 mt-1">Internet tersambung namun sangat lambat.</div></div></label>
                            <label class="flex items-start gap-3 p-4 border border-slate-200 rounded-lg cursor-pointer hover:border-brand-500 hover:bg-brand-50 transition-colors has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50"><input type="radio" name="kendala" value="intermittent" class="mt-1"><div><div class="font-semibold text-ink text-sm">Koneksi Terputus-putus</div><div class="text-xs text-slate-600 mt-1">Internet sering disconnect dan reconnect sendiri.</div></div></label>
                            <label class="flex items-start gap-3 p-4 border border-slate-200 rounded-lg cursor-pointer hover:border-brand-500 hover:bg-brand-50 transition-colors has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50"><input type="radio" name="kendala" value="wifi" class="mt-1"><div><div class="font-semibold text-ink text-sm">WiFi Tidak Terdeteksi</div><div class="text-xs text-slate-600 mt-1">Nama WiFi tidak muncul di daftar perangkat.</div></div></label>
                        </div>
                        <button class="wizard-next px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm rounded-lg">Lanjutkan ke Langkah 2 &rarr;</button>
                    </div>
                    <div class="wizard-step hidden">
                        <h3 class="font-display font-semibold text-lg text-ink mb-4">Langkah 2: Pilih Durasi Kendala</h3>
                        <div class="grid sm:grid-cols-2 gap-3 mb-6">
                            <label class="flex items-center gap-3 p-4 border border-slate-200 rounded-lg cursor-pointer hover:border-brand-500 hover:bg-brand-50 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50"><input type="radio" name="durasi" value="baru" class="mt-1" checked><span class="text-sm font-medium text-ink">Baru terjadi (kurang dari 1 jam)</span></label>
                            <label class="flex items-center gap-3 p-4 border border-slate-200 rounded-lg cursor-pointer hover:border-brand-500 hover:bg-brand-50 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50"><input type="radio" name="durasi" value="beberapa_jam" class="mt-1"><span class="text-sm font-medium text-ink">Beberapa jam (1-6 jam)</span></label>
                            <label class="flex items-center gap-3 p-4 border border-slate-200 rounded-lg cursor-pointer hover:border-brand-500 hover:bg-brand-50 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50"><input type="radio" name="durasi" value="sehari" class="mt-1"><span class="text-sm font-medium text-ink">Lebih dari 1 hari</span></label>
                            <label class="flex items-center gap-3 p-4 border border-slate-200 rounded-lg cursor-pointer hover:border-brand-500 hover:bg-brand-50 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50"><input type="radio" name="durasi" value="rutin" class="mt-1"><span class="text-sm font-medium text-ink">Terjadi secara rutin/periodik</span></label>
                        </div>
                        <div class="flex gap-3"><button class="wizard-prev px-6 py-2.5 border border-slate-300 text-slate-700 font-semibold text-sm rounded-lg hover:bg-slate-50">&larr; Kembali</button><button class="wizard-next px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm rounded-lg">Lanjutkan ke Langkah 3 &rarr;</button></div>
                    </div>
                    <div class="wizard-step hidden">
                        <h3 class="font-display font-semibold text-lg text-ink mb-4">Langkah 3: Draf Tiket Otomatis</h3>
                        <div class="bg-slate-50 rounded-lg p-5 mb-6 border border-slate-200">
                            <div class="text-xs font-semibold text-slate-500 uppercase mb-3">Ringkasan Laporan</div>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between"><span class="text-slate-600">Jenis Kendala:</span><span class="font-semibold text-ink">Tidak Ada Koneksi Internet</span></div>
                                <div class="flex justify-between"><span class="text-slate-600">Durasi:</span><span class="font-semibold text-ink">Baru terjadi (&lt; 1 jam)</span></div>
                                <div class="flex justify-between"><span class="text-slate-600">Wilayah:</span><span class="font-semibold text-ink">Jakarta Selatan</span></div>
                                <div class="flex justify-between"><span class="text-slate-600">Rekomendasi:</span><span class="font-semibold text-brand-600">Restart router terlebih dahulu</span></div>
                            </div>
                        </div>
                        <div class="bg-brand-50 border border-brand-100 rounded-lg p-4 mb-6"><p class="text-sm text-brand-800"><strong>Saran:</strong> Berdasarkan gejala yang Anda pilih, silakan coba restart router terlebih dahulu. Jika masalah tetap berlanjut setelah 10 menit, tiket akan otomatis dibuat ke tim teknisi kami.</p></div>
                        <div class="flex gap-3"><button class="wizard-prev px-6 py-2.5 border border-slate-300 text-slate-700 font-semibold text-sm rounded-lg hover:bg-slate-50">&larr; Kembali</button><button class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm rounded-lg">Kirim Tiket Otomatis</button></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php include 'includes/footer.php'; ?>