<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="custom-container">
    <div class="row mb-6 g-6">
        <div class="col-xl-8 col-lg-6">
            <div class="bg-gradient-mixed p-8 py-10 rounded-3 p-lg-7">
                <h1 class="fs-3">👋 Hola <?= esc(session()->get('name')) ?>,</h1>
                <p class="mb-0">¡Bienvenido al Panel de Control General de CNEL!</p>
                <p>Monitorea el flujo de documentos, el rendimiento de las áreas y la actividad del sistema.</p>
                <a href="<?= base_url('admin/users') ?>" class="btn btn-dark mt-2">Gestionar Usuarios</a>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6">
            <div class="card card-lg h-100">
                <div class="card-body">
                    <div class="mb-4 position-relative py-2">
                        <div>
                            <h5 class="mb-1">Resumen del Sistema</h5>
                        </div>
                        <div class="swiper-navigation position-absolute top-50 end-0 me-2">
                            <div class="swiper-button-prev ms-n3 scale-50"></div>
                            <div class="swiper-button-next ms-6 scale-50"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="swiper-container swiper" id="swiper-1" data-pagination-type="" data-speed="900"
                                data-space-between="100" data-pagination="false" data-navigation="true"
                                data-autoplay="true" data-autoplay-delay="3000"
                                data-breakpoints='{"480": {"slidesPerView": 1}, "768": {"slidesPerView": 1}, "1024": {"slidesPerView": 1}, "1200": {"slidesPerView": 1}}'>
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        <div>
                                            <h4>Trámites Atrasados</h4>
                                            <div>
                                                <p class="text-muted">Revisa las asignaciones que han superado su fecha
                                                    límite de entrega.</p>
                                            </div>
                                            <div class="mt-4">
                                                <a href="#!" class="btn btn-white btn-sm">Ver Detalles</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div>
                                            <h4>Pista de Auditoría</h4>
                                            <div>
                                                <p class="text-muted">Supervisa las últimas acciones críticas realizadas
                                                    por los usuarios.</p>
                                            </div>
                                            <div class="mt-4">
                                                <a href="<?= base_url('admin/audit') ?>" class="btn btn-white btn-sm">Ir
                                                    a Auditoría</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-pagination"></div>
                            </div>
                        </div>
                    </div>
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
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-files">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M15 3v4a1 1 0 0 0 1 1h4" />
                                <path d="M18 17h-7a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2h4l5 5v7a2 2 0 0 1 -2 2z" />
                                <path d="M16 17v2a2 2 0 0 1 -2 2h-7a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2h2" />
                            </svg>
                        </div>
                        <div>Total Trámites</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center lh-1">
                        <div class="fs-3 fw-bold"><?= esc($stats['total_documents'] ?? 0) ?></div>
                        <div class="text-success small">
                            <span>Ingresados</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-lg">
                <div class="card-body d-flex flex-column gap-8">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape icon-lg rounded-circle bg-warning-subtle text-warning-emphasis">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-clock-play">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 7v5l2 2" />
                                <path d="M17 22l5 -3l-5 -3z" />
                                <path d="M13.017 20.943a9 9 0 1 1 7.831 -7.292" />
                            </svg>
                        </div>
                        <div>Tareas en Progreso</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center lh-1">
                        <div class="fs-3 fw-bold"><?= esc($stats['active_tasks'] ?? 0) ?></div>
                        <div class="text-warning small">
                            <span>En ejecución actual</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-lg">
                <div class="card-body d-flex flex-column gap-8">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape icon-lg rounded-circle bg-success-subtle text-success-emphasis">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-users-group">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M10 13a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                <path d="M8 21v-1a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v1" />
                                <path d="M15 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                <path d="M17 10h2a2 2 0 0 1 2 2v1" />
                                <path d="M5 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                <path d="M3 13v-1a2 2 0 0 1 2 -2h2" />
                            </svg>
                        </div>
                        <div>Usuarios Activos</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center lh-1">
                        <div class="fs-3 fw-bold"><?= esc($stats['active_users'] ?? 0) ?></div>
                        <div class="text-secondary small">
                            <span>En el sistema</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-6 mb-6">
        <div class="col-xl-8 col-12">
            <div class="card card-lg h-100">
                <div class="card-body d-flex flex-column gap-5">
                    <div class="mb-4">
                        <h5 class="mb-0">Flujo de Trámites</h5>
                    </div>
                    <div class="bg-gray-100 p-3 rounded-3">
                        <ul class="nav nav-pills-white nav-fill" id="chartTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="current-week-tab" data-bs-toggle="pill"
                                    data-bs-target="#current-week" type="button" role="tab" aria-selected="true">
                                    <span class="d-flex flex-column">
                                        <span class="d-flex align-items-center justify-content-center gap-2">
                                            <span><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                    viewBox="0 0 24 24" fill="currentColor" class="text-primary">
                                                    <circle cx="12" cy="12" r="10" />
                                                </svg></span>
                                            <span>Ingresados</span>
                                        </span>
                                        <span
                                            class="text-center fs-3 fw-semibold mt-2"><?= esc($stats['docs_ingresados'] ?? 0) ?></span>
                                    </span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="past-week-tab" data-bs-toggle="pill"
                                    data-bs-target="#past-week" type="button" role="tab" aria-selected="false">
                                    <span class="d-flex flex-column">
                                        <span class="d-flex align-items-center justify-content-center gap-2">
                                            <span><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                    viewBox="0 0 24 24" fill="currentColor" class="text-success">
                                                    <circle cx="12" cy="12" r="10" />
                                                </svg></span>
                                            <span>Completados</span>
                                        </span>
                                        <span
                                            class="text-center fs-3 fw-semibold mt-2"><?= esc($stats['docs_completados'] ?? 0) ?></span>
                                    </span>
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content" id="chartTabsContent">
                        <div class="tab-pane fade show active" id="current-week" role="tabpanel">
                            <div id="totalIncomeChart"></div>
                        </div>
                        <div class="tab-pane fade" id="past-week" role="tabpanel">
                            <div id="totalExpensesChart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-12">
            <div class="card card-lg h-100">
                <div class="card-body">
                    <h5 class="mb-6">Distribución por Estados</h5>
                    <div id="totalSale" class="d-flex justify-content-center mb-4"></div>

                    <table class="table table-sm table-borderless mb-0 mt-2">
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="text-warning"><svg width="12" height="12" viewBox="0 0 24 24"
                                                fill="currentColor">
                                                <circle cx="12" cy="12" r="10" />
                                            </svg></span>
                                        <span class="ms-2">Pendientes</span>
                                    </div>
                                </td>
                                <td class="text-end fw-semibold"><?= esc($stats['docs_pendientes'] ?? 0) ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="text-info"><svg width="12" height="12" viewBox="0 0 24 24"
                                                fill="currentColor">
                                                <circle cx="12" cy="12" r="10" />
                                            </svg></span>
                                        <span class="ms-2">Aprobados</span>
                                    </div>
                                </td>
                                <td class="text-end fw-semibold"><?= esc($stats['docs_aprobados'] ?? 0) ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="text-primary"><svg width="12" height="12" viewBox="0 0 24 24"
                                                fill="currentColor">
                                                <circle cx="12" cy="12" r="10" />
                                            </svg></span>
                                        <span class="ms-2">En Ejecución</span>
                                    </div>
                                </td>
                                <td class="text-end fw-semibold"><?= esc($stats['docs_ejecucion'] ?? 0) ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="text-danger"><svg width="12" height="12" viewBox="0 0 24 24"
                                                fill="currentColor">
                                                <circle cx="12" cy="12" r="10" />
                                            </svg></span>
                                        <span class="ms-2">Rechazados</span>
                                    </div>
                                </td>
                                <td class="text-end fw-semibold"><?= esc($stats['docs_rechazados'] ?? 0) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-6 mb-6">
        <div class="col-xl-8">
            <div class="card card-lg h-100">
                <div class="card-header border-bottom-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Últimos Trámites Registrados</h5>
                    <a href="<?= base_url('secretaria/documents') ?>" class="btn btn-sm btn-light">Ver todos</a>
                </div>
                <div class="table-responsive">
                    <table class="table text-nowrap mb-0 table-centered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>N°</th>
                                <th>Título</th>
                                <th>Creado por</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_docs)): ?>
                                <?php foreach ($recent_docs as $doc): ?>
                                    <tr>
                                        <td>#<?= str_pad($doc['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 200px;"><?= esc($doc['title']) ?></div>
                                        </td>
                                        <td><?= esc($doc['creator_name'] ?? 'N/A') ?></td>
                                        <td><?= date('d/m/Y', strtotime($doc['created_at'])) ?></td>
                                        <td>
                                            <?php
                                            $badge = 'bg-secondary-subtle text-secondary-emphasis';
                                            if ($doc['status'] == 'pendiente')
                                                $badge = 'bg-warning-subtle text-warning-emphasis';
                                            if ($doc['status'] == 'aprobado')
                                                $badge = 'bg-info-subtle text-info-emphasis';
                                            if ($doc['status'] == 'completado')
                                                $badge = 'bg-success-subtle text-success-emphasis';
                                            if ($doc['status'] == 'rechazado')
                                                $badge = 'bg-danger-subtle text-danger-emphasis';
                                            ?>
                                            <span
                                                class="badge <?= $badge ?>"><?= ucfirst(str_replace('_', ' ', $doc['status'])) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-3">No hay trámites recientes</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card card-lg h-100">
                <div class="card-body">
                    <h5 class="mb-4">Rendimiento por Líderes</h5>
                    <p class="text-muted small mb-4">Progreso de tareas completadas vs asignadas.</p>

                    <div class="d-flex flex-column gap-4">
                            <?php if (!empty($leader_stats)): ?>
                                <?php foreach ($leader_stats as $ls): ?>
                                <?php
                                // Calcular porcentaje
                                $percent = $ls['total'] > 0 ? round(($ls['completed'] / $ls['total']) * 100) : 0;
                                // Variar el color de la barra
                                $barColor = 'bg-primary';
                                if ($percent == 100)
                                    $barColor = 'bg-success';
                                elseif ($percent < 50)
                                    $barColor = 'bg-warning';
                                ?>
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-semibold text-dark"><?= esc($ls['name']) ?></span>

                                        <span class="small text-muted"><?= $ls['completed'] ?>/<?= $ls['total'] ?>
                                            (<?= $percent ?>%)</span>
                                    </div>
                                    <div class="progress" style="height: 6px">


                                        <div class="progress-bar <?= $barColor ?>" role="progressbar"
                                            style="width: <?= $percent ?>%" aria-valuenow="<?= $percent ?>" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <p class="text-center text-muted mt-4">No hay datos de líderes aún.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
