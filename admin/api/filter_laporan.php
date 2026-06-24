<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filter Laporan Penjualan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Animasi sederhana saat halaman dimuat */
        .fade-in { animation: fadeIn 0.4s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4 text-slate-800">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-lg fade-in">
        
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-100 text-blue-600 rounded-full mb-3 shadow-inner">
                <i class="fas fa-file-invoice text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-800">Cetak Laporan</h2>
            <p class="text-slate-500 text-sm mt-1">Filter data penjualan yang ingin Anda unduh</p>
        </div>

        <form action="cetak_pdf.php" method="GET" target="_blank" class="space-y-5">
            
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Cari Nama Pembeli</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="nama" placeholder="Kosongkan jika ingin semua data" 
                           class="w-full pl-10 pr-3 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Status Pesanan</label>
                <select name="status" class="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm outline-none bg-white">
                    <option value="">-- Semua Status --</option>
                    <option value="diproses">Diproses</option>
                    <option value="dikirim">Dikirim</option>
                    <option value="selesai">Selesai</option>
                </select>
            </div>

            <hr class="border-slate-200">

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Rentang Tanggal</label>
                
                <div class="flex gap-2 mb-3">
                    <button type="button" onclick="setRentangTanggal(1)" class="flex-1 py-1.5 text-xs font-semibold bg-slate-100 text-slate-600 rounded-lg hover:bg-blue-100 hover:text-blue-600 transition border border-slate-200">1 Bulan</button>
                    <button type="button" onclick="setRentangTanggal(6)" class="flex-1 py-1.5 text-xs font-semibold bg-slate-100 text-slate-600 rounded-lg hover:bg-blue-100 hover:text-blue-600 transition border border-slate-200">6 Bulan</button>
                    <button type="button" onclick="setRentangTanggal(12)" class="flex-1 py-1.5 text-xs font-semibold bg-slate-100 text-slate-600 rounded-lg hover:bg-blue-100 hover:text-blue-600 transition border border-slate-200">1 Tahun</button>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-slate-500 mb-1">Dari Tanggal</label>
                        <input type="date" name="tgl_mulai" id="tgl_mulai" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-500 mb-1">Sampai Tanggal</label>
                        <input type="date" name="tgl_akhir" id="tgl_akhir" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition outline-none text-sm">
                    </div>
                </div>
            </div>

            <div class="pt-4 grid grid-cols-2 gap-4">
                <button type="submit" formaction="cetak_pdf.php" target="_blank" 
                        class="w-full py-3 px-4 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl shadow-lg shadow-red-500/30 transition flex justify-center items-center gap-2">
                    <i class="fas fa-file-pdf"></i> Cetak PDF
                </button>

                <button type="submit" formaction="cetak_excel.php" 
                        class="w-full py-3 px-4 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/30 transition flex justify-center items-center gap-2">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
            </div>
            
            <div class="text-center mt-4">
                <a href="javascript:history.back()" class="text-sm font-medium text-slate-500 hover:text-slate-800 transition underline-offset-4 hover:underline">
                    Kembali ke Dashboard
                </a>
            </div>

        </form>
    </div>

    <script>
        function formatTanggal(date) {
            let d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;

            return [year, month, day].join('-');
        }

        function setRentangTanggal(bulanMundur) {
            let endDate = new Date(); // Hari ini
            let startDate = new Date(); 
            
            // Set tanggal mulai mundur berdasarkan jumlah bulan
            startDate.setMonth(startDate.getMonth() - bulanMundur);

            // Masukkan ke dalam input form
            document.getElementById('tgl_mulai').value = formatTanggal(startDate);
            document.getElementById('tgl_akhir').value = formatTanggal(endDate);
        }
    </script>

</body>
</html>