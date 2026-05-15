<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="custom-container">
    <div class="row mb-6 align-items-center">
        <div class="col-xl-8 col-lg-6">
            <h1 class="fs-3 mb-0">Búsqueda de Documentos</h1>
            <p class="mb-0 text-muted">Busca y filtra documentos por diversos criterios.</p>
        </div>
    </div>

    <div class="row g-6 mb-6">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="filterForm" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Buscar</label>
                            <input type="text" class="form-control" id="keyword" name="keyword" placeholder="Código, título, cliente...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Estado</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Todos</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="en_revision">En Revisión</option>
                                <option value="aprobado">Aprobado</option>
                                <option value="rechazado">Rechazado</option>
                                <option value="asignado">Asignado</option>
                                <option value="trabajando">Trabajando</option>
                                <option value="completado">Completado</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Categoría</label>
                            <select class="form-select" id="category_id" name="category_id">
                                <option value="">Todas las categorías</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Líder de Área</label>
                            <select class="" id="lider_id" name="lider_id">
                                <option value="">Todos los Líderes</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Desde</label>
                            <input type="date" class="form-control" id="date_from" name="date_from">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Hasta</label>
                            <input type="date" class="form-control" id="date_to" name="date_to">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-6">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Resultados</h5>
                </div>
                <div class="table-responsive py-3">
                    <table class="table text-nowrap mb-0 table-centered table-hover" id="searchResultsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Trámite</th>
                                <th>Cliente</th>
                                <th>Título</th>
                                <th>Estado</th>
                                <th>Líder Asignado</th>
                                <th>Fecha de Ingreso</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script>
let searchTable;
let liderSelect;

document.addEventListener('DOMContentLoaded', async function() {
    await loadCategories();
    await initLiderSelect();
    initDataTable();
});

async function loadCategories() {
    try {
        const response = await fetch('<?= base_url('secretaria/document-search/get-categories') ?>');
        const data = await response.json();
        const select = document.getElementById('category_id');

        if (Array.isArray(data)) {
            data.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.id;
                option.text = cat.name;
                select.add(option);
            });
        }
    } catch (error) {
        console.error('Error cargando categorías:', error);
    }
}

async function initLiderSelect() {
    liderSelect = new TomSelect('#lider_id', {
        create: false,
        sortField: { field: 'text', direction: 'asc' },
        placeholder: 'Todos los Líderes',
        allowEmpty: true,
        onChange: function(value) {
            searchTable.ajax.url(buildUrl()).load();
        }
    });

    await loadLideres('');
}

async function loadLideres(categoryId) {
    try {
        const params = new URLSearchParams();
        if (categoryId) {
            params.append('category_id', categoryId);
        }

        const response = await fetch('<?= base_url('secretaria/document-search/get-lideres') ?>?' + params.toString());
        const data = await response.json();

        liderSelect.clearOptions();

        if (Array.isArray(data)) {
            data.forEach(lider => {
                const text = lider.category_name
                    ? `${lider.name} (${lider.category_name})`
                    : lider.name;
                liderSelect.addOption({ value: lider.id, text: text });
            });
        }
    } catch (error) {
        console.error('Error cargando líderes:', error);
    }
}

document.getElementById('category_id').addEventListener('change', async function() {
    const categoryId = this.value;
    await loadLideres(categoryId);
    searchTable.ajax.url(buildUrl()).load();
});

document.getElementById('keyword').addEventListener('input', debounce(function() {
    searchTable.ajax.url(buildUrl()).load();
}, 400));
document.getElementById('status').addEventListener('change', function() {
    searchTable.ajax.url(buildUrl()).load();
});
document.getElementById('lider_id').addEventListener('change', function() {
    searchTable.ajax.url(buildUrl()).load();
});
document.getElementById('date_from').addEventListener('change', function() {
    searchTable.ajax.url(buildUrl()).load();
});
document.getElementById('date_to').addEventListener('change', function() {
    searchTable.ajax.url(buildUrl()).load();
});

function buildUrl() {
    const keyword = document.getElementById('keyword').value;
    const status = document.getElementById('status').value;
    const categoryId = document.getElementById('category_id').value;
    const liderId = document.getElementById('lider_id').value;
    const dateFrom = document.getElementById('date_from').value;
    const dateTo = document.getElementById('date_to').value;

    const params = new URLSearchParams({
        keyword: keyword,
        status: status,
        lider_id: liderId,
        date_from: dateFrom,
        date_to: dateTo
    });

    return '<?= base_url('secretaria/document-search/data') ?>?' + params.toString();
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function initDataTable() {
    searchTable = new DataTable('#searchResultsTable', {
        processing: true,
        serverSide: true,
        ajax: {
            url: buildUrl(),
            type: 'GET',
            error: function(xhr, error, thrown) {
                console.error('DataTables error:', thrown);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al cargar los datos'
                });
            }
        },
        columns: [
            { data: 'document_code', name: 'd.document_code', render: function(data) {
                return `<span class="fw-semibold">${data || ''}</span>`;
            }},
            { data: 'client_full_name', name: 'client_full_name', render: function(data) {
                return data || 'N/A';
            }},
            { data: 'title', name: 'd.title', render: function(data) {
                return `<div class="text-truncate" style="max-width: 250px;" title="${escapeHtml(data || '')}">${escapeHtml(data || '')}</div>`;
            }},
            { data: 'status', name: 'd.status', render: function(data) {
                return getStatusBadge(data);
            }},
            { data: 'lider_name', name: 'lider_name', render: function(data) {
                return data || '<span class="text-muted">Sin asignar</span>';
            }},
            { data: 'created_at', name: 'd.created_at', render: function(data) {
                if (!data) return 'N/A';
                const date = new Date(data);
                return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
            }},
            { data: 'id', searchable: false, orderable: false, render: function(data, type, row) {
                let buttons = `<a href="<?= base_url('secretaria/document-search/view-flow/') ?>${data}" class="btn btn-outline-primary btn-sm" title="Ver Flujo">
                    <i class="fa-solid fa-eye"></i>
                </a>`;

                if (row.assignment_id) {
                    buttons += `<a href="<?= base_url('secretaria/documents/download/') ?>${data}" class="btn btn-outline-secondary btn-sm" title="Descargar" target="_blank">
                        <i class="fa-solid fa-download"></i>
                    </a>`;
                }

                return buttons;
            }}
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.3.7/i18n/es-ES.json',
        },
        scrollX: true,
        order: [[5, 'desc']],
        layout: {
            topStart: {
                buttons: [
                    'pageLength',
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fa-solid fa-file-excel"></i> Excel',
                        className: 'btn btn-success text-white'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fa-solid fa-file-pdf"></i> PDF',
                        className: 'btn btn-danger text-white'
                    }
                ]
            }
        }
    });
}

function getStatusBadge(status) {
    const classes = {
        'pendiente': 'bg-warning-subtle text-warning-emphasis',
        'en_revision': 'bg-info-subtle text-info-emphasis',
        'aprobado': 'bg-success-subtle text-success-emphasis',
        'rechazado': 'bg-danger-subtle text-danger-emphasis',
        'asignado': 'bg-primary-subtle text-primary-emphasis',
        'trabajando': 'bg-secondary-subtle text-secondary-emphasis',
        'completado': 'bg-success text-white'
    };
    const badgeClass = classes[status] || 'bg-secondary-subtle text-secondary-emphasis';
    const label = status ? status.charAt(0).toUpperCase() + status.slice(1).replace('_', ' ') : '';
    return `<span class="badge ${badgeClass}">${label}</span>`;
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<?= $this->endSection() ?>