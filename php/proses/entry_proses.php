<?php
session_start();
require '../database/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $plat_nomor = mysqli_real_escape_string($koneksi, strtoupper($_POST['plat_nomor']));
    $jenis_kendaraan = mysqli_real_escape_string($koneksi, strtolower($_POST['jenis_kendaraan']));
    $jam_masuk = date('Y-m-d H:i:s');

    // Check Capacity
    // 1. Get settings
    $q_set = mysqli_query($koneksi, "SELECT * FROM settings WHERE setting_key = 'kapasitas_$jenis_kendaraan'");
    $kapasitas = mysqli_fetch_assoc($q_set)['setting_value'];

    // 2. Get current parked count
    $q_occ = mysqli_query($koneksi, "SELECT COUNT(*) as count FROM tickets WHERE jenis_kendaraan='$jenis_kendaraan' AND status='parkir'");
    $occupied = mysqli_fetch_assoc($q_occ)['count'];

    if ($occupied >= $kapasitas) {
    header("Location: ../../gate_entry.php?status=full");
        exit;
    }

    // Generate unique ticket code: TKT-YYYYMMDD-ID
    // First, insert with a placeholder or just use auto increment ID to generate it
    $q_insert = "INSERT INTO tickets (ticket_code, plat_nomor, jenis_kendaraan, jam_masuk, status) 
                 VALUES ('TEMP', '$plat_nomor', '$jenis_kendaraan', '$jam_masuk', 'parkir')";
    
    if (mysqli_query($koneksi, $q_insert)) {
        $id = mysqli_insert_id($koneksi);
        $date_part = date('Ymd');
        $ticket_code = "TKT-$date_part-" . str_pad($id, 4, '0', STR_PAD_LEFT);
        
        // Update the temp code
        mysqli_query($koneksi, "UPDATE tickets SET ticket_code='$ticket_code' WHERE id=$id");

        header("Location: ../../gate_entry.php?status=success&print=$ticket_code");
        exit;
    } else {
        header("Location: ../../gate_entry.php?status=error");
        exit;
    }
}
?>
