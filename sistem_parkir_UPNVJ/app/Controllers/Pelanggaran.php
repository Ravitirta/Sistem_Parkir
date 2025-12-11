<?php namespace App\Controllers;

use App\Models\PelanggaranModel;
use App\Models\AreaModel;

class Pelanggaran extends BaseController
{
    // 1. HALAMAN UTAMA (Daftar Pelanggaran Valid)
    public function index()
    {
        // Panggil Model (Pastikan sudah membuat PelanggaranModel.php)
        $model = new PelanggaranModel();
        
        $data = [
            'title' => 'Laporan Pelanggaran',
            'isi'   => 'pelanggaran/index', // View daftar
            'data_valid' => $model->getValid() // Ambil data yang sudah di-ACC
        ];
        
        return view('layout/wrapper', $data);
    }

    // 2. HALAMAN INPUT (Form Upload)
    public function lapor()
    {
        $areaModel = new AreaModel();
        $data = [
            'title' => 'Input Laporan',
            'isi'   => 'pelanggaran/input', // View form input
            'area'  => $areaModel->findAll()
        ];
        return view('layout/wrapper', $data);
    }

    // 3. PROSES SIMPAN FOTO (Upload)
    public function simpanLaporan()
    {
        $model = new PelanggaranModel();
        
        // Ambil file foto dari form
        $fileFoto = $this->request->getFile('foto');
        
        // Cek validitas file
        if ($fileFoto->isValid() && ! $fileFoto->hasMoved()) {
            // Generate nama unik (random) supaya tidak bentrok
            $namaFoto = $fileFoto->getRandomName();
            // Pindahkan file ke folder 'public/uploads/'
            $fileFoto->move('uploads', $namaFoto);
        } else {
            return redirect()->back()->with('gagal', 'Gagal upload gambar.');
        }

        // Simpan data ke database
        $model->save([
            'tanggal'    => $this->request->getPost('tanggal'),
            'id_area'    => $this->request->getPost('id_area'),
            'keterangan' => $this->request->getPost('keterangan'),
            'foto'       => $namaFoto,
            'status'     => 'pending' // Default pending (tunggu admin untuk verifikasi)
        ]);

        return redirect()->to('/pelanggaran')->with('sukses', 'Laporan berhasil dikirim! Menunggu verifikasi petugas.');
    }

    // 4. HALAMAN KELOLA (Khusus Admin/Petugas)
    public function manage()
    {
        // Cek Login Manual (Security Layer)
        if(!session()->get('logged_in')){
            return redirect()->to('/auth');
        }

        $model = new PelanggaranModel();
        $data = [
            'title' => 'Verifikasi Laporan',
            'isi'   => 'pelanggaran/manage', // View tabel admin
            'data_pending' => $model->getPending() // Ambil data pending
        ];
        return view('layout/wrapper', $data);
    }

    // 5. PROSES VERIFIKASI (Terima/Tolak)
    public function verifikasi($id, $status)
    {
        if(!session()->get('logged_in')){ return redirect()->to('/auth'); }

        $model = new PelanggaranModel();
        
        // Update status jadi 'valid' atau 'invalid'
        $model->update($id, ['status' => $status]);
        
        $pesan = ($status == 'valid') ? 'Laporan Diterima.' : 'Laporan Ditolak.';
        
        return redirect()->to('/pelanggaran/manage')->with('sukses', $pesan);
    }

    // 6. FUNGSI HAPUS (Hapus Data Database & File Gambar)
    public function hapus($id)
    {
        // Security untuk Cek Login
        if(!session()->get('logged_in')){ 
            return redirect()->to('/auth'); 
        }

        $model = new PelanggaranModel();
        
        // A. Cari data laporan berdasarkan ID terlebih dulu
        $laporan = $model->find($id);

        if ($laporan) {
            // B. Hapus File Gambar Fisik di folder 'uploads'
            // Melakukan pengecekan apakah filenya sudah ada atau belum supaya tidak error
            $pathGambar = 'uploads/' . $laporan['foto'];
            
            if (file_exists($pathGambar)) {
                unlink($pathGambar); // 'unlink' adalah perintah PHP untuk menghapus file dari harddisk
            }

            // C. Hapus Data di Database
            $model->delete($id);
            
            return redirect()->to('/pelanggaran')->with('sukses', 'Laporan dan foto berhasil dihapus permanen.');
        } else {
            return redirect()->to('/pelanggaran')->with('gagal', 'Data tidak ditemukan.');
        }
    }
}
