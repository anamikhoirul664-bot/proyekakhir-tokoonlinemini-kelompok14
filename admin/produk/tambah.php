<?php
include '../../config/koneksi.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../../auth/login_admin.php");
    exit;
}

if(isset($_POST['simpan'])){

    $id_kategori = $_POST['id_kategori'];
    $brand = trim(htmlspecialchars($_POST['brand']));
    $nama = trim(htmlspecialchars($_POST['nama_produk']));
    $harga = (int) $_POST['harga'];
    $kondisi = $_POST['kondisi'];
    $garansi = trim(htmlspecialchars($_POST['garansi']));
    $berat = (int) $_POST['berat'];
    $stok = (int) $_POST['stok'];
    $status_produk = $_POST['status_produk'];

    $deskripsi = trim(htmlspecialchars($_POST['deskripsi']));
    $spesifikasi = trim(htmlspecialchars($_POST['spesifikasi']));

    // VALIDASI TEKS
    if(strlen($nama) < 3){
        $error = "Nama produk minimal 3 karakter!";
    }
    elseif(empty($brand)){
        $error = "Brand wajib diisi!";
    }
    elseif($harga <= 0){
        $error = "Harga tidak boleh kosong atau minus!";
    }
    elseif($stok < 0){
        $error = "Stok tidak boleh minus!";
    }
    elseif($berat <= 0){
        $error = "Berat produk harus diisi!";
    }
    else{
        // --- 1. PROSES UPLOAD FOTO ---
        $foto = $_FILES['foto']['name'];
        $tmp = $_FILES['foto']['tmp_name'];
        $size = $_FILES['foto']['size'];

        $allowed_foto = ['jpg','jpeg','png','webp'];
        $ext_foto = strtolower(pathinfo($foto, PATHINFO_EXTENSION));

        // --- 2. PROSES UPLOAD PDF (PANDUAN) ---
        $namaPDF = null; // Default jika pdf tidak diisi oleh admin
        $upload_pdf_sukses = true;

        if(isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] === 0) {
            $pdf_name = $_FILES['file_pdf']['name'];
            $pdf_tmp  = $_FILES['file_pdf']['tmp_name'];
            $pdf_size = $_FILES['file_pdf']['size'];
            $ext_pdf  = strtolower(pathinfo($pdf_name, PATHINFO_EXTENSION));

            if($ext_pdf !== 'pdf'){
                $error = "Format file panduan harus berupa PDF!";
                $upload_pdf_sukses = false;
            }
            elseif($pdf_size > 5000000){ // Batas 5MB
                $error = "Ukuran file PDF maksimal 5MB!";
                $upload_pdf_sukses = false;
            }
            else{
                // Generate nama unik untuk PDF
                $namaPDF = time().'_manual_'.rand(100,999).'.pdf';
                $path_pdf = "../../assets/pdf/".$namaPDF;
                
                // Pastikan folder assets/pdf sudah kamu buat terlebih dahulu
                if(!move_uploaded_file($pdf_tmp, $path_pdf)){
                    $error = "Gagal mengunggah file PDF Panduan!";
                    $upload_pdf_sukses = false;
                }
            }
        }

        // Lanjutkan jika tidak ada error pada proses PDF
        if($upload_pdf_sukses) {
            if(!in_array($ext_foto, $allowed_foto)){
                $error = "Format gambar harus JPG, JPEG, PNG, atau WEBP!";
            }
            elseif($size > 5000000){
                $error = "Ukuran gambar maksimal 5MB!";
            }
            else{
                $namaFoto = time().'_'.rand(100,999).'.'.$ext_foto;
                $path_foto = "../../assets/images/".$namaFoto;

                if(move_uploaded_file($tmp, $path_foto)){

                    // Masukkan kolom file_pdf ke dalam query INSERT
                    $query = mysqli_query(
                        $koneksi,
                        "INSERT INTO produk(
                            id_kategori,
                            brand,
                            nama_produk,
                            harga,
                            kondisi,
                            garansi,
                            berat,
                            stok,
                            status_produk,
                            deskripsi,
                            spesifikasi,
                            foto,
                            file_pdf
                        )
                        VALUES(
                            '$id_kategori',
                            '$brand',
                            '$nama',
                            '$harga',
                            '$kondisi',
                            '$garansi',
                            '$berat',
                            '$stok',
                            '$status_produk',
                            '$deskripsi',
                            '$spesifikasi',
                            '$namaFoto',
                            " . ($namaPDF ? "'$namaPDF'" : "NULL") . "
                        )"
                    );

                    if($query){
                        echo "
                        <script>
                            alert('Produk berhasil ditambahkan!');
                            window.location='index.php';
                        </script>
                        ";
                        exit;
                    }
                }else{
                    $error = "Upload gambar gagal!";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Produk</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="bg-slate-100 p-6">

<div class="max-w-5xl mx-auto">

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-4xl font-bold text-slate-800">Tambah Produk</h1>
            <p class="text-slate-500">Tambahkan produk elektronik baru beserta dokumen panduan</p>
        </div>
        <a href="index.php" class="bg-slate-700 text-white px-5 py-3 rounded-2xl hover:bg-slate-800 transition">
            ← Kembali
        </a>
    </div>

    <div class="bg-white rounded-[30px] shadow-xl p-8">

        <?php if(isset($error)): ?>
        <div class="bg-red-100 text-red-600 p-4 rounded-2xl mb-5">
            <?= $error ?>
        </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-6">

            <div>
                <label class="font-semibold block mb-2">Nama Produk</label>
                <input type="text" name="nama_produk" required class="w-full border rounded-2xl px-5 py-4" placeholder="Contoh: Samsung Galaxy S24 Ultra">
            </div>

            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label class="font-semibold block mb-2">Kategori</label>
                    <select name="id_kategori" class="w-full border rounded-2xl px-5 py-4">
                    <?php
                    $kat = mysqli_query($koneksi, "SELECT * FROM kategori");
                    while($k = mysqli_fetch_assoc($kat)):
                    ?>
                    <option value="<?= $k['id_kategori'] ?>"><?= $k['nama_kategori'] ?></option>
                    <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label class="font-semibold block mb-2">Brand</label>
                    <input type="text" name="brand" required class="w-full border rounded-2xl px-5 py-4" placeholder="Samsung / ASUS / Logitech">
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label class="font-semibold block mb-2">Harga</label>
                    <input type="number" min="1" name="harga" required class="w-full border rounded-2xl px-5 py-4">
                </div>

                <div>
                    <label class="font-semibold block mb-2">Stok</label>
                    <input type="number" min="0" name="stok" required class="w-full border rounded-2xl px-5 py-4">
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-5">
                <div>
                    <label class="font-semibold block mb-2">Kondisi</label>
                    <select name="kondisi" class="w-full border rounded-2xl px-5 py-4">
                        <option value="Baru">Baru</option>
                        <option value="Bekas">Bekas</option>
                    </select>
                </div>

                <div>
                    <label class="font-semibold block mb-2">Garansi</label>
                    <input type="text" name="garansi" class="w-full border rounded-2xl px-5 py-4" placeholder="Garansi Resmi 1 Tahun">
                </div>

                <div>
                    <label class="font-semibold block mb-2">Berat (gram)</label>
                    <input type="number" min="1" name="berat" required class="w-full border rounded-2xl px-5 py-4">
                </div>
            </div>

            <div>
                <label class="font-semibold block mb-2">Status Produk</label>
                <select name="status_produk" class="w-full border rounded-2xl px-5 py-4">
                    <option value="tersedia">Tersedia</option>
                    <option value="habis">Habis</option>
                </select>
            </div>

            <div>
                <label class="font-semibold block mb-2">Deskripsi</label>
                <textarea name="deskripsi" rows="4" class="w-full border rounded-2xl px-5 py-4"></textarea>
            </div>

            <div>
                <label class="font-semibold block mb-2">Spesifikasi Produk</label>
                <textarea name="spesifikasi" rows="6" class="w-full border rounded-2xl px-5 py-4" placeholder="RAM: 12GB&#10;Storage: 256GB&#10;Chipset: Snapdragon"></textarea>
            </div>

            <div>
                <label class="font-semibold block mb-2">Foto Produk</label>
                <input type="file" name="foto" required accept="image/*" onchange="previewImage(event)" class="w-full border rounded-2xl px-5 py-4">
                <img id="preview" class="hidden mt-5 w-52 rounded-2xl shadow-md">
            </div>

            <div class="p-5 bg-slate-50 border border-dashed border-slate-300 rounded-2xl">
                <label class="font-semibold block mb-1 text-slate-700">
                    <i class="fas fa-file-pdf text-red-500 mr-1"></i> Buku Panduan / Manual Book (Opsional)
                </label>
                <p class="text-xs text-slate-400 mb-3">Format file harus berkstensi .pdf dengan ukuran maksimal 5MB.</p>
                <input type="file" name="file_pdf" accept="application/pdf" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
            </div>

            <button type="submit" name="simpan" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-bold text-lg transition">
                <i class="fas fa-save mr-2"></i> Simpan Produk
            </button>

        </form>

    </div>
</div>

<script>
function previewImage(event){
    const preview = document.getElementById('preview');
    preview.src = URL.createObjectURL(event.target.files[0]);
    preview.classList.remove('hidden');
}
</script>

</body>
</html>