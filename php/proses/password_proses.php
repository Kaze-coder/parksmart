<?php
session_start();
require '../database/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate length
    if (strlen($new_password) < 6) {
        header("Location: ../../settings.php?pw_error=short");
        exit;
    }

    // Validate match
    if ($new_password !== $confirm_password) {
        header("Location: ../../settings.php?pw_error=mismatch");
        exit;
    }

    // Verify old password
    $query = mysqli_query($koneksi, "SELECT password FROM users WHERE id = '$user_id'");
    if ($row = mysqli_fetch_assoc($query)) {
        if (password_verify($old_password, $row['password'])) {
            // Hash new password and update
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            mysqli_query($koneksi, "UPDATE users SET password = '$hashed_password' WHERE id = '$user_id'");
            
            header("Location: ../../settings.php?pw_success=1");
            exit;
        } else {
            header("Location: ../../settings.php?pw_error=wrong");
            exit;
        }
    } else {
        header("Location: ../../index.php");
        exit;
    }
} else {
    header("Location: ../../settings.php");
    exit;
}
?>
