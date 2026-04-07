
  <link rel="stylesheet" href="<?=base_url('dist')?>/assets/libs/swiper/swiper-bundle.min.css" />
<!-- Favicon -->
<link rel="icon" href="<?= base_url('uploads/logos/' . esc($global_settings['site_logo'])) ?>" type="image/png">

<meta name="msapplication-TileColor" content="#ffffff" />
<meta name="msapplication-TileImage" content="<?=base_url('dist')?>/assets/images/favicon/ms-icon-144x144.png" />
<meta name="theme-color" content="#ffffff" />
<!-- Color modes -->
<script src="<?=base_url('dist')?>/assets/js/vendors/color-modes.js"></script>
<script>
  if (localStorage.getItem('sidebarExpanded') === 'false') {
    document.documentElement.classList.add('collapsed');
    document.documentElement.classList.remove('expanded');
  } else {
    document.documentElement.classList.remove('collapsed');
    document.documentElement.classList.add('expanded');
  }
</script>
<!-- Libs CSS -->
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800&display=swap" />
<link rel="stylesheet" href="<?=base_url('dist')?>/assets/libs/simplebar/dist/simplebar.min.css" />
<link rel="stylesheet" href="<?=base_url('dist')?>/assets/libs/@tabler/icons-webfont/tabler-icons.min.css" />

<!-- Theme CSS -->
<link rel="stylesheet" href="<?=base_url('dist')?>/assets/css/theme.min.css">

<!-- FONTSAWESOME -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- TOM SELECT -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.5.2/dist/css/tom-select.css" rel="stylesheet">

<?= $this->include('partials/header') ?>