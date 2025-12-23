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

$tim_kanan    = query("SELECT * FROM pegawai WHERE jabatan_dashboard IN ('Sekretaris', 'Bendahara', 'Staf') 
                       ORDER BY FIELD(jabatan_dashboard, 'Sekretaris', 'Bendahara', 'Staf') ASC");

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