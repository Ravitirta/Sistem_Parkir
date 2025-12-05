<?php namespace App\Models;

use CodeIgniter\Model;

class PenggunaModel extends Model
{
    // Menggunakan nama tabel 'pengguna' sesuai kode Anda
    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';
    protected $allowedFields = ['id_pengguna', 'plat_nomor', 'merk', 'id_kendaraan'];
    
    // Pastikan $primaryKey menggunakan format string ('PG_001')
    protected $useAutoIncrement = false; 
    protected $returnType = 'array';
}