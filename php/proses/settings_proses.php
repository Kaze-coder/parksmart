<?php
session_start();
require '../database/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Extract admin email to update users table
    $admin_email = $_POST['admin_email'] ?? '';
    
    // Updates for settings table
    $updates = [
        'location' => $_POST['location'] ?? '',
        'tarif_motor_1' => $_POST['tarif_motor_1'] ?? '',
        'tarif_motor_next' => $_POST['tarif_motor_next'] ?? '',
        'tarif_motor_max' => $_POST['tarif_motor_max'] ?? '',
        'tarif_mobil_1' => $_POST['tarif_mobil_1'] ?? '',
        'tarif_mobil_next' => $_POST['tarif_mobil_next'] ?? '',
        'tarif_mobil_max' => $_POST['tarif_mobil_max'] ?? ''
    ];

    $admin_name = $_POST['admin_name'] ?? '';

    // Update users table
    $user_id = $_SESSION['user_id'];
    $user_updates = [];
    
    if (!empty($admin_email)) {
        $admin_email = mysqli_real_escape_string($koneksi, $admin_email);
        $user_updates[] = "email = '$admin_email'";
    }
    
    if (!empty($admin_name)) {
        $admin_name_esc = mysqli_real_escape_string($koneksi, $admin_name);
        $user_updates[] = "name = '$admin_name_esc'";
        $_SESSION['name'] = $admin_name; // Update session
    }

    // Handle Profile Picture Upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = $_FILES['profile_picture']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        $allowedExts = ['jpg', 'jpeg', 'png'];
        if (in_array($fileExtension, $allowedExts)) {
            $newFileName = 'profile_' . $user_id . '_' . time() . '.' . $fileExtension;
            $uploadFileDir = '../../assets/img/profiles/';
            $dest_path = $uploadFileDir . $newFileName;
            
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $db_path = 'assets/img/profiles/' . $newFileName;
                $user_updates[] = "profile_picture = '$db_path'";
                $_SESSION['profile_picture'] = $db_path; // Update session
            }
        }
    }

    if (!empty($user_updates)) {
        $update_str = implode(", ", $user_updates);
        mysqli_query($koneksi, "UPDATE users SET $update_str WHERE id = '$user_id'");
    }

    foreach ($updates as $key => $value) {
        $key = mysqli_real_escape_string($koneksi, $key);
        $value = mysqli_real_escape_string($koneksi, $value);
        
        // Update setting or insert if it doesn't exist
        $query = "INSERT INTO settings (setting_key, setting_value) VALUES ('$key', '$value') 
                  ON DUPLICATE KEY UPDATE setting_value = '$value'";
        mysqli_query($koneksi, $query);
    }
    
    header("Location: ../../settings.php?success=1");
    exit;
} else {
    header("Location: ../../settings.php");
    exit;
}
?>
