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

    public function viewHistory($documentId)
    {
        $userId = session()->get('user_id');

        $document = $this->documentModel
            ->select('documents.*')
            ->join('assignments a', 'a.document_id = documents.id AND a.assigned_to = ' . $userId, 'left')
            ->where('documents.id', $documentId)
            ->where('a.assigned_to', $userId)
            ->where('documents.deleted_at', null)
            ->first();

        if (!$document) {
            return redirect()->to('lider/document-search')->with('error', 'Documento no encontrado o no tienes acceso.');
        }

        $auditLogs = $this->db->table('audit_trail')
            ->where('entity_type', 'documents')
            ->where('entity_id', $documentId)
            ->orderBy('created_at', 'ASC')
            ->get()
            ->getResultArray();

        return view('lider/document_search/view_history', [
            'document' => $document,
            'auditLogs' => $auditLogs
        ]);
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
                d.id,
                d.document_code,
                d.title,
                d.status,
                d.created_at,
                CONCAT(c.first_name, " ", c.last_name) AS client_full_name,
                c.cedula AS client_cedula,
                a.status AS assignment_status,
                a.assigned_at,
                a.started_at,
                a.completed_at
            ')
            ->join('clients c', 'c.id = d.client_id', 'left')
            ->join('assignments a', 'a.document_id = d.id AND a.assigned_to = ' . $userId, 'left')
            ->where('d.deleted_at', null)
            ->where('a.assigned_to', $userId);

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

    public function data()
    {
        $userId = session()->get('user_id');

        $draw = $this->request->getGet('draw');
        $start = $this->request->getGet('start');
        $length = $this->request->getGet('length');
        $searchValue = $this->request->getGet('search[value]') ?? '';
        $statusFilter = $this->request->getGet('status') ?? '';
        $dateFrom = $this->request->getGet('date_from') ?? '';
        $dateTo = $this->request->getGet('date_to') ?? '';

        $query = $this->db->table('documents d')
            ->select('
                d.id,
                d.document_code,
                d.title,
                d.status,
                d.created_at,
                CONCAT(c.first_name, " ", c.last_name) AS client_full_name,
                c.cedula AS client_cedula
            ')
            ->join('clients c', 'c.id = d.client_id', 'left')
            ->join('assignments a', 'a.document_id = d.id AND a.assigned_to = ' . $userId, 'left')
            ->where('d.deleted_at', null)
            ->where('a.assigned_to', $userId);

        if ($searchValue !== '') {
            $query->groupStart()
                ->like('d.document_code', $searchValue, 'both')
                ->orLike('d.title', $searchValue, 'both')
                ->orLike('c.first_name', $searchValue, 'both')
                ->orLike('c.last_name', $searchValue, 'both')
                ->orLike('c.cedula', $searchValue, 'both')
                ->groupEnd();
        }

        if ($statusFilter !== '') {
            $query->where('d.status', $statusFilter);
        }

        if ($dateFrom !== '') {
            $query->where('d.created_at >=', $dateFrom . ' 00:00:00');
        }

        if ($dateTo !== '') {
            $query->where('d.created_at <=', $dateTo . ' 23:59:59');
        }

        $totalRecords = $query->countAllResults(false);

        $documents = $query->orderBy('d.created_at', 'DESC')
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'draw' => (int)$draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $documents
        ]);
    }
}
