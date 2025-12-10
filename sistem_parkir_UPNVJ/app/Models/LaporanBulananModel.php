<?php namespace App\Models;

use CodeIgniter\Model;

class LaporanBulananModel extends Model
{
    protected $table      = 'laporan_bulanan';
    protected $primaryKey = 'id_laporan';
    protected $allowedFields = ['id_laporan', 'tahun_bulan', 'id_area', 'total_pendapatan', 'tanggal_generate'];

    // MENERIMA PARAMETER BULAN DAN TAHUN (Default null = ambil semua/terbaru)
    public function getLaporanLengkap($bulan = null, $tahun = null)
    {
        $builder = $this->select('laporan_bulanan.*, area_parkir.nama_area, laporan_bulanan.total_pendapatan as pendapatan_per_area')
                        ->join('area_parkir', 'area_parkir.id_area = laporan_bulanan.id_area', 'left');

        // Jika ada request filter Bulan & Tahun
        if ($bulan && $tahun) {
            $periode = $tahun . '-' . $bulan; // Format di DB: "2025-01"
            $builder->where('tahun_bulan', $periode);
        }

        return $builder->orderBy('tahun_bulan', 'DESC')->findAll();
    }
}