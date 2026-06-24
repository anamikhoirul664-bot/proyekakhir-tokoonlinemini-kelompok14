<?php
include '../config/koneksi.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login_admin.php");
    exit;
}

/* ================= DATA ================= */
$total_produk = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM produk"));
$total_kategori = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM kategori"));
$total_user = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM users"));
$total_pesanan = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM pesanan"));

$pendapatan = mysqli_fetch_assoc(mysqli_query(
    $koneksi,
    "SELECT SUM(total_bayar) as total FROM pesanan WHERE status_pembayaran='dibayar'"
));

$produk_habis = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM produk WHERE stok <= 0"));

/* CHART DATA */
$chart = mysqli_query($koneksi,"
SELECT 
    MONTH(tanggal_pesan) AS bulan,
    SUM(total_bayar) AS total,
    COUNT(id_pesanan) AS jumlah
FROM pesanan
WHERE status_pembayaran='dibayar'
GROUP BY MONTH(tanggal_pesan)
ORDER BY bulan ASC
");

$bulan = [];
$penjualan = [];
$jumlah = [];

while($c = mysqli_fetch_assoc($chart)){
    $bulan[] = $c['bulan'];
    $penjualan[] = $c['total'];
    $jumlah[] = $c['jumlah'];
}

/* PESANAN TERBARU */
$pesanan = mysqli_query($koneksi,"
SELECT * FROM pesanan ORDER BY tanggal_pesan DESC LIMIT 6
");

/* STATUS STYLE */
function badge($status){
    return match($status){
        'pending' => 'bg-yellow-100 text-yellow-700',
        'diproses' => 'bg-blue-100 text-blue-700',
        'dikirim' => 'bg-purple-100 text-purple-700',
        'selesai' => 'bg-green-100 text-green-700',
        default => 'bg-gray-100 text-gray-700'
    };
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Pro Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-slate-100 flex">

<!-- TOAST -->
<div id="toast" class="hidden fixed top-5 right-5 bg-green-500 text-white px-5 py-3 rounded-xl shadow-xl">
    Dashboard Loaded 🚀
</div>

<!-- SIDEBAR -->
<aside class="w-72 bg-slate-900 text-white min-h-screen p-6 flex flex-col">

    <!-- BRAND -->
    <h1 class="text-2xl font-bold mb-8">
        NexaTech <span class="text-blue-400">Store</span>
    </h1>

    <!-- NAV -->
    <nav class="space-y-2 flex-1">

        <!-- Dashboard -->
        <a class="flex items-center gap-3 bg-blue-600 px-4 py-3 rounded-xl">
            🏠 Dashboard
        </a>

        <a href="produk/index.php"
           class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 transition">
            📦 Produk
        </a>

        <a href="kategori/index.php"
           class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 transition">
            🏷️ Kategori
        </a>

        <a href="pesanan/index.php"
           class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 transition">
            🧾 Pesanan
        </a>

    </nav>

    <!-- BOTTOM ACTIONS -->
    <div class="space-y-2 mt-6">

        <!-- Kembali ke Toko -->
        <a href="../index.php"
           class="flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 transition px-4 py-3 rounded-xl font-semibold">
            🏪 Kembali ke Toko
        </a>

        <!-- Logout -->
        <a href="../auth/logout.php"
           class="flex items-center justify-center gap-2 bg-red-500 hover:bg-red-600 transition px-4 py-3 rounded-xl font-semibold">
            🚪 Logout
        </a>

    </div>

</aside>

<main class="flex-1 p-6">

<div class="flex justify-between items-center mb-6 bg-white p-4 rounded-2xl shadow">

    <div>
        <h2 class="text-2xl font-bold text-gray-800">
            Halo, <?= $_SESSION['admin']['nama']; ?> 👋
        </h2>
        <p class="text-gray-500 text-sm">
            Ringkasan performa toko
        </p>
    </div>

    <div class="flex items-center gap-4">

        <div id="clock" class="bg-gray-100 px-4 py-2 rounded-xl text-sm font-medium shadow-sm text-gray-700"></div>

        <a href="api/filter_laporan.php"
           class="bg-blue-600 hover:bg-blue-700 transition text-white px-4 py-2 rounded-xl text-sm font-semibold shadow flex items-center gap-2">
            <i class="fas fa-print"></i>
            <span>Cetak Laporan</span>
        </a>

        <div class="relative cursor-pointer p-2 hover:bg-slate-50 rounded-xl transition" id="btn-notif">
            <i class="fas fa-bell text-xl text-slate-600"></i>
            
            <span id="badge-count" class="hidden absolute top-0 right-0 bg-red-500 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center border-2 border-white">
                0
            </span>
            
            <div id="box-notif" class="hidden absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden z-50 cursor-default">
                <div class="p-4 bg-slate-50 font-bold text-sm text-slate-700 border-b text-left">
                    Pesanan Masuk
                </div>
                <div id="list-notif" class="divide-y divide-slate-100 max-h-60 overflow-y-auto text-left">
                </div>
            </div>
        </div>

    </div>

</div>

<!-- KPI CARDS (REALTIME VERSION) -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6">

    <!-- Produk -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-700 text-white p-6 rounded-2xl shadow-lg hover:scale-[1.02] transition">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm opacity-80">Produk</p>
                <h1 class="text-3xl font-bold counter" data-target="<?= $total_produk ?>">0</h1>
            </div>
            <div class="text-3xl">📦</div>
        </div>
    </div>

    <!-- Pesanan -->
    <div class="bg-gradient-to-br from-purple-500 to-purple-700 text-white p-6 rounded-2xl shadow-lg hover:scale-[1.02] transition">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm opacity-80">Pesanan</p>
                <h1 class="text-3xl font-bold counter" data-target="<?= $total_pesanan ?>">0</h1>
            </div>
            <div class="text-3xl">🧾</div>
        </div>
    </div>

    <!-- Pelanggan -->
    <div class="bg-gradient-to-br from-pink-500 to-pink-700 text-white p-6 rounded-2xl shadow-lg hover:scale-[1.02] transition">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm opacity-80">Pelanggan</p>
                <h1 class="text-3xl font-bold counter" data-target="<?= $total_user ?>">0</h1>
            </div>
            <div class="text-3xl">👤</div>
        </div>
    </div>

    <!-- Pendapatan -->
    <div class="bg-gradient-to-br from-green-500 to-green-700 text-white p-6 rounded-2xl shadow-lg hover:scale-[1.02] transition">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm opacity-80">Pendapatan</p>
                <h1 class="text-2xl font-bold">
                    Rp <span class="counter" data-target="<?= $pendapatan['total'] ?? 0 ?>">0</span>
                </h1>
            </div>
            <div class="text-3xl">💰</div>
        </div>
    </div>

</div>

<!-- JS Animasi Counter -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    const counters = document.querySelectorAll(".counter");

    counters.forEach(counter => {
        const updateCount = () => {
            const target = +counter.getAttribute("data-target");
            let count = +counter.innerText.replace(/\D/g, '') || 0;

            const speed = 50; // semakin kecil semakin cepat
            const increment = Math.ceil(target / speed);

            if (count < target) {
                counter.innerText = count + increment;
                setTimeout(updateCount, 30);
            } else {
                counter.innerText = target.toLocaleString('id-ID');
            }
        };

        updateCount();
    });
});
</script>

<!-- CHART -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

    <div class="bg-white p-6 rounded-2xl shadow-lg">
        <h3 class="font-bold mb-4">📈 Grafik Penjualan</h3>
        <canvas id="chart1"></canvas>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-lg">
        <h3 class="font-bold mb-4">📊 Grafik Pesanan</h3>
        <canvas id="chart2"></canvas>
    </div>

</div>

<!-- CONTENT -->
<div class="grid grid-cols-2 gap-5 mt-6">

<!-- PESANAN -->
<div class="bg-white p-5 rounded-2xl shadow">

<h3 class="font-bold mb-3">Pesanan Terbaru</h3>

<?php while($p = mysqli_fetch_assoc($pesanan)): ?>

<div class="flex justify-between border-b py-2">

<div>
    <p class="font-semibold">#<?= $p['id_pesanan'] ?></p>
    <small><?= $p['nama_pembeli'] ?></small>
</div>

<span class="px-3 py-1 rounded-xl text-sm <?= badge($p['status_pesanan']) ?>">
    <?= $p['status_pesanan'] ?>
</span>

</div>

<?php endwhile; ?>

</div>

<!-- QUICK ACTION -->
<div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-2xl transition">

    <h3 class="font-bold text-lg mb-5 flex items-center gap-2">
        ⚡ Quick Action
    </h3>

    <div class="grid gap-3">

        <a href="produk/tambah.php"
           class="flex items-center justify-between bg-blue-500 text-white p-4 rounded-xl hover:bg-blue-600 transition group">

            <div class="flex items-center gap-3">
                <span class="text-xl">➕</span>
                <span class="font-semibold">Tambah Produk</span>
            </div>

            <span class="group-hover:translate-x-1 transition">→</span>
        </a>

        <a href="kategori/index.php"
           class="flex items-center justify-between bg-green-500 text-white p-4 rounded-xl hover:bg-green-600 transition group">

            <div class="flex items-center gap-3">
                <span class="text-xl">🏷️</span>
                <span class="font-semibold">Tambah Kategori</span>
            </div>

            <span class="group-hover:translate-x-1 transition">→</span>
        </a>

        <a href="pesanan/index.php"
           class="flex items-center justify-between bg-purple-500 text-white p-4 rounded-xl hover:bg-purple-600 transition group">

            <div class="flex items-center gap-3">
                <span class="text-xl">📦</span>
                <span class="font-semibold">Lihat Pesanan</span>
            </div>

            <span class="group-hover:translate-x-1 transition">→</span>
        </a>

    </div>

</div>

</div>

</main>

<!-- SCRIPT FINAL CLEAN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
/* ================= CLOCK ================= */
setInterval(() => {
    document.getElementById("clock").innerHTML =
        new Date().toLocaleTimeString("id-ID");
}, 1000);

/* ================= TOAST ================= */
setTimeout(() => {
    const toast = document.getElementById("toast");
    if (toast) {
        toast.classList.remove("hidden");
        setTimeout(() => toast.remove(), 3000);
    }
}, 500);



/* ================= CHART INIT ================= */
const chartPenjualan = new Chart(document.getElementById("chart1"), {
    type: "line",
    data: {
        labels: [],
        datasets: [{
            label: "Penjualan",
            data: [],
            borderWidth: 3,
            tension: 0.4
        }]
    }
});

const chartOrder = new Chart(document.getElementById("chart2"), {
    type: "bar",
    data: {
        labels: [],
        datasets: [{
            label: "Jumlah Order",
            data: []
        }]
    }
});

/* ================= UPDATE CHART ================= */
function updateChart() {
    fetch('api/chart_data.php')
        .then(res => res.json())
        .then(data => {

            const bulan = data.bulan.map(b => "Bulan " + b);

            chartPenjualan.data.labels = bulan;
            chartPenjualan.data.datasets[0].data = data.penjualan;
            chartPenjualan.update();

            chartOrder.data.labels = bulan;
            chartOrder.data.datasets[0].data = data.jumlah;
            chartOrder.update();

        })
        .catch(err => console.log("Chart error:", err));
}

updateChart();
setInterval(updateChart, 3000);


</script>

<script>
function cekNotifikasi() {
    // Memanggil folder 'api/notif.php' karena posisinya sejajar dengan dasbor.php
    fetch('api/notif_pesanan.php') 
        .then(response => {
            if (!response.ok) {
                throw new Error("Gagal merespon dengan kode: " + response.status);
            }
            return response.json();
        })
        .then(data => {
            const badge = document.getElementById('badge-count');
            const list = document.getElementById('list-notif');
            
            if(data.total_notif > 0) {
                // Munculkan angka merah notifikasi di lonceng
                badge.innerText = data.total_notif;
                badge.classList.remove('hidden');
                
                let htmlContent = '';
                data.pesanan_baru.forEach(order => {
                    let labelStatus = '';
                    
                    // 1. Cek jika metode COD
                    if (order.metode_pembayaran === 'COD') {
                        labelStatus = `<p class="text-xs text-blue-600 font-semibold mt-0.5">📦 Pesanan COD Baru: ${order.total_format}</p>`;
                    } 
                    // 2. Cek jika metode Transfer tapi status pembayarannya masih 'belum_dibayar' atau 'pending'
                    else if (order.status_pembayaran === 'belum_dibayar' || order.status_pembayaran === 'pending') {
                        labelStatus = `<p class="text-xs text-red-500 font-semibold mt-0.5">⏳ Menunggu Pembayaran: ${order.total_format}</p>`;
                    } 
                    // 3. Jika sudah bayar dan butuh konfirmasi admin (menunggu_verifikasi)
                    else {
                        labelStatus = `<p class="text-xs text-amber-600 font-semibold mt-0.5">⚠️ Menunggu Verifikasi: ${order.total_format}</p>`;
                    }

                    // Susun daftar list pesanan (arahkan link ke folder pesanan/detail.php)
                    htmlContent += `
                        <a href="pesanan/detail.php?id=${order.id_pesanan}" class="block p-4 hover:bg-blue-50/50 transition border-b border-slate-50 last:border-none">
                            <div class="flex justify-between items-center">
                                <p class="text-sm font-bold text-slate-800">${order.nama_pembeli}</p>
                                <p class="text-[10px] text-slate-400 font-medium">${order.waktu_format} WIB</p>
                            </div>
                            ${labelStatus}
                        </a>
                    `;
                });
                
                list.innerHTML = htmlContent;
            } else {
                // Sembunyikan angka merah jika tidak ada antrean pesanan baru
                badge.classList.add('hidden');
                list.innerHTML = '<p class="text-center py-6 text-xs text-slate-400 font-medium">Tidak ada pesanan baru</p>';
            }
        })
        .catch(err => console.error("Sistem radar notifikasi eror:", err));
}

// Eksekusi radar notifikasi pertama kali saat halaman dibuka
cekNotifikasi();

// Otomatis melakukan sinkronisasi ulang ke database setiap 10 detik tanpa reload halaman
setInterval(cekNotifikasi, 10000);

// Logika Toggle Buka-Tutup Dropdown Lonceng
const btnNotif = document.getElementById('btn-notif');
const boxNotif = document.getElementById('box-notif');

if(btnNotif && boxNotif) {
    btnNotif.addEventListener('click', (e) => {
        e.stopPropagation();
        boxNotif.classList.toggle('hidden');
    });

    document.addEventListener('click', () => {
        boxNotif.classList.add('hidden');
    });

    boxNotif.addEventListener('click', (e) => {
        e.stopPropagation();
    });
}
</script>

</body>
</html>