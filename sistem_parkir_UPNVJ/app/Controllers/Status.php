<?php namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StatusAreaModel; 

class Status extends BaseController
{
    public function index()
    {
        $statusAreaModel = new StatusAreaModel();
        
        // 1. Ambil data
        $dataStatus = $statusAreaModel->getStatusUntukView();
        
        // 2. Proses data (Limit 7)
        $processedData = [];
        $limit = 7; 
        $count = 0;

        foreach ($dataStatus as $item) {
            if ($count >= $limit) break;

            $statusArea = $statusAreaModel->hitungStatus($item['kapasitas_now'], $item['kapasitas_max']);
            $waktu = date('H:i:s', strtotime($item['jam']));
            
            $processedData[] = [
                'nama_area'     => $item['nama_area'],
                'status_area'   => $statusArea,
                'jam'           => $waktu, 
                'kapasitas_now' => $item['kapasitas_now'],
                'kapasitas_max' => $item['kapasitas_max'],
            ];
            $count++;
        }

        // 3. Siapkan Data untuk Wrapper
        $data = [
            'title'        => 'Cek Status Area Parkir',
            'petugas'      => session()->get(), 
            
            // --- PERBAIKAN PENTING DI SINI ---
            // 'isi' HANYA BOLEH berisi nama file (String).
            // JANGAN gunakan view() di sini.
            'isi'          => 'status/index', 
            
            // Masukkan data statusParkir langsung ke array utama
            // agar bisa dibaca oleh wrapper dan view index
            'statusParkir' => $processedData 
        ];

        // Tampilkan view menggunakan layout wrapper
        return view('layout/wrapper', $data);
    }
}