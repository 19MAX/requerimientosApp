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
                    <form id="searchForm" class="row g-3">
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
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100" id="btnSearch">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
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
                    <span class="badge bg-primary" id="resultCount">0 documentos</span>
                </div>
                <div class="table-responsive">
                    <table class="table text-nowrap mb-0 table-centered table-hover" id="searchResultsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Trámite</th>
                                <th>Cliente</th>
                                <th>Título</th>
                                <th>Estado</th>
                                <th>Estado Asignación</th>
                                <th>Fecha de Ingreso</th>
                            </tr>
                        </thead>
                        <tbody id="searchResultsBody">
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="fa-solid fa-magnifying-glass fa-2x mb-3 d-block"></i>
                                    Ingresa criterios de búsqueda y presiona buscar
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script>
document.getElementById('searchForm').addEventListener('submit', function(e) {
    e.preventDefault();
    performSearch();
});

function performSearch() {
    const keyword = document.getElementById('keyword').value;
    const status = document.getElementById('status').value;
    const dateFrom = document.getElementById('date_from').value;
    const dateTo = document.getElementById('date_to').value;

    const btnSearch = document.getElementById('btnSearch');
    btnSearch.disabled = true;
    btnSearch.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    const params = new URLSearchParams({
        keyword: keyword,
        status: status,
        date_from: dateFrom,
        date_to: dateTo
    });

    fetch('<?= base_url('lider/document-search/search') ?>?' + params.toString())
        .then(response => response.json())
        .then(data => {
            renderResults(data.data);
            document.getElementById('resultCount').textContent = data.count + ' documento' + (data.count !== 1 ? 's' : '');
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al realizar la búsqueda.'
            });
        })
        .finally(() => {
            btnSearch.disabled = false;
            btnSearch.innerHTML = '<i class="fa-solid fa-magnifying-glass"></i>';
        });
}

function renderResults(documents) {
    const tbody = document.getElementById('searchResultsBody');

    if (documents.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-muted py-5">
                    <i class="fa-solid fa-inbox fa-2x mb-3 d-block"></i>
                    No se encontraron documentos con los criterios especificados
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = documents.map(doc => `
        <tr>
            <td><span class="fw-semibold">${escapeHtml(doc.document_code)}</span></td>
            <td>${escapeHtml(doc.client_full_name || 'N/A')}</td>
            <td>
                <div class="text-truncate" style="max-width: 250px;" title="${escapeHtml(doc.title)}">
                    ${escapeHtml(doc.title)}
                </div>
            </td>
            <td>${getStatusBadge(doc.status)}</td>
            <td>${getAssignmentBadge(doc.assignment_status)}</td>
            <td>${formatDate(doc.created_at)}</td>
        </tr>
    `).join('');
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
    const label = status.charAt(0).toUpperCase() + status.slice(1).replace('_', ' ');
    return `<span class="badge ${badgeClass}">${label}</span>`;
}

function getAssignmentBadge(status) {
    if (!status) {
        return '<span class="text-muted">Sin asignación</span>';
    }
    const classes = {
        'pendiente': 'bg-warning-subtle text-warning-emphasis',
        'en_progreso': 'bg-info-subtle text-info-emphasis',
        'completada': 'bg-success-subtle text-success-emphasis'
    };
    const badgeClass = classes[status] || 'bg-secondary-subtle text-secondary-emphasis';
    const label = status.charAt(0).toUpperCase() + status.slice(1).replace('_', ' ');
    return `<span class="badge ${badgeClass}">${label}</span>`;
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', { year: 'numeric', month: '2-digit', day: '2-digit' });
}
</script>

<?= $this->endSection() ?>
