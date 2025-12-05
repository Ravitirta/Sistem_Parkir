<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Rute Login (Bisa diakses siapa saja)
$routes->get('/', 'Auth::index');
$routes->post('/auth/loginProcess', 'Auth::loginProcess');
$routes->get('/auth/logout', 'Auth::logout');

// Rute Dashboard (Hanya bisa diakses jika sudah login)
// Kita grupkan agar rapi dan semua kena filter 'auth'
$routes->group('dashboard', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Dashboard::index');
    
    // routes transaks kendaraan masuk
    $routes->post('simpanMasuk', 'Dashboard::simpanMasuk');

    // Tambahan untuk Fitur Cek Status
    $routes->get('status', 'Status::index'); // Rute untuk menampilkan halaman Cek Status
    // <<< END: Tambahan untuk Fitur Cek Status
});
