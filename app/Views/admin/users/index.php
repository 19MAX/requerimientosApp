<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="custom-container">
    <div class="row mb-6 align-items-center">
        <div class="col-xl-8 col-lg-6">
            <h1 class="fs-3 mb-0">Gestión de Usuarios</h1>
            <p class="mb-0 text-muted">Administra los accesos, roles y estados de los usuarios del sistema.</p>
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
                Nuevo Usuario
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
                                <th>Nombre</th>
                                <th>Correo Electrónico</th>
                                <th>Teléfono</th>
                                <th>Rol</th>
                                <th>Categoría</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="icon-shape icon-md rounded-circle bg-primary-subtle text-primary">
                                                    <?= strtoupper(substr($user['name'], 0, 2)) ?>
                                                </div>
                                                <span class="fw-semibold text-inherit"><?= esc($user['name']) ?></span>
                                            </div>
                                        </td>
                                        <td><?= esc($user['email']) ?></td>
                                        <td><?= esc($user['phone'] ?? 'N/A') ?></td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                                <?= esc($user['role_name'] ?? 'Desconocido') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($user['category_name'])): ?>
                                                <span class="badge bg-info-subtle text-info-emphasis">
                                                    <?= esc($user['category_name']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($user['is_active']): ?>
                                                <span class="badge text-success-emphasis bg-success-subtle">Activo</span>
                                            <?php else: ?>
                                                <span class="badge text-danger-emphasis bg-danger-subtle">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-white btn-sm"
                                                onclick="openEditModal(<?= htmlspecialchars(json_encode($user)) ?>)">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-pencil text-primary">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4"></path>
                                                    <path d="M13.5 6.5l4 4"></path>
                                                </svg>
                                            </button>
                                            <!-- <button type="button" class="btn btn-white btn-sm"
                                                onclick="openDeleteModal(<?= $user['id'] ?>, '<?= esc($user['name']) ?>')">
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
                                            </button> -->
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!-- <tr>
                                    <td colspan="6" class="text-center py-4">No hay usuarios registrados.</td>
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
                <h5 class="modal-title" id="createUserModalLabel">Registrar Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/users/create') ?>" method="POST">
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control phone-input" name="phone">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Rol del Sistema <span class="text-danger">*</span></label>
                            <select class="form-select" name="role_id" id="create_role_id" required onchange="toggleCategoryField(this.value, 'create')">
                                <option value="">Seleccione un rol...</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>"><?= esc($role['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row" id="create_category_row" style="display: none;">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Categoría del Líder</label>
                            <select class="form-select" name="leader_category_id" id="create_leader_category_id">
                                <option value="">Seleccione una categoría...</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>"><?= esc($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Editar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/users/update') ?>" method="POST" id="editUserForm">
                <input type="hidden" name="id" id="edit_user_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" name="email" id="edit_email" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control phone-input" name="phone" id="edit_phone">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Rol del Sistema</label>
                            <select class="form-select" name="role_id" id="edit_role_id" required onchange="toggleCategoryField(this.value, 'edit')">
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>"><?= esc($role['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row" id="edit_category_row" style="display: none;">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Categoría del Líder</label>
                            <select class="form-select" name="leader_category_id" id="edit_leader_category_id">
                                <option value="">Seleccione una categoría...</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>"><?= esc($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado de la cuenta</label>
                        <select class="form-select" name="is_active" id="edit_is_active">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="form-label text-muted">Nueva Contraseña (Dejar en blanco para no cambiar)</label>
                        <input type="password" class="form-control" name="password" placeholder="***">
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

<!-- <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
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

                <form action="<?= base_url('admin/users/delete') ?>" method="POST"
                    class="d-flex justify-content-center gap-2">
                    <input type="hidden" name="id" id="delete_user_id">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Sí, eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div> -->

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    new DataTable('#miTabla', {
        language: { url: 'https://cdn.datatables.net/plug-ins/2.3.7/i18n/es-ES.json' },
        scrollX: true,
        layout: {
            topStart: {
                buttons: ['pageLength', 'excel', 'pdf', 'colvis']
            }
        }
    });

    function toggleCategoryField(roleId, prefix) {
        const categoryRow = document.getElementById(prefix + '_category_row');
        const categorySelect = document.getElementById(prefix + '_leader_category_id');
        const liderAreaRoleId = <?= $liderAreaRoleId ?? 0 ?>;

        if (parseInt(roleId) === liderAreaRoleId) {
            categoryRow.style.display = 'flex';
        } else {
            categoryRow.style.display = 'none';
            categorySelect.value = '';
        }
    }

    function openEditModal(user) {
        document.getElementById('edit_user_id').value = user.id;
        document.getElementById('edit_name').value = user.name;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_phone').value = user.phone;
        document.getElementById('edit_role_id').value = user.role_id;
        document.getElementById('edit_is_active').value = user.is_active;
        document.getElementById('edit_leader_category_id').value = user.leader_category_id || '';

        toggleCategoryField(user.role_id, 'edit');

        var editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
        editModal.show();
    }

    // function openDeleteModal(id, name) {
    //     document.getElementById('delete_user_id').value = id;
    //     document.getElementById('delete_user_name').innerText = name;

    //     var deleteModal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
    //     deleteModal.show();
    // }
</script>
<?= $this->endSection() ?>