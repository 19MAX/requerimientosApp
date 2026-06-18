<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="custom-container">
    <div class="row mb-6 align-items-center">
        <div class="col-12">
            <h1 class="fs-3 mb-0">Gestión de Clientes</h1>
            <p class="mb-0 text-muted">Visualización de clientes del sistema.</p>
        </div>
    </div>

    <div class="row g-6 mb-6">
        <div class="col-12">
            <div class="card card-lg">
                <div class="table-responsive py-3">
                    <table class="table text-nowrap table-centered table-hover" id="miTabla">
                        <thead class="table-light">
                            <tr>
                                <th>Cédula</th>
                                <th>Cliente</th>
                                <th>Correo</th>
                                <th>Teléfono</th>
                                <th>Dirección</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($clients)): ?>
                                <?php foreach ($clients as $client): ?>
                                    <tr>
                                        <td><?= esc($client['cedula']) ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="icon-shape icon-md rounded-circle bg-primary-subtle text-primary">
                                                    <?= strtoupper(substr($client['first_name'], 0, 2)) ?>
                                                </div>
                                                <span
                                                    class="fw-semibold text-inherit"><?= esc($client['first_name'] . ' ' . $client['last_name']) ?></span>
                                            </div>
                                        </td>
                                        <td><?= esc($client['email']) ?></td>
                                        <td><?= esc($client['phone'] ?? 'N/A') ?></td>
                                        <td>
                                            <?= esc($client['address'] ?? 'N/A') ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-white btn-sm"
                                                onclick="openViewModal(<?= htmlspecialchars(json_encode($client)) ?>)">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-eye text-info">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
                                                    <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"></path>
                                                </svg>
                                            </button>
                                            <!-- <button type="button" class="btn btn-white btn-sm"
                                                onclick="openDeleteModal(<?= $client['id'] ?>, '<?= esc($client['first_name'] . ' ' . $client['last_name']) ?>')">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-trash text-danger">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M4 7l16 0"></path>
                                                    <path d="M10 11l0 6"></path>
                                                    <path d="M14 11l0 6"></path>
                                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                                                </svg>
                                            </button> -->
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!-- <tr>
                                    <td colspan="6" class="text-center py-4">No hay clientes registrados.</td>
                                </tr> -->
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewClientModal" tabindex="-1" aria-labelledby="viewClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewClientModalLabel">Información del Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-uppercase small text-muted">Nombres</label>
                        <p class="fs-5 mb-0" id="view_first_name">—</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-uppercase small text-muted">Apellidos</label>
                        <p class="fs-5 mb-0" id="view_last_name">—</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-uppercase small text-muted">Cédula</label>
                        <p class="fs-5 mb-0" id="view_cedula">—</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-uppercase small text-muted">Correo Electrónico</label>
                        <p class="fs-5 mb-0" id="view_email">—</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-uppercase small text-muted">Teléfono</label>
                        <p class="fs-5 mb-0" id="view_phone">—</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-uppercase small text-muted">Dirección</label>
                        <p class="fs-5 mb-0" id="view_address">—</p>
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
    new DataTable('#miTabla', {
        language: { url: 'https://cdn.datatables.net/plug-ins/2.3.7/i18n/es-ES.json' },
        scrollX: true,
        layout: {
            topStart: {
                buttons: ['copy', 'excel', 'pdf', 'colvis']
            },
        }
    });
    // Función para poblar el modal de visualización
    function openViewModal(user) {
        document.getElementById('view_cedula').textContent = user.cedula ?? 'N/A';
        document.getElementById('view_first_name').textContent = user.first_name ?? 'N/A';
        document.getElementById('view_last_name').textContent = user.last_name ?? 'N/A';
        document.getElementById('view_email').textContent = user.email ?? 'N/A';
        document.getElementById('view_phone').textContent = user.phone ?? 'N/A';
        document.getElementById('view_address').textContent = user.address ?? 'N/A';

        var viewModal = new bootstrap.Modal(document.getElementById('viewClientModal'));
        viewModal.show();
    }
</script>
<?= $this->endSection() ?>