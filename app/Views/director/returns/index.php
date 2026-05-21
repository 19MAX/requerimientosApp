<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="custom-container">
    <div class="row mb-6 align-items-center">
        <div class="col-xl-8 col-lg-6">
            <h1 class="fs-3 mb-0">Gestión de Devoluciones</h1>
            <p class="mb-0 text-muted">Revisa las devoluciones enviadas por los líderes de área.</p>
        </div>
    </div>

    <?php if (!empty($pendingReturns)): ?>
    <div class="row mb-6">
        <div class="col-12">
            <div class="card card-lg border-warning">
                <div class="card-header bg-warning-subtle">
                    <h5 class="mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        Devoluciones Pendientes (<?= count($pendingReturns) ?>)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nº Trámite</th>
                                    <th>Documento</th>
                                    <th>Líder que devuelve</th>
                                    <th>Motivo</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingReturns as $ret): ?>
                                    <tr>
                                        <td><span class="fw-bold text-primary"><?= esc($ret['document_code'] ?? 'N/D') ?></span></td>
                                        <td><?= esc($ret['document_title'] ?? 'N/A') ?></td>
                                        <td><?= esc($ret['lider_name'] ?? 'N/A') ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-link p-0" onclick="showReason('<?= htmlspecialchars(addslashes($ret['reason'] ?? ''), ENT_QUOTES) ?>')">
                                                Ver motivo
                                            </button>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($ret['created_at'])) ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" onclick="openHandleModal(<?= $ret['id'] ?>, '<?= htmlspecialchars(addslashes($ret['document_code'] ?? ''), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($ret['lider_name'] ?? ''), ENT_QUOTES) ?>')">
                                                Revisar
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="row mb-6">
        <div class="col-12">
            <div class="alert alert-success d-flex align-items-center mb-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                No hay devoluciones pendientes de revisar.
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($processedReturns)): ?>
    <div class="row">
        <div class="col-12">
            <div class="card card-lg">
                <div class="card-header">
                    <h5 class="mb-0">Historial de Devoluciones Procesadas</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="processedTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Nº Trámite</th>
                                    <th>Documento</th>
                                    <th>Líder</th>
                                    <th>Decisión</th>
                                    <th>Respuesta del Director</th>
                                    <th>Director</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($processedReturns as $ret): ?>
                                    <tr>
                                        <td><span class="fw-bold text-primary"><?= esc($ret['document_code'] ?? 'N/D') ?></span></td>
                                        <td><?= esc($ret['document_title'] ?? 'N/A') ?></td>
                                        <td><?= esc($ret['lider_name'] ?? 'N/A') ?></td>
                                        <td>
                                            <?php if ($ret['status'] === 'aceptado'): ?>
                                                <span class="badge bg-success">Aceptada</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Rechazada</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($ret['director_response'] ?? 'N/A') ?></td>
                                        <td><?= esc($ret['director_response_name'] ?? 'N/A') ?></td>
                                        <td><?= $ret['responded_at'] ? date('d/m/Y H:i', strtotime($ret['responded_at'])) : 'N/A' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="reasonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Motivo de la Devolución</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="reasonContent" style="white-space: pre-wrap;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="handleReturnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Revisar Devolución</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('director/returns/handle') ?>" method="POST">
                <input type="hidden" name="return_id" id="handle_return_id">
                <input type="hidden" name="action" id="handle_action">
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <strong>Trámite:</strong> <span id="handle_doc_code"></span><br>
                        <strong>Líder:</strong> <span id="handle_lider_name"></span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Respuesta / Decisión <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="director_response" id="handle_response" rows="3"
                            placeholder="Indica la razón de tu decisión y qué acciones tomarás..."
                            required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" onclick="document.getElementById('handle_action').value='aceptado'">
                        Aceptar Devolución
                    </button>
                    <button type="submit" class="btn btn-danger" onclick="document.getElementById('handle_action').value='rechazado'">
                        Rechazar Devolución
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function showReason(reason) {
        document.getElementById('reasonContent').innerText = reason || 'Sin motivo registrado.';
        var modal = new bootstrap.Modal(document.getElementById('reasonModal'));
        modal.show();
    }

    function openHandleModal(returnId, docCode, liderName) {
        document.getElementById('handle_return_id').value = returnId;
        document.getElementById('handle_doc_code').innerText = docCode || 'N/D';
        document.getElementById('handle_lider_name').innerText = liderName || 'N/A';
        document.getElementById('handle_response').value = '';
        var modal = new bootstrap.Modal(document.getElementById('handleReturnModal'));
        modal.show();
    }

    <?php if (!empty($processedReturns)): ?>
    new DataTable('#processedTable', {
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.3.7/i18n/es-ES.json',
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
        order: [[6, 'desc']]
    });
    <?php endif; ?>
</script>
<?= $this->endSection() ?>