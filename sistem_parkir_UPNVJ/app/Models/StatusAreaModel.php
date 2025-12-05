<?php namespace App\Models;

use CodeIgniter\Model;

class StatusAreaModel extends Model
{
    // Menggunakan nama tabel 'status_area' sesuai kode Anda
    protected $table = 'status_area';
    protected $primaryKey = 'id';
    // Di sini saya asumsikan tabel status_area memiliki kolom 'id' sebagai PK,
    // dan juga 'id_area', 'status', 'jam', 'kapasitas_now', 'kapasitas_max'.
    protected $allowedFields = ['id_area', 'status', 'jam', 'kapasitas_now', 'kapasitas_max']; 
    protected $returnType = 'array';
    
    // --- FUNGSI BARU: CLEANING LOG 7 DATA TERAKHIR ---
    public function cleanOldLogs($id_area)
    {
        // 1. Ambil 7 ID terbaru untuk area ini
        $latestIds = $this->select('id')
                          ->where('id_area', $id_area)
                          ->orderBy('jam', 'DESC') // Urutkan berdasarkan jam terbaru
                          ->limit(7)
                          ->findAll();
        
        $idsToKeep = array_column($latestIds, 'id');

        // 2. Hapus semua log di area ini KECUALI 7 ID terbaru
        if (!empty($idsToKeep)) {
            $this->where('id_area', $id_area)
                 ->whereNotIn('id', $idsToKeep)
                 ->delete();
        }
    }
    
    // --- FUNGSI BARU: GET STATUS UNTUK VIEW ---
    public function getStatusUntukView()
    {
        // Ambil data dari tabel status_area dan join dengan area_parkir (AreaModel)
        return $this->select('status_area.*, area_parkir.nama_area')
                    ->join('area_parkir', 'area_parkir.id_area = status_area.id_area')
                    // Ambil hanya 7 data terakhir secara keseluruhan
                    ->orderBy('status_area.jam', 'DESC') 
                    ->limit(7)
                    ->findAll();
    }
    
    // Fungsi untuk menentukan Status Area (Penuh atau Belum Penuh)
    public function hitungStatus($now, $max)
    {
        return ($now >= $max) ? 'Penuh' : 'Belum Penuh';
    }
}