<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class ObatKeluar extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Obat Keluar'
        ];
        
        return view('obat/keluar/index', $data);
    }

    public function scan()
    {
        $data = [
            'title' => 'Scan QR Code'
        ];
        
        return view('obat/keluar/scan', $data);
    }
}