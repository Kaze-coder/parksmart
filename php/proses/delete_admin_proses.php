<?php
session_start();
require '../database/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../../index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $delete_id = (int)$_POST['delete_id'];
    $current_user_id = (int)$_SESSION['user_id'];

    // Prevent deleting oneself
    if ($delete_id === $current_user_id) {
        header("Location: ../../settings.php?admin_error=self_delete");
        exit;
    }

    $query = "DELETE FROM users WHERE id = $delete_id";
    if (mysqli_query($koneksi, $query)) {
        header("Location: ../../settings.php?admin_delete=1");
    } else {
        header("Location: ../../settings.php?admin_error=db");
    }
}
?>
