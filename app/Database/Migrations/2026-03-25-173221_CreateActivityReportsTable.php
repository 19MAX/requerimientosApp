<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateActivityReportsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'assignment_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
                'comment' => 'FK a la asignación completada',
            ],
            'reported_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
                'comment' => 'FK al Líder de Área que reporta',
            ],
            'comment' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Descripción de lo que realizó el Líder (opcional)',
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
                'comment' => 'Documento de evidencia subido por el Líder (opcional)',
            ],
            'file_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'file_size' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Tamaño en bytes',
            ],
            'file_mime' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
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

        $this->forge->addKey('id', true);
        $this->forge->addKey('assignment_id');
        $this->forge->addKey('reported_by');
        $this->forge->addForeignKey('assignment_id', 'assignments', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('reported_by', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('activity_reports');
    }

    public function down(): void
    {
        $this->forge->dropTable('activity_reports', true);
    }
}
