<?php

if (!function_exists('formatear_fecha')) {
    function formatear_fecha($fecha_sql, $tipo = 'completa')
    {
        // Validación básica por si la fecha viene vacía
        if (!$fecha_sql)
            return '-';

        $timestamp = strtotime($fecha_sql);

        // Arrays de meses
        $meses_largos = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];

        $meses_cortos = [
            1 => 'Ene.',
            2 => 'Feb.',
            3 => 'Mar.',
            4 => 'Abr.',
            5 => 'May.',
            6 => 'Jun.',
            7 => 'Jul.',
            8 => 'Ago.',
            9 => 'Sep.',
            10 => 'Oct.',
            11 => 'Nov.',
            12 => 'Dic.'
        ];

        // Partes de la fecha
        $dia = date('j', $timestamp);
        $mes_num = date('n', $timestamp);
        $anio = date('Y', $timestamp);
        $hora = date('g:i a', $timestamp); // Cambia 'a' por 'A' si quieres AM/PM mayúsculas

        switch ($tipo) {
            case 'solo_fecha':
                // Salida: Enero 8 del 2026
                return "{$meses_largos[$mes_num]} $dia del $anio";

            case 'corta':
                // Salida: Ene. 8, 2026 - 10:10 am
                return "{$meses_cortos[$mes_num]} $dia, $anio - $hora";

            case 'completa':
            default:
                // Salida: Enero 8 del 2026 - 10:10 am
                return "{$meses_largos[$mes_num]} $dia del $anio - $hora";
        }
    }
}

if (!function_exists('shortName')) {

    function shortName($fullName)
    {
        $parts = explode(' ', trim($fullName));

        if (count($parts) >= 2) {
            return $parts[0] . ' ' . $parts[count($parts) - 1];
        }

        return $fullName; // por si solo tiene un nombre
    }
}

?>