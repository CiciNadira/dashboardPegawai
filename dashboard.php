<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: index.php");
    exit;
}
require 'config/functions.php';

// === DATA LOGIC ===
$username     = $_SESSION["user"];
$user_data    = query("SELECT * FROM users WHERE username = '$username'")[0];
$nama_user    = $user_data['nama_lengkap'] ?? 'Administrator';
$nip_user     = $user_data['nip'] ?? '-'; 
$jabatan_user = $user_data['jabatan'] ?? 'Unit Pengelola Administrasi';

$tim_kanan    = query("SELECT * FROM pegawai WHERE jabatan_dashboard IN ('Bendahara', 'BMN', 'PPABP') 
                       ORDER BY FIELD(jabatan_dashboard, 'Bendahara', 'BMN', 'PPABP') ASC");

// Data Statistik untuk Info.php
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
    <link rel="stylesheet" href="<?= $base_url; ?>assets/css/dashboard-custom.css">

    <style>
        /* CSS INI UNTUK MEMASTIKAN BACKGROUND ROLE MUNCUL */
        .t-role {
            display: inline-block !important;
            padding: 2px 1px !important;
            border-radius: 4px !important;
            font-size: 10px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            margin-bottom: 4px !important;
            line-height: normal !important;
        }

        /* Warna spesifik yang dipaksa (Override) */
        .role-BENDAHARA { 
            background-color: #f0fdf4 !important; 
            color: #166534 !important; 
        }
        .role-BMN { 
            background-color: #fff7ed !important; 
            color: #c2410c !important; 
        }
        .role-PPABP { 
            background-color: #e0f2fe !important; 
            color: #0369a1 !important; 
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

        <?php include 'layout/dashboard/hero.php'; ?>

        <?php include 'layout/dashboard/menu.php'; ?>

        <div class="bottom-section">
            <?php include 'layout/dashboard/charts.php'; ?>

            <?php include 'layout/dashboard/info.php'; ?>
        </div>

        <p class="copyright" style="margin-top:40px;">© 2025 Badan Pusat Statistik Kota Pontianak. All rights reserved.</p>
    </div>

</body>
</html>