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

<?php
$totalProduk = mysqli_fetch_assoc(
    mysqli_query($koneksi, "SELECT COUNT(*) as total FROM produk")
)['total'];

$totalKategori = mysqli_fetch_assoc(
    mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kategori")
)['total'];
?>

<section class="container mx-auto px-6 -mt-16 relative z-10 animate-scroll opacity-0 translate-y-10 duration-700">

    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">

        <div class="bg-white rounded-3xl p-6 shadow-lg text-center">
            <i class="fa-solid fa-box text-4xl text-blue-600"></i>

            <h3 class="counter text-2xl font-bold mt-3"
            data-target="<?= $totalProduk ?>">
                0
            </h3>

            <p class="text-gray-500">
                Produk
            </p>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-lg text-center">
            <i class="fa-solid fa-layer-group text-4xl text-green-600"></i>

            <h3 class="counter text-2xl font-bold mt-3"
            data-target="<?= $totalKategori ?>">
                0
            </h3>

            <p class="text-gray-500">
                Kategori
            </p>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-lg text-center">
            <i class="fa-solid fa-star text-4xl text-yellow-400"></i>

            <h3 class="text-2xl font-bold mt-3">
                4.9
            </h3>

            <p class="text-gray-500">
                Rating
            </p>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-lg text-center">
            <i class="fa-solid fa-truck-fast text-4xl text-blue-600"></i>

            <h3 class="text-2xl font-bold mt-3">
                24 Jam
            </h3>

            <p class="text-gray-500">
                Pengiriman
            </p>
        </div>

    </div>

</section>

<section class="container mx-auto px-6 py-20">

    <div class="text-center mb-12">
        <p class="text-blue-600 font-semibold">
            KATEGORI
        </p>

        <h2 class="text-4xl font-bold">
            Belanja Berdasarkan Kategori
        </h2>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">

        <?php
        $kategori = mysqli_query($koneksi,
        "SELECT * FROM kategori LIMIT 4");

        while($kat = mysqli_fetch_assoc($kategori)):
        ?>

        <a href="pages/produk.php?kategori=<?= $kat['id_kategori'] ?>"
        class="bg-white p-8 rounded-3xl shadow hover:shadow-xl hover:-translate-y-2 transition text-center">

            <i class="fa-solid fa-layer-group text-5xl text-blue-600"></i>

            <h3 class="font-bold text-lg mt-4">
                <?= $kat['nama_kategori'] ?>
            </h3>

        </a>

        <?php endwhile; ?>

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

                    <?php
                    $isNew = strtotime($row['created_at']) > strtotime('-7 days');
                    ?>

                    <?php if($isNew): ?>
                    <span class="absolute top-4 left-4 bg-red-500 text-white px-4 py-1 rounded-full text-sm">
                    NEW
                    </span>
                    <?php endif; ?>
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


<section class="container mx-auto px-6 py-12">
  <div class="text-center mb-16">
    <span class="text-blue-600 font-bold text-sm tracking-widest uppercase">Kelebihan Kami</span>
    <h2 class="text-4xl md:text-5xl font-extrabold text-slate-800 mt-2">
      Kenapa Memilih NexaTech?
    </h2>
    <p class="text-slate-500 mt-4 max-w-xl mx-auto">Kami berkomitmen memberikan pengalaman belanja gadget terbaik dengan layanan prima.</p>
  </div>

  <div class="grid md:grid-cols-3 gap-8">

    <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group">
      <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center group-hover:bg-blue-600 transition-colors duration-300">
        <i class="fa-solid fa-shield text-2xl text-blue-600 group-hover:text-white transition-colors duration-300"></i>
      </div>
      <h3 class="font-bold text-xl text-slate-800 mt-6">Produk Original</h3>
      <p class="text-slate-500 mt-2 text-sm leading-relaxed">Semua produk dijamin 100% original dan memiliki garansi resmi pabrikan.</p>
    </div>


    <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group">
      <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center group-hover:bg-blue-600 transition-colors duration-300">
        <i class="fa-solid fa-truck-fast text-2xl text-blue-600 group-hover:text-white transition-colors duration-300"></i>
      </div>
      <h3 class="font-bold text-xl text-slate-800 mt-6">Pengiriman Cepat</h3>
      <p class="text-slate-500 mt-2 text-sm leading-relaxed">Layanan pengiriman instan dan reguler yang amanah sampai ke depan rumah Anda.</p>
    </div>


    <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group">
      <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center group-hover:bg-blue-600 transition-colors duration-300">
        <i class="fa-solid fa-headset text-2xl text-blue-600 group-hover:text-white transition-colors duration-300"></i>
      </div>
      <h3 class="font-bold text-xl text-slate-800 mt-6">Support 24 Jam</h3>
      <p class="text-slate-500 mt-2 text-sm leading-relaxed">Tim customer service kami siap membantu kendala Anda kapan pun dibutuhkan.</p>
    </div>
  </div>
</section>


<section class="bg-slate-900 py-24">

  <div class="container mx-auto px-6">
    <div class="text-center mb-16">
      <p class="text-blue-400 font-bold text-sm tracking-widest uppercase">TESTIMONI</p>
      <h2 class="text-4xl md:text-5xl font-extrabold text-white mt-2">Apa Kata Pelanggan?</h2>
    </div>

    <div class="grid md:grid-cols-3 gap-8">

      <div class="bg-slate-800/60 backdrop-blur p-8 rounded-3xl border border-slate-700/50 flex flex-col justify-between hover:border-blue-500/50 transition-all duration-300">
        <div>
          <div class="text-yellow-400 text-lg mb-4">★★★★★</div>
          <p class="text-slate-300 italic leading-relaxed">"Produk original, pengiriman cepat dan pelayanan sangat ramah."</p>
        </div>
        <div class="flex items-center gap-4 mt-6 border-t border-slate-700/50 pt-4">
          <div class="w-10 h-10 bg-blue-600 text-white font-bold rounded-full flex items-center justify-center text-sm">A</div>
          <h4 class="text-white font-bold">Anam</h4>
        </div>
      </div>


      <div class="bg-slate-800/60 backdrop-blur p-8 rounded-3xl border border-slate-700/50 flex flex-col justify-between hover:border-blue-500/50 transition-all duration-300">
        <div>
          <div class="text-yellow-400 text-lg mb-4">★★★★★</div>
          <p class="text-slate-300 italic leading-relaxed">"Laptop yang saya beli sesuai deskripsi dan garansi resmi."</p>
        </div>
        <div class="flex items-center gap-4 mt-6 border-t border-slate-700/50 pt-4">
          <div class="w-10 h-10 bg-purple-600 text-white font-bold rounded-full flex items-center justify-center text-sm">F</div>
          <h4 class="text-white font-bold">Faruq</h4>
        </div>
      </div>

      <div class="bg-slate-800/60 backdrop-blur p-8 rounded-3xl border border-slate-700/50 flex flex-col justify-between hover:border-blue-500/50 transition-all duration-300">
        <div>
          <div class="text-yellow-400 text-lg mb-4">★★★★★</div>
          <p class="text-slate-300 italic leading-relaxed">"Harga bersaing dan pilihan produknya lengkap."</p>
        </div>
        <div class="flex items-center gap-4 mt-6 border-t border-slate-700/50 pt-4">
          <div class="w-10 h-10 bg-emerald-600 text-white font-bold rounded-full flex items-center justify-center text-sm">R</div>
          <h4 class="text-white font-bold">Rahma</h4>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="container mx-auto px-6 py-24">
  <div class="bg-gradient-to-br from-blue-600 via-blue-700 to-blue-900 rounded-[40px] p-12 md:p-16 text-center text-white shadow-2xl shadow-blue-500/20 relative overflow-hidden">
    <div class="absolute -top-10 -left-10 w-40 h-40 bg-white/5 rounded-full blur-2xl"></div>
    <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
    
    <div class="relative z-10">
      <h2 class="text-4xl md:text-5xl font-extrabold tracking-tight">
        Siap Belanja Gadget Impianmu?
      </h2>
      <p class="mt-4 text-blue-100 max-w-2xl mx-auto text-lg opacity-90">
        Dapatkan berbagai produk elektronik terbaik dengan harga kompetitif dan kualitas terpercaya.
      </p>
      <a href="pages/produk.php"
         class="inline-block mt-8 bg-white text-blue-600 px-8 py-4 rounded-2xl font-bold tracking-wide shadow-lg shadow-blue-900/20 hover:scale-105 hover:shadow-xl hover:bg-blue-50 active:scale-95 transition-all duration-300">
        Mulai Belanja Sekarang
      </a>
    </div>
  </div>
</section>


<?php include 'components/footer.php'; ?>

<script>

const observer = new IntersectionObserver(entries => {

entries.forEach(entry => {

if(entry.isIntersecting){

entry.target.classList.add(
'opacity-100',
'translate-y-0'
);

}

});

});

document.querySelectorAll('.animate-scroll').forEach(el => {

observer.observe(el);

});

</script>

<div class="hero-bg transition-all duration-1000 ease-in-out" 
     style="background: linear-gradient(rgba(37,99,235,.9), rgba(29,78,216,.85)), url('https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=1920&q=80') center/cover;">
     </div>

<script>
const hero = document.querySelector('.hero-bg');


const backgrounds = [
    'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=1920&q=80',
    'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=1920&q=80',
    'https://images.unsplash.com/photo-1517336714739-489689fd1ca8' 
];

let i = 0;


function changeBackground() {
    i++;
    if (i >= backgrounds.length) {
        i = 0;
    }
    

    hero.style.backgroundImage = `linear-gradient(rgba(37, 99, 235, 0.8), rgba(29, 78, 216, 0.85)), url('${backgrounds[i]}')`;
    hero.style.backgroundPosition = 'center';
    hero.style.backgroundSize = 'cover';
}

setInterval(changeBackground, 3000);
</script>

<script>
document.querySelectorAll('.counter').forEach(counter => {

    const target = +counter.dataset.target;
    let count = 0;

    const update = () => {

        count += target / 50;

        if (count < target) {

            counter.innerText = Math.ceil(count);

            requestAnimationFrame(update);

        } else {

            counter.innerText = target + "+";

        }
    };

    update();

});
</script>

</body>
</html>