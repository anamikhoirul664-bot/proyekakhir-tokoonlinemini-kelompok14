<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['user']['id_user'];

$password_lama = $_POST['password_lama'];
$password_baru = $_POST['password_baru'];
$konfirmasi    = $_POST['konfirmasi'];

// Ambil data user
$query = mysqli_query(
    $koneksi,
    "SELECT * FROM users WHERE id_user='$id_user'"
);

$user = mysqli_fetch_assoc($query);

// Cek password lama
if (!password_verify($password_lama, $user['password'])) {

    echo "<script>
        alert('Password lama salah!');
        window.location='ubah_password.php';
    </script>";
    exit;
}

// Cek konfirmasi password
if ($password_baru != $konfirmasi) {

    echo "<script>
        alert('Konfirmasi password tidak cocok!');
        window.location='ubah_password.php';
    </script>";
    exit;
}

// Hash password baru
$password_hash = password_hash(
    $password_baru,
    PASSWORD_DEFAULT
);

// Update password
mysqli_query(
    $koneksi,
    "UPDATE users
     SET password='$password_hash'
     WHERE id_user='$id_user'"
);

echo "<script>
    alert('Password berhasil diubah');
    window.location='profile.php';
</script>";
exit;
?>