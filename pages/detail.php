<?php 
include '../config/koneksi.php'; 

$id = $_GET['id'];

$query = mysqli_query($koneksi, "
SELECT produk.*, kategori.nama_kategori
FROM produk
JOIN kategori 
ON produk.id_kategori = kategori.id_kategori
WHERE id_produk = '$id'
");

$data = mysqli_fetch_assoc($query);

// Produk terkait
$related = mysqli_query($koneksi, "
SELECT * FROM produk
WHERE id_kategori = '{$data['id_kategori']}'
AND id_produk != '$id'
LIMIT 4
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?= $data['nama_produk'] ?> - NexaTech Store</title>

<script src="https://cdn.tailwindcss.com"></script>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body class="bg-slate-100">

<?php include '../components/navbar.php'; ?>

<div class="max-w-7xl mx-auto px-4 py-6 lg:py-10">

    <a href="produk.php"
    class="inline-flex items-center gap-2 text-slate-600 hover:text-blue-600 mb-6 transition">
        <i class="fas fa-arrow-left"></i>
        Kembali ke Produk
    </a>


<div class="bg-white rounded-[30px] shadow-md overflow-hidden">

    <div class="grid lg:grid-cols-2 gap-0">

        <div class="bg-slate-100 p-5">
            <div class="bg-white rounded-[30px] overflow-hidden h-full flex items-center justify-center">
                <img
                src="../assets/images/<?= $data['foto'] ?>"
                alt="<?= $data['nama_produk'] ?>"
                class="w-full h-[300px] sm:h-[450px] object-contain">
            </div>
        </div>

        <div class="p-5 sm:p-8 lg:p-10">

            <span class="bg-blue-100 text-blue-600 px-4 py-2 rounded-full text-sm font-semibold">
                <?= $data['nama_kategori'] ?>
            </span>

            <h1 class="text-2xl sm:text-3xl font-bold mt-4 text-slate-800 leading-tight">
                <?= $data['nama_produk'] ?>
            </h1>

            <div class="mt-4">
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="text-4xl font-bold text-blue-600">
                        Rp <?= number_format($data['harga'],0,',','.') ?>
                    </span>
                    <span class="bg-red-100 text-red-600 px-3 py-1 rounded-lg text-sm font-bold">
                        -10%
                    </span>
                </div>
                <p class="text-slate-400 line-through mt-1">
                    Rp <?= number_format($data['harga'] + 2000000,0,',','.') ?>
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3 mt-6">
                <div class="bg-green-50 p-4 rounded-2xl">
                    <i class="fas fa-shield text-green-600 mb-2"></i>
                    <p class="font-semibold text-sm">Garansi Resmi</p>
                </div>

                <div class="bg-blue-50 p-4 rounded-2xl">
                    <i class="fas fa-circle-check text-blue-600 mb-2"></i>
                    <p class="font-semibold text-sm">Produk Original</p>
                </div>

                <div class="bg-yellow-50 p-4 rounded-2xl">
                    <i class="fas fa-truck text-yellow-600 mb-2"></i>
                    <p class="font-semibold text-sm">Pengiriman Cepat</p>
                </div>

                <div class="bg-purple-50 p-4 rounded-2xl">
                    <i class="fas fa-credit-card text-purple-600 mb-2"></i>
                    <p class="font-semibold text-sm">Pembayaran Aman</p>
                </div>
            </div>

            <div class="mt-6">
                <?php if($data['stok'] > 10): ?>
                    <span class="bg-green-100 text-green-700 px-4 py-2 rounded-full text-sm font-semibold">
                        ✓ Stok Tersedia
                    </span>
                <?php elseif($data['stok'] > 0): ?>
                    <span class="bg-yellow-100 text-yellow-700 px-4 py-2 rounded-full text-sm font-semibold">
                        ⚠ Stok Terbatas
                    </span>
                <?php else: ?>
                    <span class="bg-red-100 text-red-700 px-4 py-2 rounded-full text-sm font-semibold">
                        ✕ Stok Habis
                    </span>
                <?php endif; ?>
            </div>

            <form
            action="cart.php?action=add&id=<?= $data['id_produk'] ?>"
            method="POST"
            class="mt-8">

                <label class="font-semibold block mb-3">Jumlah</label>

                <input
                type="number"
                name="qty"
                value="1"
                min="1"
                max="<?= $data['stok'] ?>"
                class="w-full border rounded-2xl p-4 text-center text-lg">

                <div class="grid sm:grid-cols-2 gap-4 mt-5">
                    <button
                    type="submit"
                    name="aksi"
                    value="keranjang"
                    class="bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-bold transition">
                        <i class="fas fa-cart-plus mr-2"></i> Keranjang
                    </button>

                    <button
                    type="submit"
                    name="aksi"
                    value="beli"
                    class="bg-green-500 hover:bg-green-600 text-white py-4 rounded-2xl font-bold transition">
                        <i class="fas fa-bolt mr-2"></i> Beli Sekarang
                    </button>
                </div>

            </form>

        </div>

    </div>

    <div class="border-t px-5 sm:px-8 lg:px-10 py-10">
        <h3 class="font-bold text-2xl mb-4">Deskripsi Produk</h3>
        <p class="text-slate-700 leading-8 text-[15px]">
            <?= $data['deskripsi'] ?>
        </p>
    </div>

    <div class="border-t px-5 sm:px-8 lg:px-10 py-10">
        <h3 class="font-bold text-2xl mb-4">Spesifikasi Produk</h3>
        <div class="bg-slate-50 rounded-3xl p-6 whitespace-pre-line text-slate-700 leading-8">
            <?= $data['spesifikasi'] ?>
        </div>
    </div>

    <div class="border-t px-5 sm:px-8 lg:px-10 py-10">
        <h3 class="font-bold text-2xl mb-5">Detail Produk</h3>
        <div class="bg-slate-50 rounded-3xl overflow-hidden">
            <div class="flex justify-between px-6 py-5 border-b">
                <span class="text-slate-500">Brand</span>
                <span class="font-semibold"><?= $data['brand'] ?></span>
            </div>

            <div class="flex justify-between px-6 py-5 border-b">
                <span class="text-slate-500">Garansi</span>
                <span class="font-semibold"><?= $data['garansi'] ?></span>
            </div>

            <div class="flex justify-between px-6 py-5">
                <span class="text-slate-500">Berat</span>
                <span class="font-semibold">
                    <?= number_format($data['berat']) ?> gram
                </span>
            </div>
        </div>
    </div>

    <?php if(!empty($data['file_pdf'])): ?>
    <div class="border-t px-5 sm:px-8 lg:px-10 py-10 bg-slate-50">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5 p-6 bg-white border border-slate-200 rounded-3xl shadow-sm">
            <div class="flex items-start gap-4">
                <div class="bg-red-50 p-4 rounded-2xl text-red-500 text-2xl">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <div>
                    <h4 class="font-bold text-lg text-slate-800">Buku Panduan Pengguna</h4>
                    <p class="text-sm text-slate-500 mt-1">Unduh manual book produk ini untuk informasi petunjuk penggunaan yang lebih lengkap.</p>
                </div>
            </div>
            <div>
                <a href="../assets/pdf/<?= $data['file_pdf'] ?>" 
                   download="<?= $data['nama_produk'] ?>_Panduan.pdf" 
                   target="_blank" 
                   class="inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-slate-800 hover:bg-red-600 text-white px-6 py-4 rounded-2xl font-semibold shadow-md transition-all duration-300">
                    <i class="fas fa-download"></i>
                    Download PDF
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>
</div>
</body>
</html>