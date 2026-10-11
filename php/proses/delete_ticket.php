<?php
session_start();
require '../database/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../../index.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Only allow deletion if ticket is 'selesai' or if admin has full rights (we'll allow all for now)
    $query = "DELETE FROM tickets WHERE id = $id";
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: ../../history.php?status=deleted");
    } else {
        header("Location: ../../history.php?status=error");
    }
} else {
    header("Location: ../../history.php");
}
exit;
?>
