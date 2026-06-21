<?php
include '../../config/koneksi.php';


if (!isset($_SESSION['admin'])) {
    header("Location: ../../auth/login_admin.php");
    exit;
}

// Validasi ID
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);

// Ambil data produk
$query_produk = mysqli_query(
    $koneksi,
    "SELECT * FROM produk WHERE id_produk='$id'"
);

$data = mysqli_fetch_assoc($query_produk);

// Jika produk tidak ditemukan
if (!$data) {
    echo "
    <script>
        alert('Produk tidak ditemukan!');
        window.location='index.php';
    </script>
    ";
    exit;
}


if (!empty($data['foto']) && file_exists("../../assets/images/" . $data['foto'])) {
    unlink("../../assets/images/" . $data['foto']);
}


if (!empty($data['file_pdf']) && file_exists("../../assets/pdf/" . $data['file_pdf'])) {
    unlink("../../assets/pdf/" . $data['file_pdf']);
}


$hapus = mysqli_query(
    $koneksi,
    "DELETE FROM produk WHERE id_produk='$id'"
);

if ($hapus) {
    echo "
    <script>
        alert('Produk berhasil dihapus!');
        window.location='index.php';
    </script>
    ";
} else {
    echo "
    <script>
        alert('Gagal menghapus produk!');
        window.location='index.php';
    </script>
    ";
}
?>