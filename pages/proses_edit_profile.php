<?php
session_start();
include '../config/koneksi.php';

// 1. Proteksi halaman: Wajib login
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id_user = (int)$_SESSION['user']['id_user'];

// 2. Tangkap data dari form (Termasuk ALAMAT)
$nama   = mysqli_real_escape_string($koneksi, $_POST['nama']);
$email  = mysqli_real_escape_string($koneksi, $_POST['email']);
$no_hp  = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
$alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']); // Ambil input alamat baru

$foto = $_SESSION['user']['foto'] ?? '';

// 3. TAMENG CEK EMAIL KEMBAR: Mencegah Fatal Error Duplicate Entry
$cek_email = mysqli_query($koneksi, "SELECT id_user FROM users WHERE email = '$email' AND id_user != $id_user");

if (mysqli_num_rows($cek_email) > 0) {
    // Jika email sudah dipakai akun lain, stop proses dan beri peringatan rapi
    echo "<script>
            alert('Gagal menyimpan! Email sudah digunakan oleh pengguna lain.');
            window.location.href = 'edit_profil.php';
          </script>";
    exit;
}

// 4. Jika upload foto baru
if(isset($_FILES['foto']) && $_FILES['foto']['error'] == 0){
    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $namaFile = time() . "." . $ext;
    $folder = "../assets/profile/" . $namaFile;

    if(move_uploaded_file($_FILES['foto']['tmp_name'], $folder)){
        // Hapus foto lama dari server jika bukan foto kosong (opsional, biar hemat storage)
        if(!empty($foto) && file_exists("../assets/profile/" . $foto)) {
            unlink("../assets/profile/" . $foto);
        }
        $foto = $namaFile;
    }
}

// 5. Query UPDATE (Sekarang sudah mendukung kolom alamat)
$query = "UPDATE users SET
            nama='$nama',
            email='$email',
            no_hp='$no_hp',
            alamat='$alamat',
            foto='$foto'
          WHERE id_user='$id_user'";

if (mysqli_query($koneksi, $query)) {
    // 6. Ambil data terbaru dari database untuk memperbarui session
    $data = mysqli_fetch_assoc(
        mysqli_query(
            $koneksi,
            "SELECT * FROM users WHERE id_user='$id_user'"
        )
    );

    $_SESSION['user'] = $data;

    // Beri info sukses lalu balik ke halaman profil
    echo "<script>
            alert('Profil berhasil diperbarui!');
            window.location.href = 'profile.php';
          </script>";
    exit;
} else {
    echo "Error: " . mysqli_error($koneksi);
}
?>