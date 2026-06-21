<?php
// Pastikan session dimulai paling atas untuk validasi login admin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../../config/koneksi.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../../auth/login_admin.php");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data pesanan utama
$pesanan = mysqli_fetch_assoc(
    mysqli_query(
        $koneksi,
        "SELECT * FROM pesanan WHERE id_pesanan='$id'"
    )
);

if (!$pesanan) {
    echo "
    <script>
        alert('Pesanan tidak ditemukan!');
        window.location='index.php';
    </script>
    ";
    exit;
}

// Ambil detail item produk dalam pesanan
$detail = mysqli_query(
    $koneksi,
    "SELECT 
    detail_pesanan.*, 
    produk.nama_produk, 
    produk.harga, 
    produk.foto 
    
    FROM detail_pesanan
    
    JOIN produk 
    ON detail_pesanan.id_produk = produk.id_produk
    
    WHERE detail_pesanan.id_pesanan='$id'"
);

// PROSES 1: UBAH STATUS PESANAN (PENGIRIMAN)
if (isset($_POST['ubah_status'])) {
    $status = mysqli_real_escape_string($koneksi, $_POST['status']);
    
    mysqli_query(
        $koneksi,
        "UPDATE pesanan SET status_pesanan='$status' WHERE id_pesanan='$id'"
    );

    header("Location: detail.php?id=$id");
    exit;
}

// PROSES 2: VERIFIKASI / UBAH STATUS PEMBAYARAN
if (isset($_POST['ubah_pembayaran'])) {
    $status_bayar = mysqli_real_escape_string($koneksi, $_POST['status_pembayaran']);
    
    // Jika admin mengubah pembayaran menjadi dibayar, otomatis status pesanan naik ke 'diproses'
    $update_tambahan = "";
    if ($status_bayar === 'dibayar' && $pesanan['status_pesanan'] === 'pending') {
        $update_tambahan = ", status_pesanan='diproses'";
    }

    mysqli_query(
        $koneksi,
        "UPDATE pesanan SET status_pembayaran='$status_bayar' $update_tambahan WHERE id_pesanan='$id'"
    );

    header("Location: detail.php?id=$id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-slate-100">

<div class="flex min-h-screen">

    <aside class="w-72 bg-slate-900 text-white p-6 shrink-0">
        <h1 class="text-3xl font-bold mb-10">
            <span class="text-blue-500">Admin</span>Panel
        </h1>
        <nav class="space-y-3">
            <a href="../dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 transition">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            <a href="../produk/index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 transition">
                <i class="fas fa-box"></i> Produk
            </a>
            <a href="../kategori/index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 transition">
                <i class="fas fa-tags"></i> Kategori
            </a>
            <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600">
                <i class="fas fa-cart-shopping"></i> Pesanan
            </a>
        </nav>
    </aside>

    <main class="flex-1 p-10">
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-4xl font-bold text-slate-800">Detail Pesanan</h1>
                <p class="text-slate-500 mt-2">ID Transaksi: <span class="font-bold text-slate-700">#TRX-<?= $pesanan['id_pesanan'] ?></span></p>
            </div>
            <a href="index.php" class="bg-slate-800 text-white px-5 py-3 rounded-2xl hover:bg-slate-700 transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left text-sm"></i> Kembali
            </a>
        </div>

        <div class="bg-white rounded-[30px] p-8 shadow-sm mb-8">
            <h2 class="text-2xl font-bold mb-6 text-slate-800">Data Pembeli</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <p class="text-slate-400 text-sm">Nama Pembeli</p>
                    <h3 class="font-bold text-lg text-slate-800"><?= htmlspecialchars($pesanan['nama_pembeli']) ?></h3>
                </div>
                <div>
                    <p class="text-slate-400 text-sm">Metode Pembayaran</p>
                    <h3 class="font-bold text-lg text-blue-600"><?= strtoupper($pesanan['metode_pembayaran']) ?></h3>
                </div>
                <div>
                    <p class="text-slate-400 text-sm">Nomor HP</p>
                    <h3 class="font-bold text-lg text-slate-800"><?= htmlspecialchars($pesanan['no_hp']) ?></h3>
                </div>
                <div>
                    <p class="text-slate-400 text-sm">Batas Waktu Pembayaran (Online)</p>
                    <h3 class="font-semibold text-sm <?= $pesanan['batas_bayar'] ? 'text-red-500' : 'text-slate-500' ?>">
                        <?= $pesanan['batas_bayar'] ? date('d M Y H:i', strtotime($pesanan['batas_bayar'])) : 'Tanpa Batas Waktu (COD)' ?>
                    </h3>
                </div>
                <div class="md:col-span-2 border-t pt-4">
                    <p class="text-slate-400 text-sm">Alamat Lengkap Pengiriman</p>
                    <h3 class="font-medium text-slate-700 mt-1"><?= htmlspecialchars($pesanan['alamat']) ?></h3>
                </div>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-8 mb-8">
            
            <div class="bg-white rounded-[30px] p-8 shadow-sm flex flex-col justify-between">
                <div>
                    <p class="text-slate-400 text-sm">Status Pembayaran Saat Ini</p>
                    <span class="inline-block mt-2 px-5 py-2 rounded-full font-semibold text-sm
                    <?= match($pesanan['status_pembayaran']){
                        'belum_bayar' => 'bg-red-100 text-red-600',
                        'menunggu_verifikasi' => 'bg-yellow-100 text-yellow-600',
                        'dibayar' => 'bg-green-100 text-green-600',
                        default => 'bg-gray-100 text-gray-600'
                    } ?>">
                        <?= strtoupper(str_replace('_', ' ', $pesanan['status_pembayaran'])) ?>
                    </span>

                    <?php if(!empty($pesanan['bukti_pembayaran'])): ?>
                        <div class="mt-5 pt-4 border-t border-dashed">
                            <p class="text-sm font-medium text-slate-500 mb-2">Bukti Transfer Pelanggan:</p>
                            <a href="../../assets/images/bukti_bayar/<?= $pesanan['bukti_pembayaran'] ?>" target="_blank" class="inline-block group">
                                <img src="../../assets/images/bukti_bayar/<?= $pesanan['bukti_pembayaran'] ?>" class="w-40 h-auto rounded-xl border shadow-sm group-hover:scale-105 transition duration-300" alt="Bukti Bayar">
                                <span class="text-xs text-blue-500 block mt-1 hover:underline"><i class="fa-solid fa-magnifying-glass-plus"></i> Klik untuk memperbesar</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <form method="POST" class="mt-6 pt-6 border-t flex gap-2">
                    <select name="status_pembayaran" class="flex-1 border border-slate-200 px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium">
                        <option value="belum_bayar" <?= $pesanan['status_pembayaran'] == 'belum_bayar' ? 'selected' : '' ?>>Belum Bayar </option>
                        <option value="menunggu_verifikasi" <?= $pesanan['status_pembayaran'] == 'menunggu_verifikasi' ? 'selected' : '' ?>>Menunggu Verifikasi </option>
                        <option value="dibayar" <?= $pesanan['status_pembayaran'] == 'dibayar' ? 'selected' : '' ?>>Dibayar (Terverifikasi)</option>
                    </select>
                    <button type="submit" name="ubah_pembayaran" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-5 py-3 rounded-xl transition text-sm whitespace-nowrap">
                        Verifikasi Uang
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-[30px] p-8 shadow-sm flex flex-col justify-between">
                <div>
                    <p class="text-slate-400 text-sm">Status Alur Barang</p>
                    <span class="inline-block mt-2 px-5 py-2 rounded-full font-semibold text-sm
                    <?= match($pesanan['status_pesanan']){
                        'pending' => 'bg-yellow-100 text-yellow-600',
                        'diproses' => 'bg-blue-100 text-blue-600',
                        'dikirim' => 'bg-purple-100 text-purple-600',
                        'selesai' => 'bg-green-100 text-green-600',
                        default => 'bg-gray-100 text-gray-600'
                    } ?>">
                        <?= ucfirst($pesanan['status_pesanan']) ?>
                    </span>
                </div>

                <form method="POST" class="mt-6 pt-6 border-t flex gap-2">
                    <select name="status" class="flex-1 border border-slate-200 px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium">
                        <option value="pending" <?= $pesanan['status_pesanan'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="diproses" <?= $pesanan['status_pesanan'] == 'diproses' ? 'selected' : '' ?>>Diproses</option>
                        <option value="dikirim" <?= $pesanan['status_pesanan'] == 'dikirim' ? 'selected' : '' ?>>Dikirim</option>
                        <option value="selesai" <?= $pesanan['status_pesanan'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                    </select>
                    <button type="submit" name="ubah_status" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-3 rounded-xl transition text-sm whitespace-nowrap">
                        Update Status
                    </button>
                </form>
            </div>

        </div>

        <div class="bg-white rounded-[30px] shadow-sm overflow-hidden mb-8">
            <div class="p-8 border-b">
                <h2 class="text-2xl font-bold text-slate-800">Produk Yang Dibeli</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 text-slate-500 text-sm">
                        <tr>
                            <th class="p-5 text-left font-semibold">Produk</th>
                            <th class="p-5 text-center font-semibold">Harga</th>
                            <th class="p-5 text-center font-semibold">Qty</th>
                            <th class="p-5 text-right font-semibold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php while($row = mysqli_fetch_assoc($detail)): ?>
                            <tr class="text-slate-700">
                                <td class="p-5">
                                    <div class="flex items-center gap-4">
                                        <img src="../../assets/images/<?= $row['foto'] ?>" class="w-16 h-16 rounded-xl object-cover border" alt="">
                                        <h3 class="font-bold text-slate-800"><?= htmlspecialchars($row['nama_produk']) ?></h3>
                                    </div>
                                </td>
                                <td class="p-5 text-center">Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                <td class="p-5 text-center font-medium text-slate-600"><?= $row['jumlah'] ?></td>
                                <td class="p-5 text-right font-bold text-slate-800">Rp <?= number_format($row['subtotal'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="bg-slate-50 p-8 flex justify-between items-center border-t">
                <h2 class="text-xl font-bold text-slate-700">Total Pembayaran</h2>
                <h2 class="text-3xl font-extrabold text-blue-600">
                    Rp <?= number_format($pesanan['total_bayar'], 0, ',', '.') ?>
                </h2>
            </div>
        </div>

    </main> </div> </body>
</html>