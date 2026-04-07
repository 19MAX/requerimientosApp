<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="custom-container">
    <div class="row mb-6 g-6">
        <div class="col-xl-12 col-lg-12">
            <div class="bg-gradient-mixed p-8 py-10 rounded-3 p-lg-7">
                <h1 class="fs-3">👋 Hola <?= esc(session()->get('name')) ?>,</h1>
                <p class="mb-0">¡Bienvenido al Panel de Líder de Área de CNEL!</p>
                <p>Revisa tus tareas asignadas y gestiona el progreso de tus requerimientos.</p>
                <div class="mt-3">
                    <a href="<?= base_url('lider/my-assignments') ?>" class="btn btn-dark">Ver mis asignaciones</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-6 mb-6">
        <div class="col-xl-3 col-md-6">
            <div class="card card-lg h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h4 class="mb-0"><?= esc($stats['total'] ?? 0) ?></h4>
                            <p class="mb-0 text-muted">Total Asignaciones</p>
                        </div>
                        <div class="icon-shape icon-md bg-primary-subtle text-primary rounded-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-list-check">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3.5 5.5l1.5 1.5l2.5 -2.5" />
                                <path d="M3.5 11.5l1.5 1.5l2.5 -2.5" />
                                <path d="M3.5 17.5l1.5 1.5l2.5 -2.5" />
                                <path d="M11 6l9 0" />
                                <path d="M11 12l9 0" />
                                <path d="M11 18l9 0" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-lg h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h4 class="mb-0"><?= esc($stats['pending'] ?? 0) ?></h4>
                            <p class="mb-0 text-muted">Pendientes</p>
                        </div>
                        <div class="icon-shape icon-md bg-warning-subtle text-warning-emphasis rounded-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                <path d="M12 7v5l3 3" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-lg h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h4 class="mb-0"><?= esc($stats['in_progress'] ?? 0) ?></h4>
                            <p class="mb-0 text-muted">En Progreso</p>
                        </div>
                        <div class="icon-shape icon-md bg-info-subtle text-info-emphasis rounded-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-player-play">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M7 4v16l13 -8z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-lg h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h4 class="mb-0"><?= esc($stats['completed'] ?? 0) ?></h4>
                            <p class="mb-0 text-muted">Completadas</p>
                        </div>
                        <div class="icon-shape icon-md bg-success-subtle text-success-emphasis rounded-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-check">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M5 12l5 5l10 -10" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-6 mb-6">
        <div class="col-xl-12 col-lg-12">
            <div class="card card-lg h-100">
                <div class="card-header border-bottom-0">
                    <h5 class="mb-0">Mis Últimas Tareas Asignadas</h5>
                </div>
                <div class="table-responsive">
                    <table class="table text-nowrap mb-0 table-centered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>N° Asignación</th>
                                <th>Documento</th>
                                <th>Asignado Por</th>
                                <th>Fecha Límite</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_tasks)): ?>
                                <?php foreach ($recent_tasks as $task): ?>
                                    <tr>
                                        <td><span class="fw-semibold">#<?= str_pad($task['id'], 5, '0', STR_PAD_LEFT) ?></span></td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 200px;" title="<?= esc($task['document_title']) ?>">
                                                <?= esc($task['document_title']) ?>
                                            </div>
                                        </td>
                                        <td><?= esc($task['director_name'] ?? 'N/D') ?></td>
                                        <td>
                                            <?php if ($task['due_date']): ?>
                                                <?php $isLate = (strtotime($task['due_date']) < time() && $task['status'] !== 'completada'); ?>
                                                <span class="<?= $isLate ? 'text-danger fw-bold' : '' ?>">
                                                    <?= date('d/m/Y', strtotime($task['due_date'])) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted small">Sin fecha</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $badgeClass = 'bg-secondary-subtle text-secondary-emphasis';
                                            switch ($task['status']) {
                                                case 'pendiente':
                                                    $badgeClass = 'bg-warning-subtle text-warning-emphasis';
                                                    break;
                                                case 'en_progreso':
                                                    $badgeClass = 'bg-info-subtle text-info-emphasis';
                                                    break;
                                                case 'completada':
                                                    $badgeClass = 'bg-success text-white';
                                                    break;
                                                case 'cancelada':
                                                    $badgeClass = 'bg-danger-subtle text-danger-emphasis';
                                                    break;
                                            }
                                            ?>
                                            <span class="badge <?= $badgeClass ?>">
                                                <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No hay tareas recientes.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>