<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateObatMasukTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_obat' => [
                'type'       => 'INT',
                'constraint' => 30,
                'unsigned'   => false,
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
            'tanggal_masuk' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'jenis' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'dosis' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
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
        $this->forge->addForeignKey('id_obat', 'data_stok_obat', 'id_obat', 'CASCADE', 'CASCADE');
        $this->forge->createTable('obat_masuk');
    }

    public function down()
    {
        $this->forge->dropTable('obat_masuk');
    }
}