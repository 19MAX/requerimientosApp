<?php

/**
 * AuditHelper
 *
 * Helper para registrar eventos en la tabla audit_trail.
 * Uso: audit_log('document.created', 'documents', $docId, null, null, ['title' => 'Mi doc']);
 */
if (! function_exists('audit_humanize_status')) {
    function audit_humanize_status(?string $status): string
    {
        if ($status === null || $status === '') {
            return 'sin estado';
        }

        return ucfirst(str_replace('_', ' ', $status));
    }
}

if (! function_exists('audit_log')) {
    /**
     * Registra un evento de auditoría.
     *
     * @param string      $action      Ej: 'document.created', 'assignment.status_changed'
     * @param string      $entityType  Tabla afectada: 'documents', 'assignments', 'activity_reports'
     * @param int         $entityId    ID del registro afectado
     * @param string|null $oldStatus   Estado anterior (solo en cambios de estado)
     * @param string|null $newStatus   Estado nuevo (solo en cambios de estado)
     * @param array|null  $oldValues   Datos anteriores (snapshot)
     * @param array|null  $newValues   Datos nuevos (snapshot)
     * @param string|null $description Descripción legible para la UI
     */
    function audit_log(
        string  $action,
        string  $entityType,
        int     $entityId,
        ?string $oldStatus   = null,
        ?string $newStatus   = null,
        ?array  $oldValues   = null,
        ?array  $newValues   = null,
        ?string $description = null
    ): void {
        $db      = \Config\Database::connect();
        $request = \Config\Services::request();

        $userId = null;
        if (function_exists('auth') && auth()->loggedIn()) {
            $userId = auth()->id();
        } elseif (session()->has('user_id')) {
            $userId = session()->get('user_id');
        }

        $userAgent = $request->getUserAgent();

        $inserted = $db->table('audit_trail')->insert([
            'user_id'     => $userId,
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'old_status'  => $oldStatus,
            'new_status'  => $newStatus,
            'old_values'  => $oldValues  ? json_encode($oldValues,  JSON_UNESCAPED_UNICODE) : null,
            'new_values'  => $newValues  ? json_encode($newValues,  JSON_UNESCAPED_UNICODE) : null,
            'description' => $description,
            'ip_address'  => $request->getIPAddress(),
            'user_agent'  => $userAgent ? $userAgent->getAgentString() : null,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        if (! $inserted) {
            throw new \RuntimeException('No se pudo registrar el evento de auditoría.');
        }
    }
}

if (! function_exists('audit_status_change')) {
    function audit_status_change(
        string $entityType,
        int $entityId,
        ?string $oldStatus,
        ?string $newStatus,
        ?string $description = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        if ($oldStatus === $newStatus) {
            return;
        }

        $action = match ($entityType) {
            'documents' => AuditActions::DOCUMENT_STATUS_CHANGED,
            'assignments' => AuditActions::ASSIGNMENT_STATUS_CHANGED,
            default => "{$entityType}.status_changed",
        };

        audit_log(
            action: $action,
            entityType: $entityType,
            entityId: $entityId,
            oldStatus: $oldStatus,
            newStatus: $newStatus,
            oldValues: $oldValues ?? ['status' => $oldStatus],
            newValues: $newValues ?? ['status' => $newStatus],
            description: $description ?? "Estado cambiado de '" . audit_humanize_status($oldStatus) . "' a '" . audit_humanize_status($newStatus) . "'."
        );

        // Disparar envío de correo cuando cambie el estado de un documento
        if ($entityType === 'documents' && $newStatus !== null) {
            try {
                \App\Services\EmailService::notifyDocumentStatusChange($entityId, $oldStatus, $newStatus);
            } catch (\Throwable $e) {
                log_message('error', '[EmailService] Falló el envío de correo de estado. DocID: ' . $entityId . '. Error: ' . $e->getMessage());
            }
        }
    }
}

/**
 * Acciones predefinidas para consistencia en todo el proyecto.
 * Úsalas como constantes: AuditActions::DOCUMENT_CREATED
 */
class AuditActions
{
    // Documentos
    const DOCUMENT_CREATED        = 'document.created';
    const DOCUMENT_UPDATED        = 'document.updated';
    const DOCUMENT_STATUS_CHANGED = 'document.status_changed';
    const DOCUMENT_DELETED        = 'document.deleted';

    // Asignaciones
    const ASSIGNMENT_CREATED        = 'assignment.created';
    const ASSIGNMENT_STARTED        = 'assignment.started';
    const ASSIGNMENT_STATUS_CHANGED = 'assignment.status_changed';
    const ASSIGNMENT_CANCELLED      = 'assignment.cancelled';

    // Reportes de actividad
    const ACTIVITY_REPORTED   = 'activity.reported';
    const ACTIVITY_COMPLETED  = 'activity.completed';

    // Usuarios
    const USER_CREATED   = 'user.created';
    const USER_UPDATED   = 'user.updated';
    const USER_LOGIN     = 'user.login';
    const USER_LOGOUT    = 'user.logout';
    const USER_ACTIVATED = 'user.activated';
    const USER_DEACTIVATED = 'user.deactivated';
}

/**
 * Ejemplo de uso en un Controller o Model:
 *
 * // Al crear un documento:
 * audit_log(
 *     action:      AuditActions::DOCUMENT_CREATED,
 *     entityType:  'documents',
 *     entityId:    $documentId,
 *     newValues:   ['title' => $data['title'], 'status' => 'pendiente'],
 *     description: "Secretaria '{$userName}' creó el documento '{$data['title']}'"
 * );
 *
 * // Al cambiar estado:
 * audit_log(
 *     action:      AuditActions::DOCUMENT_STATUS_CHANGED,
 *     entityType:  'documents',
 *     entityId:    $documentId,
 *     oldStatus:   'pendiente',
 *     newStatus:   'aprobado',
 *     description: "Director aprobó el documento #{$documentId}"
 * );
 */