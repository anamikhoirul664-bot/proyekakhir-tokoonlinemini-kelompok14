<?php
include '../config/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// wajib login
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Profil Saya</title>

<script src="https://cdn.tailwindcss.com"></script>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body class="bg-slate-100">

<?php include '../components/navbar.php'; ?>

<div class="max-w-5xl mx-auto px-4 py-10">

    <!-- HEADER PROFILE -->
<div class="relative overflow-hidden bg-gradient-to-r from-blue-600 via-blue-500 to-cyan-500 rounded-3xl p-8 shadow-xl">

    <div class="flex flex-col md:flex-row items-center gap-6">

        <div class="relative">

            <?php if(!empty($user['foto'])): ?>

                <img
                src="../assets/profile/<?= $user['foto'] ?>"
                class="w-28 h-28 rounded-full object-cover border-4 border-white shadow-lg">

            <?php else: ?>

                <div class="w-28 h-28 rounded-full bg-white/20 border-4 border-white flex items-center justify-center text-5xl text-white">

                    <i class="fas fa-user"></i>

                </div>

            <?php endif; ?>

            <span class="absolute bottom-2 right-2 w-5 h-5 bg-green-400 border-2 border-white rounded-full"></span>

        </div>

        <div class="flex-1 text-white">

            <p class="text-white/80 text-sm">
                Selamat Datang 👋
            </p>

            <h1 class="text-4xl font-bold">
                <?= $user['nama']; ?>
            </h1>

            <p class="mt-2 text-white/90">
                <?= $user['email']; ?>
            </p>

        </div>

    </div>

</div>

   <div class="grid grid-cols-2 lg:grid-cols-5 gap-5 mt-8">

    <!-- CARD -->
    <a href="pesanan_saya.php"
       class="bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl transition duration-300 group">

        <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center mb-4">

            <i class="fas fa-box text-blue-600 text-xl"></i>

        </div>

        <h3 class="font-bold text-slate-800">
            Pesanan
        </h3>

        <p class="text-sm text-slate-500 mt-1">
            Riwayat transaksi
        </p>

    </a>

    <a href="cart.php"
       class="bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl transition">

        <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center mb-4">

            <i class="fas fa-cart-shopping text-green-600 text-xl"></i>

        </div>

        <h3 class="font-bold text-slate-800">
            Keranjang
        </h3>

        <p class="text-sm text-slate-500 mt-1">
            Produk pilihan
        </p>

    </a>

    <a href="alamat.php"
       class="bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl transition">

        <div class="w-14 h-14 bg-orange-100 rounded-2xl flex items-center justify-center mb-4">

            <i class="fas fa-location-dot text-orange-600 text-xl"></i>

        </div>

        <h3 class="font-bold text-slate-800">
            Alamat
        </h3>

        <p class="text-sm text-slate-500 mt-1">
            Kelola alamat
        </p>

    </a>

    <a href="ubah_password.php"
       class="bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl transition">

        <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center mb-4">

            <i class="fas fa-lock text-purple-600 text-xl"></i>

        </div>

        <h3 class="font-bold text-slate-800">
            Password
        </h3>

        <p class="text-sm text-slate-500 mt-1">
            Keamanan akun
        </p>

    </a>

    <a href="../auth/logout.php"
       onclick="return confirm('Yakin ingin logout?')"
       class="bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl transition">

        <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center mb-4">

            <i class="fas fa-right-from-bracket text-red-600 text-xl"></i>

        </div>

        <h3 class="font-bold text-slate-800">
            Logout
        </h3>

        <p class="text-sm text-slate-500 mt-1">
            Keluar akun
        </p>

    </a>

</div>

<div class="bg-white rounded-3xl p-8 shadow-sm mt-8">

    <div class="flex justify-between items-center mb-8">

        <h2 class="text-2xl font-bold text-slate-800">
            Informasi Akun
        </h2>

        <a href="edit_profil.php"
        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold transition">

            <i class="fas fa-pen mr-2"></i>
            Edit Profil

        </a>

    </div>

    <div class="grid md:grid-cols-2 gap-6">

        <div>
            <label class="text-sm text-slate-500">
                Nama Lengkap
            </label>

            <input
            type="text"
            readonly
            value="<?= $user['nama']; ?>"
            class="w-full mt-2 p-4 bg-slate-50 border rounded-xl">
        </div>

        <div>
            <label class="text-sm text-slate-500">
                Email
            </label>

            <input
            type="text"
            readonly
            value="<?= $user['email']; ?>"
            class="w-full mt-2 p-4 bg-slate-50 border rounded-xl">
        </div>

        <div>
            <label class="text-sm text-slate-500">
                Nomor HP
            </label>

            <input
            type="text"
            readonly
            value="<?= !empty($user['no_hp']) ? $user['no_hp'] : 'Belum diisi'; ?>"
            class="w-full mt-2 p-4 bg-slate-50 border rounded-xl">
        </div>

        <div>
            <label class="text-sm text-slate-500">
                Password
            </label>

            <input
            type="password"
            readonly
            value="12345678"
            class="w-full mt-2 p-4 bg-slate-50 border rounded-xl">
        </div>

    </div>

    <div class="mt-6">

        <label class="text-sm text-slate-500">
            Alamat Lengkap
        </label>

        <textarea
        readonly
        rows="4"
        class="w-full mt-2 p-4 bg-slate-50 border rounded-xl resize-none"><?= !empty($user['alamat']) ? $user['alamat'] : 'Alamat belum diisi'; ?></textarea>

    </div>

</div>

</body>
</html>