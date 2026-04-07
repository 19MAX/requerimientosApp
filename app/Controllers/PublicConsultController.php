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
                'title' => 'Revisión de dirección',
                'description' => $document['status'] === 'rechazado'
                    ? 'Requerimiento rechazado. ' . ($document['review_notes'] ?? '')
                    : 'Requerimiento revisado por dirección.',
            ];
        }

        $documentStatusChanges = $this->db->table('audit_trail at')
            ->select('at.old_status, at.new_status, at.description, at.created_at, u.name as changed_by_name')
            ->join('users u', 'u.id = at.user_id', 'left')
            ->where('at.entity_type', 'documents')
            ->where('at.entity_id', $documentId)
            ->where('at.old_status IS NOT NULL', null, false)
            ->where('at.new_status IS NOT NULL', null, false)
            ->orderBy('at.created_at', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($documentStatusChanges as $change) {
            $fromStatus = $this->statusLabel($change['old_status'] ?? null);
            $toStatus = $this->statusLabel($change['new_status'] ?? null);

            $description = "El requerimiento pasó de '{$fromStatus}' a '{$toStatus}'.";

            if (!empty($change['description'])) {
                $description .= ' ' . $change['description'];
            }

            if (!empty($change['changed_by_name'])) {
                $description .= ' Responsable: ' . $change['changed_by_name'] . '.';
            }

            $history[] = [
                'date' => $change['created_at'],
                'title' => 'Cambio de estado del requerimiento',
                'description' => $description,
            ];
        }

        $assignments = $this->db->table('assignments a')
            ->select('a.*, u1.name as director_name, u2.name as lider_name')
            ->join('users u1', 'u1.id = a.assigned_by', 'left')
            ->join('users u2', 'u2.id = a.assigned_to', 'left')
            ->where('a.document_id', $documentId)
            ->orderBy('a.assigned_at', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($assignments as $assignment) {
            if (!empty($assignment['assigned_at'])) {
                $history[] = [
                    'date' => $assignment['assigned_at'],
                    'title' => 'Asignación a líder de área',
                    'description' => 'Asignado a ' . ($assignment['lider_name'] ?? 'N/D') . ' por ' . ($assignment['director_name'] ?? 'N/D') . '.',
                ];
            }

            if (!empty($assignment['started_at'])) {
                $history[] = [
                    'date' => $assignment['started_at'],
                    'title' => 'Trabajo iniciado',
                    'description' => ($assignment['lider_name'] ?? 'El líder') . ' inició la gestión del requerimiento.',
                ];
            }

            if (!empty($assignment['completed_at'])) {
                $history[] = [
                    'date' => $assignment['completed_at'],
                    'title' => 'Actividad completada',
                    'description' => ($assignment['lider_name'] ?? 'El líder') . ' reportó la actividad como completada.',
                ];
            }
        }

        $assignmentIds = array_column($assignments, 'id');

        if (!empty($assignmentIds)) {
            $assignmentStatusChanges = $this->db->table('audit_trail at')
                ->select('at.entity_id, at.old_status, at.new_status, at.description, at.created_at, u.name as changed_by_name')
                ->join('users u', 'u.id = at.user_id', 'left')
                ->where('at.entity_type', 'assignments')
                ->whereIn('at.entity_id', $assignmentIds)
                ->where('at.old_status IS NOT NULL', null, false)
                ->where('at.new_status IS NOT NULL', null, false)
                ->orderBy('at.created_at', 'ASC')
                ->get()
                ->getResultArray();

            foreach ($assignmentStatusChanges as $change) {
                $fromStatus = $this->statusLabel($change['old_status'] ?? null);
                $toStatus = $this->statusLabel($change['new_status'] ?? null);
                $description = "La asignación pasó de '{$fromStatus}' a '{$toStatus}'.";

                if (!empty($change['description'])) {
                    $description .= ' ' . $change['description'];
                }

                if (!empty($change['changed_by_name'])) {
                    $description .= ' Responsable: ' . $change['changed_by_name'] . '.';
                }

                $history[] = [
                    'date' => $change['created_at'],
                    'title' => 'Cambio de estado de asignación',
                    'description' => $description,
                ];
            }
        }

        $logs = $this->db->table('assignment_logs al')
            ->select('al.*, uc.name as changed_by_name, uf.name as from_name, ut.name as to_name')
            ->join('users uc', 'uc.id = al.changed_by', 'left')
            ->join('users uf', 'uf.id = al.from_user_id', 'left')
            ->join('users ut', 'ut.id = al.to_user_id', 'left')
            ->where('al.document_id', $documentId)
            ->orderBy('al.created_at', 'DESC')
            ->get()
            ->getResultArray();

        foreach ($logs as $log) {
            $description = $log['notes'] ?? '';
            if ($log['action'] === 'reasignado') {
                $description = 'De ' . ($log['from_name'] ?? 'N/D') . ' a ' . ($log['to_name'] ?? 'N/D') . '. ' . $description;
            }

            $history[] = [
                'date' => $log['created_at'],
                'title' => $this->resolveLogTitle($log['action']),
                'description' => trim(($description ?: 'Sin detalle adicional.') . ' Responsable: ' . ($log['changed_by_name'] ?? 'N/D') . '.'),
            ];
        }

        if (!empty($report['created_at'])) {
            $history[] = [
                'date' => $report['created_at'],
                'title' => 'Documento final cargado',
                'description' => !empty($report['comment']) ? $report['comment'] : 'Se registró el documento final del líder de área.',
            ];
        }

        usort($history, static function ($a, $b) {
            return strtotime($b['date']) <=> strtotime($a['date']);
        });

        return $history;
    }

    private function resolveLogTitle(string $action): string
    {
        return match ($action) {
            'reasignado' => 'Reasignación',
            'creado' => 'Creación de asignación',
            'estado_documento' => 'Cambio de estado del documento',
            default => ucfirst(str_replace('_', ' ', $action)),
        };
    }

    private function statusLabel(?string $status): string
    {
        if ($status === null || $status === '') {
            return 'Sin estado';
        }

        return ucfirst(str_replace('_', ' ', $status));
    }
}
