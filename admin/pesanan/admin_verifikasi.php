<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../../config/koneksi.php';


if (!isset($_SESSION['admin'])) {
    header("Location: ../../auth/login_admin.php"); 
    exit;
}


$query = mysqli_query($koneksi, "SELECT * FROM pesanan WHERE status_pembayaran = 'menunggu_verifikasi' ORDER BY tanggal_pesan DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Verifikasi Pembayaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-slate-100 p-8">

    <div class="max-w-4xl mx-auto bg-white p-8 rounded-[25px] shadow-sm">
        
        <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-100">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Konfirmasi Pembayaran Masuk</h1>
                <p class="text-sm text-slate-500">Daftar pesanan pelanggan yang menunggu divalidasi uangnya.</p>
            </div>
            <a href="../dashboard.php" class="bg-slate-800 text-white text-sm px-4 py-2 rounded-xl hover:bg-slate-700 transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left text-xs"></i> Dashboard
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-400 text-sm font-semibold">
                        <th class="pb-3">ID Pesanan</th>
                        <th class="pb-3">Pelanggan</th>
                        <th class="pb-3">Metode</th>
                        <th class="pb-3">Total Bayar</th>
                        <th class="pb-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php if (mysqli_num_rows($query) == 0): ?>
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 font-medium">
                                <i class="fa-regular fa-circle-check text-2xl text-emerald-400 block mb-2"></i>
                                Tidak ada pembayaran yang perlu diverifikasi saat ini.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php while($row = mysqli_fetch_assoc($query)): ?>
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 font-medium text-slate-600">#TRX-<?= $row['id_pesanan'] ?></td>
                            <td class="py-4 font-semibold text-slate-800"><?= htmlspecialchars($row['nama_pembeli']) ?></td>
                            <td class="py-4">
                                <span class="bg-blue-50 text-blue-600 px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wider">
                                    <?= htmlspecialchars($row['metode_pembayaran']) ?>
                                </span>
                            </td>
                            <td class="py-4 font-bold text-emerald-600">Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?></td>
                            <td class="py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="detail.php?id=<?= $row['id_pesanan'] ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs px-3 py-2 rounded-lg font-medium transition">
                                        <i class="fa-solid fa-eye"></i> Detail
                                    </a>
                                    
                                    <form action="proses_verifikasi.php" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin uang kiriman pelanggan ini sudah benar-benar masuk?')">
                                        <input type="hidden" name="id_pesanan" value="<?= $row['id_pesanan'] ?>">
                                        <button type="submit" name="aksi" value="terima" class="bg-emerald-500 hover:bg-emerald-600 text-white text-xs px-3 py-2 rounded-lg font-medium shadow-sm transition">
                                            <i class="fa-solid fa-check"></i> Terima
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>