<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DataStokObatModel;
use App\Models\ObatKeluarModel;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Database;

class ObatKeluar extends BaseController
{
    protected $obatKeluarModel;
    protected $stokObatModel;
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->obatKeluarModel = new ObatKeluarModel();
        $this->stokObatModel = new DataStokObatModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Obat Keluar',
            'obatKeluar' => $this->obatKeluarModel->findAll()
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
    
    public function scanResult()
    {
        try {
            log_message('debug', 'ScanResult Request Body: ' . $this->request->getBody());
            
            $json = $this->request->getJSON(true); // true untuk dapat array
            
            if (empty($json)) {
                $json = $this->request->getPost();
                log_message('debug', 'Fallback ke POST data: ' . json_encode($json));
            }
            
            $id_obat = $json['id_obat'] ?? '';
            
            if (empty($id_obat)) {
                log_message('error', 'ID Obat kosong dalam request');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data ID Obat tidak ditemukan dalam request'
                ]);
            }
            
            $obat = $this->stokObatModel->find($id_obat);
            
            if ($obat) {
                return $this->response->setJSON([
                    'success' => true,
                    'obat' => $obat
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data obat dengan ID ' . $id_obat . ' tidak ditemukan'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error di scanResult: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function tambah()
    {
        $data = [
            'title' => 'Tambah Obat Keluar',
            'obat' => $this->stokObatModel->findAll()
        ];
        
        return view('obat/keluar/tambah', $data);
    }

    public function simpan()
    {
        $this->db->transStart();
        
        $id_obat = $this->request->getVar('id_obat');
        $jumlah = (int)$this->request->getVar('jumlah');
        
        $obat = $this->stokObatModel->find($id_obat);
        
        if (!$obat) {
            $this->db->transRollback();
            return redirect()->to('obat/keluar')->with('error', 'Data obat tidak ditemukan');
        }

        if ($obat['jumlah_stok'] < $jumlah) {
            $this->db->transRollback();
            return redirect()->to('obat/keluar/tambah')->with('error', 'Jumlah stok tidak mencukupi. Stok saat ini: ' . $obat['jumlah_stok']);
        }

        $dataObatKeluar = [
            'id_obat' => $id_obat,
            'nama_obat' => $obat['nama_obat'],
            'jumlah' => $jumlah,
            'satuan' => $obat['satuan'],
            'tanggal_penjualan' => $this->request->getVar('tanggal_penjualan'),
            'tanggal_kadaluwarsa' => $obat['tanggal_kadaluwarsa']
        ];
        
        $this->obatKeluarModel->insert($dataObatKeluar);

        $stokBaru = $obat['jumlah_stok'] - $jumlah;
        
        $this->stokObatModel->update($id_obat, [
            'jumlah_stok' => $stokBaru
        ]);
        
        $this->db->transComplete();
        
        if ($this->db->transStatus() === false) {
            return redirect()->to('obat/keluar')->with('error', 'Gagal menyimpan data obat keluar');
        }
        
        return redirect()->to('obat/keluar')->with('success', 'Data obat keluar berhasil disimpan');
    }

    public function edit($kode)
    {
        $obatKeluar = $this->obatKeluarModel->find($kode);
        
        if (empty($obatKeluar)) {
            return redirect()->to('obat/keluar')->with('error', 'Data obat keluar tidak ditemukan');
        }
        
        $data = [
            'title' => 'Edit Obat Keluar',
            'obatKeluar' => $obatKeluar,
            'obat' => $this->stokObatModel->findAll()
        ];
        
        return view('obat/keluar/edit', $data);
    }

    public function update($kode)
    {
        $this->db->transStart();

        $obatKeluarLama = $this->obatKeluarModel->find($kode);
        $jumlahLama = (int)$obatKeluarLama['jumlah'];
        $id_obat = $obatKeluarLama['id_obat'];

        $jumlahBaru = (int)$this->request->getVar('jumlah');
        
        $obat = $this->stokObatModel->find($id_obat);
        $stokSekarang = $obat['jumlah_stok'];

        if ($jumlahBaru > ($stokSekarang + $jumlahLama)) {
            $this->db->transRollback();
            return redirect()->to('obat/keluar/edit/' . $kode)->with('error', 
                'Stok tidak mencukupi. Stok tersedia: ' . $stokSekarang . 
                ', Total maksimal yang bisa dikeluarkan: ' . ($stokSekarang + $jumlahLama));
        }

        $dataObatKeluar = [
            'jumlah' => $jumlahBaru,
            'tanggal_penjualan' => $this->request->getVar('tanggal_penjualan')
        ];
        $this->obatKeluarModel->update($kode, $dataObatKeluar);

        // Update stok obat
        $stokBaru = ($stokSekarang + $jumlahLama) - $jumlahBaru;
        $this->stokObatModel->update($id_obat, [
            'jumlah_stok' => $stokBaru
        ]);
        
        $this->db->transComplete();
        
        if ($this->db->transStatus() === false) {
            return redirect()->to('obat/keluar/edit/' . $kode)->with('error', 'Gagal mengupdate data');
        }
        
        return redirect()->to('obat/keluar')->with('success', 'Data obat keluar berhasil diupdate');
    }

    public function hapus($kode)
    {
        $this->db->transStart();

        $obatKeluar = $this->obatKeluarModel->find($kode);
        
        if (empty($obatKeluar)) {
            return redirect()->to('obat/keluar')->with('error', 'Data obat keluar tidak ditemukan');
        }
        
        $jumlah = (int)$obatKeluar['jumlah'];
        $id_obat = $obatKeluar['id_obat'];

        $this->obatKeluarModel->delete($kode);

        $obat = $this->stokObatModel->find($id_obat);
        $stokBaru = $obat['jumlah_stok'] + $jumlah;
        
        $this->stokObatModel->update($id_obat, [
            'jumlah_stok' => $stokBaru
        ]);
        
        $this->db->transComplete();
        
        if ($this->db->transStatus() === false) {
            return redirect()->to('obat/keluar')->with('error', 'Gagal menghapus data');
        }
        
        return redirect()->to('obat/keluar')->with('success', 'Data obat keluar berhasil dihapus');
    }
}