<?php
include '../config/koneksi.php';


if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login_admin.php");
    exit;
}


$total_produk = mysqli_num_rows(
    mysqli_query($koneksi,
    "SELECT * FROM produk")
);

$total_pesanan = mysqli_num_rows(
    mysqli_query($koneksi,
    "SELECT * FROM pesanan")
);

$total_kategori = mysqli_num_rows(
    mysqli_query($koneksi,
    "SELECT * FROM kategori")
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex">



    <aside class="w-72 bg-slate-900 text-white p-6 min-h-screen sticky top-0 shadow-xl">

    <h1 class="text-3xl font-bold mb-10">
        <span class="text-blue-500">
            Admin
        </span>Panel
    </h1>

    <nav class="space-y-3">


        <a href="dashboard.php"
        class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600">

            <i class="fas fa-chart-line"></i>
            Dashboard
        </a>


        <a href="produk/index.php"
        class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 transition">

            <i class="fas fa-box"></i>
            Produk
        </a>


        <a href="kategori/index.php"
        class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 transition">

            <i class="fas fa-tags"></i>
            Kategori
        </a>


        <a href="pesanan/index.php"
        class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 transition">

            <i class="fas fa-cart-shopping"></i>
            Pesanan
        </a>


        <a href="../index.php"
        class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 transition mt-6 border border-slate-700">

            <i class="fas fa-store text-blue-400"></i>

            <span>
                Kembali ke Toko
            </span>

        </a>

        <a href="../auth/logout.php"
        onclick="return confirm('Yakin ingin logout?')"
        class="flex items-center gap-3 px-4 py-3 rounded-xl bg-red-500 hover:bg-red-600 mt-8 transition">

            <i class="fas fa-right-from-bracket"></i>
            Logout
        </a>

    </nav>

</aside>


    <main class="flex-1 p-10">
        <header class="flex justify-between items-center mb-10">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">
    Selamat Datang,
    <?= $_SESSION['admin']['nama'] ?>!
</h2>
                <p class="text-gray-500">Berikut adalah ringkasan toko anda hari ini.</p>
            </div>
            <div class="bg-white px-4 py-2 rounded-lg shadow-sm border">
                <span class="text-gray-500"><?= date('l, d F Y') ?></span>
            </div>
        </header>


        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border-b-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-semibold">Total Produk</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?= $total_produk ?></h3>
                    </div>
                    <div class="bg-blue-100 p-4 rounded-full text-blue-600">
                        <i class="fas fa-box text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border-b-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-semibold">Pesanan Masuk</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?= $total_pesanan ?></h3>
                    </div>
                    <div class="bg-green-100 p-4 rounded-full text-green-600">
                        <i class="fas fa-shopping-cart text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border-b-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-semibold">Kategori</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?= $total_kategori ?></h3>
                    </div>
                    <div class="bg-purple-100 p-4 rounded-full text-purple-600">
                        <i class="fas fa-tags text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </main>

</body>
</html>