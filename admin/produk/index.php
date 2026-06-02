<?php
include '../../config/koneksi.php';


if (!isset($_SESSION['admin'])) {
    header("Location: ../../auth/login_admin.php");
    exit;
}


$cari = $_GET['cari'] ?? '';

$query = mysqli_query($koneksi,
"SELECT produk.*, kategori.nama_kategori
FROM produk
LEFT JOIN kategori
ON produk.id_kategori = kategori.id_kategori
WHERE nama_produk LIKE '%$cari%'
ORDER BY id_produk DESC"
);


$total_produk = mysqli_num_rows(
    mysqli_query($koneksi, "SELECT * FROM produk")
);

$total_stok = mysqli_fetch_assoc(
    mysqli_query($koneksi,
    "SELECT SUM(stok) as total FROM produk")
)['total'];

$produk_habis = mysqli_num_rows(
    mysqli_query($koneksi,
    "SELECT * FROM produk WHERE stok <= 0")
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
    content="width=device-width, initial-scale=1.0">

    <title>Kelola Produk</title>

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

            <a href="index.php"
            class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600">

                <i class="fas fa-box"></i>
                Produk
            </a>

            <a href="../kategori/index.php"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 transition">

                <i class="fas fa-tags"></i>
                Kategori
            </a>

            <a href="../pesanan/index.php"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 transition">

                <i class="fas fa-cart-shopping"></i>
                Pesanan
            </a>
            
        </nav>

    </aside>


    <main class="flex-1 p-10">

        <div class="flex justify-between items-center mb-10">

            <div>
                <h1 class="text-4xl font-bold text-slate-800">
                    Kelola Produk
                </h1>

                <p class="text-slate-500 mt-2">
                    Kelola semua produk toko anda
                </p>
            </div>

            <a href="tambah.php"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-4 rounded-2xl font-semibold shadow-lg transition">

                <i class="fas fa-plus mr-2"></i>
                Tambah Produk
            </a>

        </div>


        <div class="grid md:grid-cols-3 gap-6 mb-10">

            <div class="bg-white rounded-3xl p-6 shadow-sm">

                <p class="text-slate-500">
                    Total Produk
                </p>

                <h2 class="text-4xl font-bold mt-2">
                    <?= $total_produk ?>
                </h2>
            </div>

            <div class="bg-white rounded-3xl p-6 shadow-sm">

                <p class="text-slate-500">
                    Total Stok
                </p>

                <h2 class="text-4xl font-bold mt-2 text-blue-600">
                    <?= $total_stok ?>
                </h2>
            </div>

            <div class="bg-white rounded-3xl p-6 shadow-sm">

                <p class="text-slate-500">
                    Produk Habis
                </p>

                <h2 class="text-4xl font-bold mt-2 text-red-500">
                    <?= $produk_habis ?>
                </h2>
            </div>

        </div>


        <div class="bg-white rounded-3xl p-6 shadow-sm mb-6">

            <form method="GET">

                <div class="flex gap-4">

                    <input
                    type="text"
                    name="cari"
                    value="<?= $cari ?>"
                    placeholder="Cari produk..."
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

                    <thead class="bg-slate-50">

                        <tr class="text-left">

                            <th class="p-6">Foto</th>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th class="text-center">Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php if(mysqli_num_rows($query) > 0): ?>

                    <?php while($row = mysqli_fetch_assoc($query)): ?>

                    <tr class="border-b hover:bg-slate-50 transition">

                        <td class="p-6">
                            <img
                            src="../../assets/images/<?= $row['foto'] ?>"
                            class="w-20 h-20 rounded-2xl object-cover">
                        </td>

                        <td class="font-semibold">
                            <?= $row['nama_produk'] ?>
                        </td>

                        <td>
                            <?= $row['nama_kategori'] ?>
                        </td>

                        <td class="font-bold text-blue-600">
                            Rp <?= number_format($row['harga'],0,',','.') ?>
                        </td>

                        <td>
                            <?php if($row['stok'] > 0): ?>
                                <span class="bg-green-100 text-green-600 px-4 py-2 rounded-full text-sm">
                                    <?= $row['stok'] ?> tersedia
                                </span>
                            <?php else: ?>
                                <span class="bg-red-100 text-red-600 px-4 py-2 rounded-full text-sm">
                                    Habis
                                </span>
                            <?php endif; ?>
                        </td>

                        <td class="text-center">

                            <a href="edit.php?id=<?= $row['id_produk'] ?>"
                            class="bg-yellow-400 px-4 py-3 rounded-xl text-white mr-2">

                                <i class="fas fa-edit"></i>
                            </a>

                            <a
                            href="hapus.php?id=<?= $row['id_produk'] ?>"
                            onclick="return confirm('Yakin hapus produk ini?')"
                            class="bg-red-500 px-4 py-3 rounded-xl text-white">

                                <i class="fas fa-trash"></i>
                            </a>

                        </td>

                    </tr>

                    <?php endwhile; ?>

                    <?php else: ?>

                    <tr>
                        <td colspan="6"
                        class="text-center py-16 text-slate-500">

                            Tidak ada produk ditemukan
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