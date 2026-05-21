<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateAssignmentsTableAddDevueltoStatus extends Migration
{
    public function up(): void
    {
        $db = \Config\Database::connect();
        $db->query("ALTER TABLE assignments MODIFY COLUMN status ENUM('pendiente', 'en_progreso', 'completada', 'cancelada', 'rechazado', 'devuelto') DEFAULT 'pendiente'");
    }

    public function down(): void
    {
        $db = \Config\Database::connect();
        $db->query("ALTER TABLE assignments MODIFY COLUMN status ENUM('pendiente', 'en_progreso', 'completada', 'cancelada', 'rechazado') DEFAULT 'pendiente'");
    }
}