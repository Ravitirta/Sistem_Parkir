<?php

namespace App\Models;

use CodeIgniter\Model;

class TransaksiModel extends Model
{
    // --- KONFIGURASI TABEL ---
    protected $table            = 'transaksi'; // <!- PASTIKAN INI SAMA DENGAN NAMA TABEL DI PHP MYADMIN
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    
    // Kolom apa saja yang boleh diisi/diubah
    protected $allowedFields    = [
        'no_polisi', 
        'jenis_kendaraan', 
        'tgl_masuk', 
        'tgl_keluar', 
        'biaya', 
        'status_transaksi', 
        'keterangan'
    ];

    // Menggunakan timestamps otomatis (created_at, updated_at)
    // Kalau di tabel tidak ada kolom created_at, set ini jadi false
    protected $useTimestamps = false; 

    // =================================================================
    // METHOD KHUSUS UNTUK LAPORAN (DIPANGGIL DI CONTROLLER)
    // =================================================================

    // 1. Ambil data transaksi yang selesai/keluar HARI INI
    public function getLaporanHarian()
    {
        // Query: "Ambil semua data dimana status_transaksi 'selesai' DAN tanggal keluar adalah hari ini"
        return $this->where('status_transaksi', 'selesai')
                    ->where('DATE(tgl_keluar)', date('Y-m-d'))
                    ->findAll();
    }

    // 2. Ambil data transaksi yang selesai/keluar BULAN INI
    public function getLaporanBulanan()
    {
        // Query: "Ambil semua data dimana status_transaksi 'selesai' DAN bulan keluar adalah bulan ini"
        return $this->where('status_transaksi', 'selesai')
                    ->where('MONTH(tgl_keluar)', date('m'))
                    ->where('YEAR(tgl_keluar)', date('Y'))
                    ->findAll();
    }

    // 3. Hitung TOTAL UANG (Pendapatan) BULAN INI
    public function getTotalPendapatanBulanIni()
    {
        // Query: "Jumlahkan kolom 'biaya' pada bulan ini"
        $query = $this->selectSum('biaya')
                      ->where('status_transaksi', 'selesai')
                      ->where('MONTH(tgl_keluar)', date('m'))
                      ->where('YEAR(tgl_keluar)', date('Y'))
                      ->first();

        // Kembalikan angkanya. Kalau kosong (belum ada transaksi), kembalikan 0.
        return $query['biaya'] ?? 0; 
    }
}
