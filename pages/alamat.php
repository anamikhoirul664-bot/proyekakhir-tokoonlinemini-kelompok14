<?php
session_start();

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
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Alamat Saya</title>

<script src="https://cdn.tailwindcss.com"></script>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body class="bg-slate-100 min-h-screen">

<?php include '../components/navbar.php'; ?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-10">

    <div class="bg-white rounded-3xl shadow-sm overflow-hidden">

        <!-- HEADER -->
        <div class="bg-gradient-to-r from-blue-600 to-cyan-500 p-6">

            <h1 class="text-white text-2xl md:text-3xl font-bold">
                <i class="fas fa-location-dot mr-2"></i>
                Alamat Saya
            </h1>

            <p class="text-white/80 mt-2">
                Kelola alamat pengiriman Anda dengan mudah.
            </p>

        </div>

        <!-- CONTENT -->
        <div class="p-5 md:p-8">

            <form action="simpan_alamat.php" method="POST">

                <label class="block mb-2 text-sm font-medium text-slate-700">
                    Alamat Lengkap
                </label>

                <textarea
                name="alamat"
                rows="6"
                required
                class="w-full border border-slate-300 rounded-xl p-4 focus:outline-none focus:ring-2 focus:ring-orange-500"
                placeholder="Masukkan alamat lengkap"><?= $user['alamat'] ?? '' ?></textarea>

                <div class="flex flex-col md:flex-row gap-3 mt-6">

                    <button
                    type="submit"
                    class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-xl transition">

                        <i class="fas fa-save mr-2"></i>
                        Simpan Alamat

                    </button>

                    <a href="profile.php"
                    class="w-full md:w-auto text-center bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold px-8 py-3 rounded-xl transition">

                        Kembali

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

</body>
</html>