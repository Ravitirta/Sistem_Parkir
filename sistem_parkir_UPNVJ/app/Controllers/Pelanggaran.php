<?php namespace App\Controllers;

use App\Models\PelanggaranModel;
use App\Models\AreaModel;

class Pelanggaran extends BaseController
{
    // HALAMAN UTAMA (Galeri Pelanggaran)
    public function index()
    {
        $model = new PelanggaranModel();
        
        $data = [
            'title' => 'Laporan Pelanggaran',
            'isi'   => 'pelanggaran/index', 
            'data_valid' => $model->getValid()
        ];
        
        return view('layout/wrapper', $data);
    }

    // HALAMAN INPUT (Lapor Baru)
    public function lapor()
    {
        $areaModel = new AreaModel();
        $data = [
            'title' => 'Input Laporan',
            'isi'   => 'pelanggaran/input',
            'area'  => $areaModel->findAll()
        ];
        return view('layout/wrapper', $data);
    }

    // PROSES SIMPAN FOTO (Upload)
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
            'status'     => 'pending' 
        ]);

        return redirect()->to('/pelanggaran')->with('sukses', 'Laporan berhasil dikirim! Menunggu verifikasi petugas.');
    }

    // HALAMAN KELOLA (Khusus Admin/Petugas)
    public function manage()
    {
        if(!session()->get('logged_in')){
            return redirect()->to('/auth');
        }

        $model = new PelanggaranModel();
        $data = [
            'title' => 'Verifikasi Laporan',
            'isi'   => 'pelanggaran/manage', 
            'data_pending' => $model->getPending()
        ];
        return view('layout/wrapper', $data);
    }

    // PROSES VERIFIKASI (Terima/Tolak)
    public function verifikasi($id, $status)
    {
        if(!session()->get('logged_in')){ return redirect()->to('/auth'); }

        $model = new PelanggaranModel();
        
        $model->update($id, ['status' => $status]);
        
        $pesan = ($status == 'valid') ? 'Laporan Diterima.' : 'Laporan Ditolak.';
        
        return redirect()->to('/pelanggaran/manage')->with('sukses', $pesan);
    }

    // FUNGSI HAPUS (Hapus Data Database & File Gambar)
    public function hapus($id)
    {
        if(!session()->get('logged_in')){ 
            return redirect()->to('/auth'); 
        }

        $model = new PelanggaranModel();
        
        // Cari data laporan berdasarkan ID
        $laporan = $model->find($id);

        if ($laporan) {
            $pathGambar = 'uploads/' . $laporan['foto'];
            
            if (file_exists($pathGambar)) {
                unlink($pathGambar); 
            }

            // Hapus Data di Database
            $model->delete($id);
            
            return redirect()->to('/pelanggaran')->with('sukses', 'Laporan dan foto berhasil dihapus permanen.');
        } else {
            return redirect()->to('/pelanggaran')->with('gagal', 'Data tidak ditemukan.');
        }
    }
}
