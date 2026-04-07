<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AuditController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        // Traer todos los logs con nombre y rol del usuario que ejecutó la acción
        $auditLogs = $this->db->table('audit_trail at')
            ->select('
                at.*,
                CONCAT(u.name) AS user_name,
                r.name AS role_name
            ')
            ->join('users u', 'u.id = at.user_id', 'left')
            ->join('roles r', 'r.id = u.role_id', 'left')
            ->orderBy('at.id', 'DESC')
            ->get()
            ->getResultArray();

        // Estadísticas rápidas para las tarjetas
        $stats = [
            'documents_created' => $this->db->table('audit_trail')
                ->where('action', 'document.created')
                ->countAllResults(),

            'completed' => $this->db->table('audit_trail')
                ->where('action', 'activity.completed')
                ->countAllResults(),

            'status_changes' => $this->db->table('audit_trail')
                ->where('action', 'document.status_changed')
                ->countAllResults(),

            'active_users' => $this->db->table('users')
                ->where('is_active', 1)
                ->where('deleted_at', null)
                ->countAllResults(),
        ];

        return view('admin/audit/index', [
            'auditLogs' => $auditLogs,
            'stats' => $stats,
        ]);
    }
}
