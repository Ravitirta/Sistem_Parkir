<?php namespace App\Controllers;

use App\Models\AreaModel;
use App\Models\KendaraanModel;

class Dashboard extends BaseController
{
    public function index()
    {
        // 1. Panggil Model
        $areaModel = new AreaModel();
        $kendaraanModel = new KendaraanModel();

        // 2. Siapkan Data
        $data = [
            'title'     => 'Transaksi Masuk',
            'isi'       => 'dashboard/transaksi_masuk', 
            'area'      => $areaModel->findAll(),       // Data untuk dropdown Area
            'kendaraan' => $kendaraanModel->findAll()   // Data untuk dropdown Jenis
        ];

        // 3. Tampilkan Wrapper
        return view('layout/wrapper', $data);
    }
    
    // Nanti tambahkan function simpanTransaksi() disini
}
