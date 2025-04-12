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
        'tanggal_kadaluwarsa'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}