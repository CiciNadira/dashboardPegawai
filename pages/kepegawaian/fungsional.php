<?php
require '../../config/functions.php';

// 1. Ambil Data Fungsional (JOIN dengan Tabel Pegawai agar muncul Nama & NIP)
$fungsional = query("SELECT data_fungsional.*, 
                            pegawai.nama_lengkap, 
                            pegawai.nip, 
                            pegawai.jabatan, 
                            pegawai.golongan_akhir 
                     FROM data_fungsional 
                     JOIN pegawai ON data_fungsional.pegawai_id = pegawai.id 
                     ORDER BY data_fungsional.id DESC");

// 2. Ambil List Semua Pegawai (Untuk Dropdown di Form Tambah)
$list_pegawai = query("SELECT id, nama_lengkap, nip FROM pegawai ORDER BY nama_lengkap ASC");

// 3. Logika Simpan (Tambah/Edit)
if (isset($_POST["simpan_data"])) {
    if ($_POST['id'] != "") {
        // Mode Edit
        if (ubahFungsional($_POST) > 0) {
            echo "<script>alert('Data berhasil diubah!'); document.location.href = 'fungsional.php';</script>";
        } else {
            echo "<script>alert('Data gagal diubah / Tidak ada perubahan!'); document.location.href = 'fungsional.php';</script>";
        }
    } else {
        // Mode Tambah
        if (tambahFungsional($_POST) > 0) {
            echo "<script>alert('Data berhasil ditambahkan!'); document.location.href = 'fungsional.php';</script>";
        } else {
            echo "<script>alert('Data gagal ditambahkan!'); document.location.href = 'fungsional.php';</script>";
        }
    }
}

// 4. Logika Hapus
if (isset($_GET["hapus"])) {
    $id = $_GET["hapus"];
    if (hapusFungsional($id) > 0) {
        echo "<script>alert('Data berhasil dihapus!'); document.location.href = 'fungsional.php';</script>";
    } else {
        echo "<script>alert('Data gagal dihapus!'); document.location.href = 'fungsional.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kepegawaian - Data Fungsional</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;400;600&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <link rel="stylesheet" href="<?= $base_url; ?>assets/css/kepegawaian-fungsional.css">
</head>

<body>

    <?php include '../../layout/sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <div class="header-top" style="display:flex;justify-content:space-between;align-items:center;">
                <div class="header-left" style="display:flex;align-items:center;gap:16px;">
                    <img src="<?= $base_url; ?>gambar/logo.png" class="bps-logo" alt="Logo BPS">
                    <div>
                        <h2 class="bps-title">Badan Pusat Statistik<br>Kota Pontianak</h2>
                        <p class="bps-subtitle">Sistem Informasi Kepegawaian Internal</p>
                    </div>
                </div>
            </div>
        </div>

        <h1 class="page-title">Kepegawaian - Data Fungsional</h1>
        <p class="page-sub">Kelola data fungsional pegawai</p>

        <div class="box">
            <div class="top-actions">
                <button id="exportBtn" class="btn"><img src="<?= $base_url; ?>gambar/export.png" width="16">Export Excel</button>
                <button class="btn btn-primary" id="addDataBtn">+ Tambah Data</button>
            </div>

            <div id="addDataModal" class="modal">
                <div class="add-modal-content" style="max-width:600px;background:#fff;color:#111;">
                    <span class="closeAddBtn" style="float:right;cursor:pointer;font-size:22px">&times;</span>
                    <h2 id="formTitle" style="margin-bottom:15px;font-weight:200">Tambah Data Fungsional</h2>
                    
                    <form action="" method="post">
                        <input type="hidden" name="id" id="dataId">

                        <div class="form-group">
                            <label>Pilih Pegawai</label>
                            <select name="pegawai_id" id="addPegawaiId" required style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;">
                                <option value="">-- Cari Nama Pegawai --</option>
                                <?php foreach ($list_pegawai as $p) : ?>
                                    <option value="<?= $p['id']; ?>">
                                        <?= $p['nama_lengkap']; ?> (NIP: <?= $p['nip']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small style="color:gray; font-size:12px;">*Nama, NIP, Jabatan & Pangkat otomatis terhubung dari Data SDM</small>
                        </div>

                        <div class="form-group">
                            <label>TMT Fungsional</label>
                            <input type="date" name="tmt_fungsional" id="addTmtFungsional">
                        </div>

                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                            <div class="form-group">
                                <label>AK Terakhir (Angka)</label>
                                <input type="text" name="ak_terakhir_angka" id="addAkTerakhirAngka" placeholder="0.000">
                            </div>
                            <div class="form-group">
                                <label>AK Terakhir (Tahun)</label>
                                <input type="number" name="ak_terakhir_tahun" id="addAkTerakhirTahun" placeholder="YYYY">
                            </div>
                        </div>

                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                            <div class="form-group">
                                <label>AK Konversi (Angka)</label>
                                <input type="text" name="ak_konversi_angka" id="addAkKonversiAngka" placeholder="0.000">
                            </div>
                            <div class="form-group">
                                <label>AK Konversi (Tahun)</label>
                                <input type="number" name="ak_konversi_tahun" id="addAkKonversiTahun" placeholder="YYYY">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea name="ket" id="addKet" rows="2" style="width:100%; border:1px solid #d1d5db; border-radius:10px; padding:10px;"></textarea>
                        </div>

                        <div class="add-footer">
                            <button type="button" class="add-cancel">Batal</button>
                            <button type="submit" name="simpan_data" class="add-save" id="saveDataBtn">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="search-box">
                <img src="<?= $base_url; ?>gambar/search.png" width="18">
                <input type="text" placeholder="Cari nama atau NIP..." id="searchInput">
            </div>

            <div class="table-responsive">
                <table id="dataTable">
                    <thead>
                        <tr>
                            <th rowspan="2">NAMA</th>
                            <th rowspan="2">NIP</th>
                            <th rowspan="2">PANGKAT/GOL</th>
                            <th rowspan="2">JABATAN</th>
                            <th rowspan="2">TMT FUNGSIONAL</th>
                            <th colspan="2">AK TERAKHIR</th>
                            <th colspan="2">AK KONVERSI</th>
                            <th rowspan="2">KET</th>
                            <th rowspan="2">AKSI</th>
                        </tr>
                        <tr>
                            <th style="color: #6b7280;">Angka</th>
                            <th style="color: #6b7280;">Tahun</th>
                            <th style="color: #6b7280;">Angka</th>
                            <th style="color: #6b7280;">Tahun</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php $i = 0; ?>
                        <?php foreach ($fungsional as $row) : ?>
                            <tr>
                                <td><?= $row["nama_lengkap"]; ?></td>
                                <td><?= $row["nip"]; ?></td>
                                <td><?= $row["golongan_akhir"]; ?></td>
                                <td><?= $row["jabatan"]; ?></td>
                                
                                <td><?= ($row["tmt_fungsional"] != '0000-00-00') ? date('d/m/Y', strtotime($row["tmt_fungsional"])) : '-'; ?></td>
                                <td><?= $row["ak_terakhir_angka"]; ?></td>
                                <td><?= $row["ak_terakhir_tahun"]; ?></td>
                                <td><?= $row["ak_konversi_angka"]; ?></td>
                                <td><?= $row["ak_konversi_tahun"]; ?></td>
                                <td><?= $row["keterangan"]; ?></td>
                                <td>
                                    <img src="<?= $base_url; ?>gambar/edit.png" class="aksi-btn edit" onclick="openEditModal(<?= $i; ?>)" alt="Edit">
                                    <a href="fungsional.php?hapus=<?= $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus?');">
                                        <img src="<?= $base_url; ?>gambar/hapuss.png" class="aksi-btn" alt="Delete">
                                    </a>
                                </td>
                            </tr>
                        <?php $i++; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Data Database ke JS untuk fitur Edit & Search
        const dbData = <?= json_encode($fungsional); ?>;

        document.addEventListener('DOMContentLoaded', function() {
            // Modal Logic
            const modal = document.getElementById("addDataModal");
            const addBtn = document.getElementById("addDataBtn");
            const closeBtn = document.querySelector(".closeAddBtn");
            const cancelBtn = document.querySelector(".add-cancel");

            addBtn.addEventListener("click", function() {
                document.getElementById("dataId").value = "";
                document.querySelector("form").reset();
                document.getElementById("formTitle").innerText = "Tambah Data Fungsional";
                document.getElementById("saveDataBtn").innerText = "Simpan";
                modal.style.display = "flex";
            });

            closeBtn.onclick = () => modal.style.display = "none";
            cancelBtn.onclick = () => modal.style.display = "none";
            window.onclick = (e) => { if (e.target == modal) modal.style.display = "none"; };

            // Live Search
            const searchInput = document.getElementById("searchInput");
            const tableBody = document.getElementById("tableBody");

            searchInput.addEventListener("keyup", function() {
                const keyword = this.value.toLowerCase();
                const filteredData = dbData.filter(row => 
                    (row.nama_lengkap && row.nama_lengkap.toLowerCase().includes(keyword)) ||
                    (row.nip && row.nip.toLowerCase().includes(keyword))
                );
                renderTable(filteredData);
            });
            
            // Export Excel
             document.getElementById("exportBtn").addEventListener("click", function() {
                if (dbData.length === 0) { alert("Data kosong"); return; }
                const dataToExport = dbData.map(row => ({
                    "Nama": row.nama_lengkap,
                    "NIP": row.nip,
                    "Pangkat": row.golongan_akhir,
                    "Jabatan": row.jabatan,
                    "TMT Fungsional": row.tmt_fungsional,
                    "AK Terakhir": row.ak_terakhir_angka,
                    "Ket": row.keterangan
                }));
                const ws = XLSX.utils.json_to_sheet(dataToExport);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "Fungsional");
                XLSX.writeFile(wb, "Data_Fungsional.xlsx");
            });
        });

        // Render Table untuk Search
        function renderTable(data) {
            const tableBody = document.getElementById("tableBody");
            tableBody.innerHTML = "";
            if (data.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="11" style="text-align:center;">Data tidak ditemukan</td></tr>`;
                return;
            }
            data.forEach(row => {
                let tmt = (row.tmt_fungsional && row.tmt_fungsional !== '0000-00-00') ? new Date(row.tmt_fungsional).toLocaleDateString('id-ID') : '-';
                let html = `
                    <tr>
                        <td>${row.nama_lengkap}</td>
                        <td>${row.nip}</td>
                        <td>${row.golongan_akhir || '-'}</td>
                        <td>${row.jabatan || '-'}</td>
                        <td>${tmt}</td>
                        <td>${row.ak_terakhir_angka || ''}</td>
                        <td>${row.ak_terakhir_tahun || ''}</td>
                        <td>${row.ak_konversi_angka || ''}</td>
                        <td>${row.ak_konversi_tahun || ''}</td>
                        <td>${row.keterangan || ''}</td>
                        <td>
                            <img src="<?= $base_url; ?>gambar/edit.png" class="aksi-btn" onclick="openEditModalById(${row.id})">
                            <a href="fungsional.php?hapus=${row.id}" onclick="return confirm('Hapus?');">
                                <img src="<?= $base_url; ?>gambar/hapuss.png" class="aksi-btn">
                            </a>
                        </td>
                    </tr>
                `;
                tableBody.innerHTML += html;
            });
        }

        function openEditModal(index) {
            const data = dbData[index];
            fillForm(data);
        }
        
        function openEditModalById(id) {
            const data = dbData.find(item => item.id == id);
            if(data) fillForm(data);
        }

        function fillForm(data) {
            document.getElementById("dataId").value = data.id;
            document.getElementById("addPegawaiId").value = data.pegawai_id; // Select Dropdown otomatis terpilih
            document.getElementById("addTmtFungsional").value = data.tmt_fungsional;
            document.getElementById("addAkTerakhirAngka").value = data.ak_terakhir_angka;
            document.getElementById("addAkTerakhirTahun").value = data.ak_terakhir_tahun;
            document.getElementById("addAkKonversiAngka").value = data.ak_konversi_angka;
            document.getElementById("addAkKonversiTahun").value = data.ak_konversi_tahun;
            document.getElementById("addKet").value = data.keterangan;

            document.getElementById("formTitle").innerText = "Edit Data Fungsional";
            document.getElementById("saveDataBtn").innerText = "Update";
            document.getElementById("addDataModal").style.display = "flex";
        }
    </script>
</body>
</html>