<?php

if (! function_exists('generate_document_code')) {

    /**
     * Genera un código único de seguimiento para un documento.
     *
     * Formato: DOC-NNNNNN
     * Ejemplo: DOC-000001
     *
     * Usa una transacción con SELECT ... FOR UPDATE para evitar
     * condiciones de carrera si dos usuarios crean documentos
     * al mismo tiempo.
     *
     * El secuencial NUNCA se reinicia, es global y autoincremental.
     *
     * @return string            Código único generado
     */
    function generate_document_code(): string
    {
        $db   = \Config\Database::connect();

        // Bloquear la lectura del último código para evitar duplicados
        // en inserciones concurrentes
        $last = $db->query("
            SELECT document_code
            FROM   documents
            WHERE  document_code LIKE 'DOC-%'
            ORDER  BY document_code DESC
            LIMIT  1
            FOR UPDATE
        ")->getRowArray();

        if ($last) {
            // Extraer el número secuencial del último código
            $lastNumber = (int) substr($last['document_code'], 4);
            $sequence   = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        } else {
            // Primer documento
            $sequence = '000001';
        }

        $code = "DOC-{$sequence}";

        // Doble verificación: si por alguna razón el código ya existe
        // (caso extremo), incrementar hasta encontrar uno libre
        while ($db->table('documents')->where('document_code', $code)->countAllResults() > 0) {
            $sequence = str_pad((int)$sequence + 1, 6, '0', STR_PAD_LEFT);
            $code     = "DOC-{$sequence}";
        }

        return $code;
    }
}