<?php
require '../../config/functions.php';

if (isset($_POST["excel_json"])) {
    $result = importSDM($_POST['excel_json']);
    
    $pesan = "";
    if (isset($result['msg'])) {
        $pesan = $result['msg'];
    } elseif (isset($result['success'])) {
        $pesan = "Berhasil: " . $result['success'] . ", Gagal: " . $result['fail'];
    } else {
        $pesan = "Terjadi kesalahan sistem saat import.";
    }

    echo "<script>
            alert('" . addslashes($pesan) . "');
            document.location.href = 'sdm.php';
          </script>";
}

$pegawai = query("SELECT * FROM pegawai ORDER BY id DESC");

if (isset($_POST["simpan_data"])) {
    if ($_POST['id'] != "") {
        if (ubahPegawai($_POST) > 0) {
            echo "<script>alert('Data berhasil diubah!'); document.location.href = 'sdm.php';</script>";
        } else {
            echo "<script>alert('Data gagal diubah / Tidak ada perubahan!'); document.location.href = 'sdm.php';</script>";
        }
    } else {
        if (tambahPegawai($_POST) > 0) {
            echo "<script>alert('Data berhasil ditambahkan!'); document.location.href = 'sdm.php';</script>";
        } else {
            echo "<script>alert('Data gagal ditambahkan!'); document.location.href = 'sdm.php';</script>";
        }
    }
}

if (isset($_GET["hapus"])) {
    $id = $_GET["hapus"];
    if (hapusPegawai($id) > 0) {
        echo "<script>alert('Data berhasil dihapus!'); document.location.href = 'sdm.php';</script>";
    } else {
        echo "<script>alert('Data gagal dihapus!'); document.location.href = 'sdm.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kepegawaian - Data SDM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;400;600&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <link rel="stylesheet" href="<?= $base_url; ?>assets/css/kepegawaian-sdm.css">
    <style>
        #importExcelModal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
            
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
        
        <h1 class="page-title">Kepegawaian - Data SDM</h1>
        <p class="page-sub">Kelola data dengan menambah, mengubah, atau menghapus informasi</p>

        <div class="box">
            <div class="top-actions">
                <button id="importBtn" class="btn"><img src="<?= $base_url; ?>gambar/import.png" alt="Import" width="16">Import Excel</button>
                <button id="exportBtn" class="btn"><img src="<?= $base_url; ?>gambar/export.png" alt="Export" width="16">Export Excel</button>
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
                            <span class="tag">Nama</span><span class="tag">NIP BPS</span><span class="tag">NIP</span>
                            <span class="tag">Jabatan</span><span class="tag">TMT Jab.</span><span class="tag">Gol.Akhir</span>
                            <span class="tag">TMT Gol.</span><span class="tag">Status</span><span class="tag">Pend.(SK)</span>
                            <span class="tag">TMT CPNS</span><span class="tag">AK Terakhir Angka</span><span class="tag">AK Terakhir Tahun</span>
                            <span class="tag">AK Konversi Angka</span><span class="tag">AK Konversi Tahun</span><span class="tag">Ket</span>
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
                    <h2 id="formTitle" style="margin-bottom:15px;font-weight:200">Tambah Data SDM</h2>
                    <form action="" method="post">
                        <input type="hidden" name="id" id="dataId">
                        <div class="form-group"><label>Nama</label><input type="text" name="nama" id="addNama" required></div>
                        <div class="form-group"><label>NIP BPS</label><input type="text" name="nip_bps" id="addNipBps"></div>
                        <div class="form-group"><label>NIP</label><input type="text" name="nip" id="addNip" required></div>
                        <div class="form-group"><label>Jabatan</label><input type="text" name="jabatan" id="addJabatan"></div>
                        <div class="form-group"><label>TMT Jab.</label><input type="date" name="tmt_jab" id="addTmtJab"></div>
                        <div class="form-group"><label>Gol.Akhir</label><input type="text" name="gol_akhir" id="addGolAkhir"></div>
                        <div class="form-group"><label>TMT Gol.</label><input type="date" name="tmt_gol" id="addTmtGol"></div>
                        <div class="form-group"><label>Status</label>
                            <select name="status" id="addStatus">
                                <option value="PNS">PNS</option><option value="CPNS">CPNS</option><option value="PPPK">PPPK</option>
                                <option value="TB Dalam Negeri">TB Dalam Negeri</option><option value="TB Luar Negeri">TB Luar Negeri</option>
                                <option value="Mutasi">Mutasi</option><option value="Pensiun">Pensiun</option>
                            </select>
                        </div>
                        <div class="form-group"><label>Pend.(SK)</label><input type="text" name="pend_sk" id="addPendSk"></div>
                        <div class="form-group"><label>TMT CPNS</label><input type="date" name="tmt_cpns" id="addTmtCpns"></div>
                        <div class="form-group"><label>AK Terakhir Angka</label><input type="text" name="ak_terakhir_angka" id="addAkTerakhirAngka"></div>
                        <div class="form-group"><label>AK Terakhir Tahun</label><input type="number" name="ak_terakhir_tahun" id="addAkTerakhirTahun"></div>
                        <div class="form-group"><label>AK Konv Angka</label><input type="text" name="ak_konversi_angka" id="addAkKonversiAngka"></div>
                        <div class="form-group"><label>AK Konv Tahun</label><input type="number" name="ak_konversi_tahun" id="addAkKonversiTahun"></div>
                        <div class="form-group"><label>Ket</label><textarea name="ket" id="addKet" rows="2" style="width:100%; border:1px solid #d1d5db; border-radius:10px; padding:10px;"></textarea></div>
                        <div class="add-footer">
                            <button type="button" class="add-cancel">Batal</button>
                            <button type="submit" name="simpan_data" class="add-save" id="saveDataBtn">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="search-box">
                <img src="<?= $base_url; ?>gambar/search.png" width="18" alt="Search">
                <input type="text" placeholder="Cari data..." id="searchInput">
            </div>
            <div class="table-responsive">
                <table id="dataTable">
                    <thead>
                        <tr>
                            <th rowspan="2">NAMA</th><th rowspan="2">NIP BPS</th><th rowspan="2">NIP</th><th rowspan="2">JABATAN</th>
                            <th rowspan="2">TMT JAB.</th><th rowspan="2">GOL.AKHIR</th><th rowspan="2">TMT GOL.</th><th rowspan="2">STATUS</th>
                            <th rowspan="2">PEND.(SK)</th><th rowspan="2">TMT CPNS</th>
                            <th colspan="2">AK TERAKHIR</th><th colspan="2">AK KONVERSI</th>
                            <th rowspan="2">KET</th><th rowspan="2">AKSI</th>
                        </tr>
                        <tr>
                            <th style="color: #6b7280;">Angka</th><th style="color: #6b7280;">Tahun</th>
                            <th style="color: #6b7280;">Angka</th><th style="color: #6b7280;">Tahun</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php $i = 0; ?>
                        <?php foreach ($pegawai as $row) : ?>
                            <tr>
                                <td><?= $row["nama_lengkap"]; ?></td>
                                <td><?= $row["nip_bps"]; ?></td>
                                <td><?= $row["nip"]; ?></td>
                                <td><?= $row["jabatan"]; ?></td>
                                <td><?= ($row["tmt_jabatan"] == '0000-00-00') ? '-' : date('d/m/Y', strtotime($row["tmt_jabatan"])); ?></td>
                                <td><?= $row["golongan_akhir"]; ?></td>
                                <td><?= ($row["tmt_golongan"] == '0000-00-00') ? '-' : date('d/m/Y', strtotime($row["tmt_golongan"])); ?></td>
                                <td><span class="status-active"><?= $row["status_kepegawaian"]; ?></span></td>
                                <td><?= $row["pendidikan_sk"]; ?></td>
                                <td><?= ($row["tmt_cpns"] == '0000-00-00') ? '-' : date('d/m/Y', strtotime($row["tmt_cpns"])); ?></td>
                                <td><?= $row["ak_terakhir_angka"]; ?></td>
                                <td><?= $row["ak_terakhir_tahun"]; ?></td>
                                <td><?= $row["ak_konversi_angka"]; ?></td>
                                <td><?= $row["ak_konversi_tahun"]; ?></td>
                                <td><?= $row["keterangan"]; ?></td>
                                <td>
                                    <img src="<?= $base_url; ?>gambar/edit.png" class="aksi-btn edit" onclick="openEditModalById('<?= $row['id']; ?>')" alt="Edit">
                                    <a href="<?= $base_url; ?>pages/kepegawaian/sdm.php?hapus=<?= $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus data ini?');">
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
        const dbData = <?= json_encode($pegawai); ?>;
        let globalExcelData = null;

        document.addEventListener('DOMContentLoaded', function() {
            
            const importModal = document.getElementById("importExcelModal");
            const importBtn = document.getElementById("importBtn");
            const closeImportSpan = importModal.querySelector(".closeBtn");
            const cancelImportBtn = importModal.querySelector(".cancelBtn");
            const importDataBtn = importModal.querySelector(".importDataBtn");
            const uploadArea = document.getElementById("uploadArea");
            const fileInput = document.getElementById("fileInput");

            importBtn.addEventListener("click", () => {
                importModal.style.display = "flex";
                globalExcelData = null; 
                uploadArea.innerHTML = '<div class="upload-icon">⬆</div><p>Klik untuk memilih file Excel</p><small>Format: .xlsx atau .xls</small>';
                uploadArea.style.border = "2px dashed #ccc";
                fileInput.value = "";
            });

            const closeImportModal = () => importModal.style.display = "none";
            closeImportSpan.onclick = closeImportModal;
            cancelImportBtn.onclick = closeImportModal;

            uploadArea.onclick = () => fileInput.click();

            fileInput.addEventListener("change", function(e) {
                const file = e.target.files[0];
                if(!file) return;

                const reader = new FileReader();
                reader.onload = function(e) {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, {type: 'array'});
                    const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                    
                    const jsonData = XLSX.utils.sheet_to_json(firstSheet, { raw: false, dateNF: 'yyyy-mm-dd' });
                    
                    if (jsonData.length > 0) {
                        globalExcelData = jsonData;
                        uploadArea.innerHTML = `<div class="upload-icon" style="color:green">✅</div><p>File: ${file.name}</p><small>Siap import ${jsonData.length} data.</small>`;
                        uploadArea.style.border = "2px solid green";
                    } else {
                        alert("File Excel kosong!");
                    }
                };
                reader.readAsArrayBuffer(file);
            });

            importDataBtn.addEventListener("click", function() {
                if (!globalExcelData) {
                    alert("Pilih file dulu!");
                    return;
                }
                if(confirm(`Yakin import ${globalExcelData.length} data?`)) {
                    document.getElementById("excelJson").value = JSON.stringify(globalExcelData);
                    document.getElementById("formImport").submit();
                }
            });

            document.getElementById("downloadTemplate").addEventListener("click", function(e) {
                e.preventDefault();
                const headers = [
                    "Nama", "NIP BPS", "NIP", "Jabatan", "TMT Jabatan", "Gol Akhir", "TMT Golongan", "Status", 
                    "Pendidikan", "TMT CPNS", "AK Terakhir Angka", "AK Terakhir Tahun", "AK Konversi Angka", "AK Konversi Tahun", "Ket"
                ];
                const dummyData = [
                    ["Contoh Nama", "3400xxxx", "199001012020011001", "Statistisi", "2023-01-01", "III/a", "2022-04-01", "PNS", "S1", "2020-01-01", "12.5", "2023", "", "", "Data Contoh"]
                ];
                const wb = XLSX.utils.book_new();
                const ws = XLSX.utils.aoa_to_sheet([headers, ...dummyData]);
                XLSX.utils.book_append_sheet(wb, ws, "Template SDM");
                XLSX.writeFile(wb, "Template_Import_SDM.xlsx");
            });

            const addModal = document.getElementById("addDataModal");
            const addBtn = document.getElementById("addDataBtn");
            const closeAddBtn = addModal.querySelector(".closeAddBtn");
            const cancelAddBtn = addModal.querySelector(".add-cancel");

            addBtn.addEventListener("click", function() {
                document.getElementById("dataId").value = ""; 
                document.querySelector("#addDataModal form").reset();
                document.getElementById("formTitle").innerText = "Tambah Data SDM";
                document.getElementById("saveDataBtn").innerText = "Simpan";
                addModal.style.display = "flex";
            });

            closeAddBtn.onclick = () => addModal.style.display = "none";
            cancelAddBtn.onclick = () => addModal.style.display = "none";
            
            window.onclick = (e) => {
                if (e.target == addModal) addModal.style.display = "none";
                if (e.target == importModal) importModal.style.display = "none";
            };

            const searchInput = document.getElementById("searchInput");
            searchInput.addEventListener("keyup", function() {
                const keyword = this.value.toLowerCase();
                const filteredData = dbData.filter(row =>
                    (row.nama_lengkap && row.nama_lengkap.toLowerCase().includes(keyword)) ||
                    (row.nip && row.nip.toLowerCase().includes(keyword)) ||
                    (row.jabatan && row.jabatan.toLowerCase().includes(keyword))
                );
                renderTable(filteredData);
            });

            document.getElementById("exportBtn").addEventListener("click", function() {
                if (dbData.length === 0) { alert("Data kosong"); return; }
                const dataToExport = dbData.map(row => ({
                    "Nama": row.nama_lengkap, "NIP": row.nip, "Jabatan": row.jabatan
                }));
                const ws = XLSX.utils.json_to_sheet(dataToExport);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "Data SDM");
                XLSX.writeFile(wb, "Data_SDM_BPS.xlsx");
            });
        });

        function renderTable(data) {
            const tableBody = document.getElementById("tableBody");
            tableBody.innerHTML = "";
            if (data.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="16" style="text-align:center;">Data tidak ditemukan</td></tr>`;
                return;
            }
            data.forEach((row) => {
                let tmtJab = (row.tmt_jabatan && row.tmt_jabatan != '0000-00-00') ? new Date(row.tmt_jabatan).toLocaleDateString('id-ID') : '-';
                let tmtGol = (row.tmt_golongan && row.tmt_golongan != '0000-00-00') ? new Date(row.tmt_golongan).toLocaleDateString('id-ID') : '-';
                let tmtCpns = (row.tmt_cpns && row.tmt_cpns != '0000-00-00') ? new Date(row.tmt_cpns).toLocaleDateString('id-ID') : '-';

                let rowHtml = `
                <tr>
                    <td>${row.nama_lengkap}</td>
                    <td>${row.nip_bps || '-'}</td>
                    <td>${row.nip}</td>
                    <td>${row.jabatan || '-'}</td>
                    <td>${tmtJab}</td>
                    <td>${row.golongan_akhir || '-'}</td>
                    <td>${tmtGol}</td>
                    <td><span class="status-active">${row.status_kepegawaian}</span></td>
                    <td>${row.pendidikan_sk || '-'}</td>
                    <td>${tmtCpns}</td>
                    <td>${row.ak_terakhir_angka || ''}</td>
                    <td>${row.ak_terakhir_tahun || ''}</td>
                    <td>${row.ak_konversi_angka || ''}</td>
                    <td>${row.ak_konversi_tahun || ''}</td>
                    <td>${row.keterangan || ''}</td>
                    <td>
                        <img src="../../gambar/edit.png" class="aksi-btn edit" onclick="openEditModalById('${row.id}')">
                        <a href="sdm.php?hapus=${row.id}" onclick="return confirm('Yakin ingin menghapus data ini?');">
                            <img src="../../gambar/hapuss.png" class="aksi-btn">
                        </a>
                    </td>
                </tr>`;
                tableBody.innerHTML += rowHtml;
            });
        }

        function openEditModalById(id) {
            const data = dbData.find(item => item.id == id);
            if (data) {
                document.getElementById("dataId").value = data.id;
                document.getElementById("addNama").value = data.nama_lengkap;
                document.getElementById("addNipBps").value = data.nip_bps;
                document.getElementById("addNip").value = data.nip;
                document.getElementById("addJabatan").value = data.jabatan;
                document.getElementById("addTmtJab").value = data.tmt_jabatan;
                document.getElementById("addGolAkhir").value = data.golongan_akhir;
                document.getElementById("addTmtGol").value = data.tmt_golongan;
                document.getElementById("addStatus").value = data.status_kepegawaian;
                document.getElementById("addPendSk").value = data.pendidikan_sk;
                document.getElementById("addTmtCpns").value = data.tmt_cpns;
                document.getElementById("addAkTerakhirAngka").value = data.ak_terakhir_angka;
                document.getElementById("addAkTerakhirTahun").value = data.ak_terakhir_tahun;
                document.getElementById("addAkKonversiAngka").value = data.ak_konversi_angka;
                document.getElementById("addAkKonversiTahun").value = data.ak_konversi_tahun;
                document.getElementById("addKet").value = data.keterangan;

                document.getElementById("formTitle").innerText = "Edit Data SDM";
                document.getElementById("saveDataBtn").innerText = "Update";
                document.getElementById("addDataModal").style.display = "flex";
            }
        }
    </script>
</body>
</html>