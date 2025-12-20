<?php
session_start();
if (!isset($_SESSION["login"])) header("Location: ../index.php");
require '../config/functions.php';

// Ambil Data User Login
$username_sess = $_SESSION["user"];
$user = query("SELECT * FROM users WHERE username = '$username_sess'")[0];

// Ambil List Pegawai (Untuk Dropdown Tim)
$list_pegawai = query("SELECT id, nama_lengkap, nip FROM pegawai ORDER BY nama_lengkap ASC");

// Ambil Posisi Tim Saat Ini
$cur_sekretaris = query("SELECT id FROM pegawai WHERE jabatan_dashboard = 'Sekretaris'")[0]['id'] ?? '';
$cur_bendahara  = query("SELECT id FROM pegawai WHERE jabatan_dashboard = 'Bendahara'")[0]['id'] ?? '';
$cur_staf       = query("SELECT id FROM pegawai WHERE jabatan_dashboard = 'Staf'")[0]['id'] ?? '';

// LOGIC UPDATE
if (isset($_POST["update_user"])) {
    if (updateUser($_POST) >= 0) {
        // Update Session user jika username diganti
        $_SESSION["user"] = $_POST["username"]; 
        echo "<script>alert('Profil User berhasil diperbarui!'); window.location.href='profil.php';</script>";
    }
}

if (isset($_POST["update_tim"])) {
    updateTimDashboard($_POST);
    echo "<script>alert('Susunan Tim Dashboard diperbarui!'); window.location.href='profil.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Profil</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base_url; ?>assets/css/kepegawaian-sdm.css">
    <style>
        .setting-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 20px; }
        .card-setting { background: #fff; padding: 25px; border-radius: 12px; border: 1px solid #e5e7eb; }
        .card-title { font-size: 18px; font-weight: 700; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #374151; }
        input, select { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #d1d5db; border-radius: 8px; }
        .btn-save { background: #2563eb; color: white; padding: 10px; border: none; border-radius: 8px; cursor: pointer; width: 100%; }
        .btn-save:hover { background: #1d4ed8; }
    </style>
</head>
<body>
    <?php include '../layout/sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <div class="header-top">
                <div class="header-left">
                    <img src="<?= $base_url; ?>gambar/logo.png" class="bps-logo">
                    <div>
                        <h2 class="bps-title">Pengaturan Profil</h2>
                        <p class="bps-subtitle">Kelola akun dan tampilan dashboard</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="setting-grid">
            <div class="card-setting">
                <div class="card-title">👤 Edit Profil Saya (Tampilan Dashboard Kiri)</div>
                <form action="" method="post">
                    <input type="hidden" name="id" value="<?= $user['id']; ?>">
                    
                    <label>Nama Lengkap (Tampil di Dashboard)</label>
                    <input type="text" name="nama" value="<?= $user['nama_lengkap']; ?>" required>

                    <label>NIP</label>
                    <input type="text" name="nip" value="<?= $user['nip']; ?>">

                    <label>Username Login</label>
                    <input type="text" name="username" value="<?= $user['username']; ?>" required>

                    <label>Password Baru (Kosongkan jika tidak diganti)</label>
                    <input type="password" name="password" placeholder="***">
                    
                    <button type="submit" name="update_user" class="btn-save">Simpan Perubahan</button>
                </form>
            </div>

            <div class="card-setting">
                <div class="card-title">👥 Susunan Tim (Tampilan Dashboard Kanan)</div>
                <form action="" method="post">
                    <label>Sekretaris</label>
                    <select name="sekretaris_id">
                        <option value="">-- Kosong --</option>
                        <?php foreach($list_pegawai as $p): ?>
                            <option value="<?= $p['id']; ?>" <?= ($p['id'] == $cur_sekretaris) ? 'selected' : ''; ?>>
                                <?= $p['nama_lengkap']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label>Bendahara</label>
                    <select name="bendahara_id">
                        <option value="">-- Kosong --</option>
                        <?php foreach($list_pegawai as $p): ?>
                            <option value="<?= $p['id']; ?>" <?= ($p['id'] == $cur_bendahara) ? 'selected' : ''; ?>>
                                <?= $p['nama_lengkap']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label>Staf Umum</label>
                    <select name="staf_id">
                        <option value="">-- Kosong --</option>
                        <?php foreach($list_pegawai as $p): ?>
                            <option value="<?= $p['id']; ?>" <?= ($p['id'] == $cur_staf) ? 'selected' : ''; ?>>
                                <?= $p['nama_lengkap']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit" name="update_tim" class="btn-save" style="background:#166534;">Update Tim</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>