<?php

namespace App\Models;

use CodeIgniter\Model;

class LeaderCategoryModel extends Model
{
    protected $table            = 'leader_categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'slug', 'description'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'id' => 'permit_empty|is_natural',
        'name' => [
            'label' => 'Nombre',
            'rules' => 'required|max_length[100]',
        ],
        'slug' => [
            'label' => 'Slug',
            'rules' => 'required|max_length[100]|is_unique[leader_categories.slug,id,{id}]',
        ],
        'description' => [
            'label' => 'Descripción',
            'rules' => 'permit_empty|max_length[500]',
        ],
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}