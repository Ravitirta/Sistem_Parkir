<?php namespace App\Models;

use CodeIgniter\Model;

class TransaksiModel extends Model
{
    // --- KONFIGURASI TABEL (SESUAI GAMBAR DATABASE) ---
    protected $table      = 'transaksi';      
    protected $primaryKey = 'id_transaksi';   
    
    // Agar CI4 tidak bingung mencari created_at/updated_at
    protected $useTimestamps = false; 

    // Daftar kolom yang benar-benar ada di tabel database kamu
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
    // METHOD KHUSUS UNTUK LAPORAN
    // =================================================================

    // 1. Ambil data transaksi yang selesai HARI INI
    public function getLaporanHarian($area_filter = null)
    {
        $builder = $this->select('transaksi.*, pengguna.plat_nomor, area_parkir.nama_area')
                        ->join('pengguna', 'pengguna.id_pengguna = transaksi.id_pengguna', 'left')
                        ->join('area_parkir', 'area_parkir.id_area = transaksi.id_area', 'left')
                        ->where('status_transaksi', 'selesai')
                        ->where('tanggal_transaksi', date('Y-m-d'));

        // Tambahan Filter Area
        if (!empty($area_filter)) {
            $builder->where('transaksi.id_area', $area_filter);
        }

        return $builder->orderBy('waktu_keluar', 'DESC')->findAll();
    }

     // Hitung Total Pendapatan Harian Per Area (Untuk Tabel Tambahan)
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
    
    // 2. Laporan Bulanan (Menerima Parameter Bulan & Tahun)
    public function getLaporanBulanan($bulan = null, $tahun = null, $area_filter = null)
    {
        // Pastikan parameter tidak null/kosong sebelum masuk query
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

        return $builder->groupBy('transaksi.id_area')->findAll();
    }

    // 3. Hitung TOTAL UANG (Pendapatan) BULAN INI
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

    # METHOD UNTUK HISTORY (Riwayat Detail)
    public function area_parkir.nama_area($bulan = null, $tahun = null)
    {
        $bulan = $bulan ?? date('m');
        $tahun = $tahun ?? date('Y');

        // Kita perlu JOIN ke 3 tabel: Pengguna (Plat), Kendaraan (Jenis), Petugas (Nama)
        return $this->select('transaksi.*, pengguna.plat_nomor, kendaraan.jenis_kendaraan, petugas.nama as nama_petugas')
                    ->join('pengguna', 'pengguna.id_pengguna = transaksi.id_pengguna', 'left')
                
                    ->join('kendaraan', 'kendaraan.id_kendaraan = transaksi.id_kendaraan', 'left')
                    
                    ->join('petugas', 'petugas.id_petugas = transaksi.id_petugas', 'left')
                    
                    ->where('status_transaksi', 'selesai')
                    ->where('MONTH(tanggal_transaksi)', $bulan)
                    ->where('YEAR(tanggal_transaksi)', $tahun)
                    ->orderBy('waktu_keluar', 'DESC')
                    ->findAll();
    }
# METHOD UNTUK HISTORY (Riwayat Detail)
    public function area_parkir.nama_area($bulan = null, $tahun = null)
    {
        $bulan = $bulan ?? date('m');
        $tahun = $tahun ?? date('Y');

        // TAMBAHAN: Select 'area_parkir.nama_area' dan Join ke tabel 'area_parkir'
        return $this->select('transaksi.*, pengguna.plat_nomor, kendaraan.jenis_kendaraan, petugas.nama as nama_petugas, area_parkir.nama_area')
                    ->join('pengguna', 'pengguna.id_pengguna = transaksi.id_pengguna', 'left')
                    ->join('kendaraan', 'kendaraan.id_kendaraan = transaksi.id_kendaraan', 'left')
                    ->join('petugas', 'petugas.id_petugas = transaksi.id_petugas', 'left')
                    // Join Baru:
                    ->join('area_parkir', 'area_parkir.id_area = transaksi.id_area', 'left')
                    
                    ->where('status_transaksi', 'selesai')
                    ->where('MONTH(tanggal_transaksi)', $bulan)
                    ->where('YEAR(tanggal_transaksi)', $tahun)
                    ->orderBy('waktu_keluar', 'DESC')
                    ->findAll();
    }
}
