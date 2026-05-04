<?php

namespace App\Controllers\Lider;

use App\Controllers\BaseController;
use App\Models\DocumentModel;

class DocumentSearchController extends BaseController
{
    protected $documentModel;
    protected $db;

    public function __construct()
    {
        $this->documentModel = new DocumentModel();
        $this->db = \Config\Database::connect();
        helper(['status', 'formatDate']);
    }

    public function index()
    {
        return view('lider/document_search/index');
    }

    public function search()
    {
        $userId = session()->get('user_id');
        $keyword = trim($this->request->getGet('keyword') ?? '');
        $status = trim($this->request->getGet('status') ?? '');
        $dateFrom = trim($this->request->getGet('date_from') ?? '');
        $dateTo = trim($this->request->getGet('date_to') ?? '');

        $query = $this->db->table('documents d')
            ->select('
                d.*,
                CONCAT(c.first_name, " ", c.last_name) AS client_full_name,
                c.cedula AS client_cedula,
                a.status AS assignment_status,
                a.assigned_at,
                a.started_at,
                a.completed_at
            ')
            ->join('clients c', 'c.id = d.client_id', 'left')
            ->join('assignments a', 'a.document_id = d.id AND a.assigned_to = ' . $userId, 'left')
            ->where('d.deleted_at', null);

        if ($keyword !== '') {
            $query->groupStart()
                ->like('d.document_code', $keyword, 'both')
                ->orLike('d.title', $keyword, 'both')
                ->orLike('d.description', $keyword, 'both')
                ->orLike('c.first_name', $keyword, 'both')
                ->orLike('c.last_name', $keyword, 'both')
                ->orLike('c.cedula', $keyword, 'both')
                ->groupEnd();
        }

        if ($status !== '') {
            $query->where('d.status', $status);
        }

        if ($dateFrom !== '') {
            $query->where('d.created_at >=', $dateFrom . ' 00:00:00');
        }

        if ($dateTo !== '') {
            $query->where('d.created_at <=', $dateTo . ' 23:59:59');
        }

        $documents = $query->orderBy('d.created_at', 'DESC')->get()->getResultArray();

        return $this->response->setJSON([
            'success' => true,
            'count' => count($documents),
            'data' => $documents,
        ]);
    }
}
