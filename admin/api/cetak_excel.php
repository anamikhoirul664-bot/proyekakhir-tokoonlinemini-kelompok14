<?php
include '../../config/koneksi.php';

// =======================================================
// TANGKAP DATA FILTER DARI FORM
// =======================================================
$kondisi = "WHERE 1=1"; // Default kondisi selalu benar

// Filter Nama
if (!empty($_GET['nama'])) {
    $nama = mysqli_real_escape_string($koneksi, $_GET['nama']);
    $kondisi .= " AND nama_pembeli LIKE '%$nama%'";
}

// Filter Status
if (!empty($_GET['status'])) {
    $status = mysqli_real_escape_string($koneksi, $_GET['status']);
    $kondisi .= " AND status_pesanan = '$status'";
}

// Filter Tanggal
if (!empty($_GET['tgl_mulai']) && !empty($_GET['tgl_akhir'])) {
    $tgl_mulai = mysqli_real_escape_string($koneksi, $_GET['tgl_mulai']);
    $tgl_akhir = mysqli_real_escape_string($koneksi, $_GET['tgl_akhir']);
    $kondisi .= " AND tanggal_pesan BETWEEN '$tgl_mulai 00:00:00' AND '$tgl_akhir 23:59:59'";
}

// Ambil data berdasarkan filter
$query_sql = "SELECT * FROM pesanan $kondisi ORDER BY tanggal_pesan DESC";
$data = mysqli_query($koneksi, $query_sql);

// =======================================================
// HEADER UNTUK DOWNLOAD EXCEL
// =======================================================
header("Content-Type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Penjualan_NexaTech.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>

<style>
    .title {
        font-family: 'Segoe UI', Helvetica, Arial, sans-serif;
        font-size: 16pt;
        font-weight: bold;
        color: #1e293b;
        text-align: center;
    }
    .subtitle {
        font-family: 'Segoe UI', Helvetica, Arial, sans-serif;
        font-size: 10pt;
        color: #64748b;
        text-align: center;
    }
    table {
        border-collapse: collapse;
        font-family: 'Segoe UI', Helvetica, Arial, sans-serif;
        font-size: 10pt;
        width: 100%;
    }
    th {
        background-color: #2563eb; /* Biru Khas NexaTech Store */
        color: #ffffff;
        font-weight: bold;
        padding: 10px;
        text-align: center;
        border: 1px solid #cbd5e1;
    }
    td {
        padding: 8px;
        color: #334155;
        border: 1px solid #cbd5e1;
    }
    /* Warna baris selang-seling (Zebra) */
    .genap {
        background-color: #f8fafc; 
    }
    .ganjil {
        background-color: #ffffff;
    }
    /* Aligment khusus */
    .text-center { text-align: center; }
    .text-left { text-align: left; }
    .text-right { text-align: right; }
    .font-bold { font-weight: bold; }
</style>

<table>
    <tr>
        <td colspan="5" class="title">NEXATECH STORE</td>
    </tr>
    <tr>
        <td colspan="5" class="subtitle">Laporan Data Penjualan & Pesanan Pelanggan</td>
    </tr>
    
    <?php 
    // Tampilkan informasi rentang tanggal jika difilter
    if (!empty($_GET['tgl_mulai']) && !empty($_GET['tgl_akhir'])) {
        $periode = date('d-m-Y', strtotime($tgl_mulai)) . ' s/d ' . date('d-m-Y', strtotime($tgl_akhir));
        echo '<tr><td colspan="5" class="subtitle">Periode: ' . $periode . '</td></tr>';
    }
    ?>

    <tr>
        <td colspan="5" class="subtitle">Waktu Unduh: <?= date('d-m-Y H:i') ?> WIB</td>
    </tr>
    <tr>
        <td colspan="5"></td> 
    </tr>
</table>

<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th width="50">No.</th>
            <th width="200">Nama Pembeli</th>
            <th width="150">Total Bayar</th>
            <th width="120">Status Pesanan</th>
            <th width="150">Tanggal Pesan</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        $total_seluruh = 0; // Tambahan fitur untuk menghitung total keseluruhan bayar

        if(mysqli_num_rows($data) > 0):
            while($d = mysqli_fetch_assoc($data)): 
                // Logika ganti warna baris otomatis (Zebra Striping)
                $bg_baris = ($no % 2 == 0) ? 'genap' : 'ganjil';
                
                // Format format tanggal agar mudah dibaca
                $tanggal_rapi = date('d F Y', strtotime($d['tanggal_pesan']));

                // Hitung total bayar
                $total_seluruh += $d['total_bayar'];
        ?>
            <tr class="<?= $bg_baris ?>">
                <td class="text-center"><?= $no ?></td>
                <td><?= $d['nama_pembeli'] ?></td>
                <td class="text-right">Rp <?= number_format($d['total_bayar'], 0, ',', '.') ?></td>
                <td class="text-center"><strong><?= ucfirst($d['status_pesanan']) ?></strong></td>
                <td class="text-center"><?= $tanggal_rapi ?></td>
            </tr>
        <?php 
            $no++;
            endwhile; 
        else:
        ?>
            <tr>
                <td colspan="5" class="text-center">Data tidak ditemukan pada filter ini.</td>
            </tr>
        <?php endif; ?>
        
        <?php if(mysqli_num_rows($data) > 0): ?>
        <tr>
            <td colspan="2" class="text-left font-bold" style="background-color: #e2e8f0; border-top: 2px solid #334155;">TOTAL</td>
            <td class="text-right font-bold" style="background-color: #e2e8f0; border-top: 2px solid #334155;">Rp <?= number_format($total_seluruh, 0, ',', '.') ?></td>
            <td colspan="2" style="background-color: #e2e8f0; border-top: 2px solid #334155;"></td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>