<?php namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TransaksiModel;
use App\Models\LaporanBulananModel;

class Laporan extends BaseController
{
    protected $transaksiModel;
    protected $laporanBulananModel;

    public function __construct()
    {
        $this->transaksiModel = new TransaksiModel();
        $this->laporanBulananModel = new LaporanBulananModel(); 
    }

    public function index()
    {
        if (!session()->get('logged_in')) { return redirect()->to('/'); }

        // Filter
        $bulan_pilih = $this->request->getVar('bulan') ?? date('m');
        $tahun_pilih = $this->request->getVar('tahun') ?? date('Y');

        $data = [
            'title' => 'Laporan Keuangan',
            'isi'   => 'laporan/index', 
            'user'  => session()->get('nama'),
            'bulan_ini' => $bulan_pilih,
            'tahun_ini' => $tahun_pilih,

            // Data
            'laporan_harian'  => $this->transaksiModel->getLaporanHarian(),
            'laporan_bulanan' => $this->transaksiModel->getLaporanBulanan($bulan_pilih, $tahun_pilih), // Pakai TransaksiModel agar Live
            'total_bulanan'   => $this->transaksiModel->getTotalPendapatanBulanIni($bulan_pilih, $tahun_pilih)
        ];

        return view('layout/wrapper', $data);
    }

    // --- FITUR HISTORY (DIPERBAIKI) ---
    public function history()
    {
        if (!session()->get('logged_in')) { return redirect()->to('/'); }

        // 1. Tangkap Filter
        $bulan_pilih = $this->request->getVar('bulan') ?? date('m');
        $tahun_pilih = $this->request->getVar('tahun') ?? date('Y');

        // 2. Ambil Data History (Fungsi ini harus ada di Model)
        $data_history = $this->transaksiModel->getHistoryData($bulan_pilih, $tahun_pilih);

        $data = [
            'title' => 'History Transaksi',
            'isi'   => 'history/index',
            'user'  => session()->get('nama'),
            
            'bulan_ini' => $bulan_pilih,
            'tahun_ini' => $tahun_pilih,
            
            'data_history' => $data_history
        ];

        return view('layout/wrapper', $data);
    }

    // --- FITUR CETAK ---
    public function cetak()
    {
        $tgl_awal = $this->request->getPost('tgl_awal');
        $tgl_akhir = $this->request->getPost('tgl_akhir');

        $laporan = $this->transaksiModel
            ->where('tanggal_transaksi >=', $tgl_awal) // Perbaikan nama kolom
            ->where('tanggal_transaksi <=', $tgl_akhir)
            ->where('status_transaksi', 'selesai')
            ->findAll();

        $data = [
            'title'     => 'Cetak Laporan',
            'laporan'   => $laporan,
            'tgl_awal'  => $tgl_awal,
            'tgl_akhir' => $tgl_akhir,
            'user'      => session()->get('nama')
        ];

        return view('laporan/cetak', $data);
    }
}
