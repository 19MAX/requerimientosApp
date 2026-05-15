<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<style>
    /* ── Dropzone personalizada ── */
    .dropzone-area {
        border: 2px dashed #dee2e6;
        border-radius: 0.5rem;
        padding: 1.5rem 1rem;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.2s ease, background-color 0.2s ease;
        /* background-color: #f8f9fa; */
        position: relative;
    }

    .dropzone-area:hover,
    .dropzone-area.dragover {
        border-color: #0d6efd;
        /* background-color: #eef3ff; */
    }

    .dropzone-area .dz-icon {
        color: #adb5bd;
        transition: color 0.2s ease;
    }

    .dropzone-area:hover .dz-icon,
    .dropzone-area.dragover .dz-icon {
        color: #0d6efd;
    }

    .dropzone-area .dz-label {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 0.5rem;
        margin-bottom: 0;
    }

    .dropzone-area .dz-label span {
        color: #0d6efd;
        font-weight: 500;
        text-decoration: underline;
        cursor: pointer;
    }

    /* Preview del archivo seleccionado */
    .file-preview {
        display: none;
        align-items: center;
        gap: 0.75rem;
        /* background: #fff; */
        border: 1px solid var(--ds-gray-300);
        border-radius: 0.5rem;
        padding: 0.6rem 0.85rem;
        margin-top: 0.6rem;
    }

    .file-preview.show {
        display: flex;
    }

    .file-preview .file-icon {
        font-size: 1.4rem;
        line-height: 1;
    }

    .file-preview .file-info {
        flex: 1;
        min-width: 0;
    }

    .file-preview .file-name {
        font-size: 0.82rem;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 0;
    }

    .file-preview .file-size {
        font-size: 0.75rem;
        color: #6c757d;
        margin-bottom: 0;
    }

    .file-preview .btn-remove {
        border: none;
        background: none;
        color: #adb5bd;
        padding: 0;
        line-height: 1;
        cursor: pointer;
        transition: color 0.15s;
    }

    .file-preview .btn-remove:hover {
        color: #dc3545;
    }

    /* .ts-control,
.ts-wrapper.single.input-active .ts-control {
  background: none !important;
  cursor: text;
} */
</style>
<div class="custom-container">
    <div class="row mb-6 align-items-center">
        <div class="col-xl-8 col-lg-6">
            <h1 class="fs-3 mb-0">Gestión de Requerimientos</h1>
            <p class="mb-0 text-muted">Ingresa, actualiza y gestiona las peticiones de los requerimientos y sus estados.
            </p>
        </div>
        <div class="col-xl-4 col-lg-6 text-lg-end mt-3 mt-lg-0">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDocumentModal"
                data-tippy-content="Nuevo requerimiento">
                <i class="fa-solid fa-file-circle-plus fa-lg"></i>
            </button>
        </div>
    </div>

    <div class="row g-6 mb-6">
        <div class="col-12">
            <div class="card card-lg">
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
                            <?php if (!empty($documents)): ?>
                                <?php foreach ($documents as $doc): ?>
                                    <tr>
                                        <td><span class="fw-semibold"><?= esc($doc['document_code']) ?></span>
                                        </td>
                                        <td><?= shortName(esc($doc['client_full_name'])) ?></td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 250px;"
                                                title="<?= esc($doc['title']) ?>">
                                                <?= esc($doc['title']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?= statusBadge($doc['status']) ?>
                                        </td>
                                        <td><?= formatear_fecha($doc['created_at'], 'solo_fecha') ?? 'N/A' ?></td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-white " type="button" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                                </button>

                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <?php if ($doc['status'] === 'pendiente'): ?>

                                                        <li>
                                                            <button class="dropdown-item"
                                                                onclick="openEditModal(<?= htmlspecialchars(json_encode($doc)) ?>)">
                                                                <i class="fa-solid fa-pen-to-square text-warning me-2"></i>
                                                                Editar
                                                            </button>
                                                        </li>

                                                        <li>
                                                            <button class="dropdown-item"
                                                                onclick="openDeleteModal(<?= $doc['id'] ?>, '<?= esc($doc['title']) ?>')">
                                                                <i class="fa-solid fa-trash-can text-danger me-2"></i>
                                                                Eliminar
                                                            </button>
                                                        </li>

                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="<?= base_url('secretaria/documents/download/' . $doc['id']) ?>"
                                                                target="_blank">
                                                                <i class="fa-solid fa-download text-primary me-2"></i>
                                                                Descargar
                                                            </a>
                                                        </li>

                                                    <?php else: ?>

                                                        <li>
                                                            <span class="dropdown-item text-muted">
                                                                <i class="fa-solid fa-lock me-2"></i>
                                                                No disponible
                                                            </span>
                                                        </li>

                                                    <?php endif; ?>
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

<div class="modal fade" id="createDocumentModal" tabindex="-1" aria-labelledby="createDocumentModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createDocumentModalLabel">Ingresar Nuevo Documento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('secretaria/documents/create') ?>" method="POST" enctype="multipart/form-data"
                id="createForm">
                <div class="modal-body">

                    <div class="row">


                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cliente Asociado <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2">
                                <div class="flex-grow-1">
                                    <select id="select-client" name="client_id"
                                        placeholder="Buscar cliente por nombre o correo..." required></select>
                                </div>
                                <button type="button"
                                    class="btn btn-outline-secondary d-flex align-items-center justify-content-center px-3"
                                    data-bs-toggle="modal" data-bs-target="#createClientModal"
                                    title="Crear nuevo cliente" data-tippy-content="Crear nuevo cliente">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-user-plus">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                        <path d="M16 19h6" />
                                        <path d="M19 16v6" />
                                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Título del Documento / Asunto <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title"
                                placeholder="Ej: Petición de Mantenimiento" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Director Asignado <span
                                    class="text-muted fw-normal">(opcional)</span></label>
                            <select id="select-director" name="director_id"
                                placeholder="Buscar director por nombre..."></select>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Descripción o Detalle</label>
                            <textarea class="form-control" name="description" rows="3"
                                placeholder="Breve descripción del trámite..."></textarea>
                        </div>
                        <div class="col-md-6 mb-3">

                            <label class="form-label">Adjuntar Archivo (PDF, DOC, DOCX) <span
                                    class="text-danger">*</span></label>

                            <div class="dropzone-area" id="createDropzone">
                                <input type="file" name="document_file" id="createFileInput" accept=".pdf,.doc,.docx"
                                    required
                                    style="position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" class="dz-icon">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                    <path d="M12 11v6" />
                                    <path d="M9.5 13.5l2.5 -2.5l2.5 2.5" />
                                </svg>
                                <p class="dz-label">Arrastra tu archivo aquí o <span>haz clic para buscar</span></p>
                                <p class="mb-0" style="font-size:0.78rem;color:#adb5bd;">PDF, DOC, DOCX — máx. 5 MB</p>
                            </div>

                            <div class="file-preview" id="createFilePreview">
                                <span class="file-icon" id="createFileIcon">📄</span>
                                <div class="file-info">
                                    <p class="file-name" id="createFileName">archivo.pdf</p>
                                    <p class="file-size" id="createFileSize">0 KB</p>
                                </div>
                                <button type="button" class="btn-remove" onclick="removeFile('create')"
                                    title="Quitar archivo">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M18 6L6 18M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark">Guardar y Enviar a Revisión</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="createClientModal" tabindex="-1" aria-labelledby="createClientModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createClientModalLabel">Nuevo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="ajaxCreateClientForm">
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="first_name" required placeholder="Ej: Juan">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Apellido <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="last_name" required placeholder="Ej: Pérez">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Cédula / RUC</label>
                            <input type="text" class="form-control cedula-input" name="cedula"
                                placeholder="Número de identificación" maxlength="10">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="phone" placeholder="Ej: 0999999999">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" name="email" placeholder="ejemplo@correo.com">
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Dirección</label>
                        <input type="text" class="form-control" name="address" placeholder="Dirección del cliente">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-white w-100 mb-2" data-bs-dismiss="modal">Cancelar y
                        volver</button>
                    <button type="submit" class="btn btn-primary w-100" id="btnSaveClient">Guardar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editDocumentModal" tabindex="-1" aria-labelledby="editDocumentModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDocumentModalLabel">Editar Documento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('secretaria/documents/update') ?>" method="POST" enctype="multipart/form-data"
                id="editForm">
                <input type="hidden" name="id" id="edit_document_id">
                <div class="modal-body">
                    <div class="row">
                        <!-- Cliente -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cliente Asociado <span class="text-danger">*</span></label>
                            <select id="edit-select-client" name="client_id" required></select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Título del Documento / Asunto <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" id="edit_title" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Director Asignado <span
                                    class="text-muted fw-normal">(opcional)</span></label>
                            <select id="edit-select-director" name="director_id"></select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción o Detalle</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reemplazar Archivo <span
                                class="text-muted fw-normal">(opcional)</span></label>

                        <div class="file-preview show mb-2" id="editCurrentFilePreview">
                            <span class="file-icon" id="editCurrentFileIcon">📄</span>
                            <div class="file-info">
                                <p class="file-name" id="editCurrentFileName">archivo-actual.pdf</p>
                                <p class="file-size text-muted" style="font-size:0.75rem;margin:0;">Archivo actual
                                    guardado</p>
                            </div>
                        </div>

                        <div class="dropzone-area" id="editDropzone">
                            <input type="file" name="document_file" id="editFileInput" accept=".pdf,.doc,.docx"
                                style="position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" class="dz-icon">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                <path d="M12 11v6" />
                                <path d="M9.5 13.5l2.5 -2.5l2.5 2.5" />
                            </svg>
                            <p class="dz-label">Arrastra un nuevo archivo o <span>haz clic para buscar</span></p>
                            <p class="mb-0" style="font-size:0.78rem;color:#adb5bd;">PDF, DOC, DOCX — máx. 5 MB</p>
                        </div>

                        <div class="file-preview" id="editFilePreview">
                            <span class="file-icon" id="editFileIcon">📄</span>
                            <div class="file-info">
                                <p class="file-name" id="editFileName">nuevo-archivo.pdf</p>
                                <p class="file-size" id="editFileSize">0 KB</p>
                            </div>
                            <button type="button" class="btn-remove" onclick="removeFile('edit')"
                                title="Quitar selección">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M18 6L6 18M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <small class="text-muted d-block mt-1">Si no seleccionas nada, se conservará el archivo
                            actual.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteDocumentModal" tabindex="-1" aria-labelledby="deleteDocumentModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="icon-shape icon-xl rounded-circle bg-danger-subtle text-danger mb-4 mx-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-alert-triangle">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M12 9v4"></path>
                        <path
                            d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z">
                        </path>
                        <path d="M12 16h.01"></path>
                    </svg>
                </div>
                <h4 class="mb-2">¿Estás seguro?</h4>
                <p class="text-muted mb-4">Vas a eliminar el trámite: <br><strong id="delete_document_title"></strong>.
                    Esta acción no se puede deshacer.</p>
                <form action="<?= base_url('secretaria/documents/delete') ?>" method="POST"
                    class="d-flex justify-content-center gap-2">
                    <input type="hidden" name="id" id="delete_document_id">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Sí, eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script>
    // ── DataTable ────────────────────────────────────────────────
    new DataTable('#miTabla', {
        language: { url: 'https://cdn.datatables.net/plug-ins/2.3.7/i18n/es-ES.json' },
        scrollX: true,
        layout: { topStart: { buttons: ['pageLength', 'excel', 'pdf', 'colvis'] } }
    });

    // ── Tom Select: Búsqueda de Clientes ─────────────────────────
    let clientSelect = new TomSelect('#select-client', {
        valueField: 'id',
        // Como ahora tienes first_name y last_name, no usamos un solo labelField,
        // usamos render functions para mostrar la concatenación.
        searchField: ['first_name', 'last_name', 'cedula', 'email'],
        placeholder: 'Buscar cliente por nombre, apellido o cédula...',
        load: function (query, callback) {
            // Tu backend exige al menos 2 caracteres
            if (query.length < 2) return callback();

            // Ajusta la URL a tu ruta real en CodeIgniter
            var url = '<?= base_url('secretaria/clients/search') ?>?q=' + encodeURIComponent(query);

            fetch(url)
                .then(response => response.json())
                .then(json => {
                    // Tu método search() devuelve la info dentro de "data"
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
                let fullName = escape(item.first_name + ' ' + (item.last_name || ''));
                let initial = escape(item.first_name.charAt(0).toUpperCase());
                let doc = item.cedula ? escape(item.cedula) : 'Sin cédula';

                return `<div class="py-2 px-3 d-flex align-items-center">
                        <div class="me-3">
                            <span class="d-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle fw-bold" style="width: 40px; height: 40px;">
                                ${initial}
                            </span>
                        </div>
                        <div>
                            <div class="mb-0 fw-semibold">
                                ${fullName}
                            </div>
                            <div class="text-muted small">CI: ${doc} | ${item.email ? escape(item.email) : 'Sin correo'}</div>
                        </div>
                    </div>`;
            },
            item: function (item, escape) {
                let fullName = escape(item.first_name + ' ' + (item.last_name || ''));
                return `<div class="d-flex align-items-center">
                        <span class="fw-medium">${fullName}</span>
                    </div>`;
            }
        }
    });

    // ── Tom Select: Directores ───────────────────────────────────
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

    let editClientSelect = new TomSelect('#edit-select-client', {
        valueField: 'id',
        searchField: ['first_name', 'last_name', 'cedula', 'email'],
        placeholder: 'Buscar cliente...',
        load: function (query, callback) {
            if (query.length < 2) return callback();

            fetch('<?= base_url('secretaria/clients/search') ?>?q=' + encodeURIComponent(query))
                .then(res => res.json())
                .then(json => {
                    if (json.success && json.data) {
                        callback(json.data);
                    } else {
                        callback();
                    }
                }).catch(() => callback());
        },
        render: {
            option: function (item, escape) {
                return `<div>${escape(item.first_name + ' ' + item.last_name)}</div>`;
            },
            item: function (item, escape) {
                return `<div>${escape(item.first_name + ' ' + item.last_name)}</div>`;
            }
        }
    });

    // ── Tom Select: Directores para edición ───────────────────
    let editDirectorSelect = new TomSelect('#edit-select-director', {
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

    // ── Crear Cliente por Fetch (AJAX) ───────────────────────────
    document.getElementById('ajaxCreateClientForm').addEventListener('submit', function (e) {
        e.preventDefault();

        let form = this;
        let formData = new FormData(form);
        let btnSubmit = document.getElementById('btnSaveClient');

        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Guardando...';

        // Ajusta la URL a tu ruta real en CodeIgniter
        fetch('<?= base_url('secretaria/clients/store') ?>', {
            method: 'POST',
            body: formData
        })
            .then(async response => {
                const data = await response.json();
                // Si el status no es OK, forzamos que pase al bloque data.success = false
                if (!response.ok) {
                    data.success = false;
                }
                return data;
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Excelente!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Cerrar el modal del cliente
                    let clientModal = bootstrap.Modal.getInstance(document.getElementById('createClientModal'));
                    clientModal.hide();

                    // Agregar el nuevo cliente a Tom Select y seleccionarlo automáticamente
                    // Tu backend store() devuelve el nuevo cliente en "data.data"
                    let newClient = data.data;
                    clientSelect.addOption({
                        id: newClient.id,
                        first_name: newClient.first_name,
                        last_name: newClient.last_name,
                        cedula: newClient.cedula,
                        email: newClient.email
                    });
                    clientSelect.setValue(newClient.id);

                    // Limpiar el formulario
                    form.reset();
                } else {
                    // Error: Procesar mensajes de validación
                    let errorMsg = data.message || 'Ocurrió un error al guardar el cliente.';

                    // Si hay errores de validación de CodeIgniter ($this->clientsModel->errors())
                    if (data.errors) {
                        errorMsg = Object.values(data.errors).join('<br>');
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'No se pudo crear',
                        html: errorMsg,
                        confirmButtonText: 'Entendido'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'Hubo un problema de red o de servidor. Inténtalo de nuevo.'
                });
            })
            .finally(() => {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = 'Guardar Cliente';
            });
    });

    // ── Helpers ──────────────────────────────────────────────────
    function getFileIcon(name) {
        const ext = name.split('.').pop().toLowerCase();
        return ext === 'pdf' ? '📕' : (ext === 'doc' || ext === 'docx') ? '📘' : '📄';
    }

    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(2) + ' MB';
    }

    // ── Drag & Drop genérico ─────────────────────────────────────
    function initDropzone(dropzoneId, inputId, previewId, iconId, nameId, sizeId) {
        const zone = document.getElementById(dropzoneId);
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        const iconEl = document.getElementById(iconId);
        const nameEl = document.getElementById(nameId);
        const sizeEl = document.getElementById(sizeId);

        function showPreview(file) {
            iconEl.textContent = getFileIcon(file.name);
            nameEl.textContent = file.name;
            if (sizeEl) sizeEl.textContent = formatSize(file.size);
            preview.classList.add('show');
            zone.style.display = 'none';
        }

        input.addEventListener('change', function () {
            if (this.files && this.files[0]) showPreview(this.files[0]);
        });

        zone.addEventListener('dragover', function (e) {
            e.preventDefault();
            zone.classList.add('dragover');
        });
        zone.addEventListener('dragleave', function () {
            zone.classList.remove('dragover');
        });
        zone.addEventListener('drop', function (e) {
            e.preventDefault();
            zone.classList.remove('dragover');
            const file = e.dataTransfer.files[0];
            if (!file) return;

            const allowed = ['pdf', 'doc', 'docx'];
            const ext = file.name.split('.').pop().toLowerCase();
            if (!allowed.includes(ext)) {
                alert('Solo se permiten archivos PDF, DOC o DOCX.');
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                alert('El archivo supera el tamaño máximo de 5 MB.');
                return;
            }

            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;

            showPreview(file);
        });
    }

    // ── Quitar archivo seleccionado ──────────────────────────────
    function removeFile(prefix) {
        const input = document.getElementById(prefix + 'FileInput');
        const preview = document.getElementById(prefix + 'FilePreview');
        const zone = document.getElementById(prefix + 'Dropzone');

        input.value = '';
        preview.classList.remove('show');
        zone.style.display = '';
    }

    // ── Init dropzones ───────────────────────────────────────────
    initDropzone('createDropzone', 'createFileInput', 'createFilePreview', 'createFileIcon', 'createFileName', 'createFileSize');
    initDropzone('editDropzone', 'editFileInput', 'editFilePreview', 'editFileIcon', 'editFileName', 'editFileSize');

    // ── Limpiar modal de crear al cerrarse ───────────────────────
    document.getElementById('createDocumentModal').addEventListener('hidden.bs.modal', function () {
        removeFile('create');
        document.getElementById('createForm').reset();
        clientSelect.clear();
        directorSelect.clear();
    });

    // ── Modal de edición ─────────────────────────────────────────
    function openEditModal(doc) {
        document.getElementById('edit_document_id').value = doc.id;
        document.getElementById('edit_title').value = doc.title;
        document.getElementById('edit_description').value = doc.description ?? '';

        // ✅ Cargar cliente seleccionado
        if (doc.client_id) {
            editClientSelect.addOption({
                id: doc.client_id,
                first_name: doc.client_full_name,
                last_name: ''
            });
            editClientSelect.setValue(doc.client_id);
        }

        // ✅ Cargar director seleccionado
        if (doc.director_id) {
            editDirectorSelect.addOption({
                id: doc.director_id,
                name: doc.director_name || '',
                email: doc.director_email || ''
            });
            editDirectorSelect.setValue(doc.director_id);
        }

        // archivo actual
        const currentPreview = document.getElementById('editCurrentFilePreview');
        if (doc.file_name) {
            document.getElementById('editCurrentFileIcon').textContent = getFileIcon(doc.file_name);
            document.getElementById('editCurrentFileName').textContent = doc.file_name;
            currentPreview.classList.add('show');
        } else {
            currentPreview.classList.remove('show');
        }

        removeFile('edit');
        new bootstrap.Modal(document.getElementById('editDocumentModal')).show();
    }

    // ── Limpiar modal de editar al cerrarse ──────────────────────
    document.getElementById('editDocumentModal').addEventListener('hidden.bs.modal', function () {
        removeFile('edit');
        editDirectorSelect.clear();
    });

    // ── Modal de eliminación ─────────────────────────────────────
    function openDeleteModal(id, title) {
        document.getElementById('delete_document_id').value = id;
        document.getElementById('delete_document_title').innerText = title;
        new bootstrap.Modal(document.getElementById('deleteDocumentModal')).show();
    }

    // ── Volver al modal anterior tras crear cliente ──────────────
    document.getElementById('createClientModal').addEventListener('hidden.bs.modal', function () {
        // Si necesitas volver a abrir el modal de documento automáticamente
        // new bootstrap.Modal(document.getElementById('createDocumentModal')).show();
    });
</script>
<?= $this->endSection() ?>