<?php
require '../../config/functions.php';

// Ambil Data PPPK
$data_pppk = query("SELECT * FROM dokumen_pppk ORDER BY id DESC");

// Logic Tambah (Upload)
if (isset($_POST["upload_data"])) {
    if (tambahPppk($_POST) > 0) {
        echo "<script>alert('File berhasil diupload!'); document.location.href = 'pppk.php';</script>";
    }
}

// Logic Hapus
if (isset($_GET["hapus"])) {
    if (hapusPppk($_GET["hapus"]) > 0) {
        echo "<script>alert('Data & File berhasil dihapus!'); document.location.href = 'pppk.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data!'); document.location.href = 'pppk.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kepegawaian - Data PPPK</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base_url; ?>assets/css/kepegawaian-kgb.css">
    
    <style>
        /* Modal Preview Ukuran Besar */
        .preview-modal-content {
            width: 80%;
            height: 90vh; /* Tinggi 90% dari layar */
            background: #fff;
            padding: 0; /* Nol padding agar full */
            border-radius: 12px;
            position: relative;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .preview-header {
            padding: 15px 20px;
            background: #f3f4f6;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .preview-body {
            flex: 1;
            background: #525659; /* Warna background abu-abu gelap ala PDF viewer */
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: auto;
        }

        /* Iframe untuk PDF */
        #previewFrame {
            width: 100%;
            height: 100%;
            border: none;
        }

        /* Image preview */
        #previewImage {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
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

        <h1 class="page-title">Kepegawaian - Data PPPK (Dokumen)</h1>
        <p class="page-sub">Kelola dokumen laporan PPPK dengan mengupload file.</p>

        <div class="box">
            <div class="top-actions">
                <button class="btn btn-primary" id="addDataBtn" style="background: #2563eb;">
                    <img src="<?= $base_url; ?>gambar/import.png" width="16" style="filter: brightness(0) invert(1);"> Upload Dokumen
                </button>
            </div>

            <div id="addDataModal" class="modal">
                <div class="add-modal-content" style="max-width:500px; background:#fff; color:#111;">
                    <span class="closeAddBtn" style="float:right;cursor:pointer;font-size:22px">&times;</span>
                    <h2 style="margin-bottom:15px;font-weight:200">Upload Dokumen PPPK</h2>
                    
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Judul Laporan / Dokumen</label>
                            <input type="text" name="judul" required placeholder="Contoh: Laporan Kinerja PPPK Bulan Januari" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;">
                        </div>
                        <div class="form-group">
                            <label>Pilih File</label>
                            <div style="border: 2px dashed #ccc; padding: 20px; text-align: center; border-radius: 8px; background: #f9fafb;">
                                <input type="file" name="file" required style="width:100%;">
                                <small style="display:block; margin-top:5px; color:gray;">Format: PDF, JPG, PNG (Max 10MB)</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea name="ket" rows="2" style="width:100%; border:1px solid #d1d5db; border-radius:10px; padding:10px;" placeholder="Keterangan tambahan..."></textarea>
                        </div>
                        <div class="add-footer">
                            <button type="button" class="add-cancel">Batal</button>
                            <button type="submit" name="upload_data" class="add-save">Upload</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="previewModal" class="modal" style="z-index: 100000;">
                <div class="preview-modal-content">
                    <div class="preview-header">
                        <h3 id="previewTitle" style="font-size:16px; margin:0;">Preview Dokumen</h3>
                        <span class="closePreviewBtn" style="cursor:pointer; font-size:24px;">&times;</span>
                    </div>
                    <div class="preview-body" id="previewContainer">
                        </div>
                </div>
            </div>

            <div class="search-box">
                <img src="<?= $base_url; ?>gambar/search.png" width="18">
                <input type="text" id="searchInput" placeholder="Cari dokumen...">
            </div>
            
            <div class="table-responsive">
                <table id="dataTable">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>JUDUL DOKUMEN</th>
                            <th>NAMA FILE</th>
                            <th>TANGGAL UPLOAD</th>
                            <th>KETERANGAN</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php $no = 1; ?>
                        <?php foreach ($data_pppk as $row) : 
                            $fileExt = strtolower(pathinfo($row['nama_file'], PATHINFO_EXTENSION));
                            $fileUrl = $base_url . $row['lokasi_file'];
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td style="font-weight:600; color:#2563eb;"><?= $row['judul_laporan']; ?></td>
                            <td><?= $row['nama_file']; ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_upload'])); ?></td>
                            <td><?= $row['keterangan']; ?></td>
                            <td>
                                <a href="javascript:void(0);" onclick="openPreview('<?= $fileUrl; ?>', '<?= $fileExt; ?>', '<?= $row['judul_laporan']; ?>')" title="Lihat File">
                                    <img src="<?= $base_url; ?>gambar/eye-open.png" class="aksi-btn" alt="Preview" style="width:20px;">
                                </a>

                                <a href="<?= $fileUrl; ?>" target="_blank" title="Download" download>
                                    <img src="<?= $base_url; ?>gambar/export.png" class="aksi-btn" alt="Download">
                                </a>

                                <a href="pppk.php?hapus=<?= $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus file ini?');" title="Hapus">
                                    <img src="<?= $base_url; ?>gambar/hapuss.png" class="aksi-btn" alt="Delete">
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
        // --- LOGIKA MODAL UPLOAD ---
        const modal = document.getElementById("addDataModal");
        document.getElementById("addDataBtn").onclick = () => { modal.style.display = "flex"; };
        document.querySelector(".closeAddBtn").onclick = () => modal.style.display = "none";
        document.querySelector(".add-cancel").onclick = () => modal.style.display = "none";

        // --- LOGIKA MODAL PREVIEW ---
        const previewModal = document.getElementById("previewModal");
        const closePreviewBtn = document.querySelector(".closePreviewBtn");
        const previewContainer = document.getElementById("previewContainer");
        const previewTitle = document.getElementById("previewTitle");

        function openPreview(url, ext, title) {
            previewTitle.innerText = "Preview: " + title;
            previewContainer.innerHTML = ""; // Bersihkan konten lama

            if (ext === 'pdf') {
                // Tampilkan PDF menggunakan Iframe
                previewContainer.innerHTML = `<iframe id="previewFrame" src="${url}"></iframe>`;
                previewModal.style.display = "flex";
            } 
            else if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
                // Tampilkan Gambar
                previewContainer.innerHTML = `<img id="previewImage" src="${url}">`;
                previewModal.style.display = "flex";
            } 
            else {
                // File Lain (Word/Excel) tidak bisa dipreview di browser tanpa plugin
                alert("Preview tidak tersedia untuk format file ini (" + ext + ").\nSilakan gunakan tombol Download.");
            }
        }

        // Tutup Modal Preview
        closePreviewBtn.onclick = () => {
            previewModal.style.display = "none";
            previewContainer.innerHTML = ""; // Hapus iframe agar video/pdf berhenti load
        };

        // Klik di luar modal menutup modal
        window.onclick = (e) => { 
            if (e.target == modal) modal.style.display = "none"; 
            if (e.target == previewModal) {
                previewModal.style.display = "none";
                previewContainer.innerHTML = "";
            }
        };

        // --- PENCARIAN ---
        document.getElementById("searchInput").addEventListener("keyup", function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll("#tableBody tr");
            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? "" : "none";
            });
        });
    </script>
</body>
</html>