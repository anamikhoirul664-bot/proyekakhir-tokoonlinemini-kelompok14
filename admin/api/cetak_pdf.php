<?php
// cetak_pdf.php
include '../../config/koneksi.php';

// Proteksi Admin jika diperlukan
if (!isset($_SESSION['admin'])) {
    // session_start(); 
}

require('fpdf.php');

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
    // Tambahkan jam agar mencakup seluruh waktu di tanggal akhir
    $kondisi .= " AND tanggal_pesan BETWEEN '$tgl_mulai 00:00:00' AND '$tgl_akhir 23:59:59'";
}

// Gabungkan query
$query_sql = "SELECT * FROM pesanan $kondisi ORDER BY tanggal_pesan DESC";
$data = mysqli_query($koneksi, $query_sql);

// =======================================================
// INISIALISASI FPDF
// =======================================================
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 15);

// LOGO & JUDUL TOKO (HEADER LAPORAN)
$pdf->SetFont('Helvetica', 'B', 18);
$pdf->SetTextColor(30, 41, 59); 
$pdf->Cell(0, 10, 'NEXATECH STORE', 0, 1, 'C');

$pdf->SetFont('Helvetica', '', 10);
$pdf->SetTextColor(100, 116, 139); 
$pdf->Cell(0, 5, 'Laporan Data Penjualan & Pesanan Pelanggan', 0, 1, 'C');

// Tambahkan Info Filter di Sub-header jika ada rentang tanggal
if (!empty($_GET['tgl_mulai']) && !empty($_GET['tgl_akhir'])) {
    $periode = date('d-m-Y', strtotime($tgl_mulai)) . ' s/d ' . date('d-m-Y', strtotime($tgl_akhir));
    $pdf->Cell(0, 5, 'Periode: ' . $periode, 0, 1, 'C');
}

$pdf->Cell(0, 5, 'Tanggal Cetak: ' . date('d-m-Y H:i') . ' WIB', 0, 1, 'C');

// Membuat Garis Pembatas Tebal
$pdf->Ln(4);
$pdf->SetLineWidth(0.8);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(6);

// HEADER TABEL DATA
$pdf->SetFont('Helvetica', 'B', 11);
$pdf->SetFillColor(37, 99, 235); 
$pdf->SetTextColor(255, 255, 255); 
$pdf->SetDrawColor(203, 213, 225); 
$pdf->SetLineWidth(0.2);

// MENGGANTI 'ID' MENJADI 'No.'
$pdf->Cell(15, 10, 'No.', 1, 0, 'C', true);
$pdf->Cell(60, 10, 'Nama Pembeli', 1, 0, 'L', true);
$pdf->Cell(40, 10, 'Total Bayar', 1, 0, 'R', true);
$pdf->Cell(35, 10, 'Status', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Tanggal Pesan', 1, 1, 'C', true);

// LOOPING DATA DARI DATABASE
$pdf->SetFont('Helvetica', '', 10);
$pdf->SetTextColor(51, 65, 85); 

$fill = false; 
$nomor_urut = 1; // Variabel untuk nomor urut

// Cek apakah data kosong
if (mysqli_num_rows($data) > 0) {
    while($d = mysqli_fetch_assoc($data)) {
        if ($fill) {
            $pdf->SetFillColor(248, 250, 252); 
        } else {
            $pdf->SetFillColor(255, 255, 255); 
        }

        // Tampilkan Nomor Urut (bukan ID)
        $pdf->Cell(15, 8, $nomor_urut++, 1, 0, 'C', true);
        
        $pdf->Cell(60, 8, $d['nama_pembeli'], 1, 0, 'L', true);
        
        $total_format = 'Rp ' . number_format($d['total_bayar'], 0, ',', '.');
        $pdf->Cell(40, 8, $total_format, 1, 0, 'R', true);
        
        $pdf->Cell(35, 8, ucfirst($d['status_pesanan']), 1, 0, 'C', true);
        
        $tanggal_format = date('d/m/Y', strtotime($d['tanggal_pesan']));
        $pdf->Cell(40, 8, $tanggal_format, 1, 1, 'C', true);

        $fill = !$fill; 
    }
} else {
    // Jika filter tidak menemukan hasil
    $pdf->SetFillColor(255, 255, 255);
    $pdf->Cell(190, 10, 'Data tidak ditemukan untuk filter ini.', 1, 1, 'C', true);
}

// OUTPUT KE BROWSER
$pdf->Output('I', 'laporan_penjualan_nexatech.pdf');
exit;
?>