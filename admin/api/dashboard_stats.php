<?php
include '../../config/koneksi.php';

header('Content-Type: application/json');

$pendapatan = mysqli_fetch_assoc(mysqli_query(
    $koneksi,
    "SELECT SUM(total_bayar) as total FROM pesanan WHERE status_pembayaran='dibayar'"
));

$data = [
    "produk" => mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM produk")),
    "pesanan" => mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM pesanan")),
    "user" => mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM users")),
    "pendapatan" => $pendapatan['total'] ?? 0,
];

echo json_encode($data);