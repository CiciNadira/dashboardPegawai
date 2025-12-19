<?php
session_start();
// Panggil file koneksi (pastikan path/lokasinya benar)
require 'config/functions.php';

// Cek jika tombol login ditekan
if (isset($_POST["login"])) {

    $username = $_POST["username"];
    $password = $_POST["password"];

    // Cek username di database
    // Kita gunakan md5 karena di database tadi passwordnya di-encrypt md5
    // Note: Untuk keamanan tingkat lanjut nanti bisa pakai password_verify (bcrypt)
    $password_md5 = md5($password);

    $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' AND password = '$password_md5'");

    // Jika username & password cocok (ketemu 1 baris data)
    if (mysqli_num_rows($result) === 1) {
        // Set Session
        $_SESSION["login"] = true;
        $_SESSION["user"] = $username;

        // Redirect ke Dashboard (Ubah dashboard.html jadi dashboard.php nanti)
        header("Location: dashboard.php");
        exit;
    }

    // Jika salah
    $error = true;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login BPS Kota Pontianak</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container">
    <div class="login-box">
        <img src="gambar/logo.png" class="logo" alt="Logo BPS">

        <h2>BPS Kota Pontianak</h2>
        <p class="subtitle">Sistem Informasi Kepegawaian Internal</p>

        <form action="" method="post"> 
            
            <?php if (isset($error)) : ?>
                <div class="error-msg" style="display:block; color: red; margin-bottom: 10px; font-size: 14px;">
                    Username atau password salah!
                </div>
            <?php endif; ?>

            <label for="username">Username</label>
            <input type="text" name="username" id="username" placeholder="Masukkan username Anda" required autocomplete="off">

            <label for="password">Password</label>
            <div class="password-wrapper">
                <input type="password" name="password" id="password" placeholder="Masukkan password Anda" required>
                <img id="togglePass" src="gambar/eye-closed.png" class="eye-icon" alt="toggle password visibility">
            </div>

            <button type="submit" name="login" class="btn">Masuk ke Dashboard</button> 
        </form>

        <p class="footer">© 2025 Badan Pusat Statistik Kota Pontianak</p>
    </div>
</div>

<script>
    // SCRIPT HANYA UNTUK UI (SHOW PASSWORD), BUKAN LOGIKA LOGIN
    const passwordInput = document.getElementById("password");
    const togglePass = document.getElementById("togglePass");

    togglePass.addEventListener("click", () => {
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            // Ganti icon mata terbuka (pastikan file gambarnya ada)
            togglePass.src = "gambar/eye-open.png"; 
        } else {
            passwordInput.type = "password";
            // Ganti icon mata tertutup
            togglePass.src = "gambar/eye-closed.png"; 
        }
    });
</script>
</body>
</html>