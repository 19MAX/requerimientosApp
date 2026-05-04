<?php $role = session()->get('role_slug'); ?>
<ul class="navbar-nav flex-column">

    <li class="nav-item">
        <a class="nav-link"
            href="<?= base_url($role === 'lider_area' ? 'lider' : $role) ?>">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-layout-dashboard">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M5 5m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v7a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"></path>
                    <path d="M5 18m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"></path>
                    <path d="M15 5m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"></path>
                    <path d="M15 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"></path>
                </svg>
            </span>
            <span class="text">Panel Principal</span>
        </a>
    </li>

    <li class="nav-item">
        <div class="nav-heading">Módulos del Sistema</div>
        <hr class="mx-5 nav-line mb-1">
    </li>

    <?php if ($role === 'admin'): ?>
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('admin/users') ?>">
                <span class="nav-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-users">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"></path>
                    </svg>
                </span>
                <span class="text">Gestión de Usuarios</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('admin/clients') ?>">
                <span class="nav-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-users">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"></path>
                    </svg>
                </span>
                <span class="text">Gestión de Clientes</span>
            </a>
        </li>
        <!-- <li class="nav-item">
            <a class="nav-link" href="<?= base_url('admin/audit/documents') ?>">
                <span class="nav-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-history">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M12 8l0 4l2 2"></path>
                        <path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5"></path>
                    </svg>
                </span>
                <span class="text">Auditoría de Documentos</span>
            </a>
        </li> -->
        <!-- <li class="nav-item">
            <a class="nav-link" href="<?= base_url('admin/audit/assignments') ?>">
                <span class="nav-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-list-check">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M9 6l11 0"></path>
                        <path d="M9 12l11 0"></path>
                        <path d="M9 18l11 0"></path>
                        <path d="M5 6l0 .01"></path>
                        <path d="M5 12l0 .01"></path>
                        <path d="M5 18l0 .01"></path>
                    </svg>
                </span>
                <span class="text">Estados de Asignación</span>
            </a>
        </li> -->
    <?php endif; ?>

    <?php if ($role === 'secretaria'): ?>
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('secretaria/documents') ?>">
                <span class="nav-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-file-description">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                        <path d="M9 17h6"></path>
                        <path d="M9 13h6"></path>
                    </svg>
                </span>
                <span class="text">Gestión de Documentos</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('secretaria/clients') ?>">
                <span class="nav-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-users">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"></path>
                    </svg>
                </span>
                <span class="text">Gestión de Clientes</span>
            </a>
        </li>
    <?php endif; ?>

    <?php if ($role === 'director'): ?>
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('director/review-documents') ?>">
                <span class="nav-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-file-check">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                        <path d="M9 15l2 2l4 -4"></path>
                    </svg>
                </span>
                <span class="text">Revisar Documentos</span>
            </a>
        </li>
    <?php endif; ?>

    <?php if ($role === 'lider_area'): ?>
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('lider/my-assignments') ?>">
                <span class="nav-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-list-check">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M3.5 5.5l1.5 1.5l2.5 -2.5"></path>
                        <path d="M3.5 11.5l1.5 1.5l2.5 -2.5"></path>
                        <path d="M3.5 17.5l1.5 1.5l2.5 -2.5"></path>
                        <path d="M11 6l9 0"></path>
                        <path d="M11 12l9 0"></path>
                        <path d="M11 18l9 0"></path>
                    </svg>
                </span>
                <span class="text">Mis Actividades</span>
            </a>
        </li>
    <?php endif; ?>

    <li class="nav-item">
        <div class="nav-heading">Ajustes de Cuenta</div>
        <hr class="mx-5 nav-line mb-1">
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('profile') ?>">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-user">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                </svg>
            </span>
            <span class="text">Mi Perfil</span>
        </a>
    </li>
    <li class="nav-item mt-3">
        <a class="nav-link text-danger" href="<?= base_url('logout') ?>">
            <span class="nav-icon text-danger">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-logout">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"></path>
                    <path d="M9 12h12l-3 -3"></path>
                    <path d="M18 15l3 -3"></path>
                </svg>
            </span>
            <span class="text">Cerrar Sesión</span>
        </a>
    </li>
</ul>