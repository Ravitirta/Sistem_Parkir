<?php namespace App\Models;

use CodeIgniter\Model;

class PelanggaranModel extends Model
{
    protected $table = 'pelanggaran';
    protected $primaryKey = 'id_pelanggaran';
    protected $allowedFields = ['tanggal', 'id_area', 'foto', 'keterangan', 'status'];

    // Ambil data yang sudah VALID (untuk dilihat umum)
    public function getValid()
    {
        return $this->select('pelanggaran.*, area_parkir.nama_area')
                    ->join('area_parkir', 'area_parkir.id_area = pelanggaran.id_area')
                    ->where('status', 'valid')
                    ->orderBy('tanggal', 'DESC')
                    ->findAll();
    }

    // Ambil data PENDING (untuk dicek petugas)
    public function getPending()
    {
        return $this->select('pelanggaran.*, area_parkir.nama_area')
                    ->join('area_parkir', 'area_parkir.id_area = pelanggaran.id_area')
                    ->where('status', 'pending')
                    ->findAll();
    }
}
