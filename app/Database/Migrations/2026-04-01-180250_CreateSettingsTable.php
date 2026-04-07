<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSettingsTable extends Migration
{
public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'site_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'site_logo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('settings');

        // Insertamos un registro por defecto inmediatamente después de crear la tabla
        // Esto garantiza que siempre exista el ID = 1 para que el controlador lo actualice
        $data = [
            'site_name' => 'Mi Sistema Pro',
            'site_logo' => null,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $this->db->table('settings')->insert($data);
    }

    public function down()
    {
        $this->forge->dropTable('settings', true);
    }
}
