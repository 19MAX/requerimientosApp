<?php

if (!function_exists('statusBadge')) {
    function statusBadge($status)
    {
        $classes = [
            'pendiente' => 'bg-warning-subtle text-warning-emphasis',
            'en_revision' => 'bg-info-subtle text-info-emphasis',
            'aprobado' => 'bg-success-subtle text-success-emphasis',
            'rechazado' => 'bg-danger-subtle text-danger-emphasis',
            'asignado' => 'bg-primary-subtle text-primary-emphasis',
            'completado' => 'bg-success text-white',
        ];

        $badgeClass = $classes[$status] ?? 'bg-secondary-subtle text-secondary-emphasis';
        $label = ucfirst(str_replace('_', ' ', $status));

        return "<span class=\"badge {$badgeClass}\">{$label}</span>";
    }
}