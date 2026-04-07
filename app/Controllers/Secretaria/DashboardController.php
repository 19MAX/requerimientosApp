<?php

namespace App\Controllers\Secretaria;

use App\Controllers\BaseController;
use App\Models\DocumentModel;
use App\Models\ClientsModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $documentModel = new DocumentModel();
        $clientsModel = new ClientsModel();
        
        $userId = session()->get('user_id');

        // Métricas para la Secretaria
        $stats = [
            'total_my_documents' => $documentModel->where('created_by', $userId)->countAllResults(),
            'my_pending' => $documentModel->where('created_by', $userId)->whereIn('status', ['pendiente', 'en_revision'])->countAllResults(),
            'my_approved' => $documentModel->where('created_by', $userId)->where('status', 'aprobado')->countAllResults(),
            'my_completed' => $documentModel->where('created_by', $userId)->where('status', 'completado')->countAllResults(),
            'my_rejected' => $documentModel->where('created_by', $userId)->where('status', 'rechazado')->countAllResults(),
            'total_clients' => $clientsModel->countAllResults(),
        ];

        // Últimos documentos creados por esta secretaria
        $recentDocs = $documentModel->where('created_by', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->find();

        return view('secretaria/dashboard', [
            'stats' => $stats,
            'recent_docs' => $recentDocs
        ]);
    }
}
