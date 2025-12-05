<?php namespace App\Controllers;

use App\Models\AreaModel;
use App\Models\KendaraanModel;
use App\Models\TransaksiModel;
use App\Models\StatusAreaModel; // Wajib dipanggil untuk update status

class Dashboard extends BaseController
{
    public function index()
    {
        $AreaModel = new AreaModel();
        $KendaraanModel = new KendaraanModel();

        // Data yang dikirim ke View Wrapper
        $data = [
            'title'     => 'Transaksi Masuk',
            // Pastikan Anda punya file app/Views/dashboard/transaksi_masuk.php
            'isi'       => 'dashboard/transaksi_masuk', 
            'area'      => $AreaModel->findAll(),
            'kendaraan' => $KendaraanModel->findAll(),
            'petugas'   => session()->get() // Data sesi petugas
        ];

        return view('layout/wrapper', $data);
    }

    /**
     * Fungsi Helper untuk membuat ID otomatis (contoh: TR_001, PG_005)
     */
    private function buatKodeOtomatis($tabel, $kolom, $prefix)
    {
        $db = \Config\Database::connect();
        // Ambil 1 data terakhir berdasarkan urutan ID terbesar
        $lastData = $db->table($tabel)->orderBy($kolom, 'DESC')->limit(1)->get()->getRow();

        if ($lastData) {
            $lastId = $lastData->$kolom; 
            // Ambil angka dari string (misal TR_001 -> ambil 001 jadi integer 1)
            $number = intval(substr($lastId, 3)); 
            $newNumber = $number + 1; 
        } else {
            $newNumber = 1; 
        }

        // Format kembali menjadi 3 digit (001, 002, dst)
        return $prefix . str_pad($newNumber, 3, "0", STR_PAD_LEFT); 
    }

    /**
     * Proses Simpan Transaksi Masuk
     */
    public function simpanMasuk()
    {
        $transaksiModel = new TransaksiModel();
        $statusAreaModel = new StatusAreaModel(); // Panggil model status
        $db = \Config\Database::connect();
        $session = session();

        // Ambil input dari form
        $plat_nomor = strtoupper($this->request->getPost('plat_nomor')); // Ubah ke huruf besar
        $id_area = $this->request->getPost('id_area'); 
        $id_kendaraan = $this->request->getPost('id_kendaraan');

        // --- 1. VALIDASI: Cek apakah kendaraan masih ada di dalam (belum checkout)? ---
        $cekDuplikat = $db->table('transaksi')
                          ->join('pengguna', 'pengguna.id_pengguna = transaksi.id_pengguna')
                          ->where('pengguna.plat_nomor', $plat_nomor)
                          ->where('transaksi.waktu_keluar', null) // Waktu keluar NULL berarti masih parkir
                          ->countAllResults();

        if ($cekDuplikat > 0) {
            $session->setFlashdata('gagal', 'Gagal! Kendaraan dengan plat ' . $plat_nomor . ' tercatat masih ada di dalam area parkir.');
            return redirect()->to('/dashboard');
        }

        // --- 2. PERSIAPAN DATA ---
        $waktu_sekarang = date('H:i:s'); 
        $tanggal_sekarang = date('Y-m-d');

        // A. Generate ID Transaksi Baru
        $id_transaksi_baru = $this->buatKodeOtomatis('transaksi', 'id_transaksi', 'TR_');

        // B. Cek Data Pengguna (Apakah Plat Nomor Baru atau Lama?)
        $cekPengguna = $db->table('pengguna')->where('plat_nomor', $plat_nomor)->get()->getRow();

        if ($cekPengguna) {
            // Jika sudah ada, pakai ID lama
            $id_pengguna_fix = $cekPengguna->id_pengguna;
        } else {
            // Jika belum ada, buat ID Pengguna baru dan simpan
            $id_pengguna_baru = $this->buatKodeOtomatis('pengguna', 'id_pengguna', 'PG_');
            $db->table('pengguna')->insert([
                'id_pengguna'  => $id_pengguna_baru,
                'plat_nomor'   => $plat_nomor,
                'id_kendaraan' => $id_kendaraan,
            ]);
            $id_pengguna_fix = $id_pengguna_baru;
        }

        // --- 3. SIMPAN KE TABEL TRANSAKSI ---
        $dataTransaksi = [
            'id_transaksi'      => $id_transaksi_baru, 
            'tanggal_transaksi' => $tanggal_sekarang,
            'waktu_masuk'       => $waktu_sekarang,
            'waktu_keluar'      => null, // Masih null karena baru masuk
            'id_area'           => $id_area,
            'id_pengguna'       => $id_pengguna_fix,
            'id_kendaraan'      => $id_kendaraan,
            'id_petugas'        => session()->get('id_petugas'), 
            'bayar'             => 0,
            'status_transaksi'  => 'masuk',
        ];

        $transaksiModel->insert($dataTransaksi);



        // --- 4. UPDATE LOG STATUS & HISTORY AREA ---
        
        // --- REVISI LANGKAH 5 (PASTIKAN KODENYA SEPERTI INI) ---
        // Masalahmu terjadi karena kode di sini mencoba 'insert' data baru.
        // Kita harus paksa pakai SQL 'UPDATE' agar dia hanya mengedit data yang sudah ada.
        
        $id_area = $this->request->getPost('id_area');
        
        // Query SQL Manual agar 100% aman
        $sql = "UPDATE status_area 
                SET kapasitas_now = kapasitas_now + 1, 
                    jam = ? 
                WHERE id_area = ?";
                
        // Eksekusi Query
        $db->query($sql, [$waktu_sekarang, $id_area]);

        // 6. Redirect kembali
        $session->setFlashdata('berhasil', 'Kendaraan berhasil masuk.');
        return redirect()->to('/dashboard');
    }
}