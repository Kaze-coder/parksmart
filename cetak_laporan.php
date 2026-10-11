<?php
session_start();
require 'php/database/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$today = date('Y-m-d');
$query = mysqli_query($koneksi, "SELECT * FROM tickets WHERE DATE(jam_keluar)='$today' AND status='selesai' ORDER BY jam_keluar DESC");
$total_pendapatan = 0;
$transaksi = [];
while ($row = mysqli_fetch_assoc($query)) {
    $transaksi[] = $row;
    $total_pendapatan += $row['biaya'];
}

// Get admin info
$admin_name = $_SESSION['name'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Pendapatan - ParkSmart</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; margin: 0; padding: 20px; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 24px; color: #00236f; }
        .header p { margin: 5px 0 0; color: #666; }
        .summary { margin-bottom: 20px; }
        .summary p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table th, table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table th { background-color: #f4f3fa; font-weight: bold; }
        table tr:nth-child(even) { background-color: #f9f9f9; }
        .total-row { font-weight: bold; background-color: #e8effa !important; }
        .footer { margin-top: 40px; text-align: right; }
        .signature { display: inline-block; text-align: center; }
        .signature-line { margin-top: 60px; border-top: 1px solid #333; width: 200px; padding-top: 5px; }
        .btn-print {
            padding: 10px 20px;
            background-color: #00236f;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
            @page { margin: 20mm; }
        }
    </style>
</head>
<body>
    <button class="btn-print no-print" onclick="window.print()">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M19 8H5V3H19V8ZM16 11.5C16.2833 11.5 16.5208 11.4042 16.7125 11.2125C16.9042 11.0208 17 10.7833 17 10.5C17 10.2167 16.9042 9.97917 16.7125 9.7875C16.5208 9.59583 16.2833 9.5 16 9.5C15.7167 9.5 15.4792 9.59583 15.2875 9.7875C15.0958 9.97917 15 10.2167 15 10.5C15 10.7833 15.0958 11.0208 15.2875 11.2125C15.4792 11.4042 15.7167 11.5 16 11.5ZM18 19V15H6V19H18ZM20 19V13C20 12.45 19.8042 11.9792 19.4125 11.5875C19.0208 11.1958 18.55 11 18 11H6C5.45 11 4.97917 11.1958 4.5875 11.5875C4.19583 11.9792 4 12.45 4 13V19H2V21H22V19H20ZM18 21H6C5.45 21 4.97917 20.8042 4.5875 20.4125C4.19583 20.0208 4 19.55 4 19V13C4 12.45 4.19583 11.9792 4.5875 11.5875C4.97917 11.1958 5.45 11 6 11H18C18.55 11 19.0208 11.1958 19.4125 11.5875C19.8042 11.9792 20 12.45 20 13V19C20 19.55 19.8042 20.0208 19.4125 20.4125C19.0208 20.8042 18.55 21 18 21Z" fill="currentColor"/>
        </svg>
        Cetak Dokumen
    </button>

    <div class="header">
        <h1>ParkSmart - Laporan Pendapatan</h1>
        <p>Ringkasan Transaksi Kendaraan Keluar</p>
    </div>

    <div class="summary">
        <p><strong>Tanggal Laporan:</strong> <?= date('d F Y', strtotime($today)) ?></p>
        <p><strong>Total Transaksi:</strong> <?= count($transaksi) ?> Kendaraan</p>
        <p><strong>Total Pendapatan:</strong> Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ID Tiket</th>
                <th>Plat Nomor</th>
                <th>Jenis</th>
                <th>Jam Masuk</th>
                <th>Jam Keluar</th>
                <th>Total Biaya</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($transaksi) > 0): ?>
                <?php $no = 1; foreach($transaksi as $t): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $t['ticket_code'] ?></td>
                    <td><?= $t['plat_nomor'] ?></td>
                    <td style="text-transform: capitalize;"><?= $t['jenis_kendaraan'] ?></td>
                    <td><?= date('H:i:s', strtotime($t['jam_masuk'])) ?></td>
                    <td><?= date('H:i:s', strtotime($t['jam_keluar'])) ?></td>
                    <td>Rp <?= number_format($t['biaya'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="6" style="text-align: right;">Total Pendapatan</td>
                    <td>Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada data transaksi untuk hari ini.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            <p><?= date('d F Y') ?></p>
            <div class="signature-line">
                <?= htmlspecialchars($admin_name) ?><br>
                Administrator
            </div>
        </div>
    </div>
    
    <script>
        // Optional auto-print on load
        window.onload = function() { window.print(); }
    </script>
</body>
</html>
