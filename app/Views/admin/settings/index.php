<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="custom-container">

    <div class="row mb-6 align-items-center">
        <div class="col-xl-8 col-lg-6">
            <h1 class="fs-3 mb-0">Configuración del sitio</h1>
            <p class="mb-0 text-muted">Administra la configuración del sitio.</p>
        </div>
    </div>
    <div class="row g-6 mb-6">
        <div class="col-12 mx-auto">
            <div class="card card-lg h-100">
                <div class="card-header border-bottom-0 pb-0">
                    <h5 class="mb-0">Datos Generales</h5>
                </div>
                <div class="card-body">

                    <form id="settingsForm" action="<?= base_url('admin/settings/update') ?>" method="POST"
                        enctype="multipart/form-data">


                        <div class="mb-4">
                            <label for="site_name" class="form-label">Nombre del Sitio <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="site_name" name="site_name"
                                value="<?= set_value('site_name', $settings['site_name'] ?? '') ?>" required>
                            <div class="form-text">Este nombre aparecerá en la pestaña del navegador, correos y
                                facturas.</div>
                        </div>

                        <div class="mb-5">
                            <label for="site_logo" class="form-label">Logo del Sitio</label>
                            <div class="d-flex align-items-center gap-4">

                                <div class="bg-light border rounded p-2 text-center"
                                    style="width: 120px; height: 120px; display: flex; align-items: center; justify-content: center;">
                                    <?php if (!empty($settings['site_logo'])): ?>
                                        <img src="<?= base_url('uploads/logos/' . esc($settings['site_logo'])) ?>"
                                            alt="Logo actual" class="img-fluid" style="max-height: 100px;">
                                    <?php else: ?>
                                        <span class="text-muted small">Sin logo</span>
                                    <?php endif; ?>
                                </div>

                                <div class="flex-grow-1">
                                    <input class="form-control" type="file" id="site_logo" name="site_logo"
                                        accept="image/png, image/jpeg, image/svg+xml, image/webp">
                                    <div class="form-text mt-2">
                                        Recomendado: Formato PNG transparente o SVG. Tamaño máximo: 2MB.<br>
                                        <span class="text-info small"><i class="ti ti-info-circle"></i> Deja este campo
                                            vacío si no deseas cambiar el logo actual.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end border-top pt-4">
                            <button type="submit" class="btn btn-primary">Guardar Configuración</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>

    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById('settingsForm');
        const fileInput = document.getElementById('site_logo');

        form.addEventListener('submit', function (event) {
            // Verificamos si el usuario seleccionó un archivo
            if (fileInput.files.length > 0) {
                const fileSize = fileInput.files[0].size; // Tamaño en bytes
                const maxSize = 2 * 1024 * 1024; // 2 Megabytes en bytes

                if (fileSize > maxSize) {
                    // Detenemos el envío del formulario
                    event.preventDefault();

                    // Mostramos el error amigable con SweetAlert2
                    Swal.fire({
                        icon: 'warning',
                        title: 'Archivo muy pesado',
                        text: 'El logo no puede superar los 2MB. Por favor, elige una imagen más ligera.',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#d33'
                    });

                    // Limpiamos el input para que seleccione otro
                    fileInput.value = '';
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>