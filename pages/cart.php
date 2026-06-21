<?php
include '../config/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. PROTEKSI LOGIN: Jika belum login, lempar ke halaman login
if (!isset($_SESSION['user'])) {
    echo "<script>
            alert('Silakan login terlebih dahulu untuk mengakses keranjang!');
            window.location.href = '../auth/login.php';
          </script>";
    exit;
}

// Ambil ID User yang sedang login (Hasilnya 4)
$id_user = (int)$_SESSION['user']['id_user'];



// 2. PROSES BERBAGAI AKSI JIKA TOMBOL DIKLIK
if (isset($_GET['action']) && isset($_GET['id'])) {
    
    $action = $_GET['action'];
    $id_produk = (int)$_GET['id'];

    // --- AKSI 1: TAMBAH PRODUK DARI HALAMAN DETAIL ---
    if ($action == 'add') {
        $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;
        if ($qty < 1) { $qty = 1; }

        $cek_database = mysqli_query($koneksi, "SELECT * FROM cart WHERE id_user = $id_user AND id_produk = $id_produk");

        if (mysqli_num_rows($cek_database) > 0) {
            $query_update = "UPDATE cart SET qty = qty + $qty WHERE id_user = $id_user AND id_produk = $id_produk";
            mysqli_query($koneksi, $query_update);
        } else {
            $query_insert = "INSERT INTO cart (id_user, id_produk, qty) VALUES ($id_user, $id_produk, $qty)";
            mysqli_query($koneksi, $query_insert);
        }

        if (isset($_POST['aksi']) && $_POST['aksi'] == 'beli') {
            header("Location: checkout.php");
        } else {
            header("Location: cart.php");
        }
        exit;
    }

    // --- AKSI 2: TOMBOL TOMBOL TAMBAH (+) DI HALAMAN CART ---
    if ($action == 'plus') {
        // Ambil stok maksimal produk dari tabel produk agar penambahan tidak melebihi stok
        $cek_stok = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT stok FROM produk WHERE id_produk = $id_produk"));
        $stok_maksal = (int)$cek_stok['stok'];

        // Cek qty saat ini di keranjang
        $cek_cart = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT qty FROM cart WHERE id_user = $id_user AND id_produk = $id_produk"));
        $qty_sekarang = (int)$cek_cart['qty'];

        if ($qty_sekarang < $stok_maksal) {
            mysqli_query($koneksi, "UPDATE cart SET qty = qty + 1 WHERE id_user = $id_user AND id_produk = $id_produk");
        } else {
            echo "<script>alert('Tidak bisa menambah! Jumlah keranjang sudah mencapai batas stok produk.');</script>";
        }
        header("Location: cart.php");
        exit;
    }

    // --- AKSI 3: TOMBOL KURANG (-) DI HALAMAN CART ---
    if ($action == 'minus') {
        $cek_cart = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT qty FROM cart WHERE id_user = $id_user AND id_produk = $id_produk"));
        $qty_sekarang = (int)$cek_cart['qty'];

        if ($qty_sekarang > 1) {
            // Jika jumlah lebih dari 1, kurangi 1 data
            mysqli_query($koneksi, "UPDATE cart SET qty = qty - 1 WHERE id_user = $id_user AND id_produk = $id_produk");
        } else {
            // Jika jumlahnya sudah tinggal 1 lalu dikurangi lagi, hapus barang dari keranjang
            mysqli_query($koneksi, "DELETE FROM cart WHERE id_user = $id_user AND id_produk = $id_produk");
        }
        header("Location: cart.php");
        exit;
    }

    // --- AKSI 4: TOMBOL HAPUS (TRASH) DI HALAMAN CART ---
    if ($action == 'delete') {
        mysqli_query($koneksi, "DELETE FROM cart WHERE id_user = $id_user AND id_produk = $id_produk");
        header("Location: cart.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-gray-100">

<?php include '../components/navbar.php'; ?>

<div class="container mx-auto px-4 sm:px-6 py-8 sm:py-12">

    <h2 class="text-2xl sm:text-3xl font-bold mb-6 sm:mb-8">
        Keranjang Belanja Anda
    </h2>

    <?php
    $query_tampil = mysqli_query($koneksi, "SELECT cart.*, produk.nama_produk, produk.harga, produk.foto 
                                            FROM cart 
                                            JOIN produk ON cart.id_produk = produk.id_produk 
                                            WHERE cart.id_user = '$id_user'");

    if (mysqli_num_rows($query_tampil) == 0): 
    ?>
        <div class="bg-white p-6 sm:p-8 rounded-2xl text-center shadow">
            <p class="text-gray-500 text-base sm:text-lg">
                Wah, keranjangmu masih kosong nih.
            </p>
            <a href="produk.php" class="inline-block mt-4 text-blue-600 font-bold underline">
                Cari produk sekarang!
            </a>
        </div>
    <?php else: ?>

        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px] text-left">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="p-4 sm:p-6">Produk</th>
                            <th class="p-4 sm:p-6 text-center">Jumlah</th>
                            <th class="p-4 sm:p-6 text-right">Harga</th>
                            <th class="p-4 sm:p-6 text-right">Subtotal</th>
                            <th class="p-4 sm:p-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $total_pembayaran = 0; 
                    
                    while ($row = mysqli_fetch_assoc($query_tampil)):
                        $id_p = (int)$row['id_produk'];
                        $qty = (int)$row['qty'];
                        $subtotal = (int)$row['harga'] * $qty;
                        
                        $total_pembayaran += $subtotal; 
                    ?>
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="p-4 sm:p-6">
                                <div class="flex items-center gap-4">
                                    <img src="../assets/images/<?= !empty($row['foto']) ? $row['foto'] : 'default.jpg' ?>" class="w-16 h-16 rounded-lg object-cover shrink-0">
                                    <span class="font-bold text-gray-800"><?= $row['nama_produk'] ?></span>
                                </div>
                            </td>

                            <td class="p-4 sm:p-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="cart.php?action=minus&id=<?= $id_p ?>" class="w-8 h-8 bg-red-100 text-red-600 rounded-full flex items-center justify-center hover:bg-red-200 font-bold">
                                        -
                                    </a>
                                    <span class="font-bold px-2"><?= $qty ?></span>
                                    <a href="cart.php?action=plus&id=<?= $id_p ?>" class="w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center hover:bg-green-200 font-bold">
                                        +
                                    </a>
                                </div>
                            </td>

                            <td class="p-4 sm:p-6 text-right">
                                Rp <?= number_format($row['harga'], 0, ',', '.') ?>
                            </td>

                            <td class="p-4 sm:p-6 text-right font-bold text-blue-600">
                                Rp <?= number_format($subtotal, 0, ',', '.') ?>
                            </td>

                            <td class="p-4 sm:p-6 text-center">
                                <a href="cart.php?action=delete&id=<?= $id_p ?>" onclick="return confirm('Hapus produk ini dari keranjang?')" class="text-red-500 hover:text-red-700 text-lg">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="p-5 sm:p-8 bg-gray-50 border-t">
                <div class="flex flex-col md:flex-row gap-5 justify-between items-center">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-700 text-center md:text-left">
                        Total Pembayaran:
                        <span class="block md:inline text-2xl sm:text-3xl text-blue-600 md:ml-3">
                            Rp <?= number_format($total_pembayaran, 0, ',', '.') ?>
                        </span>
                    </h3>
                    <a href="checkout.php" class="w-full md:w-auto text-center bg-green-500 text-white px-8 sm:px-10 py-4 rounded-2xl font-bold hover:bg-green-600 transition shadow-lg shadow-green-200">
                        Lanjut ke Checkout
                    </a>
                </div>
            </div>
        </div>

    <?php endif; ?>

</div>

</body>
</html>