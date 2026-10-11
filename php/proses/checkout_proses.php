<?php
session_start();
require '../database/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ticket_id = (int)$_POST['ticket_id'];
    $ticket_code = mysqli_real_escape_string($koneksi, $_POST['ticket_code']);
    $jam_keluar = mysqli_real_escape_string($koneksi, $_POST['jam_keluar']);
    $total_biaya = (int)$_POST['total_biaya'];

    // Update ticket
    $query = "UPDATE tickets SET jam_keluar = '$jam_keluar', biaya = $total_biaya, status = 'selesai' WHERE id = $ticket_id AND status = 'parkir'";
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: ../../gate_out.php?status=success&ticket_code=$ticket_code");
        exit;
    } else {
        header("Location: ../../gate_out.php?status=error");
        exit;
    }
}
?>
