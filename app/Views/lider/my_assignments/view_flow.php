<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="custom-container">
    <div class="row mb-6 align-items-center">
        <div class="col-auto">
            <a href="<?= base_url('lider/my-assignments') ?>" class="btn btn-light btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M15 18l-6 -6l6 -6" />
                </svg>
                Volver
            </a>
        </div>
        <div class="col">
            <h1 class="fs-3 mb-0">Flujo del Documento</h1>
            <p class="mb-0 text-muted">Trámite: <?= esc($assignment['document_code']) ?></p>
        </div>
    </div>

    <div class="row g-6">
        <div class="col-lg-8">
            <div class="card card-lg">
                <div class="card-header">
                    <h5 class="mb-0">Documento Inicial (Subido por Secretaría)</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="icon-shape icon-lg bg-primary-subtle text-primary rounded-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                            </svg>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1"><?= esc($assignment['document_title']) ?></h5>
                            <p class="text-muted mb-2"><?= nl2br(esc($assignment['document_description'])) ?></p>
                            <a href="<?= base_url($assignment['initial_file_path']) ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M21 15v4a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-4" />
                                    <polyline points="7 10 12 15 17 10" />
                                    <line x1="12" y1="15" x2="12" y2="3" />
                                </svg>
                                Descargar Documento Inicial
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($activityReport)): ?>
            <div class="card card-lg mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Documento Final (Subido por Líder)</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="icon-shape icon-lg bg-success-subtle text-success rounded-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 12h6" />
                                <path d="M9 16h6" />
                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                            </svg>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1">Evidencia de Trabajo</h5>
                            <p class="text-muted mb-2"><?= nl2br(esc($activityReport['comment'])) ?></p>
                            <?php if (!empty($activityReport['file_path'])): ?>
                            <a href="<?= base_url($activityReport['file_path']) ?>" class="btn btn-outline-success btn-sm" target="_blank">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M21 15v4a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-4" />
                                    <polyline points="7 10 12 15 17 10" />
                                    <line x1="12" y1="15" x2="12" y2="3" />
                                </svg>
                                Descargar Documento Final
                            </a>
                            <?php else: ?>
                            <span class="badge bg-secondary-subtle text-secondary-emphasis">Sin archivo adjunto</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="card card-lg mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Historial de Cambios</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Descripción</th>
                                    <th>Estado Anterior</th>
                                    <th>Estado Nuevo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($auditLogs)): ?>
                                    <?php foreach ($auditLogs as $log): ?>
                                    <tr>
                                        <td class="text-nowrap"><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></td>
                                        <td><?= esc($log['description'] ?? '-') ?></td>
                                        <td><span class="badge bg-secondary-subtle text-secondary-emphasis"><?= esc($log['old_status'] ?? '-') ?></span></td>
                                        <td><span class="badge bg-info-subtle text-info-emphasis"><?= esc($log['new_status'] ?? '-') ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No hay registros de cambios</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-lg">
                <div class="card-header">
                    <h5 class="mb-0">Detalles de la Asignación</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted mb-1">Asignado Por</label>
                        <p class="fw-semibold mb-0"><?= esc($assignment['director_name']) ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted mb-1">Fecha Límite</label>
                        <p class="fw-semibold mb-0">
                            <?= $assignment['due_date'] ? date('d/m/Y', strtotime($assignment['due_date'])) : 'Sin fecha' ?>
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted mb-1">Estado del Documento</label>
                        <p class="mb-0">
                            <?php
                            $statusBadge = 'bg-secondary-subtle text-secondary-emphasis';
                            switch ($assignment['document_status']) {
                                case 'pendiente': $statusBadge = 'bg-warning-subtle text-warning-emphasis'; break;
                                case 'en_revision': $statusBadge = 'bg-info-subtle text-info-emphasis'; break;
                                case 'aprobado': $statusBadge = 'bg-success-subtle text-success-emphasis'; break;
                                case 'asignado': $statusBadge = 'bg-primary-subtle text-primary-emphasis'; break;
                                case 'trabajando': $statusBadge = 'bg-warning-subtle text-warning-emphasis'; break;
                                case 'completado': $statusBadge = 'bg-success text-white'; break;
                                case 'rechazado': $statusBadge = 'bg-danger text-white'; break;
                            }
                            ?>
                            <span class="badge <?= $statusBadge ?>"><?= ucfirst(str_replace('_', ' ', $assignment['document_status'])) ?></span>
                        </p>
                    </div>
                    <hr>
                    <div class="mb-0">
                        <label class="form-label text-muted mb-1">Instrucciones</label>
                        <p class="mb-0" style="white-space: pre-wrap;"><?= esc($assignment['instructions']) ?></p>
                    </div>
                </div>
            </div>

            <?php if (!empty($activityReport)): ?>
            <div class="card card-lg mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Información del Reporte</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted mb-1">Fecha de Entrega</label>
                        <p class="fw-semibold mb-0"><?= date('d/m/Y H:i', strtotime($activityReport['created_at'])) ?></p>
                    </div>
                    <?php if (!empty($activityReport['file_name'])): ?>
                    <div class="mb-3">
                        <label class="form-label text-muted mb-1">Archivo Adjunto</label>
                        <p class="mb-0 text-truncate" title="<?= esc($activityReport['file_name']) ?>"><?= esc($activityReport['file_name']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>