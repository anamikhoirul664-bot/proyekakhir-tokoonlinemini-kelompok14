<?php
include '../../config/koneksi.php';

// Proteksi Admin
if (!isset($_SESSION['admin'])) {
    header("Location: ../../auth/login_admin.php");
    exit;
}

// Tambah kategori
if(isset($_POST['tambah'])){

    $nama_kategori = mysqli_real_escape_string(
        $koneksi,
        $_POST['nama_kategori']
    );

    mysqli_query(
        $koneksi,
        "INSERT INTO kategori(nama_kategori)
        VALUES('$nama_kategori')"
    );

    header("Location: index.php");
    exit;
}

// Hapus kategori
if(isset($_GET['hapus'])){

    $id = $_GET['hapus'];

    mysqli_query(
        $koneksi,
        "DELETE FROM kategori
        WHERE id_kategori='$id'"
    );

    header("Location: index.php");
    exit;
}

// Search
$cari = $_GET['cari'] ?? '';

$query = mysqli_query(
$koneksi,
"SELECT * FROM kategori
WHERE nama_kategori LIKE '%$cari%'
ORDER BY id_kategori DESC"
);

// Statistik
$total_kategori = mysqli_num_rows(
    mysqli_query(
        $koneksi,
        "SELECT * FROM kategori"
    )
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Kelola Kategori</title>

<script src="https://cdn.tailwindcss.com"></script>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="bg-slate-100">

<div class="flex min-h-screen">


<aside class="w-72 bg-slate-900 text-white p-6">

    <h1 class="text-3xl font-bold mb-10">
        <span class="text-blue-500">
            Admin
        </span>Panel
    </h1>

    <nav class="space-y-3">

        <a href="../dashboard.php"
        class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 transition">

            <i class="fas fa-chart-line"></i>
            Dashboard
        </a>

        <a href="../produk/index.php"
        class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 transition">

            <i class="fas fa-box"></i>
            Produk
        </a>

        <a href="index.php"
        class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600">

            <i class="fas fa-tags"></i>
            Kategori
        </a>

        <a href="../pesanan/index.php"
        class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 transition">

            <i class="fas fa-cart-shopping"></i>
            Pesanan
        </a>

    </nav>

</aside>


<main class="flex-1 p-10">


    <div class="flex justify-between items-center mb-10">

        <div>
            <h1 class="text-4xl font-bold text-slate-800">
                Kelola Kategori
            </h1>

            <p class="text-slate-500 mt-2">
                Tambah dan kelola kategori produk
            </p>
        </div>

    </div>


    <div class="bg-white rounded-3xl p-6 shadow-sm mb-8">

        <p class="text-slate-500">
            Total Kategori
        </p>

        <h2 class="text-4xl font-bold text-blue-600 mt-2">
            <?= $total_kategori ?>
        </h2>

    </div>

    <div class="grid lg:grid-cols-3 gap-8">


        <div class="bg-white rounded-[30px] shadow-sm p-8 h-fit">

            <h3 class="text-2xl font-bold mb-6">
                Tambah Kategori
            </h3>

            <form method="POST" class="space-y-5">

                <div>

                    <label class="font-semibold block mb-2">
                        Nama Kategori
                    </label>

                    <input
                    type="text"
                    name="nama_kategori"
                    required
                    placeholder="Contoh: Elektronik"
                    class="w-full border rounded-2xl px-5 py-4 outline-none focus:ring-2 focus:ring-blue-500">

                </div>

                <button
                type="submit"
                name="tambah"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-bold transition">

                    Simpan Kategori
                </button>

            </form>

        </div>


        <div class="lg:col-span-2">


            <div class="bg-white rounded-3xl p-5 shadow-sm mb-5">

                <form method="GET">

                    <div class="flex gap-3">

                        <input
                        type="text"
                        name="cari"
                        value="<?= $cari ?>"
                        placeholder="Cari kategori..."
                        class="w-full border rounded-2xl px-5 py-4 outline-none focus:ring-2 focus:ring-blue-500">

                        <button
                        class="bg-slate-900 text-white px-8 rounded-2xl">

                            Cari
                        </button>

                    </div>

                </form>

            </div>


            <div class="bg-white rounded-[30px] shadow-sm overflow-hidden">

                <table class="w-full">

                    <thead class="bg-slate-50">

                        <tr>
                            <th class="p-6">No</th>
                            <th>Nama Kategori</th>
                            <th class="text-center">
                                Aksi
                            </th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php
                    $no = 1;
                    if(mysqli_num_rows($query) > 0):
                    while($row = mysqli_fetch_assoc($query)):
                    ?>

                    <tr class="border-b hover:bg-slate-50 transition">

                        <td class="p-6">
                            <?= $no++ ?>
                        </td>

                        <td class="font-semibold">
                            <?= $row['nama_kategori'] ?>
                        </td>

                        <td class="text-center">

                            <a
                            href="index.php?hapus=<?= $row['id_kategori'] ?>"
                            onclick="return confirm('Yakin hapus kategori ini?')"
                            class="bg-red-500 text-white px-4 py-3 rounded-xl">

                                <i class="fas fa-trash"></i>
                            </a>

                        </td>

                    </tr>

                    <?php endwhile; else: ?>

                    <tr>
                        <td colspan="3"
                        class="text-center py-16 text-slate-500">

                            Tidak ada kategori ditemukan
                        </td>
                    </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</main>

</div>

</body>
</html>