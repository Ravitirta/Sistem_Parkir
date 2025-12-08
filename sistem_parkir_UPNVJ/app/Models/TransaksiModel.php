<?php namespace App\Models;

use CodeIgniter\Model;

class TransaksiModel extends Model
{
    // --- KONFIGURASI TABEL SESUAI GAMBAR DATABASE ---
    protected $table      = 'transaksi';      
    protected $primaryKey = 'id_transaksi';   
    protected $useAutoIncrement = false;
    
    // Daftar kolom sesuai gambar Tabel Transaksi kamu
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
    public function getLaporanHarian()
    {
        // Kita JOIN ke tabel pengguna & area agar data lengkap
        return $this->select('transaksi.*, pengguna.plat_nomor, area_parkir.nama_area')
                    ->join('pengguna', 'pengguna.id_pengguna = transaksi.id_pengguna', 'left')
                    ->join('area_parkir', 'area_parkir.id_area = transaksi.id_area', 'left')
                    ->where('status_transaksi', 'selesai')
                    ->where('tanggal_transaksi', date('Y-m-d')) // Cek tanggal hari ini
                    ->orderBy('waktu_keluar', 'DESC')
                    ->findAll();
    }

    // 2. Ambil Rekap Pendapatan Per Area BULAN INI
    public function getLaporanBulanan()
    {
        // Menghitung total 'bayar' dikelompokkan berdasarkan area
        return $this->select('area_parkir.nama_area, transaksi.tanggal_transaksi, SUM(transaksi.bayar) as pendapatan_per_area')
                    ->join('area_parkir', 'area_parkir.id_area = transaksi.id_area', 'left')
                    ->where('status_transaksi', 'selesai')
                    ->where('MONTH(tanggal_transaksi)', date('m'))
                    ->where('YEAR(tanggal_transaksi)', date('Y'))
                    ->groupBy('transaksi.id_area') 
                    ->findAll();
    }

    // 3. Hitung TOTAL UANG (Pendapatan) BULAN INI
    public function getTotalPendapatanBulanIni()
    {
        $query = $this->selectSum('bayar') // Nama kolom di DB kamu 'bayar' bukan 'biaya'
                      ->where('status_transaksi', 'selesai')
                      ->where('MONTH(tanggal_transaksi)', date('m'))
                      ->where('YEAR(tanggal_transaksi)', date('Y'))
                      ->first();

        return $query['bayar'] ?? 0; 
    }
}
