<?php

namespace App\Controllers\Secretaria;

use App\Controllers\BaseController;
use App\Models\ClientsModel;
use App\Models\DocumentModel;
use AuditActions;

class DocumentsController extends BaseController
{
    protected $documentModel;
    protected $clientsModel;
    protected $db;

    public function __construct()
    {
        $this->documentModel = new DocumentModel();
        $this->clientsModel = new ClientsModel();
        $this->db = \Config\Database::connect();
        helper(['document', 'audit', 'status', 'formatDate']);

    }

    public function index()
    {
        $documents = $this->db->table('documents d')
            ->select('
            d.*,
            CONCAT(c.first_name, " ", c.last_name) AS client_full_name,
            c.cedula   AS client_cedula,
            c.phone    AS client_phone
        ')
            ->join('clients c', 'c.id = d.client_id', 'left')
            ->where('d.deleted_at', null)
            ->orderBy('d.id', 'DESC')
            ->get()
            ->getResultArray();

        return view('secretaria/documents/index', [
            'documents' => $documents,
        ]);
    }

    public function create()
    {
        $uploadedFilePath = null;
        try {
            $rules = [
                'client_id' => [
                    'label' => 'Cliente',
                    'rules' => 'required|integer|is_not_unique[clients.id]',
                ],
                'title' => [
                    'label' => 'Título',
                    'rules' => 'required|min_length[5]|max_length[200]',
                ],
                'description' => [
                    'label' => 'Descripción',
                    'rules' => 'permit_empty|max_length[1000]',
                ],
                'document_file' => [
                    'label' => 'Archivo',
                    'rules' => 'uploaded[document_file]|max_size[document_file,5120]|ext_in[document_file,pdf,doc,docx]',
                ],
            ];

            if (!$this->validate($rules)) {
                $errores = implode('<br>', $this->validator->getErrors());
                return redirect()->back()->withInput()->with('error', [
                    'text' => $errores,
                    'position' => 'center',
                ]);
            }

            $file = $this->request->getFile('document_file');
            $userId = session()->get('user_id');
            $userName = session()->get('name');

            // ── PASO 1: Subir archivo al disco ──────────────────────────
            $fileData = uploadDocument($file, $userId);

            if (!$fileData) {
                return redirect()->back()->withInput()->with('error', [
                    'text' => 'Error al procesar el archivo. Asegúrate de que cumple con los requisitos.',
                    'position' => 'center',
                ]);
            }

            $uploadedFilePath = $fileData['file_path'];

            // ── PASO 2: Guardar en DB dentro de una transacción ─────────
            $this->db->transStart();

            $documentCode = generate_document_code();

            $insertData = [
                'document_code' => $documentCode,
                'created_by' => $userId,
                'client_id' => $this->request->getPost('client_id'),
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'file_path' => $fileData['file_path'],
                'file_name' => $fileData['file_name'],
                'file_size' => $fileData['file_size'],
                'file_mime' => $fileData['file_mime'],
                'status' => 'pendiente',
            ];

            $documentId = $this->documentModel->insert($insertData);

            // ── PASO 3: Registrar auditoría dentro de la misma transacción
            audit_log(
                action: AuditActions::DOCUMENT_CREATED,
                entityType: 'documents',
                entityId: $documentId,
                newStatus: 'pendiente',
                newValues: [
                    'document_code' => $documentCode,
                    'client_id' => $insertData['client_id'],
                    'title' => $insertData['title'],
                    'file_name' => $insertData['file_name'],
                    'file_size' => $insertData['file_size'],
                ],
                description: "Secretaria '{$userName}' creó el documento {$documentCode} — '{$insertData['title']}'"
            );

            $this->db->transComplete();

            // ── PASO 4: Verificar que la transacción fue exitosa ─────────
            if ($this->db->transStatus() === false) {
                deleteDocument($uploadedFilePath);
                return redirect()->back()->withInput()->with('error', [
                    'text' => 'Error al guardar el documento en la base de datos.',
                    'position' => 'center',
                ]);
            }

            // Notificar al cliente vía correo electrónico el registro exitoso
            try {
                \App\Services\EmailService::notifyDocumentRegistration($documentId);
            } catch (\Throwable $e) {
                log_message('error', '[EmailService] Falló el envío de correo de registro inicial. DocID: ' . $documentId . '. Error: ' . $e->getMessage());
            }

            return redirect()->to('secretaria/documents')->with('success', [
                'text' => "Documento {$documentCode} creado correctamente.",
                'position' => 'top-end',
            ]);

        } catch (\Exception $e) {
            if ($uploadedFilePath) {
                deleteDocument($uploadedFilePath);
            }
            log_message('error', '[DocumentsController::create] ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', [
                'text' => 'Ocurrió un error inesperado al crear el documento.',
                'position' => 'center',
            ]);
        }
    }


    // ============================================================
    // Cambio de estado — úsalo en cualquier acción que modifique
    // el status del documento (aprobar, rechazar, completar, etc.)
    // ============================================================

    public function updateStatus(int $documentId, string $newStatus): void
    {
        $userName = session()->get('name');

        // Obtener estado actual antes de actualizar
        $document = $this->documentModel->find($documentId);

        if (!$document) {
            return;
        }

        $oldStatus = $document['status'];


        // Descripción legible según la transición
        $descriptions = [
            'en_revision' => "Director '{$userName}' puso en revisión el documento {$document['document_code']}",
            'aprobado' => "Director '{$userName}' aprobó el documento {$document['document_code']}",
            'rechazado' => "Director '{$userName}' rechazó el documento {$document['document_code']}",
            'asignado' => "Director '{$userName}' asignó el documento {$document['document_code']}",
            'completado' => "Líder '{$userName}' completó la actividad del documento {$document['document_code']}",
        ];

        $this->db->transStart();

        $this->documentModel->update($documentId, [
            'status' => $newStatus,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        audit_status_change(
            entityType: 'documents',
            entityId: $documentId,
            oldStatus: $oldStatus,
            newStatus: $newStatus,
            description: $descriptions[$newStatus] ?? "Estado cambiado de '{$oldStatus}' a '{$newStatus}'",
            oldValues: ['status' => $oldStatus],
            newValues: ['status' => $newStatus]
        );

        $this->db->transComplete();
    }

    public function update()
    {
        $newFilePath = null; // Rastrea el nuevo archivo para rollback
        $oldFilePath = null; // Guarda la ruta anterior para restaurar si es necesario

        try {
            $id = $this->request->getPost('id');
            $document = $this->documentModel->find($id);

            if (!$document) {
                return redirect()->to('secretaria/documents')->with('error', [
                    'text' => 'Documento no encontrado.',
                    'position' => 'center',
                ]);
            }

            if ($document['status'] !== 'pendiente') {
                return redirect()->to('secretaria/documents')->with('error', [
                    'text' => 'Solo se pueden editar documentos en estado pendiente.',
                    'position' => 'center',
                ]);
            }

            $rules = [
                'client_id' => [
                    'label' => 'Cliente',
                    'rules' => 'required|integer|is_not_unique[clients.id]',
                ],
                'title' => ['label' => 'Título', 'rules' => 'required|max_length[200]'],
                'description' => ['label' => 'Descripción', 'rules' => 'permit_empty|max_length[1000]'],
            ];

            $file = $this->request->getFile('document_file');
            $newFileUploaded = $file && $file->isValid() && !$file->hasMoved();

            if ($newFileUploaded) {
                $rules['document_file'] = 'max_size[document_file,5120]|ext_in[document_file,pdf,doc,docx]';
            }

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('error', [
                    'text' => implode('<br>', $this->validator->getErrors()),
                    'position' => 'center',
                ]);
            }

            $data = [
                'client_id' => $this->request->getPost('client_id'),
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
            ];

            // ── PASO 1: Subir nuevo archivo si fue seleccionado ──────────
            if ($newFileUploaded) {
                $userId = session()->get('user_id');
                $fileData = uploadDocument($file, $userId);

                if (!$fileData) {
                    return redirect()->back()->withInput()->with('error', [
                        'text' => 'Error al procesar el nuevo archivo.',
                        'position' => 'center',
                    ]);
                }

                // Registrar rutas para posible rollback
                $newFilePath = $fileData['file_path'];
                $oldFilePath = $document['file_path'];

                $data['file_path'] = $fileData['file_path'];
                $data['file_name'] = $fileData['file_name'];
                $data['file_size'] = $fileData['file_size'];
                $data['file_mime'] = $fileData['file_mime'];
            }

            // ── PASO 2: Actualizar DB dentro de una transacción ──────────
            $this->db->transStart();

            $this->documentModel->update($id, $data);

            $this->db->transComplete();

            // ── PASO 3: Verificar que la transacción fue exitosa ──────────
            if ($this->db->transStatus() === false) {
                // DB falló → eliminar el nuevo archivo subido (mantener el anterior intacto)
                if ($newFilePath) {
                    deleteDocument($newFilePath);
                }
                return redirect()->back()->withInput()->with('error', [
                    'text' => 'Error al actualizar el documento en la base de datos.',
                    'position' => 'center',
                ]);
            }

            // ── PASO 4: DB exitosa → ahora sí eliminar el archivo anterior
            if ($oldFilePath) {
                deleteDocument($oldFilePath);
            }

            return redirect()->to('secretaria/documents')->with('success', [
                'text' => "Documento actualizado correctamente.",
                'position' => 'top-end',
            ]);

        } catch (\Exception $e) {
            // Excepción inesperada → eliminar nuevo archivo si ya fue subido
            if ($newFilePath) {
                deleteDocument($newFilePath);
            }

            log_message('error', '[DocumentsController::update] Error en ID ' . ($id ?? 'N/A') . ': ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', [
                'text' => 'Ocurrió un error inesperado al actualizar el documento.',
                'position' => 'center',
            ]);
        }
    }

    public function delete()
    {
        try {
            $id = $this->request->getPost('id');
            $document = $this->documentModel->find($id);

            if (!$document) {
                return redirect()->to('secretaria/documents')->with('error', [
                    'text' => 'Documento no encontrado.',
                    'position' => 'center',
                ]);
            }

            if ($document['status'] !== 'pendiente') {
                return redirect()->to('secretaria/documents')->with('error', [
                    'text' => 'No se puede eliminar un documento que ya está en proceso.',
                    'position' => 'center',
                ]);
            }

            // ── PASO 1: Soft delete en DB dentro de una transacción ──────
            $this->db->transStart();

            $this->documentModel->delete($id);

            $this->db->transComplete();

            // ── PASO 2: Solo eliminar el archivo físico si la DB fue exitosa
            if ($this->db->transStatus() === false) {
                return redirect()->to('secretaria/documents')->with('error', [
                    'text' => 'Error al eliminar el documento de la base de datos.',
                    'position' => 'center',
                ]);
            }

            deleteDocument($document['file_path']);

            return redirect()->to('secretaria/documents')->with('success', [
                'text' => 'Documento eliminado correctamente.',
                'position' => 'top-end',
            ]);

        } catch (\Exception $e) {
            log_message('error', '[DocumentsController::delete] Error al eliminar ID ' . ($id ?? 'N/A') . ': ' . $e->getMessage());
            return redirect()->to('secretaria/documents')->with('error', [
                'text' => 'Ocurrió un error al intentar eliminar el documento.',
                'position' => 'center',
            ]);
        }
    }

    public function download($id)
    {
        $document = $this->documentModel->find($id);

        if (!$document || empty($document['file_path'])) {
            return redirect()->back()->with('error', [
                'text' => 'Archivo no encontrado.',
                'position' => 'center',
            ]);
        }

        $filePath = ROOTPATH . 'public/' . $document['file_path'];

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', [
                'text' => 'El archivo físico no existe en el servidor.',
                'position' => 'center',
            ]);
        }

        return $this->response->download($filePath, null)->setFileName($document['file_name']);
    }

    public function search(): \CodeIgniter\HTTP\ResponseInterface
    {
        $q = trim($this->request->getGet('q') ?? '');

        if (strlen($q) < 2) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Ingresa al menos 2 caracteres para buscar.',
                'data' => [],
            ]);
        }

        $clients = $this->clientsModel
            ->groupStart()
            ->like('cedula', $q, 'both')
            ->orLike('first_name', $q, 'both')
            ->orLike('last_name', $q, 'both')
            ->orLike("CONCAT(first_name, ' ', last_name)", $q, 'both')
            ->groupEnd()
            ->where('deleted_at', null)
            ->orderBy('first_name', 'ASC')
            ->findAll(10); // máximo 10 resultados

        return $this->response->setJSON([
            'success' => true,
            'count' => count($clients),
            'data' => $clients,
        ]);
    }

    public function store(): \CodeIgniter\HTTP\ResponseInterface
    {
        try {
            $data = $this->request->getPost([
                'first_name',
                'last_name',
                'cedula',
                'email',
                'phone',
                'address',
            ]);

            $id = $this->clientsModel->insert($data);

            if ($id === false) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Error de validación.',
                        'errors' => $this->clientsModel->errors(),
                    ]);
            }

            $client = $this->clientsModel->find($id);

            return $this->response
                ->setStatusCode(201)
                ->setJSON([
                    'success' => true,
                    'message' => 'Cliente creado correctamente.',
                    'data' => $client,
                ]);

        } catch (\Exception $e) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Error al crear el cliente: ' . $e->getMessage(),
                ]);
        }
    }
}