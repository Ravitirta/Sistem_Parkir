<?php namespace App\Controllers;

/*
------------------------------
 PENJELASAN KELAS CONTROLLER
------------------------------
 Class Laporan adalah bagian dari arsitektur MVC CodeIgniter 4.
 Controller berfungsi sebagai penghubung antara:
   - View (tampilan)
   - Model (data / database)

 Fungsi Controller:
   1. Menerima permintaan (request) dari pengguna
   2. Memproses input
   3. Memanggil model untuk pengolahan data
   4. Mengirim data ke View untuk ditampilkan

 Keyword "namespace" menunjukkan bahwa class ini berada dalam folder App/Controllers sesuai struktur CI4.
*/
use App\Controllers\BaseController;
use App\Models\TransaksiModel;
use App\Models\AreaModel;

class Laporan extends BaseController
{
    /*
    -----------------
     PROPERTY KELAS
    -----------------
     Property ini menyimpan instance Model agar bisa digunakan oleh semua
     metode di dalam controller.
    
     Fungsi:
       - Efisiensi pemanggilan model
       - Kode lebih rapi (tidak perlu new Model() berulang kali)
    */
    protected $transaksiModel;
    protected $areaModel;

    public function __construct()
    {
        /*
        -------------
         CONSTRUCTOR
        -------------
         Method __construct() berjalan otomatis saat class diinstansiasi.
        
         Fungsinya:
           - Menginisialisasi model-model yang akan digunakan controller ini
        
         Catatan OOP:
           "new TransaksiModel()" artinya membuat object Model
           sehingga controller dapat memanggil fungsinya, contoh:
               $this->transaksiModel->getLaporanBulanan();
        */
        $this->transaksiModel = new TransaksiModel(); 
        $this->areaModel      = new AreaModel();
    }

    /*
    ------------------------------------
     FITUR LAPORAN UTAMA (MENU LAPORAN)
    ------------------------------------
     Method index() berfungsi untuk menampilkan laporan keuangan seperti:
       - laporan harian
       - laporan bulanan
       - rekap pendapatan
       - filter berdasarkan area/bulan/tahun
    */
    public function index()
    {
        // Proteksi akses: hanya user yang login yang boleh melihat laporan
        if (!session()->get('logged_in')) { 
            return redirect()->to('/'); 
        }

        /*
        ------------------------
         MENGAMBIL FILTER INPUT
        ------------------------
         Pengguna bisa memilih:
           - bulan
           - tahun
           - area parkir
        
         Jika tidak diisi, sistem menggunakan nilai default (bulan & tahun saat ini)
        */
        $bulan_pilih = $this->request->getVar('bulan') ?? date('m');
        $tahun_pilih = $this->request->getVar('tahun') ?? date('Y');
        $area_pilih  = $this->request->getVar('area');

        /*
        ---------------------------
         PERSIAPAN DATA UNTUK VIEW
        ---------------------------
         Semua data dikemas ke dalam array $data lalu dikirim ke view wrapper.
        
         View akan menampilkan data sesuai bagian:
           - laporan harian
           - laporan bulanan
           - total pendapatan
        */
        $data = [
            'title'      => 'Laporan Keuangan',
            'isi'        => 'laporan/index',
            'user'       => session()->get('nama'),
            
            // Untuk tampilan filter
            'bulan_ini'  => $bulan_pilih,
            'tahun_ini'  => $tahun_pilih,
            'area_ini'   => $area_pilih,
            'list_area'  => $this->areaModel->findAll(),

            // A. Laporan Harian
            'laporan_harian'  => $this->transaksiModel->getLaporanHarian($area_pilih),
            'rekap_harian'    => $this->transaksiModel->getRekapHarianPerArea($area_pilih),
            
            // B. Laporan Bulanan
            'laporan_bulanan' => 
                $this->transaksiModel->getLaporanBulanan($bulan_pilih, $tahun_pilih, $area_pilih),

            // C. Total Pendapatan
            'total_bulanan'   => 
                $this->transaksiModel->getTotalPendapatanBulanIni($bulan_pilih, $tahun_pilih, $area_pilih)
        ];

        // Mengembalikan tampilan dengan wrapper layout
        return view('layout/wrapper', $data);
    }

    /*
    -------------------------
     FITUR HISTORY TRANSAKSI
    -------------------------
     Menampilkan seluruh transaksi pada periode tertentu.
     Fungsinya sama seperti index(), tetapi hanya fokus pada histori.
    */
    public function history()
    {
        // Cegah akses tanpa login
        if (!session()->get('logged_in')) { 
            return redirect()->to('/'); 
        }

        // Ambil filter bulan/tahun/area
        $bulan_pilih = $this->request->getVar('bulan') ?? date('m');
        $tahun_pilih = $this->request->getVar('tahun') ?? date('Y');
        $area_pilih  = $this->request->getVar('area');

        $data = [
            'title'        => 'History Transaksi',
            'isi'          => 'history/index',
            'user'         => session()->get('nama'),
            
            'bulan_ini'    => $bulan_pilih,
            'tahun_ini'    => $tahun_pilih,
            'area_ini'     => $area_pilih,
            'list_area'    => $this->areaModel->findAll(),

            // Ambil seluruh histori transaksi sesuai filter
            'data_history' => $this->transaksiModel->getHistoryData(
                                $bulan_pilih, $tahun_pilih, $area_pilih
                              )
        ];

        return view('layout/wrapper', $data);
    }

    /*
    ---------------------
     FITUR CETAK LAPORAN
    ---------------------
     Method ini menghasilkan versi cetak (print) laporan transaksi berdasarkan
     rentang tanggal.
    
     Kelebihan:
       - Menggunakan JOIN agar data lebih lengkap:
           * nama area parkir
           * plat nomor pengguna
           * detail transaksi
    */
    public function cetak()
    {
        // Ambil input tanggal rentang
        $tgl_awal  = $this->request->getPost('tgl_awal');
        $tgl_akhir = $this->request->getPost('tgl_akhir');

        /*
        --------------------------------
         QUERY LAPORAN MENGGUNAKAN JOIN
        --------------------------------
         JOIN diperlukan agar:
           - laporan lebih informatif
           - data relasional ditampilkan secara lengkap
        
         Syarat cetak:
           - status_transaksi = 'selesai' (supaya transaksi belum selesai tidak ikut)
        */
        $laporan = $this->transaksiModel
            ->select('transaksi.*, pengguna.plat_nomor, area_parkir.nama_area')
            ->join('pengguna', 'pengguna.id_pengguna = transaksi.id_pengguna', 'left')
            ->join('area_parkir', 'area_parkir.id_area = transaksi.id_area', 'left')
            ->where('tanggal_transaksi >=', $tgl_awal)
            ->where('tanggal_transaksi <=', $tgl_akhir)
            ->where('status_transaksi', 'selesai')
            ->findAll();

        // Siapkan data untuk view cetak
        $data = [
            'title'     => 'Cetak Laporan',
            'laporan'   => $laporan,
            'tgl_awal'  => $tgl_awal,
            'tgl_akhir' => $tgl_akhir,
            'user'      => session()->get('nama')
        ];

        return view('laporan/cetak', $data);
    }
}
