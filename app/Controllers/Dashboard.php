<?php

namespace App\Controllers;

use App\Models\DataStokObatModel;
use App\Models\ObatMasukModel;
use App\Models\ObatKeluarModel;

class Dashboard extends BaseController
{
    protected $dataStokObatModel;
    protected $obatMasukModel;
    protected $obatKeluarModel;

    public function __construct()
    {
        $this->dataStokObatModel = new DataStokObatModel();
        $this->obatMasukModel = new ObatMasukModel();
        $this->obatKeluarModel = new ObatKeluarModel();
    }

    public function index()
    {
        // Get total number of medications
        $totalObat = $this->dataStokObatModel->countAll();
        
        // Get total incoming medications for current month
        $startDate = date('Y-m-01'); // First day of current month
        $endDate = date('Y-m-t');    // Last day of current month
        $obatMasukBulanIni = $this->obatMasukModel
            ->where('tanggal_masuk >=', $startDate)
            ->where('tanggal_masuk <=', $endDate)
            ->countAllResults();
        
        // Get total outgoing medications for current month
        $obatKeluarBulanIni = $this->obatKeluarModel
            ->where('tanggal_penjualan >=', $startDate)
            ->where('tanggal_penjualan <=', $endDate)
            ->countAllResults();
        
        // Get medications with low stock (below 10 items)
        $obatHampirHabis = $this->dataStokObatModel
            ->where('jumlah_stok <', 10)
            ->countAllResults();
        
        // Get latest incoming medications (limit 5)
        $obatTerbaruMasuk = $this->obatMasukModel
            ->orderBy('tanggal_masuk', 'DESC')
            ->limit(5)
            ->find();
        
        // Get latest outgoing medications (limit 5)
        $obatTerbaruKeluar = $this->obatKeluarModel
            ->orderBy('tanggal_penjualan', 'DESC')
            ->limit(5)
            ->find();
        
        $data = [
            'title' => 'Dashboard',
            'totalObat' => $totalObat,
            'obatMasukBulanIni' => $obatMasukBulanIni,
            'obatKeluarBulanIni' => $obatKeluarBulanIni,
            'obatHampirHabis' => $obatHampirHabis,
            'obatTerbaruMasuk' => $obatTerbaruMasuk,
            'obatTerbaruKeluar' => $obatTerbaruKeluar
        ];
        
        return view('dashboard/index', $data);
    }
}