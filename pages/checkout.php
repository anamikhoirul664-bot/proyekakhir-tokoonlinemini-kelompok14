<?php
// Pastikan session dimulai paling atas sebelum ada output apa pun
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../config/koneksi.php';

// Proteksi halaman: pastikan user sudah login
if (!isset($_SESSION['user']['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id_user = (int) $_SESSION['user']['id_user'];
$total = 0;
$data_checkout = [];

// =========================================================================
// PERBAIKAN UTAMA: Deteksi dari mana User datang demi akurasi data barang
// =========================================================================

// Jika di URL tidak ada tanda beli langsung, artinya user datang dari klik "Lanjut ke Checkout" di cart.php.
// Kita harus hapus paksa session checkout_langsung yang tertinggal agar tidak merusak data.
if (!isset($_GET['source']) || $_GET['source'] !== 'direct') {
    unset($_SESSION['checkout_langsung']);
}

// 1. AMBIL DATA SESUAI ALUR YANG DIPILIH USER
if (isset($_SESSION['checkout_langsung'])) {
    // --- ALUR BELI LANGSUNG (DARI DETAIL PRODUK) ---
    $id_produk = mysqli_real_escape_string($koneksi, $_SESSION['checkout_langsung']['id_produk']);
    $qty = (int)$_SESSION['checkout_langsung']['qty'];

    $query = mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk='$id_produk'");
    $produk = mysqli_fetch_assoc($query);

    if (!$produk) {
        die("Produk tidak ditemukan");
    }

    $subtotal = $produk['harga'] * $qty;
    $total = $subtotal;

    $data_checkout[] = [
        'id_produk' => $id_produk,
        'nama_produk' => $produk['nama_produk'],
        'harga' => $produk['harga'],
        'qty' => $qty,
        'foto' => $produk['foto'], 
        'subtotal' => $subtotal
    ];
} else {
    // --- ALUR KERANJANG BELANJA (DARI DATABASE CART) ---
    $query_db_cart = mysqli_query($koneksi, "SELECT 
                                                cart.qty, 
                                                cart.id_produk,
                                                produk.nama_produk, 
                                                produk.harga,
                                                produk.foto
                                             FROM cart 
                                             INNER JOIN produk ON cart.id_produk = produk.id_produk 
                                             WHERE cart.id_user = $id_user");

    // Jika keranjang kosong, kembalikan ke halaman produk
    if (mysqli_num_rows($query_db_cart) == 0) {
        header("Location: produk.php");
        exit;
    }

    // Ambil semua barang yang ada di keranjang database milik user ini
    while ($row = mysqli_fetch_assoc($query_db_cart)) {
        $id_produk = $row['id_produk'];
        $qty = (int)$row['qty'];
        $subtotal = (int)$row['harga'] * $qty;

        $data_checkout[] = [
            'id_produk' => $id_produk,
            'nama_produk' => $row['nama_produk'],
            'harga' => $row['harga'],
            'qty' => $qty,
            'foto' => $row['foto'],
            'subtotal' => $subtotal
        ];

        $total += $subtotal;
    }
}

// 2. PROSES TOMBOL "BUAT PESANAN" DIKLIK
if (isset($_POST['checkout'])) {
    $nama = trim(mysqli_real_escape_string($koneksi, $_POST['nama']));
    $alamat = trim(mysqli_real_escape_string($koneksi, $_POST['alamat']));
    $no_hp = preg_replace('/[^0-9]/', '', $_POST['no_hp']);
    $metode = mysqli_real_escape_string($koneksi, $_POST['metode_pembayaran']);

    // VALIDASI INPUT FORM
    if (strlen($nama) < 3) {
        echo "<script>alert('Nama minimal 3 karakter'); history.back();</script>";
        exit;
    }

    if (!preg_match("/^[a-zA-Z\s\.\,\']+$/", $nama)) {
        echo "<script>alert('Nama tidak valid'); history.back();</script>";
        exit;
    }

    if (!preg_match('/^(08|628)[0-9]{8,13}$/', $no_hp)) {
        echo "<script>alert('Nomor HP tidak valid'); history.back();</script>";
        exit;
    }

    if (strlen($alamat) < 15) {
        echo "<script>alert('Alamat terlalu pendek'); history.back();</script>";
        exit;
    }

    $metode_valid = ['COD', 'Transfer Bank', 'DANA', 'OVO', 'GoPay'];
    if (!in_array($metode, $metode_valid)) {
        echo "<script>alert('Metode pembayaran tidak valid'); history.back();</script>";
        exit;
    }

    // CEK STOK SEBELUM MEMBUAT PESANAN
    foreach ($data_checkout as $item) {
        $id_produk = $item['id_produk'];
        $qty = $item['qty'];

        $cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_produk, stok FROM produk WHERE id_produk='$id_produk'"));

        if (!$cek) {
            echo "<script>alert('Produk tidak ditemukan'); window.location='produk.php';</script>";
            exit;
        }

        if ($cek['stok'] < $qty) {
            echo "<script>alert('Stok {$cek['nama_produk']} tidak mencukupi'); history.back();</script>";
            exit;
        }
    }

    // ALUR LOGIKA COD VS PEMBAYARAN ONLINE
    if ($metode === 'COD') {
        $status_pembayaran = 'belum_bayar'; 
        $batas_bayar_value = "NULL";        
    } else {
        $status_pembayaran = 'belum_bayar'; 
        $waktu_deadline = date('Y-m-d H:i:s', strtotime('+24 hours'));
        $batas_bayar_value = "'$waktu_deadline'";
    }

    // INSERT DATA KE TABEL PESANAN
    $queryPesanan = "INSERT INTO pesanan (
        id_user,
        nama_pembeli,
        alamat,
        no_hp,
        total_bayar,
        metode_pembayaran,
        status_pembayaran,
        batas_bayar
    ) VALUES (
        '$id_user',
        '$nama',
        '$alamat',
        '$no_hp',
        '$total',
        '$metode',
        '$status_pembayaran',
        $batas_bayar_value
    )";

    $insertPesanan = mysqli_query($koneksi, $queryPesanan);

    if (!$insertPesanan) {
        die("Gagal menyimpan pesanan : " . mysqli_error($koneksi));
    }

    $id_pesanan = mysqli_insert_id($koneksi);

    // INPUT DETAIL PESANAN & POTONG STOK
    foreach ($data_checkout as $item) {
        $id_produk = $item['id_produk'];
        $qty = $item['qty'];
        $subtotal = $item['subtotal'];

        mysqli_query(
            $koneksi,
            "INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, subtotal) 
             VALUES ('$id_pesanan', '$id_produk', '$qty', '$subtotal')"
        );

        // Update stok aman
        $update = mysqli_query(
            $koneksi,
            "UPDATE produk SET stok = stok - $qty WHERE id_produk='$id_produk' AND stok >= $qty"
        );

        if (mysqli_affected_rows($koneksi) == 0) {
            die("Stok berubah mendadak atau tidak mencukupi");
        }
    }

    // MENGOSONGKAN KERANJANG / SESSION SETELAH BERHASIL
    if (isset($_SESSION['checkout_langsung'])) {
        unset($_SESSION['checkout_langsung']);
    } else {
        mysqli_query($koneksi, "DELETE FROM cart WHERE id_user = '$id_user'");
    }

    // Redirect langsung ke halaman detail pesanan
    echo "
    <script>
        alert('Pesanan berhasil dibuat!');
        window.location='detail_pesanan.php?id=$id_pesanan';
    </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - NexaTech Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-slate-100">

<?php include '../components/navbar.php'; ?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <form method="POST">
        <div class="grid lg:grid-cols-2 gap-8">

            <div class="bg-white p-6 rounded-3xl shadow-sm h-fit">
                <h2 class="text-2xl font-bold mb-6">Informasi Pengiriman</h2>
                
                <div class="space-y-5">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-slate-700">Nama Lengkap</label>
                        <input type="text" name="nama" value="<?= isset($_SESSION['user']['nama']) ? $_SESSION['user']['nama'] : '' ?>" required minlength="3" maxlength="100" class="w-full border rounded-xl p-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-slate-700">Nomor HP</label>
                        <input type="tel" name="no_hp" value="<?= isset($_SESSION['user']['no_hp']) ? $_SESSION['user']['no_hp'] : '' ?>" required maxlength="15" placeholder="08xxxxxxxxxx" pattern="(08|628)[0-9]{8,13}" class="w-full border rounded-xl p-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-slate-700">Alamat Lengkap</label>
                        <textarea name="alamat" rows="4" required minlength="15" maxlength="500" class="w-full border rounded-xl p-4 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Tulis alamat detail, nama jalan, nomor rumah, RT/RW..."><?= isset($_SESSION['user']['alamat']) ? $_SESSION['user']['alamat'] : '' ?></textarea>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-slate-700">Metode Pembayaran</label>
                        <select name="metode_pembayaran" required class="w-full border rounded-xl p-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Pembayaran --</option>
                            <option value="COD">COD (Bayar di Tempat)</option>
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="DANA">DANA</option>
                            <option value="OVO">OVO</option>
                            <option value="GoPay">GoPay</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm h-fit">
                <h2 class="text-2xl font-bold mb-6">Ringkasan Pesanan</h2>
                
                <div class="space-y-4 max-h-[350px] overflow-y-auto pr-2">
                    <?php foreach ($data_checkout as $item): ?>
                        <div class="flex items-center gap-4 p-3 bg-slate-50 rounded-2xl border border-slate-100">
                            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center overflow-hidden border shrink-0">
                                <img src="../assets/images/<?= !empty($item['foto']) ? $item['foto'] : 'default.jpg' ?>" alt="<?= $item['nama_produk'] ?>" class="w-full h-full object-contain p-1">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-slate-800 text-sm truncate"><?= $item['nama_produk'] ?></h4>
                                <p class="text-xs text-slate-500 mt-1"><?= $item['qty'] ?> x Rp <?= number_format($item['harga'], 0, ',', '.') ?></p>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="font-bold text-blue-600 text-sm">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="border-t border-dashed pt-4 mt-6 space-y-3">
                    <div class="flex justify-between items-center text-slate-600 text-sm">
                        <span>Total Harga Barang</span>
                        <span class="font-semibold text-slate-800">Rp <?= number_format($total, 0, ',', '.') ?></span>
                    </div>
                    <div class="flex justify-between items-center text-slate-600 text-sm">
                        <span>Biaya Ongkos Kirim</span>
                        <span class="text-green-600 font-bold">Gratis Ongkir</span>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-slate-200">
                        <span class="font-bold text-slate-800 text-base">Total Pembayaran</span>
                        <span class="text-xl font-extrabold text-blue-600">Rp <?= number_format($total, 0, ',', '.') ?></span>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" name="checkout" class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold hover:bg-blue-700 transition shadow-md flex items-center justify-center gap-2">
                        <i class="fa-solid fa-bag-shopping"></i> Buat Pesanan Sekarang
                    </button>
                </div>

            </div>

        </div>
    </form>
</div>

</body>
</html>