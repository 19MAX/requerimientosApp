<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="custom-container">
    <div class="row mb-6 align-items-center">
        <div class="col-xl-8 col-lg-6">
            <h1 class="fs-3 mb-0">Búsqueda de Documentos</h1>
            <p class="mb-0 text-muted">Documentos asignados a ti.</p>
        </div>
    </div>

    <div class="row g-6 mb-6">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="filterForm" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Buscar</label>
                            <input type="text" class="form-control" id="keyword" name="keyword" placeholder="Código, título, cliente...">
                        </div>
                        <div class="col-md-3">
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
                    <table class="table text-nowrap mb-0  table-hover" id="searchResultsTable">
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

document.getElementById('keyword').addEventListener('input', debounce(function() {
    searchTable.ajax.url(buildUrl()).load();
}, 400));
document.getElementById('status').addEventListener('change', function() {
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
    const dateFrom = document.getElementById('date_from').value;
    const dateTo = document.getElementById('date_to').value;

    const params = new URLSearchParams({
        keyword: keyword,
        status: status,
        date_from: dateFrom,
        date_to: dateTo
    });

    return '<?= base_url('lider/document-search/data') ?>?' + params.toString();
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

document.addEventListener('DOMContentLoaded', function() {
    initDataTable();
});

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
            { data: 'created_at', name: 'd.created_at', render: function(data) {
                if (!data) return 'N/A';
                const date = new Date(data);
                return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
            }},
            { data: 'id', searchable: false, orderable: false, render: function(data) {
                return `<a href="<?= base_url('lider/document-search/view-history') ?>/${data}" class="btn btn-outline-primary btn-sm" title="Ver historial de cambios">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </a>`;
            }}
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.3.7/i18n/es-ES.json',
        },
        scrollX: true,
        order: [[4, 'desc']],
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