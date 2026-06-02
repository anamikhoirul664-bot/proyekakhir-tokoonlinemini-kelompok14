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
        AND role='user'"
    );

    if(mysqli_num_rows($query) > 0){

        $data = mysqli_fetch_assoc($query);

        if(password_verify(
            $password,
            $data['password']
        )){

            $_SESSION['user'] = $data;

            header("Location: ../index.php");
            exit;

        } else {
            $error = "Password salah!";
        }

    } else {
        $error = "Akun tidak ditemukan!";
    }
}
?>



<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Login User</title>

<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-blue-100 via-white to-blue-200 min-h-screen flex items-center justify-center p-6">

<div class="bg-white p-10 rounded-[30px] shadow-2xl w-full max-w-md">

    <h1 class="text-4xl font-bold text-center text-blue-600">
        Login Akun
    </h1>

    <p class="text-center text-gray-500 mt-2 mb-8">
        Masuk untuk mulai belanja
    </p>

    <?php if(isset($error)): ?>
        <div class="bg-red-100 text-red-600 p-4 rounded-xl mb-5">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['success'])): ?>
    <div class="bg-green-100 text-green-600 p-4 rounded-xl mb-5">
        <?= $_SESSION['success']; ?>
    </div>
    <?php unset($_SESSION['success']); endif; ?>

    <form method="POST" class="space-y-5">

        <input
        type="email"
        name="email"
        required
        placeholder="Email"
        class="w-full border rounded-xl px-5 py-4">

        <input
        type="password"
        name="password"
        required
        placeholder="Password"
        class="w-full border rounded-xl px-5 py-4">

        <button
        type="submit"
        name="login"
        class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold hover:bg-blue-700 transition">

            Login Sekarang
        </button>

    </form>

    <div class="text-center mt-5">

        <p class="text-sm text-gray-500">
            Belum punya akun?

            <a href="register.php"
            class="text-blue-600 font-semibold">
                Register
            </a>
        </p>

    </div>

</div>

</body>
</html>