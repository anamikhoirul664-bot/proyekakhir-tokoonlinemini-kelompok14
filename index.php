<?php include 'config/koneksi.php'; ?>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tokoku - Gadget Premium</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        .glass {
            backdrop-filter: blur(15px);
            background: rgba(255,255,255,0.8);
        }

        .hero-bg {
            background:
            linear-gradient(rgba(37,99,235,.9), rgba(29,78,216,.85)),
            url('https://images.unsplash.com/photo-1511707171634-5f897ff02aa9');
            background-size: cover;
            background-position: center;
        }

        .card-hover:hover {
            transform: translateY(-10px);
        }

        .fade-up {
            animation: fadeUp .8s ease;
        }

        @keyframes fadeUp {
            from {
                opacity:0;
                transform: translateY(30px);
            }
            to {
                opacity:1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="bg-slate-50">

<?php include 'components/navbar.php'; ?>


<header class="hero-bg min-h-screen flex items-center">

    <div class="container mx-auto px-6">

        <div class="max-w-3xl fade-up">

            <span class="bg-white/20 text-white px-5 py-2 rounded-full text-sm">
                🔥 Promo Gadget Terbaru 2026
            </span>

            <h1 class="text-5xl md:text-7xl font-extrabold text-white leading-tight mt-6">
                Belanja Gadget  
                <span class="text-yellow-300">
                    Impianmu
                </span>
                Dengan Harga Terbaik
            </h1>

            <p class="text-blue-100 text-lg mt-5 leading-relaxed">
                Temukan smartphone, laptop, headset, dan aksesoris premium dengan kualitas terbaik serta harga yang tetap ramah di kantong.
            </p>

            <form
            action="pages/produk.php"
            method="GET"
            class="bg-white rounded-2xl shadow-2xl p-1 mt-8 flex flex-col md:flex-row gap-3">

                <input
                    type="text"
                    name="cari"
                    placeholder="Cari produk impian..."
                    class="w-full px-5 py-4 rounded-xl outline-none border">

                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-xl font-semibold transition">

                    Cari Produk

                </button>

            </form>

            <div class="flex gap-4 mt-8 flex-wrap">
                <a href="pages/produk.php"
                    class="bg-white text-blue-600 px-8 py-4 rounded-xl font-bold hover:scale-105 transition">
                    Belanja Sekarang
                </a>

                <a href="#produk"
                    class="border border-white text-white px-8 py-4 rounded-xl hover:bg-white hover:text-blue-600 transition">
                    Lihat Produk
                </a>
            </div>

        </div>
    </div>
</header>


<section class="container mx-auto px-6 -mt-16 relative z-10">

    <div class="grid md:grid-cols-4 gap-6">

        <div class="bg-white rounded-3xl p-6 shadow-lg text-center">
            <i class="fa-solid fa-box text-4xl text-blue-600"></i>
            <h3 class="text-2xl font-bold mt-3">500+</h3>
            <p class="text-gray-500">Produk</p>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-lg text-center">
            <i class="fa-solid fa-users text-4xl text-blue-600"></i>
            <h3 class="text-2xl font-bold mt-3">10K+</h3>
            <p class="text-gray-500">Pelanggan</p>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-lg text-center">
            <i class="fa-solid fa-star text-4xl text-yellow-400"></i>
            <h3 class="text-2xl font-bold mt-3">4.9</h3>
            <p class="text-gray-500">Rating</p>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-lg text-center">
            <i class="fa-solid fa-truck-fast text-4xl text-blue-600"></i>
            <h3 class="text-2xl font-bold mt-3">Fast</h3>
            <p class="text-gray-500">Pengiriman</p>
        </div>

    </div>
</section>


<section id="produk" class="container mx-auto px-6 py-24">

    <div class="flex justify-between items-center mb-10">
        <div>
            <p class="text-blue-600 font-semibold">
                PRODUK TERBARU
            </p>

            <h2 class="text-4xl font-bold text-gray-800">
                Produk Pilihan
            </h2>
        </div>

        <a href="pages/produk.php"
            class="text-blue-600 font-semibold hover:underline">
            Lihat Semua →
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

        <?php
        $query = mysqli_query($koneksi,
        "SELECT * FROM produk ORDER BY id_produk DESC LIMIT 8");

        while($row = mysqli_fetch_assoc($query)):
        ?>

        <div class="bg-white rounded-[30px] overflow-hidden shadow-md hover:shadow-2xl transition duration-500 card-hover">

            <div class="relative overflow-hidden">

                <img
                    src="assets/images/<?= $row['foto'] ?>"
                    alt="<?= $row['nama_produk'] ?>"
                    class="w-full h-64 object-cover hover:scale-110 transition duration-700">

                <span class="absolute top-4 left-4 bg-red-500 text-white px-4 py-1 rounded-full text-sm">
                    New
                </span>
            </div>

            <div class="p-6">

                <h3 class="font-bold text-lg text-gray-800">
                    <?= $row['nama_produk'] ?>
                </h3>

                <p class="text-2xl font-bold text-blue-600 mt-2">
                    Rp <?= number_format($row['harga'],0,',','.') ?>
                </p>

                <div class="flex gap-3 mt-5">

                    <a href="pages/detail.php?id=<?= $row['id_produk'] ?>"
                        class="w-full text-center border border-blue-600 text-blue-600 py-3 rounded-xl hover:bg-blue-600 hover:text-white transition font-semibold">
                        Detail
                    </a>

                    <button class="bg-blue-600 text-white px-4 rounded-xl hover:bg-blue-700 transition">
                        <i class="fa fa-cart-shopping"></i>
                    </button>

                </div>
            </div>
        </div>

        <?php endwhile; ?>

    </div>
</section>

<?php include 'components/footer.php'; ?>

</body>
</html>