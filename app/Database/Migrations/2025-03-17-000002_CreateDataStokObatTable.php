<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDataStokObatTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_obat' => [
                'type'       => 'INT',
                'constraint' => 30,
                'unsigned'   => false,
                'auto_increment' => true,
            ],
            'jumlah_stok' => [
                'type'       => 'INT',
                'constraint' => 20,
                'null'       => true,
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => '30',
                'null'       => true,
            ],
            'nama_obat' => [
                'type'       => 'VARCHAR',
                'constraint' => '225',
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
        $this->forge->addKey('id_obat', true);
        $this->forge->createTable('data_stok_obat');
    }

    public function down()
    {
        $this->forge->dropTable('data_stok_obat');
    }
}