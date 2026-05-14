<?php

namespace App\Services;

use Config\Services;

class EmailService
{
    /**
     * Envía un correo electrónico notificando el registro exitoso de un documento.
     *
     * @param int $documentId ID del documento
     * @return bool
     */
    public static function notifyDocumentRegistration(int $documentId): bool
    {
        $db = \Config\Database::connect();
        
        $document = $db->table('documents')->where('id', $documentId)->get()->getRowArray();
        if (!$document) {
            return false;
        }

        $client = $db->table('clients')->where('id', $document['client_id'])->get()->getRowArray();
        
        if (!$client || empty($client['email'])) {
            return false;
        }

        $email = Services::email();
        $email->setMailType('html');
        $email->setFrom('no-reply@cnel.gob.ec', 'Requerimientos CNEL');
        $email->setTo($client['email']);
        
        $docCode = $document['document_code'] ?? "#{$documentId}";
        $subject = "Confirmación de ingreso: Su requerimiento {$docCode} ha sido registrado";
        $email->setSubject($subject);

        $message = view('emails/document_registered', [
            'document'  => $document,
            'client'    => $client
        ]);

        $email->setMessage($message);
        
        return $email->send();
    }

    /**
     * Envía un correo electrónico notificando el cambio de estado de un documento.
     *
     * @param int $documentId ID del documento
     * @param string|null $oldStatus Estado anterior
     * @param string $newStatus Nuevo estado
     * @return bool
     */
    public static function notifyDocumentStatusChange(int $documentId, ?string $oldStatus, string $newStatus): bool
    {
        $db = \Config\Database::connect();
        
        $document = $db->table('documents')->where('id', $documentId)->get()->getRowArray();
        if (!$document) {
            return false;
        }

        $client = $db->table('clients')->where('id', $document['client_id'])->get()->getRowArray();
        
        if (!$client || empty($client['email'])) {
            // No podemos notificar si no hay cliente o el cliente no tiene correo válido
            return false;
        }

        $email = Services::email();

        // Aseguramos que el correo se envíe en formato HTML
        $email->setMailType('html');

        // En un entorno de desarrollo sin SMTP configurado, podría fallar el envío.
        // Si necesitas configurar temporalmente en código, puedes hacerlo obteniendo la instancia
        // y asignando valores, pero es mejor usar el archivo .env

        $email->setFrom('no-reply@cnel.gob.ec', 'Requerimientos CNEL');
        $email->setTo($client['email']);
        
        $docCode = $document['document_code'] ?? "#{$documentId}";
        $subject = "Actualización de estado en tu requerimiento: {$docCode}";
        $email->setSubject($subject);

        $message = view('emails/document_status_changed', [
            'document'  => $document,
            'client'    => $client,
            'oldStatus' => static::humanizeStatus($oldStatus),
            'newStatus' => static::humanizeStatus($newStatus),
        ]);

        $email->setMessage($message);
        
        // Retornamos true si se envía correctamente
        $success = $email->send();
        
        if (!$success) {
            log_message('error', 'Error al enviar correo de notificación: ' . $email->printDebugger(['headers']));
        }

        return $success;
    }

    private static function humanizeStatus(?string $status): string
    {
        if (empty($status)) {
            return 'Sin estado';
        }
        return ucfirst(str_replace('_', ' ', $status));
    }

    /**
     * Notifica a un director que se le ha asignado un requerimiento.
     */
    public static function notifyDirectorAssignment(int $documentId, int $directorId): bool
    {
        $db = \Config\Database::connect();

        $document = $db->table('documents')->where('id', $documentId)->get()->getRowArray();
        if (!$document) {
            return false;
        }

        $director = $db->table('users')->where('id', $directorId)->get()->getRowArray();
        if (!$director || empty($director['email'])) {
            return false;
        }

        $client = $db->table('clients')->where('id', $document['client_id'])->get()->getRowArray();
        $createdByUser = $db->table('users')->where('id', $document['created_by'])->get()->getRowArray();

        $email = Services::email();
        $email->setMailType('html');
        $email->setFrom('no-reply@cnel.gob.ec', 'Requerimientos CNEL');
        $email->setTo($director['email']);

        $docCode = $document['document_code'] ?? "#{$documentId}";
        $subject = "Nuevo requerimiento asignado: {$docCode}";
        $email->setSubject($subject);

        $message = view('emails/director_assignment', [
            'document' => $document,
            'director' => $director,
            'client' => $client,
            'createdByUser' => $createdByUser,
        ]);

        $email->setMessage($message);

        $success = $email->send();
        if (!$success) {
            log_message('error', 'Error al enviar correo de asignación a director: ' . $email->printDebugger(['headers']));
        }

        return $success;
    }

    /**
     * Notifica a un líder de área que se le ha asignado un requerimiento.
     */
    public static function notifyLeaderAssignment(int $documentId, int $leaderId): bool
    {
        $db = \Config\Database::connect();

        $document = $db->table('documents')->where('id', $documentId)->get()->getRowArray();
        if (!$document) {
            return false;
        }

        $leader = $db->table('users')->where('id', $leaderId)->get()->getRowArray();
        if (!$leader || empty($leader['email'])) {
            return false;
        }

        $client = $db->table('clients')->where('id', $document['client_id'])->get()->getRowArray();
        $assignment = $db->table('assignments')
            ->where('document_id', $documentId)
            ->where('assigned_to', $leaderId)
            ->orderBy('assigned_at', 'DESC')
            ->get()
            ->getRowArray();

        $email = Services::email();
        $email->setMailType('html');
        $email->setFrom('no-reply@cnel.gob.ec', 'Requerimientos CNEL');
        $email->setTo($leader['email']);

        $docCode = $document['document_code'] ?? "#{$documentId}";
        $subject = "Nuevo requerimiento asignado: {$docCode}";
        $email->setSubject($subject);

        $message = view('emails/leader_assignment', [
            'document' => $document,
            'leader' => $leader,
            'client' => $client,
            'assignment' => $assignment,
        ]);

        $email->setMessage($message);

        $success = $email->send();
        if (!$success) {
            log_message('error', 'Error al enviar correo de asignación a líder: ' . $email->printDebugger(['headers']));
        }

        return $success;
    }
}
