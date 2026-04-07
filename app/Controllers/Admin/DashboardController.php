<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    public function index()
    {
        $documentModel = new \App\Models\DocumentModel();
        $userModel = new \App\Models\UsersModel();
        $assignmentModel = new \App\Models\AssignmentModel();

        // 1. Métricas Globales
        $stats = [
            'total_documents' => $documentModel->countAllResults(),
            'active_tasks' => $assignmentModel->where('status', 'en_progreso')->countAllResults(),
            'active_users' => $userModel->where('is_active', 1)->countAllResults(),

            // Desglose de documentos
            'docs_ingresados' => $documentModel->countAllResults(),
            'docs_completados' => $documentModel->where('status', 'completado')->countAllResults(),
            'docs_pendientes' => $documentModel->where('status', 'pendiente')->countAllResults(),
            'docs_aprobados' => $documentModel->where('status', 'aprobado')->countAllResults(),
            'docs_ejecucion' => $documentModel->where('status', 'asignado')->countAllResults(),
            'docs_rechazados' => $documentModel->where('status', 'rechazado')->countAllResults(),
        ];

        // 2. Últimos 5 documentos para la tabla inferior izquierda
        $recentDocs = $documentModel->select('documents.*, users.name as creator_name')
            ->join('users', 'users.id = documents.created_by', 'left')
            ->orderBy('documents.created_at', 'DESC')
            ->limit(5)
            ->find();

        // 3. Rendimiento de Líderes (Para las barras de progreso inferiores derechas)
        // Esto es un ejemplo, podrías hacer una consulta SQL agrupada (GROUP BY)
        $db = \Config\Database::connect();
        $leaderStats = $db->query("
        SELECT u.name, 
               COUNT(a.id) as total, 
               SUM(CASE WHEN a.status = 'completada' THEN 1 ELSE 0 END) as completed
        FROM users u
        JOIN roles r ON u.role_id = r.id
        LEFT JOIN assignments a ON u.id = a.assigned_to
        WHERE r.slug = 'lider_area' AND u.is_active = 1
        GROUP BY u.id
        LIMIT 4
    ")->getResultArray();

        return view('admin/dashboard', [
            'stats' => $stats,
            'recent_docs' => $recentDocs,
            'leader_stats' => $leaderStats
        ]);
    }
}
