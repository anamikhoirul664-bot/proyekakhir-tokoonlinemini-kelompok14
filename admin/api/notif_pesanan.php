<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../../config/koneksi.php';

header('Content-Type: application/json');

// Mengambil data pesanan yang status alurnya 'pending' ATAU pembayarannya 'menunggu_verifikasi'
$data = mysqli_query($koneksi, "
    SELECT id_pesanan, nama_pembeli, total_bayar, tanggal_pesan, metode_pembayaran, status_pembayaran
    FROM pesanan
    WHERE status_pesanan = 'pending' 
    OR status_pembayaran = 'menunggu_verifikasi'
    ORDER BY id_pesanan DESC
");

$result = [];

while($row = mysqli_fetch_assoc($data)){
    $row['waktu_format'] = date('H:i', strtotime($row['tanggal_pesan']));
    $row['total_format'] = "Rp " . number_format($row['total_bayar'], 0, ',', '.');
    $result[] = $row;
}

// Mengeluarkan output data berformat JSON murni
echo json_encode([
    'total_notif' => count($result),
    'pesanan_baru' => $result
]);
exit;