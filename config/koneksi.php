<?php
// Konfigurasi Database
$host       = "localhost";
$user       = "root";
$password   = "";
$database   = "bps_kepegawaian";

// Membuat Koneksi
$koneksi = mysqli_connect($host, $user, $password, $database);

// Cek Koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

?>