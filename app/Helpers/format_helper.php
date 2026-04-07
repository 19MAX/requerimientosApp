<?php

if (!function_exists('format_title_case')) {
    /**
     * Formatea a: "Nombre Segundo"
     */
    function format_title_case(string $string): string
    {
        return mb_convert_case(mb_strtolower(trim($string), 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    }
}

if (!function_exists('format_sentence_case')) {
    /**
     * Formatea a: "Dato formateado a una mayuscula al inicio"
     */
    function format_sentence_case(string $string): string
    {
        $string = mb_strtolower(trim($string), 'UTF-8');
        $firstChar = mb_substr($string, 0, 1, 'UTF-8');
        $rest = mb_substr($string, 1, null, 'UTF-8');

        return mb_strtoupper($firstChar, 'UTF-8') . $rest;
    }
}

if (!function_exists('format_upper_case')) {
    /**
     * Formatea a: "DATOS FORMATEADOS A MAYUSCULAS"
     */
    function format_upper_case(string $string): string
    {
        return mb_strtoupper(trim($string), 'UTF-8');
    }
}