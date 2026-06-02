<?php
include '../config/koneksi.php';

// Proteksi: wajib login user
if (!isset($_SESSION['user'])) {

    echo "<script>
        alert('Silakan login terlebih dahulu untuk berbelanja!');
        window.location='../auth/login.php';
    </script>";

    exit;
}


if (isset($_GET['action']) && $_GET['action'] == "add") {

    $id = $_GET['id'];
    $qty = $_POST['qty'];

    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] += $qty;
    } else {
        $_SESSION['cart'][$id] = $qty;
    }

    $aksi = $_POST['aksi'] ?? 'keranjang';

    if ($aksi == 'beli') {

        header("Location: checkout.php");
        exit;

    } else {

        header("Location: cart.php");
        exit;

    }
}


if (isset($_GET['action']) && $_GET['action'] == "delete") {

    $id = $_GET['id'];

    unset($_SESSION['cart'][$id]);

    header("Location: cart.php");
    exit;
}


?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
    content="width=device-width, initial-scale=1.0">

    <title>Keranjang Belanja</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="bg-gray-100">

<?php include '../components/navbar.php'; ?>

<div class="container mx-auto px-4 sm:px-6 py-8 sm:py-12">

    <h2 class="text-2xl sm:text-3xl font-bold mb-6 sm:mb-8">
        Keranjang Belanja Anda
    </h2>

    <?php if (empty($_SESSION['cart'])): ?>

        <div class="bg-white p-6 sm:p-8 rounded-2xl text-center shadow">

            <p class="text-gray-500 text-base sm:text-lg">
                Wah, keranjangmu masih kosong nih.
            </p>

            <a href="produk.php"
            class="inline-block mt-4 text-blue-600 font-bold underline">

                Cari produk sekarang!
            </a>

        </div>

    <?php else: ?>

        <div class="bg-white rounded-2xl shadow-md overflow-hidden">


            <div class="overflow-x-auto">

                <table class="w-full min-w-[700px] text-left">

                    <thead class="bg-gray-50 border-b">

                        <tr>
                            <th class="p-4 sm:p-6">
                                Produk
                            </th>

                            <th class="p-4 sm:p-6 text-center">
                                Jumlah
                            </th>

                            <th class="p-4 sm:p-6 text-right">
                                Harga
                            </th>

                            <th class="p-4 sm:p-6 text-right">
                                Subtotal
                            </th>

                            <th class="p-4 sm:p-6 text-center">
                                Aksi
                            </th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php
                        $total = 0;

                        foreach ($_SESSION['cart'] as $id => $qty):

                            $q = mysqli_query(
                                $koneksi,
                                "SELECT * FROM produk
                                WHERE id_produk = '$id'"
                            );

                            $p = mysqli_fetch_assoc($q);

                            $subtotal =
                            $p['harga'] * $qty;

                            $total += $subtotal;
                        ?>

                        <tr class="border-b hover:bg-gray-50 transition">

                            <td class="p-4 sm:p-6">

                                <div class="flex items-center gap-4">

                                    <img
                                    src="../assets/images/<?= $p['foto'] ?>"
                                    class="w-16 h-16 rounded-lg object-cover shrink-0">

                                    <span class="font-bold text-gray-800">
                                        <?= $p['nama_produk'] ?>
                                    </span>

                                </div>

                            </td>

                            <td class="p-4 sm:p-6 text-center">
                                <?= $qty ?>
                            </td>

                            <td class="p-4 sm:p-6 text-right">
                                Rp <?= number_format(
                                    $p['harga'],
                                    0,
                                    ',',
                                    '.'
                                ) ?>
                            </td>

                            <td class="p-4 sm:p-6 text-right font-bold text-blue-600">

                                Rp <?= number_format(
                                    $subtotal,
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                            </td>

                            <td class="p-4 sm:p-6 text-center">

                                <a href="cart.php?action=delete&id=<?= $id ?>"
                                onclick="return confirm('Hapus produk ini?')"
                                class="text-red-500 hover:text-red-700 text-lg">

                                    <i class="fas fa-trash"></i>

                                </a>

                            </td>

                        </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>


            <div class="p-5 sm:p-8 bg-gray-50 border-t">

                <div class="flex flex-col md:flex-row gap-5 justify-between items-center">

                    <h3 class="text-lg sm:text-xl font-bold text-gray-700 text-center md:text-left">

                        Total Pembayaran:

                        <span class="block md:inline text-2xl sm:text-3xl text-blue-600 md:ml-3">

                            Rp <?= number_format(
                                $total,
                                0,
                                ',',
                                '.'
                            ) ?>

                        </span>

                    </h3>

                    <a href="checkout.php"
                    class="w-full md:w-auto text-center bg-green-500 text-white px-8 sm:px-10 py-4 rounded-2xl font-bold hover:bg-green-600 transition shadow-lg shadow-green-200">

                        Lanjut ke Checkout
                    </a>

                </div>

            </div>

        </div>

    <?php endif; ?>

</div>

</body>
</html>