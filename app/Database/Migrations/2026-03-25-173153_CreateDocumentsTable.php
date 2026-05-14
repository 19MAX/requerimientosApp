<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDocumentsTable extends Migration
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
            'document_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'after'      => 'id',
                'comment'    => 'Código único de seguimiento. Ej: 00001-2026-05-16',
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
                'comment' => 'FK al usuario Secretari@ que creó el documento',
            ],
            'client_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'after'      => 'created_by',
                'comment'    => 'FK al cliente que presenta el problema',
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
                'comment' => 'Ruta del archivo subido por la secretaria',
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
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pendiente', 'en_revision', 'aprobado', 'rechazado', 'asignado', 'trabajando', 'completado'],
                'default' => 'pendiente',
                'comment' => 'Estado actual del documento en el flujo',
            ],
            'reviewed_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'FK al Director que revisó el documento',
            ],
            'reviewed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'review_notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Comentarios del Director al revisar',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Soft delete',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('created_by');
        $this->forge->addKey('client_id');
        $this->forge->addKey('reviewed_by');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('reviewed_by', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('documents');
    }

    public function down(): void
    {
        $this->forge->dropTable('documents', true);
    }
}
