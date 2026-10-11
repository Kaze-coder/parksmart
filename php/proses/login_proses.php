<?php
session_start();
require '../database/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    $query = "SELECT * FROM users WHERE username = '$username' OR email = '$username'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['login'] = true;
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['profile_picture'] = $row['profile_picture'] ?? null;
            
            header("Location: ../../dashboard.php");
            exit;
        }
    }
    
    // Login failed
    header("Location: ../../index.php?error=1");
    exit;
} else {
    header("Location: ../../index.php");
    exit;
}
?>
