<?php

if (! function_exists('generate_document_code')) {

    /**
     * Genera un código único de seguimiento para un documento.
     *
     * Formato: ID-AAAA-MM-DD-{secuencia}
     * Ejemplo: ID-2026-05-16-00001
     *
     * Usa el ID autoincremental de MySQL (nunca se reutiliza)
     * junto con fecha para garantizar unicidad absoluta.
     *
     * @param  int    $documentId  ID del documento recién insertado
     * @return string               Código único generado
     */
    function generate_document_code(int $documentId): string
    {
        $year  = date('Y');
        $month = date('m');
        $day   = date('d');
        $sequence = str_pad($documentId, 5, '0', STR_PAD_LEFT);

        return "{$sequence}-{$year}-{$month}-{$day}";
    }
}