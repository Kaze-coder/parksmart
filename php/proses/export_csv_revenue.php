<?php
session_start();
require '../database/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../../index.php");
    exit;
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=Laporan_Pendapatan_' . date('Y-m-d_H-i') . '.csv');

$output = fopen('php://output', 'w');
fputcsv($output, array('Laporan Pendapatan Hari Ini', date('Y-m-d')));
fputcsv($output, array(''));
fputcsv($output, array('ID Tiket', 'Plat Nomor', 'Jenis', 'Jam Masuk', 'Jam Keluar', 'Durasi (Menit)', 'Biaya'));

$today = date('Y-m-d');
$query = mysqli_query($koneksi, "SELECT * FROM tickets WHERE DATE(jam_keluar)='$today' AND status='selesai' ORDER BY jam_keluar DESC");

$total_biaya = 0;
while ($row = mysqli_fetch_assoc($query)) {
    $in = strtotime($row['jam_masuk']);
    $out = strtotime($row['jam_keluar']);
    $diff = $out - $in;
    $minutes = floor($diff / 60);

    fputcsv($output, array(
        $row['ticket_code'],
        $row['plat_nomor'],
        ucfirst($row['jenis_kendaraan']),
        $row['jam_masuk'],
        $row['jam_keluar'],
        $minutes,
        $row['biaya']
    ));
    $total_biaya += $row['biaya'];
}

fputcsv($output, array(''));
fputcsv($output, array('Total Pendapatan', '', '', '', '', '', $total_biaya));

fclose($output);
exit;
