<div class="dashboard-hero">
    <div class="hero-left">
        <div class="subbag-big-card" style="height:100%; margin:0;">
            <img src="<?= $base_url; ?>gambar/kasubbag.jpg" class="subbag-photo">
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
                        $jabatan = $t['jabatan_dashboard'];
                        $bg = ($jabatan=='Sekretaris')?'#f5f3ff':(($jabatan=='Bendahara')?'#f0fdf4':'#fff7ed');
                        $cl = ($jabatan=='Sekretaris')?'#7c3aed':(($jabatan=='Bendahara')?'#166534':'#c2410c');
                    ?>
                    <div class="team-item">
                        <div class="t-avatar" style="background:<?= $bg; ?>; color:<?= $cl; ?>;"><?= $inisial; ?></div>
                        <div class="t-details">
                            <span class="t-role role-<?= $jabatan; ?>"><?= $jabatan; ?></span>
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