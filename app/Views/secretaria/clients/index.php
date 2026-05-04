<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="custom-container">
    <div class="row mb-6 align-items-center">
        <div class="col-xl-8 col-lg-6">
            <h1 class="fs-3 mb-0">Gestión de Clientes</h1>
            <p class="mb-0 text-muted">Administra los clientes del sistema.</p>
        </div>
        <div class="col-xl-4 col-lg-6 text-lg-end mt-3 mt-lg-0">
            <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-plus me-2">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M12 5l0 14"></path>
                    <path d="M5 12l14 0"></path>
                </svg>
                Nuevo Cliente
            </button>
        </div>
    </div>

    <div class="row g-6 mb-6">
        <div class="col-12">
            <div class="card card-lg">
                <div class="table-responsive py-3">
                    <table class="table text-nowrap table-centered table-hover" id="miTabla">
                        <thead class="table-light">
                            <tr>
                                <th>Cédula</th>
                                <th>Cliente</th>
                                <th>Correo</th>
                                <th>Teléfono</th>
                                <th>Dirección</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($clients)): ?>
                                <?php foreach ($clients as $client): ?>
                                    <tr>
                                        <td><?= esc($client['cedula']) ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="icon-shape icon-md rounded-circle bg-primary-subtle text-primary">
                                                    <?= strtoupper(substr($client['first_name'], 0, 2)) ?>
                                                </div>
                                                <span
                                                    class="fw-semibold text-inherit"><?= esc($client['first_name'] . ' ' . $client['last_name']) ?></span>
                                            </div>
                                        </td>
                                        <td><?= esc($client['email']) ?></td>
                                        <td><?= esc($client['phone'] ?? 'N/A') ?></td>
                                        <td>
                                            <?= esc($client['address'] ?? 'N/A') ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-white btn-sm"
                                                onclick="openEditModal(<?= htmlspecialchars(json_encode($client)) ?>)">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-pencil text-primary">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4"></path>
                                                    <path d="M13.5 6.5l4 4"></path>
                                                </svg>
                                            </button>
                                            <button type="button" class="btn btn-white btn-sm"
                                                onclick="openDeleteModal(<?= $client['id'] ?>, '<?= esc($client['first_name'] . ' ' . $client['last_name']) ?>')">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-trash text-danger">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M4 7l16 0"></path>
                                                    <path d="M10 11l0 6"></path>
                                                    <path d="M14 11l0 6"></path>
                                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!-- <tr>
                                    <td colspan="6" class="text-center py-4">No hay clientes registrados.</td>
                                </tr> -->
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createUserModalLabel">Registrar Nuevo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('secretaria/clients/create') ?>" method="POST">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombres <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="first_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="last_name" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Cédula <span class="text-danger">*</span></label>
                            <input type="text" class="form-control cedula-input" name="cedula" required maxlength="10">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Teléfono <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="phone" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Dirección</label>
                            <input type="text" class="form-control" name="address">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark">Guardar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Editar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('secretaria/clients/update') ?>" method="POST" id="editUserForm">
                <input type="hidden" name="id" id="edit_user_id">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombres</label>
                            <input type="text" class="form-control" name="first_name" id="edit_first_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Apellidos</label>
                            <input type="text" class="form-control" name="last_name" id="edit_last_name" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Cédula</label>
                            <input type="text" class="form-control cedula-input" name="cedula" id="edit_cedula" required maxlength="10">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" name="email" id="edit_email" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="phone" id="edit_phone" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Dirección</label>
                            <input type="text" class="form-control" name="address" id="edit_address">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
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
                <p class="text-muted mb-4">Vas a eliminar a <strong id="delete_user_name"></strong>. Esta acción no se
                    puede deshacer.</p>

                <form action="<?= base_url('secretaria/clients/delete') ?>" method="POST"
                    class="d-flex justify-content-center gap-2">
                    <input type="hidden" name="id" id="delete_user_id">
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
    new DataTable('#miTabla', {
        language: { url: 'https://cdn.datatables.net/plug-ins/2.3.7/i18n/es-ES.json' },
        scrollX: true,
        layout: {
            topStart: {
                buttons: ['copy', 'excel', 'pdf', 'colvis']
            },
        }
    });
    // Función para poblar el modal de edición
    function openEditModal(user) {
        document.getElementById('edit_user_id').value = user.id;
        document.getElementById('edit_cedula').value = user.cedula;
        document.getElementById('edit_first_name').value = user.first_name;
        document.getElementById('edit_last_name').value = user.last_name;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_phone').value = user.phone;
        document.getElementById('edit_address').value = user.address;

        var editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
        editModal.show();
    }

    // Función para poblar el modal de eliminación
    function openDeleteModal(id, name) {
        document.getElementById('delete_user_id').value = id;
        document.getElementById('delete_user_name').innerText = name;

        var deleteModal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
        deleteModal.show();
    }
</script>
<?= $this->endSection() ?>