<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="custom-container">
    <div class="row mb-6 align-items-center">
        <div class="col-xl-8 col-lg-6">
            <h1 class="fs-3 mb-0">Mis Actividades Asignadas</h1>
            <p class="mb-0 text-muted">Revisa tus tareas pendientes, inicia su ejecución y sube tus evidencias.</p>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-6" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-6" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-6 mb-6">
        <div class="col-12">
            <div class="card card-lg">
                <div class="card-header border-bottom-0">
                    <h5 class="mb-0">Bandeja de Tareas</h5>
                </div>
                <div class="table-responsive">
                    <table class="table text-nowrap mb-0 table-centered table-hover" id="liderTable">
                        <thead class="table-light">
                            <tr>
                                <th>N° Asignación</th>
                                <th>Documento Origen</th>
                                <th>Asignado Por</th>
                                <th>Fecha Límite</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($assignments)): ?>
                                <?php foreach ($assignments as $task): ?>
                                    <tr>
                                        <td><span class="fw-semibold">#<?= str_pad($task['id'], 5, '0', STR_PAD_LEFT) ?></span>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 200px;"
                                                title="<?= esc($task['document_title']) ?>">
                                                <?= esc($task['document_title']) ?>
                                            </div>
                                            <small class="text-muted">Doc #<?= $task['document_id'] ?></small>
                                        </td>
                                        <td><?= esc($task['director_name'] ?? 'N/A') ?></td>
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
                                        <td>
                                            <button type="button" class="btn btn-white btn-sm"
                                                onclick="openDetailsModal(<?= htmlspecialchars(json_encode($task), ENT_QUOTES) ?>)">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-eye text-dark">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                    <path
                                                        d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                                </svg>
                                                Detalles
                                            </button>

                                            <?php if ($task['status'] === 'pendiente'): ?>
                                                <form action="<?= base_url('lider/my-assignments/start') ?>" method="POST"
                                                    class="d-inline">
                                                    <input type="hidden" name="assignment_id" value="<?= $task['id'] ?>">
                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round"
                                                            class="icon icon-tabler icons-tabler-outline icon-tabler-player-play">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M7 4v16l13 -8z" />
                                                        </svg>
                                                        Iniciar Tarea
                                                    </button>
                                                </form>

                                            <?php elseif ($task['status'] === 'en_progreso'): ?>
                                                <button type="button" class="btn btn-success btn-sm"
                                                    onclick="openReportModal(<?= $task['id'] ?>, <?= $task['document_id'] ?>)">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-upload">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path
                                                            d="M4 14a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-4z" />
                                                        <path d="M12 12l0 -9" />
                                                        <path d="M9 6l3 -3l3 3" />
                                                    </svg>
                                                    Subir Evidencia
                                                </button>

                                            <?php else: ?>
                                                <span class="text-muted small ms-2"><svg xmlns="http://www.w3.org/2000/svg"
                                                        width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-check">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M5 12l5 5l10 -10" />
                                                    </svg> Finalizado</span>
                                            <?php endif; ?>
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

<div class="modal fade" id="detailsTaskModal" tabindex="-1" aria-labelledby="detailsTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsTaskModalLabel">Detalles de la Asignación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted mb-0">Documento de Origen</label>
                        <p class="fw-bold mb-1" id="detail_doc_title"></p>
                        <a href="#" id="detail_doc_link" class="btn btn-sm btn-outline-primary mt-1" target="_blank">
                            Descargar Archivo Original
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted mb-0">Asignado Por (Director)</label>
                        <p class="fw-bold mb-0" id="detail_director_name"></p>
                    </div>
                </div>

                <hr>

                <div class="mb-3">
                    <label class="form-label text-primary"><svg xmlns="http://www.w3.org/2000/svg" width="20"
                            height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-message-2 me-1">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M8 9h8" />
                            <path d="M8 13h6" />
                            <path
                                d="M9 18h-3a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-3l-3 3l-3 -3z" />
                        </svg> Instrucciones del Director</label>
                    <div class="p-3 bg-gray-100 rounded" id="detail_instructions"
                        style="white-space: pre-wrap;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reportTaskModal" tabindex="-1" aria-labelledby="reportTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportTaskModalLabel">Reportar Actividad (Subir Evidencia)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('lider/my-assignments/report') ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="assignment_id" id="report_assignment_id">
                <input type="hidden" name="document_id" id="report_document_id">
                <div class="modal-body">

                    <div class="alert alert-warning bg-warning-subtle text-warning-emphasis border-0">
                        Al enviar este reporte, la tarea se marcará como <strong>Completada</strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Comentarios de Ejecución <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="comment" rows="4"
                            placeholder="Describe lo que se realizó, observaciones o resultados obtenidos..."
                            required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Adjuntar Archivo de Evidencia (Opcional)</label>
                        <input type="file" class="form-control" name="report_file" accept=".pdf,.doc,.docx,.jpg,.png">
                        <small class="text-muted">Formatos permitidos: PDF, Word, JPG, PNG. Máx 5MB.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar y Finalizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Inicializar DataTables
    new DataTable('#liderTable', {
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.3.7/i18n/es-ES.json',
        },
        order: [[0, 'desc']] // Las más recientes primero
    });

    // Poblar y abrir el Modal de Detalles
    function openDetailsModal(task) {
        document.getElementById('detail_doc_title').innerText = task.document_title;
        document.getElementById('detail_director_name').innerText = task.director_name;
        document.getElementById('detail_instructions').innerText = task.instructions;

        // Configurar el enlace de descarga del documento original
        let downloadBtn = document.getElementById('detail_doc_link');
        if (task.doc_file_path) {
            downloadBtn.href = "<?= base_url('secretaria/documents/download/') ?>" + task.document_id;
            downloadBtn.style.display = 'inline-block';
        } else {
            downloadBtn.style.display = 'none';
        }

        var modal = new bootstrap.Modal(document.getElementById('detailsTaskModal'));
        modal.show();
    }

    // Poblar y abrir el Modal de Reporte (Evidencias)
    function openReportModal(assignmentId, documentId) {
        document.getElementById('report_assignment_id').value = assignmentId;
        document.getElementById('report_document_id').value = documentId;

        var modal = new bootstrap.Modal(document.getElementById('reportTaskModal'));
        modal.show();
    }
</script>
<?= $this->endSection() ?>