# Sistem Informasi Kepegawaian & Administrasi Internal
**Badan Pusat Statistik (BPS) Kota Pontianak**

Platform berbasis web yang dirancang untuk efisiensi pengelolaan data Sumber Daya Manusia (SDM), administrasi kepegawaian (Kenaikan Pangkat, KGB, Fungsional), serta digitalisasi pengarsipan dokumen keuangan dan laporan kinerja.

---

## 📋 Fitur Unggulan

### 1. 📊 Dashboard Eksekutif
Menyajikan ringkasan data strategis dalam satu pandangan:
- **Visualisasi Data Interaktif:** Grafik status kepegawaian (*Pie Chart*) dan sebaran golongan (*Bar Chart*) berbasis *Chart.js*.
- **Statistik Real-time:** Menampilkan total pegawai (PNS/PPPK), jam digital, dan pintasan menu.
- **Manajemen Tim Dinamis:** Pengaturan struktur organisasi tim (Sekretaris, Bendahara, Staf) yang dapat disesuaikan.

### 2. 👥 Manajemen Kepegawaian (SDM)
Pengelolaan data pegawai secara komprehensif:
- **Database Terpusat:** Fitur CRUD (*Create, Read, Update, Delete*) untuk data pegawai.
- **Import Data Massal:** Dukungan import data Pegawai, Jabatan Fungsional, KGB, dan KP dari Excel menggunakan library *SheetJS*.
- **Export Laporan:** Unduh data dalam format Excel (.xlsx) sesuai tampilan tabel.
- **Riwayat Karir:** Pencatatan historis Kenaikan Pangkat (KP), Kenaikan Gaji Berkala (KGB), dan Jabatan Fungsional.

### 3. 📂 Manajemen Administrasi & Keuangan
Sistem pengarsipan digital yang terstruktur:
- **Arsip Keuangan:** Penyimpanan bukti dukung aplikasi (Spider, BOS, Sakti).
- **Dokumen Kinerja:** Pengelolaan dokumen Lakin, BMN, dan SAKIP.
- **Manajemen PPPK:** Repositori dokumen khusus untuk pegawai PPPK.

### 4. 🔐 Keamanan & Utilitas
- **Sistem Login Hybrid:** Mendukung kompatibilitas password lama (MD5) dan standar baru (Bcrypt Hash).
- **Pemulihan Akun Mandiri:** Fitur "Lupa Password" menggunakan mekanisme **Token Otorisasi** (tanpa ketergantungan server email).
- **Validasi File:** Filter keamanan otomatis untuk membatasi ekstensi dan ukuran file yang diunggah.

---

## 🛠️ Spesifikasi Teknis

| Komponen | Teknologi |
| :--- | :--- |
| **Backend** | PHP Native (Kompatibel v7.4 / 8.x) |
| **Database** | MySQL / MariaDB |
| **Frontend** | HTML5, CSS3, JavaScript (Vanilla) |
| **Library** | `Chart.js` (Visualisasi), `SheetJS / xlsx` (Pengolah Excel) |
| **Environment** | Laragon / XAMPP |

---

## 🚀 Panduan Instalasi & Konfigurasi

Ikuti langkah-langkah berikut untuk menjalankan aplikasi pada lingkungan lokal (*Localhost*).

### 1. Persiapan Database
1. Buka aplikasi manajemen database (**phpMyAdmin** atau **HeidiSQL**).
2. Buat database baru dengan nama: `bps_kepegawaian`.
3. Import file `bps_kepegawaian.sql` yang telah disertakan dalam paket proyek.

### 2. Konfigurasi Koneksi
Buka file `config/functions.php` dan sesuaikan kredensial database Anda:

```php
$conn = mysqli_connect("localhost", "root", "", "bps_kepegawaian");
```

### 3. Struktur Direktori Upload (PENTING!)
Pastikan struktur folder berikut tersedia secara manual agar fitur upload dokumen berfungsi dengan baik (karena folder kosong sering diabaikan oleh Git):

```text
/dashboardPegawai
└── /uploads
    ├── /keuangan
    ├── /laporan
    ├── /pppk
    └── /sakip
```

> **💡 Tips:** Tambahkan file kosong bernama `.gitkeep` di dalam setiap sub-folder di atas agar struktur folder tetap terbawa saat di-*push* ke repositori.

---

## 🔐 Panduan Akses & Keamanan

### Login Administrator
Masuk menggunakan *username* dan *password* yang telah terdaftar dalam database.

### Fitur Reset Password (Token Otorisasi)
Mengingat aplikasi berjalan di jaringan intranet/lokal tanpa server email, reset password menggunakan **Kode Otorisasi Manual**:

1. Klik tautan **"Lupa Password?"** pada halaman login.
2. Masukkan **Username** akun yang terkunci.
3. Masukkan **Kode Rahasia (Token)**.
4. Masukkan **Password Baru**.

#### 🔑 Konfigurasi Token
Token default sistem adalah:
`abangtampan`

> **⚠️ Catatan Keamanan:** Sangat disarankan untuk mengubah token ini pada file `config/functions.php` di variabel berikut:
> ```php
> $KODE_RAHASIA_SISTEM = "GantiDenganTokenRahasiaAnda"; 
> ```

---

## 📂 Panduan Import Data Excel

Gunakan tombol **"⬇ Download Template"** yang tersedia di setiap menu (SDM, KGB, KP, Fungsional) sebelum melakukan import.

**Ketentuan Penting:**
1. **Dilarang Mengubah Header:** Pastikan nama kolom header sama persis dengan template.
2. **Kunci Relasi (NIP):** Kolom NIP berfungsi sebagai *Foreign Key*.
3. **Urutan Import:** Pastikan NIP pegawai sudah terdaftar di menu **SDM** sebelum mengimpor data riwayat (KGB/KP/Fungsional). Jika NIP tidak ditemukan di data induk, import riwayat akan gagal.

---

## 📝 Informasi Pengembang

Aplikasi ini dikembangkan untuk kebutuhan internal Badan Pusat Statistik (BPS) Kota Pontianak.

* **Pengembang:** Tim Kerja Praktik - Teknik Informatika
* **Tahun Rilis:** 2025
* **Status:** *Maintenance & Development*

*Jika terjadi kendala teknis, silakan hubungi tim IT internal atau pengembang terkait.*