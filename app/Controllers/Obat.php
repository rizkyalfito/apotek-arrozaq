<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DataStokObatModel;
use CodeIgniter\HTTP\ResponseInterface;

class Obat extends BaseController
{
    protected $obatModel;

    public function __construct()
    {
        $this->obatModel = new DataStokObatModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Data Stok Obat',
            'obat' => $this->obatModel->findAll()
        ];
        return view('obat/index', $data);
    }

    public function tambah()
    {
        $data = [
            'title' => 'Tambah Data Obat',
            'validation' => \Config\Services::validation()
        ];
        return view('obat/tambah', $data);
    }

    public function simpan()
    {
        // Validasi input
        $rules = [
            'nama_obat' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama obat harus diisi'
                ]
            ],
            'jumlah_stok' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'Jumlah stok harus diisi',
                    'numeric' => 'Jumlah stok harus berupa angka'
                ]
            ],
            'satuan' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Satuan harus diisi'
                ]
            ],
            'tanggal_kadaluwarsa' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tanggal kadaluwarsa harus diisi'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('obat/tambah')->withInput()->with('validation', $this->validator);
        }

        $this->obatModel->save([
            'nama_obat' => $this->request->getVar('nama_obat'),
            'jumlah_stok' => $this->request->getVar('jumlah_stok'),
            'satuan' => $this->request->getVar('satuan'),
            'tanggal_kadaluwarsa' => $this->request->getVar('tanggal_kadaluwarsa')
        ]);

        session()->setFlashdata('pesan', 'Data berhasil ditambahkan');
        return redirect()->to('obat');
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Data Obat',
            'validation' => \Config\Services::validation(),
            'obat' => $this->obatModel->find($id)
        ];

        if (empty($data['obat'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data obat tidak ditemukan');
        }

        return view('obat/edit', $data);
    }

    public function update()
    {
        // Validasi input
        $rules = [
            'nama_obat' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama obat harus diisi'
                ]
            ],
            'jumlah_stok' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'Jumlah stok harus diisi',
                    'numeric' => 'Jumlah stok harus berupa angka'
                ]
            ],
            'satuan' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Satuan harus diisi'
                ]
            ],
            'tanggal_kadaluwarsa' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Tanggal kadaluwarsa harus diisi'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('obat/edit/' . $this->request->getVar('id_obat'))->withInput()->with('validation', $this->validator);
        }

        $this->obatModel->update($this->request->getVar('id_obat'), [
            'nama_obat' => $this->request->getVar('nama_obat'),
            'jumlah_stok' => $this->request->getVar('jumlah_stok'),
            'satuan' => $this->request->getVar('satuan'),
            'tanggal_kadaluwarsa' => $this->request->getVar('tanggal_kadaluwarsa')
        ]);

        session()->setFlashdata('pesan', 'Data berhasil diupdate');
        return redirect()->to('obat');
    }

    public function hapus($id)
    {
        $obat = $this->obatModel->find($id);
        
        if (empty($obat)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data obat tidak ditemukan');
        }

        $this->obatModel->delete($id);
        session()->setFlashdata('pesan', 'Data berhasil dihapus');
        return redirect()->to('obat');
    }

    public function generateQR($id)
    {
        $obat = $this->obatModel->find($id);
        
        if (empty($obat)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data obat tidak ditemukan');
        }

        $data = [
            'title' => 'QR Code: ' . $obat['nama_obat'],
            'obat' => $obat
        ];

        return view('obat/qr_code', $data);
    }

    public function scan()
    {
        $data = [
            'title' => 'Scan QR Code Obat'
        ];
        return view('obat/scan', $data);
    }

    public function scanResult()
    {
        $qrData = $this->request->getPost('qr_data');
        
        if (empty($qrData)) {
            return redirect()->to('obat/scan')->with('error', 'Data QR Code tidak valid');
        }

        try {
            $jsonData = json_decode($qrData, true);
            
            if (isset($jsonData['id_obat'])) {
                $obat = $this->obatModel->find($jsonData['id_obat']);
                
                if ($obat) {
                    return redirect()->to('obat/edit/' . $obat['id_obat'])->with('success', 'Data obat ditemukan');
                }
            }
            
            return redirect()->to('obat/scan')->with('error', 'Data obat tidak ditemukan');
            
        } catch (\Exception $e) {
            return redirect()->to('obat/scan')->with('error', 'Format QR Code tidak valid');
        }
    }
}