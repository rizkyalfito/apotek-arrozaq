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

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function notificationMinimumStock()
    {
        $stockObatModel = $this->db->table('data_stok_obat');

        $result = $stockObatModel->where('jumlah_stok <=', 5)->get()->getResultArray();

        return $result;
    }

    public function notificationExpiringMedicine()
    {
        $builder = $this->db->table('data_stok_obat');
        
        $today = date('Y-m-d');
        
        // Ubah nama variabel agar lebih jelas
        $thirtyDaysFromNow = date('Y-m-d', strtotime('+30 days'));
        
        $builder->select('id_obat, nama_obat, jumlah_stok, satuan, tanggal_kadaluwarsa');
        $builder->where('tanggal_kadaluwarsa >=', $today);
        $builder->where('tanggal_kadaluwarsa <=', $thirtyDaysFromNow);
        $builder->orderBy('tanggal_kadaluwarsa', 'ASC');
        
        $result = $builder->get()->getResultArray();
        
        foreach ($result as &$obat) {
            $tanggalKadaluarsa = new \DateTime($obat['tanggal_kadaluwarsa']);
            $tanggalSekarang = new \DateTime($today);
            $selisihHari = $tanggalSekarang->diff($tanggalKadaluarsa)->days;
            
            $obat['hari_tersisa'] = $selisihHari;
            
            // Perbaiki logika untuk rentang 30 hari
            if ($selisihHari == 0) {
                $obat['status_kadaluarsa'] = 'Kedaluarsa Hari Ini';
                $obat['level_urgency'] = 'critical';
            } elseif ($selisihHari <= 2) {
                $obat['status_kadaluarsa'] = 'Sangat Mendesak';
                $obat['level_urgency'] = 'critical';
            } elseif ($selisihHari <= 7) {
                $obat['status_kadaluarsa'] = 'Perlu Perhatian Segera';
                $obat['level_urgency'] = 'high';
            } elseif ($selisihHari <= 14) {
                $obat['status_kadaluarsa'] = 'Perlu Perhatian';
                $obat['level_urgency'] = 'medium';
            } elseif ($selisihHari <= 30) {
                $obat['status_kadaluarsa'] = 'Monitoring';
                $obat['level_urgency'] = 'low';
            }
        }
        
        return $result;
    }
    public function getAllNotifications()
    {
        $notifications = [
            'minimum_stock' => $this->notificationMinimumStock(),
            'expiring_medicine' => $this->notificationExpiringMedicine()
        ];
        
        $notifications['total_count'] = count($notifications['minimum_stock']) + count($notifications['expiring_medicine']);
        
        return $notifications;
    }

    public function getExpiredMedicine()
    {
        $builder = $this->db->table('data_stok_obat');
        
        $today = date('Y-m-d');
        
        $builder->select('id_obat, nama_obat, jumlah_stok, satuan, tanggal_kadaluwarsa');
        $builder->where('tanggal_kadaluwarsa <', $today);
        $builder->orderBy('tanggal_kadaluwarsa', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    public function getStokObatWithTanggalMasuk($cari = null)
    {
        $builder = $this->db->table('data_stok_obat dso');
        $builder->select('dso.id_obat, dso.nama_obat, dso.jumlah_stok, dso.satuan, dso.tanggal_kadaluwarsa, om.tanggal_masuk');
        $builder->join('obat_masuk om', 'dso.id_obat = om.id_obat', 'left');
        
        if (!empty($cari)) {
            $builder->groupStart();
            $builder->like('dso.nama_obat', $cari);
            $builder->orLike('dso.id_obat', $cari);
            $builder->groupEnd();
        }
        
        $builder->groupBy('dso.id_obat');
        $builder->orderBy('dso.id_obat', 'ASC');
        
        return $builder->get()->getResultArray();
    }
}