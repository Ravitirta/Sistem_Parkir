<?php namespace App\Models;

use CodeIgniter\Model;

class TransaksiModel extends Model
{
    protected $table      = 'transaksi';      // Nama tabel di database
    protected $primaryKey = 'id_transaksi';   // Primary Key

    // Daftar kolom yang BOLEH diisi oleh sistem (Security)
    protected $allowedFields = [
        'id_transaksi', 
        'tanggal_transaksi', 
        'waktu_masuk', 
        'waktu_keluar', 
        'id_area', 
        'id_pengguna', 
        'id_kendaraan',
        'id_petugas', 
        'bayar', 
        'status_transaksi'
    ];
}