<?php
require '../../config/functions.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAKIP - BPS Kota Pontianak</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base_url; ?>assets/css/kepegawaian-kgb.css"> <style>
        /* Style Khusus Kartu Triwulan */
        .tw-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        .tw-card {
            background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            padding: 30px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
            position: relative;
            overflow: hidden;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .tw-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            border-color: #3b82f6;
        }
        
        /* Icon Bulat Besar */
        .tw-icon {
            width: 70px;
            height: 70px;
            background: #eff6ff;
            color: #2563eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: bold;
            margin: 0 auto 20px;
            transition: 0.3s;
        }
        .tw-card:hover .tw-icon {
            background: #2563eb;
            color: #fff;
            transform: scale(1.1);
        }

        .tw-title { font-size: 20px; font-weight: 700; color: #1f2937; margin-bottom: 8px; }
        .tw-desc { font-size: 14px; color: #6b7280; }

        /* Hiasan background */
        .tw-bg-num {
            position: absolute;
            right: -10px;
            bottom: -20px;
            font-size: 100px;
            font-weight: 800;
            color: rgba(0,0,0,0.03);
            line-height: 1;
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

        <h1 class="page-title">Dokumen SAKIP</h1>
        <p class="page-sub">Pilih periode triwulan untuk mengelola laporan kinerja.</p>

        <div class="box" style="background:transparent; border:none; padding:0; box-shadow:none;">
            <div class="tw-grid">
                
                <a href="dokumen.php?tw=I" class="tw-card">
                    <div class="tw-bg-num">1</div>
                    <div class="tw-icon">I</div>
                    <div class="tw-title">Triwulan I</div>
                    <div class="tw-desc">Januari - Maret</div>
                </a>

                <a href="dokumen.php?tw=II" class="tw-card">
                    <div class="tw-bg-num">2</div>
                    <div class="tw-icon">II</div>
                    <div class="tw-title">Triwulan II</div>
                    <div class="tw-desc">April - Juni</div>
                </a>

                <a href="dokumen.php?tw=III" class="tw-card">
                    <div class="tw-bg-num">3</div>
                    <div class="tw-icon">III</div>
                    <div class="tw-title">Triwulan III</div>
                    <div class="tw-desc">Juli - September</div>
                </a>

                <a href="dokumen.php?tw=IV" class="tw-card">
                    <div class="tw-bg-num">4</div>
                    <div class="tw-icon">IV</div>
                    <div class="tw-title">Triwulan IV</div>
                    <div class="tw-desc">Oktober - Desember</div>
                </a>

            </div>
        </div>
    </div>

</body>
</html>