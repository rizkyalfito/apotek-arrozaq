<?php

namespace App\Models;

use CodeIgniter\Model;

class ObatMasukModel extends Model
{
    protected $table            = 'obat_masuk';
    protected $primaryKey       = 'id_obat';
    protected $useAutoIncrement = true; // Since it's a foreign key
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}