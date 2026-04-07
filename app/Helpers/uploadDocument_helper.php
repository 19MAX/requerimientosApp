<?php

if (!function_exists('uploadDocument')) {
    // Añadimos $folder con valor por defecto 'documents' para no romper tu código anterior
    function uploadDocument($file, $userId, $folder = 'documents')
    {
        $path       = 'uploads/' . $folder . '/' . $userId . '/';
        $uploadPath = ROOTPATH . 'public/' . $path;

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        try {
            if ($file->isValid() && !$file->hasMoved()) {

                // ✅ Capturar TODOS los datos ANTES de mover el archivo
                $clientName = $file->getClientName();
                $fileSize   = $file->getSize();
                $fileMime   = $file->getMimeType(); // finfo lee el archivo en /tmp
                $randomName = $file->getRandomName();

                // Mover DESPUÉS de capturar los metadatos
                $file->move($uploadPath, $randomName);

                return [
                    'file_path' => $path . $randomName,
                    'file_name' => $clientName,
                    'file_size' => $fileSize,
                    'file_mime' => $fileMime,
                ];
            }

            return false;
        } catch (\Exception $e) {
            log_message('error', '[uploadDocument] Error al subir documento: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('deleteDocument')) {
    // Este se queda igual, ya funciona perfecto
    function deleteDocument(string $filePath): bool
    {
        if (empty($filePath)) {
            return false;
        }

        $fullPath = ROOTPATH . 'public/' . $filePath;

        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }

        log_message('warning', '[deleteDocument] Archivo no encontrado en disco: ' . $fullPath);
        return false;
    }
}