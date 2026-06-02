<?php
include '../../config/koneksi.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../../auth/login_admin.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET['id'];

// AMBIL DATA PRODUK
$queryProduk = mysqli_query(
    $koneksi,
    "SELECT * FROM produk
    WHERE id_produk='$id'"
);

$data = mysqli_fetch_assoc($queryProduk);

if (!$data) {
    header("Location: index.php");
    exit;
}

// UPDATE PRODUK
if (isset($_POST['update'])) {

    $nama = trim(htmlspecialchars($_POST['nama_produk']));
    $id_kategori = (int) $_POST['id_kategori'];
    $brand = trim(htmlspecialchars($_POST['brand']));
    $harga = (int) $_POST['harga'];
    $stok = (int) $_POST['stok'];
    $kondisi = $_POST['kondisi'];
    $garansi = trim(htmlspecialchars($_POST['garansi']));
    $berat = (int) $_POST['berat'];
    $status_produk = $_POST['status_produk'];

    $deskripsi = trim(htmlspecialchars($_POST['deskripsi']));
    $spesifikasi = trim(htmlspecialchars($_POST['spesifikasi']));

    $foto = $_FILES['foto']['name'];
    $tmp  = $_FILES['foto']['tmp_name'];
    $size = $_FILES['foto']['size'];

    // VALIDASI
    if (strlen($nama) < 3) {
        $error = "Nama produk minimal 3 karakter!";
    }

    elseif (empty($brand)) {
        $error = "Brand wajib diisi!";
    }

    elseif ($harga <= 0) {
        $error = "Harga harus lebih dari 0!";
    }

    elseif ($stok < 0) {
        $error = "Stok tidak boleh minus!";
    }

    elseif ($berat <= 0) {
        $error = "Berat produk harus diisi!";
    }

    else {

        // JIKA GANTI FOTO
        if (!empty($foto)) {

            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            $ext = strtolower(
                pathinfo($foto, PATHINFO_EXTENSION)
            );

            if (!in_array($ext, $allowed)) {
                $error =
                "Format gambar harus JPG, JPEG, PNG, atau WEBP!";
            }

            elseif ($size > 2000000) {
                $error =
                "Ukuran gambar maksimal 2MB!";
            }

            else {

                $namaFoto =
                time().'_'.rand(100,999).'.'.$ext;

                $path =
                "../../assets/images/".$namaFoto;

                if (
                    move_uploaded_file(
                        $tmp,
                        $path
                    )
                ) {

                    // hapus foto lama
                    if (
                        !empty($data['foto']) &&
                        file_exists(
                            "../../assets/images/" .
                            $data['foto']
                        )
                    ) {
                        unlink(
                            "../../assets/images/" .
                            $data['foto']
                        );
                    }

                    $query = "
                    UPDATE produk SET
                    id_kategori='$id_kategori',
                    brand='$brand',
                    nama_produk='$nama',
                    harga='$harga',
                    kondisi='$kondisi',
                    garansi='$garansi',
                    berat='$berat',
                    stok='$stok',
                    status_produk='$status_produk',
                    deskripsi='$deskripsi',
                    spesifikasi='$spesifikasi',
                    foto='$namaFoto'
                    WHERE id_produk='$id'
                    ";

                } else {
                    $error = "Upload gambar gagal!";
                }
            }

        } else {

            // TANPA GANTI FOTO
            $query = "
            UPDATE produk SET
            id_kategori='$id_kategori',
            brand='$brand',
            nama_produk='$nama',
            harga='$harga',
            kondisi='$kondisi',
            garansi='$garansi',
            berat='$berat',
            stok='$stok',
            status_produk='$status_produk',
            deskripsi='$deskripsi',
            spesifikasi='$spesifikasi'
            WHERE id_produk='$id'
            ";
        }

        if (!isset($error)) {

            if (mysqli_query($koneksi, $query)) {

                echo "
                <script>
                alert('Produk berhasil diperbarui!');
                window.location='index.php';
                </script>
                ";

                exit;
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

<title>Edit Produk</title>

<script src="https://cdn.tailwindcss.com"></script>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body class="bg-slate-100 min-h-screen p-8">

<div class="max-w-4xl mx-auto">


    <div class="flex justify-between items-center mb-8">

        <div>
            <h1 class="text-4xl font-bold text-slate-800">
                Edit Produk
            </h1>

            <p class="text-gray-500 mt-1">
                Perbarui data produk
            </p>
        </div>

        <a href="index.php"
        class="bg-slate-600 hover:bg-slate-700 text-white px-5 py-3 rounded-xl transition">

            ← Kembali
        </a>

    </div>


    <div class="bg-white rounded-[30px] shadow-xl p-10">

        <?php if(isset($error)): ?>
            <div class="bg-red-100 text-red-600 p-4 rounded-2xl mb-6">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form
        method="POST"
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
                value="<?= $data['nama_produk'] ?>"
                class="w-full border border-slate-300 rounded-2xl px-5 py-4">
            </div>


            <div>
                <label class="font-semibold block mb-2">
                    Kategori
                </label>

                <select
                name="id_kategori"
                class="w-full border border-slate-300 rounded-2xl px-5 py-4">

                    <?php
                    $kat = mysqli_query(
                        $koneksi,
                        "SELECT * FROM kategori"
                    );

                    while($k =
                    mysqli_fetch_assoc($kat)):
                    ?>

                    <option
                    value="<?= $k['id_kategori'] ?>"
                    <?= ($k['id_kategori']
                    == $data['id_kategori'])
                    ? 'selected' : '' ?>>

                        <?= $k['nama_kategori'] ?>

                    </option>

                    <?php endwhile; ?>

                </select>
            </div>

            <div class="grid md:grid-cols-2 gap-5">

            <div>
                <label class="font-semibold block mb-2">
                    Brand
                </label>

                <input
                type="text"
                name="brand"
                required
                value="<?= $data['brand'] ?>"
                class="w-full border rounded-2xl px-5 py-4">
            </div>

            <div>
                <label class="font-semibold block mb-2">
                    Kondisi
                </label>

                <select
                name="kondisi"
                class="w-full border rounded-2xl px-5 py-4">

                    <option value="Baru"
                    <?= $data['kondisi'] == 'Baru' ? 'selected' : '' ?>>
                        Baru
                    </option>

                    <option value="Bekas"
                    <?= $data['kondisi'] == 'Bekas' ? 'selected' : '' ?>>
                        Bekas
                    </option>

                </select>
            </div>

        </div>

            <div class="grid md:grid-cols-2 gap-5">

                <div>
                    <label class="font-semibold block mb-2">
                        Harga
                    </label>

                    <input
                    type="number"
                    name="harga"
                    required
                    min="0"
                    value="<?= $data['harga'] ?>"
                    class="w-full border border-slate-300 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div>
                    <label class="font-semibold block mb-2">
                        Stok
                    </label>

                    <input
                    type="number"
                    name="stok"
                    required
                    min="0"
                    value="<?= $data['stok'] ?>"
                    class="w-full border border-slate-300 rounded-2xl px-5 py-4 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div>
                    <label class="font-semibold block mb-2">
                        Garansi
                    </label>

                    <input
                    type="text"
                    name="garansi"
                    value="<?= $data['garansi'] ?>"
                    class="w-full border rounded-2xl px-5 py-4">
                </div>

                <div>
                    <label class="font-semibold block mb-2">
                        Berat (gram)
                    </label>

                    <input
                    type="number"
                    name="berat"
                    min="1"
                    value="<?= $data['berat'] ?>"
                    class="w-full border rounded-2xl px-5 py-4">
                </div>

                <div>
                    <label class="font-semibold block mb-2">
                        Status Produk
                    </label>

                    <select
                    name="status_produk"
                    class="w-full border rounded-2xl px-5 py-4">

                        <option value="tersedia"
                        <?= $data['status_produk'] == 'tersedia' ? 'selected' : '' ?>>
                            Tersedia
                        </option>

                        <option value="habis"
                        <?= $data['status_produk'] == 'habis' ? 'selected' : '' ?>>
                            Habis
                        </option>

                    </select>
                </div>

            </div>

            

            <div>
                <label class="font-semibold block mb-2">
                    Deskripsi
                </label>

               <textarea
                name="deskripsi"
                rows="4"
                class="w-full border rounded-2xl px-5 py-4"><?= $data['deskripsi'] ?></textarea>
            </div>

            <div>
            <label class="font-semibold block mb-2">
                Spesifikasi Produk
            </label>

            <textarea
            name="spesifikasi"
            rows="6"
            class="w-full border rounded-2xl px-5 py-4"><?= $data['spesifikasi'] ?></textarea>
        </div>

            <div>

                <label class="font-semibold block mb-3">
                    Foto Saat Ini
                </label>

                <img
                src="../../assets/images/<?= $data['foto'] ?>"
                class="w-40 rounded-2xl shadow-md mb-5">

                <input
                type="file"
                name="foto"
                accept="image/*"
                onchange="previewImage(event)"
                class="w-full border border-slate-300 rounded-2xl px-5 py-4">

                <p class="text-sm text-gray-500 mt-2">
                    Kosongkan jika tidak ingin mengganti foto
                </p>


                <img
                id="preview"
                class="hidden mt-5 w-40 rounded-2xl shadow-md">

            </div>


            <div class="flex justify-end gap-4 pt-4">

                <a href="index.php"
                class="bg-slate-500 hover:bg-slate-600 text-white px-6 py-3 rounded-2xl">

                    Batal
                </a>

                <button
                type="submit"
                name="update"
                class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-2xl font-bold shadow-lg">

                    <i class="fas fa-save mr-2"></i>
                    Simpan Perubahan
                </button>

            </div>

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