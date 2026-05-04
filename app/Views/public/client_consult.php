<!DOCTYPE html>
<html lang="es">
<head>
    <?= $this->include('partials/head/head-meta') ?>
    <title>CNEL - Consulta pública</title>
    <?= $this->include('partials/head/head-links') ?>
</head>
<body>
    <main class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10 col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4 p-md-5">
                            <h1 class="h3 mb-2">Consulta de requerimientos</h1>
                            <p class="text-muted mb-4">Ingresa tu número de cédula para ver el estado, historial y documentos de tus requerimientos.</p>

                            <form method="get" action="<?= base_url('consulta-requerimientos') ?>" class="row g-3 mb-4">
                                <div class="col-md-8">
                                    <label for="cedula" class="form-label">Número de cédula</label>
                                    <input
                                        type="text"
                                        class="form-control cedula-input"
                                        id="cedula"
                                        name="cedula"
                                        value="<?= esc($cedula ?? '') ?>"
                                        placeholder="Ej: 0912345678"
                                        maxlength="10"
                                        required>
                                </div>
                                <div class="col-md-4 d-grid align-self-end">
                                    <button type="submit" class="btn btn-primary">Consultar</button>
                                </div>
                            </form>

                            <?php if (!empty($message)): ?>
                                <div class="alert alert-info mb-4"><?= esc($message) ?></div>
                            <?php endif; ?>

                            <?php if (!empty($client)): ?>
                                <div class="border rounded-3 p-3 mb-4 bg-light-subtle">
                                    <h2 class="h6 mb-2">Datos del cliente</h2>
                                    <p class="mb-1"><strong>Nombre:</strong> <?= esc($client['first_name'] . ' ' . $client['last_name']) ?></p>
                                    <p class="mb-0"><strong>Cédula:</strong> <?= esc($client['cedula']) ?></p>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($documents)): ?>
                                <div class="accordion" id="accordionDocuments">
                                    <?php foreach ($documents as $index => $doc): ?>
                                        <div class="accordion-item mb-3 border rounded-3 overflow-hidden">
                                            <h2 class="accordion-header" id="heading<?= $doc['id'] ?>">
                                                <button class="accordion-button <?= $index !== 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $doc['id'] ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="collapse<?= $doc['id'] ?>">
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-semibold"><?= esc($doc['document_code']) ?> - <?= esc($doc['title']) ?></span>
                                                        <small class="text-muted">Creado: <?= esc(formatear_fecha($doc['created_at'], 'corta')) ?></small>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse<?= $doc['id'] ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" aria-labelledby="heading<?= $doc['id'] ?>" data-bs-parent="#accordionDocuments">
                                                <div class="accordion-body">
                                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                                        <span><?= statusBadge($doc['status']) ?></span>
                                                        <?php if (!empty($doc['original_url'])): ?>
                                                            <a class="btn btn-sm btn-outline-primary" href="<?= esc($doc['original_url']) ?>" target="_blank" rel="noopener">
                                                                Ver documento original
                                                            </a>
                                                        <?php endif; ?>
                                                        <?php if (!empty($doc['final_url'])): ?>
                                                            <a class="btn btn-sm btn-outline-success" href="<?= esc($doc['final_url']) ?>" target="_blank" rel="noopener">
                                                                Ver documento final
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>

                                                    <?php if (!empty($doc['description'])): ?>
                                                        <p class="mb-3"><strong>Descripción:</strong> <?= esc($doc['description']) ?></p>
                                                    <?php endif; ?>

                                                    <h3 class="h6 mb-3">Historial</h3>
                                                    <?php if (!empty($doc['history'])): ?>
                                                        <ul class="list-group list-group-flush border rounded-3">
                                                            <?php foreach ($doc['history'] as $event): ?>
                                                                <li class="list-group-item">
                                                                    <div class="small text-muted mb-1"><?= esc(formatear_fecha($event['date'], 'corta')) ?></div>
                                                                    <div class="fw-semibold"><?= esc($event['title']) ?></div>
                                                                    <div><?= esc($event['description']) ?></div>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    <?php else: ?>
                                                        <p class="text-muted mb-0">Sin eventos de historial disponibles.</p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?= $this->include('partials/scripts') ?>
</body>
</html>
