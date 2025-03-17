<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateObatKeluarTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'kode_transaksi' => [
                'type'       => 'INT',
                'constraint' => 30,
                'unsigned'   => false,
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'id_obat' => [
                'type'       => 'INT',
                'constraint' => 30,
                'unsigned'   => false,
            ],
            'tanggal_penjualan' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'nama_obat' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'jumlah' => [
                'type'       => 'INT',
                'constraint' => 30,
                'null'       => true,
            ],
            'tanggal_kadaluwarsa' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('kode_transaksi', true);
        $this->forge->addForeignKey('id_obat', 'data_stok_obat', 'id_obat', 'CASCADE', 'CASCADE');
        $this->forge->createTable('obat_keluar');
    }

    public function down()
    {
        $this->forge->dropTable('obat_keluar');
    }
}