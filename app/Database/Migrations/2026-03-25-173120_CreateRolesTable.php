<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRolesTable extends Migration
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
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
                'comment' => 'admin | secretaria | director | lider_area',
            ],
            'description' => [
                'type' => 'TEXT',
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
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('roles');

        // Insertar los 4 roles base
        $this->db->table('roles')->insertBatch([
            ['name' => 'Administración', 'slug' => 'admin', 'description' => 'Gestiona todo el sistema', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Secretaría', 'slug' => 'secretaria', 'description' => 'Ingresa y gestiona peticiones de documentos', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Director de Distribución', 'slug' => 'director', 'description' => 'Revisa y aprueba documentos, asigna tareas', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Líder de Área', 'slug' => 'lider_area', 'description' => 'Ejecuta actividades asignadas', 'created_at' => date('Y-m-d H:i:s')],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropTable('roles', true);
    }
}
