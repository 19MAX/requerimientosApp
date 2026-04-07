<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuditTrailTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Usuario que realizó la acción (null si fue el sistema)',
            ],
            'action' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
                'comment' => 'Ej: document.created, assignment.created, activity.completed',
            ],
            'entity_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
                'comment' => 'Tabla afectada: documents, assignments, activity_reports',
            ],
            'entity_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
                'comment' => 'ID del registro afectado',
            ],
            'old_status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Estado anterior (para cambios de status)',
            ],
            'new_status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Estado nuevo (para cambios de status)',
            ],
            'old_values' => [
                'type' => 'JSON',
                'null' => true,
                'comment' => 'Snapshot de los valores anteriores (JSON)',
            ],
            'new_values' => [
                'type' => 'JSON',
                'null' => true,
                'comment' => 'Snapshot de los valores nuevos (JSON)',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Descripción legible del evento para mostrar en UI',
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
                'comment' => 'IP del cliente (soporta IPv6)',
            ],
            'user_agent' => [
                'type' => 'VARCHAR',
                'constraint' => 300,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Fecha y hora exacta del evento (inmutable)',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey(['entity_type', 'entity_id']);
        $this->forge->addKey('action');
        $this->forge->addKey('created_at');
        // Sin FK en user_id para preservar historial si el usuario es eliminado
        $this->forge->createTable('audit_trail');
    }

    public function down(): void
    {
        $this->forge->dropTable('audit_trail', true);
    }
}
