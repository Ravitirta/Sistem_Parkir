<?php

namespace App\Controllers;

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
        // 1. Cek Login
        if (!session()->get('logged_in')) { return redirect()->to('/'); }

        // 2. TANGKAP DATA FILTER DARI VIEW
        // $this->request->getVar('bulan') mengambil data dari dropdown <select name="bulan">
        // Jika user baru buka halaman (belum pilih), default ke Bulan & Tahun saat ini (date('m')).
        $bulan_pilih = $this->request->getVar('bulan') ?? date('m');
        $tahun_pilih = $this->request->getVar('tahun') ?? date('Y');

        $data = [
            'title' => 'Laporan Keuangan',
            'isi'   => 'laporan/index', 
            'user'  => session()->get('nama'),
            
            // Kirim variabel ini kembali ke View agar Dropdown tidak reset ke default
            'bulan_ini' => $bulan_pilih,
            'tahun_ini' => $tahun_pilih,

            // A. Laporan Harian (Tetap ambil data hari ini secara Realtime)
            'laporan_harian'  => $this->transaksiModel->getLaporanHarian(),
            
            // B. Laporan Bulanan (DIFILTER SESUAI PILIHAN USER)
            // Kita kirim $bulan_pilih dan $tahun_pilih ke Model
            'laporan_bulanan' => $this->laporanBulananModel->getLaporanLengkap($bulan_pilih, $tahun_pilih),
            
            // C. Total Uang Bulan Ini (Card Hijau di atas)
            'total_bulanan'   => $this->transaksiModel->getTotalPendapatanBulanIni()
        ];

        return view('layout/wrapper', $data);
    }

    public function history()
    {
        // 1. Cek Login
        if (!session()->get('logged_in')) { return redirect()->to('/'); }

        // 2. Tangkap Filter (Bulan & Tahun)
        $bulan_pilih = $this->request->getVar('bulan') ?? date('m');
        $tahun_pilih = $this->request->getVar('tahun') ?? date('Y');

        // 3. Ambil Data (Pastikan Teman Anda sudah buat getHistoryData di Model)
        // Jika belum ada, biarkan array kosong [] dulu agar tidak error
        $data_history = $this->transaksiModel->getHistoryData($bulan_pilih, $tahun_pilih);
        // $data_history = []; // Pakai ini jika Model belum siap

        $data = [
            'title' => 'History Transaksi (>30 Hari)',
            'isi'   => 'history/index',
            'user'  => session()->get('nama'),
            
            // Variabel untuk Filter View
            'bulan_ini' => $bulan_pilih,
            'tahun_ini' => $tahun_pilih,
            
            // Data Tabel
            'data_history' => $data_history
        ];

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
