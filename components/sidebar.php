<aside class="w-64 bg-slate-900 min-h-screen text-white p-6 sticky top-0 hidden lg:block">
    <div class="mb-10">
        <h1 class="text-2xl font-bold text-blue-400 tracking-tight">AdminPanel</h1>
        <p class="text-[10px] text-gray-500 uppercase tracking-widest mt-1">Management System</p>
    </div>

    <nav class="space-y-2">
        <p class="text-[10px] text-gray-600 font-bold uppercase mb-2 ml-2">Utama</p>
        <a href="../dashboard.php" class="flex items-center py-3 px-4 rounded-lg hover:bg-gray-800 transition group">
            <i class="fas fa-home mr-3 text-gray-500 group-hover:text-blue-400"></i> 
            <span>Dashboard</span>
        </a>

        <p class="text-[10px] text-gray-600 font-bold uppercase mb-2 mt-6 ml-2">Katalog</p>
        <a href="../produk/index.php" class="flex items-center py-3 px-4 rounded-lg hover:bg-gray-800 transition group">
            <i class="fas fa-box mr-3 text-gray-500 group-hover:text-blue-400"></i> 
            <span>Semua Produk</span>
        </a>
        <a href="../kategori/index.php" class="flex items-center py-3 px-4 rounded-lg hover:bg-gray-800 transition group">
            <i class="fas fa-tags mr-3 text-gray-500 group-hover:text-blue-400"></i> 
            <span>Kategori</span>
        </a>

        <p class="text-[10px] text-gray-600 font-bold uppercase mb-2 mt-6 ml-2">Transaksi</p>
        <a href="../pesanan/index.php" class="flex items-center py-3 px-4 rounded-lg hover:bg-gray-800 transition group">
            <i class="fas fa-shopping-bag mr-3 text-gray-500 group-hover:text-blue-400"></i> 
            <span>Daftar Pesanan</span>
        </a>

        <div class="pt-10 border-t border-gray-800 mt-10">
            <a href="../../auth/logout.php" class="flex items-center py-3 px-4 rounded-lg bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition">
                <i class="fas fa-sign-out-alt mr-3"></i> 
                <span>Logout</span>
            </a>
        </div>
    </nav>
</aside>