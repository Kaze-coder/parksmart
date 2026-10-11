<?php
session_start();
require 'php/database/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// Get Settings for Capacity
$query_settings = mysqli_query($koneksi, "SELECT * FROM settings");
$settings = [];
while ($row = mysqli_fetch_assoc($query_settings)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
$kap_mobil = $settings['kapasitas_mobil'];
$kap_motor = $settings['kapasitas_motor'];

// Get Occupied Slots
$q_occ_mobil = mysqli_query($koneksi, "SELECT COUNT(*) as count FROM tickets WHERE jenis_kendaraan='mobil' AND status='parkir'");
$occ_mobil = mysqli_fetch_assoc($q_occ_mobil)['count'];
$q_occ_motor = mysqli_query($koneksi, "SELECT COUNT(*) as count FROM tickets WHERE jenis_kendaraan='motor' AND status='parkir'");
$occ_motor = mysqli_fetch_assoc($q_occ_motor)['count'];

$avail_mobil = $kap_mobil - $occ_mobil;
$avail_motor = $kap_motor - $occ_motor;

// Percentages
$pct_mobil = ($kap_mobil > 0) ? ($occ_mobil / $kap_mobil) * 100 : 0;
$pct_motor = ($kap_motor > 0) ? ($occ_motor / $kap_motor) * 100 : 0;

// VIP Dummy Logic (since not in DB yet)
$kap_vip = 10; 
$q_occ_vip = mysqli_query($koneksi, "SELECT COUNT(*) as count FROM tickets WHERE jenis_kendaraan='vip' AND status='parkir'");
$occ_vip = mysqli_fetch_assoc($q_occ_vip)['count'];
$avail_vip = $kap_vip - $occ_vip;
$pct_vip = ($kap_vip > 0) ? ($occ_vip / $kap_vip) * 100 : 0;

// Get Today Stats
$today = date('Y-m-d');
$q_in_today = mysqli_query($koneksi, "SELECT COUNT(*) as count FROM tickets WHERE DATE(jam_masuk)='$today'");
$in_today = mysqli_fetch_assoc($q_in_today)['count'];

$q_rev_today = mysqli_query($koneksi, "SELECT SUM(biaya) as total FROM tickets WHERE DATE(jam_keluar)='$today' AND status='selesai'");
$rev_today = mysqli_fetch_assoc($q_rev_today)['total'] ?? 0;

// Recent Activities
$q_recent = mysqli_query($koneksi, "SELECT * FROM tickets ORDER BY id DESC LIMIT 5");

// Get Hourly Occupancy for Chart (Last 9 hours)
$hourly_labels = [];
$hourly_data = [];
$current_hour = (int)date('H');

for ($h = max(0, $current_hour - 8); $h <= $current_hour; $h++) {
    $hourly_labels[] = sprintf('%02d:00', $h);
    $h_str = sprintf('%02d', $h);
    $q_hour = mysqli_query($koneksi, "SELECT COUNT(*) as count FROM tickets WHERE DATE(jam_masuk)='$today' AND HOUR(jam_masuk)='$h_str'");
    $hourly_data[] = mysqli_fetch_assoc($q_hour)['count'] ?? 0;
}

// Get Daily Occupancy for Chart (Last 7 Days) for "Minggu" view
$weekly_labels = [];
$weekly_data = [];
for ($d = 6; $d >= 0; $d--) {
    $date = date('Y-m-d', strtotime("-$d days"));
    $weekly_labels[] = date('D', strtotime($date));
    $q_day = mysqli_query($koneksi, "SELECT COUNT(*) as count FROM tickets WHERE DATE(jam_masuk)='$date'");
    $weekly_data[] = mysqli_fetch_assoc($q_day)['count'] ?? 0;
}

$hourly_labels_json = json_encode($hourly_labels);
$hourly_data_json = json_encode($hourly_data);
$weekly_labels_json = json_encode($weekly_labels);
$weekly_data_json = json_encode($weekly_data);



?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Monitoring - ParkSmart</title>
    <?php include 'components/layout/head.php'; ?>
</head>
<body class="bg-surface text-on-surface font-body-md min-h-screen flex no-scrollbar">
    
    <?php include 'components/layout/sidebar.php'; ?>

    <div class="flex-1 md:ml-64 flex flex-col min-h-screen">
        <?php include 'components/layout/header.php'; ?>

        <main class="flex-1 p-md md:p-xl overflow-y-auto no-scrollbar">
            <div class="mb-lg flex justify-between items-end">
                <div>
                    <h2 class="font-headline-lg text-headline-lg text-primary">Monitoring Real-time</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant mt-xs">Kapasitas parkir dan aktivitas kendaraan hari ini.</p>
                </div>
                <div class="flex items-center gap-xs font-label-sm text-label-sm text-on-surface-variant bg-surface-container px-sm py-xs rounded-full">
                    <span class="w-2 h-2 rounded-full bg-[#10b981] animate-pulse"></span>
                    Live Update
                </div>
            </div>

            <!-- Capacity Parkir Grid (3 Cards) -->
            <section class="grid grid-cols-1 md:grid-cols-3 gap-md mb-xl">
                <!-- Card 1: Mobil -->
                <div class="bg-surface-container-lowest rounded-xl p-lg border border-outline-variant shadow-sm flex flex-col justify-between">
                    <div class="flex justify-between items-start mb-lg">
                        <div class="flex items-center gap-md">
                            <div class="w-12 h-12 bg-primary-container text-on-primary-container rounded-lg flex items-center justify-center">
                                <span class="material-symbols-outlined text-[28px]">directions_car</span>
                            </div>
                            <h3 class="font-headline-md text-headline-md text-on-surface">Mobil</h3>
                        </div>
                        <?php if($avail_mobil > 0): ?>
                            <span class="bg-[#d1fae5] text-[#065f46] font-label-bold text-label-bold px-sm py-xs rounded-full">Tersedia</span>
                        <?php else: ?>
                            <span class="bg-error-container text-on-error-container font-label-bold text-label-bold px-sm py-xs rounded-full">Penuh</span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <div class="flex items-baseline gap-xs mb-sm">
                            <span class="font-headline-xl text-headline-xl text-primary"><?= $avail_mobil ?></span>
                            <span class="font-body-md text-body-md text-on-surface-variant">/ <?= $kap_mobil ?> slot</span>
                        </div>
                        <div class="w-full bg-surface-variant rounded-full h-2 overflow-hidden">
                            <div class="bg-primary h-2 rounded-full" style="width: <?= $pct_mobil ?>%"></div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Motor -->
                <div class="bg-surface-container-lowest rounded-xl p-lg border border-outline-variant shadow-sm flex flex-col justify-between">
                    <div class="flex justify-between items-start mb-lg">
                        <div class="flex items-center gap-md">
                            <div class="w-12 h-12 bg-secondary-container text-on-secondary-container rounded-lg flex items-center justify-center">
                                <span class="material-symbols-outlined text-[28px]">two_wheeler</span>
                            </div>
                            <h3 class="font-headline-md text-headline-md text-on-surface">Motor</h3>
                        </div>
                        <?php if($avail_motor > 0): ?>
                            <span class="bg-[#d1fae5] text-[#065f46] font-label-bold text-label-bold px-sm py-xs rounded-full">Tersedia</span>
                        <?php else: ?>
                            <span class="bg-error-container text-on-error-container font-label-bold text-label-bold px-sm py-xs rounded-full">Penuh</span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <div class="flex items-baseline gap-xs mb-sm">
                            <span class="font-headline-xl text-headline-xl text-secondary"><?= $avail_motor ?></span>
                            <span class="font-body-md text-body-md text-on-surface-variant">/ <?= $kap_motor ?> slot</span>
                        </div>
                        <div class="w-full bg-surface-variant rounded-full h-2 overflow-hidden">
                            <div class="bg-secondary h-2 rounded-full" style="width: <?= $pct_motor ?>%"></div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: VIP -->
                <div class="bg-surface-container-lowest rounded-xl p-lg border border-outline-variant shadow-sm flex flex-col justify-between relative overflow-hidden">
                    <?php if($avail_vip <= 0): ?>
                        <div class="absolute -right-6 -top-6 w-32 h-32 bg-error/10 rounded-full blur-2xl"></div>
                    <?php else: ?>
                        <div class="absolute -right-6 -top-6 w-32 h-32 bg-tertiary-container/20 rounded-full blur-2xl"></div>
                    <?php endif; ?>
                    <div class="flex justify-between items-start mb-lg relative z-10">
                        <div class="flex items-center gap-md">
                            <div class="w-12 h-12 bg-tertiary-container text-on-tertiary-container rounded-lg flex items-center justify-center">
                                <span class="material-symbols-outlined text-[28px]">star</span>
                            </div>
                            <h3 class="font-headline-md text-headline-md text-on-surface">VIP</h3>
                        </div>
                        <?php if($avail_vip > 0): ?>
                            <span class="bg-[#d1fae5] text-[#065f46] font-label-bold text-label-bold px-sm py-xs rounded-full">Tersedia</span>
                        <?php else: ?>
                            <span class="bg-error-container text-on-error-container font-label-bold text-label-bold px-sm py-xs rounded-full">Penuh</span>
                        <?php endif; ?>
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-baseline gap-xs mb-sm">
                            <span class="font-headline-xl text-headline-xl <?= $avail_vip > 0 ? 'text-tertiary' : 'text-error' ?>"><?= $avail_vip ?></span>
                            <span class="font-body-md text-body-md text-on-surface-variant">/ <?= $kap_vip ?> slot</span>
                        </div>
                        <div class="w-full bg-surface-variant rounded-full h-2 overflow-hidden">
                            <div class="<?= $avail_vip > 0 ? 'bg-tertiary' : 'bg-error' ?> h-2 rounded-full" style="width: <?= $pct_vip ?>%"></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Lower Section: Activity & Chart -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-md">
                
                <!-- Live Activity Feed (1/3 Width) -->
                <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm flex flex-col lg:col-span-1">
                    <div class="p-lg pb-md flex justify-between items-center">
                        <h3 class="font-headline-md text-headline-md text-on-surface">Live Activity</h3>
                        <span class="material-symbols-outlined text-primary cursor-pointer hover:bg-surface-container p-xs rounded-full transition-colors">filter_list</span>
                    </div>
                    <div class="px-md flex-1 overflow-y-auto max-h-[400px] no-scrollbar">
                        <ul class="flex flex-col">
                            <?php 
                            // Dummy activities array to match design perfectly if DB is empty or use actual DB.
                            // To match design perfectly, we'll format the output to closely resemble it.
                            while($act = mysqli_fetch_assoc($q_recent)): 
                                $is_masuk = $act['status'] == 'parkir';
                                // Logic for activity: display latest activity time
                                $time_display = date('H:i', strtotime($is_masuk ? $act['jam_masuk'] : ($act['jam_keluar'] ?? $act['jam_masuk'])));
                                $act_type = $is_masuk ? 'Masuk' : 'Keluar';
                            ?>
                            <li class="flex items-start gap-md py-md border-b border-surface-variant last:border-0 hover:bg-surface-container-lowest transition-colors group cursor-default">
                                <?php if($act['status'] == 'parkir'): ?>
                                <div class="w-8 h-8 bg-[#d1fae5] text-[#065f46] rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-[18px]">login</span>
                                </div>
                                <?php else: ?>
                                <div class="w-8 h-8 bg-error-container text-error rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-[18px]">logout</span>
                                </div>
                                <?php endif; ?>
                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start gap-xs">
                                        <p class="font-label-bold text-label-bold text-on-surface truncate">
                                            <?= $act_type ?> - <?= $act['plat_nomor'] ?>
                                        </p>
                                        <span class="font-label-sm text-label-sm text-on-surface-variant flex-shrink-0"><?= $time_display ?></span>
                                    </div>
                                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-xs">Gate Utama • <?= ucfirst($act['jenis_kendaraan']) ?></p>
                                </div>
                            </li>
                            <?php endwhile; ?>
                            <?php if(mysqli_num_rows($q_recent) == 0): ?>
                            <li class="text-center text-on-surface-variant py-md font-body-sm">Belum ada aktivitas.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="p-md text-center border-t border-surface-variant">
                        <a href="history.php" class="font-label-bold text-label-bold text-primary hover:underline cursor-pointer">Lihat Semua Aktivitas</a>
                    </div>
                </section>

                <!-- Chart Section (2/3 Width) -->
                <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-lg lg:col-span-2 flex flex-col">
                    <div class="flex justify-between items-center mb-xl">
                        <h3 class="font-headline-md text-headline-md text-on-surface">Tren Okupansi Hari Ini</h3>
                        <div class="flex bg-surface-container p-[2px] rounded-lg border border-outline-variant">
                            <button id="btn-chart-hari" class="px-md py-xs bg-surface-container-lowest text-on-surface rounded-md font-label-sm text-label-sm shadow-sm border border-outline-variant/50 transition-colors" onclick="switchChart('hari')">Hari Ini</button>
                            <button id="btn-chart-minggu" class="px-md py-xs text-on-surface-variant rounded-md font-label-sm text-label-sm hover:text-on-surface transition-colors" onclick="switchChart('minggu')">Minggu</button>
                        </div>
                    </div>
                    
                    <div class="flex-1 relative flex items-end mt-sm min-h-[200px]">
                        <canvas id="monitoringChart"></canvas>
                    </div>
                </section>
                
            </div>

        </main>
    </div>
    <!-- Chart.js CDN and Init -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const hourlyLabels = <?= $hourly_labels_json ?>;
        const hourlyData = <?= $hourly_data_json ?>;
        const weeklyLabels = <?= $weekly_labels_json ?>;
        const weeklyData = <?= $weekly_data_json ?>;

        let monitoringChart;

        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('monitoringChart').getContext('2d');
            
            // Create gradient fill for the line chart
            let gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(0, 35, 111, 0.2)'); // primary color with 20% opacity
            gradient.addColorStop(1, 'rgba(0, 35, 111, 0)');
            
            const data = {
                labels: hourlyLabels,
                datasets: [{
                    label: 'Kendaraan Masuk',
                    data: hourlyData,
                    borderColor: '#00236f', // primary brand color
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#00236f',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4 // Smooth curve
                }]
            };

            const config = {
                type: 'line',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // Clean minimalist look
                        },
                        tooltip: {
                            backgroundColor: '#1a1b21',
                            titleFont: { family: 'Inter', size: 12 },
                            bodyFont: { family: 'Inter', size: 14, weight: 'bold' },
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                font: { family: 'Inter', size: 12 },
                                color: '#757682'
                            }
                        },
                        y: {
                            grid: {
                                color: '#e3e1e9',
                                drawBorder: false,
                                borderDash: [5, 5]
                            },
                            ticks: {
                                font: { family: 'Inter', size: 12 },
                                color: '#757682',
                                stepSize: 20
                            },
                            beginAtZero: true
                        }
                    }
                }
            };

            monitoringChart = new Chart(ctx, config);
        });

        function switchChart(type) {
            if (!monitoringChart) return;
            
            const btnHari = document.getElementById('btn-chart-hari');
            const btnMinggu = document.getElementById('btn-chart-minggu');

            if (type === 'hari') {
                btnHari.className = "px-md py-xs bg-surface-container-lowest text-on-surface rounded-md font-label-sm text-label-sm shadow-sm border border-outline-variant/50 transition-colors";
                btnMinggu.className = "px-md py-xs text-on-surface-variant rounded-md font-label-sm text-label-sm hover:text-on-surface transition-colors";
                
                monitoringChart.data.labels = hourlyLabels;
                monitoringChart.data.datasets[0].data = hourlyData;
            } else {
                btnMinggu.className = "px-md py-xs bg-surface-container-lowest text-on-surface rounded-md font-label-sm text-label-sm shadow-sm border border-outline-variant/50 transition-colors";
                btnHari.className = "px-md py-xs text-on-surface-variant rounded-md font-label-sm text-label-sm hover:text-on-surface transition-colors";
                
                monitoringChart.data.labels = weeklyLabels;
                monitoringChart.data.datasets[0].data = weeklyData;
            }
            monitoringChart.update();
        }
    </script>
</body>
</html>
