<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DataStokObatModel;
use App\Models\ObatMasukModel;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Database;

class ObatMasuk extends BaseController
{
    protected $obatMasukModel;
    protected $stokObatModel;
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->obatMasukModel = new ObatMasukModel();
        $this->stokObatModel = new DataStokObatModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Obat Masuk',
            'obatMasuk' => $this->obatMasukModel->findAll()
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

    public function scanResult()
    {
        $json = $this->request->getJSON();
        $id_obat = $json->id_obat ?? '';

        $obat = $this->stokObatModel->find($id_obat);
        
        if ($obat) {
            return $this->response->setJSON([
                'success' => true,
                'obat' => $obat
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data obat tidak ditemukan'
            ]);
        }
    }

    public function tambah()
    {
        $data = [
            'title' => 'Tambah Obat Masuk',
            'obat' => $this->stokObatModel->findAll()
        ];
        
        return view('obat/masuk/tambah', $data);
    }

    public function simpan()
    {
        $this->db->transStart();
        
        $id_obat = $this->request->getVar('id_obat');
        $jumlah = (int)$this->request->getVar('jumlah');

        $obat = $this->stokObatModel->find($id_obat);
        
        if (!$obat) {
            $this->db->transRollback();
            return redirect()->to('obat/masuk')->with('error', 'Data obat tidak ditemukan');
        }

        $dataObatMasuk = [
            'id_obat' => $id_obat,
            'nama_obat' => $obat['nama_obat'],
            'jumlah' => $jumlah,
            'satuan' => $obat['satuan'],
            'tanggal_masuk' => date('Y-m-d'),
            'tanggal_kadaluwarsa' => $this->request->getVar('tanggal_kadaluwarsa')
        ];
        
        $this->obatMasukModel->insert($dataObatMasuk);

        $stokBaru = $obat['jumlah_stok'] + $jumlah;
        
        $this->stokObatModel->update($id_obat, [
            'jumlah_stok' => $stokBaru,
            'tanggal_kadaluwarsa' => $this->request->getVar('tanggal_kadaluwarsa')
        ]);
        
        $this->db->transComplete();
        
        if ($this->db->transStatus() === false) {
            return redirect()->to('obat/masuk')->with('error', 'Gagal menyimpan data obat masuk');
        }
        
        return redirect()->to('obat/masuk')->with('pesan', 'Data obat masuk berhasil disimpan');
    }

    public function edit($id)
    {
        $obatMasuk = $this->obatMasukModel->find($id);
        
        if (empty($obatMasuk)) {
            return redirect()->to('obat/masuk')->with('error', 'Data obat masuk tidak ditemukan');
        }
        
        $data = [
            'title' => 'Edit Obat Masuk',
            'obatMasuk' => $obatMasuk,
            'obat' => $this->stokObatModel->findAll()
        ];
        
        return view('obat/masuk/edit', $data);
    }

    public function update($id){
        $this->db->transStart();
        $obatMasukLama = $this->obatMasukModel->find($id);
    
    if (empty($obatMasukLama)) {
        return redirect()->to('obat/masuk')->with('error', 'Data obat masuk tidak ditemukan');
    }
    
    $id_obat = $obatMasukLama['id_obat'];
    $jumlahLama = (int)$obatMasukLama['jumlah'];
    $jumlahBaru = (int)$this->request->getVar('jumlah');
    $selisih = $jumlahBaru - $jumlahLama;

    $dataObatMasuk = [
        'jumlah' => $jumlahBaru,
        'tanggal_masuk' => $this->request->getVar('tanggal_masuk'),
        'tanggal_kadaluwarsa' => $this->request->getVar('tanggal_kadaluwarsa')
    ];
    
    $this->obatMasukModel->update($id, $dataObatMasuk);

    $obat = $this->stokObatModel->find($id_obat);
    if ($obat) {
        $stokBaru = $obat['jumlah_stok'] + $selisih;
        
        $this->stokObatModel->update($id_obat, [
            'jumlah_stok' => $stokBaru,
            'tanggal_kadaluwarsa' => $this->request->getVar('tanggal_kadaluwarsa')
        ]);
    }
    
    $this->db->transComplete();
    
    if ($this->db->transStatus() === false) {
        return redirect()->to('obat/masuk/edit/' . $id)->with('error', 'Gagal mengupdate data');
    }
    
    return redirect()->to('obat/masuk')->with('pesan', 'Data obat masuk berhasil diupdate');
}

public function hapus($id)
{
    $this->db->transStart();
    
    $obatMasuk = $this->obatMasukModel->find($id);
    
    if (empty($obatMasuk)) {
        return redirect()->to('obat/masuk')->with('error', 'Data obat masuk tidak ditemukan');
    }
    
    $id_obat = $obatMasuk['id_obat'];
    $jumlah = (int)$obatMasuk['jumlah'];
    
    $this->obatMasukModel->delete($id);
    
    $obat = $this->stokObatModel->find($id_obat);
    if ($obat) {
        $stokBaru = $obat['jumlah_stok'] - $jumlah;
        
        if ($stokBaru < 0) {
            $stokBaru = 0;
        }
        
        $this->stokObatModel->update($id_obat, [
            'jumlah_stok' => $stokBaru
        ]);
    }
    
    $this->db->transComplete();
    
    if ($this->db->transStatus() === false) {
        return redirect()->to('obat/masuk')->with('error', 'Gagal menghapus data');
    }
    
    return redirect()->to('obat/masuk')->with('pesan', 'Data obat masuk berhasil dihapus');
}
}