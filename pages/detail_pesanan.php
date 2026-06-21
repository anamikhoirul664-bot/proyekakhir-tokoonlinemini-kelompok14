<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id_user = mysqli_real_escape_string($koneksi, $_SESSION['user']['id_user']);
// Memastikan id_pesanan berupa angka untuk keamanan tambahan
$id_pesanan = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data pesanan
$pesanan = mysqli_fetch_assoc(
    mysqli_query(
        $koneksi,
        "SELECT * FROM pesanan 
        WHERE id_pesanan='$id_pesanan' 
        AND id_user='$id_user'"
    )
);

if (!$pesanan) {
    echo "
    <script>
        alert('Pesanan tidak ditemukan');
        window.location='pesanan_saya.php';
    </script>
    ";
    exit;
}

// Pengecekan batas_bayar dipindahkan ke sini setelah variabel $pesanan didefinisikan
if (!isset($pesanan['batas_bayar'])) {
    $pesanan['batas_bayar'] = null;
}

// Ambil detail produk dalam pesanan
$detail = mysqli_query(
    $koneksi,
    "SELECT 
    dp.*, 
    p.nama_produk, 
    p.foto, 
    p.harga 
    
    FROM detail_pesanan dp
    
    JOIN produk p 
    ON dp.id_produk = p.id_produk
    
    WHERE dp.id_pesanan='$id_pesanan'"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="bg-slate-100">

<?php include '../components/navbar.php'; ?>

<div class="max-w-5xl mx-auto px-4 py-6 sm:py-8">

    <div class="bg-white p-5 sm:p-6 rounded-[28px] shadow-sm mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">
            Detail Pesanan
        </h1>
        <p class="text-slate-500 mt-2 text-sm sm:text-base">
            ID Pesanan: 
            <span class="font-semibold">
                #TRX-<?= $pesanan['id_pesanan'] ?>
            </span>
        </p>
        <p class="text-slate-500 text-sm">
            <?= date('d M Y H:i', strtotime($pesanan['tanggal_pesan'])) ?>
        </p>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm mb-6">
        <h2 class="font-bold text-lg mb-4">Status Pesanan</h2>
        <div class="flex flex-wrap gap-3">
            <span class="px-4 py-2 rounded-full text-sm font-semibold 
            <?= match($pesanan['status_pembayaran']){
                'belum_bayar' => 'bg-red-100 text-red-600',
                'menunggu_bayar' => 'bg-yellow-100 text-yellow-600',
                'lunas' => 'bg-green-100 text-green-600',
                default => 'bg-gray-100 text-gray-600'
            } ?>">
                Pembayaran: <?= strtoupper(str_replace('_',' ',$pesanan['status_pembayaran'])) ?>
            </span>

            <span class="px-4 py-2 rounded-full text-sm font-semibold 
            <?= match($pesanan['status_pesanan']){
                'pending' => 'bg-yellow-100 text-yellow-600',
                'diproses' => 'bg-blue-100 text-blue-600',
                'dikirim' => 'bg-purple-100 text-purple-600',
                'selesai' => 'bg-green-100 text-green-600',
                default => 'bg-gray-100 text-gray-600'
            } ?>">
                Pesanan: <?= strtoupper($pesanan['status_pesanan']) ?>
            </span>
        </div>
    </div>

    <div class="bg-white p-5 sm:p-6 rounded-[28px] shadow-sm mb-6">
        <h2 class="font-bold text-lg mb-5">
            Produk Pesanan
        </h2>
        <div class="space-y-5">
            <?php while($row = mysqli_fetch_assoc($detail)): ?>
                <div class="flex flex-col sm:flex-row gap-4 border-b pb-5">
                    <img src="../assets/images/<?= $row['foto'] ?>" class="w-full sm:w-24 h-52 sm:h-24 object-cover rounded-2xl" alt="<?= $row['nama_produk'] ?>">
                    <div class="flex-1">
                        <h3 class="font-bold text-slate-800 text-lg">
                            <?= $row['nama_produk'] ?>
                        </h3>
                        <p class="text-slate-500 text-sm mt-1">
                            Qty: <?= $row['jumlah'] ?>
                        </p>
                        <p class="text-slate-500 text-sm">
                            Harga: Rp <?= number_format($row['harga'], 0, ',', '.') ?>
                        </p>
                    </div>
                    <div class="sm:text-right">
                        <p class="text-sm text-slate-500">Subtotal</p>
                        <h3 class="font-bold text-blue-600 text-lg">
                            Rp <?= number_format($row['subtotal'], 0, ',', '.') ?>
                        </h3>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <div class="bg-white p-5 sm:p-6 rounded-[28px] shadow-sm mb-6">
        <h2 class="font-bold text-lg mb-5">
            Ringkasan Pembayaran
        </h2>
        <div class="space-y-4">
            <div class="flex justify-between">
                <span class="text-slate-500">Metode Pembayaran</span>
                <span class="font-semibold">
                    <?= strtoupper($pesanan['metode_pembayaran']) ?>
                </span>
            </div>
            <div class="flex justify-between text-xl font-bold border-t pt-4">
                <span>Total</span>
                <span class="text-blue-600">
                    Rp <?= number_format($pesanan['total_bayar'], 0, ',', '.') ?>
                </span>
            </div>
        </div>
    </div>

    <?php if($pesanan['status_pembayaran'] == 'belum_bayar' && !empty($pesanan['batas_bayar'])): ?>
        <div class="bg-red-50 text-red-600 p-5 rounded-[22px] mb-6 border border-red-100 shadow-sm">
            <div class="font-semibold mb-2 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left"></i> Selesaikan pembayaran sebelum:
            </div>
            <div id="countdown" class="text-xl font-bold tracking-wide"></div>
        </div>

        <script>
        const deadline = new Date("<?= $pesanan['batas_bayar'] ?>").getTime();

        const timer = setInterval(() => {
            const now = new Date().getTime();
            const diff = deadline - now;

            if(diff <= 0){
                document.getElementById("countdown").innerHTML = "<span class='text-red-500 font-extrabold'>WAKTU PEMBAYARAN HABIS</span>";
                clearInterval(timer);
                return;
            }

            const h = Math.floor(diff / (1000 * 60 * 60));
            const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const s = Math.floor((diff % (1000 * 60)) / 1000);

            document.getElementById("countdown").innerHTML = 
                h + " jam " + m + " menit " + s + " detik";
        }, 1000);
        </script>
    <?php endif; ?>

    <?php if($pesanan['status_pembayaran'] == 'belum_bayar'): ?>
        <div class="flex justify-end">
            <a href="bayar.php?id=<?= $pesanan['id_pesanan'] ?>" 
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 transition text-white px-8 py-4 rounded-2xl font-bold shadow-md hover:shadow-lg w-full sm:w-auto justify-center">
               <i class="fa-solid fa-credit-card"></i> Bayar Sekarang
            </a>
        </div>
    <?php endif; ?>

</div> </body>
</html>