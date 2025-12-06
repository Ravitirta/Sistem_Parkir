<?php

use CodeIgniter\Router\RouteCollection;

$routes->get('/', 'Auth::index');
$routes->post('/auth/loginProcess', 'Auth::loginProcess');
$routes->get('/auth/logout', 'Auth::logout');

// Rute Dashboard 
$routes->group('dashboard', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Dashboard::index');
    
    // routes transaksi kendaraan masuk
    $routes->post('simpanMasuk', 'Dashboard::simpanMasuk');

    // routes Cek Status
    $routes->get('status', 'Status::index'); 

    //  untuk Fitur Update/Checkout
    $routes->get('update', 'Update::index'); // Tampilan list/pencarian
    $routes->post('update/calculate/(:segment)', 'Update::calculate/$1'); // Hitung bayar
    $routes->post('update/checkout/(:segment)', 'Update::checkout/$1'); // Update status selesai
  
    // untuk Transaksi Keluar
    // Menampilkan form Transaksi Keluar
    $routes->get('transaksiKeluar', 'TransaksiKeluar::index'); 
    // Memproses data dan menyimpan Transaksi Keluar
    $routes->post('transaksiKeluar/simpanKeluar', 'TransaksiKeluar::simpanKeluar');
});
