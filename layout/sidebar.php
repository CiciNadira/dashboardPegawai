<?php
global $base_url;
?>

<div class="sidebar" id="sidebar">
    <div class="collapse-btn" id="toggleSidebar">❮</div>

    <div class="sidebar-header">
        <img src="<?= $base_url; ?>gambar/logo.png" alt="Logo BPS">
        <h3>BPS Kota Pontianak</h3>
        <p>Kepegawaian Internal</p>
    </div>

    <div class="sidebar-body">
        <ul class="menu">

            <li onclick="window.location.href='<?= $base_url; ?>dashboard.php'">
                <img src="<?= $base_url; ?>gambar/homee.png" alt="Dashboard">
                <span>Dashboard</span>
            </li>

            <li class="subbag-item" data-submenu="kepegawaian">
                <img src="<?= $base_url; ?>gambar/simpegg.png">
                <span>Kepegawaian</span>
            </li>
            <ul class="submenu" id="kepegawaian">
                <li onclick="window.location.href='<?= $base_url; ?>pages/kepegawaian/sdm.php'">SDM</li>
                <li onclick="window.location.href='<?= $base_url; ?>pages/kepegawaian/fungsional.php'">Fungsional</li>
                <li onclick="window.location.href='<?= $base_url; ?>pages/kepegawaian/kgb.php'">KGB</li>
                <li onclick="window.location.href='<?= $base_url; ?>pages/kepegawaian/kp.php'">KP</li>
                <li onclick="window.location.href='<?= $base_url; ?>pages/kepegawaian/pppk.php'">PPPK</li>
            </ul>

            <li class="subbag-item" data-submenu="keuangan">
                <img src="<?= $base_url; ?>gambar/keuangan.jpg">
                <span>Keuangan</span>
            </li>
            <ul class="submenu" id="keuangan">
                <li onclick="window.location.href='<?= $base_url; ?>pages/keuangan/spider.php'">Spider</li>
                <li onclick="window.location.href='<?= $base_url; ?>pages/keuangan/bos.php'">BOS</li>
                <li onclick="window.location.href='<?= $base_url; ?>pages/keuangan/sakti.php'">Sakti</li>
                <li onclick="window.location.href='<?= $base_url; ?>pages/keuangan/omspan.php'">OM SPAN</li>
                <li onclick="window.location.href='<?= $base_url; ?>pages/keuangan/sirup.php'">SIRUP</li>
            </ul>

            <li onclick="window.location.href='<?= $base_url; ?>pages/sakip/index.php'">
                <img src="<?= $base_url; ?>gambar/sakipp.png">
                <span>Sakip</span>
            </li>

            <li class="subbag-item" data-submenu="laporan">
                <img src="<?= $base_url; ?>gambar/lakinn.png">
                <span>Laporan</span>
            </li>
            <ul class="submenu" id="laporan">
                <li onclick="window.location.href='<?= $base_url; ?>pages/laporan/keuangan.php'">Lap. Keuangan</li>
                <li onclick="window.location.href='<?= $base_url; ?>pages/laporan/kepegawaian.php'">Lap. Kepegawaian</li>
                <li onclick="window.location.href='<?= $base_url; ?>pages/laporan/lakin.php'">Lakin & BMN</li>
            </ul>

        </ul>
    </div>

    <div class="user-account" id="userAccountBtn">
        <div class="ua-left">
            <div class="ua-photo">KS</div>
            <div class="ua-info">
                <div class="ua-name">Kepala Sub Bagian</div>
                <div class="ua-role">Administrator</div>
            </div>
        </div>
        <div class="ua-caret">▾</div>
        <div class="ua-dropdown" id="accountDropdown">
            <a class="logout-btn" id="logoutDropdown">
                <img src="<?= $base_url; ?>gambar/logout.svg" class="logout-icon" alt="Logout"> Logout
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById("sidebar");
        const toggleBtn = document.getElementById("toggleSidebar");

        if (sidebar && toggleBtn) {
            toggleBtn.addEventListener("click", () => {
                sidebar.classList.toggle("collapsed");
                toggleBtn.innerHTML = sidebar.classList.contains("collapsed") ? "❯" : "❮";
            });
        }

        const submenus = document.querySelectorAll(".submenu");
        const items = document.querySelectorAll(".subbag-item");

        items.forEach(item => {
            item.addEventListener("click", () => {
                const targetId = item.dataset.submenu;
                const targetMenu = document.getElementById(targetId);

                submenus.forEach(menu => {
                    if (menu !== targetMenu) menu.classList.remove("open");
                });

                if (targetMenu) targetMenu.classList.toggle("open");
            });
        });

        // Deteksi Halaman Aktif (Agar menu tetap terbuka & berwarna biru)
        const currentUrl = window.location.href; // Ambil URL lengkap saat ini
        const allLinks = document.querySelectorAll('.menu li, .submenu li');

        allLinks.forEach(link => {
            // Ambil URL tujuan dari atribut onclick
            // Format onclick: "window.location.href='http://.../file.php'"
            let onclickAttr = link.getAttribute('onclick');

            if (onclickAttr) {
                // Bersihkan string agar hanya sisa URL-nya saja
                let targetUrl = onclickAttr.replace("window.location.href='", "").replace("'", "");

                // Bandingkan URL saat ini dengan URL tujuan tombol
                if (currentUrl === targetUrl) {
                    link.classList.add('active');
                    const parentSubmenu = link.closest('.submenu');
                    if (parentSubmenu) parentSubmenu.classList.add('open');
                }
            }
        });

        const userBtn = document.getElementById("userAccountBtn");
        const userDrop = document.getElementById("accountDropdown");
        const logoutBtn = document.getElementById("logoutDropdown");

        if (userBtn && userDrop) {
            userBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                userDrop.style.display = userDrop.style.display === "block" ? "none" : "block";
            });
            window.addEventListener("click", (e) => {
                if (!userBtn.contains(e.target) && !userDrop.contains(e.target)) {
                    userDrop.style.display = "none";
                }
            });
        }

        if (logoutBtn) {
            logoutBtn.addEventListener("click", () => {
                sessionStorage.clear();
                // Redirect ke logout logic (sementara ke index)
                window.location.href = "<?= $base_url; ?>index.php";
            });
        }
    });
</script>