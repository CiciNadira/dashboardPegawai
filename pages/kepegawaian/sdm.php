<?php
require '../../config/functions.php';

if (isset($_POST["import_data"])) {
    $result = importSDM($_POST['excel_json']);
    echo "<script>
            alert('Import Selesai! Berhasil: " . $result['success'] . ", Gagal/Duplikat: " . $result['fail'] . "');
            document.location.href = 'sdm.php';
          </script>";
}

$pegawai = query("SELECT * FROM pegawai ORDER BY id DESC");

if (isset($_POST["simpan_data"])) {

    if ($_POST['id'] != "") {
        if (ubahPegawai($_POST) > 0) {
            echo "<script>
                    alert('Data berhasil diubah!');
                    document.location.href = 'sdm.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Data gagal diubah / Tidak ada perubahan!');
                    document.location.href = 'sdm.php';
                  </script>";
        }
    } else {
        if (tambahPegawai($_POST) > 0) {
            echo "<script>
                    alert('Data berhasil ditambahkan!');
                    document.location.href = 'sdm.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Data gagal ditambahkan!');
                    document.location.href = 'sdm.php';
                  </script>";
        }
    }
}

if (isset($_GET["hapus"])) {
    $id = $_GET["hapus"];
    if (hapusPegawai($id) > 0) {
        echo "<script>
                alert('Data berhasil dihapus!');
                document.location.href = 'sdm.php';
              </script>";
    } else {
        echo "<script>
                alert('Data gagal dihapus!');
                document.location.href = 'sdm.php';
              </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<link>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Kepegawaian - Data SDM</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;400;600&display=swap" rel="stylesheet">
<!-- Tambahkan CDN untuk SheetJS (xlsx) untuk memproses file Excel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<link rel="stylesheet" href="<?= $base_url; ?>assets/css/kepegawaian-sdm.css">

</link>

<body>

    <!-- SIDEBAR -->

    <?php include '../../layout/sidebar.php'; ?>

    <!-- MAIN CONTENT -->
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
            <!-- POPUP IMPORT EXCEL -->
            <div id="importExcelModal" class="modal">
                <div class="modal-content">
                    <span class="closeBtn">&times;</span>
                    <h2>Import Data dari Excel</h2>
                    <!-- Format yang Dibutuhkan -->
                    <div class="format-box">
                        <p><b>Format Excel yang Dibutuhkan:</b></p>
                        <div class="tag-container">
                            <span class="tag">Nama</span>
                            <span class="tag">NIP BPS</span>
                            <span class="tag">NIP</span>
                            <span class="tag">Jabatan</span>
                            <span class="tag">TMT Jab.</span>
                            <span class="tag">Gol.Akhir</span>
                            <span class="tag">TMT Gol.</span>
                            <span class="tag">Status</span>
                            <span class="tag">Pend.(SK)</span>
                            <span class="tag">TMT CPNS</span>
                            <span class="tag">AK Terakhir Angka</span>
                            <span class="tag">AK Terakhir Tahun</span>
                            <span class="tag">AK Konversi Angka</span>
                            <span class="tag">AK Konversi Tahun</span>
                            <span class="tag">Ket</span>
                        </div>
                        <a href="#" id="downloadTemplate" class="template-link">Download Template Excel</a>
                    </div>
                    <!-- Upload Area -->
                    <div id="uploadArea" class="upload-box">
                        <div class="upload-icon">⬆</div>
                        <p>Klik untuk memilih file Excel</p>
                        <small>Format: .xlsx atau .xls</small>
                    </div>
                    <!-- Preview Area -->
                    <div id="previewArea" class="preview-area-box" style="display:none;">
                        <table id="previewTable"></table>
                    </div>
                    <div class="modal-footer">
                        <button class="cancelBtn">Batal</button>
                        <button class="importDataBtn">Import Data</button>
                    </div>
                    <input type="file" id="fileInput" accept=".xlsx,.xls" hidden>
                </div>
            </div>

            <!-- POPUP TAMBAH DATA (FINAL FIXED) -->
            <div id="addDataModal" class="modal">
                <div class="add-modal-content" style="max-width:600px;background:#fff;color:#111;">
                    <span class="closeAddBtn" style="float:right;cursor:pointer;font-size:22px">&times;</span>
                    <h2 id="formTitle" style="margin-bottom:15px;font-weight:200">Tambah Data SDM</h2>
                    <form action="" method="post">
                        <input type="hidden" name="id" id="dataId">

                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="nama" id="addNama" placeholder="Masukkan Nama Pegawai" required>
                        </div>

                        <div class="form-group" id="nipBpsGroup">
                            <label>NIP BPS</label>
                            <input type="text" name="nip_bps" id="addNipBps" placeholder="Masukkan NIP BPS">
                        </div>

                        <div class="form-group" id="nipGroup">
                            <label>NIP</label>
                            <input type="text" name="nip" id="addNip" placeholder="Masukkan NIP" required>
                        </div>

                        <div class="form-group">
                            <label>Jabatan</label>
                            <input type="text" name="jabatan" id="addJabatan" placeholder="Masukkan Jabatan">
                        </div>

                        <div class="form-group">
                            <label>TMT Jab.</label>
                            <input type="date" name="tmt_jab" id="addTmtJab">
                        </div>

                        <div class="form-group">
                            <label>Gol.Akhir</label>
                            <input type="text" name="gol_akhir" id="addGolAkhir" placeholder="Contoh: III/a">
                        </div>

                        <div class="form-group">
                            <label>TMT Gol.</label>
                            <input type="date" name="tmt_gol" id="addTmtGol">
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" id="addStatus">
                                <option value="CPNS">CPNS</option>
                                <option value="PNS">PNS</option>
                                <option value="PPPK">PPPK</option>
                                <option value="TB Dalam Negeri">TB Dalam Negeri</option>
                                <option value="TB Luar Negeri">TB Luar Negeri</option>
                                <option value="Mutasi">Mutasi</option>
                                <option value="Pensiun">Pensiun</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Pend.(SK)</label>
                            <input type="text" name="pend_sk" id="addPendSk" placeholder="Masukkan Pendidikan">
                        </div>

                        <div class="form-group">
                            <label>TMT CPNS</label>
                            <input type="date" name="tmt_cpns" id="addTmtCpns">
                        </div>

                        <div class="form-group">
                            <label>AK Terakhir Angka</label>
                            <input type="text" name="ak_terakhir_angka" id="addAkTerakhirAngka" placeholder="0.000">
                        </div>
                        <div class="form-group">
                            <label>AK Terakhir Tahun</label>
                            <input type="number" name="ak_terakhir_tahun" id="addAkTerakhirTahun" placeholder="Tahun">
                        </div>
                        <div class="form-group">
                            <label>AK Konversi Angka</label>
                            <input type="text" name="ak_konversi_angka" id="addAkKonversiAngka" placeholder="0.000">
                        </div>
                        <div class="form-group">
                            <label>AK Konversi Tahun</label>
                            <input type="number" name="ak_konversi_tahun" id="addAkKonversiTahun" placeholder="Tahun">
                        </div>

                        <div class="form-group">
                            <label>Ket</label>
                            <textarea name="ket" id="addKet" rows="2" style="width:100%; border:1px solid #d1d5db; border-radius:10px; padding:10px;" placeholder="Keterangan tambahan..."></textarea>
                        </div>

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
                            <th rowspan="2">NAMA</th>
                            <th rowspan="2">NIP BPS</th>
                            <th rowspan="2">NIP</th>
                            <th rowspan="2">JABATAN</th>
                            <th rowspan="2">TMT JAB.</th>
                            <th rowspan="2">GOL.AKHIR</th>
                            <th rowspan="2">TMT GOL.</th>
                            <th rowspan="2">STATUS</th>
                            <th rowspan="2">PEND.(SK)</th>
                            <th rowspan="2">TMT CPNS</th>
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
                        <?php foreach ($pegawai as $row) : ?>
                            <tr>
                                <td><?= $row["nama_lengkap"]; ?></td>
                                <td><?= $row["nip_bps"]; ?></td>
                                <td><?= $row["nip"]; ?></td>
                                <td><?= $row["jabatan"]; ?></td>
                                <td><?= date('d/m/Y', strtotime($row["tmt_jabatan"])); ?></td>
                                <td><?= $row["golongan_akhir"]; ?></td>
                                <td><?= date('d/m/Y', strtotime($row["tmt_golongan"])); ?></td>
                                <td><span class="status-active"><?= $row["status_kepegawaian"]; ?></span></td>
                                <td><?= $row["pendidikan_sk"]; ?></td>
                                <td><?= date('d/m/Y', strtotime($row["tmt_cpns"])); ?></td>
                                <td><?= $row["ak_terakhir_angka"]; ?></td>
                                <td><?= $row["ak_terakhir_tahun"]; ?></td>
                                <td><?= $row["ak_konversi_angka"]; ?></td>
                                <td><?= $row["ak_konversi_tahun"]; ?></td>
                                <td><?= $row["keterangan"]; ?></td>
                                <td>
                                    <img src="<?= $base_url; ?>gambar/edit.png" class="aksi-btn edit" onclick="openEditModal(<?= $i; ?>)" alt="Edit">
                                    <a href="<?= $base_url; ?>pages/kepegawaian/sdm.php?hapus=<?= $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus data ini?');">
                                        <img src="<?= $base_url; ?>gambar/hapuss.png" class="aksi-btn" alt="Delete">
                                    </a>
                                </td>
                            </tr>
                            <?php $i++; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- <tbody id="tableBody"> -->
                <!-- Data akan dimuat dari localStorage -->
                <!-- </tbody> -->
                </table>
            </div>
        </div>
    </div>
    <script>
        // 1. AMBIL DATA DARI PHP KE JAVASCRIPT
        // Kita butuh data ini untuk fitur Search & Export tanpa reload database
        const dbData = <?= json_encode($pegawai); ?>;

        document.addEventListener('DOMContentLoaded', function() {

            // --- LOGIKA MODAL TAMBAH/EDIT ---
            const modal = document.getElementById("addDataModal");
            const addBtn = document.getElementById("addDataBtn");
            const closeBtn = document.querySelector(".closeAddBtn");
            const cancelBtn = document.querySelector(".add-cancel");

            // Buka Modal Tambah
            addBtn.addEventListener("click", function() {
                document.getElementById("dataId").value = ""; // ID Kosong = Mode Tambah
                // Reset semua input form menjadi kosong
                document.querySelector("form").reset();

                document.getElementById("formTitle").innerText = "Tambah Data SDM";
                document.getElementById("saveDataBtn").innerText = "Simpan";
                modal.style.display = "flex";
            });

            // Tutup Modal
            closeBtn.onclick = () => modal.style.display = "none";
            cancelBtn.onclick = () => modal.style.display = "none";
            window.onclick = (e) => {
                if (e.target == modal) modal.style.display = "none";
            };

            // --- FITUR PENCARIAN (LIVE SEARCH) ---
            const searchInput = document.getElementById("searchInput");
            const tableBody = document.getElementById("tableBody");

            searchInput.addEventListener("keyup", function() {
                const keyword = this.value.toLowerCase();

                // Filter data di Javascript
                const filteredData = dbData.filter(row =>
                    (row.nama_lengkap && row.nama_lengkap.toLowerCase().includes(keyword)) ||
                    (row.nip && row.nip.toLowerCase().includes(keyword)) ||
                    (row.jabatan && row.jabatan.toLowerCase().includes(keyword))
                );

                renderTable(filteredData);
            });

            // --- FITUR EXPORT EXCEL ---
            document.getElementById("exportBtn").addEventListener("click", function() {
                if (dbData.length === 0) {
                    alert("Tidak ada data untuk di-export!");
                    return;
                }

                // Format data untuk Excel
                const dataToExport = dbData.map(row => ({
                    "Nama": row.nama_lengkap,
                    "NIP BPS": row.nip_bps,
                    "NIP": row.nip,
                    "Jabatan": row.jabatan,
                    "TMT Jabatan": row.tmt_jabatan,
                    "Gol. Akhir": row.golongan_akhir,
                    "TMT Golongan": row.tmt_golongan,
                    "Status": row.status_kepegawaian,
                    "Pendidikan": row.pendidikan_sk,
                    "TMT CPNS": row.tmt_cpns,
                    "AK Terakhir": row.ak_terakhir_angka,
                    "Ket": row.keterangan
                }));

                // Gunakan Library SheetJS
                const ws = XLSX.utils.json_to_sheet(dataToExport);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "Data SDM");
                XLSX.writeFile(wb, "Data_SDM_BPS.xlsx");
            });
        });

        // --- FUNGSI RENDER TABEL (Dipakai saat Search) ---
        function renderTable(data) {
            const tableBody = document.getElementById("tableBody");
            tableBody.innerHTML = ""; // Bersihkan tabel lama

            if (data.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="16" style="text-align:center;">Data tidak ditemukan</td></tr>`;
                return;
            }

            data.forEach((row, index) => {
                // Kita perlu link hapus & edit yang sesuai
                // Perhatikan penggunaan backtick (`) untuk template literal
                let rowHtml = `
                <tr>
                    <td>${row.nama_lengkap}</td>
                    <td>${row.nip_bps || '-'}</td>
                    <td>${row.nip}</td>
                    <td>${row.jabatan || '-'}</td>
                    <td>${formatDate(row.tmt_jabatan)}</td>
                    <td>${row.golongan_akhir || '-'}</td>
                    <td>${formatDate(row.tmt_golongan)}</td>
                    <td><span class="status-active">${row.status_kepegawaian}</span></td>
                    <td>${row.pendidikan_sk || '-'}</td>
                    <td>${formatDate(row.tmt_cpns)}</td>
                    <td>${row.ak_terakhir_angka || ''}</td>
                    <td>${row.ak_terakhir_tahun || ''}</td>
                    <td>${row.ak_konversi_angka || ''}</td>
                    <td>${row.ak_konversi_tahun || ''}</td>
                    <td>${row.keterangan || ''}</td>
                    <td>
                        <img src="../../gambar/edit.png" class="aksi-btn edit" onclick="openEditModalById('${row.id}')" alt="Edit">
                        <a href="sdm.php?hapus=${row.id}" onclick="return confirm('Yakin ingin menghapus data ini?');">
                            <img src="../../gambar/hapuss.png" class="aksi-btn" alt="Delete">
                        </a>
                    </td>
                </tr>
            `;
                tableBody.innerHTML += rowHtml;
            });
        }

        // Fungsi Format Tanggal (YYYY-MM-DD -> DD/MM/YYYY)
        function formatDate(dateString) {
            if (!dateString || dateString == '0000-00-00') return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID');
        }

        // --- FUNGSI EDIT DATA (Versi Baru: Cari berdasarkan ID) ---
        function openEditModal(index) {
            // Fungsi lama (berdasarkan index array PHP)
            // Kita alihkan ke openEditModalById agar konsisten saat di-search
            const data = dbData[index];
            fillEditForm(data);
        }

        // Fungsi helper untuk mencari data by ID (dipakai setelah search)
        function openEditModalById(id) {
            // Cari data di array dbData yang punya id sesuai
            const data = dbData.find(item => item.id == id);
            if (data) fillEditForm(data);
        }

        function fillEditForm(data) {
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
    </script>
</body>

</html>