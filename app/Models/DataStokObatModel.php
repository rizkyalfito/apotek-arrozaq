<?php

namespace App\Models;

use CodeIgniter\Model;

class DataStokObatModel extends Model
{
    protected $table            = 'data_stok_obat';
    protected $primaryKey       = 'id_obat';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'jumlah_stok', 
        'satuan', 
        'nama_obat', 
        'tanggal_kadaluwarsa'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}