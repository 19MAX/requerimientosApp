<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::login');
$routes->get('consulta-requerimientos', 'PublicConsultController::index');

//Rutas de autenticación
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::loginProcess');
$routes->get('logout', 'Auth::logout');

// Rutas globales para cualquier usuario autenticado
$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('profile', 'ProfileController::index');
    $routes->post('profile/update-info', 'ProfileController::updateInfo');
    $routes->post('profile/update-password', 'ProfileController::updatePassword');
});


//Rutas de ADMINISTRADOR
$routes->group('admin',['filter' => ['auth', 'role:admin']], function ($routes) {
    $routes->get('/', 'Admin\DashboardController::index');
    $routes->get('users', 'Admin\UsersController::index');
    $routes->post('users/create', 'Admin\UsersController::create');
    $routes->post('users/update', 'Admin\UsersController::update');
    $routes->post('users/delete', 'Admin\UsersController::delete');

    $routes->get('leader-categories', 'Admin\LeaderCategoriesController::index');
    $routes->post('leader-categories/create', 'Admin\LeaderCategoriesController::create');
    $routes->post('leader-categories/update', 'Admin\LeaderCategoriesController::update');
    $routes->post('leader-categories/delete', 'Admin\LeaderCategoriesController::delete');

    $routes->group('clients', function ($routes) {
        $routes->get('/', 'Admin\ClientsController::index');
        $routes->post('create', 'Admin\ClientsController::create');
        $routes->post('update', 'Admin\ClientsController::update');
        $routes->post('delete', 'Admin\ClientsController::delete');
    });
    // $routes->get('audit', 'Admin\AuditController::index');
    // $routes->get('audit/assignments', 'Admin\AuditController::assignmentStatus');
    // $routes->get('audit/documents', 'Admin\AuditController::documentChanges');

    $routes->get('settings', 'Admin\SettingsController::index');
    $routes->post('settings/update', 'Admin\SettingsController::update');
});

// Rutas de SECRETARÍA
$routes->group('secretaria', ['filter' => ['auth', 'role:secretaria']], function ($routes) {
    $routes->get('/', 'Secretaria\DashboardController::index');

    $routes->group('clients', function ($routes) {
        $routes->get('/', 'Secretaria\ClientsController::index');
        $routes->post('create', 'Secretaria\ClientsController::create');
        $routes->post('update', 'Secretaria\ClientsController::update');
        $routes->post('delete', 'Secretaria\ClientsController::delete');
    });

    $routes->get('documents', 'Secretaria\DocumentsController::index');
    $routes->post('documents/create', 'Secretaria\DocumentsController::create');
    $routes->post('documents/update', 'Secretaria\DocumentsController::update');
    $routes->post('documents/delete', 'Secretaria\DocumentsController::delete');

    // Ruta para descargar el archivo (la usaremos en la vista)
    $routes->get('documents/download/(:num)', 'Secretaria\DocumentsController::download/$1');

    $routes->get('clients/search', 'Secretaria\DocumentsController::search');
    $routes->post('clients/store', 'Secretaria\DocumentsController::store');

    $routes->get('document-search', 'Secretaria\DocumentSearchController::index');
    $routes->get('document-search/search', 'Secretaria\DocumentSearchController::search');
});

// Rutas de DIRECTOR
$routes->group('director', ['filter' => ['auth', 'role:director']], function ($routes) {
    $routes->get('/', 'Director\DashboardController::index');
    $routes->get('review-documents', 'Director\DocumentController::index');
    // Procesamiento de formulario
    $routes->post('review-documents/handle', 'Director\DocumentController::handleReview');
    $routes->post('review-documents/reassign', 'Director\DocumentController::reassign');
    //Busqueda de líderes de área para asignar tareas
    $routes->get('leaders/search', 'Director\DocumentController::searchLeaders');

});

// Rutas de LÍDER DE ÁREA
$routes->group('lider', ['filter' => ['auth', 'role:lider_area']], function ($routes) {
    $routes->get('/', 'Lider\DashboardController::index');
    // Vista principal de tareas
    $routes->get('my-assignments', 'Lider\MyAssignmentsController::index');

    // Procesamiento de acciones
    $routes->post('my-assignments/start', 'Lider\MyAssignmentsController::startTask');
    $routes->post('my-assignments/report', 'Lider\MyAssignmentsController::reportTask');

    $routes->get('document-search', 'Lider\DocumentSearchController::index');
    $routes->get('document-search/search', 'Lider\DocumentSearchController::search');
});
