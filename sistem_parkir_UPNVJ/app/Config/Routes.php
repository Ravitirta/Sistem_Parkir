<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// =================================================================
// 1. RUTE AUTH (LOGIN/LOGOUT)
// =================================================================
$routes->get('/', 'Auth::index');
$routes->post('/auth/loginProcess', 'Auth::loginProcess');
$routes->get('/auth/logout', 'Auth::logout');

// =================================================================
// 2. RUTE DASHBOARD (PARKIR) - WAJIB LOGIN
// =================================================================
$routes->group('dashboard', ['filter' => 'auth'], function($routes) {
    
    // Dashboard Utama
    $routes->get('/', 'Dashboard::index');
    
    // Transaksi Masuk
    $routes->post('simpanMasuk', 'Dashboard::simpanMasuk');

    // Cek Status
    $routes->get('status', 'Status::index'); 

    // --- Fitur Update / Checkout ---
    $routes->get('update', 'Update::index'); 
    $routes->post('update/calculate/(:segment)', 'Update::calculate/$1'); 
    $routes->post('update/checkout/(:segment)', 'Update::checkout/$1'); 

    // --- Fitur Transaksi Keluar ---
    $routes->get('transaksiKeluar', 'TransaksiKeluar::index'); 
    $routes->post('transaksiKeluar/simpanKeluar', 'TransaksiKeluar::simpanKeluar');

    // >>> FITUR LAPORAN (REKAP KEUANGAN/PARKIR) <<<
    $routes->get('laporan', 'Laporan::index');          // Halaman filter
    $routes->post('laporan/cetak', 'Laporan::cetak');   // Cetak laporan

}); // Tutup Grup Dashboard


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