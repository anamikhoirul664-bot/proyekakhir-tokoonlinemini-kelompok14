<?php
$host = "sql309.infinityfree.com";
$user = "if0_42192166";
$pass = "NIUroitq1srmdN3";
$db   = "if0_42192166_toko_mini";

$koneksi = mysqli_connect(
    $host,
    $user,
    $pass,
    $db
);

if (!$koneksi) {
    die(
        "Koneksi gagal: " .
        mysqli_connect_error()
    );
}

if (
    session_status() ===
    PHP_SESSION_NONE
) {
    session_start();
}
?>