<div class="dashboard-hero" style="margin-top: 20px;">
    <div class="hero-left">
        <div class="subbag-big-card" style="height:100%; margin:0;">
            <img src="<?= $base_url; ?>gambar/user.png" class="subbag-photo">
            <div class="subbag-info">
                <h2><?= $jabatan_user; ?></h2> 
                <h4>Unit Pengelola Administrasi</h4>
                <div class="subbag-details">
                    <div class="detail-row"><div class="detail-label">Nama:</div><div class="detail-value"><?= $nama_user; ?></div></div>
                    <div class="detail-row"><div class="detail-label">NIP:</div><div class="detail-value"><?= $nip_user; ?></div></div>
                    <div class="detail-row"><div class="detail-label">Status:</div><div class="detail-value">Aktif</div></div>
                </div>
            </div>
        </div>
    </div>

    <div class="hero-right">
        <div class="team-list-card">
            <div class="team-header">👥 Tim Sub Bagian Umum</div>
            <div class="team-scroll-area">
                <?php if(empty($tim_kanan)) : ?>
                    <div style="text-align:center; padding:30px; color:#9ca3af; font-size:13px;">
                        Belum ada tim. <a href="pages/profil.php" style="color:#2563eb;">Atur disini.</a>
                    </div>
                <?php else : ?>
                    <?php foreach($tim_kanan as $t) : 
                        $inisial = strtoupper(substr($t['nama_lengkap'], 0, 2));
                        
                        // PERBAIKAN: Paksa nama jabatan jadi huruf besar semua
                        $jabatan_asli = $t['jabatan_dashboard']; 
                        $jabatan_upper = strtoupper($jabatan_asli); 

                        // Logika warna untuk avatar (Lingkaran CI, JA, SU)
                        if ($jabatan_upper == 'BENDAHARA') {
                            $bg = '#f0fdf4'; $cl = '#166534'; // Hijau
                        } elseif ($jabatan_upper == 'BMN') {
                            $bg = '#fff7ed'; $cl = '#c2410c'; // Oranye
                        } elseif ($jabatan_upper == 'PPABP') {
                            $bg = '#e0f2fe'; $cl = '#0369a1'; // Biru Muda (Ini agar SU jadi biru)
                        } else {
                            $bg = '#f3f4f6'; $cl = '#6b7280';
                        }
                    ?>
                    <div class="team-item">
                        <div class="t-avatar" style="background:<?= $bg; ?>; color:<?= $cl; ?>;">
                            <?= $inisial; ?>
                        </div>
                        <div class="t-details">
                            <span class="t-role role-<?= $jabatan_upper; ?>">
                                <?= $jabatan_upper; ?>
                            </span>
                            <div class="t-name"><?= $t['nama_lengkap']; ?></div>
                            <div class="t-nip">NIP. <?= $t['nip']; ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>