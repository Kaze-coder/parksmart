<?php
session_start();
require 'php/database/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

if(!isset($_GET['id'])) {
    echo "ID Tiket tidak valid.";
    exit;
}

$ticket_code = mysqli_real_escape_string($koneksi, $_GET['id']);
$query = mysqli_query($koneksi, "SELECT * FROM tickets WHERE ticket_code='$ticket_code'");
if(mysqli_num_rows($query) == 0) {
    echo "Data tiket tidak ditemukan.";
    exit;
}

$ticket = mysqli_fetch_assoc($query);
// Sertakan fungsi tarif terpusat
require_once 'php/config/tarif.php';

// Get Location Settings
$query_settings = mysqli_query($koneksi, "SELECT setting_value FROM settings WHERE setting_key = 'location'");
$loc_setting = mysqli_fetch_assoc($query_settings);
$location = $loc_setting ? $loc_setting['setting_value'] : 'Gedung ParkSmart Center, Jl. Teknologi No. 88, Kota Modern';

$is_exit = $ticket['status'] == 'selesai' || isset($_GET['preview_keluar']);

// Jika ini preview struk keluar (sebelum checkout beneran di DB)
if ($is_exit && $ticket['status'] == 'parkir') {
    $jam_keluar = time();
    $kalkulasi = hitungTarifParkir($ticket['jam_masuk'], $jam_keluar, $ticket['jenis_kendaraan']);
    
    $ticket['jam_keluar'] = date('Y-m-d H:i:s', $jam_keluar);
    $ticket['biaya'] = $kalkulasi['total_biaya'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_exit ? 'Struk Parkir' : 'Cetak Tiket' ?> - <?= $ticket_code ?></title>
    
    <!-- Using Space Mono for that authentic thermal receipt look -->
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Space Mono', monospace;
            background-color: #f4f3fa;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
            margin: 0;
            color: #000; /* Struk is usually pure black */
        }
        .ticket {
            width: 300px;
            background: #ffffff;
            padding: 24px 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            position: relative;
        }
        
        .header { text-align: center; margin-bottom: 12px; }
        .header p { margin: 4px 0; font-size: 13px; line-height: 1.4; }
        .header .title { font-size: 16px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px; }
        
        .divider { 
            border-bottom: 2px dashed #000; 
            margin: 12px 0; 
        }
        
        .barcode-container { 
            text-align: center; 
            margin: 16px 0;
        }
        
        .details { 
            font-size: 13px; 
            line-height: 1.5;
            margin: 16px 0;
        }
        
        /* Table layout for perfect colon alignment */
        .details table {
            width: 100%;
            border-collapse: collapse;
        }
        .details table td {
            vertical-align: top;
            padding: 2px 0;
        }
        .details table td.label {
            width: 100px;
        }
        .details table td.colon {
            width: 15px;
            text-align: center;
        }
        .details table td.value {
            text-align: left;
        }
        
        .btn-print {
            margin-top: 40px;
            padding: 12px 24px;
            background-color: #00236f;
            color: white;
            border: none;
            border-radius: 8px;
            font-family: inherit;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,35,111,0.2);
            transition: opacity 0.2s;
        }
        .btn-print:hover { opacity: 0.9; }
        
        @media print {
            body { background-color: transparent; padding: 0; align-items: flex-start; }
            .ticket { box-shadow: none; border-radius: 0; padding: 0; width: 100%; max-width: 300px; margin: 0 auto; }
            .no-print { display: none; }
        }
    </style>
    <!-- QRious Library untuk QR Code -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
</head>
<body onload="initPrint()">
    
    <div class="ticket">
        <div class="header">
            <p><?= $is_exit ? 'Struk Parkir' : 'Karcis Masuk' ?></p>
            <div class="title">PARKSMART</div>
            <p><?= htmlspecialchars($location) ?></p>
        </div>
        
        <div class="divider"></div>
        
        <?php if (!$is_exit): ?>
        <div class="barcode-container">
            <canvas id="qrcode"></canvas>
            <p style="font-size: 11px; margin-top: 4px; font-weight: bold; font-family: monospace;"><?= $ticket['ticket_code'] ?></p>
        </div>
        <div class="divider"></div>
        <?php endif; ?>
        
        <div class="details">
            <table>
                <?php if ($is_exit): ?>
                <tr>
                    <td class="label">Jenis Trx.</td>
                    <td class="colon">:</td>
                    <td class="value">CASH</td>
                </tr>
                <tr>
                    <td class="label">Tiket</td>
                    <td class="colon">:</td>
                    <td class="value"><?= $ticket['ticket_code'] ?></td>
                </tr>
                <tr>
                    <td class="label">Masuk</td>
                    <td class="colon">:</td>
                    <td class="value"><?= date('Y-m-d H:i:s', strtotime($ticket['jam_masuk'])) ?></td>
                </tr>
                <tr>
                    <td class="label">Keluar</td>
                    <td class="colon">:</td>
                    <td class="value"><?= date('Y-m-d H:i:s', strtotime($ticket['jam_keluar'])) ?></td>
                </tr>
                <tr>
                    <td class="label">Plat Nomor</td>
                    <td class="colon">:</td>
                    <td class="value"><?= $ticket['plat_nomor'] ?> / <?= ucfirst($ticket['jenis_kendaraan']) ?></td>
                </tr>
                
                <?php 
                // Kalkulasi ulang untuk format durasi di struk
                if ($ticket['status'] == 'selesai' || isset($_GET['preview_keluar'])) {
                    $jam_keluar_param = isset($_GET['preview_keluar']) ? time() : $ticket['jam_keluar'];
                    $kalkulasi = hitungTarifParkir($ticket['jam_masuk'], $jam_keluar_param, $ticket['jenis_kendaraan']);
                    $dur_str = $kalkulasi['durasi_asli'];
                }
                ?>
                <tr>
                    <td class="label">Durasi</td>
                    <td class="colon">:</td>
                    <td class="value"><?= $dur_str ?></td>
                </tr>
                <tr>
                    <td class="label">Biaya Parkir</td>
                    <td class="colon">:</td>
                    <td class="value"><?= number_format($ticket['biaya'], 0, '.', '.') ?></td>
                </tr>
                <?php else: ?>
                <!-- Format Tiket Masuk -->
                <tr>
                    <td class="label">Tiket</td>
                    <td class="colon">:</td>
                    <td class="value"><?= $ticket['ticket_code'] ?></td>
                </tr>
                <tr>
                    <td class="label">Plat Nomor</td>
                    <td class="colon">:</td>
                    <td class="value"><?= $ticket['plat_nomor'] ?></td>
                </tr>
                <tr>
                    <td class="label">Jenis</td>
                    <td class="colon">:</td>
                    <td class="value"><?= ucfirst($ticket['jenis_kendaraan']) ?></td>
                </tr>
                <tr>
                    <td class="label">Masuk</td>
                    <td class="colon">:</td>
                    <td class="value"><?= date('Y-m-d H:i:s', strtotime($ticket['jam_masuk'])) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
        
        <div class="divider"></div>
        
        <div class="header" style="margin-top: 16px;">
            <?php if ($is_exit): ?>
                <p>Terima kasih atas kunjungan Anda.</p>
                <p>Selamat jalan.</p>
            <?php else: ?>
                <p>Simpan tiket ini untuk keluar.</p>
                <p>Jangan tinggalkan barang berharga Anda.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <button class="no-print btn-print" onclick="window.print()"><?= $is_exit ? 'Cetak Ulang Struk' : 'Cetak Ulang Tiket' ?></button>

    <script>
        function initPrint() {
            <?php if (!$is_exit): ?>
            // Generate QR Code untuk karcis masuk (Kamera laptop/HP sangat cepat membaca QR Code)
            var qr = new QRious({
                element: document.getElementById('qrcode'),
                value: '<?= $ticket['ticket_code'] ?>',
                size: 160,
                level: 'M' // Medium error correction
            });
            <?php endif; ?>

            // Beri waktu sebentar untuk render canvas/image sebelum pop-up print muncul
            setTimeout(function() {
                window.print();
            }, 300);
        }
    </script>
</body>
</html>
