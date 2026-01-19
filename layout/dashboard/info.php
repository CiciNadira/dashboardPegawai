<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

    .stat-container {
        display: flex;
        flex-wrap: wrap; /* Ini kuncinya: Biar kartu turun ke bawah kalau sempit */
        gap: 16px;
        width: 100%;
        font-family: 'Inter', sans-serif;
    }

    .stat-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 24px 15px;
        /* Menggunakan flex-grow agar kartu mengisi ruang yang ada */
        flex: 1 1 200px; /* Tumbuh, Menyusut, dan Minimal lebar 200px */
        min-height: 180px; 
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        box-sizing: border-box; /* Biar padding tidak merusak ukuran */
    }

    .icon-box {
        font-size: 24px;
        margin-bottom: 8px;
        background: #f8fafc;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .value {
        /* Menggunakan font-size yang lebih aman agar tidak tumpah */
        font-size: 42px; 
        font-weight: 800;
        color: #1e293b;
        line-height: 1;
        margin: 5px 0;
        white-space: nowrap; /* Mencegah angka pecah ke bawah */
    }

    .label {
        font-size: 14px;
        font-weight: 600;
        color: #64748b;
        margin-top: 5px;
    }

    .sub-label {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 4px;
        text-transform: uppercase;
    }

    /* Khusus untuk tampilan mobile sangat kecil */
    @media (max-width: 480px) {
        .value {
            font-size: 32px;
        }
        .stat-card {
            min-height: 150px;
        }
    }
</style>

<div class="stat-container">
    <div class="stat-card clickable" onclick="location.href='<?= $base_url; ?>pages/kepegawaian/sdm.php'">
        <div class="icon-box">👥</div>
        <div class="value"><?= $total_pegawai; ?></div>
        <div class="label">Total Pegawai</div>
    </div>

    <div class="stat-card">
        <div class="icon-box">🕒</div>
        <div class="value" id="digitalClock">00:00</div>
        <div class="label">Waktu Sekarang</div>
        <div class="sub-label"><?= date('d M Y'); ?></div>
    </div>
</div>

<script>
    function updateClock() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        document.getElementById('digitalClock').innerText = h + ":" + m;
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>