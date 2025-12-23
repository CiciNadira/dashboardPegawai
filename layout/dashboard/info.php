<div class="stat-mini-grid">
    <div class="stat-mini" onclick="location.href='<?= $base_url; ?>pages/kepegawaian/sdm.php'" style="cursor:pointer;">
        <div class="sm-icon" style="background:#eff6ff; color:#2563eb;">👥</div>
        <div>
            <div class="sm-val"><?= $total_pegawai; ?></div>
            <div class="sm-label">Total Pegawai</div>
        </div>
    </div>
    <div class="stat-mini">
        <div class="sm-icon" style="background:#f0fdf4; color:#166534;">✅</div>
        <div>
            <div class="sm-val"><?= $total_pns; ?></div>
            <div class="sm-label">Pegawai PNS</div>
        </div>
    </div>
    <div class="stat-mini">
        <div class="sm-icon" style="background:#fff7ed; color:#c2410c;">📋</div>
        <div>
            <div class="sm-val"><?= $total_pppk; ?></div>
            <div class="sm-label">Pegawai PPPK</div>
        </div>
    </div>
    
    <div class="stat-mini" style="background: #1f2937; color: white; border:none;">
        <div class="sm-icon" style="background:rgba(255,255,255,0.1); color:#fff;">🕒</div>
        <div>
            <div class="sm-val" id="digitalClock">--:--</div>
            <div class="sm-label" style="color:#9ca3af;"><?= date('d M Y'); ?></div>
        </div>
    </div>
</div>

<script>
    // Script Jam Digital Simple
    function updateClock() {
        const now = new Date();
        document.getElementById('digitalClock').innerText = 
            String(now.getHours()).padStart(2, '0') + ":" + 
            String(now.getMinutes()).padStart(2, '0');
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>