<?php $role = session()->get('role_slug'); ?>

<div id="miniSidebar" style="height: 100vh; overflow-y: auto;">
    <div class="brand-logo">
        <a class="d-none d-md-flex align-items-center gap-2" href="<?= base_url($role) ?>">

            <?php if (!empty($global_settings['site_logo'])): ?>
                <img src="<?= base_url('uploads/logos/' . esc($global_settings['site_logo'])) ?>" class="img-fluid"
                    alt="Logo" style="max-height: 70px; width: auto;">
            <?php else: ?>
                <img src="<?= base_url('dist/assets/images/brand/logo/logo-icon.svg') ?>" class="img-fluid" alt="Logo"
                    style="max-height: 40px; width: auto;">
            <?php endif; ?>

            <!-- <span class="fw-bold fs-4 site-logo-text">
                <?= esc($global_settings['site_name'] ?? 'CNEL') ?>
            </span> -->

        </a>
    </div>

    <?= view('partials/menu_links') ?>
</div>


<div class="offcanvasNav offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample"
    aria-labelledby="offcanvasExampleLabel">

    <div class="offcanvas-header">
        <a class="d-flex align-items-center gap-2" href="<?= base_url($role) ?>">

            <?php if (!empty($global_settings['site_logo'])): ?>
                <img src="<?= base_url('uploads/logos/' . esc($global_settings['site_logo'])) ?>" class="img-fluid"
                    alt="Logo" style="max-height: 40px; width: auto;">
            <?php else: ?>
                <img src="<?= base_url('dist/assets/images/brand/logo/logo-icon.svg') ?>" class="img-fluid" alt="Logo"
                    style="max-height: 40px; width: auto;">
            <?php endif; ?>

            <span class="fw-bold fs-4 site-logo-text">
                <?= esc($global_settings['site_name'] ?? 'CNEL') ?>
            </span>

        </a>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-0">
        <?= view('partials/menu_links') ?>
    </div>
</div>