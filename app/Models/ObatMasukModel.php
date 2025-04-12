<?php

namespace App\Models;

use CodeIgniter\Model;

class ObatMasukModel extends Model
{
    protected $table            = 'obat_masuk';
    protected $primaryKey       = 'id_obat';
    protected $useAutoIncrement = false; // Since it's a foreign key
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_obat',
        'nama_obat',
        'jumlah',
        'tanggal_masuk',
        'jenis',
        'dosis',
        'satuan',
        'tanggal_kadaluwarsa'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}