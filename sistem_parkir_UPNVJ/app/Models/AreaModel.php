<?php namespace App\Models;

use CodeIgniter\Model;

class AreaModel extends Model
{
    protected $table = 'area_parkir';
    protected $primaryKey = 'id_area';
    protected $allowedFields = ['id_area', 'nama_area'];
}
