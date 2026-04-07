<?php

namespace App\Controllers\Lider;

use App\Controllers\BaseController;
use App\Models\AssignmentModel;
use App\Models\DocumentModel;

class DashboardController extends BaseController
{
    protected $assignmentModel;
    protected $documentModel;

    public function __construct()
    {
        $this->assignmentModel = new AssignmentModel();
        $this->documentModel = new DocumentModel();
    }

    public function index()
    {
        $userId = session()->get('user_id');

        // Estadísticas para el líder
        $stats = [
            'total' => $this->assignmentModel->where('assigned_to', $userId)->countAllResults(),
            'pending' => $this->assignmentModel->where('assigned_to', $userId)->where('status', 'pendiente')->countAllResults(),
            'in_progress' => $this->assignmentModel->where('assigned_to', $userId)->where('status', 'en_progreso')->countAllResults(),
            'completed' => $this->assignmentModel->where('assigned_to', $userId)->where('status', 'completada')->countAllResults(),
        ];

        // Últimas asignaciones
        $recentTasks = $this->assignmentModel
            ->select('assignments.*, documents.title as document_title, users.name as director_name')
            ->join('documents', 'documents.id = assignments.document_id')
            ->join('users', 'users.id = assignments.assigned_by')
            ->where('assignments.assigned_to', $userId)
            ->orderBy('assignments.created_at', 'DESC')
            ->limit(5)
            ->find();

        return view('lider/dashboard', [
            'stats' => $stats,
            'recent_tasks' => $recentTasks
        ]);
    }
}
