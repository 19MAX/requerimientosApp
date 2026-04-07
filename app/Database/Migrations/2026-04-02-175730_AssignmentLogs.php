<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AssignmentLogs extends Migration
{
 public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'assignment_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],

            'document_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],

            'action' => [
                'type'       => 'VARCHAR',
                'constraint' => 40,
            ],

            'from_user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],

            'to_user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],

            'changed_by' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],

            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
            ],
        ]);

        $this->forge->addKey('id', true);

        $this->forge->createTable('assignment_logs');
    }

    public function down()
    {
        $this->forge->dropTable('assignment_logs');
    }
}
