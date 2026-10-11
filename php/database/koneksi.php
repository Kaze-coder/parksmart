<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "parksmart";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (mysqli_connect_errno()){
    echo "Koneksi database gagal : " . mysqli_connect_error();
    exit;
}

// Set Timezone Global ke WIB (Waktu Indonesia Barat)
date_default_timezone_set('Asia/Jakarta');
mysqli_query($koneksi, "SET time_zone = '+07:00'");
?>
