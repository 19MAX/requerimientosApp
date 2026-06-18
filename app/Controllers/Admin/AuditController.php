<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class AuditController extends BaseController
{
    protected $db;
    private const MODULE_DOCUMENTS = 'documents';
    private const MODULE_ASSIGNMENT_STATUS = 'assignment_status';

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        return $this->assignmentStatus();
    }

    public function assignmentStatus()
    {
        return $this->renderModule(self::MODULE_ASSIGNMENT_STATUS);
    }

    public function documentChanges()
    {
        return $this->renderModule(self::MODULE_DOCUMENTS);
    }

    private function renderModule(string $module)
    {
        $moduleConfig = $this->getModuleConfig($module);

        return view('admin/audit/index', [
            'moduleKey' => $module,
            'moduleTitle' => $moduleConfig['title'],
            'moduleDescription' => $moduleConfig['description'],
            'summaryCards' => $moduleConfig['summaryCards'],
            'auditLogs' => $this->getAuditLogs($module),
        ]);
    }

    private function getAuditLogs(string $module): array
    {
        $builder = $this->db->table('audit_trail at')
            ->select('
                at.*,
                CONCAT(u.name) AS user_name,
                r.name AS role_name
            ')
            ->join('users u', 'u.id = at.user_id', 'left')
            ->join('roles r', 'r.id = u.role_id', 'left');

        $this->applyModuleFilter($builder, $module);

        return $builder
            ->orderBy('at.id', 'DESC')
            ->get()
            ->getResultArray();
    }

    private function getModuleConfig(string $module): array
    {
        if ($module === self::MODULE_DOCUMENTS) {
            return [
                'title' => 'Auditoría de Cambios de Documentos',
                'description' => 'Historial de cambios realizados únicamente sobre requerimientos/documentos.',
                'summaryCards' => [
                    [
                        'label' => 'Cambios de documentos',
                        'value' => $this->countLogs(self::MODULE_DOCUMENTS),
                        'tone' => 'bg-primary-subtle text-primary',
                        'icon' => 'file',
                    ],
                    [
                        'label' => 'Estados cambiados',
                        'value' => $this->countLogs(self::MODULE_DOCUMENTS, 'document.status_changed'),
                        'tone' => 'bg-warning-subtle text-warning',
                        'icon' => 'status',
                    ],
                    [
                        'label' => 'Documentos creados',
                        'value' => $this->countLogs(self::MODULE_DOCUMENTS, 'document.created'),
                        'tone' => 'bg-success-subtle text-success',
                        'icon' => 'check',
                    ],
                    [
                        'label' => 'Documentos completados',
                        'value' => $this->countLogs(self::MODULE_DOCUMENTS, 'document.status_changed', 'completado'),
                        'tone' => 'bg-info-subtle text-info',
                        'icon' => 'users',
                    ],
                ],
            ];
        }

        return [
            'title' => 'Auditoría de Estado de Asignaciones',
            'description' => 'Historial de cambios de estado de asignaciones en el sistema.',
            'summaryCards' => [
                [
                    'label' => 'Cambios de estado',
                    'value' => $this->countLogs(self::MODULE_ASSIGNMENT_STATUS),
                    'tone' => 'bg-primary-subtle text-primary',
                    'icon' => 'status',
                ],
                [
                    'label' => 'Asignaciones a pendiente',
                    'value' => $this->countLogs(self::MODULE_ASSIGNMENT_STATUS, null, 'pendiente'),
                    'tone' => 'bg-warning-subtle text-warning',
                    'icon' => 'status',
                ],
                [
                    'label' => 'Asignaciones en progreso',
                    'value' => $this->countLogs(self::MODULE_ASSIGNMENT_STATUS, null, 'en_progreso'),
                    'tone' => 'bg-info-subtle text-info',
                    'icon' => 'status',
                ],
                [
                    'label' => 'Asignaciones completadas',
                    'value' => $this->countLogs(self::MODULE_ASSIGNMENT_STATUS, null, 'completada'),
                    'tone' => 'bg-success-subtle text-success',
                    'icon' => 'check',
                ],
            ],
        ];
    }

    private function countLogs(string $module, ?string $action = null, ?string $newStatus = null): int
    {
        $builder = $this->db->table('audit_trail at');
        $this->applyModuleFilter($builder, $module);

        if ($action !== null) {
            $builder->where('at.action', $action);
        }

        if ($newStatus !== null) {
            $builder->where('at.new_status', $newStatus);
        }

        return $builder->countAllResults();
    }

    private function applyModuleFilter($builder, string $module): void
    {
        if ($module === self::MODULE_DOCUMENTS) {
            $builder->where('at.entity_type', 'documents');

            return;
        }

        $builder->where('at.entity_type', 'assignments')
            ->where('at.action', 'assignment.status_changed');
    }

    public function clientAuditLog(int $clientId)
    {
        $request = service('request');
        $draw = $request->getGet('draw') ?? 1;
        $start = $request->getGet('start') ?? 0;
        $length = $request->getGet('length') ?? 10;
        $searchValue = $request->getGet('search[value]') ?? '';

        $builder = $this->db->table('audit_trail at')
            ->select('
                at.id,
                at.action,
                at.old_values,
                at.new_values,
                at.created_at,
                CONCAT(u.name) AS user_name
            ')
            ->join('users u', 'u.id = at.user_id', 'left')
            ->where('at.entity_type', 'clients')
            ->where('at.entity_id', $clientId);

        // Total records before filtering
        $recordsTotal = $builder->countAllResults(false);

        // Apply search if provided
        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('u.name', $searchValue)
                ->orLike('at.action', $searchValue)
            ->groupEnd();
        }

        // Filtered records
        $recordsFiltered = $builder->countAllResults(false);

        // Get paginated results
        $results = $builder
            ->orderBy('at.created_at', 'DESC')
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        // Build DataTable response
        $data = array_map(function ($row) {
            $fecha = date('d/m/Y H:i', strtotime($row['created_at']));
            $usuario = $row['user_name'] ?? 'Desconocido';
            $accion = $row['action'];
            $cambios = $this->renderCambios($row['old_values'], $row['new_values']);

            return [
                'fecha' => $fecha,
                'usuario' => $usuario,
                'accion' => $accion,
                'cambios' => $cambios,
            ];
        }, $results);

        return $this->response->setJSON([
            'draw' => (int) $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    private function renderCambios(?string $oldValues, ?string $newValues): string
    {
        if ($oldValues === null && $newValues === null) {
            return '—';
        }

        $oldObj = !empty($oldValues) ? json_decode($oldValues, true) : [];
        $newObj = !empty($newValues) ? json_decode($newValues, true) : [];

        if (!is_array($oldObj)) { $oldObj = []; }
        if (!is_array($newObj)) { $newObj = []; }

        $allKeys = array_unique(array_merge(array_keys($oldObj), array_keys($newObj)));
        $changes = [];
        $isCreation = empty($oldObj) && !empty($newObj);

        foreach ($allKeys as $key) {
            $oldVal = $oldObj[$key] ?? '';
            $newVal = $newObj[$key] ?? '';
            if (json_encode($oldVal) !== json_encode($newVal)) {
                if ($isCreation) {
                    $changes[] = "{$key}: — → '{$newVal}'";
                } else {
                    $changes[] = "{$key}: '{$oldVal}' → '{$newVal}'";
                }
            }
        }

        return !empty($changes) ? implode(', ', $changes) : 'Sin cambios';
    }
}
