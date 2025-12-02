<?php namespace App\Models;

use CodeIgniter\Model;

class KendaraanModel extends Model
{
    protected $table = 'kendaraan';
    protected $primaryKey = 'id_kendaraan';
    protected $allowedFields = ['id_kendaraan', 'jenis_kendaraan', 'harga_perjam'];
}
