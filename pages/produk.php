<?php 
include '../config/koneksi.php'; 

// Pencarian
$keyword = "";

if (isset($_GET['cari'])) {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['cari']);

    $query_produk = mysqli_query(
        $koneksi,
        "SELECT produk.*
        FROM produk
        JOIN kategori
        ON produk.id_kategori = kategori.id_kategori
        WHERE produk.nama_produk LIKE '%$keyword%'
        OR produk.brand LIKE '%$keyword%'
        OR kategori.nama_kategori LIKE '%$keyword%'"
    );
} 

elseif (isset($_GET['kategori'])) {
    $id_kat = $_GET['kategori'];

    $query_produk = mysqli_query(
        $koneksi,
        "SELECT * FROM produk
        WHERE id_kategori = '$id_kat'"
    );
}

else {
    $query_produk = mysqli_query(
        $koneksi,
        "SELECT * FROM produk
        ORDER BY id_produk DESC"
    );
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Produk - Toko Mini</title>

<script src="https://cdn.tailwindcss.com"></script>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="bg-slate-100">

<?php include '../components/navbar.php'; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="flex flex-col lg:flex-row gap-6">

        <!-- SIDEBAR -->
        <aside class="w-full lg:w-1/4">

            <div class="bg-white rounded-3xl shadow-sm p-5 sticky top-24">

                <h2 class="font-bold text-lg mb-4">
                    Cari Produk
                </h2>


                <form method="GET" class="relative mb-6">

                    <input
                    type="text"
                    name="cari"
                    value="<?= $keyword ?>"
                    placeholder="Cari produk..."
                    class="w-full border border-slate-200 rounded-xl px-4 py-3 pr-12 outline-none focus:ring-2 focus:ring-blue-500">

                    <button
                    type="submit"
                    class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">

                        <i class="fas fa-search"></i>

                    </button>

                </form>


                <h2 class="font-bold text-lg mb-4">
                    Kategori
                </h2>

                <div class="flex flex-wrap gap-2">

                    <a href="produk.php"
                    class="bg-slate-100 hover:bg-blue-600 hover:text-white px-4 py-2 rounded-xl text-sm transition">

                        Semua

                    </a>

                    <?php
                    $kat = mysqli_query(
                        $koneksi,
                        "SELECT * FROM kategori"
                    );

                    while($k =
                    mysqli_fetch_assoc($kat)):
                    ?>

                    <a href="produk.php?kategori=<?= $k['id_kategori'] ?>"
                    class="bg-slate-100 hover:bg-blue-600 hover:text-white px-4 py-2 rounded-xl text-sm transition">

                        <?= $k['nama_kategori'] ?>

                    </a>

                    <?php endwhile; ?>

                </div>

            </div>

        </aside>


        <main class="w-full lg:w-3/4">


            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 mb-6">

                <div>

                    <h1 class="text-2xl md:text-3xl font-bold text-slate-800">

                        <?= isset($_GET['cari'])
                        ? "Hasil: '$keyword'"
                        : "Semua Produk" ?>

                    </h1>

                    <p class="text-slate-500 text-sm mt-1">
                        Temukan produk terbaik pilihanmu
                    </p>

                </div>

                <span class="text-slate-500 text-sm">

                    <?= mysqli_num_rows($query_produk) ?>
                    Produk ditemukan

                </span>

            </div>

            <?php if(mysqli_num_rows($query_produk) > 0): ?>


            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">

                <?php while($row =
                mysqli_fetch_assoc($query_produk)): ?>

                <div class="bg-white rounded-[28px] overflow-hidden shadow-sm hover:shadow-xl transition duration-300 group">


                    <div class="overflow-hidden">

                        <img
                        src="../assets/images/<?= $row['foto'] ?>"
                        alt="<?= $row['nama_produk'] ?>"
                        class="w-full h-52 sm:h-60 object-cover group-hover:scale-105 transition duration-500">

                    </div>


                    <div class="p-5">

                        <h3 class="font-bold text-slate-800 text-base sm:text-lg line-clamp-2">

                            <?= $row['nama_produk'] ?>

                        </h3>

                        <p class="text-blue-600 font-bold text-xl mt-2">

                            Rp <?= number_format(
                            $row['harga'],
                            0,
                            ',',
                            '.'
                            ) ?>

                        </p>

                        <a href="detail.php?id=<?= $row['id_produk'] ?>"
                        class="block text-center mt-4 bg-slate-100 hover:bg-blue-600 hover:text-white py-3 rounded-xl font-semibold transition">

                            Lihat Detail

                        </a>

                    </div>

                </div>

                <?php endwhile; ?>

            </div>

            <?php else: ?>

            <div class="bg-white rounded-3xl p-12 text-center">

                <i class="fas fa-box-open text-5xl text-slate-300 mb-4"></i>

                <h2 class="font-bold text-xl">
                    Produk Tidak Ditemukan
                </h2>

                <p class="text-slate-500 mt-2">
                    Coba kata kunci lain
                </p>

            </div>

            <?php endif; ?>

        </main>

    </div>

</div>

<?php include '../components/footer.php'; ?>

</body>
</html>