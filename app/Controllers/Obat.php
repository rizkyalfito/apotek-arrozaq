<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DataStokObatModel;
use App\Models\ObatMasukModel;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Database;

class Obat extends BaseController
{
    protected $db;
    protected $obatModel;
    protected $obatMasukModel;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->obatModel = new DataStokObatModel();
        $this->obatMasukModel = new ObatMasukModel();
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
            'title' => 'Tambah Data Obat'
        ];
        return view('obat/tambah', $data);
    }

    public function simpan()
    {
        $this->db->transStart();
        $data = [
            'nama_obat' => $this->request->getVar('nama_obat'),
            'jumlah_stok' => $this->request->getVar('jumlah'),
            'satuan' => $this->request->getVar('satuan'),
            'tanggal_kadaluwarsa' => $this->request->getVar('expired'),
        ];

        $this->obatModel->insert($data);
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            session()->setFlashdata('error', 'Gagal menyimpan data.');
            return redirect()->to('/obat/tambah');
        }

        return redirect()->to('/obat')->with('success', 'Berhasil menyimpan data.');
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Data Obat',
            'obat' => $this->obatModel->find($id)
        ];

        if (empty($data['obat'])) {
            return redirect()->to('/obat')->with('error', 'Data obat tidak ada.');
        }

        return view('obat/edit', $data);
    }

    public function update($id)
    {
        $this->db->transStart();
        $data = [
            'nama_obat' => $this->request->getVar('nama_obat'),
            'jumlah_stok' => $this->request->getVar('jumlah'),
            'satuan' => $this->request->getVar('satuan'),
            'tanggal_kadaluwarsa' => $this->request->getVar('expired'),
        ];

        $this->obatModel->update([$id], $data);
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            session()->setFlashdata('error', 'Gagal menyimpan data.');
            return redirect()->to('/obat/edit/' . $id);
        }

        return redirect()->to('/obat')->with('success', 'Berhasil menyimpan data.');
    }

    public function hapus($id)
    {
        $this->db->transStart();
        $obat = $this->obatModel->find($id);

        if (empty($obat)) {
            return redirect()->to('obat')->with('error', 'Data obat tidak ada.');
        }

        $this->obatModel->delete($id);
        $this->db->transComplete();

        return redirect()->to('obat')->with('success', 'Berhasil menghapus data.');
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
