<?php
$base_url = "/PROJEK%20AKHIR";
?>

<nav id="navbar"
class="fixed top-0 left-0 w-full z-50 bg-white shadow-sm transition-all duration-300">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between h-20">

            <!-- LOGO -->
            <a href="<?= $base_url ?>/index.php"
            class="text-2xl md:text-3xl font-extrabold shrink-0">

                <span class="text-blue-600">
                    NexaTech
                </span>

                <span class="text-slate-800">
                    Store
                </span>

            </a>

            <!-- MENU DESKTOP -->
            <ul class="hidden md:flex items-center gap-8 font-medium text-slate-700">

                <li>
                    <a href="<?= $base_url ?>/index.php"
                    class="hover:text-blue-600 transition flex items-center gap-2">

                        <i class="fa-solid fa-house"></i>
                        Home
                    </a>
                </li>

                <li>
                    <a href="<?= $base_url ?>/pages/produk.php"
                    class="hover:text-blue-600 transition flex items-center gap-2">

                        <i class="fa-solid fa-box"></i>
                        Produk
                    </a>
                </li>

                <li>
                    <a href="<?= $base_url ?>/pages/pesanan_saya.php"
                    class="hover:text-blue-600 transition flex items-center gap-2">

                        <i class="fa-solid fa-bag-shopping"></i>
                        Pesanan Saya
                    </a>
                </li>

            </ul>

            <!-- RIGHT DESKTOP -->
            <div class="hidden md:flex items-center gap-3">

                <!-- CART -->
                <a href="<?= $base_url ?>/pages/cart.php"
                class="relative bg-slate-100 hover:bg-slate-200 p-3 rounded-xl transition">

                    <i class="fa-solid fa-cart-shopping text-lg"></i>

                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">

                        <?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>

                    </span>

                </a>

                <?php if(isset($_SESSION['user'])): ?>

                    <!-- PROFILE -->
                    <a href="<?= $base_url ?>/pages/profile.php"
                        class="flex items-center gap-3 bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-xl transition">

                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white">

                            <i class="fa-solid fa-user"></i>

                        </div>

                        <div>
                            <p class="text-xs text-slate-500">
                                Selamat Datang
                            </p>

                            <p class="font-semibold text-slate-700">
                                <?= $_SESSION['user']['nama'] ?>
                            </p>
                        </div>

                    </a>

                    <a href="<?= $base_url ?>/auth/logout.php"
                    onclick="return confirm('Yakin ingin logout?')"
                    class="bg-red-500 text-white px-5 py-3 rounded-xl hover:bg-red-600 transition font-semibold">

                        Logout
                    </a>

                <?php elseif(isset($_SESSION['admin'])): ?>

                    <a href="<?= $base_url ?>/admin/dashboard.php"
                    class="bg-green-600 text-white px-5 py-3 rounded-xl hover:bg-green-700 transition font-semibold">

                        Dashboard
                    </a>

                    <a href="<?= $base_url ?>/auth/logout.php"
                    onclick="return confirm('Yakin ingin logout?')"
                    class="bg-red-500 text-white px-5 py-3 rounded-xl hover:bg-red-600 transition font-semibold">

                        Logout
                    </a>

                <?php else: ?>

                    <a href="<?= $base_url ?>/auth/login.php"
                    class="hover:text-blue-600 font-medium transition">

                        Login
                    </a>

                    <a href="<?= $base_url ?>/auth/register.php"
                    class="bg-slate-900 text-white px-5 py-3 rounded-xl hover:bg-slate-800 transition font-semibold">

                        Register
                    </a>

                <?php endif; ?>

            </div>

            <!-- RIGHT MOBILE -->
            <div class="flex items-center gap-3 md:hidden">

                <!-- CART MOBILE -->
                <a href="<?= $base_url ?>/pages/cart.php"
                class="relative bg-slate-100 p-3 rounded-xl">

                    <i class="fa-solid fa-cart-shopping"></i>

                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">

                        <?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>

                    </span>

                </a>

                <!-- HAMBURGER -->
                <button id="menuBtn"
                class="text-2xl text-slate-700">

                    <i class="fa-solid fa-bars"></i>

                </button>

            </div>

        </div>

    </div>

    <!-- MOBILE MENU -->
    <div id="mobileMenu"
    class="hidden md:hidden bg-white border-t shadow-lg">

        <div class="flex flex-col p-5 gap-4">

            <?php if(isset($_SESSION['user'])): ?>

                <!-- USER PROFILE -->
                <a href="<?= $base_url ?>/pages/profile.php"
                        class="flex items-center gap-3 bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-xl transition">

                    <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white text-lg">

                        <i class="fa-solid fa-user"></i>

                    </div>

                    <div>
                        <p class="text-sm text-slate-500">
                            Selamat Datang
                        </p>

                        <p class="font-bold text-slate-800">
                            <?= $_SESSION['user']['nama'] ?>
                        </p>
                    </div>

                </a>

            <?php endif; ?>

            <a href="<?= $base_url ?>/index.php"
            class="flex items-center gap-3 py-2 border-b">

                <i class="fa-solid fa-house text-blue-600"></i>
                Home
            </a>

            <a href="<?= $base_url ?>/pages/produk.php"
            class="flex items-center gap-3 py-2 border-b">

                <i class="fa-solid fa-box text-blue-600"></i>
                Produk
            </a>

            <a href="<?= $base_url ?>/pages/pesanan_saya.php"
            class="flex items-center gap-3 py-2 border-b">

                <i class="fa-solid fa-bag-shopping text-blue-600"></i>
                Pesanan Saya
            </a>

            <?php if(isset($_SESSION['user']) || isset($_SESSION['admin'])): ?>

                <a href="<?= $base_url ?>/auth/logout.php"
                onclick="return confirm('Yakin ingin logout?')"
                class="bg-red-500 text-white text-center py-3 rounded-xl mt-2">

                    Logout
                </a>

            <?php else: ?>

                <a href="<?= $base_url ?>/auth/login.php"
                class="border text-center py-3 rounded-xl">

                    Login
                </a>

                <a href="<?= $base_url ?>/auth/register.php"
                class="bg-slate-900 text-white text-center py-3 rounded-xl">

                    Register
                </a>

            <?php endif; ?>

        </div>

    </div>

</nav>

<!-- Spacer -->
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

        navbar.classList.add(
            'shadow-lg',
            'backdrop-blur-md',
            'bg-white/90'
        );

    } else {

        navbar.classList.remove(
            'shadow-lg',
            'backdrop-blur-md',
            'bg-white/90'
        );
    }
});
</script>