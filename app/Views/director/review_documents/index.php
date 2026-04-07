<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="custom-container">
    <div class="row mb-6 align-items-center">
        <div class="col-xl-8 col-lg-6">
            <h1 class="fs-3 mb-0">Revisión y Asignación de Requerimientos</h1>
            <p class="mb-0 text-muted">Bandeja de entrada para revisar peticiones y derivarlas a los Líderes de Área.
            </p>
        </div>
    </div>

    <div class="row g-6 mb-6">
        <div class="col-12">
            <div class="card card-lg">
                <div class="table-responsive py-3">
                    <table class="table text-nowrap mb-0 table-centered table-hover" id="directorTable">
                        <thead class="table-light">
                            <tr>
                                <th>Trámite</th>
                                <th>Cliente</th>
                                <th>Título</th>
                                <th>Estado</th>
                                <th>Creado por</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($documents)): ?>
                                <?php foreach ($documents as $doc): ?>
                                    <tr>
                                        <td><span class="fw-semibold"><?= esc($doc['document_code']) ?></span>
                                        </td>
                                        <td>
                                            <?= shortName(esc($doc['client_full_name'])) ?>
                                        </td>

                                        <td>
                                            <div class="text-truncate" style="max-width: 200px;"
                                                title="<?= esc($doc['title']) ?>">
                                                <?= esc($doc['title']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?= statusBadge($doc['status']) ?>
                                        </td>
                                        <td><?= esc($doc['creator_name'] ?? 'N/A') ?></td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-white " type="button" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                                </button>

                                                <ul class="dropdown-menu dropdown-menu-end">

                                                    <?php if (in_array($doc['status'], ['pendiente', 'en_revision', 'aprobado'])): ?>
                                                        <li>
                                                            <button class="dropdown-item"
                                                                onclick="openReviewModal(<?= $doc['id'] ?>, '<?= htmlspecialchars(esc($doc['title']), ENT_QUOTES) ?>',     '<?= base_url($doc['file_path']) ?>')">
                                                                <i class="fa-solid fa-eye text-info me-2"></i>
                                                                Revisar
                                                            </button>
                                                        </li>
                                                    <?php endif; ?>
                                                    <?php if ($doc['status'] === 'asignado'): ?>
                                                        <li>
                                                            <button class="dropdown-item"
                                                                onclick="openReassignModal(<?= $doc['id'] ?>, '<?= htmlspecialchars(esc($doc['title']), ENT_QUOTES) ?>', '<?= $doc['assigned_to'] ?? '' ?>')">
                                                                <i class="fa-solid fa-user-pen text-warning me-2"></i> Reasignar /
                                                                Editar
                                                            </button>
                                                        </li>
                                                    <?php endif; ?>

                                                    <li>
                                                        <a class="dropdown-item" href="<?= base_url($doc['file_path']) ?>"
                                                            target="_blank">
                                                            <i class="fa-solid fa-download text-primary me-2"></i>
                                                            Descargar
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reviewDocumentModal" tabindex="-1" aria-labelledby="reviewDocumentModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="reviewDocumentModalLabel">Revisar Requerimiento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('director/review-documents/handle') ?>" method="POST" id="reviewForm">
                <input type="hidden" name="document_id" id="review_document_id">
                <input type="hidden" name="status" id="review_decision_hidden">
                <input type="hidden" name="assigned_to" id="review_leader_hidden">

                <div class="modal-body">

                    <!-- Info del documento -->
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 border mb-4"
                        style="background:var(--bs-secondary-bg)">

                        <div class="flex-grow-1 min-w-0">
                            <p class="fw-bold mb-0" id="review_document_title"></p>
                            <p class="text-muted small mb-0">Documento seleccionado para revisión</p>
                        </div>

                        <!-- BOTÓN DESCARGA -->
                        <div>
                            <a href="#" id="download_document_btn" class="btn btn-outline-primary" target="_blank">
                                <i class="fa-solid fa-download me-1"></i> Descargar
                            </a>
                        </div>

                    </div>

                    <!-- Selector de decisión -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Decisión <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <input type="radio" class="btn-check" name="ui_decision" id="decision_approve"
                                value="aprobado" autocomplete="off">
                            <label class="btn btn-outline-success flex-fill py-2" for="decision_approve">
                                <i class="fa-solid fa-circle-check me-2"></i>Aprobar
                            </label>

                            <input type="radio" class="btn-check" name="ui_decision" id="decision_reject"
                                value="rechazado" autocomplete="off">
                            <label class="btn btn-outline-danger flex-fill py-2" for="decision_reject">
                                <i class="fa-solid fa-circle-xmark me-2"></i>Rechazar
                            </label>
                        </div>
                    </div>

                    <!-- Panel: Aprobar -->
                    <div id="panel_approve" style="display:none">
                        <div class="border rounded-3 p-3 mb-3">

                            <div class="row">
                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-semibold mb-2">
                                        <i class="fa-solid fa-user-tie me-1"></i>
                                        Líder de Área Responsable <span class="text-danger">*</span>
                                    </label>
                                    <select id="select-leader" name="leader_id"
                                        placeholder="Buscar líder por nombre o área..."></select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold mb-2">
                                        <i class="fa-solid fa-calendar me-1"></i>
                                        Fecha Límite <span class="text-muted fw-normal">(opcional)</span>
                                    </label>
                                    <input type="date" class="form-control" name="due_date" min="<?= date('Y-m-d') ?>">
                                </div>

                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="fa-solid fa-list-check me-1"></i>
                                    Instrucciones de Ejecución <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" name="instructions" id="approve_instructions" rows="3"
                                    placeholder="Describe qué debe hacer el líder de área con este documento..."></textarea>
                                <div class="form-text mt-1">
                                    Se notificará al líder seleccionado para gestionar el requerimiento.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panel: Rechazar -->
                    <div id="panel_reject" style="display:none">
                        <div class="border rounded-3 p-3">
                            <label class="form-label fw-semibold text-danger mb-2" for="reject_reason">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                Motivo de Rechazo <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="reject_reason" name="review_notes" rows="4"
                                placeholder="Describe el motivo por el cual se rechaza este requerimiento..."></textarea>
                            <div class="form-text mt-1">
                                Este mensaje será visible como justificación del rechazo.
                            </div>
                        </div>
                    </div>

                </div><!-- /modal-body -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-secondary" id="btnReviewSubmit" disabled>
                        <i class="fa-solid fa-paper-plane me-1"></i>
                        <span id="btnReviewLabel">Selecciona una decisión</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="reassignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modificar Asignación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="<?= base_url('director/review-documents/reassign') ?>" method="POST" id="reassignForm">
                <input type="hidden" name="document_id" id="reassign_document_id">

                <div class="modal-body">
                    <div class="alert alert-info py-2 small">
                        <i class="fa-solid fa-circle-info me-2"></i>
                        Selecciona si deseas reasignar el documento a un nuevo líder o reabrirlo para devolverlo a
                        revisión.
                    </div>

                    <div class="mb-3 border-bottom pb-3">
                        <label class="form-label fw-bold">Acción a realizar</label>
                        <select class="form-select" name="action" id="reassign_action">
                            <option value="reassign">Reasignar a otro líder</option>
                            <option value="reopen">Devolver a "En Revisión" (Reabrir)</option>
                        </select>
                    </div>

                    <div id="reassign_fields_container">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nuevo Líder Responsable <span
                                        class="text-danger">*</span></label>
                                <select id="select-leader-reassign" name="assigned_to"
                                    placeholder="Buscar nuevo líder..."></select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nueva Fecha Límite <span
                                        class="text-muted fw-normal">(opcional)</span></label>
                                <input type="date" class="form-control" name="due_date" min="<?= date('Y-m-d') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nuevas Instrucciones <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" name="instructions" id="reassign_instructions" rows="3"
                                placeholder="Instrucciones para el nuevo líder..."></textarea>
                        </div>
                    </div>

                    <div id="reopen_info_msg" style="display:none;">
                        <div class="alert alert-warning mb-0 border-0">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i>
                            Al reabrir, se cancelará la asignación actual y el requerimiento volverá a tu bandeja
                            principal para que puedas rechazarlo o aprobarlo desde cero.
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Ejecutar Acción</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // ── DataTable ────────────────────────────────────────────────
    new DataTable('#directorTable', {
        language: { url: 'https://cdn.datatables.net/plug-ins/2.3.7/i18n/es-ES.json' },
        scrollX: true,
        layout: { topStart: { buttons: ['pageLength', 'excel', 'pdf'] } }
    });

    // ── Tom Select: Líderes ──────────────────────────────────────
    let leaderSelect = null;

    function initLeaderSelect() {
        if (leaderSelect) return;

        leaderSelect = new TomSelect('#select-leader', {
            valueField: 'id',
            searchField: ['name', 'email'],
            placeholder: 'Buscar líder por nombre o área...',
            load: function (query, callback) {
                if (query.length < 2) return callback();
                fetch('<?= base_url('director/leaders/search') ?>?q=' + encodeURIComponent(query))
                    .then(r => r.json())
                    .then(json => {
                        if (json.success && json.data) callback(json.data);
                        else callback();
                    })
                    .catch(() => callback());
            },
            render: {
                option: function (item, escape) {
                    const initial = escape(item.name.charAt(0).toUpperCase());
                    const name = escape(item.name);
                    return `<div class="d-flex align-items-center gap-3 ">
                        <span class="d-flex align-items-center justify-content-center bg-primary-subtle
                                     text-primary rounded-circle fw-bold flex-shrink-0"
                              style="width:36px;height:36px;">${initial}</span>
                        <div>
                            <div class="fw-semibold small">${name}</div>
                        </div>
                    </div>`;
                },
                item: function (item, escape) {
                    const name = escape(item.name);
                    return `<div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-user-tie text-primary"></i>
                        <span class="fw-medium">${name}</span>
                    </div>`;
                },
                no_results: function () {
                    return `<div class="px-3 py-2 text-muted small">Sin resultados</div>`;
                }
            }
        });
    }

    // ── Abrir modal ──────────────────────────────────────────────
    function openReviewModal(id, title, fileUrl) {
        // Datos básicos
        document.getElementById('review_document_id').value = id;
        document.getElementById('review_document_title').innerText = title;

        document.getElementById('download_document_btn').href = fileUrl;

        // Resetear estado
        document.querySelectorAll('input[name="ui_decision"]').forEach(r => r.checked = false);
        document.getElementById('panel_approve').style.display = 'none';
        document.getElementById('panel_reject').style.display = 'none';
        document.getElementById('reject_reason').value = '';
        document.getElementById('approve_instructions').value = '';
        document.getElementById('review_decision_hidden').value = '';
        document.getElementById('btnReviewSubmit').disabled = true;
        document.getElementById('btnReviewSubmit').className = 'btn btn-secondary';
        document.getElementById('btnReviewLabel').textContent = 'Selecciona una decisión';

        initLeaderSelect();
        if (leaderSelect) leaderSelect.clear();

        new bootstrap.Modal(document.getElementById('reviewDocumentModal')).show();
    }

    // ── Cambio de decisión ───────────────────────────────────────
    document.querySelectorAll('input[name="ui_decision"]').forEach(radio => {
        radio.addEventListener('change', function () {
            const decision = this.value;
            const btn = document.getElementById('btnReviewSubmit');
            const label = document.getElementById('btnReviewLabel');

            document.getElementById('review_decision_hidden').value = decision;
            btn.disabled = false;

            if (decision === 'aprobado') {
                document.getElementById('panel_approve').style.display = '';
                document.getElementById('panel_reject').style.display = 'none';
                btn.className = 'btn btn-success';
                label.textContent = 'Aprobar y Asignar';

                document.getElementById('approve_instructions').setAttribute('required', '');
                document.getElementById('reject_reason').removeAttribute('required');

            } else {
                document.getElementById('panel_reject').style.display = '';
                document.getElementById('panel_approve').style.display = 'none';
                btn.className = 'btn btn-danger';
                label.textContent = 'Enviar Rechazo';

                document.getElementById('reject_reason').setAttribute('required', '');
                document.getElementById('approve_instructions').removeAttribute('required');
            }
        });
    });

    // ── Validación antes de enviar ───────────────────────────────
    document.getElementById('reviewForm').addEventListener('submit', function (e) {
        const decision = document.getElementById('review_decision_hidden').value;

        if (!decision) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Falta la decisión', text: 'Debes seleccionar Aprobar o Rechazar.' });
            return;
        }

        if (decision === 'aprobado') {
            if (!leaderSelect?.getValue()) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Falta el líder', text: 'Selecciona un líder de área.' });
                return;
            }
            if (!document.getElementById('approve_instructions').value.trim()) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Faltan instrucciones', text: 'Escribe las instrucciones para el líder.' });
                return;
            }
            // Pasar el valor del TomSelect al hidden antes de enviar
            document.getElementById('review_leader_hidden').value = leaderSelect.getValue();
        }

        if (decision === 'rechazado' && !document.getElementById('reject_reason').value.trim()) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Falta el motivo', text: 'Describe el motivo del rechazo.' });
        }
    });

    let reassignLeaderSelect = null;

    function openReassignModal(id, title, currentLeaderId) {
        document.getElementById('reassign_document_id').value = id;

        // Resetear form a su estado inicial por si lo abren varias veces
        document.getElementById('reassign_action').value = 'reassign';
        document.getElementById('reassign_instructions').value = '';

        // Disparar el evento change para mostrar los paneles correctos
        document.getElementById('reassign_action').dispatchEvent(new Event('change'));

        // Inicializar TomSelect para el modal de reasignación
        if (!reassignLeaderSelect) {
            reassignLeaderSelect = new TomSelect('#select-leader-reassign', {
                valueField: 'id',
                labelField: 'name',
                searchField: ['name', 'email'],
                load: function (query, callback) {
                    if (query.length < 2) return callback();
                    fetch('<?= base_url('director/leaders/search') ?>?q=' + encodeURIComponent(query))
                        .then(r => r.json())
                        .then(json => callback(json.data))
                        .catch(() => callback());
                },
                render: {
                    option: (item, escape) => `<div class="py-2 px-3"><strong>${escape(item.name)}</strong></div>`,
                    item: (item, escape) => `<div><i class="fa-solid fa-user-tie me-2"></i>${escape(item.name)}</div>`
                }
            });
        }

        reassignLeaderSelect.clear();
        new bootstrap.Modal(document.getElementById('reassignModal')).show();
    }

    // Lógica para mostrar/ocultar campos dependiendo si elige 'reassign' o 'reopen'
    document.getElementById('reassign_action').addEventListener('change', function () {
        const reassignContainer = document.getElementById('reassign_fields_container');
        const reopenMsg = document.getElementById('reopen_info_msg');
        const instructionInput = document.getElementById('reassign_instructions');

        if (this.value === 'reopen') {
            // Ocultar campos de líder e instrucciones
            reassignContainer.style.display = 'none';
            reopenMsg.style.display = 'block';

            // Quitar validaciones de HTML5 para poder enviar el form limpio
            instructionInput.removeAttribute('required');
        } else {
            // Mostrar campos de líder e instrucciones
            reassignContainer.style.display = 'block';
            reopenMsg.style.display = 'none';

            // El controlador exige instrucciones, así que las hacemos requeridas en el front
            instructionInput.setAttribute('required', 'true');
        }
    });

</script>
<?= $this->endSection() ?>