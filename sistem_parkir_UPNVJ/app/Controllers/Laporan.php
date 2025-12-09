<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TransaksiModel; 

class Laporan extends BaseController
{
    protected $transaksiModel;

    public function __construct()
    {
        // Pakai TransaksiModel, bukan AreaModel
        $this->transaksiModel = new TransaksiModel();
    }

    public function index()
    {
        // Cek Login
        if (!session()->get('logged_in')) {
            return redirect()->to('/'); 
        }

        $data = [
            'title' => 'Laporan - Sistem Parkir UPNVJ',
            'user'  => session()->get('nama_petugas'),
            'laporan_harian'  => $this->transaksiModel->getLaporanHarian(),
            'laporan_bulanan' => $this->transaksiModel->getLaporanBulanan(),
            'total_bulanan'   => $this->transaksiModel->getTotalPendapatanBulanIni()
        ];

        return view('laporan/index', $data);
    }

    public function cetak()
    {
        $tgl_awal = $this->request->getPost('tgl_awal');
        $tgl_akhir = $this->request->getPost('tgl_akhir');

        // Ambil data sesuai tanggal
        $laporan = $this->transaksiModel->getLengkap()
            ->where('tanggal_transaksi >=', $tgl_awal)
            ->where('tanggal_transaksi <=', $tgl_akhir)
            ->where('status', 'selesai') 
            ->findAll();

        $data = [
            'title'     => 'Cetak Laporan',
            'laporan'   => $laporan,
            'tgl_awal'  => $tgl_awal,
            'tgl_akhir' => $tgl_akhir,
            'user'      => session()->get('nama_petugas')
        ];

        return view('laporan/cetak', $data);
    }
}