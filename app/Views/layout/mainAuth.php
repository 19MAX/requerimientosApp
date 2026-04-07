<!DOCTYPE html>
<html lang="es">

<head>
    <?= $this->include('partials/head/head-meta') ?>
    <title> CNEL - <?= $this->renderSection('title') ?></title>
    <?= $this->include('partials/head/head-links') ?>
</head>

<body>
    <main class="d-flex flex-column justify-content-center vh-100">
    <?= $this->renderSection('content') ?>
    </main>
    <?= $this->include('partials/scripts') ?>
</body>

</html>