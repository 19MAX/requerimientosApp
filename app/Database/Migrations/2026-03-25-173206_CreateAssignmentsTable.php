<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAssignmentsTable extends Migration
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
            'document_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
                'comment' => 'FK al documento aprobado',
            ],
            'assigned_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
                'comment' => 'FK al Director que asigna la actividad',
            ],
            'assigned_to' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
                'comment' => 'FK al Líder de Área que ejecutará la actividad',
            ],
            'instructions' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Instrucciones adicionales del Director al Líder',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pendiente', 'en_progreso', 'completada', 'cancelada','rechazado'],
                'default' => 'pendiente',
            ],
            'due_date' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Fecha límite para completar la actividad (opcional)',
            ],
            'assigned_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Fecha en que se realizó la asignación',
            ],
            'started_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Cuando el Líder inició la ejecución',
            ],
            'completed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Cuando el Líder marcó la actividad como completada',
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
        $this->forge->addKey('document_id');
        $this->forge->addKey('assigned_by');
        $this->forge->addKey('assigned_to');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('document_id', 'documents', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('assigned_by', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('assigned_to', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('assignments');
    }

    public function down(): void
    {
        $this->forge->dropTable('assignments', true);
    }
}
