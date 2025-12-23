<?php
require '../../config/functions.php';

$jenis_aplikasi = "Omspan"; 
$judul_halaman  = "Keuangan - Aplikasi OMSPAN";

$data_keuangan = query("SELECT * FROM data_keuangan WHERE jenis_aplikasi = '$jenis_aplikasi' ORDER BY id DESC");

if (isset($_POST["simpan_data"])) {
    if ($_POST['id'] != "") {
        if (ubahKeuangan($_POST) > 0) echo "<script>alert('Data berhasil diupdate!'); location.href='omspan.php';</script>";
        else echo "<script>alert('Gagal/Tidak ada perubahan!'); location.href='omspan.php';</script>";
    } else {
        if (tambahKeuangan($_POST) > 0) echo "<script>alert('Data berhasil disimpan!'); location.href='omspan.php';</script>";
    }
}

if (isset($_GET["hapus"])) {
    if (hapusKeuangan($_GET["hapus"]) > 0) echo "<script>alert('Data berhasil dihapus!'); location.href='omspan.php';</script>";
    else echo "<script>alert('Gagal menghapus data!'); location.href='omspan.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $judul_halaman; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;400;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="<?= $base_url; ?>assets/css/kepegawaian-kgb.css">
    
    <style>
        .doc-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-top: 20px; }
        
        .doc-card { 
            background: #fff; 
            border: 1px solid #e5e7eb; 
            border-radius: 16px; 
            padding: 20px; 
            display: flex; 
            flex-direction: column; 
            justify-content: space-between; 
            position: relative; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
            transition: all 0.3s ease;
        }
        .doc-card:hover { transform: translateY(-5px); box-shadow: 0 12px 24px rgba(0,0,0,0.08); border-color: #3b82f6; }

        .status-badge { font-size: 11px; padding: 4px 10px; border-radius: 20px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-Proses { background: #fef3c7; color: #d97706; border: 1px solid #fcd34d; } /* Kuning */
        .status-Selesai { background: #dcfce7; color: #166534; border: 1px solid #86efac; } /* Hijau */
        .status-Belum { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; } /* Merah */

        .preview-modal-content { width: 80%; height: 90vh; background: #fff; border-radius: 12px; display: flex; flex-direction: column; overflow: hidden; }
        .preview-body { flex: 1; background: #525659; display: flex; justify-content: center; align-items: center; }
        #previewFrame { width: 100%; height: 100%; border: none; }
        #previewImage { max-width: 100%; max-height: 100%; object-fit: contain; }
        
        .card-actions { display: flex; gap: 8px; border-top: 1px solid #f3f4f6; padding-top: 15px; margin-top: 15px; }
        .btn-act { flex: 1; padding: 8px; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
        .btn-view { background: #eff6ff; color: #2563eb; } .btn-view:hover { background: #dbeafe; }
        .btn-edit { background: #f3f4f6; color: #4b5563; } .btn-edit:hover { background: #e5e7eb; }
        .btn-del { background: #fef2f2; color: #dc2626; } .btn-del:hover { background: #fee2e2; }
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

        <h1 class="page-title"><?= $judul_halaman; ?></h1>
        <p class="page-sub">Kelola kegiatan dan bukti dukung aplikasi keuangan.</p>
        
        <div class="box" style="background:transparent; border:none; padding:0; box-shadow:none;">
            <div class="top-actions" style="background:#fff; padding:20px; border-radius:16px; box-shadow:0 4px 12px rgba(0,0,0,0.05); margin-bottom: 20px;">
                <div class="search-box" style="margin-bottom:0; flex:1;">
                    <img src="<?= $base_url; ?>gambar/search.png" width="18">
                    <input type="text" id="searchInput" placeholder="Cari uraian kegiatan...">
                </div>
                <button class="btn btn-primary" id="addDataBtn" style="background: #2563eb;">+ Tambah Kegiatan</button>
            </div>

            <div id="addDataModal" class="modal">
                <div class="add-modal-content" style="max-width:500px; background:#fff; color:#111;">
                    <span class="closeAddBtn" style="float:right;cursor:pointer;font-size:22px">&times;</span>
                    <h2 id="formTitle" style="margin-bottom:15px;font-weight:200">Tambah Data</h2>
                    
                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="dataId">
                        <input type="hidden" name="jenis_aplikasi" value="<?= $jenis_aplikasi; ?>">
                        <input type="hidden" name="file_lama" id="fileLama">

                        <div class="form-group">
                            <label>Tanggal Kegiatan</label>
                            <input type="date" name="tanggal" id="addTanggal" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:8px;">
                        </div>
                        <div class="form-group">
                            <label>Uraian Kegiatan</label>
                            <input type="text" name="uraian" id="addUraian" required placeholder="Contoh: Input Data Triwulan I" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:8px;">
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" id="addStatus" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:8px;">
                                <option value="Proses">Sedang Proses</option>
                                <option value="Selesai">Selesai</option>
                                <option value="Belum">Belum Dimulai</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Bukti Dukung (Screenshot/PDF)</label>
                            <div style="border: 2px dashed #ccc; padding: 15px; text-align: center; border-radius: 8px; background: #f9fafb;">
                                <input type="file" name="bukti" id="addBukti" style="width:100%;">
                                <small id="fileHelp" style="display:block; margin-top:5px; color:gray; font-size:12px;">*Wajib diisi saat tambah baru (Max 5MB)</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea name="ket" id="addKet" rows="2" style="width:100%; border:1px solid #ccc; border-radius:8px; padding:10px;"></textarea>
                        </div>
                        <div class="add-footer">
                            <button type="button" class="add-cancel">Batal</button>
                            <button type="submit" name="simpan_data" class="add-save" id="saveBtn">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="previewModal" class="modal" style="z-index: 100000;">
                <div class="preview-modal-content">
                    <div style="padding:15px; background:#f3f4f6; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e5e7eb;">
                        <h3 id="previewTitle" style="margin:0; font-size:16px;">Preview Bukti</h3>
                        <span class="closePreviewBtn" style="cursor:pointer; font-size:24px;">&times;</span>
                    </div>
                    <div class="preview-body" id="previewContainer"></div>
                </div>
            </div>

            <div class="doc-grid" id="docGrid">
                <?php if (empty($data_keuangan)) : ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #6b7280; background:#fff; border-radius:12px; border:1px dashed #ccc;">
                        <p>Belum ada data kegiatan <?= $jenis_aplikasi; ?>.</p>
                    </div>
                <?php else : ?>
                    <?php foreach ($data_keuangan as $row) : 
                        $fileUrl = $base_url . "uploads/keuangan/" . $row['bukti_file'];
                        $ext = strtolower(pathinfo($row['bukti_file'], PATHINFO_EXTENSION));
                        $statusColor = ($row['status'] == 'Selesai') ? '#166534' : '#111827';
                    ?>
                    <div class="doc-card">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                            <span class="status-badge status-<?= $row['status']; ?>"><?= $row['status']; ?></span>
                            <small style="color:#6b7280; font-size:12px;">
                                <?= date('d M Y', strtotime($row['tanggal_kegiatan'])); ?>
                            </small>
                        </div>
                        
                        <div style="flex:1;">
                            <h4 style="margin:0 0 8px 0; color:<?= $statusColor; ?>; font-size:16px; line-height:1.4;">
                                <?= $row['uraian_kegiatan']; ?>
                            </h4>
                            <p style="font-size:13px; color:#6b7280; margin:0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= $row['keterangan'] ? $row['keterangan'] : 'Tidak ada keterangan tambahan.'; ?>
                            </p>
                        </div>

                        <div class="card-actions">
                            <button type="button" class="btn-act btn-view" onclick="openPreview('<?= $fileUrl; ?>', '<?= $ext; ?>')" title="Lihat Bukti">
                                <img src="<?= $base_url; ?>gambar/eye-open.png" width="16" style="margin-right:4px;"> Bukti
                            </button>
                            
                            <button type="button" class="btn-act btn-edit" onclick='editData(<?= json_encode($row); ?>)' title="Edit Data">
                                <img src="<?= $base_url; ?>gambar/edit.png" width="16">
                            </button>
                            
                            <a href="omspan.php?hapus=<?= $row['id']; ?>" class="btn-act btn-del" onclick="return confirm('Yakin ingin menghapus data ini?');" title="Hapus Data">
                                <img src="<?= $base_url; ?>gambar/hapuss.png" width="16">
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
        
        document.getElementById("addDataBtn").onclick = () => {
            document.querySelector("form").reset();
            document.getElementById("dataId").value = "";
            document.getElementById("formTitle").innerText = "Tambah Data <?= $jenis_aplikasi; ?>";
            document.getElementById("saveBtn").innerText = "Simpan";
            document.getElementById("fileHelp").innerText = "*Wajib upload bukti dukung";
            modal.style.display = "flex";
        };

        function editData(data) {
            document.getElementById("dataId").value = data.id;
            document.getElementById("addTanggal").value = data.tanggal_kegiatan;
            document.getElementById("addUraian").value = data.uraian_kegiatan;
            document.getElementById("addStatus").value = data.status;
            document.getElementById("addKet").value = data.keterangan;
            document.getElementById("fileLama").value = data.bukti_file;
            
            document.getElementById("formTitle").innerText = "Edit Data <?= $jenis_aplikasi; ?>";
            document.getElementById("saveBtn").innerText = "Update";
            document.getElementById("fileHelp").innerText = "*Biarkan kosong jika tidak ingin mengubah file";
            modal.style.display = "flex";
        }

        const previewModal = document.getElementById("previewModal");
        const previewContainer = document.getElementById("previewContainer");

        function openPreview(url, ext) {
            previewContainer.innerHTML = "";
            if(ext === 'pdf') {
                previewContainer.innerHTML = `<iframe id="previewFrame" src="${url}"></iframe>`;
            } else if(['jpg','jpeg','png'].includes(ext)) {
                previewContainer.innerHTML = `<img id="previewImage" src="${url}">`;
            } else {
                alert("Preview tidak tersedia untuk format file ini. Silakan download manual.");
                return;
            }
            previewModal.style.display = "flex";
        }

        document.querySelector(".closeAddBtn").onclick = () => modal.style.display = "none";
        document.querySelector(".add-cancel").onclick = () => modal.style.display = "none";
        document.querySelector(".closePreviewBtn").onclick = () => previewModal.style.display = "none";
        
        window.onclick = (e) => { 
            if(e.target == modal) modal.style.display = "none";
            if(e.target == previewModal) previewModal.style.display = "none";
        };

        document.getElementById("searchInput").addEventListener("keyup", function() {
            let filter = this.value.toLowerCase();
            document.querySelectorAll(".doc-card").forEach(card => {
                let text = card.innerText.toLowerCase();
                card.style.display = text.includes(filter) ? "flex" : "none";
            });
        });
    </script>
</body>
</html>