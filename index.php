<?php
session_start();
require 'config/functions.php';

if (isset($_POST["login"])) {

    $username = $_POST["username"];
    $password = $_POST["password"];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");

    if (mysqli_num_rows($result) === 1) {

        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row["password"]) || $row["password"] === md5($password)) {

            $_SESSION["login"] = true;
            $_SESSION["user"] = $row["username"];
            $_SESSION["user_id"] = $row["id"];

            header("Location: dashboard.php");
            exit;
        }
    }

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

                <div style="margin-top: 15px;">
                    <a href="reset.php" style="font-size: 13px; color: #6b7280; text-decoration: none;">Lupa Password?</a>
                </div>
            </form>

            <p class="footer">© 2025 Badan Pusat Statistik Kota Pontianak</p>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById("password");
        const togglePass = document.getElementById("togglePass");

        togglePass.addEventListener("click", () => {
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                togglePass.src = "gambar/eye-open.png";
            } else {
                passwordInput.type = "password";
                togglePass.src = "gambar/eye-closed.png";
            }
        });
    </script>
</body>

</html>