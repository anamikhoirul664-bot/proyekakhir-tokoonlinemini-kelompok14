<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../config/koneksi.php';

if (isset($_POST['id_pesanan']) && isset($_POST['aksi'])) {
    $id_pesanan = (int)$_POST['id_pesanan'];
    $aksi = $_POST['aksi'];

    if ($aksi === 'terima') {

        $update = mysqli_query($koneksi, "UPDATE pesanan SET 
            status_pembayaran = 'dibayar', 
            status_pesanan = 'diproses' 
            WHERE id_pesanan = '$id_pesanan'");

        if ($update) {
            echo "<script>alert('Pembayaran berhasil diverifikasi!'); window.location='admin_verifikasi.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui database: " . mysqli_error($koneksi) . "'); window.location='admin_verifikasi.php';</script>";
        }
        exit;
    }
} else {
    header("Location: admin_verifikasi.php");
    exit;
}
?>