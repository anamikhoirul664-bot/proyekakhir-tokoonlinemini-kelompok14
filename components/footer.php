<?php
$base_url = "/PROJEK%20AKHIR";
?>

<footer class="bg-slate-950 text-gray-300 mt-24">


    <div class="container mx-auto px-6 py-16">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">


            <div>

                <h2 class="text-3xl font-extrabold mb-5">
                    <span class="text-blue-500">
                        NexaTech
                    </span>
                    <span class="text-white">
                        Store
                    </span>
                </h2>

                <p class="text-sm leading-relaxed text-gray-400">
                    Platform toko online modern berbasis PHP Native &
                    Tailwind CSS yang dibuat untuk pembelajaran web
                    development sekaligus simulasi ecommerce profesional.
                </p>

                <div class="flex gap-4 mt-6">

                    <a href="https://instagram.com/anami6702"
                    class="bg-slate-800 hover:bg-blue-600 transition w-11 h-11 rounded-xl flex items-center justify-center">
                        <i class="fab fa-instagram"></i>
                    </a>

                    <a href="https://facebook.com/khoirulanami"
                    class="bg-slate-800 hover:bg-blue-600 transition w-11 h-11 rounded-xl flex items-center justify-center">
                        <i class="fab fa-facebook-f"></i>
                    </a>

                    <a href="https://wa.me/6281945509422?text=Halo%20saya%20ingin%20bertanya%20tentang%20produk"
                    class="bg-slate-800 hover:bg-green-600 transition w-11 h-11 rounded-xl flex items-center justify-center">
                        <i class="fab fa-whatsapp"></i>
                    </a>

                </div>

            </div>


            <div>

                <h3 class="text-white text-xl font-bold mb-5">
                    Navigasi
                </h3>

                <ul class="space-y-3">

                    <li>
                        <a href="<?= $base_url ?>/index.php"
                        class="hover:text-blue-400 transition">
                            Beranda
                        </a>
                    </li>

                    <li>
                        <a href="<?= $base_url ?>/pages/produk.php"
                        class="hover:text-blue-400 transition">
                            Produk
                        </a>
                    </li>

                    <li>
                        <a href="<?= $base_url ?>/pages/cart.php"
                        class="hover:text-blue-400 transition">
                            Keranjang
                        </a>
                    </li>

                </ul>

            </div>


            <div>

                <h3 class="text-white text-xl font-bold mb-5">
                    Bantuan
                </h3>

            <ul class="space-y-3 text-sm text-gray-400">

                <li>
                    <a href="/PROJEK%20AKHIR/faq.php" class="hover:text-blue-500">
                        FAQ
                    </a>
                </li>

                <li>
                    <a href="/PROJEK%20AKHIR/cara-belanja.php" class="hover:text-blue-500">
                        Cara Belanja
                    </a>
                </li>

                <li>
                    <a href="/PROJEK%20AKHIR/syarat-ketentuan.php" class="hover:text-blue-500">
                        Syarat & Ketentuan
                    </a>
                </li>

                <li>
                    <a href="/PROJEK%20AKHIR/kebijakan-privasi.php" class="hover:text-blue-500">
                        Kebijakan Privasi
                    </a>
                </li>

            </ul>

            </div>


            <div>

                <h3 class="text-white text-xl font-bold mb-5">
                    Kontak Kami
                </h3>

                <div class="space-y-4 text-sm text-gray-400">

                    <p class="flex items-start gap-3">
                        <i class="fas fa-location-dot mt-1 text-blue-500"></i>
                        Indonesia
                    </p>

                    <p class="flex items-center gap-3">
                        <i class="fas fa-envelope text-blue-500"></i>
                        support@tokomini.com
                    </p>

                    <p class="flex items-center gap-3">
                        <i class="fas fa-phone text-blue-500"></i>
                        +62 812-3456-7890
                    </p>

                    <p class="flex items-center gap-3">
                        <i class="fas fa-clock text-blue-500"></i>
                        Senin - Sabtu (08:00 - 21:00)
                    </p>

                </div>

            </div>

        </div>
    </div>


    <div class="border-t border-slate-800">

        <div class="container mx-auto px-6 py-6 flex flex-col md:flex-row justify-between items-center gap-4">

            <p class="text-sm text-gray-500 text-center md:text-left">
                © <?= date('Y') ?> Toko Mini. Dibuat dengan
                <span class="text-red-500">❤</span>
                menggunakan PHP & Tailwind CSS.
            </p>

            <p class="text-xs text-gray-600">
                Version 1.0 | Educational Project
            </p>

        </div>

    </div>

</footer>