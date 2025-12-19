<?php
session_start();
// Cek Login
if (!isset($_SESSION["login"])) {
    header("Location: index.php");
    exit;
}

// Panggil Functions & Config
require 'config/functions.php';

// 1. AMBIL DATA USER YANG LOGIN
$username = $_SESSION["user"];
// Kita ambil data user berdasarkan username yang disimpan di session
$user_data = query("SELECT * FROM users WHERE username = '$username'")[0];

// Jika data NIP/Jabatan kosong, beri default (agar tidak error tampilannya)
$nama_user = $user_data['nama_lengkap'] ?? 'Administrator';
$nip_user  = $user_data['nip'] ?? '-'; 
$jabatan_user = $user_data['jabatan'] ?? 'Unit Pengelola Administrasi';

// 2. HITUNG TOTAL PEGAWAI (Real-time dari Database)
$result_pegawai = mysqli_query($conn, "SELECT COUNT(*) as total FROM pegawai");
$data_pegawai = mysqli_fetch_assoc($result_pegawai);
$total_pegawai = $data_pegawai['total'];
// 3. HITUNG TOTAL SUB BAGIAN (Hardcoded untuk sekarang)
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

        <div class="subbag-big-card">
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
        // Cek Login Frontend (Double Protection)
        if (sessionStorage.getItem("isLogin") !== "true") {
            // Opsional: karena sudah ada PHP session check di atas
        }
    </script>
</body>
</html>