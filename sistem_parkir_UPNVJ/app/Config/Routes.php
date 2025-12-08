<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// 1. RUTE PUBLIC (Bisa diakses SIAPA SAJA tanpa Login)
// =================================================================

// Halaman awal langsung ke Cek Status (Default Orang Umum)
$routes->get('/', 'Status::index'); 

// Rute Auth (Login & Logout)
$routes->get('/auth', 'Auth::index'); 
$routes->post('/auth/loginProcess', 'Auth::loginProcess');
$routes->get('/auth/logout', 'Auth::logout');

// Rute Fitur Cek Status (Dibuka untuk Umum)
$routes->get('dashboard/status', 'Status::index');

// 2. RUTE PROTECTED (HANYA PETUGAS LOGIN)
// =================================================================
$routes->group('dashboard', ['filter' => 'auth'], function($routes) {
    
    // Dashboard Utama
    $routes->get('/', 'Dashboard::index');
    
    // Fitur Transaksi Masuk
    $routes->post('simpanMasuk', 'Dashboard::simpanMasuk');

    // Fitur Cek Status (Versi Admin)
    $routes->get('status', 'Status::index'); 

    // Fitur Update / Checkout
    $routes->get('update', 'Update::index'); 
    $routes->post('update/calculate/(:segment)', 'Update::calculate/$1'); 
    $routes->post('update/checkout/(:segment)', 'Update::checkout/$1'); 

    // Fitur Transaksi Keluar
    $routes->get('transaksiKeluar', 'TransaksiKeluar::index'); 
    $routes->post('transaksiKeluar/simpanKeluar', 'TransaksiKeluar::simpanKeluar');

    // Fitur Laporan & History
    $routes->get('laporan', 'Laporan::index');   
    $routes->get('history', 'Laporan::history'); 
    $routes->post('laporan/cetak', 'Laporan::cetak');

});


// =================================================================
// 3. FITUR PELANGGARAN (PUBLIC & PRIVATE)
// =================================================================

// A. Rute Public (Bisa diakses Orang Umum tanpa Login)
$routes->get('/pelanggaran', 'Pelanggaran::index');           
$routes->get('/pelanggaran/lapor', 'Pelanggaran::lapor');     
$routes->post('/pelanggaran/simpanLaporan', 'Pelanggaran::simpanLaporan'); 

// B. Rute Admin/Petugas (Hanya bisa diakses jika sudah Login)
$routes->group('pelanggaran', ['filter' => 'auth'], function($routes) {
    $routes->get('manage', 'Pelanggaran::manage'); 
    $routes->get('verifikasi/(:num)/(:segment)', 'Pelanggaran::verifikasi/$1/$2');
    $routes->get('hapus/(:num)', 'Pelanggaran::hapus/$1');
});
