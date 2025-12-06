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

    // routes Cek Status
    $routes->get('status', 'Status::index'); 

    // >>> START: Tambahan untuk Fitur Update/Checkout
    $routes->get('update', 'Update::index'); // Tampilan list/pencarian
    // rute ini seharusnya POST
    $routes->post('update/calculate/(:segment)', 'Update::calculate/$1'); // Hitung bayar
    $routes->post('update/checkout/(:segment)', 'Update::checkout/$1'); // Update status selesai
    // <<< END: Tambahan untuk Fitur Update/Checkout

    // >>> Fitur Transaksi Keluar
    $routes->get('transaksiKeluar', 'TransaksiKeluar::index'); 
    $routes->post('transaksiKeluar/simpanKeluar', 'TransaksiKeluar::simpanKeluar');
    // <<< END: Fitur Transaksi Keluar
});

// 3. FITUR PELANGGARAN (PUBLIC & PRIVATE)
// --------------------------------------------------------------------

// A. Rute Public (Bisa diakses Orang Umum tanpa Login)
$routes->get('/pelanggaran', 'Pelanggaran::index');           // Lihat daftar valid
$routes->get('/pelanggaran/lapor', 'Pelanggaran::lapor');     // Form upload
$routes->post('/pelanggaran/simpanLaporan', 'Pelanggaran::simpanLaporan'); // Proses upload

// B. Rute Admin/Petugas (Hanya bisa diakses jika sudah Login)
// Kita bungkus dalam grup 'pelanggaran' dengan filter 'auth'
$routes->group('pelanggaran', ['filter' => 'auth'], function($routes) {

    // rute manage
    $routes->get('manage', 'Pelanggaran::manage'); 
    $routes->get('verifikasi/(:num)/(:segment)', 'Pelanggaran::verifikasi/$1/$2');

    // Rute Hapus
    $routes->get('hapus/(:num)', 'Pelanggaran::hapus/$1');
});