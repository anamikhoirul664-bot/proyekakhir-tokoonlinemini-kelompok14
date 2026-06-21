<?php
$base_url = "https://tokoonlinemini.free.nf";

// 1. Pastikan session dimulai agar bisa membaca data user yang sedang login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Hubungkan ke database (Gunakan file koneksi Anda)
include __DIR__ . '/../config/koneksi.php'; 

// VALIDASI PENGAMAN: Pastikan session 'user' berbentuk array yang valid, jika string rusak, netralkan
$user_aman = (isset($_SESSION['user']) && is_array($_SESSION['user'])) ? $_SESSION['user'] : null;

// 3. Hitung jumlah item keranjang riil dari database
$total_keranjang = 0;
if ($user_aman && isset($user_aman['id_user'])) {
    $id_user_navbar = (int)$user_aman['id_user'];
    $query_nav = mysqli_query($koneksi, "SELECT SUM(qty) as total FROM cart WHERE id_user = '$id_user_navbar'");
    $data_nav = mysqli_fetch_assoc($query_nav);
    $total_keranjang = (int)$data_nav['total'];
}
?>

<nav id="navbar" class="fixed top-0 left-0 w-full z-50 bg-white shadow-sm transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">

            <a href="<?= $base_url ?>/index.php" class="text-2xl md:text-3xl font-extrabold shrink-0">
                <span class="text-blue-600">NexaTech</span>
                <span class="text-slate-800">Store</span>
            </a>

            <ul class="hidden md:flex items-center gap-8 font-medium text-slate-700">
                <li>
                    <a href="<?= $base_url ?>/index.php" class="hover:text-blue-600 transition flex items-center gap-2">
                        <i class="fa-solid fa-house"></i> Home
                    </a>
                </li>
                <li>
                    <a href="<?= $base_url ?>/pages/produk.php" class="hover:text-blue-600 transition flex items-center gap-2">
                        <i class="fa-solid fa-box"></i> Produk
                    </a>
                </li>
                <li>
                    <a href="<?= $base_url ?>/pages/pesanan_saya.php" class="hover:text-blue-600 transition flex items-center gap-2">
                        <i class="fa-solid fa-bag-shopping"></i> Pesanan Saya
                    </a>
                </li>
            </ul>

            <div class="hidden md:flex items-center gap-3">
                <a href="<?= $base_url ?>/pages/cart.php" class="relative bg-slate-100 hover:bg-slate-200 p-3 rounded-xl transition flex items-center justify-center w-11 h-11">
                    <i class="fa-solid fa-cart-shopping text-lg text-slate-700"></i>
                    <?php if ($total_keranjang > 0): ?>
                        <span class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center border-2 border-white shadow-sm">
                            <?= $total_keranjang ?>
                        </span>
                    <?php endif; ?>
                </a>

                <?php if($user_aman): ?>
                    <a href="<?= $base_url ?>/pages/profile.php" class="flex items-center gap-3 bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-xl transition">
                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white shrink-0">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div class="leading-tight">
                            <p class="text-[10px] text-slate-400">Selamat Datang</p>
                            <p class="font-semibold text-sm text-slate-700 max-w-[100px] truncate">
                                <?= htmlspecialchars(isset($user_aman['nama']) ? $user_aman['nama'] : 'User') ?>
                            </p>
                        </div>
                    </a>

                    <a href="<?= $base_url ?>/auth/logout.php" onclick="return confirm('Yakin ingin logout?')" class="bg-red-500 text-white px-5 py-2.5 rounded-xl hover:bg-red-600 transition font-semibold text-sm">
                        Logout
                    </a>
                <?php elseif(isset($_SESSION['admin'])): ?>
                    <a href="<?= $base_url ?>/admin/dashboard.php" class="bg-green-600 text-white px-5 py-2.5 rounded-xl hover:bg-green-700 transition font-semibold text-sm">
                        Dashboard
                    </a>
                    <a href="<?= $base_url ?>/auth/logout.php" onclick="return confirm('Yakin ingin logout?')" class="bg-red-500 text-white px-5 py-2.5 rounded-xl hover:bg-red-600 transition font-semibold text-sm">
                        Logout
                    </a>
                <?php else: ?>
                    <a href="<?= $base_url ?>/auth/login.php" class="hover:text-blue-600 font-medium text-sm transition px-3 py-2">
                        Login
                    </a>
                    <a href="<?= $base_url ?>/auth/register.php" class="bg-slate-900 text-white px-5 py-2.5 rounded-xl hover:bg-slate-800 transition font-semibold text-sm">
                        Register
                    </a>
                <?php endif; ?>
            </div>

            <div class="flex items-center gap-3 md:hidden">
                <a href="<?= $base_url ?>/pages/cart.php" class="relative bg-slate-100 p-3 rounded-xl flex items-center justify-center w-11 h-11">
                    <i class="fa-solid fa-cart-shopping text-slate-700"></i>
                    <?php if ($total_keranjang > 0): ?>
                        <span class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center border-2 border-white shadow-sm">
                            <?= $total_keranjang ?>
                        </span>
                    <?php endif; ?>
                </a>

                <button id="menuBtn" class="text-2xl text-slate-700 p-1 focus:outline-none">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>

        </div>
    </div>

    <div id="mobileMenu" class="hidden md:hidden bg-white border-t shadow-lg">
        <div class="flex flex-col p-5 gap-4">
            <?php if($user_aman): ?>
                <a href="<?= $base_url ?>/pages/profile.php" class="flex items-center gap-3 bg-slate-100 hover:bg-slate-200 px-4 py-3 rounded-xl transition">
                    <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white text-lg shrink-0">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Selamat Datang</p>
                        <p class="font-bold text-slate-800">
                            <?= htmlspecialchars(isset($user_aman['nama']) ? $user_aman['nama'] : 'User') ?>
                        </p>
                    </div>
                </a>
            <?php endif; ?>

            <a href="<?= $base_url ?>/index.php" class="flex items-center gap-3 py-2 border-b font-medium text-slate-700">
                <i class="fa-solid fa-house text-blue-600"></i> Home
            </a>
            <a href="<?= $base_url ?>/pages/produk.php" class="flex items-center gap-3 py-2 border-b font-medium text-slate-700">
                <i class="fa-solid fa-box text-blue-600"></i> Produk
            </a>
            <a href="<?= $base_url ?>/pages/pesanan_saya.php" class="flex items-center gap-3 py-2 border-b font-medium text-slate-700">
                <i class="fa-solid fa-bag-shopping text-blue-600"></i> Pesanan Saya
            </a>

            <?php if($user_aman || isset($_SESSION['admin'])): ?>
                <a href="<?= $base_url ?>/auth/logout.php" onclick="return confirm('Yakin ingin logout?')" class="bg-red-500 text-white text-center py-3 rounded-xl mt-2 font-semibold shadow-md">
                    Logout
                </a>
            <?php else: ?>
                <a href="<?= $base_url ?>/auth/login.php" class="border border-slate-200 text-center py-3 rounded-xl font-medium text-slate-700">
                    Login
                </a>
                <a href="<?= $base_url ?>/auth/register.php" class="bg-slate-900 text-white text-center py-3 rounded-xl font-semibold shadow-md">
                    Register
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="h-20"></div>

<script>
const menuBtn = document.getElementById('menuBtn');
const mobileMenu = document.getElementById('mobileMenu');

menuBtn.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
});

const navbar = document.getElementById('navbar');

window.addEventListener('scroll', () => {
    if(window.scrollY > 50){
        navbar.classList.add('shadow-lg', 'backdrop-blur-md', 'bg-white/90');
    } else {
        navbar.classList.remove('shadow-lg', 'backdrop-blur-md', 'bg-white/90');
    }
});
</script>