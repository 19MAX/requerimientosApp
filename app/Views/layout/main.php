<!DOCTYPE html>
<html lang="es">

<head>
    <?= $this->include('partials/head/head-meta') ?>
    <title> CNEL - <?= $this->renderSection('title') ?></title>
    <?= $this->include('partials/head/head-links') ?>
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

</body>

</html>