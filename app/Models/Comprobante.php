<?php

namespace App\Models;

use CodeIgniter\Model;

class Comprobante extends Model
{
    protected $table = 'comprobantes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['codigo', 'nombre']; // Ajusta según tu estructura de base de datos
}