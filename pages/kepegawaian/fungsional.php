<?php
require '../../config/functions.php';

// === 1. LOGIC IMPORT EXCEL ===
if (isset($_POST["excel_json"])) {
    $result = importFungsional($_POST['excel_json']); // Panggil fungsi khusus
    
    $pesan = "";
    if (isset($result['msg'])) {
        $pesan = $result['msg'];
    } else {
        $pesan = "Terjadi kesalahan sistem.";
    }

    echo "<script>
            alert('" . addslashes($pesan) . "');
            document.location.href = 'fungsional.php';
          </script>";
}

// === 2. LOGIC BAWAAN ===
$fungsional = query("SELECT data_fungsional.*, 
                            pegawai.nama_lengkap, 
                            pegawai.nip, 
                            pegawai.jabatan, 
                            pegawai.golongan_akhir 
                     FROM data_fungsional 
                     JOIN pegawai ON data_fungsional.pegawai_id = pegawai.id 
                     ORDER BY data_fungsional.id DESC");

$list_pegawai = query("SELECT id, nama_lengkap, nip FROM pegawai ORDER BY nama_lengkap ASC");

if (isset($_POST["simpan_data"])) {
    if ($_POST['id'] != "") {
        if (ubahFungsional($_POST) > 0) echo "<script>alert('Data berhasil diubah!'); document.location.href = 'fungsional.php';</script>";
        else echo "<script>alert('Data gagal diubah / Tidak ada perubahan!'); document.location.href = 'fungsional.php';</script>";
    } else {
        if (tambahFungsional($_POST) > 0) echo "<script>alert('Data berhasil ditambahkan!'); document.location.href = 'fungsional.php';</script>";
        else echo "<script>alert('Data gagal ditambahkan!'); document.location.href = 'fungsional.php';</script>";
    }
}

if (isset($_GET["hapus"])) {
    if (hapusFungsional($_GET["hapus"]) > 0) echo "<script>alert('Data berhasil dihapus!'); document.location.href = 'fungsional.php';</script>";
    else echo "<script>alert('Data gagal dihapus!'); document.location.href = 'fungsional.php';</script>";
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
    <link rel="stylesheet" href="<?= $base_url; ?>assets/css/kepegawaian-sdm.css">
    <style>
        /* CSS MODAL IMPORT TENGAH (Sama seperti SDM) */
        #importExcelModal {
            display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%;
            overflow: auto; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center;
        }
        #importExcelModal .modal-content { margin: 0; }
    </style>
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

        <h1 class="page-title">Kepegawaian - Data Fungsional</h1>
        <p class="page-sub">Kelola data fungsional pegawai</p>

        <div class="box">
            <div class="top-actions">
                <button id="importBtn" class="btn"><img src="<?= $base_url; ?>gambar/import.png" width="16">Import Excel</button>
                <button id="exportBtn" class="btn"><img src="<?= $base_url; ?>gambar/export.png" width="16">Export Excel</button>
                <button class="btn btn-primary" id="addDataBtn">+ Tambah Data</button>
            </div>

            <form method="post" id="formImport" style="display:none;">
                <input type="hidden" name="excel_json" id="excelJson">
            </form>

            <div id="importExcelModal" class="modal">
                <div class="modal-content">
                    <span class="closeBtn">&times;</span>
                    <h2>Import Data dari Excel</h2>
                    <div class="format-box">
                        <p><b>Format Excel yang Dibutuhkan:</b></p>
                        <div class="tag-container">
                            <span class="tag">NIP</span>
                            <span class="tag">TMT Fungsional</span>
                            <span class="tag">AK Terakhir Angka</span>
                            <span class="tag">AK Terakhir Tahun</span>
                            <span class="tag">AK Konversi Angka</span>
                            <span class="tag">AK Konversi Tahun</span>
                            <span class="tag">Ket</span>
                        </div>
                        <a href="#" id="downloadTemplate" class="template-link">Download Template Excel</a>
                    </div>
                    <div id="uploadArea" class="upload-box">
                        <div class="upload-icon">⬆</div>
                        <p>Klik untuk memilih file Excel</p>
                        <small>Format: .xlsx atau .xls</small>
                    </div>
                    <div class="modal-footer">
                        <button class="cancelBtn">Batal</button>
                        <button class="importDataBtn">Import Data</button>
                    </div>
                    <input type="file" id="fileInput" accept=".xlsx,.xls" hidden>
                </div>
            </div>

            <div id="addDataModal" class="modal">
                <div class="add-modal-content" style="max-width:600px;background:#fff;color:#111;">
                    <span class="closeAddBtn" style="float:right;cursor:pointer;font-size:22px">&times;</span>
                    <h2 id="formTitle">Tambah Data Fungsional</h2>
                    <form action="" method="post">
                        <input type="hidden" name="id" id="dataId">
                        
                        <div class="form-group">
                            <label>Pilih Pegawai</label>
                            <select name="pegawai_id" id="addPegawaiId" required style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;">
                                <option value="">-- Cari Nama Pegawai --</option>
                                <?php foreach ($list_pegawai as $p) : ?>
                                    <option value="<?= $p['id']; ?>"><?= $p['nama_lengkap']; ?> (NIP: <?= $p['nip']; ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group"><label>TMT Fungsional</label><input type="date" name="tmt_fungsional" id="addTmtFungsional"></div>
                        
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                            <div class="form-group"><label>AK Terakhir (Angka)</label><input type="text" name="ak_terakhir_angka" id="addAkTerakhirAngka"></div>
                            <div class="form-group"><label>AK Terakhir (Tahun)</label><input type="number" name="ak_terakhir_tahun" id="addAkTerakhirTahun"></div>
                        </div>
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                            <div class="form-group"><label>AK Konv (Angka)</label><input type="text" name="ak_konversi_angka" id="addAkKonversiAngka"></div>
                            <div class="form-group"><label>AK Konv (Tahun)</label><input type="number" name="ak_konversi_tahun" id="addAkKonversiTahun"></div>
                        </div>
                        <div class="form-group"><label>Keterangan</label><textarea name="ket" id="addKet" rows="2" style="width:100%; border:1px solid #d1d5db; padding:10px;"></textarea></div>

                        <div class="add-footer">
                            <button type="button" class="add-cancel">Batal</button>
                            <button type="submit" name="simpan_data" class="add-save" id="saveDataBtn">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="search-box">
                <img src="<?= $base_url; ?>gambar/search.png" width="18"><input type="text" placeholder="Cari data..." id="searchInput">
            </div>
            <div class="table-responsive">
                <table id="dataTable">
                    <thead>
                        <tr>
                            <th rowspan="2">NAMA</th><th rowspan="2">NIP</th><th rowspan="2">PANGKAT</th><th rowspan="2">JABATAN</th>
                            <th rowspan="2">TMT JAB.</th><th colspan="2">AK TERAKHIR</th><th colspan="2">AK KONVERSI</th>
                            <th rowspan="2">KET</th><th rowspan="2">AKSI</th>
                        </tr>
                        <tr><th>Angka</th><th>Tahun</th><th>Angka</th><th>Tahun</th></tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php $i = 0; foreach ($fungsional as $row) : ?>
                            <tr>
                                <td><?= $row["nama_lengkap"]; ?></td><td><?= $row["nip"]; ?></td><td><?= $row["golongan_akhir"]; ?></td><td><?= $row["jabatan"]; ?></td>
                                <td><?= ($row["tmt_fungsional"] != '0000-00-00') ? date('d/m/Y', strtotime($row["tmt_fungsional"])) : '-'; ?></td>
                                <td><?= $row["ak_terakhir_angka"]; ?></td><td><?= $row["ak_terakhir_tahun"]; ?></td>
                                <td><?= $row["ak_konversi_angka"]; ?></td><td><?= $row["ak_konversi_tahun"]; ?></td>
                                <td><?= $row["keterangan"]; ?></td>
                                <td>
                                    <img src="<?= $base_url; ?>gambar/edit.png" class="aksi-btn edit" onclick="openEditModal(<?= $i; ?>)">
                                    <a href="fungsional.php?hapus=<?= $row['id']; ?>" onclick="return confirm('Hapus?');"><img src="<?= $base_url; ?>gambar/hapuss.png" class="aksi-btn"></a>
                                </td>
                            </tr>
                        <?php $i++; endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const dbData = <?= json_encode($fungsional); ?>;
        let globalExcelData = null;

        document.addEventListener('DOMContentLoaded', function() {
            // --- 1. IMPORT MODAL LOGIC (UI SDM) ---
            const importModal = document.getElementById("importExcelModal");
            const fileInput = document.getElementById("fileInput");
            const uploadArea = document.getElementById("uploadArea");

            document.getElementById("importBtn").onclick = () => {
                importModal.style.display = "flex";
                globalExcelData = null;
                uploadArea.innerHTML = '<div class="upload-icon">⬆</div><p>Klik untuk memilih file Excel</p><small>Format: .xlsx atau .xls</small>';
                uploadArea.style.border = "2px dashed #ccc";
                fileInput.value = "";
            };

            document.querySelector(".closeBtn").onclick = () => importModal.style.display = "none";
            document.querySelector(".cancelBtn").onclick = () => importModal.style.display = "none";
            uploadArea.onclick = () => fileInput.click();

            fileInput.addEventListener("change", function(e) {
                const file = e.target.files[0];
                if(!file) return;
                const reader = new FileReader();
                reader.onload = function(e) {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, {type: 'array'});
                    const jsonData = XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]], { raw: false, dateNF: 'yyyy-mm-dd' });
                    if (jsonData.length > 0) {
                        globalExcelData = jsonData;
                        uploadArea.innerHTML = `<div class="upload-icon" style="color:green">✅</div><p>File: ${file.name}</p><small>Siap import ${jsonData.length} data.</small>`;
                        uploadArea.style.border = "2px solid green";
                    }
                };
                reader.readAsArrayBuffer(file);
            });

            document.querySelector(".importDataBtn").onclick = () => {
                if (!globalExcelData) return alert("Pilih file dulu!");
                if(confirm(`Yakin import ${globalExcelData.length} data?`)) {
                    document.getElementById("excelJson").value = JSON.stringify(globalExcelData);
                    document.getElementById("formImport").submit();
                }
            };

            // --- 2. DOWNLOAD TEMPLATE ---
            document.getElementById("downloadTemplate").addEventListener("click", function(e) {
                e.preventDefault();
                const headers = ["NIP", "TMT Fungsional", "AK Terakhir Angka", "AK Terakhir Tahun", "AK Konversi Angka", "AK Konversi Tahun", "Ket"];
                const dummy = [["198501012010011001", "2023-01-01", "25.500", "2023", "", "", "Contoh"]];
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet([headers, ...dummy]), "Template Fungsional");
                XLSX.writeFile(wb, "Template_Fungsional.xlsx");
            });

            // --- 3. CRUD LAINNYA ---
            const addModal = document.getElementById("addDataModal");
            document.getElementById("addDataBtn").onclick = () => {
                document.querySelector("#addDataModal form").reset();
                document.getElementById("dataId").value = "";
                document.getElementById("formTitle").innerText = "Tambah Data Fungsional";
                document.getElementById("saveDataBtn").innerText = "Simpan";
                addModal.style.display = "flex";
            };
            document.querySelector(".closeAddBtn").onclick = () => addModal.style.display = "none";
            document.querySelector(".add-cancel").onclick = () => addModal.style.display = "none";
            window.onclick = (e) => {
                if(e.target == addModal) addModal.style.display = "none";
                if(e.target == importModal) importModal.style.display = "none";
            };

            document.getElementById("searchInput").addEventListener("keyup", function() {
                let filter = this.value.toLowerCase();
                document.querySelectorAll("#tableBody tr").forEach(row => {
                    row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
                });
            });

            document.getElementById("exportBtn").onclick = () => {
                if (dbData.length === 0) return alert("Data kosong");
                const dataExport = dbData.map(r => ({ "Nama": r.nama_lengkap, "NIP": r.nip, "TMT": r.tmt_fungsional }));
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, XLSX.utils.json_to_sheet(dataExport), "Data Fungsional");
                XLSX.writeFile(wb, "Data_Fungsional.xlsx");
            };
        });

        function openEditModal(index) {
            const data = dbData[index];
            document.getElementById("dataId").value = data.id;
            document.getElementById("addPegawaiId").value = data.pegawai_id;
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