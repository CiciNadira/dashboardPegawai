<?php
require '../../config/functions.php';

// Ambil Data KP + Info Pegawai (JOIN)
$data_kp = query("SELECT data_kp.*, p.nama_lengkap, p.nip, p.golongan_akhir, p.jabatan 
                  FROM data_kp 
                  JOIN pegawai p ON data_kp.pegawai_id = p.id 
                  ORDER BY data_kp.id DESC");

// List Pegawai untuk Dropdown
$list_pegawai = query("SELECT id, nama_lengkap, nip FROM pegawai ORDER BY nama_lengkap ASC");

// Logic CRUD
if (isset($_POST["simpan_data"])) {
    if ($_POST['id'] != "") {
        // Mode Edit
        if (ubahKp($_POST) > 0) echo "<script>alert('Berhasil diubah!'); location.href='kp.php';</script>";
        else echo "<script>alert('Gagal/Tidak ada perubahan!'); location.href='kp.php';</script>";
    } else {
        // Mode Tambah
        if (tambahKp($_POST) > 0) echo "<script>alert('Berhasil ditambah!'); location.href='kp.php';</script>";
        else echo "<script>alert('Gagal ditambah!'); location.href='kp.php';</script>";
    }
}

// Logic Hapus
if (isset($_GET["hapus"])) {
    if (hapusKp($_GET["hapus"]) > 0) echo "<script>alert('Berhasil dihapus!'); location.href='kp.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kepegawaian - Data KP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;400;600&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <link rel="stylesheet" href="<?= $base_url; ?>assets/css/kepegawaian-kgb.css">
</head>
<body>
    <?php include '../../layout/sidebar.php'; ?>

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

        <h1 class="page-title">Kepegawaian - Data Kenaikan Pangkat (KP)</h1>
        <p class="page-sub">Kelola data dengan menambah, mengubah, atau menghapus informasi</p>
        <div class="box">
            <div class="top-actions">
                <button id="exportBtn" class="btn"><img src="<?= $base_url; ?>gambar/export.png" width="16"> Export Excel</button>
                <button class="btn btn-primary" id="addDataBtn">+ Tambah Data</button>
            </div>

            <div id="addDataModal" class="modal">
                <div class="add-modal-content" style="max-width:600px; background:#fff; color:#111;">
                    <span class="closeAddBtn" style="float:right;cursor:pointer;font-size:22px">&times;</span>
                    <h2 id="formTitle" style="margin-bottom:15px;font-weight:200">Tambah Data KP</h2>
                    
                    <form action="" method="post">
                        <input type="hidden" name="id" id="dataId">
                        
                        <div class="form-group">
                            <label>Pilih Pegawai</label>
                            <select name="pegawai_id" id="pegawaiId" required style="width:100%; padding:10px; border-radius:10px; border:1px solid #d1d5db;">
                                <option value="">-- Pilih Pegawai --</option>
                                <?php foreach ($list_pegawai as $p) : ?>
                                    <option value="<?= $p['id']; ?>"><?= $p['nama_lengkap']; ?> (NIP: <?= $p['nip']; ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                            <div class="form-group">
                                <label>KP Terakhir</label>
                                <input type="date" name="kp_terakhir" id="kpTerakhir">
                            </div>
                            <div class="form-group">
                                <label>KP YAD (Yang Akan Datang)</label>
                                <input type="date" name="kp_yad" id="kpYad">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea name="ket" id="ket" rows="2" style="width:100%; border:1px solid #d1d5db; border-radius:10px; padding:10px;" placeholder="Keterangan tambahan..."></textarea>
                        </div>

                        <div class="add-footer">
                            <button type="button" class="add-cancel">Batal</button>
                            <button type="submit" name="simpan_data" class="add-save" id="saveBtn">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="search-box">
                <img src="<?= $base_url; ?>gambar/search.png" width="18">
                <input type="text" id="searchInput" placeholder="Cari data...">
            </div>
            
            <div class="table-responsive">
                <table id="dataTable">
                    <thead>
                        <tr>
                            <th rowspan="2">NAMA</th>
                            <th rowspan="2">NIP</th>
                            <th rowspan="2">PANGKAT/GOL</th>
                            <th rowspan="2">JABATAN</th>
                            <th colspan="2">KENAIKAN PANGKAT</th> <th rowspan="2">KET</th>
                            <th rowspan="2">AKSI</th>
                        </tr>
                        <tr>
                            <th style="color: #6b7280;">Terakhir</th>
                            <th style="color: #6b7280;">YAD</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php foreach ($data_kp as $row) : ?>
                        <tr>
                            <td><?= $row['nama_lengkap']; ?></td>
                            <td><?= $row['nip']; ?></td>
                            <td><?= $row['golongan_akhir']; ?></td>
                            <td><?= $row['jabatan']; ?></td>
                            <td><?= ($row['kp_terakhir'] != '0000-00-00') ? date('d/m/Y', strtotime($row['kp_terakhir'])) : '-'; ?></td>
                            <td><?= ($row['kp_yad'] != '0000-00-00') ? date('d/m/Y', strtotime($row['kp_yad'])) : '-'; ?></td>
                            <td><?= $row['keterangan']; ?></td>
                            <td>
                                <img src="<?= $base_url; ?>gambar/edit.png" class="aksi-btn edit" onclick="editData(<?= $row['id']; ?>)">
                                <a href="kp.php?hapus=<?= $row['id']; ?>" onclick="return confirm('Hapus data?');">
                                    <img src="<?= $base_url; ?>gambar/hapuss.png" class="aksi-btn">
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const dbData = <?= json_encode($data_kp); ?>;
        const modal = document.getElementById("addDataModal");

        // Buka Modal Tambah
        document.getElementById("addDataBtn").onclick = () => {
            document.querySelector("form").reset();
            document.getElementById("dataId").value = "";
            document.getElementById("formTitle").innerText = "Tambah Data KP";
            document.getElementById("saveBtn").innerText = "Simpan";
            modal.style.display = "flex";
        };

        // Tutup Modal
        document.querySelector(".closeAddBtn").onclick = () => modal.style.display = "none";
        document.querySelector(".add-cancel").onclick = () => modal.style.display = "none";
        window.onclick = (e) => { if (e.target == modal) modal.style.display = "none"; };

        // Fitur Pencarian (Live Search)
        document.getElementById("searchInput").addEventListener("keyup", function() {
            const keyword = this.value.toLowerCase();
            const tableBody = document.getElementById("tableBody");
            tableBody.innerHTML = "";

            const filtered = dbData.filter(row => 
                (row.nama_lengkap && row.nama_lengkap.toLowerCase().includes(keyword)) ||
                (row.nip && row.nip.toLowerCase().includes(keyword))
            );

            if (filtered.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="8" style="text-align:center;">Data tidak ditemukan</td></tr>`;
                return;
            }

            filtered.forEach(row => {
                let tgl1 = (row.kp_terakhir && row.kp_terakhir != '0000-00-00') ? new Date(row.kp_terakhir).toLocaleDateString('id-ID') : '-';
                let tgl2 = (row.kp_yad && row.kp_yad != '0000-00-00') ? new Date(row.kp_yad).toLocaleDateString('id-ID') : '-';
                
                let html = `
                    <tr>
                        <td>${row.nama_lengkap}</td>
                        <td>${row.nip}</td>
                        <td>${row.golongan_akhir || '-'}</td>
                        <td>${row.jabatan || '-'}</td>
                        <td>${tgl1}</td>
                        <td>${tgl2}</td>
                        <td>${row.keterangan || ''}</td>
                        <td>
                            <img src="<?= $base_url; ?>gambar/edit.png" class="aksi-btn" onclick="editData(${row.id})">
                            <a href="kp.php?hapus=${row.id}" onclick="return confirm('Hapus data?');">
                                <img src="<?= $base_url; ?>gambar/hapuss.png" class="aksi-btn">
                            </a>
                        </td>
                    </tr>
                `;
                tableBody.innerHTML += html;
            });
        });

        // Fungsi Edit Data
        function editData(id) {
            const data = dbData.find(x => x.id == id);
            if(data) {
                document.getElementById("dataId").value = data.id;
                document.getElementById("pegawaiId").value = data.pegawai_id;
                document.getElementById("kpTerakhir").value = data.kp_terakhir;
                document.getElementById("kpYad").value = data.kp_yad;
                document.getElementById("ket").value = data.keterangan;
                
                document.getElementById("formTitle").innerText = "Edit Data KP";
                document.getElementById("saveBtn").innerText = "Update";
                modal.style.display = "flex";
            }
        }

        // Fitur Export Excel
        document.getElementById("exportBtn").addEventListener("click", function() {
            if (dbData.length === 0) { alert("Data kosong"); return; }
            const dataToExport = dbData.map(row => ({
                "Nama": row.nama_lengkap,
                "NIP": row.nip,
                "Pangkat": row.golongan_akhir,
                "Jabatan": row.jabatan,
                "KP Terakhir": row.kp_terakhir,
                "KP YAD": row.kp_yad,
                "Ket": row.keterangan
            }));
            const ws = XLSX.utils.json_to_sheet(dataToExport);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Data KP");
            XLSX.writeFile(wb, "Data_KP.xlsx");
        });
    </script>
</body>
</html>