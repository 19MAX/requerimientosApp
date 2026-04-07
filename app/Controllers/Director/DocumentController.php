<?php

namespace App\Controllers\Director;

use App\Controllers\BaseController;
use App\Models\AssignmentModel;
use App\Models\DocumentModel;
use App\Models\UsersModel;

class DocumentController extends BaseController
{
    protected $documentModel;
    protected $userModel;
    protected $assignmentModel;
    protected $usersModel;
    protected $db;

    public function __construct()
    {
        $this->documentModel = new DocumentModel();
        $this->userModel = new UsersModel();
        $this->assignmentModel = new AssignmentModel();
        $this->usersModel = new UsersModel();
        $this->db = \Config\Database::connect(); // Instancia para usar transacciones
        helper(['status', 'formatDate']);
    }

    public function index()
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

    /**
     * Maneja la revisión unificada: aprueba+asigna o rechaza en un solo POST.
     * Ruta sugerida: POST director/review-documents/handle
     */
    public function handleReview()
    {
        $documentId = $this->request->getPost('document_id');
        $status = $this->request->getPost('status');   // 'aprobado' | 'rechazado'

        // ── Validación base ─────────────────────────────────────────
        $baseRules = [
            'document_id' => 'required|integer',
            'status' => 'required|in_list[aprobado,rechazado]',
        ];

        if (!$this->validate($baseRules)) {
            $errores = implode('<br>', $this->validator->getErrors());
            return redirect()->back()->with('error', [
                'text' => $errores,
                'position' => 'center'
            ]);
        }

        // ── Verificar existencia y estado del documento ──────────────
        $document = $this->documentModel->find($documentId);

        if (!$document) {
            return redirect()->to('director/review-documents')
                ->with('error', [
                    'text' => 'El documento no existe.',
                    'position' => 'center'
                ]);
        }

        if (!in_array($document['status'], ['pendiente', 'en_revision'])) {
            return redirect()->to('director/review-documents')
                ->with('error', [
                    'text' => 'Este documento ya fue procesado anteriormente.',
                    'position' => 'center'
                ]);
        }

        // ── Enrutar según decisión ───────────────────────────────────
        return $status === 'aprobado'
            ? $this->_approveAndAssign($documentId, $document)
            : $this->_reject($documentId);
    }

    // ────────────────────────────────────────────────────────────────
    // Métodos internos (privados)
    // ────────────────────────────────────────────────────────────────

    /**
     * Aprueba el documento y crea la asignación al líder en una sola transacción.
     */
    private function _approveAndAssign(int $documentId, array $document): \CodeIgniter\HTTP\RedirectResponse
    {
        // Validación específica para aprobación
        $rules = [
            'assigned_to' => 'required|integer',
            'instructions' => 'required|min_length[10]',
            'due_date' => 'permit_empty|valid_date',
        ];

        if (!$this->validate($rules)) {
            $errores = implode('<br>', $this->validator->getErrors());
            return redirect()->back()
                ->withInput()
                ->with('error', [
                    'text' => $errores,
                    'position' => 'center'
                ]);
        }

        try {
            $this->db->transStart();

            // 1. Marcar documento como aprobado
            $this->documentModel->update($documentId, [
                'status' => 'aprobado',
                'reviewed_by' => session()->get('user_id'),
                'reviewed_at' => date('Y-m-d H:i:s'),
            ]);

            // 1.1 Verificar si existe la asignación

            $asignacionExistente = $this->assignmentModel
                ->where('document_id', $documentId)
                ->whereIn('status', ['pendiente', 'en_progreso'])
                ->first();

            // 1.2 Si ya existe una asignación activa, no crear una nueva, solo actualizar la existente

            if ($asignacionExistente) {
                $this->assignmentModel->update($asignacionExistente['id'], [
                    'assigned_to' => $this->request->getPost('assigned_to'),
                    'instructions' => $this->request->getPost('instructions'),
                    'status' => 'pendiente',
                    'due_date' => $this->request->getPost('due_date') ?: null,
                    'assigned_by' => session()->get('user_id'),   // El director que aprobó
                    'assigned_at' => date('Y-m-d H:i:s'),
                ]);
            } else {

                // 2. Crear la asignación
                $this->assignmentModel->insert([
                    'document_id' => $documentId,
                    'assigned_by' => session()->get('user_id'),
                    'assigned_to' => $this->request->getPost('assigned_to'),
                    'instructions' => $this->request->getPost('instructions'),
                    'due_date' => $this->request->getPost('due_date') ?: null,
                    'status' => 'pendiente',
                    'assigned_at' => date('Y-m-d H:i:s'),
                ]);
            }

            // 3. Actualizar documento a 'asignado'
            $this->documentModel->update($documentId, [
                'status' => 'asignado',
            ]);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Falló la transacción al aprobar y asignar.');
            }

            return redirect()->to('director/review-documents')
                ->with('success', [
                    'text' => 'Documento aprobado y asignado correctamente.',
                    'position' => 'top-end'
                ]);

        } catch (\Exception $e) {
            log_message('critical', '[DocumentController::_approveAndAssign] Doc ID ' . $documentId . ': ' . $e->getMessage());
            return redirect()->to('director/review-documents')
                ->with('error', [
                    'text' => 'Error interno al aprobar el documento.',
                    'position' => 'center'
                ]);
        }
    }

    /**
     * Rechaza el documento y guarda el motivo.
     */
    private function _reject(int $documentId): \CodeIgniter\HTTP\RedirectResponse
    {
        // Validación específica para rechazo
        $rules = [
            'review_notes' => 'required|min_length[10]',
        ];

        if (!$this->validate($rules)) {
            $errores = implode('<br>', $this->validator->getErrors());
            return redirect()->back()
                ->withInput()
                ->with('error', [
                    'text' => $errores,
                    'position' => 'center'
                ]);
        }

        try {
            $this->documentModel->update($documentId, [
                'status' => 'rechazado',
                'reviewed_by' => session()->get('user_id'),
                'reviewed_at' => date('Y-m-d H:i:s'),
                'review_notes' => $this->request->getPost('review_notes'),
            ]);

            return redirect()->to('director/review-documents')
                ->with('success', [
                    'text' => 'Documento rechazado correctamente.',
                    'position' => 'top-end'
                ]);

        } catch (\Exception $e) {
            log_message('error', '[DocumentController::_reject] Doc ID ' . $documentId . ': ' . $e->getMessage());
            return redirect()->to('director/review-documents')
                ->with('error', [
                    'text' => 'Error interno al rechazar el documento.',
                    'position' => 'center'
                ]);
        }
    }




    /**
     * Reasigna un documento a otro líder, o reabre para cambiar la decisión.
     * Ruta: POST director/review-documents/reassign
     *
     * Recibe:
     *   - document_id   (int)
     *   - action        'reassign' | 'reopen'
     *
     * Si action = reassign:
     *   - assigned_to   (int)
     *   - instructions  (string)
     *   - due_date      (date, opcional)
     *
     * Si action = reopen:
     *   - no campos extra, solo devuelve a 'en_revision'
     */
    public function reassign()
    {
        $documentId = (int) $this->request->getPost('document_id');
        $action = $this->request->getPost('action'); // 'reassign' | 'reopen'

        // ── Validación base ──────────────────────────────────────────
        if (
            !$this->validate([
                'document_id' => 'required|integer',
                'action' => 'required|in_list[reassign,reopen]',
            ])
        ) {
            return redirect()->back()->with('error', implode(' ', $this->validator->getErrors()));
        }

        // ── Verificar existencia del documento ───────────────────────
        $document = $this->documentModel->find($documentId);

        if (!$document) {
            return redirect()->to('director/review-documents')
                ->with('error', 'El documento no existe.');
        }

        // ── Solo se puede reasignar/reabrir si NO está completado ────
        $estadosPermitidos = ['aprobado', 'asignado', 'rechazado'];

        if (!in_array($document['status'], $estadosPermitidos)) {
            return redirect()->to('director/review-documents')
                ->with('error', 'No se puede modificar un documento en estado "' . $document['status'] . '".');
        }

        return $action === 'reassign'
            ? $this->_doReassign($documentId, $document)
            : $this->_doReopen($documentId, $document);
    }

    /**
     * Cancela la asignación activa y crea una nueva para otro líder.
     */
    private function _doReassign(int $documentId, array $document): \CodeIgniter\HTTP\RedirectResponse
    {
        if (
            !$this->validate([
                'assigned_to' => 'required|integer',
                'instructions' => 'required|min_length[5]',
                'due_date' => 'permit_empty|valid_date',
            ])
        ) {
            return redirect()->back()
                ->withInput()
                ->with('error', implode(' ', $this->validator->getErrors()));
        }

        if ($document['status'] === 'trabajando') {
            return redirect()->to('director/review-documents')
                ->with('error', 'El líder ya comenzó a trabajar en este requerimiento. No se puede reasignar.');
        }

        try {
            $this->db->transStart();

            // Obtener la asignación activa del documento
            $asignacionActiva = $this->assignmentModel
                ->where('document_id', $documentId)
                ->whereIn('status', ['pendiente', 'en_progreso'])
                ->orderBy('assigned_at', 'DESC')
                ->first();

            $nuevoLiderId = $this->request->getPost('assigned_to');
            $nuevasInstruc = $this->request->getPost('instructions');
            $nuevaFecha = $this->request->getPost('due_date') ?: null;
            $directorId = session()->get('user_id');

            if ($asignacionActiva) {
                // ── ACTUALIZAR asignación existente ──────────────────
                // Se guardan los valores anteriores en el historial antes de pisar los datos
                $liderAnterior = $asignacionActiva['assigned_to'];

                $this->assignmentModel->update($asignacionActiva['id'], [
                    'assigned_to' => $nuevoLiderId,
                    'instructions' => $nuevasInstruc,
                    'due_date' => $nuevaFecha,
                    'assigned_by' => $directorId,   // El director que reasignó
                    'assigned_at' => date('Y-m-d H:i:s'),
                    'status' => 'pendiente',    // Vuelve a pendiente para el nuevo líder
                    'started_at' => null,           // Limpiar si ya había iniciado
                ]);

                // ── HISTORIAL: registrar cambio de responsable ────────
                // Tabla sugerida: assignment_logs (id, assignment_id, document_id,
                //   action, from_user_id, to_user_id, changed_by, notes, created_at)
                $this->db->table('assignment_logs')->insert([
                    'assignment_id' => $asignacionActiva['id'],
                    'document_id' => $documentId,
                    'action' => 'reasignado',
                    'from_user_id' => $liderAnterior,
                    'to_user_id' => $nuevoLiderId,
                    'changed_by' => $directorId,
                    'notes' => 'Reasignación manual por el director. Instrucciones actualizadas.',
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

            } else {
                // Caso borde: documento aprobado pero sin asignación previa (no debería pasar)
                // Se crea la asignación desde cero y se registra como creación
                $newId = $this->assignmentModel->insert([
                    'document_id' => $documentId,
                    'assigned_by' => $directorId,
                    'assigned_to' => $nuevoLiderId,
                    'instructions' => $nuevasInstruc,
                    'due_date' => $nuevaFecha,
                    'status' => 'pendiente',
                    'assigned_at' => date('Y-m-d H:i:s'),
                ]);

                // ── HISTORIAL: registrar creación de asignación ───────
                $this->db->table('assignment_logs')->insert([
                    'assignment_id' => $newId,
                    'document_id' => $documentId,
                    'action' => 'creado',
                    'from_user_id' => null,
                    'to_user_id' => $nuevoLiderId,
                    'changed_by' => $directorId,
                    'notes' => 'Asignación creada directamente desde reasignación (caso borde: no existía asignación previa).',
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            // Documento se mantiene en 'asignado' porque sigue estando asignado, solo cambia el líder responsable
            $this->documentModel->update($documentId, [
                'status' => 'asignado',
            ]);

            // ── HISTORIAL: registrar cambio de estado del documento ───
            $this->db->table('assignment_logs')->insert([
                'assignment_id' => $asignacionActiva['id'] ?? null,
                'document_id' => $documentId,
                'action' => 'estado_documento',
                'from_user_id' => null,
                'to_user_id' => null,
                'changed_by' => $directorId,
                'notes' => 'Estado del documento mantenido en "asignado" tras reasignación.',
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Falló la transacción al reasignar.');
            }

            return redirect()->to('director/review-documents')
                ->with('success', 'Requerimiento reasignado correctamente al nuevo líder.');

        } catch (\Exception $e) {
            log_message('critical', '[DocumentController::_doReassign] Doc ID ' . $documentId . ': ' . $e->getMessage());
            return redirect()->to('director/review-documents')
                ->with('error', 'Error interno al reasignar. Contacte a soporte.');
        }
    }

    /**
     * Devuelve el documento a 'en_revision' para que el director
     * pueda tomar una nueva decisión (aprobar o rechazar).
     */
    private function _doReopen(int $documentId, array $document): \CodeIgniter\HTTP\RedirectResponse
    {
        // Bloquear reapertura si el líder ya comenzó
        if ($document['status'] === 'trabajando') {
            return redirect()->to('director/review-documents')
                ->with('error', 'El líder ya comenzó a trabajar. No se puede reabrir la decisión.');
        }

        try {
            $this->db->transStart();

            // 1. Cancelar asignación activa si existe
            $asignacionActiva = $this->assignmentModel
                ->where('document_id', $documentId)
                ->whereIn('status', ['pendiente'])
                ->orderBy('assigned_at', 'DESC')
                ->first();

            if ($asignacionActiva) {
                $this->assignmentModel->update($asignacionActiva['id'], [
                    'status' => 'rechazado', // o 'cancelado' según preferencia
                    // 'ended_at' => date('Y-m-d H:i:s'),
                ]);
            }

            // 2. Devolver documento a revisión limpia
            $this->documentModel->update($documentId, [
                'status' => 'pendiente',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_notes' => null,
            ]);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Falló la transacción al reabrir.');
            }

            return redirect()->to('director/review-documents')
                ->with('success', 'Requerimiento devuelto a revisión. Puedes tomar una nueva decisión.');

        } catch (\Exception $e) {
            log_message('critical', '[DocumentController::_doReopen] Doc ID ' . $documentId . ': ' . $e->getMessage());
            return redirect()->to('director/review-documents')
                ->with('error', 'Error interno al reabrir el documento.');
        }
    }

    /**
     * El líder marca que comenzó a trabajar → documento: 'trabajando',
     * asignación: 'en_progreso'.
     * Ruta: POST lider/assignments/start
     */
    public function startWork()
    {
        $assignmentId = (int) $this->request->getPost('assignment_id');

        $assignment = $this->assignmentModel->find($assignmentId);

        if (!$assignment || $assignment['status'] !== 'pendiente') {
            return redirect()->back()
                ->with('error', 'La asignación no existe o ya fue iniciada.');
        }

        try {
            $this->db->transStart();

            $this->assignmentModel->update($assignmentId, [
                'status' => 'en_progreso',
                'started_at' => date('Y-m-d H:i:s'),
            ]);

            $this->documentModel->update($assignment['document_id'], [
                'status' => 'trabajando',
            ]);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Falló la transacción al iniciar trabajo.');
            }

            return redirect()->back()
                ->with('success', 'Actividad iniciada. ¡Éxito en la gestión!');

        } catch (\Exception $e) {
            log_message('error', '[DocumentController::startWork] Assignment ID ' . $assignmentId . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al iniciar la actividad.');
        }
    }

    /**
     * El líder marca como completado → documento: 'completado',
     * asignación: 'completado'.
     * Ruta: POST lider/assignments/complete
     */
    public function completeWork()
    {
        $assignmentId = (int) $this->request->getPost('assignment_id');

        $assignment = $this->assignmentModel->find($assignmentId);

        if (!$assignment || $assignment['status'] !== 'en_progreso') {
            return redirect()->back()
                ->with('error', 'La asignación no está en progreso o no existe.');
        }

        try {
            $this->db->transStart();

            $this->assignmentModel->update($assignmentId, [
                'status' => 'completado',
                'completed_at' => date('Y-m-d H:i:s'),
                'result_notes' => $this->request->getPost('result_notes') ?? null,
            ]);

            $this->documentModel->update($assignment['document_id'], [
                'status' => 'completado',
            ]);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Falló la transacción al completar trabajo.');
            }

            return redirect()->back()
                ->with('success', 'Requerimiento marcado como completado exitosamente.');

        } catch (\Exception $e) {
            log_message('error', '[DocumentController::completeWork] Assignment ID ' . $assignmentId . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al completar la actividad.');
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

        $clients = $this->usersModel
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
