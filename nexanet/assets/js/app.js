document.addEventListener('DOMContentLoaded', function () {
    // Mobile menu
    const mobileBtn = document.getElementById('mobileMenuBtn');
    const mobileDrawer = document.getElementById('mobileDrawer');
    const iconOpen = document.getElementById('iconOpen');
    const iconClose = document.getElementById('iconClose');
    if (mobileBtn && mobileDrawer) {
        mobileBtn.addEventListener('click', function () {
            const isOpen = !mobileDrawer.classList.contains('hidden');
            mobileDrawer.classList.toggle('hidden');
            iconOpen.classList.toggle('hidden', !isOpen);
            iconClose.classList.toggle('hidden', isOpen);
        });
    }
    document.querySelectorAll('#mobileDrawer a').forEach(function (link) {
        link.addEventListener('click', function () {
            if (mobileDrawer && !mobileDrawer.classList.contains('hidden')) {
                mobileDrawer.classList.add('hidden');
                iconOpen.classList.remove('hidden');
                iconClose.classList.add('hidden');
            }
        });
    });

    // Bandwidth simulator
    const slider = document.getElementById('deviceSlider');
    const deviceCount = document.getElementById('deviceCount');
    const recMbps = document.getElementById('recMbps');
    const recCost = document.getElementById('recCost');
    const activityList = document.querySelectorAll('.activity-check');
    function calculateBandwidth() {
        if (!slider) return;
        const devices = parseInt(slider.value, 10);
        const baseMbps = devices * 8;
        let extraMbps = 0;
        activityList.forEach(function (cb) { if (cb.checked) extraMbps += parseInt(cb.dataset.mbps, 10); });
        const totalMbps = baseMbps + extraMbps;
        const cost = totalMbps * 250;
        if (deviceCount) deviceCount.textContent = devices + ' perangkat';
        if (recMbps) recMbps.textContent = '~' + totalMbps + ' Mbps';
        if (recCost) recCost.textContent = 'Rp' + cost.toLocaleString('id-ID') + '/bulan';
    }
    if (slider) { slider.addEventListener('input', calculateBandwidth); calculateBandwidth(); }
    activityList.forEach(function (cb) { cb.addEventListener('change', calculateBandwidth); });

    // Wizard
    const wizardSteps = document.querySelectorAll('.wizard-step');
    const nextBtns = document.querySelectorAll('.wizard-next');
    const prevBtns = document.querySelectorAll('.wizard-prev');
    const stepIndicators = document.querySelectorAll('.step-indicator');
    let currentStep = 0;
    function showStep(index) {
        wizardSteps.forEach(function (step, i) { step.classList.toggle('hidden', i !== index); });
        stepIndicators.forEach(function (ind, i) {
            ind.className = 'step-indicator w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold ';
            if (i < index) ind.classList.add('bg-status-normal','text-white');
            else if (i === index) ind.classList.add('bg-brand-600','text-white');
            else ind.classList.add('bg-slate-200','text-slate-500');
        });
        currentStep = index;
    }
    nextBtns.forEach(function (btn) { btn.addEventListener('click', function () { if (currentStep < wizardSteps.length - 1) showStep(currentStep + 1); }); });
    prevBtns.forEach(function (btn) { btn.addEventListener('click', function () { if (currentStep > 0) showStep(currentStep - 1); }); });

    // Modal
    const modalTriggers = document.querySelectorAll('[data-modal-target]');
    const modalCloseBtns = document.querySelectorAll('[data-modal-close]');
    const modalOverlay = document.getElementById('speedBoostModal');
    modalTriggers.forEach(function (t) { t.addEventListener('click', function (e) { e.preventDefault(); if (modalOverlay) modalOverlay.classList.add('active'); }); });
    modalCloseBtns.forEach(function (b) { b.addEventListener('click', function () { if (modalOverlay) modalOverlay.classList.remove('active'); }); });
    if (modalOverlay) modalOverlay.addEventListener('click', function (e) { if (e.target === modalOverlay) modalOverlay.classList.remove('active'); });

    // Chart bars animation
    document.querySelectorAll('.chart-bar').forEach(function (bar) { setTimeout(function () { bar.style.height = bar.dataset.height + '%'; }, 100); });

    // Submit ticket form (diagnosis-mandiri step 3)
    const submitTicketBtns = document.querySelectorAll('button');
    submitTicketBtns.forEach(function(btn) {
        if (btn.textContent.includes('Kirim Tiket Otomatis')) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const kendala = document.querySelector('input[name="kendala"]:checked');
                const durasi = document.querySelector('input[name="durasi"]:checked');
                if (!kendala || !durasi) { alert('Pilih kendala dan durasi terlebih dahulu'); return; }
                fetch('api/submit_ticket.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({judul: 'Kendala: ' + kendala.value + ' (' + durasi.value + ')', kategori: 'Diagnosis Mandiri', prioritas: 'Sedang', deskripsi: 'Kendala: ' + kendala.value + ', Durasi: ' + durasi.value})
                }).then(r => r.json()).then(data => {
                    if (data.success) { alert('Tiket berhasil dibuat: ' + data.kode_tiket); window.location.href = 'status-tiket.php'; }
                    else { alert(data.message || 'Gagal membuat tiket. Pastikan Anda sudah login.'); }
                }).catch(() => alert('Terjadi kesalahan'));
            });
        }
    });

    // Addon toggle buttons
    document.querySelectorAll('.addon-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const addonId = this.dataset.addonId;
            const action = this.dataset.action;
            fetch('api/submit_addon.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({addon_id: addonId, action: action})
            }).then(r => r.json()).then(data => {
                if (data.success) { location.reload(); } else { alert(data.message); }
            }).catch(() => alert('Terjadi kesalahan'));
        });
    });

    // Speed boost modal form
    const speedBoostForm = document.querySelector('#speedBoostModal form');
    if (speedBoostForm) {
        speedBoostForm.addEventListener('submit', function (e) {
            e.preventDefault();
            alert('Pengajuan Speed Boost berhasil! Tim kami akan segera memproses.');
            if (modalOverlay) modalOverlay.classList.remove('active');
        });
    }

    // Search ticket
    const ticketSearch = document.getElementById('ticketSearch');
    if (ticketSearch) {
        ticketSearch.addEventListener('keypress', function (e) {
            if (e.key === 'Enter' && this.value.trim().length >= 3) {
                window.location.href = 'detail-tiket.php?kode=' + encodeURIComponent(this.value.trim());
            }
        });
    }
});