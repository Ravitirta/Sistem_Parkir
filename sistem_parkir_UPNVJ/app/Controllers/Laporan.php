<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TransaksiModel;

class Laporan extends BaseController
{
    protected $transaksiModel;

    public function __construct()
    {
        // 1. Load Model Transaksi agar bisa dipakai di semua fungsi
        $this->transaksiModel = new TransaksiModel();
    }

    public function index()
    {
        // 2. Cek apakah Petugas sudah Login?
        if (!session()->get('logged_in')) {
            return redirect()->to('/'); 
        }

        // 3. Siapkan Data untuk dikirim ke View Dashboard Laporan
        $data = [
            'title' => 'Laporan - Sistem Parkir UPNVJ',
            'isi' => 'laporan/index',
            'user'  => session()->get('nama'),
            
            // Pastikan fungsi ini ada di TransaksiModel
            'laporan_harian'  => $this->transaksiModel->getLaporanHarian(),
            'laporan_bulanan' => $this->transaksiModel->getLaporanBulanan(),
            'total_bulanan'   => $this->transaksiModel->getTotalPendapatanBulanIni()
        ];

        // 4. Tampilkan file View index
        return view('layout/wrapper', $data);
    }

    // --- FUNGSI CETAK LAPORAN ---
    public function cetak()
    {
        // 1. Ambil input tanggal dari form filter di view
        $tgl_awal = $this->request->getPost('tgl_awal');
        $tgl_akhir = $this->request->getPost('tgl_akhir');

        // 2. Ambil data dari database berdasarkan rentang tanggal
        $laporan = $this->transaksiModel
            ->where('tgl_keluar >=', $tgl_awal . ' 00:00:00')
            ->where('tgl_keluar <=', $tgl_akhir . ' 23:59:59')
            ->findAll();

        // 3. Siapkan data untuk view cetak
        $data = [
            'title'     => 'Cetak Laporan',
            'laporan'   => $laporan,
            'tgl_awal'  => $tgl_awal,
            'tgl_akhir' => $tgl_akhir,
            'user'      => session()->get('nama_petugas')
        ];

        // 4. Tampilkan view cetak
        return view('laporan/cetak', $data);
    }
}
