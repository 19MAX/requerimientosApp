<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientsModel extends Model
{
    protected $table = 'clients';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = false;
    protected $allowedFields = [];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'id' => 'permit_empty|is_natural',
        'first_name' => [
            'label' => 'Nombres',
            'rules' => 'required|max_length[100]',
        ],
        'last_name' => [
            'label' => 'Apellidos',
            'rules' => 'required|max_length[100]',
        ],
        'cedula' => [
            'label' => 'Cédula',
            // OJO AQUÍ: Agregamos el placeholder {id} al final
            'rules' => 'required|min_length[10]|max_length[13]|is_unique[clients.cedula,id,{id}]',
        ],
        'email' => [
            'label' => 'Correo Electrónico',
            // OJO AQUÍ: Agregamos el placeholder {id} al final
            'rules' => 'required|valid_email|is_unique[clients.email,id,{id}]',
        ],
        'phone' => [
            'label' => 'Teléfono',
            'rules' => 'permit_empty|max_length[20]',
        ],
        'address' => [
            'label' => 'Dirección',
            'rules' => 'permit_empty|max_length[200]',
        ]
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['formatData'];
    protected $afterInsert = [];
    protected $beforeUpdate = ['formatData'];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    protected function formatData(array $data)
    {
        if (!isset($data['data'])) {
            return $data;
        }

        // Cargamos el helper
        helper('format');

        if (isset($data['data']['first_name'])) {
            $data['data']['first_name'] = format_title_case($data['data']['first_name']);
        }

        if (isset($data['data']['last_name'])) {
            $data['data']['last_name'] = format_title_case($data['data']['last_name']);
        }

        if (isset($data['data']['email'])) {
            $data['data']['email'] = mb_strtolower(trim($data['data']['email']), 'UTF-8');
        }

        if (isset($data['data']['address'])) {
            $data['data']['address'] = format_sentence_case($data['data']['address']);
        }

        if (isset($data['data']['cedula'])) {
            $data['data']['cedula'] = trim($data['data']['cedula']);
        }

        if (isset($data['data']['phone'])) {
            $data['data']['phone'] = trim($data['data']['phone']);
        }

        return $data;
    }
}
