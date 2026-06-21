<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../config/koneksi.php';

// Proteksi halaman user
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['user']['id_user'];
$id_pesanan = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data pesanan untuk memastikan ini memang pesanan milik user yang login
$pesanan = mysqli_fetch_assoc(
    mysqli_query(
        $koneksi,
        "SELECT * FROM pesanan 
        WHERE id_pesanan='$id_pesanan' 
        AND id_user='$id_user'"
    )
);

// Validasi akses pesanan
if (!$pesanan) {
    echo "<script>alert('Pesanan tidak ditemukan'); window.location='pesanan_saya.php';</script>";
    exit;
}

// Jika metodenya COD, tidak perlu masuk halaman ini
if ($pesanan['metode_pembayaran'] === 'COD') {
    echo "<script>alert('Pesanan COD dibayar saat barang sampai!'); window.location='detail_pesanan.php?id=$id_pesanan';</script>";
    exit;
}

// Jika sudah lunas atau sedang menunggu verifikasi, tidak perlu upload lagi
if ($pesanan['status_pembayaran'] !== 'belum_bayar') {
    echo "<script>alert('Pesanan ini sudah dibayar atau sedang dalam verifikasi'); window.location='detail_pesanan.php?id=$id_pesanan';</script>";
    exit;
}

// PROSES SUBMIT BUKTI PEMBAYARAN
if (isset($_POST['kirim_bukti'])) {
    $nama_file = $_FILES['bukti']['name'];
    $ukuran_file = $_FILES['bukti']['size'];
    $error_file = $_FILES['bukti']['error'];
    $tmp_file = $_FILES['bukti']['tmp_name'];

    // 1. Cek apakah ada file yang diupload
    if ($error_file === 4) {
        echo "<script>alert('Silakan pilih file bukti pembayaran terlebih dahulu!'); history.back();</script>";
        exit;
    }

    // 2. Validasi ekstensi/format file (Hanya gambar)
    $ekstensi_valid = ['jpg', 'jpeg', 'png'];
    $ekstensi_diupload = explode('.', $nama_file);
    $ekstensi_diupload = strtolower(end($ekstensi_diupload));

    if (!in_array($ekstensi_diupload, $ekstensi_valid)) {
        echo "<script>alert('Format file harus JPG, JPEG, atau PNG!'); history.back();</script>";
        exit;
    }

    // 3. Validasi ukuran file (Maksimal 2MB = 2.048.000 bytes)
    if ($ukuran_file > 2048000) {
        echo "<script>alert('Ukuran file terlalu besar! Maksimal 2MB'); history.back();</script>";
        exit;
    }

    // 4. Generate nama file baru agar unik dan tidak tabrakan di server
    $nama_file_baru = "BUKTI_" . $id_pesanan . "_" . time() . "." . $ekstensi_diupload;

    // 5. Tentukan folder tujuan penyimpanan (Pastikan folder ini sudah kamu buat)
    // Jalur folder: assets/images/bukti_bayar/
    $folder_tujuan = "../assets/images/bukti_bayar/";
    
    // Buat folder otomatis jika belum ada di dalam server kamu
    if (!is_dir($folder_tujuan)) {
        mkdir($folder_tujuan, 0755, true);
    }

    // 6. Pindahkan file gambar dari komputer user ke server kita
    if (move_uploaded_file($tmp_file, $folder_tujuan . $nama_file_baru)) {
        
        // Update nama file bukti dan naikkan status pembayaran menjadi 'menunggu_verifikasi'
        $update = mysqli_query(
            $koneksi,
            "UPDATE pesanan SET 
            bukti_pembayaran = '$nama_file_baru',
            status_pembayaran = 'menunggu_verifikasi'
            WHERE id_pesanan = '$id_pesanan'"
        );

        if ($update) {
            echo "<script>alert('Bukti pembayaran berhasil dikirim! Mohon tunggu verifikasi admin.'); window.location='detail_pesanan.php?id=$id_pesanan';</script>";
            exit;
        } else {
            echo "Gagal mengupdate database: " . mysqli_error($koneksi);
        }
    } else {
        echo "<script>alert('Gagal mengunggah file gambar ke server.'); history.back();</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Pesanan #<?= $id_pesanan ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-slate-100">

<?php include '../components/navbar.php'; ?>

<div class="max-w-3xl mx-auto px-4 py-8">
    
    <div class="bg-white p-6 rounded-3xl shadow-sm mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Konfirmasi Pembayaran</h1>
            <p class="text-sm text-slate-500 mt-1">Transaksi #TRX-<?= $pesanan['id_pesanan'] ?></p>
        </div>
        <a href="detail_pesanan.php?id=<?= $id_pesanan ?>" class="text-sm font-semibold text-slate-600 hover:text-slate-800">
            ← Detail Pesanan
        </a>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        
        <div class="md:col-span-2 space-y-6">
            
            <div class="bg-blue-600 text-white p-6 rounded-3xl shadow-sm">
                <p class="text-blue-100 text-sm">Total Tagihan Yang Harus Dibayar</p>
                <h2 class="text-3xl font-extrabold mt-1">Rp <?= number_format($pesanan['total_bayar'], 0, ',', '.') ?></h2>
                <div class="mt-4 pt-4 border-t border-blue-500/50 text-xs text-blue-200 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info"></i> Pastikan nominal transfer pas hingga digit terakhir.
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm">
                <h3 class="font-bold text-lg text-slate-800 mb-4">Informasi Rekening Tujuan</h3>
                
                <?php if ($pesanan['metode_pembayaran'] == 'Transfer Bank'): ?>
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center gap-4">
                        <div class="bg-blue-100 text-blue-600 p-3 rounded-xl font-bold text-sm tracking-wider">BANK BCA</div>
                        <div>
                            <p class="text-xs text-slate-400">Nomor Rekening</p>
                            <p class="font-bold text-slate-800 text-lg tracking-wide">1234-5678-90</p>
                            <p class="text-xs text-slate-500 mt-0.5">a.n PT Toko Online Sukses</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center gap-4">
                        <div class="bg-amber-100 text-amber-700 px-4 py-3 rounded-xl font-extrabold text-sm tracking-wide">
                            <?= strtoupper($pesanan['metode_pembayaran']) ?>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400">Nomor Akun / HP</p>
                            <p class="font-bold text-slate-800 text-lg tracking-wide">0812-3456-7890</p>
                            <p class="text-xs text-slate-500 mt-0.5">a.n Toko Online E-Commerce</p>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mt-6 space-y-2 text-sm text-slate-600">
                    <p class="font-semibold text-slate-700">Langkah-langkah:</p>
                    <p>1. Lakukan transfer sesuai metode pilihan di atas.</p>
                    <p>2. Simpan struk digital / foto struk fisik bukti transfer Anda.</p>
                    <p>3. Upload foto tersebut pada form di sebelah kanan.</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-sm h-fit">
            <h3 class="font-bold text-lg text-slate-800 mb-4">Kirim Bukti</h3>
            
            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                
                <div class="border-2 border-dashed border-slate-200 rounded-2xl p-4 text-center hover:border-blue-400 transition cursor-pointer relative bg-slate-50">
                    <input type="file" name="bukti" accept="image/*" required 
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" id="buktiImg">
                    
                    <div id="preview-placeholder">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-400 mb-2"></i>
                        <p class="text-xs font-semibold text-slate-600">Pilih Foto Struk</p>
                        <p class="text-[10px] text-slate-400 mt-1">Format: JPG, JPEG, PNG (Maks 2MB)</p>
                    </div>

                    <img id="preview-image" class="hidden w-full max-h-40 object-contain rounded-xl mx-auto" />
                </div>

                <button type="submit" name="kirim_bukti" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 rounded-xl text-sm transition shadow-md shadow-emerald-100">
                    <i class="fa-solid fa-paper-plane mr-1"></i> Kirim Bukti Transfer
                </button>
            </form>
        </div>

    </div>
</div>

<script>
document.getElementById('buktiImg').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-placeholder').classList.add('hidden');
            const imgElement = document.getElementById('preview-image');
            imgElement.src = e.target.result;
            imgElement.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    }
});
</script>

</body>
</html>