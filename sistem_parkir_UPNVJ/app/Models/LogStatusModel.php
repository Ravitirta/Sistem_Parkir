<?php namespace App\Models;

use CodeIgniter\Model;

class LogStatusModel extends Model
{
    protected $table = 'log_status';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_area', 'timestamp', 'kapasitas_now', 'kapasitas_max'];

    /**
     * Mengambil semua data status parkir terbaru per area,
     * digabungkan dengan nama area.
     */
    public function getStatusParkir()
    {
        // Join dengan tabel area_parkir untuk mendapatkan nama_area
        return $this->select('log_status.*, area_parkir.nama_area')
                    ->join('area_parkir', 'area_parkir.id_area = log_status.id_area')
                    // Mengambil data berdasarkan timestamp terbaru jika ada banyak log (atau hanya ambil 7 log terbaru sesuai ketentuan)
                    ->orderBy('log_status.timestamp', 'DESC')
                    ->findAll();
    }
    
    /**
     * Fungsi untuk menentukan Status Area (Penuh atau Belum Penuh)
     */
    public function hitungStatus($now, $max)
    {
        return ($now >= $max) ? 'Penuh' : 'Belum Penuh';
    }
}
