<?php

namespace App\Database\Seeds;

use App\Models\UsersModel;
use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
   public function run()
    {
        $role = $this->db->table('roles')->where('slug', 'admin')->get()->getRow();

        if (!$role) {
            echo "Error: El rol 'admin' no existe. Ejecuta las migraciones primero.\n";
            return;
        }

        $userModel = new UsersModel();

        $userModel->insert([
            'role_id'   => $role->id,
            'name'      => 'Administrador del Sistema',
            'email'     => 'admin@admin.com',
            'password'  => 'password',
            'phone'     => '0999999999',
            'is_active' => 1
        ]);

        echo "Seeder: Administrador creado exitosamente.\n";
    }
}
