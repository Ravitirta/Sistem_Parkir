<?php namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LogStatusModel;

class Status extends BaseController
{
    /**
     * Menampilkan halaman Cek Status Area Parkir.
     */
    public function index()
    {
        // Panggil Model yang sudah dibuat
        $logStatusModel = new LogStatusModel();
        
        // 1. Ambil data status dari database (sudah join dengan nama area)
        $dataStatus = $logStatusModel->getStatusParkir();
        
        // 2. Proses data: Menghitung status Penuh/Belum Penuh dan membatasi data
        $processedData = [];
        // Batasi hanya 7 data terakhir dari semua log (sesuai ketentuan)
        $limit = 7; 
        $count = 0;

        foreach ($dataStatus as $item) {
            if ($count >= $limit) {
                // Berhenti jika sudah mencapai 7 data terakhir
                break;
            }

            // Hitung status Penuh/Belum Penuh
            $statusArea = $logStatusModel->hitungStatus($item['kapasitas_now'], $item['kapasitas_max']);
            
            // Format waktu
            $waktu = date('H:i:s', strtotime($item['timestamp']));
            
            $processedData[] = [
                'nama_area'     => $item['nama_area'],
                'status_area'   => $statusArea,
                'jam'           => $waktu, 
                'kapasitas_now' => $item['kapasitas_now'],
                'kapasitas_max' => $item['kapasitas_max'],
            ];
            $count++;
        }

        // Siapkan data yang akan dikirim ke View
        $data = [
            'title' => 'Cek Status Area Parkir',
            'statusParkir' => $processedData,
            // Data session petugas mungkin diperlukan di layout utama (sidebar/header)
            'petugas' => session()->get(), 
        ];

        // Tampilkan view
        return view('status/index', $data);
    }
}
