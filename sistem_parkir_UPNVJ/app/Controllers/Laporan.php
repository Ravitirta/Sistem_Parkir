<?php namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TransaksiModel;
use App\Models\AreaModel;

class Laporan extends BaseController
{
    protected $transaksiModel;
    protected $laporanBulananModel;
    protected $areaModel;

    public function __construct()
    {
        $this->transaksiModel = new TransaksiModel(); 
        $this->areaModel = new AreaModel();
    }

    public function index()
    {
        if (!session()->get('logged_in')) { return redirect()->to('/'); }

        // 1. TANGKAP INPUT FILTER
        $bulan_pilih = $this->request->getVar('bulan') ?? date('m');
        $tahun_pilih = $this->request->getVar('tahun') ?? date('Y');
        $area_pilih  = $this->request->getVar('area');

        $data = [
            'title' => 'Laporan Keuangan',
            'isi'   => 'laporan/index', 
            'user'  => session()->get('nama'),
            
            // Data Filter untuk View
            'bulan_ini' => $bulan_pilih,
            'tahun_ini' => $tahun_pilih,
            'area_ini'  => $area_pilih,
            'list_area' => $this->areaModel->findAll(), // Isi dropdown

            // A. Laporan Harian
            'laporan_harian'  => $this->transaksiModel->getLaporanHarian($area_pilih),
            'rekap_harian'    => $this->transaksiModel->getRekapHarianPerArea($area_pilih),
            
            // B. Laporan Bulanan
            'laporan_bulanan' => $this->transaksiModel->getLaporanBulanan($bulan_pilih, $tahun_pilih, $area_pilih),
            
            // C. Total Uang
            'total_bulanan'   => $this->transaksiModel->getTotalPendapatanBulanIni($bulan_pilih, $tahun_pilih, $area_pilih)
        ];

        return view('layout/wrapper', $data);
    }

    // --- FITUR HISTORY ---
    public function history()
    {
        if (!session()->get('logged_in')) { return redirect()->to('/'); }

        // 1. Tangkap Filter
        $bulan_pilih = $this->request->getVar('bulan') ?? date('m');
        $tahun_pilih = $this->request->getVar('tahun') ?? date('Y');
        $area_pilih  = $this->request->getVar('area');

        $data = [
            'title' => 'History Transaksi',
            'isi'   => 'history/index',
            'user'  => session()->get('nama'),
            
            'bulan_ini' => $bulan_pilih,
            'tahun_ini' => $tahun_pilih,
            'area_ini'  => $area_pilih,
            'list_area' => $this->areaModel->findAll(),
            
            // Panggil History Data dengan parameter lengkap
            'data_history' => $this->transaksiModel->getHistoryData($bulan_pilih, $tahun_pilih, $area_pilih)
        ];

        return view('layout/wrapper', $data);
    }

    // --- FITUR CETAK ---
    public function cetak()
    {
        $tgl_awal = $this->request->getPost('tgl_awal');
        $tgl_akhir = $this->request->getPost('tgl_akhir');

        // Tips: Sebaiknya gunakan JOIN juga disini agar nama area dan plat nomor muncul saat dicetak
        $laporan = $this->transaksiModel
            ->select('transaksi.*, pengguna.plat_nomor, area_parkir.nama_area')
            ->join('pengguna', 'pengguna.id_pengguna = transaksi.id_pengguna', 'left')
            ->join('area_parkir', 'area_parkir.id_area = transaksi.id_area', 'left')
            ->where('tanggal_transaksi >=', $tgl_awal)
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
