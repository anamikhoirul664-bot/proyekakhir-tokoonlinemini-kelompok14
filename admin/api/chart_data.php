<?php
include '../../config/koneksi.php';

header('Content-Type: application/json');


$query = mysqli_query($koneksi, "
SELECT 
    MONTH(tanggal_pesan) AS bulan,
    SUM(CASE 
        WHEN status_pembayaran = 'dibayar' 
        THEN total_bayar 
        ELSE 0 
    END) AS penjualan,
    COUNT(id_pesanan) AS jumlah
FROM pesanan
GROUP BY MONTH(tanggal_pesan)
ORDER BY bulan ASC
");


$bulan = [];
$penjualan = [];
$jumlah = [];


while($row = mysqli_fetch_assoc($query)){

    $bulan[] = (int)$row['bulan'];

    // penting: anti NULL
    $penjualan[] = (int)$row['penjualan'];

    $jumlah[] = (int)$row['jumlah'];
}


echo json_encode([
    "bulan" => $bulan,
    "penjualan" => $penjualan,
    "jumlah" => $jumlah
]);
?>