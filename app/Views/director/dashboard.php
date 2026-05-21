<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="custom-container">
    <div class="row mb-6 g-6">
        <div class="col-xl-12 col-lg-12">
            <div class="bg-gradient-mixed p-8 py-10 rounded-3 p-lg-7">
                <h1 class="fs-3">👋 Hola <?= esc(session()->get('name')) ?>,</h1>
                <p class="mb-0">¡Bienvenido al Panel de Dirección de CNEL!</p>
                <p>Gestiona las revisiones de documentos y supervisa el progreso de las asignaciones.</p>
                <div class="d-flex gap-3 flex-wrap mt-3">
                    <a href="<?= base_url('director/review-documents') ?>" class="btn btn-dark">Revisar Documentos</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-xl-3 row-cols-md-3 mb-6 g-6">
        <div class="col">
            <div class="card card-lg">
                <div class="card-body d-flex flex-column gap-8">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape icon-lg rounded-circle bg-warning-subtle text-warning-emphasis">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-file-search">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                <path d="M12 21h-5a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v2" />
                                <path d="M18 18m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                <path d="M20.2 20.2l1.8 1.8" />
                            </svg>
                        </div>
                        <div>Pendientes de Revisión</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center lh-1">
                        <div class="fs-3 fw-bold"><?= esc($stats['pending_review'] ?? 0) ?></div>
                        <div class="text-warning small">Esperando aprobación</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-lg">
                <div class="card-body d-flex flex-column gap-8">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape icon-lg rounded-circle bg-info-subtle text-info-emphasis">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-user-share">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                <path d="M6 21v-2a4 4 0 0 1 4 -4h3" />
                                <path d="M16 22l5 -5" />
                                <path d="M21 21.5v-4.5h-4.5" />
                            </svg>
                        </div>
                        <div>Aprobados / Por Asignar</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center lh-1">
                        <div class="fs-3 fw-bold"><?= esc($stats['approved_not_assigned'] ?? 0) ?></div>
                        <div class="text-info small">Listos para gestión</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-lg">
                <div class="card-body d-flex flex-column gap-8">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape icon-lg rounded-circle bg-primary-subtle text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-clock-play">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 7v5l2 2" />
                                <path d="M17 12a5 5 0 1 0 -5 5" />
                                <path d="M19 19m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                <path d="M17 21l3 -3l-3 -3" />
                            </svg>
                        </div>
                        <div>En Ejecución</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center lh-1">
                        <div class="fs-3 fw-bold"><?= esc($stats['in_execution'] ?? 0) ?></div>
                        <div class="text-primary small">Gestión en curso</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-6 mb-6">
        <div class="col-xl-8 col-lg-7">
            <div class="card card-lg h-100">
                <div class="card-header border-bottom-0">
                    <h5 class="mb-0">Últimos Trámites Registrados</h5>
                </div>
                <div class="table-responsive">
                    <table class="table text-nowrap mb-0 table-centered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>N° Trámite</th>
                                <th>Título</th>
                                <th>Creado Por</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_docs)): ?>
                                <?php foreach ($recent_docs as $doc): ?>
                                    <tr>
                                        <td>#<?= esc($doc['id']) ?></td>
                                        <td><div class="text-truncate" style="max-width: 150px;"><?= esc($doc['title']) ?></div></td>
                                        <td><?= esc($doc['creator_name'] ?? 'N/D') ?></td>
                                        <td><?= date('d/m/Y', strtotime($doc['created_at'])) ?></td>
                                        <td>
                                            <span class="badge bg-<?= getStatusColor($doc['status']) ?>-subtle text-<?= getStatusColor($doc['status']) ?>-emphasis">
                                                <?= ucfirst(str_replace('_', ' ', $doc['status'])) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No hay trámites recientes.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5">
            <div class="card card-lg h-100">
                <div class="card-body">
                    <h5 class="mb-4">Resumen General</h5>
                    <div class="d-flex flex-column gap-4">
                        <div class="d-flex justify-content-between align-items-center p-3 bg-success-subtle rounded text-success-emphasis">
                            <span>Completados</span>
                            <span class="fw-bold"><?= esc($stats['completed'] ?? 0) ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 bg-danger-subtle rounded text-danger-emphasis">
                            <span>Rechazados</span>
                            <span class="fw-bold"><?= esc($stats['rejected'] ?? 0) ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 bg-secondary-subtle rounded text-secondary-emphasis">
                            <span>Total Trámites</span>
                            <span class="fw-bold"><?= esc($stats['total_documents'] ?? 0) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
function getStatusColor($status) {
    return match($status) {
        'pendiente', 'en_revision' => 'warning',
        'aprobado' => 'info',
        'asignado' => 'primary',
        'completado' => 'success',
        'rechazado' => 'danger',
        default => 'secondary'
    };
}
?>
<?= $this->endSection() ?>
