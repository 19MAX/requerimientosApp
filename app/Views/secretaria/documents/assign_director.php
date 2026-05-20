<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="custom-container">
    <div class="row mb-6 align-items-center">
        <div class="col-xl-8 col-lg-6">
            <h1 class="fs-3 mb-0">Asignar Director</h1>
            <p class="mb-0 text-muted">Documentos pendientes que aún no tienen un director asignado.</p>
        </div>
        <div class="col-xl-4 col-lg-6 text-lg-end mt-3 mt-lg-0">
            <a href="<?= base_url('secretaria/documents') ?>" class="btn btn-light">
                <i class="fa-solid fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <div class="row g-6 mb-6">
        <div class="col-12">
            <div class="card card-lg">
                <?php if (empty($documents)): ?>
                    <div class="card-body text-center py-5">
                        <div class="icon-shape icon-xl rounded-circle bg-success-subtle text-success mb-4 mx-auto">
                            <i class="fa-solid fa-check fa-2x"></i>
                        </div>
                        <h4 class="mb-2">¡Todo al día!</h4>
                        <p class="text-muted mb-0">Todos los documentos tienen un director asignado.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive py-3">
                        <table class="table text-nowrap mb-0 table-centered table-hover" id="miTabla">
                            <thead class="table-light">
                                <tr>
                                    <th>Trámite</th>
                                    <th>Cliente</th>
                                    <th>Título</th>
                                    <th>Estado</th>
                                    <th>Fecha de Ingreso</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($documents as $doc): ?>
                                    <tr>
                                        <td><span class="fw-semibold"><?= esc($doc['document_code']) ?></span></td>
                                        <td><?= shortName(esc($doc['client_full_name'])) ?></td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 250px;"
                                                title="<?= esc($doc['title']) ?>">
                                                <?= esc($doc['title']) ?>
                                            </div>
                                        </td>
                                        <td><?= statusBadge($doc['status']) ?></td>
                                        <td><?= formatear_fecha($doc['created_at'], 'solo_fecha') ?? 'N/A' ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning"
                                                onclick="openAssignModal(<?= htmlspecialchars(json_encode($doc)) ?>)">
                                                <i class="fa-solid fa-user-tie me-1"></i> Asignar Director
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="assignDirectorModal" tabindex="-1" aria-labelledby="assignDirectorModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignDirectorModalLabel">Asignar Director</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('secretaria/documents/assign-director/save') ?>" method="POST" id="assignForm">
                <div class="modal-body">
                    <input type="hidden" name="document_id" id="assign_document_id">

                    <div class="mb-3">
                        <label class="form-label">Documento</label>
                        <p class="mb-0 fw-semibold" id="assign_document_code"></p>
                        <p class="text-muted small mb-0" id="assign_document_title"></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Director <span class="text-danger">*</span></label>
                        <select id="select-director" name="director_id" required></select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Asignar Director</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script>
    new DataTable('#miTabla', {
        language: { url: 'https://cdn.datatables.net/plug-ins/2.3.7/i18n/es-ES.json' },
        scrollX: true,
        layout: { topStart: { buttons: ['pageLength', 'excel', 'pdf', 'colvis'] } }
    });

    let directorSelect = new TomSelect('#select-director', {
        valueField: 'id',
        searchField: ['name', 'email'],
        placeholder: 'Buscar director por nombre o correo...',
        load: function (query, callback) {
            if (query.length < 2) return callback();

            var url = '<?= base_url('secretaria/directors/search') ?>?q=' + encodeURIComponent(query);

            fetch(url)
                .then(response => response.json())
                .then(json => {
                    if (json.success && json.data) {
                        callback(json.data);
                    } else {
                        callback();
                    }
                }).catch(() => {
                    callback();
                });
        },
        render: {
            option: function (item, escape) {
                let initial = escape(item.name.charAt(0).toUpperCase());
                let name = escape(item.name);
                let email = item.email ? escape(item.email) : '';

                return `<div class="py-2 px-3 d-flex align-items-center">
                        <div class="me-3">
                            <span class="d-flex align-items-center justify-content-center bg-warning-subtle text-warning rounded-circle fw-bold" style="width: 36px; height: 36px;">
                                ${initial}
                            </span>
                        </div>
                        <div>
                            <div class="mb-0 fw-semibold">${name}</div>
                            <div class="text-muted small">${email}</div>
                        </div>
                    </div>`;
            },
            item: function (item, escape) {
                let name = escape(item.name);
                return `<div class="d-flex align-items-center">
                        <i class="fa-solid fa-user-tie text-warning me-2"></i>
                        <span class="fw-medium">${name}</span>
                    </div>`;
            },
            no_results: function () {
                return `<div class="px-3 py-2 text-muted small">Sin resultados</div>`;
            }
        }
    });

    function openAssignModal(doc) {
        document.getElementById('assign_document_id').value = doc.id;
        document.getElementById('assign_document_code').textContent = doc.document_code;
        document.getElementById('assign_document_title').textContent = doc.title;

        directorSelect.clear();

        new bootstrap.Modal(document.getElementById('assignDirectorModal')).show();
    }

    document.getElementById('assignDirectorModal').addEventListener('hidden.bs.modal', function () {
        directorSelect.clear();
    });
</script>
<?= $this->endSection() ?>