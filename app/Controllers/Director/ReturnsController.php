<?php

namespace App\Controllers\Director;

use App\Controllers\BaseController;
use App\Models\AssignmentModel;
use App\Models\AssignmentReturnModel;
use App\Models\DocumentModel;
use App\Models\UsersModel;

class ReturnsController extends BaseController
{
    protected $returnModel;
    protected $assignmentModel;
    protected $documentModel;
    protected $userModel;
    protected $db;

    public function __construct()
    {
        $this->returnModel = new AssignmentReturnModel();
        $this->assignmentModel = new AssignmentModel();
        $this->documentModel = new DocumentModel();
        $this->userModel = new UsersModel();
        $this->db = \Config\Database::connect();
        helper(['audit']);
    }

    public function index()
    {
        $currentDirectorId = session()->get('user_id');

        $pendingReturns = $this->returnModel
            ->select('assignment_returns.*, documents.document_code, documents.title as document_title,
                      users.name as lider_name, assignments.instructions')
            ->join('documents', 'documents.id = assignment_returns.document_id')
            ->join('assignments', 'assignments.id = assignment_returns.assignment_id')
            ->join('users', 'users.id = assignment_returns.returned_by')
            ->where('assignment_returns.status', 'pendiente')
            ->where('assignments.assigned_by', $currentDirectorId)
            ->orderBy('assignment_returns.created_at', 'DESC')
            ->findAll();

        $processedReturns = $this->returnModel
            ->select('assignment_returns.*, documents.document_code, documents.title as document_title,
                      users.name as lider_name, d.name as director_response_name')
            ->join('documents', 'documents.id = assignment_returns.document_id')
            ->join('assignments', 'assignments.id = assignment_returns.assignment_id')
            ->join('users', 'users.id = assignment_returns.returned_by')
            ->join('users d', 'd.id = assignment_returns.responded_by', 'left')
            ->whereIn('assignment_returns.status', ['aceptado', 'rechazado'])
            ->where('assignments.assigned_by', $currentDirectorId)
            ->orderBy('assignment_returns.updated_at', 'DESC')
            ->findAll();

        return view('director/returns/index', [
            'pendingReturns' => $pendingReturns,
            'processedReturns' => $processedReturns
        ]);
    }

    public function handleReturn()
    {
        $returnId = $this->request->getPost('return_id');
        $action = $this->request->getPost('action');
        $directorResponse = $this->request->getPost('director_response');

        if (!$returnId || !in_array($action, ['aceptado', 'rechazado'])) {
            return redirect()->back()->with('error', 'Datos inválidos.');
        }

        $returnRecord = $this->returnModel->find($returnId);
        if (!$returnRecord || $returnRecord['status'] !== 'pendiente') {
            return redirect()->to('director/returns')->with('error', 'Devolución no encontrada o ya procesada.');
        }

        $assignment = $this->assignmentModel->find($returnRecord['assignment_id']);
        if (!$assignment) {
            return redirect()->to('director/returns')->with('error', 'Asignación no encontrada.');
        }

        $currentDirectorId = session()->get('user_id');
        if ($assignment['assigned_by'] != $currentDirectorId) {
            return redirect()->to('director/returns')->with('error', 'No tienes autorización para procesar esta devolución.');
        }

        $directorName = session()->get('name') ?? 'Director';
        $liderName = $this->userModel->find($returnRecord['returned_by'])['name'] ?? 'Líder';

        try {
            $this->db->transStart();

            $this->returnModel->update($returnId, [
                'status' => $action,
                'director_response' => $directorResponse,
                'responded_by' => $currentDirectorId,
                'responded_at' => date('Y-m-d H:i:s'),
            ]);

            if ($action === 'aceptado') {
                $oldAssignmentStatus = $assignment['status'];
                $this->assignmentModel->update($assignment['id'], [
                    'status' => 'cancelada',
                ]);
                audit_status_change(
                    entityType: 'assignments',
                    entityId: (int) $assignment['id'],
                    oldStatus: $oldAssignmentStatus,
                    newStatus: 'cancelada',
                    description: "Director '{$directorName}' aceptó la devolución del líder '{$liderName}'. Motivo: {$directorResponse}"
                );

                $document = $this->documentModel->find($assignment['document_id']);
                $oldDocumentStatus = $document['status'] ?? null;
                $this->documentModel->update($assignment['document_id'], [
                    'status' => 'rechazado',
                    'review_notes' => "Devuelto por líder '{$liderName}': {$directorResponse}",
                ]);
                audit_status_change(
                    entityType: 'documents',
                    entityId: (int) $assignment['document_id'],
                    oldStatus: $oldDocumentStatus,
                    newStatus: 'rechazado',
                    description: "Documento devuelto por líder '{$liderName}' y aceptado por director '{$directorName}'."
                );
            } else {
                audit_status_change(
                    entityType: 'assignments',
                    entityId: (int) $assignment['id'],
                    oldStatus: $assignment['status'],
                    newStatus: $assignment['status'],
                    description: "Director '{$directorName}' rechazó la devolución del líder '{$liderName}'. Motivo: {$directorResponse}"
                );
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Falló la transacción.');
            }

            $message = $action === 'aceptado'
                ? 'Devolución aceptada. El documento vuelve a revisión.'
                : 'Devolución rechazada. La asignación continúa.';

            return redirect()->to('director/returns')->with('success', $message);

        } catch (\Exception $e) {
            log_message('error', '[ReturnsController::handleReturn] Error: ' . $e->getMessage());
            return redirect()->to('director/returns')->with('error', 'Error al procesar la devolución.');
        }
    }
}