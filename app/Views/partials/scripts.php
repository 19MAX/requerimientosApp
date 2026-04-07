<script src="<?= base_url('dist') ?>/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('dist') ?>/assets/libs/simplebar/dist/simplebar.min.js"></script>

<!-- Theme JS -->
<script src="<?= base_url('dist') ?>/assets/js/theme.min.js"></script>



<!-- jsvectormap -->
<script src="<?= base_url('dist') ?>/assets/js/vendors/sidebarnav.js"></script>
<script src="<?= base_url('dist') ?>/assets/libs/jsvectormap/dist/js/jsvectormap.min.js"></script>
<script src="<?= base_url('dist') ?>/assets/libs/jsvectormap/dist/maps/world.js"></script>
<script src="<?= base_url('dist') ?>/assets/libs/jsvectormap/dist/maps/world-merc.js"></script>
<script src="<?= base_url('dist') ?>/assets/libs/apexcharts/dist/apexcharts.min.js"></script>
<script src="<?= base_url('dist') ?>/assets/js/vendors/chart.js"></script>
<script src="<?= base_url('dist') ?>/assets/libs/choices.js/public/assets/scripts/choices.min.js"></script>
<script src="<?= base_url('dist') ?>/assets/js/vendors/choice.js"></script>
<script src="<?= base_url('dist') ?>/assets/libs/swiper/swiper-bundle.min.js"></script>
<script src="<?= base_url('dist') ?>/assets/js/vendors/swiper.js"></script>

<!-- TipyyJs DEV-->
 <script src="https://unpkg.com/@popperjs/core@2/dist/umd/popper.min.js"></script>
<script src="https://unpkg.com/tippy.js@6/dist/tippy-bundle.umd.js"></script>

<!-- TOM SELECT JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.5.2/dist/js/tom-select.complete.min.js"></script>

<script src="<?=base_url('dist')?>/assets/personalized/tippy.tooltips.js"></script>

<!-- Sweetalert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php
$success = session()->getFlashdata('success');
$error = session()->getFlashdata('error');
?>

<?php if ($success || $error): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            <?php if ($success): ?>
                <?php
                $text = is_array($success) ? $success['text'] : $success;
                $position = is_array($success) && isset($success['position']) ? $success['position'] : 'center';
                // Si la posición es esquina (top-end), se ve mejor como Toast
                $isToast = str_contains($position, 'end') || str_contains($position, 'start') ? 'true' : 'false';
                ?>
                Swal.fire({
                    toast: <?= $isToast ?>,
                    position: '<?= $position ?>',
                    icon: 'success',
                    title: '¡Éxito!',
                    html: '<?= $text ?>', // Usamos html en lugar de text para que lea los <br>
                    showConfirmButton: <?= $isToast === 'true' ? 'false' : 'true' ?>,
                    timer: 3000,
                    timerProgressBar: true
                });
            <?php endif; ?>

            <?php if ($error): ?>
                <?php
                $text = is_array($error) ? $error['text'] : $error;
                $position = is_array($error) && isset($error['position']) ? $error['position'] : 'center';
                $isToast = str_contains($position, 'end') || str_contains($position, 'start') ? 'true' : 'false';
                ?>
                Swal.fire({
                    toast: <?= $isToast ?>,
                    position: '<?= $position ?>',
                    icon: 'error',
                    title: '¡Ocurrió un problema!',
                    html: '<?= $text ?>',
                    showConfirmButton: true,
                    timer: <?= $isToast === 'true' ? '4000' : 'undefined' ?>,
                    timerProgressBar: <?= $isToast ?>
                });
            <?php endif; ?>

        });
    </script>
<?php endif; ?>