<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Laporan extends BaseController
{
    public function obatMasuk()
    {
        $data = [
            'title' => 'Laporan Obat Masuk'
        ];
        
        return view('laporan/obat_masuk', $data);
    }

    public function obatKeluar()
    {
        $data = [
            'title' => 'Laporan Obat Keluar'
        ];
        
        return view('laporan/obat_keluar', $data);
    }

    public function stokObat()
    {
        $data = [
            'title' => 'Laporan Stok Obat'
        ];
        
        return view('laporan/stok_obat', $data);
    }
}