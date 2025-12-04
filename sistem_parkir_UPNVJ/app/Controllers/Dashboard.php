<?php namespace App\Controllers;

use App\Models\AreaModel;
use App\Models\KendaraanModel;
use App\Models\TransaksiModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $areaModel = new AreaModel();
        $kendaraanModel = new KendaraanModel();

        $data = [
            'title'     => 'Transaksi Masuk',
            'isi'       => 'dashboard/transaksi_masuk',
            'area'      => $areaModel->findAll(),
            'kendaraan' => $kendaraanModel->findAll()
        ];

        return view('layout/wrapper', $data);
    }

    // --- FUNGSI BARU: MEMBUAT KODE OTOMATIS (PG_003, TR_003) ---
    private function buatKodeOtomatis($tabel, $kolom, $prefix)
    {
        $db = \Config\Database::connect();
        // Ambil kode terakhir, misal: PG_002
        $lastData = $db->table($tabel)->orderBy($kolom, 'DESC')->limit(1)->get()->getRow();

        if ($lastData) {
            // Pecah string PG_002 jadi angka 2
            $lastId = $lastData->$kolom; 
            $number = intval(substr($lastId, 3)); // Ambil angka setelah 3 karakter pertama
            $newNumber = $number + 1; // Tambah 1
        } else {
            $newNumber = 1; // Jika belum ada data, mulai dari 1
        }

        // Gabungkan lagi jadi PG_003 (str_pad biar nol-nya ikut)
        return $prefix . str_pad($newNumber, 3, "0", STR_PAD_LEFT); 
    }

    // --- LOGIKA SIMPAN YANG SUDAH DIPERBAIKI ---
    public function simpanMasuk()
    {
        $transaksiModel = new TransaksiModel();
        $db = \Config\Database::connect();

        // 1. GENERATE ID TRANSAKSI BARU (TR_003)
        $id_transaksi_baru = $this->buatKodeOtomatis('transaksi', 'id_transaksi', 'TR_');

        // 2. CEK PENGGUNA (LOGIKA PG_003)
        $plat_nomor = $this->request->getPost('plat_nomor');
        $id_kendaraan = $this->request->getPost('id_kendaraan');
        
        // Cek apakah plat nomor ini sudah pernah ada?
        $cekPengguna = $db->table('pengguna')->where('plat_nomor', $plat_nomor)->get()->getRow();

        if ($cekPengguna) {
            // Jika SUDAH ADA, pakai ID lama (misal PG_002)
            $id_pengguna_fix = $cekPengguna->id_pengguna;
        } else {
            // Jika BELUM ADA, buat ID BARU (PG_003)
            $id_pengguna_baru = $this->buatKodeOtomatis('pengguna', 'id_pengguna', 'PG_');
            
            // Insert ke tabel pengguna
            $db->table('pengguna')->insert([
                'id_pengguna'  => $id_pengguna_baru,
                'plat_nomor'   => $plat_nomor,
                'id_kendaraan' => $id_kendaraan
            ]);
            $id_pengguna_fix = $id_pengguna_baru;
        }

        // 3. SIAPKAN DATA TRANSAKSI
        $data = [
            'id_transaksi'      => $id_transaksi_baru, // Pakai TR_003
            'tanggal_transaksi' => date('Y-m-d'),      // Realtime Tanggal
            'waktu_masuk'       => date('H:i:s'),      // Realtime Jam
            'waktu_keluar'      => null,
            'id_area'           => $this->request->getPost('id_area'),
            'id_pengguna'       => $id_pengguna_fix,   // Pakai PG_003
            'id_petugas'        => session()->get('id_petugas'), 
            'bayar'             => 0,
            'status_transaksi'  => 'masuk', // Status Masuk
        ];

        // 4. SIMPAN KE DATABASE TRANSAKSI
        $transaksiModel->insert($data);

        // 5. UPDATE KAPASITAS AREA (+1)
        // Query: UPDATE status_area SET kapasitas_now = kapasitas_now + 1 WHERE id_area = 'AR_001'
        $id_area = $this->request->getPost('id_area');
        $db->query("UPDATE status_area SET kapasitas_now = kapasitas_now + 1 WHERE id_area = ?", [$id_area]);

        // 6. Redirect kembali
        return redirect()->to('/dashboard');
    }
}
