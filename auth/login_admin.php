<?php

include '../config/koneksi.php';

if(isset($_POST['login'])){

    $email = mysqli_real_escape_string(
        $koneksi,
        $_POST['email']
    );

    $password = $_POST['password'];

    $query = mysqli_query(
        $koneksi,
        "SELECT * FROM users
        WHERE email='$email'
        AND role='admin'"
    );

    if(mysqli_num_rows($query) > 0){

        $data = mysqli_fetch_assoc($query);

        if(password_verify(
            $password,
            $data['password']
        )){

            $_SESSION['admin'] = $data;

            header(
                "Location: ../admin/dashboard.php"
            );
            exit;

        } else {
            $error = "Password salah!";
        }

    } else {
        $error = "Admin tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Login Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body class="bg-gradient-to-br from-blue-100 via-white to-blue-200 min-h-screen flex items-center justify-center p-6">

    <div class="bg-white shadow-2xl rounded-[30px] p-10 w-full max-w-md border border-slate-100">

        <div class="text-center mb-8">

            <div class="bg-blue-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-5 shadow-lg">
                <i class="fas fa-user-shield text-white text-3xl"></i>
            </div>

            <h1 class="text-4xl font-extrabold text-slate-800">
                Admin Login
            </h1>

            <p class="text-gray-500 mt-2">
                Masuk untuk mengelola toko
            </p>

        </div>

        <?php if(isset($error)): ?>
            <div class="bg-red-100 text-red-600 p-4 rounded-xl mb-5 text-sm">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">


            <div>
                <label class="font-semibold block mb-2 text-slate-700">
                    Email Admin
                </label>

                <input
                    type="email"
                    name="email"
                    required
                    class="w-full border border-slate-300 rounded-2xl px-5 py-4 outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="admin@gmail.com">
            </div>


            <div>
                <label class="font-semibold block mb-2 text-slate-700">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    required
                    class="w-full border border-slate-300 rounded-2xl px-5 py-4 outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Masukkan password">
            </div>


            <button
                type="submit"
                name="login"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-bold transition duration-300 shadow-lg hover:scale-[1.02]">

                Masuk Sekarang
            </button>

        </form>

        <div class="text-center mt-6">

            <a href="../index.php"
            class="text-blue-600 hover:underline text-sm">

                ← Kembali ke Toko
            </a>

        </div>

    </div>

</body>
</html>