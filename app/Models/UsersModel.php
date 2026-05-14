<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'role_id', 'leader_category_id', 'name', 'email', 'phone', 'password', 'is_active', 'last_login_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'id' => 'permit_empty|is_natural',
        'name' => [
            'label' => 'Nombres',
            'rules' => 'required|max_length[100]',
        ],
        'email' => [
            'label' => 'Correo Electrónico',
            'rules' => 'required|valid_email|is_unique[users.email,id,{id}]',
        ],
        'phone' => [
            'label' => 'Teléfono',
            'rules' => 'permit_empty|max_length[20]',
        ],
        'role_id' => [
            'label' => 'Rol',
            'rules' => 'required|is_natural_no_zero',
        ],
        'leader_category_id' => [
            'label' => 'Categoría de Líder',
            'rules' => 'permit_empty|is_natural_no_zero',
        ],
        'is_active' => [
            'label' => 'Estado',
            'rules' => 'permit_empty|in_list[0,1]',
        ],
        'password' => [
            'label' => 'Contraseña',
            'rules' => 'required|min_length[6]',
        ],
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword','formatData'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['hashPassword','formatData'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_BCRYPT);
        }
        return $data;
    }

    protected function formatData(array $data)
    {
        if (!isset($data['data'])) {
            return $data;
        }

        // Cargamos el helper
        helper('format');

        if (isset($data['data']['name'])) {
            $data['data']['name'] = format_title_case($data['data']['name']);
        }

        if (isset($data['data']['email'])) {
            $data['data']['email'] = mb_strtolower(trim($data['data']['email']), 'UTF-8');
        }

        if (isset($data['data']['phone'])) {
            $data['data']['phone'] = trim($data['data']['phone']);
        }

        return $data;
    }
}
