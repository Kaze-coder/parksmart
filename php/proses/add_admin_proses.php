<?php
session_start();
require '../database/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../../index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($koneksi, trim($_POST['admin_name']));
    $username = mysqli_real_escape_string($koneksi, trim($_POST['admin_username']));
    $email = mysqli_real_escape_string($koneksi, trim($_POST['admin_email']));
    $password = $_POST['admin_password'];

    // Check if username already exists
    $check = mysqli_query($koneksi, "SELECT id FROM users WHERE username = '$username'");
    if (mysqli_num_rows($check) > 0) {
        header("Location: ../../settings.php?admin_error=exists");
        exit;
    }

    // Minimum password length validation
    if (strlen($password) < 6) {
        header("Location: ../../settings.php?admin_error=short_pw");
        exit;
    }

    // Hash password with Bcrypt
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $query = "INSERT INTO users (username, email, password, name) VALUES ('$username', '$email', '$hashed_password', '$name')";

    if (mysqli_query($koneksi, $query)) {
        header("Location: ../../settings.php?admin_success=1");
    } else {
        header("Location: ../../settings.php?admin_error=db");
    }
}
?>
