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
    protected $protectFields    = false;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function notificationMinimumStock()
    {
        $stockObatModel = $this->db->table('data_stok_obat');

        $result = $stockObatModel->where('jumlah_stok <', 10)->get()->getResultArray();

        return $result;

    }
}