<?php

include '../config/koneksi.php';


if(isset($_POST['register'])){

    $nama = mysqli_real_escape_string(
        $koneksi,
        $_POST['nama']
    );

    $email = mysqli_real_escape_string(
        $koneksi,
        $_POST['email']
    );

    $password = password_hash(
        $_POST['password'],
        PASSWORD_DEFAULT
    );


    $cek = mysqli_query(
        $koneksi,
        "SELECT * FROM users
        WHERE email='$email'"
    );

    if(mysqli_num_rows($cek) > 0){

        $error = "Email sudah digunakan!";

    } else {

        mysqli_query(
        $koneksi,
        "INSERT INTO users
        (nama,email,password,role)
        VALUES
        ('$nama','$email','$password','user')"
        );

        $_SESSION['success'] = "Registrasi berhasil, silakan login.";

        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Register</title>

<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-blue-100 via-white to-blue-200 min-h-screen flex items-center justify-center p-6">

<div class="bg-white shadow-2xl rounded-[30px] p-10 w-full max-w-md">

    <h1 class="text-4xl font-bold text-center text-blue-600 mb-2">
        Buat Akun
    </h1>

    <p class="text-center text-gray-500 mb-8">
        Daftar untuk mulai belanja
    </p>

    <?php if(isset($error)): ?>
        <div class="bg-red-100 text-red-600 p-4 rounded-xl mb-5">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <?php if(isset($success)): ?>
        <div class="bg-green-100 text-green-600 p-4 rounded-xl mb-5">
            <?= $success ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-5">

        <input
        type="text"
        name="nama"
        required
        placeholder="Nama lengkap"
        class="w-full border rounded-xl px-5 py-4">

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
        name="register"
        class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold hover:bg-blue-700 transition">

            Daftar Sekarang
        </button>

    </form>

    <div class="text-center mt-5">

        <p class="text-sm text-gray-500">
            Sudah punya akun?

            <a href="login.php"
            class="text-blue-600 font-semibold">

                Login
            </a>
        </p>

    </div>

</div>

</body>
</html>