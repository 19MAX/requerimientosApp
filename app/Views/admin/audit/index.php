<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<?php
$moduleKey = $moduleKey ?? 'assignment_status';
$moduleTitle = $moduleTitle ?? 'Registro de Auditoría';
$moduleDescription = $moduleDescription ?? 'Historial de acciones realizadas en el sistema.';
$summaryCards = $summaryCards ?? [];
?>
<div class="custom-container">
    <div class="row mb-6 align-items-center">
        <div class="col-xl-8 col-lg-6">
            <h1 class="fs-3 mb-0"><?= esc($moduleTitle) ?></h1>
            <p class="mb-0 text-muted"><?= esc($moduleDescription) ?></p>
        </div>
        <div class="col-xl-4 col-lg-6 mt-3 mt-lg-0">
            <div class="d-flex justify-content-lg-end gap-2 mb-2">
                <a href="<?= base_url('admin/audit/documents') ?>"
                    class="btn btn-sm <?= $moduleKey === 'documents' ? 'btn-dark' : 'btn-white' ?>">
                    Documentos
                </a>
                <a href="<?= base_url('admin/audit/assignments') ?>"
                    class="btn btn-sm <?= $moduleKey === 'assignment_status' ? 'btn-dark' : 'btn-white' ?>">
                    Asignaciones
                </a>
            </div>
            <span class="badge bg-dark-subtle fs-6 px-3 py-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 6l0 6l3 3" />
                    <circle cx="12" cy="12" r="9" />
                </svg>
                <?= number_format(count($auditLogs ?? [])) ?> registros
            </span>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-6" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Tarjetas de resumen rápido -->
    <?php if (!empty($summaryCards)): ?>
        <div class="row g-4 mb-6">
            <?php foreach ($summaryCards as $card): ?>
                <?php $icon = $card['icon'] ?? 'status'; ?>
                <div class="col-md-3">
                    <div class="card card-sm border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="icon-shape icon-lg rounded-3 <?= esc($card['tone'] ?? 'bg-secondary-subtle text-secondary') ?>">
                                <?php if ($icon === 'file'): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                    </svg>
                                <?php elseif ($icon === 'check'): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M5 12l5 5l10 -10" />
                                    </svg>
                                <?php elseif ($icon === 'users'): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <circle cx="12" cy="7" r="4" />
                                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                    </svg>
                                <?php else: ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 9v4" />
                                        <path
                                            d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" />
                                        <path d="M12 16h.01" />
                                    </svg>
                                <?php endif; ?>
                            </div>
                            <div>
                                <p class="mb-0 text-muted small"><?= esc($card['label'] ?? '') ?></p>
                                <h4 class="mb-0"><?= number_format((int) ($card['value'] ?? 0)) ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Tabla principal -->
    <div class="row g-6 mb-6">
        <div class="col-12">
            <div class="card card-lg">
                <div class="table-responsive py-3">
                    <table class="table text-nowrap mb-0 table-centered table-hover" id="auditTable">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha y Hora</th>
                                <th>Usuario</th>
                                <th>Acción</th>
                                <th>Entidad</th>
                                <th>Cambio de Estado</th>
                                <th>IP</th>
                                <th>Detalle</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($auditLogs)): ?>
                                <?php foreach ($auditLogs as $log): ?>
                                    <tr>
                                        <td>
                                            <span class="fw-medium"><?= date('d/m/Y', strtotime($log['created_at'])) ?></span>
                                            <br>
                                            <small
                                                class="text-muted"><?= date('H:i:s', strtotime($log['created_at'])) ?></small>
                                        </td>
                                        <td>
                                            <?php if (!empty($log['user_name'])): ?>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="icon-shape icon-sm rounded-circle bg-primary-subtle text-primary"
                                                        style="font-size: 0.7rem;">
                                                        <?= strtoupper(substr($log['user_name'], 0, 2)) ?>
                                                    </div>
                                                    <div>
                                                        <span class="fw-medium d-block"
                                                            style="max-width:130px;overflow:hidden;text-overflow:ellipsis;">
                                                            <?= esc($log['user_name']) ?>
                                                        </span>
                                                        <small class="text-muted"><?= esc($log['role_name'] ?? '') ?></small>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted fst-italic">Sistema</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            // Badge de color según la acción
                                            $actionBadge = match (true) {
                                                str_contains($log['action'], 'created') => ['bg-success-subtle text-success', 'Creación'],
                                                str_contains($log['action'], 'updated') => ['bg-warning-subtle text-warning', 'Actualización'],
                                                str_contains($log['action'], 'deleted') => ['bg-danger-subtle text-danger', 'Eliminación'],
                                                str_contains($log['action'], 'status') => ['bg-info-subtle text-info', 'Cambio estado'],
                                                str_contains($log['action'], 'login') => ['bg-primary-subtle text-primary', 'Login'],
                                                str_contains($log['action'], 'completed') => ['bg-success-subtle text-success', 'Completado'],
                                                str_contains($log['action'], 'assigned') => ['bg-purple-subtle text-purple', 'Asignación'],
                                                default => ['bg-secondary-subtle text-secondary', 'Evento'],
                                            };
                                            ?>
                                            <span class="badge <?= $actionBadge[0] ?>"><?= $actionBadge[1] ?></span>
                                            <br>
                                            <small class="text-muted"><?= esc($log['action']) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-dark-subtle"><?= esc($log['entity_type']) ?></span>
                                            <small class="text-muted d-block"># <?= $log['entity_id'] ?></small>
                                        </td>
                                        <td>
                                            <?php if ($log['old_status'] || $log['new_status']): ?>
                                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                                    <?php if ($log['old_status']): ?>
                                                        <span
                                                            class="badge bg-secondary-subtle text-secondary"><?= esc($log['old_status']) ?></span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M5 12h14M12 5l7 7-7 7" />
                                                        </svg>
                                                    <?php endif; ?>
                                                    <?php if ($log['new_status']): ?>
                                                        <span
                                                            class="badge bg-primary-subtle text-primary"><?= esc($log['new_status']) ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small
                                                class="text-muted font-monospace"><?= esc($log['ip_address'] ?? '—') ?></small>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-white btn-sm"
                                                onclick="openAuditDetail(<?= htmlspecialchars(json_encode($log), ENT_QUOTES) ?>)"
                                                title="Ver detalle completo">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <circle cx="12" cy="12" r="9" />
                                                    <path d="M12 8l.01 0" />
                                                    <path d="M11 12l1 0l0 4l1 0" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No hay registros para este módulo.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de detalle de auditoría -->
<div class="modal fade" id="auditDetailModal" tabindex="-1" aria-labelledby="auditDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0" id="auditDetailModalLabel">Detalle del Evento</h5>
                    <small class="text-muted" id="audit-detail-id"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <!-- Descripción principal -->
                <div class="alert alert-light border mb-4 d-flex gap-3 align-items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="text-primary mt-1 flex-shrink-0">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <circle cx="12" cy="12" r="9" />
                        <path d="M12 8l.01 0" />
                        <path d="M11 12l1 0l0 4l1 0" />
                    </svg>
                    <p class="mb-0" id="audit-detail-description">—</p>
                </div>

                <div class="row g-3 mb-4">
                    <!-- Columna izquierda -->
                    <div class="col-md-6">
                        <div class="card card-sm h-100 bg-light border-0">
                            <div class="card-body">
                                <h6 class="text-muted text-uppercase small fw-semibold mb-3">Información del evento</h6>
                                <dl class="row mb-0 small">
                                    <dt class="col-5 text-muted fw-normal">Evento</dt>
                                    <dd class="col-7 mb-2"><span id="audit-detail-action"
                                            class="badge bg-secondary-subtle text-secondary"></span></dd>

                                    <dt class="col-5 text-muted fw-normal">Entidad</dt>
                                    <dd class="col-7 mb-2"><span id="audit-detail-entity-type"
                                            class="badge bg-dark-subtle"></span></dd>

                                    <dt class="col-5 text-muted fw-normal">ID Registro</dt>
                                    <dd class="col-7 mb-2" id="audit-detail-entity-id">—</dd>

                                    <dt class="col-5 text-muted fw-normal">Fecha</dt>
                                    <dd class="col-7 mb-0" id="audit-detail-date">—</dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <!-- Columna derecha -->
                    <div class="col-md-6">
                        <div class="card card-sm h-100 bg-light border-0">
                            <div class="card-body">
                                <h6 class="text-muted text-uppercase small fw-semibold mb-3">Información del usuario
                                </h6>
                                <dl class="row mb-0 small">
                                    <dt class="col-5 text-muted fw-normal">Usuario</dt>
                                    <dd class="col-7 mb-2" id="audit-detail-user">—</dd>

                                    <dt class="col-5 text-muted fw-normal">Rol</dt>
                                    <dd class="col-7 mb-2" id="audit-detail-role">—</dd>

                                    <dt class="col-5 text-muted fw-normal">IP</dt>
                                    <dd class="col-7 mb-2"><code id="audit-detail-ip">—</code></dd>

                                    <dt class="col-5 text-muted fw-normal">User Agent</dt>
                                    <dd class="col-7 mb-0 text-truncate" id="audit-detail-ua" style="max-width:180px;"
                                        title=""></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cambio de estado -->
                <div id="audit-status-block" class="mb-4 d-none">
                    <h6 class="text-muted text-uppercase small fw-semibold mb-2">Cambio de estado</h6>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-secondary-subtle text-secondary fs-6 px-3 py-2"
                            id="audit-old-status"></span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14M12 5l7 7-7 7" />
                        </svg>
                        <span class="badge bg-primary-subtle text-primary fs-6 px-3 py-2" id="audit-new-status"></span>
                    </div>
                </div>

                <!-- Datos anteriores / nuevos -->
                <div class="row g-3" id="audit-values-block">
                    <div class="col-md-6" id="old-values-col">
                        <h6 class="text-muted text-uppercase small fw-semibold mb-2">Valores anteriores</h6>
                        <pre class="bg-danger-subtle text-danger rounded-3 p-3 small mb-0" id="audit-old-values"
                            style="white-space: pre-wrap; word-break: break-word;"></pre>
                    </div>
                    <div class="col-md-6" id="new-values-col">
                        <h6 class="text-muted text-uppercase small fw-semibold mb-2">Valores nuevos</h6>
                        <pre class="bg-success-subtle text-success rounded-3 p-3 small mb-0" id="audit-new-values"
                            style="white-space: pre-wrap; word-break: break-word;"></pre>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // DataTable
    new DataTable('#auditTable', {
        language: { url: 'https://cdn.datatables.net/plug-ins/2.3.7/i18n/es-ES.json' },
        scrollX: true,
        layout: {
            topStart: {
                buttons: ['pageLength','copy', 'excel', 'pdf']
            },
        }
    });

    // Abre el modal de detalle con la info del log
    function openAuditDetail(log) {
        // Info básica
        document.getElementById('audit-detail-id').textContent = 'Registro #' + log.id;
        document.getElementById('audit-detail-description').textContent = log.description || 'Sin descripción registrada.';
        document.getElementById('audit-detail-action').textContent = log.action;
        document.getElementById('audit-detail-entity-type').textContent = log.entity_type;
        document.getElementById('audit-detail-entity-id').textContent = '#' + log.entity_id;
        document.getElementById('audit-detail-date').textContent = formatDate(log.created_at);
        document.getElementById('audit-detail-user').textContent = log.user_name || 'Sistema';
        document.getElementById('audit-detail-role').textContent = log.role_name || '—';
        document.getElementById('audit-detail-ip').textContent = log.ip_address || '—';

        const uaEl = document.getElementById('audit-detail-ua');
        uaEl.textContent = log.user_agent || '—';
        uaEl.title = log.user_agent || '';

        // Cambio de estado
        const statusBlock = document.getElementById('audit-status-block');
        if (log.old_status || log.new_status) {
            statusBlock.classList.remove('d-none');
            document.getElementById('audit-old-status').textContent = log.old_status || '—';
            document.getElementById('audit-new-status').textContent = log.new_status || '—';
        } else {
            statusBlock.classList.add('d-none');
        }

        // Valores anteriores / nuevos
        const valuesBlock = document.getElementById('audit-values-block');
        const oldValuesEl = document.getElementById('audit-old-values');
        const newValuesEl = document.getElementById('audit-new-values');
        const oldValuesCol = document.getElementById('old-values-col');
        const newValuesCol = document.getElementById('new-values-col');

        const hasOld = log.old_values && log.old_values !== 'null';
        const hasNew = log.new_values && log.new_values !== 'null';

        if (hasOld || hasNew) {
            valuesBlock.classList.remove('d-none');

            if (hasOld) {
                oldValuesCol.classList.remove('d-none');
                oldValuesEl.textContent = formatJSON(log.old_values);
            } else {
                oldValuesCol.classList.add('d-none');
            }

            if (hasNew) {
                newValuesCol.classList.remove('d-none');
                newValuesEl.textContent = formatJSON(log.new_values);
            } else {
                newValuesCol.classList.add('d-none');
            }
        } else {
            valuesBlock.classList.add('d-none');
        }

        new bootstrap.Modal(document.getElementById('auditDetailModal')).show();
    }

    function formatJSON(value) {
        try {
            const parsed = typeof value === 'string' ? JSON.parse(value) : value;
            return JSON.stringify(parsed, null, 2);
        } catch {
            return value ?? '—';
        }
    }

    function formatDate(dateStr) {
        if (!dateStr) return '—';
        const d = new Date(dateStr);
        return d.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' })
            + ' ' + d.toLocaleTimeString('es-ES');
    }
</script>
<?= $this->endSection() ?>