<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');
$routes->get('logout', 'LoginController::logout');
$routes->post('login', 'LoginController::login');

//AUTHORIZATION
$routes->group('', ['filter' => 'roleAdminFilter'], function ($routes) {
    $routes->get('perfil', 'PerfilController::index');
    $routes->put('perfil', 'PerfilController::updatePerfil');
    $routes->put('updatePassword', 'PerfilController::updatePassword');
    $routes->get('dashboard', 'AdminController::index');

    $routes->get('admin/empresa', 'ConfigController::index');
    $routes->put('admin/empresa/(:num)', 'ConfigController::update/$1');

    $routes->get('admin/cajas/cierre', 'CajaController::cierre');
    $routes->post('admin/cajas/cerrar', 'CajaController::cerrar');

    $routes->get('generar-reporte-excel', 'MovimientoController::generarReporteExcel');
    $routes->get('generar-reporte-pdf', 'MovimientoController::generarReportePdf');

    $routes->resource('admin/movimientos', ['controller' => 'MovimientoController']);
    $routes->resource('admin/comprobantes', ['controller' => 'ComprobanteController']);
    $routes->resource('admin/cajas', ['controller' => 'CajaController']);
    $routes->resource('admin/usuarios', ['controller' => 'UsuarioController']);
});
