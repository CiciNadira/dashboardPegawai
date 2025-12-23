<?php
require 'config/functions.php';

if (isset($_POST["reset"])) {
    if (resetPasswordViaToken($_POST) > 0) {
        echo "<script>
                alert('Password berhasil direset! Silakan login dengan password baru.');
                document.location.href = 'index.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal reset atau Password sama dengan sebelumnya.');
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - BPS</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .container { width: 100%; max-width: 400px; padding: 20px; }
        .login-box { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center; }
        .logo { width: 80px; margin-bottom: 20px; }
        h2 { margin: 0 0 5px 0; color: #111827; }
        .subtitle { margin: 0 0 20px 0; color: #6b7280; font-size: 14px; }
        label { display: block; text-align: left; margin-bottom: 5px; color: #374151; font-weight: 500; font-size: 14px; }
        input { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box; }
        .btn { width: 100%; background: #dc2626; color: white; padding: 10px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
        .btn:hover { background: #b91c1c; }
        .back-link { display: block; margin-top: 15px; font-size: 14px; text-decoration: none; color: #2563eb; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="container">
    <div class="login-box">
        <img src="gambar/logo.png" class="logo" alt="Logo BPS">

        <h2>Reset Password</h2>
        <p class="subtitle">Masukkan Kode Rahasia Admin untuk mereset.</p>

        <form action="" method="post"> 
            
            <label>Username</label>
            <input type="text" name="username" placeholder="Username akun yang mau direset" required autocomplete="off">

            <label>Kode Rahasia (Token)</label>
            <input type="password" name="kode_unik" placeholder="Masukkan Kode Keamanan" required>

            <label>Password Baru</label>
            <input type="password" name="password_baru" placeholder="Password baru Anda" required>

            <button type="submit" name="reset" class="btn">Reset Password</button> 
        </form>

        <a href="index.php" class="back-link">← Kembali ke Login</a>
    </div>
</div>

</body>
</html>