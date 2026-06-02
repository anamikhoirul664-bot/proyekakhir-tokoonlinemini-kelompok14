<?php
include '../config/koneksi.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['user']['id_user'];
$id_pesanan = $_GET['id'] ?? 0;


$pesanan = mysqli_fetch_assoc(
    mysqli_query(
        $koneksi,
        "SELECT * FROM pesanan
        WHERE id_pesanan='$id_pesanan'
        AND id_user='$id_user'"
    )
);

if (!$pesanan) {
    echo "
    <script>
        alert('Pesanan tidak ditemukan');
        window.location='pesanan_saya.php';
    </script>
    ";
    exit;
}


$detail = mysqli_query(
    $koneksi,
    "SELECT
    dp.*,
    p.nama_produk,
    p.foto,
    p.harga

    FROM detail_pesanan dp

    JOIN produk p
    ON dp.id_produk = p.id_produk

    WHERE dp.id_pesanan='$id_pesanan'"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Detail Pesanan</title>

<script src="https://cdn.tailwindcss.com"></script>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="bg-slate-100">

<?php include '../components/navbar.php'; ?>

<div class="max-w-5xl mx-auto px-4 py-6 sm:py-8">


    <div class="bg-white p-5 sm:p-6 rounded-[28px] shadow-sm mb-6">

        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">
            Detail Pesanan
        </h1>

        <p class="text-slate-500 mt-2 text-sm sm:text-base">
            ID Pesanan:
            <span class="font-semibold">
                #TRX-<?= $pesanan['id_pesanan'] ?>
            </span>
        </p>

        <p class="text-slate-500 text-sm">
            <?= date(
                'd M Y H:i',
                strtotime($pesanan['tanggal_pesan'])
            ) ?>
        </p>

    </div>


    <div class="bg-white p-5 sm:p-6 rounded-[28px] shadow-sm mb-6">

        <h2 class="font-bold text-lg mb-4">
            Status Pesanan
        </h2>

        <div class="flex flex-wrap gap-3">


            <span class="px-4 py-2 rounded-full text-sm font-semibold
            <?php
            if($pesanan['status_pembayaran'] == 'belum_bayar'){
                echo 'bg-red-100 text-red-600';
            } elseif($pesanan['status_pembayaran'] == 'menunggu_verifikasi'){
                echo 'bg-yellow-100 text-yellow-600';
            } else {
                echo 'bg-green-100 text-green-600';
            }
            ?>">
                Pembayaran:
                <?= ucfirst(
                    str_replace(
                        '_',
                        ' ',
                        $pesanan['status_pembayaran']
                    )
                ) ?>
            </span>


            <span class="px-4 py-2 rounded-full text-sm font-semibold
            <?php
            if($pesanan['status_pesanan'] == 'pending'){
                echo 'bg-yellow-100 text-yellow-600';
            } elseif($pesanan['status_pesanan'] == 'diproses'){
                echo 'bg-blue-100 text-blue-600';
            } elseif($pesanan['status_pesanan'] == 'dikirim'){
                echo 'bg-purple-100 text-purple-600';
            } else {
                echo 'bg-green-100 text-green-600';
            }
            ?>">
                Pesanan:
                <?= ucfirst($pesanan['status_pesanan']) ?>
            </span>

        </div>

    </div>


    <div class="bg-white p-5 sm:p-6 rounded-[28px] shadow-sm mb-6">

        <h2 class="font-bold text-lg mb-5">
            Produk Pesanan
        </h2>

        <div class="space-y-5">

        <?php while($row = mysqli_fetch_assoc($detail)): ?>

            <div class="flex flex-col sm:flex-row gap-4 border-b pb-5">


                <img
                src="../assets/images/<?= $row['foto'] ?>"
                class="w-full sm:w-24 h-52 sm:h-24 object-cover rounded-2xl">


                <div class="flex-1">

                    <h3 class="font-bold text-slate-800 text-lg">
                        <?= $row['nama_produk'] ?>
                    </h3>

                    <p class="text-slate-500 text-sm mt-1">
                        Qty:
                        <?= $row['jumlah'] ?>
                    </p>

                    <p class="text-slate-500 text-sm">
                        Harga:
                        Rp <?= number_format(
                            $row['harga'],
                            0,
                            ',',
                            '.'
                        ) ?>
                    </p>

                </div>


                <div class="sm:text-right">

                    <p class="text-sm text-slate-500">
                        Subtotal
                    </p>

                    <h3 class="font-bold text-blue-600 text-lg">
                        Rp <?= number_format(
                            $row['subtotal'],
                            0,
                            ',',
                            '.'
                        ) ?>
                    </h3>

                </div>

            </div>

        <?php endwhile; ?>

        </div>

    </div>


    <div class="bg-white p-5 sm:p-6 rounded-[28px] shadow-sm">

        <h2 class="font-bold text-lg mb-5">
            Ringkasan Pembayaran
        </h2>

        <div class="space-y-4">

            <div class="flex justify-between">

                <span class="text-slate-500">
                    Metode Pembayaran
                </span>

                <span class="font-semibold">
                    <?= $pesanan['metode_pembayaran'] ?>
                </span>

            </div>

            <div class="flex justify-between text-xl font-bold border-t pt-4">

                <span>Total</span>

                <span class="text-blue-600">
                    Rp <?= number_format(
                        $pesanan['total_bayar'],
                        0,
                        ',',
                        '.'
                    ) ?>
                </span>

            </div>

        </div>

    </div>

</div>

</body>
</html>