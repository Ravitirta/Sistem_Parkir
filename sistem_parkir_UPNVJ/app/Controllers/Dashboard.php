<?php namespace App\Controllers;

use App\Models\AreaModel;
use App\Models\KendaraanModel;
use App\Models\TransaksiModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $AreaModel = new AreaModel();
        $KendaraanModel = new KendaraanModel();

        $data = [
            'title'     => 'Transaksi Masuk',
            'isi'       => 'dashboard/transaksi_masuk',
            'area'      => $AreaModel->findAll(),
            'kendaraan' => $KendaraanModel->findAll()
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

    // --- LOGIKA SIMPAN MASUK ---
    public function simpanMasuk()
    {
        $transaksiModel = new TransaksiModel();
        $db = \Config\Database::connect();

        // Ambil Waktu Realtime
        $waktu_sekarang = date('H:i:s'); 
        $tanggal_sekarang = date('Y-m-d');

        // 1. GENERATE ID TRANSAKSI BARU
        $id_transaksi_baru = $this->buatKodeOtomatis('transaksi', 'id_transaksi', 'TR_');

        // 2. CEK PENGGUNA (LOGIKA PG_xxx)
        $plat_nomor = $this->request->getPost('plat_nomor');
        $id_kendaraan = $this->request->getPost('id_kendaraan');
        
        $cekPengguna = $db->table('pengguna')->where('plat_nomor', $plat_nomor)->get()->getRow();

        if ($cekPengguna) {
            $id_pengguna_fix = $cekPengguna->id_pengguna;
        } else {
            $id_pengguna_baru = $this->buatKodeOtomatis('pengguna', 'id_pengguna', 'PG_');
            
            // Insert Pengguna Baru
            $db->table('pengguna')->insert([
                'id_pengguna'  => $id_pengguna_baru,
                'plat_nomor'   => $plat_nomor,
                'id_kendaraan' => $id_kendaraan
            ]);
            $id_pengguna_fix = $id_pengguna_baru;
        }

        // 3. SIAPKAN DATA TRANSAKSI
        $data = [
            'id_transaksi'      => $id_transaksi_baru, 
            'tanggal_transaksi' => $tanggal_sekarang,
            'waktu_masuk'       => $waktu_sekarang,
            'waktu_keluar'      => null,
            'id_area'           => $this->request->getPost('id_area'),
            'id_pengguna'       => $id_pengguna_fix,
            'id_kendaraan'      => $this->request->getPost('id_kendaraan'),
            'id_petugas'        => session()->get('id_petugas'), 
            'bayar'             => 0,
            'status_transaksi'  => 'parkir',
        ];

        // 4. SIMPAN KE DATABASE TRANSAKSI
        $transaksiModel->insert($data);

        // 5. UPDATE KAPASITAS & JAM DI STATUS AREA (REVISI DISINI)
        $id_area = $this->request->getPost('id_area');
        
        $sql = "UPDATE status_area 
                SET kapasitas_now = kapasitas_now + 1, 
                    jam = ? 
                WHERE id_area = ?";
                
        $db->query($sql, [$waktu_sekarang, $id_area]);

        // 6. Redirect kembali
        return redirect()->to('/dashboard');
    }
}
