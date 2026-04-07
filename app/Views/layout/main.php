<!DOCTYPE html>
<html lang="es">

<head>
    <?= $this->include('partials/head/head-meta') ?>
    <title> CNEL - <?= $this->renderSection('title') ?></title>
    <?= $this->include('partials/head/head-links') ?>

<!-- DataTables Bootstrap 5 CSS -->
<link href="https://cdn.datatables.net/2.3.7/css/dataTables.bootstrap5.css" rel="stylesheet">

<!-- DataTables Buttons Bootstrap 5 CSS -->
<link href="https://cdn.datatables.net/buttons/3.2.6/css/buttons.bootstrap5.css" rel="stylesheet">

</head>

<body>
    <!-- Vertical Sidebar -->
    <div>
        <?= $this->include('partials/sidebar') ?>

        <!-- Main Content -->
        <div id="content" class="position-relative h-100">
            <?= $this->include('partials/topbar-second') ?>

            <?=$this->renderSection('content')?>

        </div>

    </div>
    <?= $this->include('partials/scripts') ?>

    <!-- jQuery (primero siempre) -->
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>


<!-- DataTables core -->
<script src="https://cdn.datatables.net/2.3.7/js/dataTables.js"></script>

<!-- DataTables Bootstrap 5 -->
<script src="https://cdn.datatables.net/2.3.7/js/dataTables.bootstrap5.js"></script>

<!-- Buttons extension -->
<script src="https://cdn.datatables.net/buttons/3.2.6/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.6/js/buttons.bootstrap5.js"></script>

<!-- Export dependencies -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<!-- Buttons functions -->
<script src="https://cdn.datatables.net/buttons/3.2.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.6/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.6/js/buttons.colVis.min.js"></script>
    <?=$this->renderSection('scripts')?>

</body>

</html>