<?php
include '../config/koneksi.php';


if (!isset($_SESSION['user'])) {
    echo "
    <script>
        alert('Silakan login terlebih dahulu!');
        window.location='../auth/login.php';
    </script>";
    exit;
}


if (empty($_SESSION['cart'])) {
    header("Location: produk.php");
    exit;
}


$total = 0;

foreach ($_SESSION['cart'] as $id => $qty) {

    $p = mysqli_fetch_assoc(
        mysqli_query(
            $koneksi,
            "SELECT harga
            FROM produk
            WHERE id_produk='$id'"
        )
    );

    $total += $p['harga'] * $qty;
}


if (isset($_POST['checkout'])) {

    $id_user = $_SESSION['user']['id_user'];

    $nama = mysqli_real_escape_string(
        $koneksi,
        $_POST['nama']
    );

    $alamat = mysqli_real_escape_string(
        $koneksi,
        $_POST['alamat']
    );

    $no_hp = mysqli_real_escape_string(
        $koneksi,
        $_POST['no_hp']
    );

    $metode = mysqli_real_escape_string(
        $koneksi,
        $_POST['metode_pembayaran']
    );


    $status_pembayaran =
    ($metode == 'COD')
    ? 'belum_bayar'
    : 'menunggu_verifikasi';


    $query = mysqli_query(
        $koneksi,
        "INSERT INTO pesanan (
            id_user,
            nama_pembeli,
            alamat,
            no_hp,
            total_bayar,
            metode_pembayaran,
            status_pembayaran
        ) VALUES (
            '$id_user',
            '$nama',
            '$alamat',
            '$no_hp',
            '$total',
            '$metode',
            '$status_pembayaran'
        )"
    );

    if ($query) {

        $id_pesanan =
        mysqli_insert_id($koneksi);


        foreach (
            $_SESSION['cart']
            as $id_produk => $qty
        ) {

            $produk = mysqli_fetch_assoc(
                mysqli_query(
                    $koneksi,
                    "SELECT *
                    FROM produk
                    WHERE id_produk='$id_produk'"
                )
            );

            $subtotal =
            $produk['harga'] * $qty;

            mysqli_query(
                $koneksi,
                "INSERT INTO detail_pesanan (
                    id_pesanan,
                    id_produk,
                    jumlah,
                    subtotal
                ) VALUES (
                    '$id_pesanan',
                    '$id_produk',
                    '$qty',
                    '$subtotal'
                )"
            );


            mysqli_query(
                $koneksi,
                "UPDATE produk
                SET stok = stok - $qty
                WHERE id_produk='$id_produk'"
            );
        }

        unset($_SESSION['cart']);

        echo "
        <script>
            alert('Pesanan berhasil dibuat!');
            window.location='../index.php';
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Checkout</title>

<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100">

<?php include '../components/navbar.php'; ?>

<div class="max-w-7xl mx-auto px-4 py-8">

    <div class="grid lg:grid-cols-2 gap-8">


        <div class="bg-white p-6 rounded-3xl shadow-sm">

            <h2 class="text-2xl font-bold mb-6">
                Informasi Pengiriman
            </h2>

            <form method="POST" class="space-y-5">

                <div>
                    <label class="block mb-2">
                        Nama Lengkap
                    </label>

                    <input
                    type="text"
                    name="nama"
                    required
                    class="w-full border rounded-xl p-4">
                </div>

                <div>
                    <label class="block mb-2">
                        Nomor HP
                    </label>

                    <input
                    type="text"
                    name="no_hp"
                    required
                    class="w-full border rounded-xl p-4">
                </div>

                <div>
                    <label class="block mb-2">
                        Alamat Lengkap
                    </label>

                    <textarea
                    name="alamat"
                    rows="4"
                    required
                    class="w-full border rounded-xl p-4"></textarea>
                </div>


                <div>

                    <label class="block mb-2 font-semibold">
                        Metode Pembayaran
                    </label>

                    <select
                    name="metode_pembayaran"
                    required
                    class="w-full border rounded-xl p-4">

                        <option value="">
                            -- Pilih Pembayaran --
                        </option>

                        <option value="COD">
                            COD (Bayar di Tempat)
                        </option>

                        <option value="Transfer Bank">
                            Transfer Bank
                        </option>

                        <option value="DANA">
                            DANA
                        </option>

                        <option value="OVO">
                            OVO
                        </option>

                        <option value="GoPay">
                            GoPay
                        </option>

                    </select>

                </div>

                <button
                type="submit"
                name="checkout"
                class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold hover:bg-blue-700 transition">

                    Buat Pesanan
                </button>

            </form>

        </div>


        <div class="bg-white p-6 rounded-3xl shadow-sm">

            <h2 class="text-2xl font-bold mb-6">
                Ringkasan Pesanan
            </h2>

            <div class="space-y-4">

                <?php
                foreach (
                    $_SESSION['cart']
                    as $id => $qty
                ):

                $p = mysqli_fetch_assoc(
                    mysqli_query(
                        $koneksi,
                        "SELECT *
                        FROM produk
                        WHERE id_produk='$id'"
                    )
                );
                ?>

                <div class="flex justify-between">

                    <div>
                        <h3 class="font-semibold">
                            <?= $p['nama_produk'] ?>
                        </h3>

                        <p class="text-sm text-gray-500">
                            <?= $qty ?> x
                            Rp <?= number_format($p['harga'],0,',','.') ?>
                        </p>
                    </div>

                    <span class="font-bold">
                        Rp <?= number_format($p['harga'] * $qty,0,',','.') ?>
                    </span>

                </div>

                <?php endforeach; ?>

                <hr>

                <div class="flex justify-between text-xl font-bold">

                    <span>Total</span>

                    <span class="text-blue-600">
                        Rp <?= number_format($total,0,',','.') ?>
                    </span>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>