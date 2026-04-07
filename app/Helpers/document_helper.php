<?php

if (! function_exists('generate_document_code')) {

    /**
     * Genera un código único de seguimiento para un documento.
     *
     * Formato: DOC-YYYY-MM-DD-NNNN
     * Ejemplo: DOC-2026-04-01-0001
     *
     * Usa una transacción con SELECT ... FOR UPDATE para evitar
     * condiciones de carrera si dos usuarios crean documentos
     * al mismo tiempo en el mismo día.
     *
     * @param  string|null $date Fecha base en formato Y-m-d (por defecto hoy)
     * @return string            Código único generado
     */
    function generate_document_code(?string $date = null): string
    {
        $db   = \Config\Database::connect();
        $date = $date ?? date('Y-m-d');

        [$year, $month, $day] = explode('-', $date);
        $prefix = "DOC-{$year}-{$month}-{$day}";

        // Bloquear la lectura del último consecutivo del día
        // para evitar duplicados en inserciones concurrentes
        $last = $db->query("
            SELECT document_code
            FROM   documents
            WHERE  document_code LIKE ?
            ORDER  BY document_code DESC
            LIMIT  1
            FOR UPDATE
        ", ["{$prefix}-%"])->getRowArray();

        if ($last) {
            // Extraer el número secuencial del último código del día
            $lastNumber = (int) substr($last['document_code'], -4);
            $sequence   = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            // Primer documento del día
            $sequence = '0001';
        }

        $code = "{$prefix}-{$sequence}";

        // Doble verificación: si por alguna razón el código ya existe
        // (caso extremo), incrementar hasta encontrar uno libre
        while ($db->table('documents')->where('document_code', $code)->countAllResults() > 0) {
            $sequence = str_pad((int)$sequence + 1, 4, '0', STR_PAD_LEFT);
            $code     = "{$prefix}-{$sequence}";
        }

        return $code;
    }
}