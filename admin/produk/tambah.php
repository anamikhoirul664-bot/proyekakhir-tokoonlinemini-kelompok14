<?php
include '../../config/koneksi.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../../auth/login_admin.php");
    exit;
}

if(isset($_POST['simpan'])){

    $nama = trim(htmlspecialchars($_POST['nama_produk']));
    $id_kategori = $_POST['id_kategori'];
    $brand = trim(htmlspecialchars($_POST['brand']));
    $harga = (int) $_POST['harga'];
    $stok = (int) $_POST['stok'];
    $kondisi = $_POST['kondisi'];
    $garansi = trim(htmlspecialchars($_POST['garansi']));
    $berat = (int) $_POST['berat'];
    $status_produk = $_POST['status_produk'];

    $deskripsi = trim(htmlspecialchars($_POST['deskripsi']));
    $spesifikasi = trim(htmlspecialchars($_POST['spesifikasi']));

    // VALIDASI
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

        // FOTO
        $foto = $_FILES['foto']['name'];
        $tmp = $_FILES['foto']['tmp_name'];
        $size = $_FILES['foto']['size'];

        $allowed = ['jpg','jpeg','png','webp'];

        $ext = strtolower(
            pathinfo($foto, PATHINFO_EXTENSION)
        );

        if(!in_array($ext, $allowed)){
            $error = "Format gambar harus JPG, JPEG, PNG, atau WEBP!";
        }

        elseif($size > 5000000){
            $error = "Ukuran gambar maksimal 5MB!";
        }

        else{

            $namaFoto =
            time().'_'.rand(100,999).'.'.$ext;

            $path =
            "../../assets/images/".$namaFoto;

            if(move_uploaded_file($tmp, $path)){

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
                        foto
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
                        '$namaFoto'
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
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Tambah Produk</title>

<script src="https://cdn.tailwindcss.com"></script>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body class="bg-slate-100 p-6">

<div class="max-w-5xl mx-auto">

    <div class="flex justify-between items-center mb-8">

        <div>
            <h1 class="text-4xl font-bold text-slate-800">
                Tambah Produk
            </h1>

            <p class="text-slate-500">
                Tambahkan produk elektronik baru
            </p>
        </div>

        <a href="index.php"
        class="bg-slate-700 text-white px-5 py-3 rounded-2xl hover:bg-slate-800 transition">

            ← Kembali
        </a>

    </div>

    <div class="bg-white rounded-[30px] shadow-xl p-8">

        <?php if(isset($error)): ?>
        <div class="bg-red-100 text-red-600 p-4 rounded-2xl mb-5">
            <?= $error ?>
        </div>
        <?php endif; ?>

        <form method="POST"
        enctype="multipart/form-data"
        class="space-y-6">

            <div>
                <label class="font-semibold block mb-2">
                    Nama Produk
                </label>

                <input
                type="text"
                name="nama_produk"
                required
                class="w-full border rounded-2xl px-5 py-4"
                placeholder="Contoh: Samsung Galaxy S24 Ultra">
            </div>

            <div class="grid md:grid-cols-2 gap-5">

                <div>
                    <label class="font-semibold block mb-2">
                        Kategori
                    </label>

                    <select
                    name="id_kategori"
                    class="w-full border rounded-2xl px-5 py-4">

                    <?php
                    $kat = mysqli_query(
                        $koneksi,
                        "SELECT * FROM kategori"
                    );

                    while($k = mysqli_fetch_assoc($kat)):
                    ?>

                    <option value="<?= $k['id_kategori'] ?>">
                        <?= $k['nama_kategori'] ?>
                    </option>

                    <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label class="font-semibold block mb-2">
                        Brand
                    </label>

                    <input
                    type="text"
                    name="brand"
                    required
                    class="w-full border rounded-2xl px-5 py-4"
                    placeholder="Samsung / ASUS / Logitech">
                </div>

            </div>

            <div class="grid md:grid-cols-2 gap-5">

                <div>
                    <label class="font-semibold block mb-2">
                        Harga
                    </label>

                    <input
                    type="number"
                    min="1"
                    name="harga"
                    required
                    class="w-full border rounded-2xl px-5 py-4">
                </div>

                <div>
                    <label class="font-semibold block mb-2">
                        Stok
                    </label>

                    <input
                    type="number"
                    min="0"
                    name="stok"
                    required
                    class="w-full border rounded-2xl px-5 py-4">
                </div>

            </div>

            <div class="grid md:grid-cols-3 gap-5">

                <div>
                    <label class="font-semibold block mb-2">
                        Kondisi
                    </label>

                    <select
                    name="kondisi"
                    class="w-full border rounded-2xl px-5 py-4">

                        <option value="Baru">Baru</option>
                        <option value="Bekas">Bekas</option>

                    </select>
                </div>

                <div>
                    <label class="font-semibold block mb-2">
                        Garansi
                    </label>

                    <input
                    type="text"
                    name="garansi"
                    class="w-full border rounded-2xl px-5 py-4"
                    placeholder="Garansi Resmi 1 Tahun">
                </div>

                <div>
                    <label class="font-semibold block mb-2">
                        Berat (gram)
                    </label>

                    <input
                    type="number"
                    min="1"
                    name="berat"
                    required
                    class="w-full border rounded-2xl px-5 py-4">
                </div>

            </div>

            <div>
                <label class="font-semibold block mb-2">
                    Status Produk
                </label>

                <select
                name="status_produk"
                class="w-full border rounded-2xl px-5 py-4">

                    <option value="tersedia">
                        Tersedia
                    </option>

                    <option value="habis">
                        Habis
                    </option>

                </select>
            </div>

            <div>
                <label class="font-semibold block mb-2">
                    Deskripsi
                </label>

                <textarea
                name="deskripsi"
                rows="4"
                class="w-full border rounded-2xl px-5 py-4"></textarea>
            </div>

            <div>
                <label class="font-semibold block mb-2">
                    Spesifikasi Produk
                </label>

                <textarea
                name="spesifikasi"
                rows="6"
                class="w-full border rounded-2xl px-5 py-4"
                placeholder="RAM: 12GB&#10;Storage: 256GB&#10;Chipset: Snapdragon"></textarea>
            </div>

            <div>
                <label class="font-semibold block mb-2">
                    Foto Produk
                </label>

                <input
                type="file"
                name="foto"
                required
                accept="image/*"
                onchange="previewImage(event)"
                class="w-full border rounded-2xl px-5 py-4">

                <img
                id="preview"
                class="hidden mt-5 w-52 rounded-2xl shadow-md">
            </div>

            <button
            type="submit"
            name="simpan"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-bold text-lg transition">

                <i class="fas fa-save mr-2"></i>
                Simpan Produk
            </button>

        </form>

    </div>

</div>

<script>
function previewImage(event){

    const preview =
    document.getElementById('preview');

    preview.src =
    URL.createObjectURL(
        event.target.files[0]
    );

    preview.classList.remove('hidden');
}
</script>

</body>
</html>