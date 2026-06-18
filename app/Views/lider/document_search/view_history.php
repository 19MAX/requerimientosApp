<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="custom-container">
    <div class="row mb-6 align-items-center">
        <div class="col-auto">
            <a href="<?= base_url('lider/document-search') ?>" class="btn btn-light btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M15 18l-6 -6l6 -6" />
                </svg>
                Volver
            </a>
        </div>
        <div class="col">
            <h1 class="fs-3 mb-0">Historial de Cambios</h1>
            <p class="mb-0 text-muted">Trámite: <?= esc($document['document_code']) ?></p>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><?= esc($document['title']) ?></h5>
                    <button type="button" id="btnDescargarHistorial" class="btn btn-outline-danger btn-sm">
                        <i class="fa-solid fa-file-pdf"></i> Descargar Historial PDF
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0" id="historialTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Descripción</th>
                                    <th>Estado Anterior</th>
                                    <th>Estado Nuevo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($auditLogs)): ?>
                                    <?php foreach ($auditLogs as $log): ?>
                                    <tr>
                                        <td class="text-nowrap"><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></td>
                                        <td><?= esc($log['description'] ?? '-') ?></td>
                                        <td><span class="badge bg-secondary-subtle text-secondary-emphasis"><?= esc($log['old_status'] ?? '-') ?></span></td>
                                        <td><span class="badge bg-info-subtle text-info-emphasis"><?= esc($log['new_status'] ?? '-') ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No hay registros de cambios</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('btnDescargarHistorial').addEventListener('click', function() {
    const docCode = '<?= esc($document['document_code']) ?>';
    const docTitle = '<?= esc($document['title']) ?>';
    const createdAt = '<?= date('d/m/Y', strtotime($document['created_at'])) ?>';

    const tableBody = [];
    const rows = document.querySelectorAll('#historialTable tbody tr');
    if (rows.length === 0 || (rows.length === 1 && rows[0].querySelector('td[colspan]'))) {
        tableBody.push(['', 'No hay registros de cambios', '', '']);
    } else {
        rows.forEach(row => {
            const cols = row.querySelectorAll('td');
            if (cols.length >= 4) {
                tableBody.push([
                    cols[0].textContent.trim(),
                    cols[1].textContent.trim(),
                    cols[2].textContent.trim(),
                    cols[3].textContent.trim()
                ]);
            }
        });
    }

    const docDefinition = {
        pageSize: 'A4',
        pageMargins: [40, 60, 40, 60],
        content: [
            { text: 'Historial del Documento - ' + docCode, style: 'header' },
            { text: 'Título: ' + docTitle, style: 'subheader' },
            { text: 'Fecha de Ingreso: ' + createdAt, style: 'subheader' },
            { text: ' ', margin: [0, 10, 0, 0] },
            {
                table: {
                    headerRows: 1,
                    widths: ['auto', '*', 'auto', 'auto'],
                    body: [
                        [
                            { text: 'Fecha', style: 'tableHeader' },
                            { text: 'Descripción', style: 'tableHeader' },
                            { text: 'Estado Anterior', style: 'tableHeader' },
                            { text: 'Estado Nuevo', style: 'tableHeader' }
                        ],
                        ...tableBody
                    ]
                },
                layout: {
                    fillColor: function(rowIndex) {
                        return rowIndex === 0 ? '#f5f5f5' : null;
                    }
                }
            }
        ],
        styles: {
            header: {
                fontSize: 16,
                bold: true,
                margin: [0, 0, 0, 10]
            },
            subheader: {
                fontSize: 10,
                margin: [0, 2, 0, 2]
            },
            tableHeader: {
                bold: true,
                fontSize: 10,
                fillColor: '#f5f5f5'
            }
        },
        defaultStyle: {
            fontSize: 9
        }
    };

    pdfMake.createPdf(docDefinition).download('historial-' + docCode + '.pdf');
});
</script>
<?= $this->endSection() ?>