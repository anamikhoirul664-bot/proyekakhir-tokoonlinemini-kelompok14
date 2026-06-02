<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['user']['id_user'];
$alamat = mysqli_real_escape_string(
    $koneksi,
    $_POST['alamat']
);

$query = "UPDATE users
          SET alamat='$alamat'
          WHERE id_user='$id_user'";

mysqli_query($koneksi, $query);

// Update session juga
$_SESSION['user']['alamat'] = $alamat;

header("Location: profile.php");
exit;
?>