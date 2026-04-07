<?php

namespace App\Controllers\Director;

use App\Controllers\BaseController;
use App\Models\AssignmentModel;
use App\Models\DocumentModel;
use App\Models\UsersModel;

class DashboardController extends BaseController
{
    protected $documentModel;
    protected $userModel;
    protected $assignmentModel;
    protected $db;

    public function __construct()
    {
        $this->documentModel = new DocumentModel();
        $this->userModel = new UsersModel();
        $this->assignmentModel = new AssignmentModel();
        $this->db = \Config\Database::connect();
        helper(['status', 'formatDate']);
    }

    public function index()
    {
        // Métricas para el Director
        $stats = [
            'total_documents' => $this->documentModel->countAllResults(),
            'pending_review' => $this->documentModel->whereIn('status', ['pendiente', 'en_revision'])->countAllResults(),
            'approved_not_assigned' => $this->documentModel->where('status', 'aprobado')->countAllResults(),
            'in_execution' => $this->documentModel->where('status', 'asignado')->countAllResults(),
            'completed' => $this->documentModel->where('status', 'completado')->countAllResults(),
            'rejected' => $this->documentModel->where('status', 'rechazado')->countAllResults(),
        ];

        // Últimos documentos
        $recentDocs = $this->documentModel->select('documents.*, users.name as creator_name')
            ->join('users', 'users.id = documents.created_by', 'left')
            ->orderBy('documents.created_at', 'DESC')
            ->limit(5)
            ->find();

        return view('director/dashboard', [
            'stats' => $stats,
            'recent_docs' => $recentDocs
        ]);
    }

    public function reviewDocuments()
    {
        // 1. Obtener documentos con el nombre de la secretaria que lo creó
        $documents = $this->documentModel->select('documents.*, users.name as creator_name, CONCAT(c.first_name, " ", c.last_name) AS client_full_name')
            ->join('users', 'users.id = documents.created_by', 'left')
            ->join('clients c', 'c.id = documents.client_id', 'left')
            ->orderBy('documents.created_at', 'DESC')
            ->findAll();

        // 2. Obtener el ID del rol 'lider_area' para filtrar los usuarios
        $roleLider = $this->db->table('roles')->where('slug', 'lider_area')->get()->getRow();

        $lideres = [];
        if ($roleLider) {
            // Obtener todos los líderes de área activos
            $lideres = $this->userModel->where('role_id', $roleLider->id)
                ->where('is_active', 1)
                ->findAll();
        }

        return view('director/review_documents/index', [
            'documents' => $documents,
            'lideres' => $lideres
        ]);
    }

    public function processReview()
    {
        try {
            $rules = [
                'document_id' => 'required|integer',
                'status' => 'required|in_list[aprobado,rechazado]',
                'review_notes' => 'permit_empty'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->with('error', $this->validator->listErrors());
            }

            $documentId = $this->request->getPost('document_id');
            $status = $this->request->getPost('status');
            $notes = $this->request->getPost('review_notes');

            // Verificar que el documento exista y esté en un estado válido para revisar
            $document = $this->documentModel->find($documentId);

            if (!$document) {
                return redirect()->to('director/review-documents')->with('error', 'El documento no existe.');
            }

            if (!in_array($document['status'], ['pendiente', 'en_revision'])) {
                return redirect()->to('director/review-documents')->with('error', 'Este documento ya fue procesado anteriormente.');
            }

            $oldStatus = $document['status'];
            $directorName = session()->get('name') ?? 'Director';

            // Actualizar el documento
            $this->db->transStart();
            $this->documentModel->update($documentId, [
                'status' => $status,
                'reviewed_by' => session()->get('user_id'),
                'reviewed_at' => date('Y-m-d H:i:s'),
                'review_notes' => $notes
            ]);

            audit_status_change(
                entityType: 'documents',
                entityId: $documentId,
                oldStatus: $oldStatus,
                newStatus: $status,
                description: "Director '{$directorName}' cambió el estado de '{$oldStatus}' a '{$status}'."
            );
            $this->db->transComplete();

            $mensaje = $status === 'aprobado' ? 'Documento aprobado. Ahora puedes asignarlo.' : 'Documento rechazado y devuelto a secretaría.';

            return redirect()->to('director/review-documents')->with('success', $mensaje);

        } catch (\Exception $e) {
            log_message('error', '[DirectorController::processReview] Error procesando doc ID ' . $this->request->getPost('document_id') . ': ' . $e->getMessage());
            return redirect()->to('director/review-documents')->with('error', 'Ocurrió un error interno al guardar la revisión.');
        }
    }

    /**
     * Crea la asignación para el Líder de Área
     */
    public function createAssignment()
    {
        try {
            $rules = [
                'document_id' => 'required|integer',
                'assigned_to' => 'required|integer',
                'due_date' => 'permit_empty|valid_date',
                'instructions' => 'required'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
            }

            $documentId = $this->request->getPost('document_id');

            // Validar que el documento esté aprobado
            $document = $this->documentModel->find($documentId);
            if (!$document || $document['status'] !== 'aprobado') {
                return redirect()->to('director/review-documents')->with('error', 'Solo se pueden asignar documentos previamente aprobados.');
            }

            // INICIO DE TRANSACCIÓN: Nos aseguramos de que ambas cosas pasen o ninguna
            $this->db->transStart();
            $directorName = session()->get('name') ?? 'Director';
            $oldDocumentStatus = $document['status'];

            // 1. Crear el registro en la tabla assignments
            $assignmentData = [
                'document_id' => $documentId,
                'assigned_by' => session()->get('user_id'), // El director actual
                'assigned_to' => $this->request->getPost('assigned_to'),
                'instructions' => $this->request->getPost('instructions'),
                'status' => 'pendiente', // Estado inicial de la asignación
                'due_date' => $this->request->getPost('due_date') ?: null,
                'assigned_at' => date('Y-m-d H:i:s')
            ];

            $this->assignmentModel->insert($assignmentData);

            // 2. Actualizar el estado del documento a 'asignado'
            $this->documentModel->update($documentId, [
                'status' => 'asignado',
            ]);

            audit_status_change(
                entityType: 'documents',
                entityId: $documentId,
                oldStatus: $oldDocumentStatus,
                newStatus: 'asignado',
                description: "Director '{$directorName}' cambió el estado de '{$oldDocumentStatus}' a 'asignado'."
            );

            // COMPLETAR TRANSACCIÓN
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                // Si algo falló en la base de datos, lanzamos una excepción
                throw new \Exception('Falló la transacción de base de datos al asignar la tarea.');
            }

            return redirect()->to('director/review-documents')->with('success', 'Actividad asignada correctamente al Líder de Área.');

        } catch (\Exception $e) {
            log_message('critical', '[DirectorController::createAssignment] Error al asignar tarea: ' . $e->getMessage());
            return redirect()->to('director/review-documents')->with('error', 'Ocurrió un error crítico al intentar crear la asignación. Contacte a soporte.');
        }
    }

    public function searchLeaders(): \CodeIgniter\HTTP\ResponseInterface
    {
        $q = trim($this->request->getGet('q') ?? '');

        if (strlen($q) < 2) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Ingresa al menos 2 caracteres para buscar.',
                'data' => [],
            ]);
        }

        $clients = $this->userModel
            ->groupStart()
            ->like('name', $q, 'both')
            ->orLike('email', $q, 'both')
            ->groupEnd()
            ->where('role_id', 4)
            ->where('deleted_at', null)
            ->findAll(10); // máximo 10 resultados

        return $this->response->setJSON([
            'success' => true,
            'count' => count($clients),
            'data' => $clients,
        ]);
    }
}
