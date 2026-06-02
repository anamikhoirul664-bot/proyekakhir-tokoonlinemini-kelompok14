<?php
include '../../config/koneksi.php';


if (!isset($_SESSION['admin'])) {
    header("Location: ../../auth/login_admin.php");
    exit;
}

$cari = $_GET['cari'] ?? '';

$query = mysqli_query(
$koneksi,
"SELECT * FROM pesanan
WHERE nama_pembeli LIKE '%$cari%'
OR no_hp LIKE '%$cari%'
ORDER BY tanggal_pesan DESC"
);


$total_pesanan = mysqli_num_rows(
    mysqli_query($koneksi,
    "SELECT * FROM pesanan")
);

$total_pendapatan = mysqli_fetch_assoc(
    mysqli_query(
        $koneksi,
        "SELECT SUM(total_bayar)
        as total FROM pesanan"
    )
)['total'];


if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    mysqli_query(
        $koneksi,
        "DELETE FROM pesanan
        WHERE id_pesanan = '$id'"
    );

    echo "<script>
        alert('Pesanan berhasil dihapus');
        window.location='index.php';
    </script>";
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Kelola Pesanan</title>

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


    <div class="flex justify-between items-center mb-10">

        <div>
            <h1 class="text-4xl font-bold text-slate-800">
                Data Pesanan
            </h1>

            <p class="text-slate-500 mt-2">
                Kelola semua pesanan pelanggan
            </p>
        </div>

    </div>


    <div class="grid md:grid-cols-2 gap-6 mb-8">

        <div class="bg-white rounded-3xl p-6 shadow-sm">

            <p class="text-slate-500">
                Total Pesanan
            </p>

            <h2 class="text-4xl font-bold text-blue-600 mt-2">
                <?= $total_pesanan ?>
            </h2>

        </div>

        <div class="bg-white rounded-3xl p-6 shadow-sm">

            <p class="text-slate-500">
                Total Pendapatan
            </p>

            <h2 class="text-4xl font-bold text-green-600 mt-2">
                Rp <?= number_format($total_pendapatan ?? 0,0,',','.') ?>
            </h2>

        </div>

    </div>


    <div class="bg-white rounded-3xl p-5 shadow-sm mb-5">

        <form method="GET">

            <div class="flex gap-3">

                <input
                type="text"
                name="cari"
                value="<?= $cari ?>"
                placeholder="Cari nama pembeli / nomor HP..."
                class="w-full border rounded-2xl px-5 py-4 outline-none focus:ring-2 focus:ring-blue-500">

                <button
                class="bg-slate-900 text-white px-8 rounded-2xl">

                    Cari
                </button>

            </div>

        </form>

    </div>

<div class="bg-white rounded-[30px] shadow-sm overflow-hidden">

    <div class="overflow-x-auto">

        <table class="w-full">

            <thead class="bg-slate-50 border-b">

                <tr class="text-left text-slate-700">

                    <th class="p-6">Tanggal</th>
                    <th>Pembeli</th>
                    <th>No HP</th>
                    <th>Alamat</th>
                    <th>Total</th>
                    <th class="text-center">Aksi</th>

                </tr>

            </thead>

            <tbody>

            <?php if(mysqli_num_rows($query) > 0): ?>

            <?php while($row = mysqli_fetch_assoc($query)): ?>

            <tr class="border-b hover:bg-slate-50 transition">


                <td class="p-6 text-slate-500 whitespace-nowrap">
                    <?= date(
                        'd M Y H:i',
                        strtotime($row['tanggal_pesan'])
                    ) ?>
                </td>


                <td class="font-semibold text-slate-800">
                    <?= $row['nama_pembeli'] ?>
                </td>

 
                <td>
                    <?= $row['no_hp'] ?>
                </td>


                <td class="max-w-xs truncate text-slate-600">
                    <?= $row['alamat'] ?>
                </td>

                <td>

                    <span class="bg-green-100 text-green-600 px-4 py-2 rounded-full text-sm font-semibold">

                        Rp <?= number_format(
                            $row['total_bayar'],
                            0,
                            ',',
                            '.'
                        ) ?>

                    </span>

                </td>


                <td class="text-center">

                    <div class="flex justify-center gap-2">


                        <a href="detail.php?id=<?= $row['id_pesanan'] ?>"
                        class="bg-blue-500 hover:bg-blue-600 text-white w-11 h-11 rounded-xl flex items-center justify-center transition shadow-sm"
                        title="Lihat Detail">

                            <i class="fas fa-eye"></i>

                        </a>


                        <a href="index.php?hapus=<?= $row['id_pesanan'] ?>"
                        onclick="return confirm('Yakin ingin menghapus pesanan ini?')"
                        class="bg-red-500 hover:bg-red-600 text-white w-11 h-11 rounded-xl flex items-center justify-center transition shadow-sm"
                        title="Hapus Pesanan">

                            <i class="fas fa-trash"></i>

                        </a>

                    </div>

                </td>

            </tr>

            <?php endwhile; ?>

            <?php else: ?>

            <tr>

                <td colspan="6"
                class="text-center py-16 text-slate-500">

                    Belum ada pesanan masuk

                </td>

            </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</main>

</div>

</body>
</html>