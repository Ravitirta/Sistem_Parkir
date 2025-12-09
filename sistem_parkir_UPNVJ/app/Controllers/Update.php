<?php namespace App\Controllers;

use App\Models\TransaksiModel;
use App\Models\KendaraanModel; 
use App\Models\StatusAreaModel; 

class Update extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $request = $this->request; // Menggunakan Request Object yang sudah ada

        // --- LOGIKA MENGAMBIL DATA UNTUK TABEL ---
        
        $builder = $db->table('transaksi t'); // Gunakan Alias 't' untuk Transaksi

        // 1. Join ke Pengguna (p) untuk mengambil Plat Nomor
        $builder->join('pengguna p', 'p.id_pengguna = t.id_pengguna');
        
        // 2. Join ke Kendaraan (k) untuk Jenis Kendaraan (lewat tabel Pengguna)
        $builder->join('kendaraan k', 'k.id_kendaraan = p.id_kendaraan');

        // 3. Pilih kolom yang dibutuhkan
        $builder->select('t.*, p.plat_nomor, k.jenis_kendaraan');

        // 4. FILTER: Hanya ambil kendaraan yang BELUM keluar
        $builder->groupStart()
                ->where('t.status_transaksi', 'masuk')
                ->orWhere('t.waktu_keluar', null)
                ->groupEnd();

        // 5. Fitur Pencarian Plat Nomor
        $keyword = $request->getVar('plat_nomor');
        if ($keyword) {
            $builder->like('p.plat_nomor', $keyword);
        }

        // 6. Ambil semua datanya
        $query = $builder->get();
        $dataTransaksi = $query->getResultArray();

        // --- KIRIM DATA KE VIEW ---
        $data = [
            'title'          => 'Transaksi Keluar',
            'isi'            => 'update/index', 
            'petugas'        => session()->get(), 
            'request'        => $request,
            // Variabel ini yang ditunggu oleh View index.php Anda!
            'transaksiMasuk' => $dataTransaksi 
        ];

        return view('layout/wrapper', $data);
    }

    // --- FUNGSI BARU UNTUK MENGHITUNG BIAYA (Flat Rate) ---
    public function calculate($id_transaksi)
    {
        $db = \Config\Database::connect();
        $session = session();

        // [Query untuk mengambil data harga, sama seperti sebelumnya]
        $dataMasuk = $db->table('transaksi t')
                        ->select('t.waktu_masuk, k.harga_perjam') 
                        ->join('pengguna p', 'p.id_pengguna = t.id_pengguna')
                        ->join('kendaraan k', 'k.id_kendaraan = p.id_kendaraan')
                        ->where('t.id_transaksi', $id_transaksi)
                        ->where('t.waktu_keluar', null)
                        ->get()->getRow();

        if (!$dataMasuk) {
            $session->setFlashdata('gagal', 'Transaksi tidak valid atau sudah diproses.');
            return redirect()->to('/dashboard/update');
        }

        $total_bayar = $dataMasuk->harga_perjam;
        $waktu_keluar_real = date('H:i:s');

        $session->set('hitung_bayar_' . $id_transaksi, [
            'total_bayar' => $total_bayar,
            'waktu_keluar_real' => $waktu_keluar_real,
        ]);
        
        // Kita juga set flashdata agar View bisa baca hasil perhitungannya
        $session->setFlashdata('perhitungan_berhasil', $id_transaksi); 
        
        return redirect()->to('/dashboard/update');
    }

    // --- FUNGSI UNTUK MENYELESAIKAN TRANSAKSI (Checkout) ---
    public function checkout($id_transaksi)
    {
        $transaksiModel = new TransaksiModel();
        $db = \Config\Database::connect();
        $session = session();
        
        // 1. Ambil data perhitungan yang disimpan di session biasa
        $hitungData = $session->get('hitung_bayar_' . $id_transaksi); // 👈 UBAH DARI getFlashdata ke get()
        
        if (empty($hitungData)) {
            $session->setFlashdata('gagal', 'Harap klik Bayar (OUT) terlebih dahulu!');
            return redirect()->to('/dashboard/update');
        }

        // 2. Hapus data perhitungan dari session agar tidak bisa dipakai lagi
        $session->remove('hitung_bayar_' . $id_transaksi); // 👈 HAPUS DATA SEMENTARA

        // 3. Ambil data area untuk update kapasitas
        $dataMasuk = $db->table('transaksi')->where('id_transaksi', $id_transaksi)->get()->getRow();

        // 4. Update Transaksi
        $transaksiModel->update($id_transaksi, [
            'waktu_keluar'      => $hitungData['waktu_keluar_real'],
            'bayar'             => $hitungData['total_bayar'],
            'status_transaksi'  => 'selesai', 
        ]);

        // 5. Update Kapasitas Area
        $db->query("UPDATE status_area SET kapasitas_now = kapasitas_now - 1, jam = ? WHERE id_area = ?", [$hitungData['waktu_keluar_real'], $dataMasuk->id_area]);

        $session->setFlashdata('berhasil', 'Checkout Berhasil! Total Bayar: Rp ' . number_format($hitungData['total_bayar']));
        
        // 6. Kembali ke halaman Update
        return redirect()->to('/dashboard/update');
    }
}