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

$nama_user    = $user_data['nama_lengkap'] ?? 'Administrator';
$nip_user     = $user_data['nip'] ?? '-'; 
$jabatan_user = $user_data['jabatan'] ?? 'Unit Pengelola Administrasi';

// === 2. DATA TIM (KARTU KANAN - Dari Tabel Pegawai) ===
// Urutkan: Sekretaris -> Bendahara -> Staf
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
        /* === LAYOUT UTAMA === */
        .dashboard-hero {
            display: flex;
            gap: 25px;
            margin-bottom: 30px;
            /* Pastikan tinggi kiri & kanan sama */
            align-items: stretch; 
        }

        /* KIRI: Kasubbag (Lebar Tetap) */
        .hero-left {
            flex: 0 0 380px;
        }

        /* KANAN: Tim List (Mengisi Sisa Ruang) */
        .hero-right {
            flex: 1;
        }

        /* CARD LIST STYLE (Kanan) */
        .team-list-card {
            background: #fff;
            border-radius: 20px; /* Sama dengan kartu kiri */
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            height: 100%; /* Agar tinggi mengikuti kartu kiri */
            padding: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .team-header {
            padding: 20px 25px;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            font-size: 16px;
            font-weight: 700;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .team-scroll-area {
            padding: 10px 25px;
            overflow-y: auto;
            flex: 1;
        }

        /* ITEM LIST PEGAWAI */
        .team-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #f3f4f6;
            transition: 0.2s;
        }
        .team-item:last-child { border-bottom: none; }
        .team-item:hover { transform: translateX(5px); }

        /* Foto Kecil di List */
        .t-avatar {
            width: 50px; height: 50px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-weight: bold; font-size: 18px;
            flex-shrink: 0;
        }

        /* Detail Teks */
        .t-details { flex: 1; }
        
        .t-role {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
        }
        .t-name { font-size: 15px; font-weight: 600; color: #111; margin-bottom: 2px; }
        .t-nip { font-size: 12px; color: #6b7280; }

        /* Warna Role */
        .role-Sekretaris { background: #f5f3ff; color: #7c3aed; } /* Ungu */
        .role-Bendahara { background: #f0fdf4; color: #166534; }  /* Hijau */
        .role-Staf { background: #fff7ed; color: #c2410c; }       /* Orange */

        /* Responsive di HP */
        @media (max-width: 900px) {
            .dashboard-hero { flex-direction: column; }
            .hero-left { flex: none; width: 100%; }
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
                <div class="team-list-card">
                    <div class="team-header">
                        <span style="font-size:20px;">👥</span> Tim Sub Bagian Umum
                    </div>

                    <div class="team-scroll-area">
                        <?php if(empty($tim_kanan)) : ?>
                            <div style="text-align:center; padding:30px; color:#9ca3af;">
                                <p>Belum ada tim yang diatur.</p>
                                <a href="pages/profil.php" style="color:#2563eb; font-size:13px;">Atur di Profil &rarr;</a>
                            </div>
                        <?php else : ?>
                            
                            <?php foreach($tim_kanan as $t) : 
                                $inisial = strtoupper(substr($t['nama_lengkap'], 0, 2));
                                $jabatan = $t['jabatan_dashboard'];
                                // Warna Background Avatar Sesuai Role
                                $bgAvatar = ($jabatan=='Sekretaris')?'#f5f3ff':(($jabatan=='Bendahara')?'#f0fdf4':'#fff7ed');
                                $clAvatar = ($jabatan=='Sekretaris')?'#7c3aed':(($jabatan=='Bendahara')?'#166534':'#c2410c');
                            ?>
                            
                            <div class="team-item">
                                <div class="t-avatar" style="background:<?= $bgAvatar; ?>; color:<?= $clAvatar; ?>;">
                                    <?= $inisial; ?>
                                </div>
                                
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

    <script>
        if (sessionStorage.getItem("isLogin") !== "true") {
             // Validasi opsional
        }
    </script>
</body>
</html>