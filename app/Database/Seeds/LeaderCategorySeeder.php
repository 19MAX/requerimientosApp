<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LeaderCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Líder de Operación',
                'slug' => 'lider_operacion',
                'description' => 'Líder de área de operaciones',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Líder de Mantenimiento',
                'slug' => 'lider_mantenimiento',
                'description' => 'Líder de área de mantenimiento',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Líder de Ingeniería y Construcción',
                'slug' => 'lider_ingenieria',
                'description' => 'Líder de área de ingeniería y construcción',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Líder de alumbrado público ',
                'slug' => 'lider_alumbrado',
                'description' => 'Líder de área de alumbrado público',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('leader_categories')->insertBatch($categories);
        echo "Seeder: Categorías de líder creadas exitosamente.\n";
    }
}