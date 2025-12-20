<?php
require '../../config/functions.php';

// === CONFIGURASI HALAMAN ===
$kategori = 'Lakin';
$judul_page = 'Laporan Lakin';
// ===========================

// Ambil Data Sesuai Kategori
$data_laporan = query("SELECT * FROM data_laporan WHERE kategori = '$kategori' ORDER BY id DESC");

// Logic Tambah
if (isset($_POST["simpan_data"])) {
    if (tambahLaporan($_POST) > 0) {
        echo "<script>alert('Laporan berhasil diupload!'); window.location.href='keuangan.php';</script>";
    }
}

// Logic Hapus
if (isset($_GET["hapus"])) {
    if (hapusLaporan($_GET["hapus"]) > 0) {
        echo "<script>alert('Laporan dihapus!'); window.location.href='keuangan.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $judul_page; ?> - BPS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base_url; ?>assets/css/kepegawaian-kgb.css">
    <style>
        /* Style Card Grid (Sama dengan PPPK/Sakip) */
        .doc-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-top: 20px; }
        .doc-card { 
            background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; padding: 20px; 
            display: flex; flex-direction: column; justify-content: space-between; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.02); transition: 0.3s; 
        }
        .doc-card:hover { transform: translateY(-5px); box-shadow: 0 12px 24px rgba(0,0,0,0.08); border-color: #3b82f6; }

        /* Icon File Besar */
        .file-icon { 
            width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; 
            font-size: 18px; font-weight: bold; margin-bottom: 15px; 
        }
        .icon-pdf { background: #fee2e2; color: #dc2626; }
        .icon-xls { background: #d1fae5; color: #059669; }
        .icon-img { background: #dbeafe; color: #2563eb; }
        .icon-doc { background: #f3f4f6; color: #4b5563; }

        /* Modal Preview */
        .preview-modal-content { width: 80%; height: 90vh; background: #fff; border-radius: 12px; display: flex; flex-direction: column; overflow: hidden; }
        .preview-body { flex: 1; background: #525659; display: flex; justify-content: center; align-items: center; }
        #previewFrame { width: 100%; height: 100%; border: none; }
        #previewImage { max-width: 100%; max-height: 100%; object-fit: contain; }
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

        <h1 class="page-title"><?= $judul_page; ?></h1>
        <p class="page-sub">Arsip dokumen <?= strtolower($judul_page); ?>.</p>

        <div class="box" style="background:transparent; border:none; padding:0; box-shadow:none;">
            <div class="top-actions" style="background:#fff; padding:20px; border-radius:16px; box-shadow:0 4px 12px rgba(0,0,0,0.05);">
                <div class="search-box" style="margin-bottom:0; flex:1;">
                    <img src="<?= $base_url; ?>gambar/search.png" width="18">
                    <input type="text" id="searchInput" placeholder="Cari laporan...">
                </div>
                <button class="btn btn-primary" id="addDataBtn" style="background: #2563eb;">+ Upload Laporan</button>
            </div>

            <div id="addDataModal" class="modal">
                <div class="add-modal-content" style="max-width:500px; background:#fff; color:#111;">
                    <span class="closeAddBtn" style="float:right;cursor:pointer;font-size:22px">&times;</span>
                    <h2 style="margin-bottom:15px;font-weight:200">Upload <?= $judul_page; ?></h2>
                    
                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="kategori" value="<?= $kategori; ?>">

                        <div class="form-group">
                            <label>Judul Laporan</label>
                            <input type="text" name="judul" required placeholder="Contoh: Laporan Keuangan Bulan Januari" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;">
                        </div>

                        <div class="form-group">
                            <label>Periode (Bulan/Tahun)</label>
                            <input type="text" name="periode" required placeholder="Contoh: Januari 2025" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;">
                        </div>

                        <div class="form-group">
                            <label>File Dokumen</label>
                            <div style="border: 2px dashed #ccc; padding: 15px; text-align: center; border-radius: 8px; background: #f9fafb;">
                                <input type="file" name="file" required style="width:100%;">
                                <small style="display:block; margin-top:5px; color:gray;">PDF / Excel / Gambar (Max 10MB)</small>
                            </div>
                        </div>

                        <div class="add-footer">
                            <button type="button" class="add-cancel">Batal</button>
                            <button type="submit" name="simpan_data" class="add-save">Upload</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="previewModal" class="modal" style="z-index: 100000;">
                <div class="preview-modal-content">
                    <div style="padding:15px; background:#f3f4f6; display:flex; justify-content:space-between; align-items:center;">
                        <h3 id="previewTitle" style="margin:0; font-size:16px;">Preview</h3>
                        <span class="closePreviewBtn" style="cursor:pointer; font-size:24px;">&times;</span>
                    </div>
                    <div class="preview-body" id="previewContainer"></div>
                </div>
            </div>

            <div class="doc-grid">
                <?php if (empty($data_laporan)) : ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #6b7280; background:#fff; border-radius:12px;">
                        <p>Belum ada laporan yang diupload.</p>
                    </div>
                <?php else : ?>
                    <?php foreach ($data_laporan as $row) : 
                        $fileUrl = $base_url . "uploads/laporan/" . $row['nama_file'];
                        $ext = strtolower(pathinfo($row['nama_file'], PATHINFO_EXTENSION));
                        
                        // Icon Type
                        $iconClass = 'icon-doc'; $typeText = 'DOC';
                        if ($ext == 'pdf') { $iconClass = 'icon-pdf'; $typeText = 'PDF'; }
                        if (in_array($ext, ['xls','xlsx'])) { $iconClass = 'icon-xls'; $typeText = 'XLS'; }
                        if (in_array($ext, ['jpg','png','jpeg'])) { $iconClass = 'icon-img'; $typeText = 'IMG'; }
                    ?>
                    <div class="doc-card">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                            <div class="file-icon <?= $iconClass; ?>"><?= $typeText; ?></div>
                            <span style="font-size:11px; background:#f3f4f6; padding:4px 8px; border-radius:6px; color:#6b7280;">
                                <?= $row['periode_bulan']; ?>
                            </span>
                        </div>
                        
                        <h4 style="margin:0 0 5px 0; color:#1f2937; font-size:16px; line-height:1.4;">
                            <?= $row['judul_laporan']; ?>
                        </h4>
                        <small style="color:#9ca3af; margin-bottom:15px; display:block;">
                            Diupload: <?= date('d M Y', strtotime($row['tanggal_upload'])); ?>
                        </small>

                        <div style="display:flex; gap:10px; border-top:1px solid #f3f4f6; padding-top:15px; margin-top:auto;">
                            <button onclick="openPreview('<?= $fileUrl; ?>', '<?= $ext; ?>')" style="flex:1; background:#eff6ff; color:#2563eb; border:none; padding:8px; border-radius:8px; cursor:pointer;">Preview</button>
                            
                            <a href="<?= $fileUrl; ?>" download style="background:#f0fdf4; color:#166534; padding:8px 10px; border-radius:8px; text-decoration:none;">
                                <img src="<?= $base_url; ?>gambar/export.png" width="14">
                            </a>

                            <a href="?hapus=<?= $row['id']; ?>" onclick="return confirm('Hapus laporan ini?');" style="background:#fef2f2; color:#dc2626; padding:8px 10px; border-radius:8px; text-decoration:none;">
                                <img src="<?= $base_url; ?>gambar/hapuss.png" width="14">
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById("addDataModal");
        const previewModal = document.getElementById("previewModal");
        const previewContainer = document.getElementById("previewContainer");

        document.getElementById("addDataBtn").onclick = () => modal.style.display = "flex";
        document.querySelector(".closeAddBtn").onclick = () => modal.style.display = "none";
        document.querySelector(".add-cancel").onclick = () => modal.style.display = "none";
        document.querySelector(".closePreviewBtn").onclick = () => previewModal.style.display = "none";
        
        window.onclick = (e) => { 
            if(e.target == modal) modal.style.display = "none";
            if(e.target == previewModal) previewModal.style.display = "none";
        };

        function openPreview(url, ext) {
            previewContainer.innerHTML = "";
            if(ext === 'pdf') previewContainer.innerHTML = `<iframe id="previewFrame" src="${url}"></iframe>`;
            else if(['jpg','jpeg','png'].includes(ext)) previewContainer.innerHTML = `<img id="previewImage" src="${url}">`;
            else alert("Preview tidak tersedia, silakan download.");
            previewModal.style.display = "flex";
        }

        document.getElementById("searchInput").addEventListener("keyup", function() {
            let filter = this.value.toLowerCase();
            document.querySelectorAll(".doc-card").forEach(card => {
                card.style.display = card.innerText.toLowerCase().includes(filter) ? "flex" : "none";
            });
        });
    </script>
</body>
</html>