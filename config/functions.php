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
?>