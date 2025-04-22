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
        
        // Set font
        $pdf->SetFont('helvetica', 'B', 16);
        
        // Title
        $pdf->Cell(0, 10, 'LAPORAN OBAT MASUK', 0, 1, 'C');
        
        // Periode
        $tanggal_mulai = $this->request->getGet('tanggal_mulai') ?? date('Y-m-01');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir') ?? date('Y-m-d');
        
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 7, 'Periode: ' . date('d-m-Y', strtotime($tanggal_mulai)) . ' s/d ' . date('d-m-Y', strtotime($tanggal_akhir)), 0, 1, 'C');
        
        // Add a horizontal line separator
        $pdf->Ln(2);
        $pdf->SetLineWidth(0.5);
        $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + $pdf->getPageWidth() - 40, $pdf->GetY());
        $pdf->Ln(5);
        
        // Calculate table width to center it
        $pageWidth = $pdf->getPageWidth();
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
        
        // Ambil data obat masuk
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
            $pdf->Cell(array_sum($colWidth), 10, 'Tidak ada data yang tersedia', 1, 1, 'C');
        }
        
        // Add footer with date generated
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->Cell(0, 10, 'Dicetak pada: ' . date('d/m/Y H:i:s'), 0, 0, 'R');
        
        // Output PDF
        $this->response->setContentType('application/pdf');
        $pdf->Output('laporan_obat_masuk.pdf', 'I');
    }

    public function exportExcelObatMasuk()
    {
        // Membuat objek Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set judul kolom
        $sheet->setCellValue('A1', 'Laporan Obat Masuk');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Set header tabel
        $sheet->setCellValue('A3', 'ID Obat');
        $sheet->setCellValue('B3', 'Nama Obat');
        $sheet->setCellValue('C3', 'Jumlah');
        $sheet->setCellValue('D3', 'Satuan');
        $sheet->setCellValue('E3', 'Tanggal Masuk');
        $sheet->setCellValue('F3', 'Tanggal Kadaluwarsa');
        
        $sheet->getStyle('A3:F3')->getFont()->setBold(true);
        $sheet->getStyle('A3:F3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A3:F3')->getFill()->getStartColor()->setARGB('FFCCCCCC');
        
        // Ambil data obat masuk
        $obatMasuk = $this->obatMasukModel->findAll();
        
        $row = 4;
        foreach ($obatMasuk as $data) {
            $sheet->setCellValue('A' . $row, $data['id_obat']);
            $sheet->setCellValue('B' . $row, $data['nama_obat']);
            $sheet->setCellValue('C' . $row, $data['jumlah']);
            $sheet->setCellValue('D' . $row, $data['satuan']);
            $sheet->setCellValue('E' . $row, date('d-m-Y', strtotime($data['tanggal_masuk'])));
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
        $sheet->getStyle('A3:F' . ($row - 1))->applyFromArray($styleArray);
        
        // Set header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="laporan_obat_masuk.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    // ===== OBAT KELUAR =====
    public function obatKeluar()
    {
        // Default: Tampilkan semua data obat keluar
        $data = [
            'title' => 'Laporan Obat Keluar',
            'obatKeluar' => $this->obatKeluarModel->findAll()
        ];
        
        return view('laporan/obat_keluar', $data);
    }
    
    public function filterObatKeluar()
    {
        $tanggal_mulai = $this->request->getPost('tanggal_mulai');
        $tanggal_akhir = $this->request->getPost('tanggal_akhir');
        
        // Query dengan filter tanggal
        $obatKeluar = $this->obatKeluarModel
            ->where('tanggal_penjualan >=', $tanggal_mulai)
            ->where('tanggal_penjualan <=', $tanggal_akhir)
            ->findAll();
        
        $data = [
            'title' => 'Laporan Obat Keluar',
            'obatKeluar' => $obatKeluar,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_akhir' => $tanggal_akhir
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
        $pdf->SetMargins(20, 20, 20);
        
        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 20);
        
        // Add a page
        $pdf->AddPage();
        
        // Set font
        $pdf->SetFont('helvetica', 'B', 16);
        
        // Title
        $pdf->Cell(0, 10, 'LAPORAN OBAT KELUAR', 0, 1, 'C');
        
        // Periode
        $tanggal_mulai = $this->request->getGet('tanggal_mulai') ?? date('Y-m-01');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir') ?? date('Y-m-d');
        
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 7, 'Periode: ' . date('d-m-Y', strtotime($tanggal_mulai)) . ' s/d ' . date('d-m-Y', strtotime($tanggal_akhir)), 0, 1, 'C');
        
        // Add a horizontal line separator
        $pdf->Ln(2);
        $pdf->SetLineWidth(0.5);
        $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + $pdf->getPageWidth() - 40, $pdf->GetY());
        $pdf->Ln(5);
        
        // Calculate table width to center it
        $pageWidth = $pdf->getPageWidth();
        $tableWidth = 250; // Total width of our table
        $leftMargin = ($pageWidth - $tableWidth) / 2;
        
        // Reset left margin for table to center it
        $pdf->SetX($leftMargin);
        
        // Header tabel dengan styling
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetFillColor(220, 220, 220); // Light gray background for header
        
        // Column widths
        $colWidth = [30, 20, 60, 20, 30, 30, 60];
        
        $pdf->Cell($colWidth[0], 8, 'Kode Transaksi', 1, 0, 'C', true);
        $pdf->Cell($colWidth[1], 8, 'ID Obat', 1, 0, 'C', true);
        $pdf->Cell($colWidth[2], 8, 'Nama Obat', 1, 0, 'C', true);
        $pdf->Cell($colWidth[3], 8, 'Jumlah', 1, 0, 'C', true);
        $pdf->Cell($colWidth[4], 8, 'Satuan', 1, 0, 'C', true);
        $pdf->Cell($colWidth[5], 8, 'Tanggal Jual', 1, 0, 'C', true);
        $pdf->Cell($colWidth[6], 8, 'Tanggal Kadaluwarsa', 1, 1, 'C', true);
        
        // Isi tabel dengan style alternating row colors
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetFillColor(245, 245, 245); // Very light gray for alternating rows
        
        // Ambil data obat keluar
        $obatKeluar = $this->obatKeluarModel
            ->where('tanggal_penjualan >=', $tanggal_mulai)
            ->where('tanggal_penjualan <=', $tanggal_akhir)
            ->findAll();
        
        $rowCount = 0;
        foreach ($obatKeluar as $row) {
            // Reset X position for each row to maintain table centering
            $pdf->SetX($leftMargin);
            
            // Alternating fill
            $fill = ($rowCount % 2 == 0) ? true : false;
            
            $pdf->Cell($colWidth[0], 7, $row['kode_transaksi'], 1, 0, 'C', $fill);
            $pdf->Cell($colWidth[1], 7, $row['id_obat'], 1, 0, 'C', $fill);
            $pdf->Cell($colWidth[2], 7, $row['nama_obat'], 1, 0, 'L', $fill);
            $pdf->Cell($colWidth[3], 7, $row['jumlah'], 1, 0, 'C', $fill);
            $pdf->Cell($colWidth[4], 7, $row['satuan'], 1, 0, 'C', $fill);
            $pdf->Cell($colWidth[5], 7, date('d-m-Y', strtotime($row['tanggal_penjualan'])), 1, 0, 'C', $fill);
            $pdf->Cell($colWidth[6], 7, date('d-m-Y', strtotime($row['tanggal_kadaluwarsa'])), 1, 1, 'C', $fill);
            
            $rowCount++;
        }
        
        // If no data
        if (count($obatKeluar) == 0) {
            $pdf->SetX($leftMargin);
            $pdf->Cell(array_sum($colWidth), 10, 'Tidak ada data yang tersedia', 1, 1, 'C');
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
        
        // Set judul kolom
        $sheet->setCellValue('A1', 'Laporan Obat Keluar');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Set header tabel
        $sheet->setCellValue('A3', 'Kode Transaksi');
        $sheet->setCellValue('B3', 'ID Obat');
        $sheet->setCellValue('C3', 'Nama Obat');
        $sheet->setCellValue('D3', 'Jumlah');
        $sheet->setCellValue('E3', 'Satuan');
        $sheet->setCellValue('F3', 'Tanggal Penjualan');
        $sheet->setCellValue('G3', 'Tanggal Kadaluwarsa');
        
        $sheet->getStyle('A3:G3')->getFont()->setBold(true);
        $sheet->getStyle('A3:G3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A3:G3')->getFill()->getStartColor()->setARGB('FFCCCCCC');
        
        // Ambil data obat keluar
        $tanggal_mulai = $this->request->getGet('tanggal_mulai') ?? date('Y-m-01');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir') ?? date('Y-m-d');
        
        $obatKeluar = $this->obatKeluarModel
            ->where('tanggal_penjualan >=', $tanggal_mulai)
            ->where('tanggal_penjualan <=', $tanggal_akhir)
            ->findAll();
        
        $row = 4;
        foreach ($obatKeluar as $data) {
            $sheet->setCellValue('A' . $row, $data['kode_transaksi']);
            $sheet->setCellValue('B' . $row, $data['id_obat']);
            $sheet->setCellValue('C' . $row, $data['nama_obat']);
            $sheet->setCellValue('D' . $row, $data['jumlah']);
            $sheet->setCellValue('E' . $row, $data['satuan']);
            $sheet->setCellValue('F' . $row, date('d-m-Y', strtotime($data['tanggal_penjualan'])));
            $sheet->setCellValue('G' . $row, date('d-m-Y', strtotime($data['tanggal_kadaluwarsa'])));
            $row++;
        }
        
        // Auto size kolom
        foreach (range('A', 'G') as $col) {
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
        $sheet->getStyle('A3:G' . ($row - 1))->applyFromArray($styleArray);
        
        // Set header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="laporan_obat_keluar.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
    
    // ===== STOK OBAT =====
    public function stokObat()
    {
        $data = [
            'title' => 'Laporan Stok Obat',
            'stokObat' => $this->stokObatModel->findAll()
        ];
        
        return view('laporan/stok_obat', $data);
    }
    
    public function filterStokObat()
    {
        $cari = $this->request->getPost('cari');
        
        if (!empty($cari)) {
            $stokObat = $this->stokObatModel
                ->like('nama_obat', $cari)
                ->orLike('id_obat', $cari)
                ->findAll();
        } else {
            $stokObat = $this->stokObatModel->findAll();
        }
        
        $data = [
            'title' => 'Laporan Stok Obat',
            'stokObat' => $stokObat,
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
        
        // Set margin (increased left and right margins to center the table)
        $pdf->SetMargins(20, 20, 20);
        
        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 20);
        
        // Add a page
        $pdf->AddPage();
        
        // Set font
        $pdf->SetFont('helvetica', 'B', 16);
        
        // Title
        $pdf->Cell(0, 10, 'LAPORAN STOK OBAT', 0, 1, 'C');
        
        // Tanggal cetak
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 7, 'Tanggal: ' . date('d-m-Y'), 0, 1, 'C');
        
        // Add a horizontal line separator
        $pdf->Ln(2);
        $pdf->SetLineWidth(0.5);
        $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + $pdf->getPageWidth() - 40, $pdf->GetY());
        $pdf->Ln(5);
        
        // Calculate table width to center it
        $pageWidth = $pdf->getPageWidth();
        $tableWidth = 220; // Total width of our table
        $leftMargin = ($pageWidth - $tableWidth) / 2;
        
        // Reset left margin for table to center it
        $pdf->SetX($leftMargin);
        
        // Header tabel dengan styling
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetFillColor(220, 220, 220); // Light gray background for header
        
        // Column widths
        $colWidth = [20, 80, 30, 30, 60];
        
        $pdf->Cell($colWidth[0], 8, 'ID Obat', 1, 0, 'C', true);
        $pdf->Cell($colWidth[1], 8, 'Nama Obat', 1, 0, 'C', true);
        $pdf->Cell($colWidth[2], 8, 'Jumlah Stok', 1, 0, 'C', true);
        $pdf->Cell($colWidth[3], 8, 'Satuan', 1, 0, 'C', true);
        $pdf->Cell($colWidth[4], 8, 'Tanggal Kadaluwarsa', 1, 1, 'C', true);
        
        // Isi tabel dengan style alternating row colors
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetFillColor(245, 245, 245); // Very light gray for alternating rows
        
        // Ambil data stok obat
        $cari = $this->request->getGet('cari') ?? '';
        
        if (!empty($cari)) {
            $stokObat = $this->stokObatModel
                ->like('nama_obat', $cari)
                ->orLike('id_obat', $cari)
                ->findAll();
        } else {
            $stokObat = $this->stokObatModel->findAll();
        }
        
        $rowCount = 0;
        foreach ($stokObat as $row) {
            // Reset X position for each row to maintain table centering
            $pdf->SetX($leftMargin);
            
            // Alternating fill
            $fill = ($rowCount % 2 == 0) ? true : false;
            
            $pdf->Cell($colWidth[0], 7, $row['id_obat'], 1, 0, 'C', $fill);
            $pdf->Cell($colWidth[1], 7, $row['nama_obat'], 1, 0, 'L', $fill);
            $pdf->Cell($colWidth[2], 7, $row['jumlah_stok'], 1, 0, 'C', $fill);
            $pdf->Cell($colWidth[3], 7, $row['satuan'], 1, 0, 'C', $fill);
            $pdf->Cell($colWidth[4], 7, date('d-m-Y', strtotime($row['tanggal_kadaluwarsa'])), 1, 1, 'C', $fill);
            
            $rowCount++;
        }
        
        // If no data
        if (count($stokObat) == 0) {
            $pdf->SetX($leftMargin);
            $pdf->Cell(array_sum($colWidth), 10, 'Tidak ada data yang tersedia', 1, 1, 'C');
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
        
        // Set judul kolom
        $sheet->setCellValue('A1', 'Laporan Stok Obat');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Set header tabel
        $sheet->setCellValue('A3', 'ID Obat');
        $sheet->setCellValue('B3', 'Nama Obat');
        $sheet->setCellValue('C3', 'Jumlah Stok');
        $sheet->setCellValue('D3', 'Satuan');
        $sheet->setCellValue('E3', 'Tanggal Kadaluwarsa');
        
        $sheet->getStyle('A3:E3')->getFont()->setBold(true);
        $sheet->getStyle('A3:E3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A3:E3')->getFill()->getStartColor()->setARGB('FFCCCCCC');
        
        // Ambil data stok obat
        $cari = $this->request->getGet('cari') ?? '';
        
        if (!empty($cari)) {
            $stokObat = $this->stokObatModel
                ->like('nama_obat', $cari)
                ->orLike('id_obat', $cari)
                ->findAll();
        } else {
            $stokObat = $this->stokObatModel->findAll();
        }
        
        $row = 4;
        foreach ($stokObat as $data) {
            $sheet->setCellValue('A' . $row, $data['id_obat']);
            $sheet->setCellValue('B' . $row, $data['nama_obat']);
            $sheet->setCellValue('C' . $row, $data['jumlah_stok']);
            $sheet->setCellValue('D' . $row, $data['satuan']);
            $sheet->setCellValue('E' . $row, date('d-m-Y', strtotime($data['tanggal_kadaluwarsa'])));
            $row++;
        }
        
        // Auto size kolom
        foreach (range('A', 'E') as $col) {
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
        $sheet->getStyle('A3:E' . ($row - 1))->applyFromArray($styleArray);
        
        // Set header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="laporan_stok_obat.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}