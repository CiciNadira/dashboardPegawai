<?php
require '../../config/functions.php';

// === 1. LOGIC IMPORT ===
if (isset($_POST["excel_json"])) {
    $result = importKGB($_POST['excel_json']);
    $pesan = isset($result['msg']) ? $result['msg'] : "Terjadi kesalahan sistem.";
    echo "<script>alert('" . addslashes($pesan) . "'); document.location.href = 'kgb.php';</script>";
}

// === 2. LOGIC BAWAAN ===
$data_kgb = query("SELECT data_kgb.*, p.nama_lengkap, p.nip, p.golongan_akhir, p.jabatan 
                   FROM data_kgb JOIN pegawai p ON data_kgb.pegawai_id = p.id ORDER BY data_kgb.id DESC");
$list_pegawai = query("SELECT id, nama_lengkap, nip FROM pegawai ORDER BY nama_lengkap ASC");

if (isset($_POST["simpan_data"])) {
    if ($_POST['id'] != "") {
        if (ubahKgb($_POST) > 0) echo "<script>alert('Berhasil diubah!'); location.href='kgb.php';</script>";
        else echo "<script>alert('Gagal/Tidak ada perubahan!'); location.href='kgb.php';</script>";
    } else {
        if (tambahKgb($_POST) > 0) echo "<script>alert('Berhasil ditambah!'); location.href='kgb.php';</script>";
        else echo "<script>alert('Gagal ditambah!'); location.href='kgb.php';</script>";
    }
}
if (isset($_GET["hapus"])) {
    if (hapusKgb($_GET["hapus"]) > 0) echo "<script>alert('Berhasil dihapus!'); location.href='kgb.php';</script>";
    else echo "<script>alert('Gagal!'); location.href='kgb.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kepegawaian - Data KGB</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;400;600&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <link rel="stylesheet" href="<?= $base_url; ?>assets/css/kepegawaian-kgb.css">
    <style>
        /* UI KONSISTEN SDM */
        #importExcelModal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
        }

        #importExcelModal .modal-content {
            margin: 0;
        }
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

        <h1 class="page-title">Kepegawaian - Data KGB</h1>
        <p class="page-sub">Kelola data dengan menambah, mengubah, atau menghapus informasi</p>
        <div class="box">
            <div class="top-actions">
                <button id="importBtn" class="btn"><img src="<?= $base_url; ?>gambar/import.png" width="16">Import Excel</button>
                <button id="exportBtn" class="btn"><img src="<?= $base_url; ?>gambar/export.png" width="16"> Export Excel</button>
                <button class="btn btn-primary" id="addDataBtn">+ Tambah Data</button>
            </div>

            <form method="post" id="formImport" style="display:none;">
                <input type="hidden" name="excel_json" id="excelJson">
            </form>

            <div id="importExcelModal" class="modal">
                <div class="modal-content">
                    <span class="closeBtn">&times;</span>
                    <h2>Import Data KGB</h2>
                    <div class="format-box">
                        <p><b>Format Excel:</b></p>
                        <div class="tag-container">
                            <span class="tag">NIP</span>
                            <span class="tag">MKG</span>
                            <span class="tag">KGB Terakhir</span>
                            <span class="tag">KGB YAD</span>
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
                <div class="add-modal-content" style="max-width:600px; background:#fff; color:#111;">
                    <span class="closeAddBtn" style="float:right;cursor:pointer;font-size:22px">&times;</span>
                    <h2 id="formTitle">Tambah Data KGB</h2>

                    <form action="" method="post">
                        <input type="hidden" name="id" id="dataId">

                        <div class="form-group">
                            <label>Pilih Pegawai</label>
                            <select name="pegawai_id" id="pegawaiId" required style="width:100%; padding:10px;">
                                <option value="">-- Pilih Pegawai --</option>
                                <?php foreach ($list_pegawai as $p) : ?>
                                    <option value="<?= $p['id']; ?>"><?= $p['nama_lengkap']; ?> (NIP: <?= $p['nip']; ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group"><label>MKG (Masa Kerja Golongan)</label><input type="text" name="mkg" id="mkg" placeholder="Contoh: 10 Tahun 02 Bulan"></div>

                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                            <div class="form-group"><label>KGB Terakhir (Sesudah)</label><input type="date" name="kgb_terakhir" id="kgbTerakhir"></div>
                            <div class="form-group"><label>KGB YAD (Yang Akan Datang)</label><input type="date" name="kgb_yad" id="kgbYad"></div>
                        </div>

                        <div class="form-group"><label>Keterangan</label><textarea name="ket" id="ket" rows="2" style="width:100%; border:1px solid #d1d5db; padding:10px;"></textarea></div>

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
                            <th rowspan="2">MKG</th>
                            <th colspan="2">KGB</th>
                            <th rowspan="2">KET</th>
                            <th rowspan="2">AKSI</th>
                        </tr>
                        <tr>
                            <th style="color: #6b7280;">Sesudah</th>
                            <th style="color: #6b7280;">YAD</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php foreach ($data_kgb as $row) : ?>
                            <tr>
                                <td><?= $row['nama_lengkap']; ?></td>
                                <td><?= $row['nip']; ?></td>
                                <td><?= $row['golongan_akhir']; ?></td>
                                <td><?= $row['jabatan']; ?></td>
                                <td><?= $row['mkg']; ?></td>
                                <td><?= ($row['kgb_terakhir'] != '0000-00-00') ? date('d/m/Y', strtotime($row['kgb_terakhir'])) : '-'; ?></td>
                                <td><?= ($row['kgb_yad'] != '0000-00-00') ? date('d/m/Y', strtotime($row['kgb_yad'])) : '-'; ?></td>
                                <td><?= $row['keterangan']; ?></td>
                                <td>
                                    <img src="<?= $base_url; ?>gambar/edit.png" class="aksi-btn edit" onclick="editData(<?= $row['id']; ?>)">
                                    <a href="kgb.php?hapus=<?= $row['id']; ?>" onclick="return confirm('Hapus data?');"><img src="<?= $base_url; ?>gambar/hapuss.png" class="aksi-btn"></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const dbData = <?= json_encode($data_kgb); ?>;
        let globalExcelData = null;

        document.addEventListener('DOMContentLoaded', function() {
            // --- 1. IMPORT EXCEL LOGIC ---
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
                if (!file) return;
                const reader = new FileReader();
                reader.onload = function(e) {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, {
                        type: 'array'
                    });
                    const jsonData = XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]], {
                        raw: false,
                        dateNF: 'yyyy-mm-dd'
                    });
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
                if (confirm(`Yakin import ${globalExcelData.length} data?`)) {
                    document.getElementById("excelJson").value = JSON.stringify(globalExcelData);
                    document.getElementById("formImport").submit();
                }
            };

            // --- 2. DOWNLOAD TEMPLATE ---
            document.getElementById("downloadTemplate").addEventListener("click", function(e) {
                e.preventDefault();
                const headers = ["NIP", "MKG", "KGB Terakhir", "KGB YAD", "Ket"];
                const dummy = [
                    ["198501012010011001", "12 Tahun 02 Bulan", "2023-01-01", "2025-01-01", "Berkala"]
                ];
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet([headers, ...dummy]), "Template KGB");
                XLSX.writeFile(wb, "Template_KGB.xlsx");
            });

            // --- 3. CRUD LAINNYA ---
            const addModal = document.getElementById("addDataModal");
            document.getElementById("addDataBtn").onclick = () => {
                document.querySelector("form").reset();
                document.getElementById("dataId").value = "";
                document.getElementById("formTitle").innerText = "Tambah Data KGB";
                document.getElementById("saveBtn").innerText = "Simpan";
                addModal.style.display = "flex";
            };
            document.querySelector(".closeAddBtn").onclick = () => addModal.style.display = "none";
            document.querySelector(".add-cancel").onclick = () => addModal.style.display = "none";
            window.onclick = (e) => {
                if (e.target == addModal) addModal.style.display = "none";
                if (e.target == importModal) importModal.style.display = "none";
            };

            document.getElementById("searchInput").addEventListener("keyup", function() {
                let filter = this.value.toLowerCase();
                document.querySelectorAll("#tableBody tr").forEach(row => {
                    row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
                });
            });
            
            document.getElementById("exportBtn").addEventListener("click", function() {
                if (dbData.length === 0) return alert("Data kosong");

                const dataExport = dbData.map(row => ({
                    "Nama": row.nama_lengkap,
                    "NIP": row.nip,
                    "Pangkat/Gol": row.golongan_akhir,
                    "Jabatan": row.jabatan,
                    "MKG": row.mkg,
                    "KGB Terakhir (Sesudah)": row.kgb_terakhir,
                    "KGB YAD": row.kgb_yad,
                    "Keterangan": row.keterangan
                }));

                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, XLSX.utils.json_to_sheet(dataExport), "Data KGB");
                XLSX.writeFile(wb, "Data_KGB.xlsx");
            });
        });

        function editData(id) {
            const data = dbData.find(x => x.id == id);
            if (data) {
                document.getElementById("dataId").value = data.id;
                document.getElementById("pegawaiId").value = data.pegawai_id;
                document.getElementById("mkg").value = data.mkg;
                document.getElementById("kgbTerakhir").value = data.kgb_terakhir;
                document.getElementById("kgbYad").value = data.kgb_yad;
                document.getElementById("ket").value = data.keterangan;
                document.getElementById("formTitle").innerText = "Edit Data KGB";
                document.getElementById("saveBtn").innerText = "Update";
                document.getElementById("addDataModal").style.display = "flex";
            }
        }
    </script>
</body>

</html>