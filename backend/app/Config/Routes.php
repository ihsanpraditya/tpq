<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->group('api', ['namespace' => 'App\Controllers\Api', 'filter' => 'cors'], static function (RouteCollection $routes): void {
    $routes->options('(.+)', static function (): void {});

    $routes->group('auth', static function (RouteCollection $routes): void {
        $routes->post('login', 'AuthController::login');
        $routes->post('logout', 'AuthController::logout');
        $routes->get('me', 'AuthController::me', ['filter' => 'jwtAuth']);
    });

    // Rute API lain yang butuh login akan ditambahkan di sini, contoh:
    // $routes->group('halaqah', ['filter' => 'jwtAuth'], static function (RouteCollection $routes): void {
    //     $routes->get('/', 'HalaqahController::index');
    // });
});
