<?php
include '../../config/koneksi.php';

// Mengatur Header agar otomatis kedownload sebagai file Excel
header("Content-Type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Penjualan_NexaTech.xls");

$data = mysqli_query($koneksi, "
    SELECT * FROM pesanan ORDER BY tanggal_pesan DESC
");
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
    }
    td {
        padding: 8px;
        color: #334155;
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
    .text-right { text-align: right; }
</style>

<table>
    <tr>
        <td colspan="5" class="title">NEXATECH STORE</td>
    </tr>
    <tr>
        <td colspan="5" class="subtitle">Laporan Data Penjualan & Pesanan Pelanggan</td>
    </tr>
    <tr>
        <td colspan="5" class="subtitle">Waktu Unduh: <?= date('d-m-Y H:i') ?> WIB</td>
    </tr>
    <tr>
        <td colspan="5"></td> </tr>
</table>

<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th width="50">ID</th>
            <th width="200">Nama Pembeli</th>
            <th width="150">Total Bayar</th>
            <th width="120">Status Pesanan</th>
            <th width="150">Tanggal Pesan</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        while($d = mysqli_fetch_assoc($data)): 
            // Logika ganti warna baris otomatis (Zebra Striping)
            $bg_baris = ($no % 2 == 0) ? 'genap' : 'ganjil';
            
            // Format format tanggal agar mudah dibaca
            $tanggal_rapi = date('d F Y', strtotime($d['tanggal_pesan']));
        ?>
            <tr class="<?= $bg_baris ?>">
                <td class="text-center"><?= $d['id_pesanan'] ?></td>
                <td><?= $d['nama_pembeli'] ?></td>
                
                <td class="text-right">Rp <?= number_format($d['total_bayar'], 0, ',', '.') ?></td>
                
                <td class="text-center"><strong><?= ucfirst($d['status_pesanan']) ?></strong></td>
                <td class="text-center"><?= $tanggal_rapi ?></td>
            </tr>
        <?php 
        $no++;
        endwhile; 
        ?>
    </tbody>
</table>