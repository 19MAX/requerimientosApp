<?php

namespace App\Controllers\Lider;

use App\Controllers\BaseController;
use App\Models\ActivityReportModel;
use App\Models\AssignmentModel;
use App\Models\DocumentModel;
use CodeIgniter\HTTP\ResponseInterface;

class MyAssignmentsController extends BaseController
{
protected $assignmentModel;
    protected $documentModel;
    protected $reportModel;
    protected $db;

    public function __construct()
    {
        $this->assignmentModel = new AssignmentModel();
        $this->documentModel = new DocumentModel();
        $this->reportModel = new ActivityReportModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $userId = session()->get('user_id');

        // Obtener las asignaciones del usuario logeado con JOIN para traer info del doc y del director
        $assignments = $this->assignmentModel
            ->select('assignments.*, documents.title as document_title, documents.file_path as doc_file_path, users.name as director_name')
            ->join('documents', 'documents.id = assignments.document_id')
            ->join('users', 'users.id = assignments.assigned_by')
            ->where('assignments.assigned_to', $userId)
            ->orderBy('assignments.created_at', 'DESC')
            ->findAll();

        return view('lider/my_assignments/index', [
            'assignments' => $assignments
        ]);
    }

    /**
     * Cambia el estado de la tarea de Pendiente a En Progreso
     */
    public function startTask()
    {
        try {
            $assignmentId = $this->request->getPost('assignment_id');
            $userId = session()->get('user_id');

            $assignment = $this->assignmentModel->find($assignmentId);

            // Validaciones de seguridad
            if (!$assignment || $assignment['assigned_to'] != $userId) {
                return redirect()->to('lider/my-assignments')->with('error', 'Asignación no encontrada o no autorizada.');
            }

            if ($assignment['status'] !== 'pendiente') {
                return redirect()->to('lider/my-assignments')->with('error', 'La tarea ya fue iniciada o completada previamente.');
            }

            // Actualizar estado y registrar la fecha de inicio
            $this->assignmentModel->update($assignmentId, [
                'status'     => 'en_progreso',
                'started_at' => date('Y-m-d H:i:s')
            ]);

            return redirect()->to('lider/my-assignments')->with('success', 'Has iniciado la tarea. ¡Mucho éxito!');

        } catch (\Exception $e) {
            log_message('error', '[MyAssignmentsController::startTask] Error: ' . $e->getMessage());
            return redirect()->to('lider/my-assignments')->with('error', 'Ocurrió un error al iniciar la tarea.');
        }
    }

    /**
     * Sube la evidencia y finaliza el proceso completo
     */
    public function reportTask()
    {
        $uploadedFilePath = null; // Rastrea el archivo subido para rollback

        try {
            $rules = [
                'assignment_id' => 'required|integer',
                'document_id'   => 'required|integer',
                'comment'       => 'required',
                'report_file'   => 'max_size[report_file,5120]|ext_in[report_file,pdf,doc,docx,jpg,jpeg,png]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
            }

            $assignmentId = $this->request->getPost('assignment_id');
            $documentId   = $this->request->getPost('document_id');
            $userId       = session()->get('user_id');

            // Validar existencia y propiedad
            $assignment = $this->assignmentModel->find($assignmentId);
            if (!$assignment || $assignment['assigned_to'] != $userId || $assignment['status'] !== 'en_progreso') {
                return redirect()->to('lider/my-assignments')->with('error', 'No puedes reportar esta tarea en su estado actual.');
            }

            // ── PASO 1: Procesar el archivo adjunto usando tu Helper Dinámico ──
            $file = $this->request->getFile('report_file');
            $fileData = null;

            if ($file && $file->isValid()) {
                // Pasamos 'reports' como tercer parámetro para guardarlo en la carpeta correcta
                $fileData = uploadDocument($file, $userId, 'reports');

                if (!$fileData) {
                    return redirect()->back()->withInput()->with('error', 'Error al procesar el archivo de evidencia.');
                }

                // Guardar la ruta por si necesitamos hacer rollback
                $uploadedFilePath = $fileData['file_path'];
            }

            // ── PASO 2: INICIO DE LA TRANSACCIÓN ──
            $this->db->transStart();

            // Insertar el reporte en activity_reports
            $this->reportModel->insert([
                'assignment_id' => $assignmentId,
                'reported_by'   => $userId,
                'comment'       => $this->request->getPost('comment'),
                'file_path'     => $fileData ? $fileData['file_path'] : null,
                'file_name'     => $fileData ? $fileData['file_name'] : null,
                'file_size'     => $fileData ? $fileData['file_size'] : null,
                'file_mime'     => $fileData ? $fileData['file_mime'] : null
            ]);

            // Marcar la asignación como completada
            $this->assignmentModel->update($assignmentId, [
                'status'       => 'completada',
                'completed_at' => date('Y-m-d H:i:s')
            ]);

            // Marcar el documento original como completado
            $this->documentModel->update($documentId, [
                'status' => 'completado'
            ]);

            // FIN DE LA TRANSACCIÓN
            $this->db->transComplete();

            // ── PASO 3: Verificar éxito y Rollback si es necesario ──
            if ($this->db->transStatus() === false) {
                // DB falló → eliminar el archivo ya subido
                if ($uploadedFilePath) {
                    deleteDocument($uploadedFilePath);
                }
                return redirect()->back()->withInput()->with('error', 'Falló la transacción al guardar el reporte en la base de datos.');
            }

            return redirect()->to('lider/my-assignments')->with('success', 'Reporte enviado con éxito. La actividad ha sido marcada como completada.');

        } catch (\Exception $e) {
            // Excepción inesperada → eliminar archivo si ya fue subido
            if ($uploadedFilePath) {
                deleteDocument($uploadedFilePath);
            }

            log_message('critical', '[MyAssignmentsController::reportTask] Error al procesar reporte: ' . $e->getMessage());
            return redirect()->to('lider/my-assignments')->with('error', 'Ocurrió un error crítico al intentar guardar tu reporte. Contacta a soporte.');
        }
    }
}
