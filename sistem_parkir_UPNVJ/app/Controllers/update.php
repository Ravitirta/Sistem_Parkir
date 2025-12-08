<?php namespace App\Controllers;

use App\Models\TransaksiModel;
use App\Models\KendaraanModel; 
use App\Models\StatusAreaModel; 

class TransaksiKeluar extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Transaksi Keluar',
            'isi'   => 'form', 
            'petugas' => session()->get() 
        ];

        return view('layout/wrapper', $data);
    }

    /**
     * Proses untukSimpan Transaksi Keluar
     * Melakukan validasi, menentukan biaya, update data transaksi, dan kurangi kapasitas area parkir.
     */
    public function simpanKeluar()
    {
        $transaksiModel = new TransaksiModel();
        $db = \Config\Database::connect();
        $session = session();
        
        $id_transaksi = $this->request->getPost('id_transaksi'); 

        // 1. VALIDASI & PENGAMBILAN DATA TRANSAKSI
        $dataMasuk = $db->table('transaksi')
                        // Join ke tabel kendaraan untuk mendapatkan harga_perjam
                        ->select('transaksi.*, kendaraan.harga_perjam') 
                        ->join('kendaraan', 'kendaraan.id_kendaraan = transaksi.id_kendaraan')
                        ->where('transaksi.id_transaksi', $id_transaksi)
                        ->where('transaksi.waktu_keluar', null) 
                        ->get()->getRow();

        if (!$dataMasuk) {
            $session->setFlashdata('gagal', 'Gagal! Transaksi ' . $id_transaksi . ' tidak ditemukan, sudah keluar, atau ID salah.');
            return redirect()->to('/transaksikeluar');
        }

        // 2. PENENTUAN BIAYA PARKIR (Tarif Flat)
        
        $waktu_masuk = $dataMasuk->waktu_masuk; 
        $waktu_keluar = date('H:i:s');
       
        $total_bayar = $dataMasuk->harga_perjam; 
        
        // LOGIKA DURASI PARKIR
        $tanggal_transaksi = $dataMasuk->tanggal_transaksi;
        $timestamp_masuk = strtotime($tanggal_transaksi . ' ' . $waktu_masuk);
        $timestamp_keluar = strtotime(date('Y-m-d') . ' ' . $waktu_keluar);
        $selisih_detik = abs($timestamp_keluar - $timestamp_masuk);
        
        // Format durasi parkir
        $jam = floor($selisih_detik / 3600);
        $menit = floor(($selisih_detik % 3600) / 60);
        $durasi_parkir = $jam . ' jam ' . $menit . ' menit';

        // 3. UPDATE DATA DI TABEL TRANSAKSI
        $dataUpdate = [
            'waktu_keluar'      => $waktu_keluar,
            'bayar'             => $total_bayar,
            'status_transaksi'  => 'selesai', 
        ];
        
        $transaksiModel->update($id_transaksi, $dataUpdate);

        // 4. UPDATE KAPASITAS AREA
        $id_area_parkir = $dataMasuk->id_area;
        
        $sql = "UPDATE status_area 
                SET kapasitas_now = kapasitas_now - 1, 
                    jam = ? 
                WHERE id_area = ?";
                    
        $db->query($sql, [$waktu_keluar, $id_area_parkir]);

        // 5. Redirect dan tampilkan hasil
        $session->setFlashdata('berhasil', 
            'Kendaraan berhasil keluar. Durasi parkir: ' . $durasi_parkir . '. **TOTAL BAYAR: Rp ' . number_format($total_bayar) . '**'
        );
        return redirect()->to('/transaksikeluar');
    }
}
