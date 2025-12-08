<?php namespace App\Controllers;

use App\Models\TransaksiModel;
use App\Models\KendaraanModel; 
use App\Models\StatusAreaModel; 

class Update extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Transaksi Keluar',
            'isi'   => 'update/index', 
            'petugas' => session()->get(), 
            'request' => $this->request
        ];

        return view('layout/wrapper', $data);
    }

    public function simpanKeluar()
    {
        $transaksiModel = new TransaksiModel();
        $db = \Config\Database::connect();
        $session = session();
        
        $id_transaksi = $this->request->getPost('id_transaksi'); 

        // 1. Cek Data
        $dataMasuk = $db->table('transaksi')
                        ->select('transaksi.*, kendaraan.harga_perjam') 
                        ->join('kendaraan', 'kendaraan.id_kendaraan = transaksi.id_kendaraan')
                        ->where('transaksi.id_transaksi', $id_transaksi)
                        ->where('transaksi.waktu_keluar', null) 
                        ->get()->getRow();

        if (!$dataMasuk) {
            $session->setFlashdata('gagal', 'Gagal! Transaksi tidak ditemukan/sudah keluar.');
            return redirect()->to('/dashboard/update');
        }

        // 2. Bayar Flat
        $waktu_keluar = date('H:i:s');
        $total_bayar = $dataMasuk->harga_perjam; 

        // 3. Update Transaksi
        $transaksiModel->update($id_transaksi, [
            'waktu_keluar'      => $waktu_keluar,
            'bayar'             => $total_bayar,
            'status_transaksi'  => 'selesai', 
        ]);

        // 4. Update Kapasitas (Kurangi 1)
        $db->query("UPDATE status_area SET kapasitas_now = kapasitas_now - 1, jam = ? WHERE id_area = ?", [$waktu_keluar, $dataMasuk->id_area]);

        $session->setFlashdata('berhasil', 'Berhasil keluar. Total Bayar: Rp ' . number_format($total_bayar));
        return redirect()->to('/dashboard/update');
    }
}
