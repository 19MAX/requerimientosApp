<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDirectorIdToDocuments extends Migration
{
    public function up()
    {
        $fields = [
            'director_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'description',
            ],
        ];

        $this->forge->addColumn('documents', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('documents', 'director_id');
    }
}