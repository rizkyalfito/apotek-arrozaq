<?php

namespace App\Models;

use CodeIgniter\Model;

class ObatKeluarModel extends Model
{
    protected $table            = 'obat_keluar';
    protected $primaryKey       = 'kode_transaksi';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'satuan',
        'id_obat',
        'tanggal_penjualan',
        'nama_obat',
        'jumlah',
        'tanggal_kadaluwarsa',
        'harga_jual' // Menambahkan harga_jual jika ingin menyimpan di tabel obat_keluar
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    // Method untuk mendapatkan data obat keluar dengan harga jual
    public function getObatKeluarWithHarga($tanggal_mulai = null, $tanggal_akhir = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('obat_keluar.*, data_stok_obat.harga_jual');
        $builder->join('data_stok_obat', 'obat_keluar.id_obat = data_stok_obat.id_obat', 'left');
        
        if ($tanggal_mulai) {
            $builder->where('tanggal_penjualan >=', $tanggal_mulai);
        }
        
        if ($tanggal_akhir) {
            $builder->where('tanggal_penjualan <=', $tanggal_akhir);
        }
        
        return $builder->get()->getResultArray();
    }
}