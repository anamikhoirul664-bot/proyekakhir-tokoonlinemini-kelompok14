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

$id_user = $_SESSION['user']['id_user'];


$query = mysqli_query(
    $koneksi,
    "SELECT *
    FROM pesanan
    WHERE id_user='$id_user'
    ORDER BY tanggal_pesan DESC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Pesanan Saya</title>

<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="bg-slate-100">

<?php include '../components/navbar.php'; ?>

<div class="max-w-6xl mx-auto px-4 py-8">

    <div class="mb-8">

        <h1 class="text-3xl font-bold text-slate-800">
            Pesanan Saya
        </h1>

        <p class="text-slate-500 mt-2">
            Riwayat semua pembelian anda
        </p>

    </div>

    <?php if(mysqli_num_rows($query) > 0): ?>

        <div class="space-y-5">

        <?php while($row =
        mysqli_fetch_assoc($query)): ?>

        <div class="bg-white rounded-[25px] p-6 shadow-sm">

            <div class="flex flex-col md:flex-row md:justify-between gap-4">

                <div>

                    <h2 class="font-bold text-xl text-slate-800">
                        #TRX-<?= $row['id_pesanan'] ?>
                    </h2>

                    <p class="text-slate-500 mt-1">
                        <?= date(
                        'd M Y H:i',
                        strtotime(
                        $row['tanggal_pesan']
                        )) ?>
                    </p>

                </div>

                <div class="flex flex-wrap gap-3">


                    <span class="px-4 py-2 rounded-full text-sm font-semibold
                    <?php
                    if($row['status_pembayaran'] == 'belum_bayar'){
                        echo 'bg-red-100 text-red-600';
                    } elseif($row['status_pembayaran'] == 'menunggu_verifikasi'){
                        echo 'bg-yellow-100 text-yellow-600';
                    } else {
                        echo 'bg-green-100 text-green-600';
                    }
                    ?>">
                        <?= ucfirst(
                        str_replace(
                        '_',
                        ' ',
                        $row['status_pembayaran']
                        )) ?>
                    </span>


                    <span class="px-4 py-2 rounded-full text-sm font-semibold
                    <?php
                    if($row['status_pesanan'] == 'pending'){
                        echo 'bg-yellow-100 text-yellow-600';
                    } elseif($row['status_pesanan'] == 'diproses'){
                        echo 'bg-blue-100 text-blue-600';
                    } elseif($row['status_pesanan'] == 'dikirim'){
                        echo 'bg-purple-100 text-purple-600';
                    } else {
                        echo 'bg-green-100 text-green-600';
                    }
                    ?>">
                        <?= ucfirst($row['status_pesanan']) ?>
                    </span>

                </div>

            </div>

            <div class="border-t mt-5 pt-5">

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">

                <!-- INFO -->
                <div class="grid sm:grid-cols-2 gap-6">

                    <div>
                        <p class="text-slate-500 text-sm">
                            Metode Pembayaran
                        </p>

                        <h3 class="font-semibold text-slate-800 mt-1">
                            <?= $row['metode_pembayaran'] ?>
                        </h3>
                    </div>

                    <div>
                        <p class="text-slate-500 text-sm">
                            Total Bayar
                        </p>

                        <h3 class="font-bold text-blue-600 text-2xl mt-1">
                            Rp <?= number_format(
                                $row['total_bayar'],
                                0,
                                ',',
                                '.'
                            ) ?>
                        </h3>
                    </div>

                </div>

                <!-- BUTTON -->
                <a href="detail_pesanan.php?id=<?= $row['id_pesanan'] ?>"
                class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl transition shadow-md hover:shadow-lg font-semibold">

                    <i class="fas fa-eye"></i>
                    Detail Pesanan

                </a>

            </div>

        </div>

        </div>

        <?php endwhile; ?>

        </div>

    <?php else: ?>

    <div class="bg-white rounded-[30px] p-16 text-center">

        <i class="fas fa-box-open text-6xl text-slate-300 mb-5"></i>

        <h2 class="text-2xl font-bold text-slate-700">
            Belum ada pesanan
        </h2>

        <p class="text-slate-500 mt-2">
            Yuk mulai belanja sekarang
        </p>

        <a href="produk.php"
        class="inline-block mt-5 bg-blue-600 text-white px-6 py-3 rounded-xl">

            Belanja Sekarang
        </a>

    </div>

    <?php endif; ?>

</div>

</body>
</html>