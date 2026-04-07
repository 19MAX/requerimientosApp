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
        $db = \Config\Database::connect();

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

        // 1.1 Datos para gráfico de distribución por estados
        $chartDataEstados = [
            $stats['docs_pendientes'],
            $stats['docs_aprobados'],
            $stats['docs_ejecucion'],
            $stats['docs_rechazados']
        ];

        // 1.2 Datos para gráfico de flujo de trámites (últimos 6 meses por ejemplo, pero lo haremos simple agrupando por mes actual)
        // Por simplicidad, obtenemos ingresados y completados de los últimos 6 meses
        $ingresadosPorMes = [];
        $completadosPorMes = [];
        $mesesLabels = [];

        for ($i = 5; $i >= 0; $i--) {
            $mesDate = date('Y-m', strtotime("-$i months"));
            $mesNombre = date('M', strtotime("-$i months")); // Jan, Feb...
            
            $ingresados = $db->table('documents')
                ->where("DATE_FORMAT(created_at, '%Y-%m') = '$mesDate'")
                ->countAllResults();

            // Usamos audit_trail para saber cuándo se completó
            $completados = $db->table('audit_trail')
                ->where('entity_type', 'documents')
                ->where('new_status', 'completado')
                ->where("DATE_FORMAT(created_at, '%Y-%m') = '$mesDate'")
                ->countAllResults();

            $ingresadosPorMes[] = $ingresados;
            $completadosPorMes[] = $completados;
            $mesesLabels[] = $mesNombre;
        }

        $chartDataFlujo = [
            'labels' => $mesesLabels,
            'ingresados' => $ingresadosPorMes,
            'completados' => $completadosPorMes
        ];

        // 2. Últimos 5 documentos para la tabla inferior izquierda
        $recentDocs = $documentModel->select('documents.*, users.name as creator_name')
            ->join('users', 'users.id = documents.created_by', 'left')
            ->orderBy('documents.created_at', 'DESC')
            ->limit(5)
            ->find();

        // 3. Rendimiento de Líderes
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
            'leader_stats' => $leaderStats,
            'chartDataEstados' => json_encode($chartDataEstados),
            'chartDataFlujo' => json_encode($chartDataFlujo)
        ]);
    }
}
