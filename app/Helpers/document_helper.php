<?php

if (! function_exists('generate_document_code')) {

    /**
     * Genera un código único de seguimiento para un documento.
     *
     * Formato: DOC-DDMM-HHMMSS-{id}
     * Ejemplo: DOC-0705-143022-1
     *
     * Usa el ID autoincremental de MySQL (nunca se reutiliza)
     * junto con fecha y hora para garantizar unicidad absoluta.
     *
     * @param  int    $documentId  ID del documento recién insertado
     * @return string               Código único generado
     */
    function generate_document_code(int $documentId): string
    {
        $datePart = date('dm');
        $timePart = date('His');

        return "DOC-{$datePart}-{$timePart}-{$documentId}";
    }
}