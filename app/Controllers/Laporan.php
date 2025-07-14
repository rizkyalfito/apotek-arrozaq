<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ObatMasukModel;
use App\Models\ObatKeluarModel;
use App\Models\DataStokObatModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use TCPDF;

class Laporan extends BaseController
{
    protected $obatMasukModel;
    protected $obatKeluarModel;
    protected $stokObatModel;

    public function __construct()
    {
        $this->obatMasukModel = new ObatMasukModel();
        $this->obatKeluarModel = new ObatKeluarModel();
        $this->stokObatModel = new DataStokObatModel();
    }

    // ===== OBAT MASUK =====
    public function obatMasuk()
    {
        // Default: Tampilkan semua data obat masuk
        $data = [
            'title' => 'Laporan Obat Masuk',
            'obatMasuk' => $this->obatMasukModel->findAll()
        ];
        
        return view('laporan/obat_masuk', $data);
    }

    public function filterObatMasuk()
    {
        $tanggal_mulai = $this->request->getPost('tanggal_mulai');
        $tanggal_akhir = $this->request->getPost('tanggal_akhir');
        
        // Query dengan filter tanggal
        $obatMasuk = $this->obatMasukModel
            ->where('tanggal_masuk >=', $tanggal_mulai)
            ->where('tanggal_masuk <=', $tanggal_akhir)
            ->findAll();
        
        $data = [
            'title' => 'Laporan Obat Masuk',
            'obatMasuk' => $obatMasuk,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_akhir' => $tanggal_akhir
        ];
        
        return view('laporan/obat_masuk', $data);
    }

    public function exportPdfObatMasuk()
    {
        // Membuat objek TCPDF
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        
        // Set informasi dokumen
        $pdf->SetCreator('Sistem Manajemen Obat');
        $pdf->SetAuthor('Admin');
        $pdf->SetTitle('Laporan Obat Masuk');
        
        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Set margin (increased left and right margins to center the table)
        $pdf->SetMargins(20, 20, 20);
        
        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 20);
        
        // Add a page
        $pdf->AddPage();
        
        // ===== HEADER WITH LOGO AND APOTEK NAME (CENTERED) =====
        // Logo path
        $logoPath = FCPATH . 'assets/adminlte/dist/img/logo-apotek.jpg';
        
        // Calculate center position for logo and text
        $pageWidth = $pdf->getPageWidth();
        $logoWidth = 15; // Reduced from 25
        $logoHeight = 15; // Reduced from 25
        $logoX = ($pageWidth - $logoWidth) / 2;
        
        // Check if logo exists
        if (file_exists($logoPath)) {
            // Add logo (centered)
            $pdf->Image($logoPath, $logoX, 20, $logoWidth, $logoHeight, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }
        
        // Nama Apotek (centered below logo)
        $pdf->SetFont('helvetica', 'B', 16); // Reduced from 20
        $pdf->SetXY(20, 38); // Position below logo
        $pdf->Cell(0, 10, 'APOTEK-ARROZAQ', 0, 1, 'C');
        
        // Add minimal space after header (reduced from 10 to 5)
        $pdf->Ln(5);
        
        // ===== REPORT TITLE =====
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'LAPORAN OBAT MASUK', 0, 1, 'C');
        
        // Get filter parameters - check both GET and POST methods
        $tanggal_mulai = $this->request->getGet('tanggal_mulai') 
                        ?? $this->request->getPost('tanggal_mulai') 
                        ?? date('Y-m-01');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir') 
                        ?? $this->request->getPost('tanggal_akhir') 
                        ?? date('Y-m-d');
        
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 7, 'Periode: ' . date('d-m-Y', strtotime($tanggal_mulai)) . ' s/d ' . date('d-m-Y', strtotime($tanggal_akhir)), 0, 1, 'C');
        
        // Add a horizontal line separator
        $pdf->Ln(2);
        $pdf->SetLineWidth(0.5);
        $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + $pdf->getPageWidth() - 40, $pdf->GetY());
        $pdf->Ln(5);
        
        // Calculate table width to center it
        $tableWidth = 250; // Total width of our table
        $leftMargin = ($pageWidth - $tableWidth) / 2;
        
        // Reset left margin for table to center it
        $pdf->SetX($leftMargin);
        
        // Header tabel dengan styling
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetFillColor(220, 220, 220); // Light gray background for header
        
        // Column widths
        $colWidth = [20, 60, 20, 30, 60, 60];
        
        $pdf->Cell($colWidth[0], 8, 'ID Obat', 1, 0, 'C', true);
        $pdf->Cell($colWidth[1], 8, 'Nama Obat', 1, 0, 'C', true);
        $pdf->Cell($colWidth[2], 8, 'Jumlah', 1, 0, 'C', true);
        $pdf->Cell($colWidth[3], 8, 'Satuan', 1, 0, 'C', true);
        $pdf->Cell($colWidth[4], 8, 'Tanggal Masuk', 1, 0, 'C', true);
        $pdf->Cell($colWidth[5], 8, 'Tanggal Kadaluwarsa', 1, 1, 'C', true);
        
        // Isi tabel dengan style alternating row colors
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetFillColor(245, 245, 245); // Very light gray for alternating rows
        
        // Ambil data obat masuk dengan filter tanggal
        $obatMasuk = $this->obatMasukModel
            ->where('tanggal_masuk >=', $tanggal_mulai)
            ->where('tanggal_masuk <=', $tanggal_akhir)
            ->findAll();
        
        $rowCount = 0;
        foreach ($obatMasuk as $row) {
            // Reset X position for each row to maintain table centering
            $pdf->SetX($leftMargin);
            
            // Alternating fill
            $fill = ($rowCount % 2 == 0) ? true : false;
            
            $pdf->Cell($colWidth[0], 7, $row['id_obat'], 1, 0, 'C', $fill);
            $pdf->Cell($colWidth[1], 7, $row['nama_obat'], 1, 0, 'L', $fill);
            $pdf->Cell($colWidth[2], 7, $row['jumlah'], 1, 0, 'C', $fill);
            $pdf->Cell($colWidth[3], 7, $row['satuan'], 1, 0, 'C', $fill);
            $pdf->Cell($colWidth[4], 7, date('d-m-Y', strtotime($row['tanggal_masuk'])), 1, 0, 'C', $fill);
            $pdf->Cell($colWidth[5], 7, date('d-m-Y', strtotime($row['tanggal_kadaluwarsa'])), 1, 1, 'C', $fill);
            
            $rowCount++;
        }
        
        // If no data
        if (count($obatMasuk) == 0) {
            $pdf->SetX($leftMargin);
            $pdf->Cell(array_sum($colWidth), 10, 'Tidak ada data yang tersedia untuk periode tersebut', 1, 1, 'C');
        }
        
        // Add footer with date generated
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->Cell(0, 10, 'Dicetak pada: ' . date('d/m/Y H:i:s'), 0, 0, 'R');
        
        // Output PDF
        $this->response->setContentType('application/pdf');
        $pdf->Output('laporan_obat_masuk_' . $tanggal_mulai . '_' . $tanggal_akhir . '.pdf', 'I');
    }


    public function exportExcelObatMasuk()
    {
        // Get filter parameters - check both GET and POST methods
        $tanggal_mulai = $this->request->getGet('tanggal_mulai') 
                        ?? $this->request->getPost('tanggal_mulai') 
                        ?? date('Y-m-01');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir') 
                        ?? $this->request->getPost('tanggal_akhir') 
                        ?? date('Y-m-d');
        
        // Membuat objek Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set judul kolom
        $sheet->setCellValue('A1', 'Laporan Obat Masuk');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Set periode
        $sheet->setCellValue('A2', 'Periode: ' . date('d-m-Y', strtotime($tanggal_mulai)) . ' s/d ' . date('d-m-Y', strtotime($tanggal_akhir)));
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Set header tabel
        $sheet->setCellValue('A4', 'ID Obat');
        $sheet->setCellValue('B4', 'Nama Obat');
        $sheet->setCellValue('C4', 'Jumlah');
        $sheet->setCellValue('D4', 'Satuan');
        $sheet->setCellValue('E4', 'Tanggal Masuk');
        $sheet->setCellValue('F4', 'Tanggal Kadaluwarsa');
        
        $sheet->getStyle('A4:F4')->getFont()->setBold(true);
        $sheet->getStyle('A4:F4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A4:F4')->getFill()->getStartColor()->setARGB('FFCCCCCC');
        
        // Ambil data obat masuk dengan filter tanggal
        $obatMasuk = $this->obatMasukModel
            ->where('tanggal_masuk >=', $tanggal_mulai)
            ->where('tanggal_masuk <=', $tanggal_akhir)
            ->findAll();
        
        $row = 5;
        foreach ($obatMasuk as $data) {
            $sheet->setCellValue('A' . $row, $data['id_obat']);
            $sheet->setCellValue('B' . $row, $data['nama_obat']);
            $sheet->setCellValue('C' . $row, $data['jumlah']);
            $sheet->setCellValue('D' . $row, $data['satuan']);
            $sheet->setCellValue('E' . $row, date('d-m-Y', strtotime($data['tanggal_masuk'])));
            $sheet->setCellValue('F' . $row, date('d-m-Y', strtotime($data['tanggal_kadaluwarsa'])));
            $row++;
        }
        
        // If no data, add message
        if (count($obatMasuk) == 0) {
            $sheet->setCellValue('A5', 'Tidak ada data yang tersedia untuk periode tersebut');
            $sheet->mergeCells('A5:F5');
            $sheet->getStyle('A5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $row = 6;
        }
        
        // Auto size kolom
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set border untuk semua cell data
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A4:F' . ($row - 1))->applyFromArray($styleArray);
        
        // Set header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="laporan_obat_masuk_' . $tanggal_mulai . '_' . $tanggal_akhir . '.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    // ===== OBAT KELUAR =====
    public function obatKeluar()
    {
        // Tampilkan semua data obat keluar dengan join untuk mendapatkan harga
        $obatKeluarData = $this->obatKeluarModel
            ->select('obat_keluar.*, data_stok_obat.harga_modal, data_stok_obat.harga_jual')
            ->join('data_stok_obat', 'data_stok_obat.id_obat = obat_keluar.id_obat', 'left')
            ->findAll();
        
        $data = [
            'title' => 'Laporan Obat Keluar',
            'obatKeluar' => $obatKeluarData
        ];
        
        return view('laporan/obat_keluar', $data);
    }
    
    public function filterObatKeluar()
    {
        $tanggal_mulai = $this->request->getPost('tanggal_mulai');
        $tanggal_akhir = $this->request->getPost('tanggal_akhir');
        
        // Query dengan filter tanggal dan join untuk mendapatkan harga
        $obatKeluar = $this->obatKeluarModel
            ->select('obat_keluar.*, data_stok_obat.harga_modal, data_stok_obat.harga_jual')
            ->join('data_stok_obat', 'data_stok_obat.id_obat = obat_keluar.id_obat', 'left')
            ->where('obat_keluar.tanggal_penjualan >=', $tanggal_mulai)
            ->where('obat_keluar.tanggal_penjualan <=', $tanggal_akhir)
            ->findAll();
        
        $data = [
            'title' => 'Laporan Obat Keluar',
            'obatKeluar' => $obatKeluar,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_akhir' => $tanggal_akhir
        ];
        
        return view('laporan/obat_keluar', $data);
    }
    
    // Method baru untuk filter hari ini
    public function filterObatKeluarHariIni()
    {
        $tanggal_hari_ini = date('Y-m-d');
        
        // Query untuk data hari ini saja
        $obatKeluar = $this->obatKeluarModel
            ->select('obat_keluar.*, data_stok_obat.harga_modal, data_stok_obat.harga_jual')
            ->join('data_stok_obat', 'data_stok_obat.id_obat = obat_keluar.id_obat', 'left')
            ->where('obat_keluar.tanggal_penjualan', $tanggal_hari_ini)
            ->findAll();
        
        $data = [
            'title' => 'Laporan Obat Keluar Hari Ini',
            'obatKeluar' => $obatKeluar,
            'tanggal_mulai' => $tanggal_hari_ini,
            'tanggal_akhir' => $tanggal_hari_ini
        ];
        
        return view('laporan/obat_keluar', $data);
    }
    
   
public function exportPdfObatKeluar()
{
    // Membuat objek TCPDF
    $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
    
    // Set informasi dokumen
    $pdf->SetCreator('Sistem Manajemen Obat');
    $pdf->SetAuthor('Admin');
    $pdf->SetTitle('Laporan Obat Keluar');
    
    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    // Set margin (increased left and right margins to center the table)
    $pdf->SetMargins(15, 20, 15);
    
    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 20);
    
    // Add a page
    $pdf->AddPage();
    
    // ===== HEADER WITH LOGO AND APOTEK NAME (CENTERED) =====
    // Logo path
    $logoPath = FCPATH . 'assets/adminlte/dist/img/logo-apotek.jpg';
    
    // Calculate center position for logo and text
    $pageWidth = $pdf->getPageWidth();
    $logoWidth = 15; // Reduced from 25
    $logoHeight = 15; // Reduced from 25
    $logoX = ($pageWidth - $logoWidth) / 2;
    
    // Check if logo exists
    if (file_exists($logoPath)) {
        // Add logo (centered)
        $pdf->Image($logoPath, $logoX, 20, $logoWidth, $logoHeight, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    }
    
    // Nama Apotek (centered below logo)
    $pdf->SetFont('helvetica', 'B', 16); // Reduced from 20
    $pdf->SetXY(15, 38); // Position below logo
    $pdf->Cell(0, 10, 'APOTEK-ARROZAQ', 0, 1, 'C');
    
    // Add minimal space after header (reduced from 10 to 5)
    $pdf->Ln(5);
    
    // ===== REPORT TITLE =====
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'LAPORAN OBAT KELUAR', 0, 1, 'C');
    
    // Periode
    $tanggal_mulai = $this->request->getGet('tanggal_mulai') ?? date('Y-m-01');
    $tanggal_akhir = $this->request->getGet('tanggal_akhir') ?? date('Y-m-d');
    
    $pdf->SetFont('helvetica', '', 11);
    $periodeText = 'Periode: ' . date('d-m-Y', strtotime($tanggal_mulai)) . ' s/d ' . date('d-m-Y', strtotime($tanggal_akhir));
    
    // Tambahkan keterangan "Hari Ini" jika filter adalah hari ini
    if ($tanggal_mulai == date('Y-m-d') && $tanggal_akhir == date('Y-m-d')) {
        $periodeText .= ' (Hari Ini)';
    }
    
    $pdf->Cell(0, 7, $periodeText, 0, 1, 'C');
    
    // Add a horizontal line separator
    $pdf->Ln(2);
    $pdf->SetLineWidth(0.5);
    $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + $pdf->getPageWidth() - 30, $pdf->GetY());
    $pdf->Ln(5);
    
    // Calculate table width to center it
    $tableWidth = 270; // Total width of our table (increased for new columns)
    $leftMargin = ($pageWidth - $tableWidth) / 2;
    
    // Reset left margin for table to center it
    $pdf->SetX($leftMargin);
    
    // Header tabel dengan styling
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor(220, 220, 220); // Light gray background for header
    
    // Column widths (adjusted for new columns)
    $colWidth = [30, 18, 50, 18, 25, 25, 35, 30, 44];
    
    $pdf->Cell($colWidth[0], 8, 'Kode Transaksi', 1, 0, 'C', true);
    $pdf->Cell($colWidth[1], 8, 'ID Obat', 1, 0, 'C', true);
    $pdf->Cell($colWidth[2], 8, 'Nama Obat', 1, 0, 'C', true);
    $pdf->Cell($colWidth[3], 8, 'Jumlah', 1, 0, 'C', true);
    $pdf->Cell($colWidth[4], 8, 'Satuan', 1, 0, 'C', true);
    $pdf->Cell($colWidth[5], 8, 'Tgl Jual', 1, 0, 'C', true);
    $pdf->Cell($colWidth[6], 8, 'Harga Jual', 1, 0, 'C', true);
    $pdf->Cell($colWidth[7], 8, 'Harga Total', 1, 0, 'C', true);
    $pdf->Cell($colWidth[8], 8, 'Tgl Kadaluwarsa', 1, 1, 'C', true);
    
    // Isi tabel dengan style alternating row colors
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetFillColor(245, 245, 245); // Very light gray for alternating rows
    
    // Ambil data obat keluar dengan join ke tabel data_stok_obat untuk mendapatkan harga_jual
    $obatKeluar = $this->obatKeluarModel
        ->select('obat_keluar.*, data_stok_obat.harga_jual')
        ->join('data_stok_obat', 'obat_keluar.id_obat = data_stok_obat.id_obat', 'left')
        ->where('tanggal_penjualan >=', $tanggal_mulai)
        ->where('tanggal_penjualan <=', $tanggal_akhir)
        ->findAll();
    
    $rowCount = 0;
    $totalKeseluruhan = 0;
    
    foreach ($obatKeluar as $row) {
        // Reset X position for each row to maintain table centering
        $pdf->SetX($leftMargin);
        
        // Alternating fill
        $fill = ($rowCount % 2 == 0) ? true : false;
        
        // Calculate harga total
        $hargaJual = $row['harga_jual'] ?? 0;
        $hargaTotal = $hargaJual * $row['jumlah'];
        $totalKeseluruhan += $hargaTotal;
        
        $pdf->Cell($colWidth[0], 7, $row['kode_transaksi'], 1, 0, 'C', $fill);
        $pdf->Cell($colWidth[1], 7, $row['id_obat'], 1, 0, 'C', $fill);
        $pdf->Cell($colWidth[2], 7, $row['nama_obat'], 1, 0, 'L', $fill);
        $pdf->Cell($colWidth[3], 7, $row['jumlah'], 1, 0, 'C', $fill);
        $pdf->Cell($colWidth[4], 7, $row['satuan'], 1, 0, 'C', $fill);
        $pdf->Cell($colWidth[5], 7, date('d-m-Y', strtotime($row['tanggal_penjualan'])), 1, 0, 'C', $fill);
        $pdf->Cell($colWidth[6], 7, 'Rp ' . number_format($hargaJual, 0, ',', '.'), 1, 0, 'R', $fill);
        $pdf->Cell($colWidth[7], 7, 'Rp ' . number_format($hargaTotal, 0, ',', '.'), 1, 0, 'R', $fill);
        $pdf->Cell($colWidth[8], 7, date('d-m-Y', strtotime($row['tanggal_kadaluwarsa'])), 1, 1, 'C', $fill);
        
        $rowCount++;
    }
    
    // If no data
    if (count($obatKeluar) == 0) {
        $pdf->SetX($leftMargin);
        $pdf->Cell(array_sum($colWidth), 10, 'Tidak ada data yang tersedia', 1, 1, 'C');
    } else {
        // Add total row
        $pdf->SetX($leftMargin);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(200, 200, 200);
        $pdf->Cell($colWidth[0] + $colWidth[1] + $colWidth[2] + $colWidth[3] + $colWidth[4] + $colWidth[5] + $colWidth[6], 8, 'TOTAL KESELURUHAN', 1, 0, 'C', true);
        $pdf->Cell($colWidth[7], 8, 'Rp ' . number_format($totalKeseluruhan, 0, ',', '.'), 1, 0, 'R', true);
        $pdf->Cell($colWidth[8], 8, '', 1, 1, 'C', true);
    }
    
    // Add footer with date generated
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->Cell(0, 10, 'Dicetak pada: ' . date('d/m/Y H:i:s'), 0, 0, 'R');
    
    // Output PDF
    $this->response->setContentType('application/pdf');
    $pdf->Output('laporan_obat_keluar.pdf', 'I');
}
    
    public function exportExcelObatKeluar()
    {
        // Membuat objek Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Get parameters
        $tanggal_mulai = $this->request->getGet('tanggal_mulai') ?? date('Y-m-01');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir') ?? date('Y-m-d');
        
        // Set judul kolom
        $judulLaporan = 'Laporan Obat Keluar';
        if ($tanggal_mulai == date('Y-m-d') && $tanggal_akhir == date('Y-m-d')) {
            $judulLaporan .= ' - Hari Ini';
        }
        
        $sheet->setCellValue('A1', $judulLaporan);
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Set periode
        $periodeText = 'Periode: ' . date('d-m-Y', strtotime($tanggal_mulai)) . ' s/d ' . date('d-m-Y', strtotime($tanggal_akhir));
        $sheet->setCellValue('A2', $periodeText);
        $sheet->mergeCells('A2:I2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Set header tabel
        $sheet->setCellValue('A4', 'Kode Transaksi');
        $sheet->setCellValue('B4', 'ID Obat');
        $sheet->setCellValue('C4', 'Nama Obat');
        $sheet->setCellValue('D4', 'Jumlah');
        $sheet->setCellValue('E4', 'Satuan');
        $sheet->setCellValue('F4', 'Tanggal Penjualan');
        $sheet->setCellValue('G4', 'Harga Jual');
        $sheet->setCellValue('H4', 'Harga Total');
        $sheet->setCellValue('I4', 'Tanggal Kadaluwarsa');
        
        $sheet->getStyle('A4:I4')->getFont()->setBold(true);
        $sheet->getStyle('A4:I4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A4:I4')->getFill()->getStartColor()->setARGB('FFCCCCCC');
        
        // Ambil data obat keluar dengan join ke tabel data_stok_obat untuk mendapatkan harga_jual
        $obatKeluar = $this->obatKeluarModel
            ->select('obat_keluar.*, data_stok_obat.harga_jual')
            ->join('data_stok_obat', 'obat_keluar.id_obat = data_stok_obat.id_obat', 'left')
            ->where('tanggal_penjualan >=', $tanggal_mulai)
            ->where('tanggal_penjualan <=', $tanggal_akhir)
            ->findAll();
        
        $row = 5;
        $totalKeseluruhan = 0;
        
        foreach ($obatKeluar as $data) {
            // Calculate harga total
            $hargaJual = $data['harga_jual'] ?? 0;
            $hargaTotal = $hargaJual * $data['jumlah'];
            $totalKeseluruhan += $hargaTotal;
            
            $sheet->setCellValue('A' . $row, $data['kode_transaksi']);
            $sheet->setCellValue('B' . $row, $data['id_obat']);
            $sheet->setCellValue('C' . $row, $data['nama_obat']);
            $sheet->setCellValue('D' . $row, $data['jumlah']);
            $sheet->setCellValue('E' . $row, $data['satuan']);
            $sheet->setCellValue('F' . $row, date('d-m-Y', strtotime($data['tanggal_penjualan'])));
            $sheet->setCellValue('G' . $row, $hargaJual);
            $sheet->setCellValue('H' . $row, $hargaTotal);
            $sheet->setCellValue('I' . $row, date('d-m-Y', strtotime($data['tanggal_kadaluwarsa'])));
            
            // Format currency for harga columns
            $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0');
            
            $row++;
        }
        
        // Add total row if there's data
        if (count($obatKeluar) > 0) {
            $sheet->setCellValue('A' . $row, '');
            $sheet->setCellValue('B' . $row, '');
            $sheet->setCellValue('C' . $row, '');
            $sheet->setCellValue('D' . $row, '');
            $sheet->setCellValue('E' . $row, '');
            $sheet->setCellValue('F' . $row, '');
            $sheet->setCellValue('G' . $row, 'TOTAL:');
            $sheet->setCellValue('H' . $row, $totalKeseluruhan);
            $sheet->setCellValue('I' . $row, '');
            
            // Style for total row
            $sheet->getStyle('G' . $row . ':H' . $row)->getFont()->setBold(true);
            $sheet->getStyle('G' . $row . ':H' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            $sheet->getStyle('G' . $row . ':H' . $row)->getFill()->getStartColor()->setARGB('FFDDDDDD');
            $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0');
        }
        
        // Auto size kolom
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set border untuk semua cell data
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $totalRowNum = count($obatKeluar) > 0 ? $row : $row - 1;
        $sheet->getStyle('A4:I' . $totalRowNum)->applyFromArray($styleArray);
        
        // Generate nama file
        $filename = 'laporan_obat_keluar';
        if ($tanggal_mulai == date('Y-m-d') && $tanggal_akhir == date('Y-m-d')) {
            $filename .= '_hari_ini';
        }
        $filename .= '.xlsx';
        
        // Set header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
    
    // ===== STOK OBAT =====
    public function stokObat()
    {
        $data = [
            'title' => 'Laporan Stok Obat',
            'stokObat' => $this->stokObatModel->getStokObatWithTanggalMasuk()
        ];
        
        return view('laporan/stok_obat', $data);
    }
    
    public function filterStokObat()
    {
        $tanggal_mulai = $this->request->getPost('tanggal_mulai');
        $tanggal_akhir = $this->request->getPost('tanggal_akhir');
        $cari = $this->request->getPost('cari');
        
        // Gunakan method dari model yang sudah ada
        $stokObat = $this->stokObatModel->getStokObatWithTanggalMasuk($cari);
        
        // Filter berdasarkan tanggal jika ada (lakukan di PHP karena sudah join)
        if (!empty($tanggal_mulai) && !empty($tanggal_akhir)) {
            $stokObat = array_filter($stokObat, function($item) use ($tanggal_mulai, $tanggal_akhir) {
                if (empty($item['tanggal_masuk'])) {
                    return false; // Exclude items without tanggal_masuk
                }
                
                $tanggal_item = date('Y-m-d', strtotime($item['tanggal_masuk']));
                return $tanggal_item >= $tanggal_mulai && $tanggal_item <= $tanggal_akhir;
            });
        }
        
        $data = [
            'title' => 'Laporan Stok Obat',
            'stokObat' => $stokObat,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_akhir' => $tanggal_akhir,
            'cari' => $cari
        ];
        
        return view('laporan/stok_obat', $data);
    }
    
    public function exportPdfStokObat()
    {
        // Membuat objek TCPDF
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        
        // Set informasi dokumen
        $pdf->SetCreator('Sistem Manajemen Obat');
        $pdf->SetAuthor('Admin');
        $pdf->SetTitle('Laporan Stok Obat');
        
        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Set margin
        $pdf->SetMargins(15, 20, 15);
        
        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 20);
        
        // Add a page
        $pdf->AddPage();
        
        // ===== HEADER WITH LOGO AND APOTEK NAME (CENTERED) =====
        // Logo path
        $logoPath = FCPATH . 'assets/adminlte/dist/img/logo-apotek.jpg';
        
        // Calculate center position for logo and text
        $pageWidth = $pdf->getPageWidth();
        $logoWidth = 15; // Reduced from 25
        $logoHeight = 15; // Reduced from 25
        $logoX = ($pageWidth - $logoWidth) / 2;
        
        // Check if logo exists
        if (file_exists($logoPath)) {
            // Add logo (centered)
            $pdf->Image($logoPath, $logoX, 20, $logoWidth, $logoHeight, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }
        
        // Nama Apotek (centered below logo)
        $pdf->SetFont('helvetica', 'B', 16); // Reduced from 20
        $pdf->SetXY(15, 38); // Position below logo
        $pdf->Cell(0, 10, 'APOTEK-ARROZAQ', 0, 1, 'C');
        
        // Add minimal space after header (reduced from 10 to 5)
        $pdf->Ln(5);
        
        // ===== REPORT TITLE =====
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'LAPORAN STOK OBAT', 0, 1, 'C');
        
        // Ambil parameter filter dari GET request
        $tanggal_mulai = $this->request->getGet('tanggal_mulai');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');
        $cari = $this->request->getGet('cari') ?? '';
        
        // Tampilkan periode jika ada filter tanggal
        $pdf->SetFont('helvetica', '', 11);
        if (!empty($tanggal_mulai) && !empty($tanggal_akhir)) {
            $pdf->Cell(0, 7, 'Periode: ' . date('d-m-Y', strtotime($tanggal_mulai)) . ' s/d ' . date('d-m-Y', strtotime($tanggal_akhir)), 0, 1, 'C');
        }
        $pdf->Cell(0, 7, 'Tanggal Cetak: ' . date('d-m-Y'), 0, 1, 'C');
        
        // Add a horizontal line separator
        $pdf->Ln(2);
        $pdf->SetLineWidth(0.5);
        
        // Get page dimensions - using fixed margins since we set them above
        $leftMargin = 15; // We set this margin above
        $rightMargin = 15; // We set this margin above
        $availableWidth = $pageWidth - $leftMargin - $rightMargin;
        
        // Draw line from left to right margin
        $pdf->Line($leftMargin, $pdf->GetY(), $pageWidth - $rightMargin, $pdf->GetY());
        $pdf->Ln(5);
        
        // Calculate table positioning
        $colWidth = [25, 80, 30, 30, 50, 50]; // Adjusted column widths
        $tableWidth = array_sum($colWidth);
        
        // Calculate starting X position to center the table
        $startX = $leftMargin + ($availableWidth - $tableWidth) / 2;
        
        // Header tabel dengan styling
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetFillColor(220, 220, 220); // Light gray background for header
        
        // Set X position for centered table
        $pdf->SetX($startX);
        
        $pdf->Cell($colWidth[0], 8, 'ID Obat', 1, 0, 'C', true);
        $pdf->Cell($colWidth[1], 8, 'Nama Obat', 1, 0, 'C', true);
        $pdf->Cell($colWidth[2], 8, 'Jumlah Stok', 1, 0, 'C', true);
        $pdf->Cell($colWidth[3], 8, 'Satuan', 1, 0, 'C', true);
        $pdf->Cell($colWidth[4], 8, 'Tanggal Masuk', 1, 0, 'C', true);
        $pdf->Cell($colWidth[5], 8, 'Tanggal Kadaluwarsa', 1, 1, 'C', true);
        
        // Isi tabel dengan style alternating row colors
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetFillColor(245, 245, 245); // Very light gray for alternating rows
        
        // Ambil data stok obat dengan filter yang sama seperti di filterStokObat()
        $stokObat = $this->stokObatModel->getStokObatWithTanggalMasuk($cari);
        
        // Filter berdasarkan tanggal_masuk jika ada (sama seperti di filterStokObat)
        if (!empty($tanggal_mulai) && !empty($tanggal_akhir)) {
            $stokObat = array_filter($stokObat, function($item) use ($tanggal_mulai, $tanggal_akhir) {
                if (empty($item['tanggal_masuk'])) {
                    return false; // Exclude items without tanggal_masuk
                }
                
                $tanggal_item = date('Y-m-d', strtotime($item['tanggal_masuk']));
                return $tanggal_item >= $tanggal_mulai && $tanggal_item <= $tanggal_akhir;
            });
        }
        
        $rowCount = 0;
        foreach ($stokObat as $row) {
            // Set X position for each row to maintain table centering
            $pdf->SetX($startX);
            
            // Alternating fill
            $fill = ($rowCount % 2 == 0) ? true : false;
            
            $pdf->Cell($colWidth[0], 7, $row['id_obat'], 1, 0, 'C', $fill);
            $pdf->Cell($colWidth[1], 7, $row['nama_obat'], 1, 0, 'L', $fill);
            $pdf->Cell($colWidth[2], 7, $row['jumlah_stok'], 1, 0, 'C', $fill);
            $pdf->Cell($colWidth[3], 7, $row['satuan'], 1, 0, 'C', $fill);
            $pdf->Cell($colWidth[4], 7, $row['tanggal_masuk'] ? date('d-m-Y', strtotime($row['tanggal_masuk'])) : '-', 1, 0, 'C', $fill);
            $pdf->Cell($colWidth[5], 7, date('d-m-Y', strtotime($row['tanggal_kadaluwarsa'])), 1, 1, 'C', $fill);
            
            $rowCount++;
        }
        
        // If no data
        if (count($stokObat) == 0) {
            $pdf->SetX($startX);
            $pdf->Cell($tableWidth, 10, 'Tidak ada data yang tersedia', 1, 1, 'C');
        }
        
        // Add footer with date generated
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->Cell(0, 10, 'Dicetak pada: ' . date('d/m/Y H:i:s'), 0, 0, 'R');
        
        // Output PDF
        $this->response->setContentType('application/pdf');
        $pdf->Output('laporan_stok_obat.pdf', 'I');
    }
    public function exportExcelStokObat()
    {
        // Membuat objek Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Ambil parameter filter dari GET request
        $tanggal_mulai = $this->request->getGet('tanggal_mulai');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');
        $cari = $this->request->getGet('cari') ?? '';
        
        // Set judul kolom
        $sheet->setCellValue('A1', 'Laporan Stok Obat');
        $sheet->mergeCells('A1:F1'); // Updated to F1 for 6 columns
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Tambahkan informasi periode jika ada filter
        if (!empty($tanggal_mulai) && !empty($tanggal_akhir)) {
            $sheet->setCellValue('A2', 'Periode: ' . date('d-m-Y', strtotime($tanggal_mulai)) . ' s/d ' . date('d-m-Y', strtotime($tanggal_akhir)));
            $sheet->mergeCells('A2:F2');
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $headerRow = 4;
        } else {
            $headerRow = 3;
        }
        
        // Set header tabel
        $sheet->setCellValue('A' . $headerRow, 'ID Obat');
        $sheet->setCellValue('B' . $headerRow, 'Nama Obat');
        $sheet->setCellValue('C' . $headerRow, 'Jumlah Stok');
        $sheet->setCellValue('D' . $headerRow, 'Satuan');
        $sheet->setCellValue('E' . $headerRow, 'Tanggal Masuk');
        $sheet->setCellValue('F' . $headerRow, 'Tanggal Kadaluwarsa');
        
        $sheet->getStyle('A' . $headerRow . ':F' . $headerRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $headerRow . ':F' . $headerRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A' . $headerRow . ':F' . $headerRow)->getFill()->getStartColor()->setARGB('FFCCCCCC');
        
        // Ambil data stok obat dengan filter yang sama seperti di filterStokObat()
        $stokObat = $this->stokObatModel->getStokObatWithTanggalMasuk($cari);
        
        // Filter berdasarkan tanggal_masuk jika ada (sama seperti di filterStokObat)
        if (!empty($tanggal_mulai) && !empty($tanggal_akhir)) {
            $stokObat = array_filter($stokObat, function($item) use ($tanggal_mulai, $tanggal_akhir) {
                if (empty($item['tanggal_masuk'])) {
                    return false; // Exclude items without tanggal_masuk
                }
                
                $tanggal_item = date('Y-m-d', strtotime($item['tanggal_masuk']));
                return $tanggal_item >= $tanggal_mulai && $tanggal_item <= $tanggal_akhir;
            });
        }
        
        $row = $headerRow + 1;
        foreach ($stokObat as $data) {
            $sheet->setCellValue('A' . $row, $data['id_obat']);
            $sheet->setCellValue('B' . $row, $data['nama_obat']);
            $sheet->setCellValue('C' . $row, $data['jumlah_stok']);
            $sheet->setCellValue('D' . $row, $data['satuan']);
            $sheet->setCellValue('E' . $row, $data['tanggal_masuk'] ? date('d-m-Y', strtotime($data['tanggal_masuk'])) : '-');
            $sheet->setCellValue('F' . $row, date('d-m-Y', strtotime($data['tanggal_kadaluwarsa'])));
            $row++;
        }
        
        // Auto size kolom
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set border untuk semua cell data
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A' . $headerRow . ':F' . ($row - 1))->applyFromArray($styleArray);
        
        // Set header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="laporan_stok_obat.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}