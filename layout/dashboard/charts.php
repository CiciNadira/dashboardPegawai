<?php
// 1. Query Data untuk Grafik Status (Pie Chart)
$query_status = mysqli_query($conn, "SELECT status_kepegawaian, COUNT(*) as total FROM pegawai GROUP BY status_kepegawaian");
$label_status = [];
$data_status  = [];
while ($row = mysqli_fetch_assoc($query_status)) {
    $label_status[] = $row['status_kepegawaian'];
    $data_status[]  = $row['total'];
}

// 2. Query Data untuk Grafik Golongan (Bar Chart)
$query_gol = mysqli_query($conn, "SELECT golongan_akhir, COUNT(*) as total FROM pegawai WHERE golongan_akhir != '' GROUP BY golongan_akhir ORDER BY golongan_akhir ASC");
$label_gol = [];
$data_gol  = [];
while ($row = mysqli_fetch_assoc($query_gol)) {
    $label_gol[] = $row['golongan_akhir'];
    $data_gol[]  = $row['total'];
}
?>

<div class="charts-card" style="background: #fff; border-radius: 16px; border: 1px solid #e5e7eb; padding: 20px; height: 100%; margin-bottom:20px;">
    <div style="font-weight: 700; color: #374151; margin-bottom: 20px; font-size: 16px;">
        📊 Statistik Kepegawaian
    </div>
    
    <div style="display: flex; gap: 20px; height: 300px;">
        <div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
            <canvas id="chartStatus"></canvas>
            <p style="margin-top: 10px; font-size: 12px; color: #6b7280; font-weight: 600;">Komposisi Status Pegawai</p>
        </div>
        
        <div style="width: 1px; background: #e5e7eb;"></div>

        <div style="flex: 1.5; display: flex; flex-direction: column; align-items: center;">
            <canvas id="chartGolongan"></canvas>
            <p style="margin-top: 10px; font-size: 12px; color: #6b7280; font-weight: 600;">Sebaran Golongan</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // --- 1. CONFIG PIE CHART (STATUS) ---
    const ctxStatus = document.getElementById('chartStatus').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut', // Bentuk Donat
        data: {
            labels: <?= json_encode($label_status); ?>,
            datasets: [{
                data: <?= json_encode($data_status); ?>,
                backgroundColor: [
                    '#3b82f6', // Biru (PNS)
                    '#f59e0b', // Kuning (PPPK)
                    '#10b981', // Hijau
                    '#ef4444'  // Merah
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } }
            }
        }
    });

    // --- 2. CONFIG BAR CHART (GOLONGAN) ---
    const ctxGol = document.getElementById('chartGolongan').getContext('2d');
    new Chart(ctxGol, {
        type: 'bar',
        data: {
            labels: <?= json_encode($label_gol); ?>,
            datasets: [{
                label: 'Jumlah Pegawai',
                data: <?= json_encode($data_gol); ?>,
                backgroundColor: '#6366f1', // Ungu
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { grid: { display: false } }
            }
        }
    });
</script>