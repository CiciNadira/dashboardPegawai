<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: index.php");
    exit;
}
require 'config/functions.php';

// === 1. DATA USER LOGIN & TIM (SAMA SEPERTI SEBELUMNYA) ===
$username = $_SESSION["user"];
$user_data = query("SELECT * FROM users WHERE username = '$username'")[0];
$nama_user    = $user_data['nama_lengkap'] ?? 'Administrator';
$nip_user     = $user_data['nip'] ?? '-'; 
$jabatan_user = $user_data['jabatan'] ?? 'Unit Pengelola Administrasi';

// Tim Kanan
$tim_kanan = query("SELECT * FROM pegawai WHERE jabatan_dashboard IN ('Sekretaris', 'Bendahara', 'Staf') 
                    ORDER BY FIELD(jabatan_dashboard, 'Sekretaris', 'Bendahara', 'Staf') ASC");

// === 2. REMINDER SYSTEM (PERINGATAN DINI) ===
// Mencari yang jatuh tempo 2 bulan ke depan (60 hari)
$today = date('Y-m-d');
$reminder_kgb = query("SELECT p.nama_lengkap, k.kgb_yad FROM data_kgb k JOIN pegawai p ON k.pegawai_id = p.id 
                       WHERE k.kgb_yad BETWEEN '$today' AND DATE_ADD('$today', INTERVAL 60 DAY) LIMIT 3");
$reminder_kp  = query("SELECT p.nama_lengkap, k.kp_yad FROM data_kp k JOIN pegawai p ON k.pegawai_id = p.id 
                       WHERE k.kp_yad BETWEEN '$today' AND DATE_ADD('$today', INTERVAL 60 DAY) LIMIT 3");

// === 3. STATISTIK SIMPLE ===
$total_pegawai = count(query("SELECT id FROM pegawai"));
$total_pns     = count(query("SELECT id FROM pegawai WHERE status_kepegawaian = 'PNS'"));
$total_pppk    = count(query("SELECT id FROM pegawai WHERE status_kepegawaian = 'PPPK'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Pegawai BPS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base_url; ?>assets/css/kepegawaian-sdm.css">
    <link rel="stylesheet" href="<?= $base_url; ?>assets/css/dashboard.css">
    <style>
        /* Layout Utama */
        .dashboard-hero { display: flex; gap: 20px; margin-bottom: 30px; align-items: stretch; }
        .hero-left { flex: 0 0 350px; } 
        .hero-right { flex: 1; }

        /* Tim List Style */
        .team-list-card { background: #fff; border-radius: 20px; border: 1px solid #e5e7eb; box-shadow: 0 10px 25px rgba(0,0,0,0.05); height: 100%; display: flex; flex-direction: column; overflow: hidden; }
        .team-header { padding: 15px 20px; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #374151; display: flex; align-items: center; gap: 8px; }
        .team-scroll-area { padding: 10px 20px; overflow-y: auto; flex: 1; }
        .team-item { display: flex; align-items: center; gap: 12px; padding: 12px 0; border-bottom: 1px solid #f3f4f6; }
        .team-item:last-child { border-bottom: none; }
        .t-avatar { width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 16px; flex-shrink: 0; }
        .t-role { font-size: 10px; font-weight: 700; text-transform: uppercase; padding: 2px 6px; border-radius: 4px; display: inline-block; margin-bottom: 2px; }
        .t-name { font-size: 14px; font-weight: 600; color: #111; }
        .t-nip { font-size: 11px; color: #6b7280; }

        /* Warna Role */
        .role-Sekretaris { background: #f5f3ff; color: #7c3aed; } 
        .role-Bendahara { background: #f0fdf4; color: #166534; }
        .role-Staf { background: #fff7ed; color: #c2410c; }

        /* === SECTION SUB BAGIAN (GRID MENU) === */
        .section-title { font-size: 18px; font-weight: 700; color: #111; margin: 30px 0 15px 0; display: flex; align-items: center; gap: 10px; }
        .subbag-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        .subbag-card {
            background: #fff; border: 1px solid #e5e7eb; border-radius: 16px;
            padding: 20px; text-align: center; cursor: pointer; transition: 0.3s;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            height: 140px; box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }
        .subbag-card:hover { transform: translateY(-5px); border-color: #3b82f6; box-shadow: 0 10px 20px rgba(0,0,0,0.08); }
        .subbag-icon { width: 50px; height: 50px; object-fit: contain; margin-bottom: 10px; }
        .subbag-name { font-weight: 600; font-size: 14px; color: #374151; }

        /* === SECTION INFO & REMINDER (BAWAH) === */
        .bottom-section { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
        
        /* Reminder Box */
        .reminder-card { background: #fff; border-radius: 16px; border: 1px solid #fee2e2; overflow: hidden; }
        .rem-header { background: #fef2f2; padding: 15px 20px; color: #b91c1c; font-weight: 700; border-bottom: 1px solid #fee2e2; display: flex; align-items: center; gap: 8px; }
        .rem-list { list-style: none; padding: 0; margin: 0; }
        .rem-item { padding: 12px 20px; border-bottom: 1px dashed #fee2e2; display: flex; justify-content: space-between; font-size: 13px; }
        .rem-item:last-child { border-bottom: none; }
        .rem-date { font-weight: 700; background: #fff; border: 1px solid #fecaca; padding: 2px 8px; border-radius: 6px; color: #b91c1c; font-size: 11px; }

        /* Stat Box */
        .stat-mini-grid { display: grid; gap: 15px; }
        .stat-mini { background: #fff; padding: 15px; border-radius: 16px; border: 1px solid #e5e7eb; display: flex; align-items: center; gap: 15px; }
        .sm-icon { width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .sm-val { font-size: 20px; font-weight: 800; color: #111; line-height: 1; }
        .sm-label { font-size: 12px; color: #6b7280; }

        /* Responsive */
        @media (max-width: 900px) {
            .dashboard-hero { flex-direction: column; }
            .hero-left { flex: none; width: 100%; }
            .bottom-section { grid-template-columns: 1fr; }
        }
    </style>
</head>

<body>

    <?php include 'layout/sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <div class="header-top">
                <div class="header-left">
                    <img src="<?= $base_url; ?>gambar/logo.png" class="bps-logo">
                    <div>
                        <h2 class="bps-title">Badan Pusat Statistik<br>Kota Pontianak</h2>
                        <p class="bps-subtitle">Sistem Informasi Kepegawaian Internal</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-hero">
            <div class="hero-left">
                <div class="subbag-big-card" style="height:100%; margin:0;">
                    <img src="<?= $base_url; ?>gambar/kasubbag.jpg" class="subbag-photo">
                    <div class="subbag-info">
                        <h2><?= $jabatan_user; ?></h2> 
                        <h4>Unit Pengelola Administrasi</h4>
                        <div class="subbag-details">
                            <div class="detail-row"><div class="detail-label">Nama:</div><div class="detail-value"><?= $nama_user; ?></div></div>
                            <div class="detail-row"><div class="detail-label">NIP:</div><div class="detail-value"><?= $nip_user; ?></div></div>
                            <div class="detail-row"><div class="detail-label">Status:</div><div class="detail-value">Aktif</div></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="hero-right">
                <div class="team-list-card">
                    <div class="team-header">👥 Tim Sub Bagian Umum</div>
                    <div class="team-scroll-area">
                        <?php if(empty($tim_kanan)) : ?>
                            <div style="text-align:center; padding:30px; color:#9ca3af; font-size:13px;">
                                Belum ada tim. <a href="pages/profil.php" style="color:#2563eb;">Atur disini.</a>
                            </div>
                        <?php else : ?>
                            <?php foreach($tim_kanan as $t) : 
                                $inisial = strtoupper(substr($t['nama_lengkap'], 0, 2));
                                $jabatan = $t['jabatan_dashboard'];
                                $bg = ($jabatan=='Sekretaris')?'#f5f3ff':(($jabatan=='Bendahara')?'#f0fdf4':'#fff7ed');
                                $cl = ($jabatan=='Sekretaris')?'#7c3aed':(($jabatan=='Bendahara')?'#166534':'#c2410c');
                            ?>
                            <div class="team-item">
                                <div class="t-avatar" style="background:<?= $bg; ?>; color:<?= $cl; ?>;"><?= $inisial; ?></div>
                                <div class="t-details">
                                    <span class="t-role role-<?= $jabatan; ?>"><?= $jabatan; ?></span>
                                    <div class="t-name"><?= $t['nama_lengkap']; ?></div>
                                    <div class="t-nip">NIP. <?= $t['nip']; ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <h3 class="section-title">📂 Akses Cepat Sub Bagian</h3>
        <div class="subbag-grid">
            <div class="subbag-card" onclick="location.href='<?= $base_url; ?>pages/keuangan/spider.php'">
                <img src="<?= $base_url; ?>gambar/spider.png" class="subbag-icon" alt="Spider">
                <div class="subbag-name">Spider</div>
            </div>
            <div class="subbag-card" onclick="location.href='<?= $base_url; ?>pages/keuangan/bos.php'">
                <img src="<?= $base_url; ?>gambar/bos.png" class="subbag-icon" alt="BOS">
                <div class="subbag-name">BOS</div>
            </div>
            <div class="subbag-card" onclick="location.href='<?= $base_url; ?>pages/keuangan/sakti.php'">
                <img src="<?= $base_url; ?>gambar/keuangan.jpg" class="subbag-icon" alt="Sakti">
                <div class="subbag-name">Sakti</div>
            </div>
            <div class="subbag-card" onclick="location.href='<?= $base_url; ?>pages/sakip/index.php'">
                <img src="<?= $base_url; ?>gambar/sakipp.png" class="subbag-icon" alt="Sakip">
                <div class="subbag-name">Sakip</div>
            </div>
            <div class="subbag-card" onclick="location.href='<?= $base_url; ?>pages/laporan/lakin.php'">
                <img src="<?= $base_url; ?>gambar/lakinn.png" class="subbag-icon" alt="Lakin">
                <div class="subbag-name">Lakin</div>
            </div>
            <div class="subbag-card" onclick="location.href='<?= $base_url; ?>pages/laporan/bmn.php'">
                <img src="<?= $base_url; ?>gambar/BMN.png" class="subbag-icon" alt="BMN">
                <div class="subbag-name">BMN</div>
            </div>
            <div class="subbag-card" onclick="location.href='<?= $base_url; ?>pages/kepegawaian/sdm.php'">
                <img src="<?= $base_url; ?>gambar/simpegg.png" class="subbag-icon" alt="Simpeg">
                <div class="subbag-name">Simpeg / SDM</div>
            </div>
            <div class="subbag-card" onclick="location.href='<?= $base_url; ?>pages/kepegawaian/pppk.php'">
                <img src="<?= $base_url; ?>gambar/pppkk.svg" class="subbag-icon" alt="PPPK">
                <div class="subbag-name">PPPK</div>
            </div>
        </div>

        <div class="bottom-section">
            <div class="reminder-card">
                <div class="rem-header">🔔 Peringatan Dini (KGB & KP)</div>
                <ul class="rem-list">
                    <?php if (empty($reminder_kgb) && empty($reminder_kp)) : ?>
                        <li class="rem-item" style="justify-content:center; color:#9ca3af;">Tidak ada jadwal terdekat.</li>
                    <?php else : ?>
                        <?php foreach($reminder_kgb as $r) : ?>
                            <li class="rem-item">
                                <span>KGB: <b><?= $r['nama_lengkap']; ?></b></span>
                                <span class="rem-date"><?= date('d/m/Y', strtotime($r['kgb_yad'])); ?></span>
                            </li>
                        <?php endforeach; ?>
                        <?php foreach($reminder_kp as $r) : ?>
                            <li class="rem-item">
                                <span>KP: <b><?= $r['nama_lengkap']; ?></b></span>
                                <span class="rem-date"><?= date('d/m/Y', strtotime($r['kp_yad'])); ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

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
            </div>
        </div>

        <p class="copyright" style="margin-top:40px;">© 2025 Badan Pusat Statistik Kota Pontianak. All rights reserved.</p>
    </div>

</body>
</html>