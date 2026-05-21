<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAssignmentReturnsTable extends Migration
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
                'comment' => 'FK a la asignación que se devuelve',
            ],
            'document_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
                'comment' => 'FK al documento asociado',
            ],
            'returned_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
                'comment' => 'FK al Líder que devuelve',
            ],
            'reason' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Motivo de la devolución',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pendiente', 'aceptado', 'rechazado'],
                'default' => 'pendiente',
                'comment' => 'Estado de la devolución: pendiente=esperando director, aceptado=director aceptó, rechazado=director rechazó',
            ],
            'director_response' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Respuesta del director cuando acepta/rechaza la devolución',
            ],
            'responded_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'FK al director que respondió',
            ],
            'responded_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Cuándo el director respondió',
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
        $this->forge->addKey('document_id');
        $this->forge->addKey('returned_by');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('assignment_id', 'assignments', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('document_id', 'documents', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('returned_by', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('assignment_returns');
    }

    public function down(): void
    {
        $this->forge->dropTable('assignment_returns', true);
    }
}