<?php
include '../../config/koneksi.php';

// Proteksi Admin jika diperlukan
if (!isset($_SESSION['admin'])) {
    // session_start(); // Pastikan session_start() aktif di config atau di sini jika belum
}

// Cukup panggil fpdf.php langsung karena filenya sudah satu folder (sejajar)
require('fpdf.php');

// 1. Inisialisasi Objek FPDF (P = Portrait, mm = Milimeter, A4 = Ukuran Kertas)
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 15);

// 2. LOGO & JUDUL TOKO (HEADER LAPORAN)
$pdf->SetFont('Helvetica', 'B', 18);
$pdf->SetTextColor(30, 41, 59); // Warna Slate-800 ala Tailwind
$pdf->Cell(0, 10, 'NEXATECH STORE', 0, 1, 'C');

$pdf->SetFont('Helvetica', '', 10);
$pdf->SetTextColor(100, 116, 139); // Warna abu-abu text-slate-500
$pdf->Cell(0, 5, 'Laporan Data Penjualan & Pesanan Pelanggan', 0, 1, 'C');
$pdf->Cell(0, 5, 'Tanggal Cetak: ' . date('d-m-Y H:i') . ' WIB', 0, 1, 'C');

// Membuat Garis Pembatas Tebal
$pdf->Ln(4);
$pdf->SetLineWidth(0.8);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(6);

// 3. HEADER TABEL DATA
$pdf->SetFont('Helvetica', 'B', 11);
$pdf->SetFillColor(37, 99, 235); // Warna Biru Khas NexaTech Store (Blue-600)
$pdf->SetTextColor(255, 255, 255); // Warna Teks Putih
$pdf->SetDrawColor(203, 213, 225); // Warna Border Lembut (Border-slate-300)
$pdf->SetLineWidth(0.2);

// Cell(Lebar, Tinggi, Teks, Border, Pindah Baris [0=tidak, 1=ya], Alignment, Fill Background)
$pdf->Cell(15, 10, 'ID', 1, 0, 'C', true);
$pdf->Cell(60, 10, 'Nama Pembeli', 1, 0, 'L', true);
$pdf->Cell(40, 10, 'Total Bayar', 1, 0, 'R', true);
$pdf->Cell(35, 10, 'Status', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Tanggal Pesan', 1, 1, 'C', true);

// 4. LOOPING DATA DARI DATABASE
$pdf->SetFont('Helvetica', '', 10);
$pdf->SetTextColor(51, 65, 85); // Teks warna gelap kembali

$data = mysqli_query($koneksi, "SELECT * FROM pesanan ORDER BY tanggal_pesan DESC");

$fill = false; // Untuk selang-seling warna baris tabel (Zebra striping)

while($d = mysqli_fetch_assoc($data)) {
    // Atur warna background baris otomatis selang-seling biar rapi
    if ($fill) {
        $pdf->SetFillColor(248, 250, 252); // Abu-abu sangat muda (slate-50)
    } else {
        $pdf->SetFillColor(255, 255, 255); // Putih bersih
    }

    $pdf->Cell(15, 8, $d['id_pesanan'], 1, 0, 'C', true);
    $pdf->Cell(60, 8, $d['nama_pembeli'], 1, 0, 'L', true);
    
    // Format mata uang Rupiah biar rapi di laporan PDF
    $total_format = 'Rp ' . number_format($d['total_bayar'], 0, ',', '.');
    $pdf->Cell(40, 8, $total_format, 1, 0, 'R', true);
    
    $pdf->Cell(35, 8, ucfirst($d['status_pesanan']), 1, 0, 'C', true);
    
    // Format tanggal pesan
    $tanggal_format = date('d/m/Y', strtotime($d['tanggal_pesan']));
    $pdf->Cell(40, 8, $tanggal_format, 1, 1, 'C', true);

    $fill = !$fill; // Switch warna baris selanjutnya
}

// 5. OUTPUT KE BROWSER
// "I" artinya file PDF langsung dibuka/di-preview di browser admin, ganti "D" jika ingin langsung otomatis download.
$pdf->Output('I', 'laporan_penjualan_nexatech.pdf');
exit;
?>