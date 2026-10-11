<?php
session_start();
require '../database/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../../index.php");
    exit;
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=Riwayat_Tiket_' . date('Y-m-d_H-i') . '.csv');

$output = fopen('php://output', 'w');
fputcsv($output, array('ID Tiket', 'Plat Nomor', 'Jenis', 'Jam Masuk', 'Jam Keluar', 'Biaya', 'Status'));

$where = [];
if (isset($_GET['date']) && !empty($_GET['date'])) {
    $date = mysqli_real_escape_string($koneksi, $_GET['date']);
    // Date format from input is YYYY-MM-DD
    $where[] = "DATE(jam_masuk) = '$date'";
}
if (isset($_GET['jenis']) && !empty($_GET['jenis']) && $_GET['jenis'] != 'Semua Kendaraan') {
    $jenis = strtolower(mysqli_real_escape_string($koneksi, $_GET['jenis']));
    $where[] = "jenis_kendaraan = '$jenis'";
}

$where_sql = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";
$query = mysqli_query($koneksi, "SELECT * FROM tickets $where_sql ORDER BY jam_masuk DESC");

while ($row = mysqli_fetch_assoc($query)) {
    fputcsv($output, array(
        $row['ticket_code'],
        $row['plat_nomor'],
        ucfirst($row['jenis_kendaraan']),
        $row['jam_masuk'],
        $row['jam_keluar'] ? $row['jam_keluar'] : '-',
        $row['biaya'] ? $row['biaya'] : '0',
        ucfirst($row['status'])
    ));
}
fclose($output);
exit;
