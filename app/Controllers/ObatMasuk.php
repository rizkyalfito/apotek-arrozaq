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

    // Contoh perbaikan untuk controller "Obat/Masuk" di CodeIgniter
public function scanResult() {
    // Pastikan request berupa AJAX
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request method'
        ]);
    }
    
    // Ambil data dari request JSON
    $json = $this->request->getJSON(true);
    
    // Log untuk troubleshooting
    log_message('info', 'Barcode scan received: ' . json_encode($json));
    
    if (!isset($json['id_obat'])) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'ID Obat tidak ditemukan pada request'
        ]);
    }
    
    // Bersihkan ID obat dari karakter yang tidak diinginkan
    $idObat = trim($json['id_obat']);
    
    // Log ID yang akan dicari
    log_message('info', 'Searching for medicine with ID: ' . $idObat);
    
    // Jika ID merupakan digit pertama dari kode yang lebih panjang, 
    // coba cari dengan wildcard atau regex
    $obatModel = new DataStokObatModel();
    
    // Coba pencarian persis
    $obat = $obatModel->where('id_obat', $idObat)->first();
    
    // Jika tidak ditemukan, coba cek apakah ID ini merupakan bagian dari ID lain
    // Ini untuk kasus di mana scanner hanya membaca sebagian dari barcode
    if (!$obat) {
        log_message('info', 'Exact match not found, trying alternative search');
        
        // Untuk database MySQL
        $obat = $obatModel->like('id_obat', $idObat)->first();
        
        // Jika database yang digunakan adalah PostgreSQL, gunakan ini:
        // $obat = $obatModel->where("id_obat::text LIKE ?", ['%' . $idObat . '%'])->first();
    }
    
    if ($obat) {
        log_message('info', 'Obat found: ' . json_encode($obat));
        return $this->response->setJSON([
            'success' => true,
            'obat' => $obat
        ]);
    } else {
        log_message('info', 'Obat not found for ID: ' . $idObat);
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