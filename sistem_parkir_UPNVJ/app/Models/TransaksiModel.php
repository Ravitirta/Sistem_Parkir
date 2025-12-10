<?php namespace App\Models;

use CodeIgniter\Model;

class TransaksiModel extends Model
{
    // --- KONFIGURASI TABEL ---
    protected $table      = 'transaksi';      
    protected $primaryKey = 'id_transaksi';   
    
    // Non-aktifkan timestamps otomatis (created_at/updated_at) karena kita pakai manual
    protected $useTimestamps = false; 

    // Daftar kolom yang boleh diisi
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

    // =================================================================
    // METHOD KHUSUS UNTUK FITUR LAPORAN
    // =================================================================

    /**
     * 1. Ambil data transaksi yang selesai HARI INI
     * Digunakan di halaman Laporan Harian
     */
    public function getLaporanHarian($area_filter = null)
    {
        $builder = $this->select('transaksi.*, pengguna.plat_nomor, area_parkir.nama_area')
                        ->join('pengguna', 'pengguna.id_pengguna = transaksi.id_pengguna', 'left')
                        ->join('area_parkir', 'area_parkir.id_area = transaksi.id_area', 'left')
                        ->where('status_transaksi', 'selesai')
                        ->where('tanggal_transaksi', date('Y-m-d'));

        // Tambahan Filter Area (Jika dipilih)
        if (!empty($area_filter)) {
            $builder->where('transaksi.id_area', $area_filter);
        }

        return $builder->orderBy('waktu_keluar', 'DESC')->findAll();
    }

    /**
     * Hitung Total Pendapatan Harian Per Area
     * Digunakan untuk tabel rekap kecil di Laporan Harian
     */
    public function getRekapHarianPerArea($area_filter = null)
    {
        $builder = $this->select('area_parkir.nama_area, SUM(transaksi.bayar) as total_harian')
                        ->join('area_parkir', 'area_parkir.id_area = transaksi.id_area', 'left')
                        ->where('status_transaksi', 'selesai')
                        ->where('tanggal_transaksi', date('Y-m-d'));

        if (!empty($area_filter)) {
            $builder->where('transaksi.id_area', $area_filter);
        }

        return $builder->groupBy('transaksi.id_area')->findAll();
    }
    
    /**
     * 2. Laporan Bulanan (Menerima Parameter Bulan & Tahun)
     * Mengelompokkan pendapatan berdasarkan tanggal dan area
     */
    public function getLaporanBulanan($bulan = null, $tahun = null, $area_filter = null)
    {
        // Set default ke bulan/tahun sekarang jika parameter kosong
        $bulan = empty($bulan) ? date('m') : $bulan;
        $tahun = empty($tahun) ? date('Y') : $tahun;

        $builder = $this->select('area_parkir.nama_area, transaksi.tanggal_transaksi, SUM(transaksi.bayar) as pendapatan_per_area')
                        ->join('area_parkir', 'area_parkir.id_area = transaksi.id_area', 'left')
                        ->where('status_transaksi', 'selesai')
                        ->where('MONTH(tanggal_transaksi)', $bulan)
                        ->where('YEAR(tanggal_transaksi)', $tahun);

        if (!empty($area_filter)) {
            $builder->where('transaksi.id_area', $area_filter);
        }

        // Group by area agar muncul per area pendapatannya
        return $builder->groupBy('transaksi.id_area')->findAll();
    }

    /**
     * 3. Hitung TOTAL UANG (Pendapatan) BULAN INI
     * Mengembalikan satu angka total (integer)
     */
    public function getTotalPendapatanBulanIni($bulan = null, $tahun = null, $area_filter = null)
    {
        $bulan = empty($bulan) ? date('m') : $bulan;
        $tahun = empty($tahun) ? date('Y') : $tahun;

        $builder = $this->selectSum('bayar') 
                        ->where('status_transaksi', 'selesai')
                        ->where('MONTH(tanggal_transaksi)', $bulan)
                        ->where('YEAR(tanggal_transaksi)', $tahun);

        if (!empty($area_filter)) {
            $builder->where('transaksi.id_area', $area_filter);
        }

        $query = $builder->first();
        return $query['bayar'] ?? 0; 
    }

    // =================================================================
    // METHOD UNTUK FITUR HISTORY (ARSIP)
    // =================================================================

    /**
     * Mengambil detail riwayat transaksi berdasarkan Bulan & Tahun
     * Termasuk Join ke tabel Area, Pengguna, Kendaraan, dan Petugas
     */
    public function getHistoryData($bulan = null, $tahun = null)
    {
        $bulan = $bulan ?? date('m');
        $tahun = $tahun ?? date('Y');

        return $this->select('transaksi.*, pengguna.plat_nomor, kendaraan.jenis_kendaraan, petugas.nama as nama_petugas, area_parkir.nama_area')
                    // Join ke tabel Pengguna untuk ambil Plat Nomor
                    ->join('pengguna', 'pengguna.id_pengguna = transaksi.id_pengguna', 'left')
                    
                    // Join ke tabel Kendaraan untuk ambil Jenis (Mobil/Motor)
                    ->join('kendaraan', 'kendaraan.id_kendaraan = transaksi.id_kendaraan', 'left')
                    
                    // Join ke tabel Petugas untuk ambil Nama Petugas
                    ->join('petugas', 'petugas.id_petugas = transaksi.id_petugas', 'left')
                    
                    // Join ke tabel Area Parkir untuk ambil Nama Area (UPDATE BARU)
                    ->join('area_parkir', 'area_parkir.id_area = transaksi.id_area', 'left')
                    
                    // Filter Status Selesai & Sesuai Bulan/Tahun
                    ->where('status_transaksi', 'selesai')
                    ->where('MONTH(tanggal_transaksi)', $bulan)
                    ->where('YEAR(tanggal_transaksi)', $tahun)
                    
                    // Urutkan dari yang paling baru keluar
                    ->orderBy('waktu_keluar', 'DESC')
                    ->findAll();
    }
}
