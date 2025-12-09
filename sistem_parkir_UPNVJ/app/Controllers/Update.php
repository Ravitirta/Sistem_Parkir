<?php namespace App\Controllers;

use App\Models\TransaksiModel;
use App\Models\KendaraanModel; 
use App\Models\StatusAreaModel; 
use App\Models\AreaModel; // 1. Tambahkan Model Area

class Update extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $request = $this->request;
        
        // Load Model Area untuk isi dropdown
        $areaModel = new AreaModel();

        // --- LOGIKA QUERY DATA ---
        
        $builder = $db->table('transaksi t'); 

        // Join tabel-tabel terkait
        $builder->join('pengguna p', 'p.id_pengguna = t.id_pengguna');
        $builder->join('kendaraan k', 'k.id_kendaraan = t.id_kendaraan'); // Menggunakan id_kendaraan dari transaksi (lebih akurat)
        $builder->join('area_parkir a', 'a.id_area = t.id_area'); // 2. Join ke tabel Area

        // Pilih kolom (tambahkan nama_area)
        $builder->select('t.*, p.plat_nomor, k.jenis_kendaraan, k.harga_perjam, a.nama_area');

        // Filter: Hanya kendaraan yang BELUM keluar (Masih Parkir)
        $builder->groupStart()
                ->where('t.status_transaksi', 'masuk')
                ->orWhere('t.status_transaksi', 'parkir') // Handle 'parkir' juga jaga-jaga
                ->orWhere('t.waktu_keluar', null)
                ->groupEnd();

        // 3. Filter Pencarian Plat Nomor
        $keyword = $request->getVar('plat_nomor');
        if ($keyword) {
            $builder->like('p.plat_nomor', $keyword);
        }

        // 4. Filter Area Parkir (BARU)
        $area_id = $request->getVar('area');
        if ($area_id) {
            $builder->where('t.id_area', $area_id);
        }

        // Urutkan dari yang paling lama masuk (agar yang lama segera diproses)
        $builder->orderBy('t.waktu_masuk', 'ASC');

        $query = $builder->get();
        $dataTransaksi = $query->getResultArray();

        // --- KIRIM DATA KE VIEW ---
        $data = [
            'title'          => 'Kendaraan Keluar (Update)',
            'isi'            => 'update/index', 
            'petugas'        => session()->get(), 
            'request'        => $request,
            'transaksiMasuk' => $dataTransaksi,
            
            // Kirim data area & pilihan saat ini
            'areas'          => $areaModel->findAll(),
            'selected_area'  => $area_id
        ];

        return view('layout/wrapper', $data);
    }

    // --- FUNGSI HITUNG BIAYA ---
    public function calculate($id_transaksi)
    {
        $db = \Config\Database::connect();
        $session = session();

        // 1. Ambil data lengkap (Tanggal & Waktu Masuk)
        $dataMasuk = $db->table('transaksi t')
                        ->select('t.tanggal_transaksi, t.waktu_masuk, k.harga_perjam') 
                        ->join('kendaraan k', 'k.id_kendaraan = t.id_kendaraan')
                        ->where('t.id_transaksi', $id_transaksi)
                        ->get()->getRow();

        if (!$dataMasuk) {
            $session->setFlashdata('gagal', 'Data transaksi tidak ditemukan.');
            return redirect()->back();
        }

        // 2. Buat Timestamp Masuk (Gabungan Tanggal + Jam)
        // Contoh: "2025-12-09 07:00:00"
        $waktuMasukString = $dataMasuk->tanggal_transaksi . ' ' . $dataMasuk->waktu_masuk;
        $timestampMasuk   = strtotime($waktuMasukString);
        
        // 3. Ambil Timestamp Keluar (Realtime)
        $timestampKeluar  = time(); 

        // 4. Hitung Selisih Detik
        $selisihDetik = $timestampKeluar - $timestampMasuk;

        // 5. Konversi ke Jam dengan Pembulatan Standar
        // round() membulatkan ke bilangan bulat terdekat.
        $durasiJam = round($selisihDetik / 3600); 
        
        // Minimal bayar 1 jam (jika hasil round 0, tetap dihitung 1)
        if($durasiJam < 1) $durasiJam = 1; 
        
        // 6. Hitung Total Bayar
        $total_bayar = $durasiJam * $dataMasuk->harga_perjam;
        
        // Format Waktu Keluar untuk Database
        $waktu_keluar_real = date('H:i:s', $timestampKeluar);

        // Simpan ke Session Sementara
        $session->set('hitung_bayar_' . $id_transaksi, [
            'total_bayar'       => $total_bayar,
            'waktu_keluar_real' => $waktu_keluar_real,
            'durasi'            => $durasiJam,
            'detail_masuk'      => $dataMasuk->waktu_masuk, 
            'detail_keluar'     => $waktu_keluar_real       
        ]);
        
        $session->setFlashdata('perhitungan_berhasil', $id_transaksi); 
        
        // Redirect kembali dengan membawa filter area agar tidak reset
        return redirect()->to('/dashboard/update?area=' . $this->request->getVar('area_redirect')); 
    }

    // --- FUNGSI CHECKOUT (SELESAI) ---
    public function checkout($id_transaksi)
    {
        $transaksiModel = new TransaksiModel();
        $db = \Config\Database::connect();
        $session = session();
        
        $hitungData = $session->get('hitung_bayar_' . $id_transaksi); 
        
        if (empty($hitungData)) {
            $session->setFlashdata('gagal', 'Harap klik tombol Hitung Bayar dulu!');
            return redirect()->to('/dashboard/update');
        }

        $session->remove('hitung_bayar_' . $id_transaksi); 

        // Ambil data untuk update kapasitas
        $trx = $transaksiModel->find($id_transaksi);

        // Update Database Transaksi
        $transaksiModel->update($id_transaksi, [
            'waktu_keluar'      => $hitungData['waktu_keluar_real'],
            'bayar'             => $hitungData['total_bayar'],
            'status_transaksi'  => 'selesai', 
            'id_petugas'        => session()->get('id_petugas') // Catat petugas yang checkout
        ]);

        // Update Kapasitas Area (Slot bertambah/kosong 1)
        // Logika: Kendaraan keluar = Kapasitas terisi berkurang 1
        $db->query("UPDATE status_area SET kapasitas_now = kapasitas_now - 1, jam = ? WHERE id_area = ?", [date('H:i:s'), $trx['id_area']]);

        $session->setFlashdata('berhasil', 'Transaksi Selesai. Total: Rp ' . number_format($hitungData['total_bayar']));
        
        return redirect()->to('/dashboard/update');
    }
}
