<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['user']['id_user'];

$nama  = mysqli_real_escape_string($koneksi, $_POST['nama']);
$email = mysqli_real_escape_string($koneksi, $_POST['email']);
$no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);

$foto = $_SESSION['user']['foto'] ?? '';

// Jika upload foto baru
if(isset($_FILES['foto']) && $_FILES['foto']['error'] == 0){

    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);

    $namaFile = time() . "." . $ext;

    $folder = "../assets/profile/" . $namaFile;

    if(move_uploaded_file($_FILES['foto']['tmp_name'], $folder)){
        $foto = $namaFile;
    }
}

$query = "UPDATE users SET
            nama='$nama',
            email='$email',
            no_hp='$no_hp',
            foto='$foto'
          WHERE id_user='$id_user'";

mysqli_query($koneksi, $query);

// Ambil data terbaru
$data = mysqli_fetch_assoc(
    mysqli_query(
        $koneksi,
        "SELECT * FROM users WHERE id_user='$id_user'"
    )
);

$_SESSION['user'] = $data;

header("Location: profile.php");
exit;