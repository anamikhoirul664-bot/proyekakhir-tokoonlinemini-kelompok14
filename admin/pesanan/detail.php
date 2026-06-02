<?php
include '../../config/koneksi.php';


if (!isset($_SESSION['admin'])) {
    header("Location: ../../auth/login_admin.php");
    exit;
}


$id = $_GET['id'] ?? 0;


$pesanan = mysqli_fetch_assoc(
    mysqli_query(
        $koneksi,
        "SELECT * FROM pesanan
        WHERE id_pesanan='$id'"
    )
);


if (!$pesanan) {
    echo "
    <script>
        alert('Pesanan tidak ditemukan!');
        window.location='index.php';
    </script>
    ";
    exit;
}


$detail = mysqli_query(
    $koneksi,
    "SELECT
    detail_pesanan.*,
    produk.nama_produk,
    produk.harga,
    produk.foto

    FROM detail_pesanan

    JOIN produk
    ON detail_pesanan.id_produk =
    produk.id_produk

    WHERE detail_pesanan.id_pesanan='$id'"
);

if(isset($_POST['ubah_status'])){

    $status = $_POST['status'];

    mysqli_query(
        $koneksi,
        "UPDATE pesanan
        SET status_pesanan='$status'
        WHERE id_pesanan='$id'"
    );

    header("Location: detail.php?id=$id");
    exit;
}

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

<div class="flex min-h-screen">

<aside class="w-72 bg-slate-900 text-white p-6">

    <h1 class="text-3xl font-bold mb-10">
        <span class="text-blue-500">
            Admin
        </span>Panel
    </h1>

    <nav class="space-y-3">

        <a href="../dashboard.php"
        class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 transition">

            <i class="fas fa-chart-line"></i>
            Dashboard
        </a>

        <a href="../produk/index.php"
        class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 transition">

            <i class="fas fa-box"></i>
            Produk
        </a>

        <a href="../kategori/index.php"
        class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 transition">

            <i class="fas fa-tags"></i>
            Kategori
        </a>

        <a href="index.php"
        class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600">

            <i class="fas fa-cart-shopping"></i>
            Pesanan
        </a>

    </nav>

</aside>

<main class="flex-1 p-10">

    <div class="flex justify-between items-center mb-8">

        <div>

            <h1 class="text-4xl font-bold text-slate-800">
                Detail Pesanan
            </h1>

            <p class="text-slate-500 mt-2">
                Informasi lengkap pesanan customer
            </p>

        </div>

        <a href="index.php"
        class="bg-slate-800 text-white px-5 py-3 rounded-2xl hover:bg-slate-700 transition">

            ← Kembali

        </a>

    </div>

    <div class="bg-white rounded-[30px] p-8 shadow-sm mb-8">

        <h2 class="text-2xl font-bold mb-6">
            Data Pembeli
        </h2>

        <div class="grid md:grid-cols-2 gap-5">

            <div>
                <p class="text-slate-500 text-sm">
                    Nama Pembeli
                </p>

                <h3 class="font-bold text-lg">
                    <?= $pesanan['nama_pembeli'] ?>
                </h3>
            </div>

            <div>
                <p class="text-slate-500 text-sm">
                    Metode Pembayaran
                </p>

                <h3 class="font-bold text-lg">
                    <?= $pesanan['metode_pembayaran'] ?>
                </h3>
            </div>

            <div>
                <p class="text-slate-500 text-sm">
                    Nomor HP
                </p>

                <h3 class="font-bold text-lg">
                    <?= $pesanan['no_hp'] ?>
                </h3>
            </div>

            <div class="md:col-span-2">
                <p class="text-slate-500 text-sm">
                    Alamat
                </p>

                <h3 class="font-semibold">
                    <?= $pesanan['alamat'] ?>
                </h3>
            </div>

        </div>

    </div>

    <div class="bg-white rounded-[30px] p-8 shadow-sm mb-8">

        <div class="flex justify-between items-center">

            <div>
                <p class="text-slate-500 text-sm">
                    Status Pesanan
                </p>

                <span class="inline-block mt-2 px-5 py-2 rounded-full font-semibold
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

                    <?= ucfirst($pesanan['status_pesanan']) ?>

                </span>
            </div>

            <form method="POST">

                <select
                name="status"
                class="border px-4 py-3 rounded-xl">

                    <option value="pending">
                        Pending
                    </option>

                    <option value="diproses">
                        Diproses
                    </option>

                    <option value="dikirim">
                        Dikirim
                    </option>

                    <option value="selesai">
                        Selesai
                    </option>

                </select>

                <button
                type="submit"
                name="ubah_status"
                class="bg-blue-600 text-white px-5 py-3 rounded-xl ml-2">

                    Update
                </button>

            </form>

        </div>

    </div>


    <div class="bg-white rounded-[30px] shadow-sm overflow-hidden">

        <div class="p-8 border-b">

            <h2 class="text-2xl font-bold">
                Produk Yang Dibeli
            </h2>

        </div>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="bg-slate-50">

                    <tr>

                        <th class="p-5 text-left">
                            Produk
                        </th>

                        <th>
                            Harga
                        </th>

                        <th>
                            Qty
                        </th>

                        <th>
                            Subtotal
                        </th>

                    </tr>

                </thead>

                <tbody>

                <?php while($row =
                mysqli_fetch_assoc($detail)): ?>

                    <tr class="border-b">

                        <td class="p-5">

                            <div class="flex items-center gap-4">

                                <img
                                src="../../assets/images/<?= $row['foto'] ?>"
                                class="w-20 h-20 rounded-2xl object-cover">

                                <div>

                                    <h3 class="font-bold text-slate-800">
                                        <?= $row['nama_produk'] ?>
                                    </h3>

                                </div>

                            </div>

                        </td>

                        <td>
                            Rp <?= number_format(
                            $row['harga'],
                            0,
                            ',',
                            '.'
                            ) ?>
                        </td>

                        <td>
                            <?= $row['jumlah'] ?>
                        </td>

                        <td class="font-bold text-green-600">

                            Rp <?= number_format(
                            $row['subtotal'],
                            0,
                            ',',
                            '.'
                            ) ?>

                        </td>

                    </tr>

                <?php endwhile; ?>

                </tbody>

            </table>

        </div>


        <div class="bg-slate-50 p-8 flex justify-between items-center">

            <h2 class="text-2xl font-bold">
                Total Pembayaran
            </h2>

            <h2 class="text-4xl font-bold text-blue-600">

                Rp <?= number_format(
                $pesanan['total_bayar'],
                0,
                ',',
                '.'
                ) ?>

            </h2>

        </div>

    </div>

</main>

</div>

</body>
</html>