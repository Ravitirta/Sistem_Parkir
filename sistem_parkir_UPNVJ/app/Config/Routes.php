<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// 1. RUTE PUBLIC (Bisa diakses SIAPA SAJA tanpa Login)
// Halaman awal langsung ke Cek Status (Default Orang Umum)
$routes->get('/', 'Status::index'); 

// Rute Login Proses (Hanya prosesnya, formnya nanti via PopUp)
$routes->post('/auth/loginProcess', 'Auth::loginProcess');
$routes->get('/auth/logout', 'Auth::logout');

// Rute Fitur Cek Status (Dibuka untuk Umum)
$routes->get('dashboard/status', 'Status::index');

// Rute Pelanggaran (Sisi Publik)
$routes->get('/pelanggaran', 'Pelanggaran::index');
$routes->get('/pelanggaran/lapor', 'Pelanggaran::lapor');
$routes->post('/pelanggaran/simpanLaporan', 'Pelanggaran::simpanLaporan');


// 2. RUTE PROTECTED (HANYA PETUGAS LOGIN)
$routes->group('dashboard', ['filter' => 'auth'], function($routes) {
    // Dashboard Petugas
    $routes->get('/', 'Dashboard::index'); 
    
    // Transaksi
    $routes->post('simpanMasuk', 'Dashboard::simpanMasuk');
    $routes->get('update', 'Update::index'); 
    $routes->post('update/calculate/(:segment)', 'Update::calculate/$1'); 
    $routes->post('update/checkout/(:segment)', 'Update::checkout/$1'); 
    $routes->get('transaksiKeluar', 'TransaksiKeluar::index'); 
    $routes->post('transaksiKeluar/simpanKeluar', 'TransaksiKeluar::simpanKeluar');

    // Laporan
    $routes->get('laporan', 'Laporan::index');   
    $routes->get('history', 'Laporan::history'); 
});

// Rute Pelanggaran (Sisi Admin)
$routes->group('pelanggaran', ['filter' => 'auth'], function($routes) {
    $routes->get('manage', 'Pelanggaran::manage'); 
    $routes->get('verifikasi/(:num)/(:segment)', 'Pelanggaran::verifikasi/$1/$2');
    $routes->get('hapus/(:num)', 'Pelanggaran::hapus/$1');
});
