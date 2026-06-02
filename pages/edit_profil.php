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

<title>Edit Profil</title>

<script src="https://cdn.tailwindcss.com"></script>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body class="bg-slate-100 min-h-screen">

<?php include '../components/navbar.php'; ?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-10">

    <div class="bg-white rounded-3xl shadow-sm overflow-hidden">

        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-cyan-500 p-6">

            <h1 class="text-white text-2xl md:text-3xl font-bold">
                <i class="fas fa-user-edit mr-2"></i>
                Edit Profil
            </h1>

            <p class="text-white/80 mt-2">
                Perbarui informasi akun Anda.
            </p>

        </div>

        <div class="p-5 md:p-8">

            <form
            action="proses_edit_profile.php"
            method="POST"
            enctype="multipart/form-data">

                <!-- FOTO PROFIL -->
                <div class="flex flex-col items-center mb-8">

                    <?php if(!empty($user['foto'])): ?>

                        <img
                        src="../assets/profile/<?= $user['foto'] ?>"
                        class="w-32 h-32 rounded-full object-cover border-4 border-blue-100 shadow-lg mb-4">

                    <?php else: ?>

                        <div class="w-32 h-32 rounded-full bg-slate-200 flex items-center justify-center text-5xl text-slate-500 mb-4">

                            <i class="fas fa-user"></i>

                        </div>

                    <?php endif; ?>

                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Foto Profil
                    </label>

                    <input
                    type="file"
                    name="foto"
                    class="w-full md:w-auto border border-slate-300 rounded-xl p-3">

                </div>

                <!-- FORM -->
                <div class="grid md:grid-cols-2 gap-5">

                    <div>

                        <label class="block mb-2 text-sm font-medium text-slate-700">
                            Nama Lengkap
                        </label>

                        <input
                        type="text"
                        name="nama"
                        value="<?= $user['nama'] ?>"
                        required
                        class="w-full border border-slate-300 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-blue-500">

                    </div>

                    <div>

                        <label class="block mb-2 text-sm font-medium text-slate-700">
                            Email
                        </label>

                        <input
                        type="email"
                        name="email"
                        value="<?= $user['email'] ?>"
                        required
                        class="w-full border border-slate-300 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-blue-500">

                    </div>

                    <div>

                        <label class="block mb-2 text-sm font-medium text-slate-700">
                            Nomor HP
                        </label>

                        <input
                        type="text"
                        name="no_hp"
                        value="<?= $user['no_hp'] ?? '' ?>"
                        class="w-full border border-slate-300 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-blue-500">

                    </div>

                </div>

                <div class="flex flex-col md:flex-row gap-3 mt-8">

                    <button
                    type="submit"
                    class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-xl transition">

                        <i class="fas fa-save mr-2"></i>
                        Simpan Perubahan

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