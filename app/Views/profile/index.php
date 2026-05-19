<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="custom-container">
    <div class="row mb-6">
        <div class="col-12">
            <div class="bg-gradient-mixed p-6 rounded-3 d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="fs-3 mb-1">Mi Perfil</h1>
                    <p class="mb-0">Actualiza tu información personal y gestiona tu seguridad.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-6 mb-6">
        <div class="col-xl-7 col-lg-12">
            <div class="card card-lg h-100">
                <div class="card-header border-bottom-0 pb-0">
                    <h5 class="mb-0">Información Básica</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('profile/update-info') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= esc($user['id'] ?? '') ?>">

                        <div class="mb-4">
                            <label for="name" class="form-label">Nombre Completo <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="<?= set_value('name', $user['name'] ?? '') ?>" required>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label">Correo Electrónico <span
                                    class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?= set_value('email', $user['email'] ?? '') ?>" required>
                        </div>

                        <div class="mb-4">
                            <label for="phone" class="form-label">Teléfono</label>
                            <input type="text" class="form-control phone-input" id="phone" name="phone"
                                value="<?= set_value('phone', $user['phone'] ?? '') ?>">
                        </div>

                        <div class="d-flex justify-content-end mt-5">
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-5 col-lg-12">
            <div class="card card-lg h-100">
                <div class="card-header border-bottom-0 pb-0">
                    <h5 class="mb-0">Cambiar Contraseña</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info bg-info-subtle text-info-emphasis border-0 small mb-4">
                        Si no deseas cambiar tu contraseña, deja estos campos en blanco.
                    </div>

                    <form action="<?= base_url('profile/update-password') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= esc($user['id'] ?? '') ?>">

                        <div class="mb-4">
                            <label for="current_password" class="form-label">Contraseña Actual</label>
                            <input type="password" class="form-control" id="current_password" name="current_password"
                                placeholder="••••••••">
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="••••••••">
                        </div>

                        <div class="mb-4">
                            <label for="password_confirm" class="form-label">Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm"
                                placeholder="••••••••">
                        </div>

                        <div class="d-flex justify-content-end mt-5">
                            <button type="submit" class="btn btn-dark">Actualizar Contraseña</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>


<?= $this->endSection() ?>