<?php namespace App\Models;

use CodeIgniter\Model;

class TransaksiModel extends Model
{
    // ------------------------
    // KONFIGURASI DASAR MODEL
    // ------------------------
    /*
     Kelas Model ini digunakan oleh Laporan.php untuk mengambil data transaksi dari database. 
     Semua fungsi laporan yang dipanggil Controller berada di sini.
     */
    protected $table      = 'transaksi';       // Nama tabel utama yang digunakan
    protected $primaryKey = 'id_transaksi';    // Primary key tabel

    // Timestamps otomatis dimatikan karena aplikasi mengatur sendiri waktu masuk/keluar
    protected $useTimestamps = false;

    /*
     Kolom-kolom yang boleh diproses Model.
     Hal ini diperlukan ketika Controller menyimpan atau meng-update data.
     */
    protected $allowedFields = [
        'id_transaksi', 
        'tanggal_transaksi', 
        'waktu_masuk', 
        'waktu_keluar', 
        'id_area', 
        'id_pengguna',
        'id_kendaraan',  
        'id_petugas', 
        'bayar', 
        'status_transaksi'
    ];

    // ==========================================================
    // BAGIAN INI: METHOD KHUSUS YANG DIPANGGIL OLEH Laporan.php
    // ==========================================================

    /**
     1. LAPORAN HARIAN
     Method ini dipanggil oleh Laporan.php pada bagian:
        $this->transaksiModel->getLaporanHarian($area_pilih)
    
     Fungsi:
     - Mengambil semua transaksi yang selesai pada HARI INI
     - Digunakan untuk tampilan tabel laporan harian
     - Bisa difilter berdasarkan area tertentu
     */
    public function getLaporanHarian($area_filter = null)
    {
        $builder = $this->select('transaksi.*, pengguna.plat_nomor, area_parkir.nama_area')
                        ->join('pengguna', 'pengguna.id_pengguna = transaksi.id_pengguna', 'left')
                        ->join('area_parkir', 'area_parkir.id_area = transaksi.id_area', 'left')
                        ->where('status_transaksi', 'selesai')
                        ->where('tanggal_transaksi', date('Y-m-d'));   // HANYA hari ini

        // Filter area sesuai pilihan user pada Laporan.php
        if (!empty($area_filter)) {
            $builder->where('transaksi.id_area', $area_filter);
        }

        return $builder->orderBy('waktu_keluar', 'DESC')->findAll();
    }

    /**
     2. REKAP HARIAN PER AREA
     Dipanggil oleh Laporan.php:
        $this->transaksiModel->getRekapHarianPerArea($area_pilih)
     
     Fungsi:
     - Menghitung total pendapatan harian menggunakan SUM(bayar)
     - Ditampilkan pada kotak "Rekap Harian" di halaman laporan
     */
    public function getRekapHarianPerArea($area_filter = null)
    {
        $builder = $this->select('area_parkir.nama_area, SUM(transaksi.bayar) as total_harian')
                        ->join('area_parkir', 'area_parkir.id_area = transaksi.id_area', 'left')
                        ->where('status_transaksi', 'selesai')
                        ->where('tanggal_transaksi', date('Y-m-d'));

        // Area disesuaikan dengan filter dari Laporan.php
        if (!empty($area_filter)) {
            $builder->where('transaksi.id_area', $area_filter);
        }

        // Group by area untuk mendapatkan total per area
        return $builder->groupBy('transaksi.id_area')->findAll();
    }
    
    /**
     3. LAPORAN BULANAN
     Dipanggil oleh Laporan.php:
        $this->transaksiModel->getLaporanBulanan($bulan, $tahun, $area)
     
     Fungsi:
     - Mengolah data laporan berdasarkan bulan & tahun yang dipilih user
     - Digunakan di bagian "Laporan Bulanan" pada halaman laporan
     - Melakukan perhitungan pendapatan menggunakan SUM
     */
    public function getLaporanBulanan($bulan = null, $tahun = null, $area_filter = null)
    {
        // Jika Laporan.php tidak mengirim bulan/tahun, pakai bulan sekarang
        $bulan = empty($bulan) ? date('m') : $bulan;
        $tahun = empty($tahun) ? date('Y') : $tahun;

        $builder = $this->select('area_parkir.nama_area, transaksi.tanggal_transaksi, SUM(transaksi.bayar) as pendapatan_per_area')
                        ->join('area_parkir', 'area_parkir.id_area = transaksi.id_area', 'left')
                        ->where('status_transaksi', 'selesai')
                        ->where('MONTH(tanggal_transaksi)', $bulan)
                        ->where('YEAR(tanggal_transaksi)', $tahun);

        // Filter area berdasarkan pilihan dropdown di Laporan.php
        if (!empty($area_filter)) {
            $builder->where('transaksi.id_area', $area_filter);
        }

        return $builder->groupBy('transaksi.id_area')->findAll();
    }

    /**
     4. TOTAL PENDAPATAN BULANAN
     Dipanggil oleh Laporan.php:
        $this->transaksiModel->getTotalPendapatanBulanIni($bulan, $tahun, $area)
     
     Fungsi:
     - Menghasilkan satu angka total pendapatan bulan tersebut
     - Ditampilkan di bagian "Total Pendapatan Bulan Ini" pada Laporan.php
     */
    public function getTotalPendapatanBulanIni($bulan = null, $tahun = null, $area_filter = null)
    {
        $bulan = empty($bulan) ? date('m') : $bulan;
        $tahun = empty($tahun) ? date('Y') : $tahun;

        $builder = $this->selectSum('bayar')
                        ->where('status_transaksi', 'selesai')
                        ->where('MONTH(tanggal_transaksi)', $bulan)
                        ->where('YEAR(tanggal_transaksi)', $tahun);

        // Filter area sesuai pilihan user
        if (!empty($area_filter)) {
            $builder->where('transaksi.id_area', $area_filter);
        }

        $query = $builder->first();
        return $query['bayar'] ?? 0;     // Jika null, kembalikan angka 0
    }

    // =======================================================================
    // METHOD KHUSUS UNTUK FITUR HISTORY (dipanggil oleh Laporan.php->history)
    // =======================================================================

    /**
     5. HISTORY TRANSAKSI (ARSIP)
     Dipanggil oleh Laporan.php:
        $this->transaksiModel->getHistoryData($bulan, $tahun, $area)
    
     Fungsi:
     - Mengambil semua transaksi masa lalu
     - Menampilkan detail lengkap: plat, jenis kendaraan, area, petugas, dll
     - Digunakan untuk halaman "History Transaksi"
     */
    public function getHistoryData($bulan = null, $tahun = null)
    {
        $bulan = $bulan ?? date('m');
        $tahun = $tahun ?? date('Y');

        return $this->select('transaksi.*, pengguna.plat_nomor, kendaraan.jenis_kendaraan, petugas.nama as nama_petugas, area_parkir.nama_area')
                    ->join('pengguna', 'pengguna.id_pengguna = transaksi.id_pengguna', 'left')
                    ->join('kendaraan', 'kendaraan.id_kendaraan = transaksi.id_kendaraan', 'left')
                    ->join('petugas', 'petugas.id_petugas = transaksi.id_petugas', 'left')
                    ->join('area_parkir', 'area_parkir.id_area = transaksi.id_area', 'left')
                    ->where('status_transaksi', 'selesai')
                    ->where('MONTH(tanggal_transaksi)', $bulan)
                    ->where('YEAR(tanggal_transaksi)', $tahun)
                    ->orderBy('waktu_keluar', 'DESC')
                    ->findAll();
    }
}
