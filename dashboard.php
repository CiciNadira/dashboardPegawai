<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: index.php");
    exit;
}
require 'config/functions.php';

// === 1. DATA USER LOGIN (KARTU KIRI) ===
$username = $_SESSION["user"];
$user_data = query("SELECT * FROM users WHERE username = '$username'")[0];

// Data Tampilan (Ambil dari tabel users)
$nama_user    = $user_data['nama_lengkap'] ?? 'Administrator';
$nip_user     = $user_data['nip'] ?? '-'; 
$jabatan_user = $user_data['jabatan'] ?? 'Unit Pengelola Administrasi';

// === 2. DATA TIM (KARTU KANAN - Dari Tabel Pegawai) ===
$tim_kanan = query("SELECT * FROM pegawai WHERE jabatan_dashboard IN ('Sekretaris', 'Bendahara', 'Staf') 
                    ORDER BY FIELD(jabatan_dashboard, 'Sekretaris', 'Bendahara', 'Staf') ASC");

// === 3. STATISTIK ===
$total_pegawai   = count(query("SELECT id FROM pegawai"));
$total_subbagian = 8; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Pegawai BPS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base_url; ?>assets/css/kepegawaian-sdm.css">
    <link rel="stylesheet" href="<?= $base_url; ?>assets/css/dashboard.css">
    <style>
        /* Layout Kiri-Kanan */
        .dashboard-hero { display: flex; gap: 20px; margin-bottom: 30px; align-items: stretch; }
        .hero-left { flex: 0 0 350px; } /* Lebar kartu kasubbag */
        .hero-right { flex: 1; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; }
        
        /* Card Tim Kecil */
        .mini-card {
            background: #fff; padding: 15px; border-radius: 12px; border: 1px solid #e5e7eb;
            display: flex; align-items: center; gap: 12px; box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            transition: 0.3s;
        }
        .mini-card:hover { transform: translateY(-3px); border-color: #3b82f6; }
        .mc-photo {
            width: 45px; height: 45px; background: #f3f4f6; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-weight: bold; color: #6b7280; font-size: 16px;
        }
        .mc-role { font-size: 10px; font-weight: 700; text-transform: uppercase; margin-bottom: 2px; }
        .mc-name { font-size: 13px; font-weight: 600; color: #111; line-height: 1.2; }
        .mc-nip { font-size: 10px; color: #9ca3af; }
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
                    <img src="<?= $base_url; ?>gambar/kasubbag.jpg" class="subbag-photo" alt="Foto Profil">

                    <div class="subbag-info">
                        <h2><?= $jabatan_user; ?></h2> 
                        <h4>Unit Pengelola Administrasi & Data Kepegawaian</h4>

                        <div class="subbag-details">
                            <div class="detail-row">
                                <div class="detail-label">Nama:</div>
                                <div class="detail-value"><?= $nama_user; ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">NIP:</div>
                                <div class="detail-value"><?= $nip_user; ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Status:</div>
                                <div class="detail-value">Aktif</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="hero-right">
                <?php if(empty($tim_kanan)) : ?>
                    <div style="grid-column:1/-1; background:#f9fafb; border:2px dashed #e5e7eb; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#9ca3af; text-align:center;">
                        <p>Tim belum diatur.<br>Buka menu <b>Pengaturan</b> untuk mengisi.</p>
                    </div>
                <?php else : ?>
                    <?php foreach($tim_kanan as $t) : 
                        $jabatan = $t['jabatan_dashboard'];
                        $color = ($jabatan=='Sekretaris')?'#7c3aed':(($jabatan=='Bendahara')?'#166534':'#c2410c');
                        $bg = ($jabatan=='Sekretaris')?'#f5f3ff':(($jabatan=='Bendahara')?'#f0fdf4':'#fff7ed');
                    ?>
                    <div class="mini-card">
                        <div class="mc-photo" style="background:<?= $bg; ?>; color:<?= $color; ?>;">
                            <?= strtoupper(substr($t['nama_lengkap'], 0, 2)); ?>
                        </div>
                        <div>
                            <div class="mc-role" style="color:<?= $color; ?>"><?= $jabatan; ?></div>
                            <div class="mc-name"><?= $t['nama_lengkap']; ?></div>
                            <div class="mc-nip">NIP. <?= $t['nip']; ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="stats-wrapper">
            <div class="stat-card clickable" onclick="window.location.href='<?= $base_url; ?>pages/kepegawaian/sdm.php'">
                <div class="stat-icon"><img src="<?= $base_url; ?>gambar/employee.png"></div>
                <div class="stat-content">
                    <h4>Total Pegawai</h4>
                    <div class="stat-value"><?= $total_pegawai; ?></div>
                    <div class="stat-desc link-text">Klik untuk melihat detail</div>
                </div>
            </div>

            <div class="stat-card clickable">
                <div class="stat-icon"><img src="<?= $base_url; ?>gambar/gedung.png"></div>
                <div class="stat-content">
                    <h4>Total Sub Bagian</h4>
                    <div class="stat-value"><?= $total_subbagian; ?></div>
                    <div class="stat-desc link-text">Info Unit Kerja</div>
                </div>
            </div>
        </div>

        <p class="copyright">© 2025 Badan Pusat Statistik Kota Pontianak. All rights reserved.</p>
    </div>
</body>
</html>