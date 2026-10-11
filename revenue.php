<?php
session_start();
require 'php/database/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));

// Pendapatan Hari Ini
$q_rev_today = mysqli_query($koneksi, "SELECT SUM(biaya) as total FROM tickets WHERE DATE(jam_keluar)='$today' AND status='selesai'");
$rev_today = mysqli_fetch_assoc($q_rev_today)['total'] ?? 0;

$q_rev_yest = mysqli_query($koneksi, "SELECT SUM(biaya) as total FROM tickets WHERE DATE(jam_keluar)='$yesterday' AND status='selesai'");
$rev_yest = mysqli_fetch_assoc($q_rev_yest)['total'] ?? 0;

$rev_diff = $rev_yest > 0 ? (($rev_today - $rev_yest) / $rev_yest) * 100 : 0;

// Transaksi Hari Ini
$q_trx_today = mysqli_query($koneksi, "SELECT COUNT(*) as count FROM tickets WHERE DATE(jam_keluar)='$today' AND status='selesai'");
$trx_today = mysqli_fetch_assoc($q_trx_today)['count'];

$q_trx_yest = mysqli_query($koneksi, "SELECT COUNT(*) as count FROM tickets WHERE DATE(jam_keluar)='$yesterday' AND status='selesai'");
$trx_yest = mysqli_fetch_assoc($q_trx_yest)['count'];

$trx_diff = $trx_yest > 0 ? (($trx_today - $trx_yest) / $trx_yest) * 100 : 0;

// Rata-rata Durasi Hari Ini
$q_dur_today = mysqli_query($koneksi, "SELECT AVG(TIMESTAMPDIFF(MINUTE, jam_masuk, jam_keluar)) as avg_min FROM tickets WHERE DATE(jam_keluar)='$today' AND status='selesai'");
$dur_today_min = mysqli_fetch_assoc($q_dur_today)['avg_min'] ?? 0;

$q_dur_yest = mysqli_query($koneksi, "SELECT AVG(TIMESTAMPDIFF(MINUTE, jam_masuk, jam_keluar)) as avg_min FROM tickets WHERE DATE(jam_keluar)='$yesterday' AND status='selesai'");
$dur_yest_min = mysqli_fetch_assoc($q_dur_yest)['avg_min'] ?? 0;

$dur_diff = $dur_yest_min > 0 ? (($dur_today_min - $dur_yest_min) / $dur_yest_min) * 100 : 0;

function formatDuration($minutes) {
    if (!$minutes) return "0j 0m";
    $h = floor($minutes / 60);
    $m = round($minutes % 60);
    return "{$h}j {$m}m";
}
$dur_today_str = formatDuration($dur_today_min);

// Get Last 7 Days Revenue Data for Chart
$chart_labels = [];
$chart_data = [];

// Create an array of the last 7 days (including today) in chronological order
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $day_name = date('D', strtotime($date));
    
    // Translate day name to Indonesian
    $hari = [
        'Sun' => 'Min', 'Mon' => 'Sen', 'Tue' => 'Sel',
        'Wed' => 'Rab', 'Thu' => 'Kam', 'Fri' => 'Jum', 'Sat' => 'Sab'
    ];
    $chart_labels[] = $hari[$day_name];
    
    $q_chart = mysqli_query($koneksi, "SELECT SUM(biaya) as total FROM tickets WHERE DATE(jam_keluar)='$date' AND status='selesai'");
    $total = mysqli_fetch_assoc($q_chart)['total'] ?? 0;
    $chart_data[] = $total;
}

$chart_labels_json = json_encode($chart_labels);
$chart_data_json = json_encode($chart_data);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Laporan Pendapatan - ParkSmart</title>
    <?php include 'components/layout/head.php'; ?>
    <style>
        .decor-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            position: absolute;
            top: -16px;
            right: -16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .decor-icon {
            transform: translate(-4px, 4px);
            font-size: 24px;
        }
    </style>
</head>
<body class="bg-background text-on-surface font-body-md min-h-screen flex no-scrollbar">
    
    <?php include 'components/layout/sidebar.php'; ?>

    <div class="flex-1 md:ml-64 flex flex-col min-h-screen">
        <?php include 'components/layout/header.php'; ?>

        <main class="flex-1 p-md md:p-xl max-w-container-max mx-auto w-full no-scrollbar">
            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-md mb-lg">
                <div>
                    <h2 class="font-headline-lg text-headline-lg text-primary">Laporan Pendapatan</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant mt-xs">Ringkasan performa finansial dan tren hari ini.</p>
                </div>
                <div class="flex gap-sm">
                    <a href="php/proses/export_csv_revenue.php" class="flex items-center gap-xs px-md py-sm bg-surface-container-lowest border border-outline-variant rounded-lg font-label-bold text-label-bold text-on-surface hover:bg-surface-container-low transition-colors shadow-sm">
                        <span class="material-symbols-outlined text-sm">download</span>
                        Export CSV
                    </a>
                    <a href="cetak_laporan.php" target="_blank" class="flex items-center gap-xs px-md py-sm bg-primary text-on-primary rounded-lg font-label-bold text-label-bold shadow-sm hover:shadow-md transition-shadow">
                        <span class="material-symbols-outlined text-sm">print</span>
                        Cetak Laporan
                    </a>
                </div>
            </div>

            <!-- Bento Grid Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-md mb-xl">
                <!-- Pendapatan Hari Ini -->
                <div class="bg-surface-container-lowest rounded-xl p-lg border border-outline-variant/30 shadow-sm relative overflow-hidden flex flex-col justify-between min-h-[160px]">
                    <div class="decor-circle bg-[#e8effa]">
                        <span class="material-symbols-outlined text-primary decor-icon">account_balance_wallet</span>
                    </div>
                    <div class="relative z-10 mb-md">
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Pendapatan Hari Ini</p>
                    </div>
                    <div class="relative z-10">
                        <h3 class="font-headline-lg text-headline-lg text-primary mb-xs" style="font-size: 36px; line-height: 44px; letter-spacing: -0.02em;">Rp <?= number_format($rev_today, 0, ',', '.') ?></h3>
                        <div class="flex items-center gap-xs text-sm">
                            <?php if($rev_diff >= 0): ?>
                                <span class="material-symbols-outlined text-green-600 text-sm" style="font-variation-settings: 'wght' 700;">trending_up</span>
                                <span class="font-label-sm text-label-sm text-green-600 font-bold">+<?= number_format($rev_diff, 1) ?>%</span>
                            <?php else: ?>
                                <span class="material-symbols-outlined text-red-600 text-sm" style="font-variation-settings: 'wght' 700;">trending_down</span>
                                <span class="font-label-sm text-label-sm text-red-600 font-bold"><?= number_format($rev_diff, 1) ?>%</span>
                            <?php endif; ?>
                            <span class="font-body-sm text-body-sm text-on-surface-variant">vs kemarin</span>
                        </div>
                    </div>
                </div>

                <!-- Total Transaksi -->
                <div class="bg-surface-container-lowest rounded-xl p-lg border border-outline-variant/30 shadow-sm relative overflow-hidden flex flex-col justify-between min-h-[160px]">
                    <div class="decor-circle bg-[#f1f5f9]">
                        <span class="material-symbols-outlined text-secondary decor-icon">receipt_long</span>
                    </div>
                    <div class="relative z-10 mb-md">
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Total Transaksi</p>
                    </div>
                    <div class="relative z-10">
                        <h3 class="font-headline-lg text-headline-lg text-primary mb-xs" style="font-size: 36px; line-height: 44px; letter-spacing: -0.02em;"><?= $trx_today ?></h3>
                        <div class="flex items-center gap-xs text-sm">
                            <?php if($trx_diff >= 0): ?>
                                <span class="material-symbols-outlined text-green-600 text-sm" style="font-variation-settings: 'wght' 700;">trending_up</span>
                                <span class="font-label-sm text-label-sm text-green-600 font-bold">+<?= number_format($trx_diff, 1) ?>%</span>
                            <?php else: ?>
                                <span class="material-symbols-outlined text-red-600 text-sm" style="font-variation-settings: 'wght' 700;">trending_down</span>
                                <span class="font-label-sm text-label-sm text-red-600 font-bold"><?= number_format($trx_diff, 1) ?>%</span>
                            <?php endif; ?>
                            <span class="font-body-sm text-body-sm text-on-surface-variant">vs kemarin</span>
                        </div>
                    </div>
                </div>

                <!-- Rata-rata Durasi -->
                <div class="bg-surface-container-lowest rounded-xl p-lg border border-outline-variant/30 shadow-sm relative overflow-hidden flex flex-col justify-between min-h-[160px]">
                    <div class="decor-circle bg-[#fdf2f0]">
                        <span class="material-symbols-outlined text-[#8c3d2e] decor-icon">timer</span>
                    </div>
                    <div class="relative z-10 mb-md">
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Rata-rata Durasi</p>
                    </div>
                    <div class="relative z-10">
                        <h3 class="font-headline-lg text-headline-lg text-primary mb-xs" style="font-size: 36px; line-height: 44px; letter-spacing: -0.02em;"><?= $dur_today_str ?></h3>
                        <div class="flex items-center gap-xs text-sm">
                            <?php if($dur_diff > 0): ?>
                                <span class="material-symbols-outlined text-red-600 text-sm" style="font-variation-settings: 'wght' 700;">trending_up</span>
                                <span class="font-label-sm text-label-sm text-red-600 font-bold">+<?= number_format($dur_diff, 1) ?>%</span>
                            <?php elseif($dur_diff < 0): ?>
                                <span class="material-symbols-outlined text-green-600 text-sm" style="font-variation-settings: 'wght' 700;">trending_down</span>
                                <span class="font-label-sm text-label-sm text-green-600 font-bold"><?= number_format($dur_diff, 1) ?>%</span>
                            <?php else: ?>
                                <span class="material-symbols-outlined text-on-surface-variant text-sm" style="font-variation-settings: 'wght' 700;">trending_flat</span>
                                <span class="font-label-sm text-label-sm text-on-surface-variant font-bold">0%</span>
                            <?php endif; ?>
                            <span class="font-body-sm text-body-sm text-on-surface-variant">vs kemarin</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="bg-surface-container-lowest rounded-xl p-lg border border-outline-variant/30 shadow-sm mb-xl min-h-[400px]">
                <div class="flex justify-between items-center mb-md">
                    <h3 class="font-headline-md text-headline-md text-primary">Tren Pendapatan (7 Hari Terakhir)</h3>
                    <button class="px-md py-sm bg-surface-container-lowest border border-outline-variant rounded-lg font-label-sm text-label-sm text-on-surface-variant hover:bg-surface-container-low transition-colors shadow-sm">
                        7 Hari Terakhir
                    </button>
                </div>
                <!-- Dynamic Chart.js Area -->
                <div class="w-full h-full min-h-[300px] mt-lg relative">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

        </main>
    </div>
    <!-- Chart.js CDN and Init -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            
            const data = {
                labels: <?= $chart_labels_json ?>,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: <?= $chart_data_json ?>,
                    backgroundColor: '#00236f', // primary brand color
                    borderRadius: 4,
                    barPercentage: 0.6,
                    hoverBackgroundColor: '#1e3a8a' // hover state color
                }]
            };

            const config = {
                type: 'bar',
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
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    let value = context.parsed.y;
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
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
                                callback: function(value, index, values) {
                                    if (value >= 1000000) {
                                        return (value / 1000000) + 'M';
                                    } else if (value >= 1000) {
                                        return (value / 1000) + 'K';
                                    }
                                    return value;
                                },
                                maxTicksLimit: 5
                            },
                            beginAtZero: true
                        }
                    }
                }
            };

            new Chart(ctx, config);
        });
    </script>
</body>
</html>
