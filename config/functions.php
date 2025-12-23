<?php
$base_url = "http://localhost/dashboardPegawai/"; 

// Koneksi ke Database
// Gunakan Try-Catch untuk koneksi agar tidak blank putih jika DB mati
try {
    $conn = mysqli_connect("localhost", "root", "", "bps_kepegawaian");
} catch (Exception $e) {
    die("Koneksi Database Gagal: Pastikan Laragon/MySQL sudah Start. <br>Error: " . $e->getMessage());
}

// Variabel Global untuk menampung pesan error
$error_msg = "";

// Fungsi Query (Ambil Data)
function query($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while( $row = mysqli_fetch_assoc($result) ) {
        $rows[] = $row;
    }
    return $rows;
}

// ==========================================
// 1. CRUD PEGAWAI (SDM) - DENGAN VALIDASI
// ==========================================
function tambahPegawai($data) {
    global $conn, $error_msg;

    $nama           = htmlspecialchars($data["nama"]);
    $nip_bps        = htmlspecialchars($data["nip_bps"]);
    $nip            = htmlspecialchars($data["nip"]);
    
    // --- VALIDASI 1: Cek apakah NIP sudah ada? ---
    $cek_nip = mysqli_query($conn, "SELECT nip FROM pegawai WHERE nip = '$nip'");
    if (mysqli_fetch_assoc($cek_nip)) {
        $error_msg = "NIP $nip sudah terdaftar! Gunakan NIP lain.";
        return 0; // Gagal
    }

    $jabatan        = htmlspecialchars($data["jabatan"]);
    $tmt_jabatan    = htmlspecialchars($data["tmt_jab"]);
    $gol_akhir      = htmlspecialchars($data["gol_akhir"]);
    $tmt_golongan   = htmlspecialchars($data["tmt_gol"]);
    $status         = htmlspecialchars($data["status"]);
    $pendidikan_sk  = htmlspecialchars($data["pend_sk"]);
    $tmt_cpns       = htmlspecialchars($data["tmt_cpns"]);
    $ak_terakhir_angka = htmlspecialchars($data["ak_terakhir_angka"]);
    $ak_terakhir_tahun = htmlspecialchars($data["ak_terakhir_tahun"]);
    $ak_konversi_angka = htmlspecialchars($data["ak_konversi_angka"]);
    $ak_konversi_tahun = htmlspecialchars($data["ak_konversi_tahun"]);
    $keterangan        = htmlspecialchars($data["ket"]);

    $query = "INSERT INTO pegawai 
                (nama_lengkap, nip_bps, nip, jabatan, tmt_jabatan, golongan_akhir, tmt_golongan, 
                 status_kepegawaian, pendidikan_sk, tmt_cpns, 
                 ak_terakhir_angka, ak_terakhir_tahun, ak_konversi_angka, ak_konversi_tahun, keterangan)
              VALUES
                ('$nama', '$nip_bps', '$nip', '$jabatan', '$tmt_jabatan', '$gol_akhir', '$tmt_golongan',
                 '$status', '$pendidikan_sk', '$tmt_cpns',
                 '$ak_terakhir_angka', '$ak_terakhir_tahun', '$ak_konversi_angka', '$ak_konversi_tahun', '$keterangan')";

    // --- EXECUTE DENGAN TRY-CATCH (Anti Blank Page) ---
    try {
        mysqli_query($conn, $query);
        return mysqli_affected_rows($conn);
    } catch (mysqli_sql_exception $e) {
        $error_msg = "Gagal Simpan: " . $e->getMessage();
        return 0;
    }
}

function ubahPegawai($data) {
    global $conn, $error_msg;
    $id = $data["id"];
    
    // Ambil NIP lama dulu untuk validasi
    $nip_baru = htmlspecialchars($data["nip"]);
    
    // Cek duplikat NIP (Kecuali punya diri sendiri)
    $cek_nip = mysqli_query($conn, "SELECT nip FROM pegawai WHERE nip = '$nip_baru' AND id != $id");
    if (mysqli_fetch_assoc($cek_nip)) {
        $error_msg = "NIP $nip_baru sudah dipakai pegawai lain!";
        return 0;
    }

    $nama           = htmlspecialchars($data["nama"]);
    $nip_bps        = htmlspecialchars($data["nip_bps"]);
    $jabatan        = htmlspecialchars($data["jabatan"]);
    $tmt_jabatan    = htmlspecialchars($data["tmt_jab"]);
    $gol_akhir      = htmlspecialchars($data["gol_akhir"]);
    $tmt_golongan   = htmlspecialchars($data["tmt_gol"]);
    $status         = htmlspecialchars($data["status"]);
    $pendidikan_sk  = htmlspecialchars($data["pend_sk"]);
    $tmt_cpns       = htmlspecialchars($data["tmt_cpns"]);
    $ak_terakhir_angka = htmlspecialchars($data["ak_terakhir_angka"]);
    $ak_terakhir_tahun = htmlspecialchars($data["ak_terakhir_tahun"]);
    $ak_konversi_angka = htmlspecialchars($data["ak_konversi_angka"]);
    $ak_konversi_tahun = htmlspecialchars($data["ak_konversi_tahun"]);
    $keterangan        = htmlspecialchars($data["ket"]);

    $query = "UPDATE pegawai SET
                nama_lengkap = '$nama', nip_bps = '$nip_bps', nip = '$nip_baru', jabatan = '$jabatan',
                tmt_jabatan = '$tmt_jabatan', golongan_akhir = '$gol_akhir', tmt_golongan = '$tmt_golongan',
                status_kepegawaian = '$status', pendidikan_sk = '$pendidikan_sk', tmt_cpns = '$tmt_cpns',
                ak_terakhir_angka = '$ak_terakhir_angka', ak_terakhir_tahun = '$ak_terakhir_tahun',
                ak_konversi_angka = '$ak_konversi_angka', ak_konversi_tahun = '$ak_konversi_tahun',
                keterangan = '$keterangan'
              WHERE id = $id";
    
    try {
        mysqli_query($conn, $query);
        return mysqli_affected_rows($conn);
    } catch (mysqli_sql_exception $e) {
        $error_msg = "Gagal Update: " . $e->getMessage();
        return 0;
    }
}

function hapusPegawai($id) {
    global $conn, $error_msg;
    try {
        mysqli_query($conn, "DELETE FROM pegawai WHERE id = $id");
        return mysqli_affected_rows($conn);
    } catch (mysqli_sql_exception $e) {
        $error_msg = "Gagal Hapus: Data ini mungkin sedang dipakai di tabel KGB/KP/Fungsional. Hapus data turunannya dulu.";
        return 0;
    }
}

// ==========================================
// 2. CRUD FUNGSIONAL
// ==========================================
function tambahFungsional($data) {
    global $conn, $error_msg;
    // Amankan data
    $pegawai_id = htmlspecialchars($data["pegawai_id"]);
    $tmt        = htmlspecialchars($data["tmt_fungsional"]);

    $ak_ta = htmlspecialchars($data["ak_terakhir_angka"]);
    $ak_tt = htmlspecialchars($data["ak_terakhir_tahun"]);
    $ak_ka = htmlspecialchars($data["ak_konversi_angka"]);
    $ak_kt = htmlspecialchars($data["ak_konversi_tahun"]);
    $ket = htmlspecialchars($data["ket"]);

    $query = "INSERT INTO data_fungsional (pegawai_id, tmt_fungsional, ak_terakhir_angka, ak_terakhir_tahun, ak_konversi_angka, ak_konversi_tahun, keterangan)
              VALUES ('$pegawai_id', '$tmt', '$ak_ta', '$ak_tt', '$ak_ka', '$ak_kt', '$ket')";
    
    try {
        mysqli_query($conn, $query);
        return mysqli_affected_rows($conn);
    } catch (mysqli_sql_exception $e) {
        $error_msg = "Gagal: " . $e->getMessage();
        return 0;
    }
}

function ubahFungsional($data) {
    global $conn, $error_msg;
    $id = $data["id"];

    $pegawai_id = htmlspecialchars($data["pegawai_id"]);
    $tmt = htmlspecialchars($data["tmt_fungsional"]);
    $ak_ta = htmlspecialchars($data["ak_terakhir_angka"]);
    $ak_tt = htmlspecialchars($data["ak_terakhir_tahun"]);
    $ak_ka = htmlspecialchars($data["ak_konversi_angka"]);
    $ak_kt = htmlspecialchars($data["ak_konversi_tahun"]);
    $ket = htmlspecialchars($data["ket"]);

    $query = "UPDATE data_fungsional SET
                pegawai_id = '$pegawai_id', tmt_fungsional = '$tmt',
                ak_terakhir_angka = '$ak_ta', ak_terakhir_tahun = '$ak_tt',
                ak_konversi_angka = '$ak_ka', ak_konversi_tahun = '$ak_kt',
                keterangan = '$ket'
              WHERE id = $id";
    try {
        mysqli_query($conn, $query);
        return mysqli_affected_rows($conn);
    } catch (Exception $e) {
        $error_msg = $e->getMessage();
        return 0;
    }
}

function hapusFungsional($id) {
    global $conn, $error_msg;
    try {
        mysqli_query($conn, "DELETE FROM data_fungsional WHERE id = $id");
        return mysqli_affected_rows($conn);
    } catch (Exception $e) {
        $error_msg = $e->getMessage();
        return 0;
    }
}

// ==========================================
// 3. CRUD KGB & KP (Sama logikanya)
// ==========================================

function tambahKgb($data) {
    global $conn, $error_msg;
    $pegawai_id = htmlspecialchars($data["pegawai_id"]);
    $mkg = htmlspecialchars($data["mkg"]);
    $kgb_last = htmlspecialchars($data["kgb_terakhir"]);
    $kgb_yad = htmlspecialchars($data["kgb_yad"]);
    $ket = htmlspecialchars($data["ket"]);

    try {
        mysqli_query($conn, "INSERT INTO data_kgb (pegawai_id, mkg, kgb_terakhir, kgb_yad, keterangan) VALUES ('$pegawai_id', '$mkg', '$kgb_last', '$kgb_yad', '$ket')");
        return mysqli_affected_rows($conn);
    } catch (Exception $e) { $error_msg = $e->getMessage(); return 0; }
}

function ubahKgb($data) {
    global $conn, $error_msg;
    $id = $data["id"];
    $pegawai_id = htmlspecialchars($data["pegawai_id"]);
    $mkg = htmlspecialchars($data["mkg"]);
    $kgb_last = htmlspecialchars($data["kgb_terakhir"]);
    $kgb_yad = htmlspecialchars($data["kgb_yad"]);
    $ket = htmlspecialchars($data["ket"]);

    try {
        mysqli_query($conn, "UPDATE data_kgb SET pegawai_id='$pegawai_id', mkg='$mkg', kgb_terakhir='$kgb_last', kgb_yad='$kgb_yad', keterangan='$ket' WHERE id=$id");
        return mysqli_affected_rows($conn);
    } catch (Exception $e) { $error_msg = $e->getMessage(); return 0; }
}

function hapusKgb($id) {
    global $conn, $error_msg;
    try {
        mysqli_query($conn, "DELETE FROM data_kgb WHERE id = $id");
        return mysqli_affected_rows($conn);
    } catch (Exception $e) { $error_msg = $e->getMessage(); return 0; }
}

function tambahKp($data) {
    global $conn, $error_msg;
    $pegawai_id = htmlspecialchars($data["pegawai_id"]);
    $kp_last = htmlspecialchars($data["kp_terakhir"]);
    $kp_yad = htmlspecialchars($data["kp_yad"]);
    $ket = htmlspecialchars($data["ket"]);

    try {
        mysqli_query($conn, "INSERT INTO data_kp (pegawai_id, kp_terakhir, kp_yad, keterangan) VALUES ('$pegawai_id', '$kp_last', '$kp_yad', '$ket')");
        return mysqli_affected_rows($conn);
    } catch (Exception $e) { $error_msg = $e->getMessage(); return 0; }
}

function ubahKp($data) {
    global $conn, $error_msg;
    $id = $data["id"];
    $pegawai_id = htmlspecialchars($data["pegawai_id"]);
    $kp_last = htmlspecialchars($data["kp_terakhir"]);
    $kp_yad = htmlspecialchars($data["kp_yad"]);
    $ket = htmlspecialchars($data["ket"]);

    try {
        mysqli_query($conn, "UPDATE data_kp SET pegawai_id='$pegawai_id', kp_terakhir='$kp_last', kp_yad='$kp_yad', keterangan='$ket' WHERE id=$id");
        return mysqli_affected_rows($conn);
    } catch (Exception $e) { $error_msg = $e->getMessage(); return 0; }
}

function hapusKp($id) {
    global $conn, $error_msg;
    try {
        mysqli_query($conn, "DELETE FROM data_kp WHERE id = $id");
        return mysqli_affected_rows($conn);
    } catch (Exception $e) { $error_msg = $e->getMessage(); return 0; }
}

// 5. UPLOAD PPPK
function uploadDokumen() {
    global $error_msg;
    
    // Cek ada file?
    if ($_FILES['file']['error'] === 4) { $error_msg = "Pilih file dulu!"; return false; }

    $namaFile = $_FILES['file']['name'];
    $ukuranFile = $_FILES['file']['size'];
    $tmpName = $_FILES['file']['tmp_name'];
    
    $ekstensiValid = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip'];
    $ekstensi = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

    if (!in_array($ekstensi, $ekstensiValid)) {
        $error_msg = "Format file tidak didukung! (Hanya PDF, Office, Gambar)";
        return false;
    }
    if ($ukuranFile > 10000000) {
        $error_msg = "File terlalu besar! Max 10MB.";
        return false;
    }

    $namaBaru = uniqid() . '.' . $ekstensi;
    move_uploaded_file($tmpName, '../../uploads/pppk/' . $namaBaru);
    return $namaBaru;
}

function tambahPppk($data) {
    global $conn, $error_msg;
    $judul = htmlspecialchars($data["judul"]);
    $ket = htmlspecialchars($data["ket"]);
    
    $file = uploadDokumen();
    if (!$file) return 0;

    $lokasi = "uploads/pppk/" . $file;
    try {
        mysqli_query($conn, "INSERT INTO dokumen_pppk (judul_laporan, nama_file, lokasi_file, keterangan) VALUES ('$judul', '$file', '$lokasi', '$ket')");
        return mysqli_affected_rows($conn);
    } catch (Exception $e) { $error_msg = $e->getMessage(); return 0; }
}

function hapusPppk($id) {
    global $conn, $error_msg;
    $res = mysqli_query($conn, "SELECT nama_file FROM dokumen_pppk WHERE id=$id");
    $f = mysqli_fetch_assoc($res);
    
    try {
        if(file_exists('../../uploads/pppk/' . $f['nama_file'])) unlink('../../uploads/pppk/' . $f['nama_file']);
        mysqli_query($conn, "DELETE FROM dokumen_pppk WHERE id=$id");
        return mysqli_affected_rows($conn);
    } catch (Exception $e) { $error_msg = $e->getMessage(); return 0; }
}

// ==========================================
// 6. CRUD KEUANGAN (Spider, BOS, Sakti, dll)
// ==========================================

function uploadBuktiKeuangan() {
    $namaFile   = $_FILES['bukti']['name'];
    $ukuranFile = $_FILES['bukti']['size'];
    $error      = $_FILES['bukti']['error'];
    $tmpName    = $_FILES['bukti']['tmp_name'];

    // Cek apakah ada file yang diupload
    if ($error === 4) {
        return null; // Boleh kosong (misal saat edit data tapi gak ganti file)
    }

    $ekstensiValid = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];
    $ekstensiFile  = explode('.', $namaFile);
    $ekstensiFile  = strtolower(end($ekstensiFile));

    if (!in_array($ekstensiFile, $ekstensiValid)) {
        echo "<script>alert('Format file tidak didukung! Gunakan PDF, Office, atau Gambar.');</script>";
        return false;
    }

    if ($ukuranFile > 5242880) { // Max 5MB
        echo "<script>alert('Ukuran file terlalu besar! Maksimal 5MB.');</script>";
        return false;
    }

    $namaFileBaru = uniqid() . '.' . $ekstensiFile;
    // Pastikan folder ini sudah dibuat: uploads/keuangan/
    move_uploaded_file($tmpName, '../../uploads/keuangan/' . $namaFileBaru);

    return $namaFileBaru;
}

function tambahKeuangan($data) {
    global $conn;
    
    $jenis   = htmlspecialchars($data["jenis_aplikasi"]);
    $tgl     = htmlspecialchars($data["tanggal"]);
    $uraian  = htmlspecialchars($data["uraian"]);
    $status  = htmlspecialchars($data["status"]);
    $ket     = htmlspecialchars($data["ket"]);

    // Upload Bukti (Wajib saat tambah baru)
    $bukti = uploadBuktiKeuangan();
    if (!$bukti) {
        echo "<script>alert('Harap upload bukti dukung!');</script>";
        return false;
    }

    $query = "INSERT INTO data_keuangan (jenis_aplikasi, tanggal_kegiatan, uraian_kegiatan, status, bukti_file, keterangan)
              VALUES ('$jenis', '$tgl', '$uraian', '$status', '$bukti', '$ket')";
    
    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

function hapusKeuangan($id) {
    global $conn;
    
    // Hapus file fisik dulu
    $result = mysqli_query($conn, "SELECT bukti_file FROM data_keuangan WHERE id = $id");
    $row = mysqli_fetch_assoc($result);
    $file = $row['bukti_file'];
    
    if ($file && file_exists('../../uploads/keuangan/' . $file)) {
        unlink('../../uploads/keuangan/' . $file);
    }

    mysqli_query($conn, "DELETE FROM data_keuangan WHERE id = $id");
    return mysqli_affected_rows($conn);
}

function ubahKeuangan($data) {
    global $conn;
    $id = $data["id"];
    $tgl     = htmlspecialchars($data["tanggal"]);
    $uraian  = htmlspecialchars($data["uraian"]);
    $status  = htmlspecialchars($data["status"]);
    $ket     = htmlspecialchars($data["ket"]);
    $fileLama = htmlspecialchars($data["file_lama"]);

    // Cek apakah user upload file baru
    if ($_FILES['bukti']['error'] === 4) {
        $bukti = $fileLama; // Pakai file lama
    } else {
        $bukti = uploadBuktiKeuangan(); // Upload file baru
        if (!$bukti) return false;
        
        // Hapus file lama agar tidak menumpuk
        if (file_exists('../../uploads/keuangan/' . $fileLama)) {
            unlink('../../uploads/keuangan/' . $fileLama);
        }
    }

    $query = "UPDATE data_keuangan SET
                tanggal_kegiatan = '$tgl',
                uraian_kegiatan = '$uraian',
                status = '$status',
                bukti_file = '$bukti',
                keterangan = '$ket'
              WHERE id = $id";

    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}
// ==========================================
// 7. CRUD SAKIP (Laporan Kinerja)
// ==========================================

function uploadSakip() {
    $namaFile   = $_FILES['file']['name'];
    $ukuranFile = $_FILES['file']['size'];
    $error      = $_FILES['file']['error'];
    $tmpName    = $_FILES['file']['tmp_name'];

    if ($error === 4) {
        echo "<script>alert('Pilih file terlebih dahulu!');</script>";
        return false;
    }

    $ekstensiValid = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip'];
    $ekstensiFile  = explode('.', $namaFile);
    $ekstensiFile  = strtolower(end($ekstensiFile));

    if (!in_array($ekstensiFile, $ekstensiValid)) {
        echo "<script>alert('Format file tidak didukung!');</script>";
        return false;
    }

    if ($ukuranFile > 10485760) { // Max 10MB
        echo "<script>alert('Ukuran file terlalu besar! Maksimal 10MB.');</script>";
        return false;
    }

    $namaFileBaru = uniqid() . '.' . $ekstensiFile;
    move_uploaded_file($tmpName, '../../uploads/sakip/' . $namaFileBaru);

    return $namaFileBaru;
}

function tambahSakip($data) {
    global $conn;
    
    $tahun    = htmlspecialchars($data["tahun"]);
    $triwulan = htmlspecialchars($data["triwulan"]);
    $judul    = htmlspecialchars($data["judul"]);
    $ket      = htmlspecialchars($data["ket"]);

    $file = uploadSakip();
    if (!$file) return false;

    $query = "INSERT INTO data_sakip (tahun, triwulan, judul_dokumen, nama_file, keterangan)
              VALUES ('$tahun', '$triwulan', '$judul', '$file', '$ket')";
    
    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

function hapusSakip($id) {
    global $conn;
    
    $result = mysqli_query($conn, "SELECT nama_file FROM data_sakip WHERE id = $id");
    $row = mysqli_fetch_assoc($result);
    $file = $row['nama_file'];
    
    if ($file && file_exists('../../uploads/sakip/' . $file)) {
        unlink('../../uploads/sakip/' . $file);
    }

    mysqli_query($conn, "DELETE FROM data_sakip WHERE id = $id");
    return mysqli_affected_rows($conn);
}

// ==========================================
// 8. CRUD LAPORAN (Keuangan, Kepegawaian, Lakin)
// ==========================================

function uploadLaporanFile() {
    $namaFile   = $_FILES['file']['name'];
    $ukuranFile = $_FILES['file']['size'];
    $error      = $_FILES['file']['error'];
    $tmpName    = $_FILES['file']['tmp_name'];

    if ($error === 4) {
        echo "<script>alert('Pilih file laporan terlebih dahulu!');</script>";
        return false;
    }

    $ekstensiValid = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];
    $ekstensiFile  = explode('.', $namaFile);
    $ekstensiFile  = strtolower(end($ekstensiFile));

    if (!in_array($ekstensiFile, $ekstensiValid)) {
        echo "<script>alert('Format file tidak didukung! Gunakan PDF, Word, Excel, atau Gambar.');</script>";
        return false;
    }

    if ($ukuranFile > 10485760) { // Max 10MB
        echo "<script>alert('Ukuran file terlalu besar! Maksimal 10MB.');</script>";
        return false;
    }

    $namaFileBaru = uniqid() . '.' . $ekstensiFile;
    move_uploaded_file($tmpName, '../../uploads/laporan/' . $namaFileBaru);

    return $namaFileBaru;
}

function tambahLaporan($data) {
    global $conn;
    
    $kategori = htmlspecialchars($data["kategori"]);
    $judul    = htmlspecialchars($data["judul"]);
    $periode  = htmlspecialchars($data["periode"]); // Misal: Januari 2025

    $file = uploadLaporanFile();
    if (!$file) return false;

    // Tanggal upload otomatis hari ini (current_timestamp di database)
    $query = "INSERT INTO data_laporan (kategori, judul_laporan, periode_bulan, nama_file)
              VALUES ('$kategori', '$judul', '$periode', '$file')";
    
    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

function hapusLaporan($id) {
    global $conn;
    
    // Hapus file fisik
    $result = mysqli_query($conn, "SELECT nama_file FROM data_laporan WHERE id = $id");
    $row = mysqli_fetch_assoc($result);
    $file = $row['nama_file'];
    
    if ($file && file_exists('../../uploads/laporan/' . $file)) {
        unlink('../../uploads/laporan/' . $file);
    }

    mysqli_query($conn, "DELETE FROM data_laporan WHERE id = $id");
    return mysqli_affected_rows($conn);
}

// ==========================================
// 9. FUNGSI PENGATURAN & PROFIL
// ==========================================

// 1. Update Akun User (Login)
function updateUser($data) {
    global $conn;
    $id       = $data["id"];
    $username = strtolower(stripslashes($data["username"]));
    $password = mysqli_real_escape_string($conn, $data["password"]);
    $nama     = htmlspecialchars($data["nama"]); // Update nama tampilan juga
    $nip      = htmlspecialchars($data["nip"]);

    // Cek username kembar
    $cek = mysqli_query($conn, "SELECT username FROM users WHERE username = '$username' AND id != $id");
    if (mysqli_fetch_assoc($cek)) {
        echo "<script>alert('Username sudah terpakai!');</script>";
        return false;
    }

    // Jika password diisi, update password. Jika kosong, biarkan password lama.
    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET username = '$username', password = '$password_hash', nama_lengkap = '$nama', nip = '$nip' WHERE id = $id";
    } else {
        $query = "UPDATE users SET username = '$username', nama_lengkap = '$nama', nip = '$nip' WHERE id = $id";
    }

    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

// 2. Update Susunan Tim (Kanan Dashboard)
function updateTimDashboard($data) {
    global $conn;

    // Reset semua jadi 'Tidak' dulu
    mysqli_query($conn, "UPDATE pegawai SET jabatan_dashboard = 'Tidak'");

    if (!empty($data['sekretaris_id'])) {
        $id = $data['sekretaris_id'];
        mysqli_query($conn, "UPDATE pegawai SET jabatan_dashboard = 'Sekretaris' WHERE id = $id");
    }
    if (!empty($data['bendahara_id'])) {
        $id = $data['bendahara_id'];
        mysqli_query($conn, "UPDATE pegawai SET jabatan_dashboard = 'Bendahara' WHERE id = $id");
    }
    if (!empty($data['staf_id'])) {
        $id = $data['staf_id'];
        mysqli_query($conn, "UPDATE pegawai SET jabatan_dashboard = 'Staf' WHERE id = $id");
    }
    return 1;
}

// ==========================================
// 10. FUNGSI IMPORT EXCEL (PERBAIKAN DECIMAL)
// ==========================================

function excelDateToSQL($dateValue) {
    if (empty($dateValue) || $dateValue == '-' || $dateValue == '0000-00-00') return '0000-00-00';
    if (is_numeric($dateValue)) {
        $unixDate = ($dateValue - 25569) * 86400;
        return gmdate("Y-m-d", $unixDate);
    }
    try {
        $date = new DateTime($dateValue);
        return $date->format('Y-m-d');
    } catch (Exception $e) {
        return '0000-00-00';
    }
}

function importSDM($json_data) {
    global $conn;
    
    $data = json_decode($json_data, true);
    if (json_last_error() !== JSON_ERROR_NONE || empty($data)) {
        return ["status" => "error", "msg" => "Data JSON Kosong/Rusak."];
    }

    $berhasil = 0;
    $gagal = 0;
    $duplikat = 0;
    $error_list = [];

    foreach ($data as $row) {
        $nama       = htmlspecialchars($row['Nama'] ?? '');
        $nip_bps    = htmlspecialchars($row['NIP BPS'] ?? '');
        $nip        = htmlspecialchars($row['NIP'] ?? '');
        $jabatan    = htmlspecialchars($row['Jabatan'] ?? '');
        $tmt_jab    = excelDateToSQL($row['TMT Jabatan'] ?? ''); 
        $gol_akhir  = htmlspecialchars($row['Gol Akhir'] ?? '');
        $tmt_gol    = excelDateToSQL($row['TMT Golongan'] ?? '');
        $status     = htmlspecialchars($row['Status'] ?? 'PNS');
        $pendidikan = htmlspecialchars($row['Pendidikan'] ?? '');
        $tmt_cpns   = excelDateToSQL($row['TMT CPNS'] ?? '');
        $ket        = htmlspecialchars($row['Ket'] ?? '');

        // Jika Angka Kredit kosong atau strip (-), paksa jadi 0
        $ak_ta = $row['AK Terakhir Angka'] ?? 0;
        if($ak_ta === '' || $ak_ta === '-') $ak_ta = 0;

        $ak_tt = $row['AK Terakhir Tahun'] ?? 0;
        if($ak_tt === '' || $ak_tt === '-') $ak_tt = 0;

        $ak_ka = $row['AK Konversi Angka'] ?? 0;
        if($ak_ka === '' || $ak_ka === '-') $ak_ka = 0;

        $ak_kt = $row['AK Konversi Tahun'] ?? 0;
        if($ak_kt === '' || $ak_kt === '-') $ak_kt = 0;

        if (empty($nip)) { $gagal++; continue; }

        // Cek Duplikat
        $cek = mysqli_query($conn, "SELECT id FROM pegawai WHERE nip = '$nip'");
        if (mysqli_num_rows($cek) > 0) {
            $duplikat++;
        } else {
            $query = "INSERT INTO pegawai 
                      (nama_lengkap, nip_bps, nip, jabatan, tmt_jabatan, golongan_akhir, tmt_golongan, status_kepegawaian, pendidikan_sk, tmt_cpns, ak_terakhir_angka, ak_terakhir_tahun, ak_konversi_angka, ak_konversi_tahun, keterangan)
                      VALUES 
                      ('$nama', '$nip_bps', '$nip', '$jabatan', '$tmt_jab', '$gol_akhir', '$tmt_gol', '$status', '$pendidikan', '$tmt_cpns', '$ak_ta', '$ak_tt', '$ak_ka', '$ak_kt', '$ket')";
            
            if (mysqli_query($conn, $query)) {
                $berhasil++;
            } else {
                $gagal++;
                $error_list[] = mysqli_error($conn);
            }
        }
    }

    $pesan = "Import Selesai!\\n✅ Masuk: $berhasil\\n⚠️ Duplikat: $duplikat\\n❌ Gagal: $gagal";
    if(!empty($error_list)) $pesan .= "\\nInfo Error Pertama: " . $error_list[0];

    return ["status" => "success", "msg" => $pesan];
}

// 11. IMPORT FUNGSIONAL (FIX DECIMAL)
function importFungsional($json_data) {
    global $conn;
    $data = json_decode($json_data, true);
    if (empty($data)) return ["status" => "error", "msg" => "Data kosong/Format Salah"];

    $berhasil = 0; $gagal = 0; $skip = 0;
    $error_log = "";

    foreach ($data as $row) {
        $nip = htmlspecialchars($row['NIP'] ?? '');
        if(empty($nip)) { $gagal++; continue; }
        $cek = mysqli_query($conn, "SELECT id FROM pegawai WHERE nip = '$nip'");
        $peg = mysqli_fetch_assoc($cek);

        if ($peg) {
            $pegawai_id = $peg['id'];
            $tmt        = excelDateToSQL($row['TMT Fungsional'] ?? '');
            
            $ak_ta = $row['AK Terakhir Angka'] ?? 0;
            if($ak_ta === '' || $ak_ta === '-') $ak_ta = 0;

            $ak_tt = $row['AK Terakhir Tahun'] ?? 0;
            if($ak_tt === '' || $ak_tt === '-') $ak_tt = 0;

            $ak_ka = $row['AK Konversi Angka'] ?? 0;
            if($ak_ka === '' || $ak_ka === '-') $ak_ka = 0;

            $ak_kt = $row['AK Konversi Tahun'] ?? 0;
            if($ak_kt === '' || $ak_kt === '-') $ak_kt = 0;

            $ket = htmlspecialchars($row['Ket'] ?? '');

            $query = "INSERT INTO data_fungsional 
                      (pegawai_id, tmt_fungsional, ak_terakhir_angka, ak_terakhir_tahun, ak_konversi_angka, ak_konversi_tahun, keterangan) 
                      VALUES 
                      ('$pegawai_id', '$tmt', '$ak_ta', '$ak_tt', '$ak_ka', '$ak_kt', '$ket')";
            
            if(mysqli_query($conn, $query)) {
                $berhasil++;
            } else {
                $gagal++;
                $error_log .= mysqli_error($conn) . " | ";
            }
        } else {
            $skip++;
        }
    }
    
    $pesan = "Import Selesai!\\n✅ Berhasil: $berhasil\\n⚠️ NIP Tidak Dikenal: $skip\\n❌ Gagal: $gagal";
    if(!empty($error_log)) $pesan .= "\\nInfo Error: " . substr($error_log, 0, 150);

    return ["msg" => $pesan];
}

// ==========================================
// 12. IMPORT KGB (UPDATE: PAKAI NAMA KOLOM)
// ==========================================
function importKGB($json_data) {
    global $conn;
    $data = json_decode($json_data, true);
    if (empty($data)) return ["status" => "error", "msg" => "Data Excel kosong/Format salah"];

    $berhasil = 0; $gagal = 0; $skip = 0;

    foreach ($data as $row) {
        $nip = htmlspecialchars($row['NIP'] ?? '');
        if(empty($nip)) { $gagal++; continue; }

        // Cari ID Pegawai
        $cek = mysqli_query($conn, "SELECT id FROM pegawai WHERE nip = '$nip'");
        $peg = mysqli_fetch_assoc($cek);

        if ($peg) {
            $pegawai_id = $peg['id'];
            $mkg        = htmlspecialchars($row['MKG'] ?? '');
            $akhir      = excelDateToSQL($row['KGB Terakhir'] ?? '');
            $yad        = excelDateToSQL($row['KGB YAD'] ?? '');
            $ket        = htmlspecialchars($row['Ket'] ?? '');

            // Gunakan Nama Kolom Eksplisit (Lebih Aman)
            $query = "INSERT INTO data_kgb (pegawai_id, mkg, kgb_terakhir, kgb_yad, keterangan) 
                      VALUES ('$pegawai_id', '$mkg', '$akhir', '$yad', '$ket')";
            
            if(mysqli_query($conn, $query)) $berhasil++;
            else $gagal++;
        } else {
            $skip++; // NIP tidak ditemukan
        }
    }
    return ["msg" => "Import KGB Selesai!\\n✅ Berhasil: $berhasil\\n⚠️ NIP Tidak Dikenal: $skip\\n❌ Gagal: $gagal"];
}

// ==========================================
// 13. IMPORT KP (UPDATE: PAKAI NAMA KOLOM)
// ==========================================
function importKP($json_data) {
    global $conn;
    $data = json_decode($json_data, true);
    if (empty($data)) return ["status" => "error", "msg" => "Data Excel kosong/Format salah"];

    $berhasil = 0; $gagal = 0; $skip = 0;

    foreach ($data as $row) {
        $nip = htmlspecialchars($row['NIP'] ?? '');
        if(empty($nip)) { $gagal++; continue; }

        $cek = mysqli_query($conn, "SELECT id FROM pegawai WHERE nip = '$nip'");
        $peg = mysqli_fetch_assoc($cek);

        if ($peg) {
            $pegawai_id = $peg['id'];
            $akhir      = excelDateToSQL($row['KP Terakhir'] ?? '');
            $yad        = excelDateToSQL($row['KP YAD'] ?? '');
            $ket        = htmlspecialchars($row['Ket'] ?? '');

            // Gunakan Nama Kolom Eksplisit
            $query = "INSERT INTO data_kp (pegawai_id, kp_terakhir, kp_yad, keterangan) 
                      VALUES ('$pegawai_id', '$akhir', '$yad', '$ket')";
            
            if(mysqli_query($conn, $query)) $berhasil++;
            else $gagal++;
        } else {
            $skip++;
        }
    }
    return ["msg" => "Import KP Selesai!\\n✅ Berhasil: $berhasil\\n⚠️ NIP Tidak Dikenal: $skip\\n❌ Gagal: $gagal"];
}
?>