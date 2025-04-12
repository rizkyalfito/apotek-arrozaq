<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class ObatMasuk extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Obat Masuk'
        ];
        
        return view('obat/masuk/index', $data);
    }

    public function scan()
    {
        $data = [
            'title' => 'Scan QR Code'
        ];
        
        return view('obat/masuk/scan', $data);
    }
}