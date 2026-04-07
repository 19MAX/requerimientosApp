<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="custom-container">
    <div class="row mb-6 g-6">
        <div class="col-xl-12 col-lg-12">
            <div class="bg-gradient-mixed p-8 py-10 rounded-3 p-lg-7">
                <h1 class="fs-3">👋 Hola <?= esc(session()->get('name')) ?>,</h1>
                <p class="mb-0">¡Bienvenido al Panel de Secretaría de CNEL!</p>
                <p>Registra nuevos trámites, gestiona clientes y realiza el seguimiento de tus solicitudes.</p>
                <div class="mt-3">
                    <a href="<?= base_url('secretaria/documents') ?>" class="btn btn-dark">Registrar Trámite</a>
                    <a href="<?= base_url('secretaria/clients') ?>" class="btn btn-outline-dark ms-2">Gestionar Clientes</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-xl-3 row-cols-md-3 mb-6 g-6">
        <div class="col">
            <div class="card card-lg">
                <div class="card-body d-flex flex-column gap-8">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape icon-lg rounded-circle bg-primary-subtle text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-files">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M15 3v4a1 1 0 0 0 1 1h4" />
                                <path d="M18 17h-7a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2h4l5 5v7a2 2 0 0 1 -2 2z" />
                                <path d="M16 17v2a2 2 0 0 1 -2 2h-7a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2h2" />
                            </svg>
                        </div>
                        <div>Mis Trámites Totales</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center lh-1">
                        <div class="fs-3 fw-bold"><?= esc($stats['total_my_documents'] ?? 0) ?></div>
                        <div class="text-primary small">Ingresados por ti</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-lg">
                <div class="card-body d-flex flex-column gap-8">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape icon-lg rounded-circle bg-warning-subtle text-warning-emphasis">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-clock">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                <path d="M12 7v5l3 3" />
                            </svg>
                        </div>
                        <div>En Revisión / Pendiente</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center lh-1">
                        <div class="fs-3 fw-bold"><?= esc($stats['my_pending'] ?? 0) ?></div>
                        <div class="text-warning small">Esperando dirección</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-lg">
                <div class="card-body d-flex flex-column gap-8">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape icon-lg rounded-circle bg-success-subtle text-success-emphasis">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-check">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M5 12l5 5l10 -10" />
                            </svg>
                        </div>
                        <div>Mis Trámites Finalizados</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center lh-1">
                        <div class="fs-3 fw-bold"><?= esc($stats['my_completed'] ?? 0) ?></div>
                        <div class="text-success small">Gestión completada</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-6 mb-6">
        <div class="col-xl-8 col-lg-7">
            <div class="card card-lg h-100">
                <div class="card-header border-bottom-0">
                    <h5 class="mb-0">Mis Últimos Registros</h5>
                </div>
                <div class="table-responsive">
                    <table class="table text-nowrap mb-0 table-centered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>N° Trámite</th>
                                <th>Título</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_docs)): ?>
                                <?php foreach ($recent_docs as $doc): ?>
                                    <tr>
                                        <td>#<?= esc($doc['id']) ?></td>
                                        <td><div class="text-truncate" style="max-width: 200px;"><?= esc($doc['title']) ?></div></td>
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
                                    <td colspan="4" class="text-center">Aún no has registrado trámites.</td>
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
                    <h5 class="mb-4">Resumen Adicional</h5>
                    <div class="d-flex flex-column gap-4">
                        <div class="d-flex justify-content-between align-items-center p-3 bg-danger-subtle rounded text-danger-emphasis">
                            <span>Mis Trámites Rechazados</span>
                            <span class="fw-bold"><?= esc($stats['my_rejected'] ?? 0) ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 bg-info-subtle rounded text-info-emphasis">
                            <span>Mis Trámites Aprobados</span>
                            <span class="fw-bold"><?= esc($stats['my_approved'] ?? 0) ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 bg-secondary-subtle rounded text-secondary-emphasis">
                            <span>Total Clientes</span>
                            <span class="fw-bold"><?= esc($stats['total_clients'] ?? 0) ?></span>
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
