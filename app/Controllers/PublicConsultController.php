<?php

namespace App\Controllers;

class PublicConsultController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        helper(['status', 'formatDate']);
    }

    public function index()
    {
        $cedula = trim((string) $this->request->getGet('cedula'));

        $client = null;
        $documents = [];
        $message = null;

        if ($cedula !== '') {
            if (!preg_match('/^[0-9]{6,13}$/', $cedula)) {
                $message = 'La cédula debe contener solo números (6 a 13 dígitos).';
            } else {
                $client = $this->db->table('clients')
                    ->where('cedula', $cedula)
                    ->where('deleted_at', null)
                    ->get()
                    ->getRowArray();

                if (!$client) {
                    $message = 'No se encontró ningún cliente con esa cédula.';
                } else {
                    $documents = $this->db->table('documents')
                        ->where('client_id', $client['id'])
                        ->where('deleted_at', null)
                        ->orderBy('created_at', 'DESC')
                        ->get()
                        ->getResultArray();

                    foreach ($documents as &$document) {
                        $report = $this->db->table('activity_reports ar')
                            ->select('ar.*')
                            ->join('assignments a', 'a.id = ar.assignment_id')
                            ->where('a.document_id', $document['id'])
                            ->orderBy('ar.created_at', 'DESC')
                            ->get()
                            ->getRowArray();

                        $document['original_url'] = !empty($document['file_path']) ? base_url($document['file_path']) : null;
                        $document['final_report'] = $report;
                        $document['final_url'] = (!empty($report) && !empty($report['file_path'])) ? base_url($report['file_path']) : null;
                        $document['history'] = $this->buildHistory((int) $document['id'], $report);
                    }
                    unset($document);

                    if (empty($documents)) {
                        $message = 'El cliente existe, pero todavía no tiene requerimientos registrados.';
                    }
                }
            }
        }

        return view('public/client_consult', [
            'cedula' => $cedula,
            'client' => $client,
            'documents' => $documents,
            'message' => $message,
        ]);
    }

    private function buildHistory(int $documentId, ?array $report): array
    {
        $history = [];

        $document = $this->db->table('documents')
            ->select('created_at, reviewed_at, status, review_notes')
            ->where('id', $documentId)
            ->get()
            ->getRowArray();

        if (!empty($document['created_at'])) {
            $history[] = [
                'date' => $document['created_at'],
                'title' => 'Requerimiento registrado',
                'description' => 'El requerimiento fue ingresado al sistema.',
            ];
        }

        if (!empty($document['reviewed_at'])) {
            $history[] = [
                'date' => $document['reviewed_at'],
                'title' => 'Revisión técnica',
                'description' => $document['status'] === 'rechazado'
                    ? 'El requerimiento no fue aprobado tras la revisión inicial. ' . ($document['review_notes'] ?? '')
                    : 'El requerimiento superó la revisión técnica inicial.',
            ];
        }

        $documentStatusChanges = $this->db->table('audit_trail at')
            ->select('at.old_status, at.new_status, at.description, at.created_at')
            ->where('at.entity_type', 'documents')
            ->where('at.entity_id', $documentId)
            ->where('at.old_status IS NOT NULL', null, false)
            ->where('at.new_status IS NOT NULL', null, false)
            ->orderBy('at.created_at', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($documentStatusChanges as $change) {
            $toStatus = $this->statusLabel($change['new_status'] ?? null);

            $description = "El estado del requerimiento cambió a '{$toStatus}'.";

            $history[] = [
                'date' => $change['created_at'],
                'title' => 'Actualización de estado',
                'description' => $description,
            ];
        }

        $assignments = $this->db->table('assignments a')
            ->select('a.*')
            ->where('a.document_id', $documentId)
            ->orderBy('a.assigned_at', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($assignments as $assignment) {
            if (!empty($assignment['assigned_at'])) {
                $history[] = [
                    'date' => $assignment['assigned_at'],
                    'title' => 'Asignación de área',
                    'description' => 'El requerimiento ha sido derivado al área técnica correspondiente para su gestión.',
                ];
            }

            if (!empty($assignment['started_at'])) {
                $history[] = [
                    'date' => $assignment['started_at'],
                    'title' => 'Gestión iniciada',
                    'description' => 'El equipo técnico ha iniciado los trabajos relacionados con este requerimiento.',
                ];
            }

            if (!empty($assignment['completed_at'])) {
                $history[] = [
                    'date' => $assignment['completed_at'],
                    'title' => 'Gestión finalizada',
                    'description' => 'Se ha completado la gestión técnica del requerimiento.',
                ];
            }
        }

        if (!empty($report['created_at'])) {
            $history[] = [
                'date' => $report['created_at'],
                'title' => 'Resultado disponible',
                'description' => 'El documento de respuesta o evidencia ha sido cargado al sistema.',
            ];
        }

        usort($history, static function ($a, $b) {
            return strtotime($b['date']) <=> strtotime($a['date']);
        });

        return $history;
    }

    private function statusLabel(?string $status): string
    {
        if ($status === null || $status === '') {
            return 'Sin estado';
        }

        return ucfirst(str_replace('_', ' ', $status));
    }
}
