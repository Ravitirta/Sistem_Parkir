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
    public function getLaporanHarian()
    {
        // KITA HARUS JOIN KE TABEL PENGGUNA & AREA
        // Karena di tabel transaksi cuma ada ID (PG_001, AR_001), 
        // sedangkan kita butuh Plat Nomor dan Nama Area untuk ditampilkan.
        
        return $this->select('transaksi.*, pengguna.plat_nomor, area_parkir.nama_area')
                    ->join('pengguna', 'pengguna.id_pengguna = transaksi.id_pengguna', 'left')
                    ->join('area_parkir', 'area_parkir.id_area = transaksi.id_area', 'left')
                    
                    // PERBAIKAN: Gunakan 'status_transaksi' (sesuai DB), BUKAN 'status'
                    ->where('status_transaksi', 'selesai')
                    
                    // PERBAIKAN: Gunakan 'tanggal_transaksi' (sesuai DB), BUKAN 'tanggal_generate'
                    ->where('tanggal_transaksi', date('Y-m-d'))
                    
                    ->orderBy('waktu_keluar', 'DESC')
                    ->findAll();
    }

    // 2. Ambil Rekap Pendapatan Per Area BULAN INI
    public function getLaporanBulanan()
    {
        return $this->select('area_parkir.nama_area, transaksi.tanggal_transaksi, SUM(transaksi.bayar) as pendapatan_per_area')
                    ->join('area_parkir', 'area_parkir.id_area = transaksi.id_area', 'left')
                    
                    // PERBAIKAN: Gunakan 'status_transaksi'
                    ->where('status_transaksi', 'selesai')
                    
                    // Filter Bulan & Tahun Ini
                    ->where('MONTH(tanggal_transaksi)', date('m'))
                    ->where('YEAR(tanggal_transaksi)', date('Y'))
                    
                    ->groupBy('transaksi.id_area') 
                    ->findAll();
    }

    // 3. Hitung TOTAL UANG (Pendapatan) BULAN INI
    public function getTotalPendapatanBulanIni()
    {
        // PERBAIKAN: Gunakan 'bayar' (sesuai DB), BUKAN 'biaya'
        $query = $this->selectSum('bayar') 
                      ->where('status_transaksi', 'selesai')
                      ->where('MONTH(tanggal_transaksi)', date('m'))
                      ->where('YEAR(tanggal_transaksi)', date('Y'))
                      ->first();

        // Jika null (belum ada data), kembalikan 0
        return $query['bayar'] ?? 0; 
    }
}
