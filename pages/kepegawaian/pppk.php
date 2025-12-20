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
        /* === CSS TAMBAHAN KHUSUS CARD LAYOUT === */
        
        /* Container Grid */
        .doc-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        /* Card Style */
        .doc-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.3s ease;
            position: relative;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        }

        .doc-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.08);
            border-color: #3b82f6;
        }

        /* Header Card (Icon + Tanggal) */
        .doc-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .file-icon-box {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
        }

        /* Warna Ikon Berdasarkan Tipe */
        .icon-pdf { background: #fee2e2; color: #ef4444; } /* Merah */
        .icon-img { background: #dbeafe; color: #2563eb; } /* Biru */
        .icon-doc { background: #e0e7ff; color: #4f46e5; } /* Ungu */
        .icon-xls { background: #d1fae5; color: #059669; } /* Hijau */
        .icon-zip { background: #fef3c7; color: #d97706; } /* Kuning */

        .doc-date {
            font-size: 12px;
            color: #6b7280;
            background: #f3f4f6;
            padding: 4px 8px;
            border-radius: 6px;
        }

        /* Body Card */
        .doc-body h4 {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 6px;
            line-height: 1.4;
            /* Membatasi teks jadi 2 baris lalu ... */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .doc-body p {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 20px;
            /* Batasi 3 baris */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Footer Card (Tombol Aksi) */
        .doc-footer {
            display: flex;
            gap: 8px;
            border-top: 1px solid #f3f4f6;
            padding-top: 15px;
        }

        .btn-card {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: .2s;
            text-decoration: none;
            gap: 5px;
        }

        .btn-preview { background: #eff6ff; color: #1d4ed8; }
        .btn-preview:hover { background: #dbeafe; }

        .btn-download { background: #f0fdf4; color: #15803d; }
        .btn-download:hover { background: #dcfce7; }

        .btn-delete { background: #fef2f2; color: #b91c1c; }
        .btn-delete:hover { background: #fee2e2; }


        /* === CSS MODAL PREVIEW (Tetap sama) === */
        .preview-modal-content {
            width: 80%;
            height: 90vh;
            background: #fff;
            padding: 0;
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
            background: #525659;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: auto;
        }
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

        <h1 class="page-title">Kepegawaian - Data PPPK (Dokumen)</h1>
        <p class="page-sub">Kelola dokumen laporan PPPK dengan tampilan kartu interaktif.</p>

        <div class="box" style="background: transparent; border:none; box-shadow:none; padding:0;">
            <div class="top-actions" style="background: #fff; padding: 20px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                <div class="search-box" style="margin-bottom:0; flex:1;">
                    <img src="<?= $base_url; ?>gambar/search.png" width="18">
                    <input type="text" id="searchInput" placeholder="Cari dokumen...">
                </div>
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
                            <input type="text" name="judul" required placeholder="Contoh: Laporan Kinerja Bulan Januari" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;">
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
                    <div class="preview-body" id="previewContainer"></div>
                </div>
            </div>

            <div class="doc-grid" id="docGrid">
                <?php if (empty($data_pppk)) : ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #6b7280; background:#fff; border-radius:12px;">
                        <img src="<?= $base_url; ?>gambar/import.png" style="width:40px; opacity:0.3; margin-bottom:10px;"><br>
                        Belum ada dokumen yang diupload.
                    </div>
                <?php else : ?>
                    <?php foreach ($data_pppk as $row) : 
                        $fileExt = strtolower(pathinfo($row['nama_file'], PATHINFO_EXTENSION));
                        $fileUrl = $base_url . $row['lokasi_file'];
                        
                        // Tentukan Icon Berdasarkan Tipe File
                        $iconClass = 'icon-doc';
                        $iconText = 'DOC';
                        if ($fileExt == 'pdf') { $iconClass = 'icon-pdf'; $iconText = 'PDF'; }
                        elseif (in_array($fileExt, ['jpg','jpeg','png'])) { $iconClass = 'icon-img'; $iconText = 'IMG'; }
                        elseif (in_array($fileExt, ['xls','xlsx'])) { $iconClass = 'icon-xls'; $iconText = 'XLS'; }
                    ?>
                    
                    <div class="doc-card">
                        <div class="doc-header">
                            <div class="file-icon-box <?= $iconClass; ?>">
                                <?= $iconText; ?>
                            </div>
                            <div class="doc-date">
                                <?= date('d M Y', strtotime($row['tanggal_upload'])); ?>
                            </div>
                        </div>
                        
                        <div class="doc-body">
                            <h4 title="<?= $row['judul_laporan']; ?>"><?= $row['judul_laporan']; ?></h4>
                            <p title="<?= $row['keterangan']; ?>"><?= $row['keterangan'] ? $row['keterangan'] : 'Tidak ada keterangan tambahan.'; ?></p>
                        </div>

                        <div class="doc-footer">
                            <a href="javascript:void(0);" class="btn-card btn-preview" onclick="openPreview('<?= $fileUrl; ?>', '<?= $fileExt; ?>', '<?= $row['judul_laporan']; ?>')">
                                <img src="<?= $base_url; ?>gambar/eye-open.png" width="14" style="opacity:0.7"> Lihat
                            </a>
                            
                            <a href="<?= $fileUrl; ?>" class="btn-card btn-download" target="_blank" download>
                                <img src="<?= $base_url; ?>gambar/export.png" width="14" style="opacity:0.7"> Unduh
                            </a>

                            <a href="pppk.php?hapus=<?= $row['id']; ?>" class="btn-card btn-delete" onclick="return confirm('Yakin ingin menghapus dokumen ini?');">
                                <img src="<?= $base_url; ?>gambar/hapuss.png" width="14" style="opacity:0.7">
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
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
            previewContainer.innerHTML = "";

            if (ext === 'pdf') {
                previewContainer.innerHTML = `<iframe id="previewFrame" src="${url}"></iframe>`;
                previewModal.style.display = "flex";
            } 
            else if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
                previewContainer.innerHTML = `<img id="previewImage" src="${url}">`;
                previewModal.style.display = "flex";
            } 
            else {
                alert("Preview tidak tersedia untuk format file ini (" + ext + ").\nSilakan gunakan tombol Download.");
            }
        }

        closePreviewBtn.onclick = () => {
            previewModal.style.display = "none";
            previewContainer.innerHTML = "";
        };

        window.onclick = (e) => { 
            if (e.target == modal) modal.style.display = "none"; 
            if (e.target == previewModal) {
                previewModal.style.display = "none";
                previewContainer.innerHTML = "";
            }
        };

        // --- PENCARIAN (UPDATE KHUSUS CARD) ---
        document.getElementById("searchInput").addEventListener("keyup", function() {
            let filter = this.value.toLowerCase();
            let cards = document.querySelectorAll(".doc-card");

            cards.forEach(card => {
                let text = card.innerText.toLowerCase();
                if (text.includes(filter)) {
                    card.style.display = "flex"; // Tampilkan (flex agar layout tetap benar)
                } else {
                    card.style.display = "none"; // Sembunyikan
                }
            });
        });
    </script>
</body>
</html>