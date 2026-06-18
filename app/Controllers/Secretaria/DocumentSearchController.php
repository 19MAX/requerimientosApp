<?php

namespace App\Controllers\Secretaria;

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
        return view('secretaria/document_search/index');
    }

    public function search()
    {
        $keyword = trim($this->request->getGet('keyword') ?? '');
        $status = trim($this->request->getGet('status') ?? '');
        $dateFrom = trim($this->request->getGet('date_from') ?? '');
        $dateTo = trim($this->request->getGet('date_to') ?? '');
        $liderId = trim($this->request->getGet('lider_id') ?? '');

        $query = $this->db->table('documents d')
            ->select('
                d.*,
                CONCAT(c.first_name, " ", c.last_name) AS client_full_name,
                c.cedula AS client_cedula
            ')
            ->join('clients c', 'c.id = d.client_id', 'left')
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

    public function data()
    {
        $draw = $this->request->getGet('draw');
        $start = $this->request->getGet('start');
        $length = $this->request->getGet('length');
        $searchValue = $this->request->getGet('search[value]') ?? '';
        $statusFilter = $this->request->getGet('status') ?? '';
        $dateFrom = $this->request->getGet('date_from') ?? '';
        $dateTo = $this->request->getGet('date_to') ?? '';
        $liderId = $this->request->getGet('lider_id') ?? '';

        $completionSubquery = $this->db->table('activity_reports ar')
            ->select('MAX(ar.created_at)')
            ->join('assignments a2', 'a2.id = ar.assignment_id', 'inner')
            ->where('a2.document_id = ' . 'd.id', null, false)
            ->getCompiledSelect();

        $query = $this->db->table('documents d')
            ->select('
                d.id,
                d.document_code,
                d.title,
                d.status,
                d.created_at,
                (' . $completionSubquery . ') AS completed_at,
                CONCAT(c.first_name, " ", c.last_name) AS client_full_name,
                c.cedula AS client_cedula,
                u.name AS lider_name,
                lc.name AS leader_category_name,
                a.id AS assignment_id
            ')
            ->join('clients c', 'c.id = d.client_id', 'left')
            ->join('assignments a', 'a.document_id = d.id', 'left')
            ->join('users u', 'u.id = a.assigned_to', 'left')
            ->join('leader_categories lc', 'lc.id = u.leader_category_id', 'left')
            ->where('d.deleted_at', null);

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

        if ($liderId !== '') {
            $query->where('a.assigned_to', $liderId);
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

    public function getLideres()
    {
        $categoryId = trim($this->request->getGet('category_id') ?? '');

        $query = $this->db->table('users u')
            ->select('u.id, u.name, u.leader_category_id, lc.name as category_name')
            ->join('roles r', 'r.id = u.role_id')
            ->join('leader_categories lc', 'lc.id = u.leader_category_id', 'left')
            ->where('r.slug', 'lider_area')
            ->where('u.is_active', 1);

        if ($categoryId !== '') {
            $query->where('u.leader_category_id', $categoryId);
        }

        $lideres = $query->orderBy('lc.name', 'ASC')
            ->orderBy('u.name', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON($lideres);
    }

    public function getCategories()
    {
        $categories = $this->db->table('leader_categories')
            ->select('id, name')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON($categories);
    }

    public function viewFlow($documentId)
    {
        $document = $this->db->table('documents d')
            ->select('
                d.id,
                d.document_code,
                d.title,
                d.description,
                d.file_path,
                d.file_name,
                d.status,
                d.created_at,
                c.first_name,
                c.last_name,
                c.cedula AS client_cedula
            ')
            ->join('clients c', 'c.id = d.client_id', 'left')
            ->where('d.id', $documentId)
            ->get()
            ->getRowArray();

        if (!$document) {
            return redirect()->to('secretaria/document-search')->with('error', 'Documento no encontrado.');
        }

        $clientFullName = trim(($document['first_name'] ?? '') . ' ' . ($document['last_name'] ?? ''));
        if ($clientFullName === '') {
            $clientFullName = 'N/A';
        }

        $assignment = $this->db->table('assignments a')
            ->select('a.*, u.name AS lider_name, dir.name AS director_name')
            ->join('users u', 'u.id = a.assigned_to', 'left')
            ->join('users dir', 'dir.id = a.assigned_by', 'left')
            ->where('a.document_id', $documentId)
            ->get()
            ->getRowArray();

        $activityReport = null;
        if ($assignment) {
            $activityReport = $this->db->table('activity_reports')
                ->where('assignment_id', $assignment['id'])
                ->orderBy('created_at', 'DESC')
                ->get()
                ->getRowArray();
        }

        $auditLogs = $this->db->table('audit_trail')
            ->where('entity_type', 'documents')
            ->where('entity_id', $documentId)
            ->orderBy('created_at', 'ASC')
            ->get()
            ->getResultArray();

        $document['client_full_name'] = $clientFullName;

        return view('secretaria/document_search/view_flow', [
            'document' => $document,
            'assignment' => $assignment,
            'activityReport' => $activityReport,
            'auditLogs' => $auditLogs
        ]);
    }
}
