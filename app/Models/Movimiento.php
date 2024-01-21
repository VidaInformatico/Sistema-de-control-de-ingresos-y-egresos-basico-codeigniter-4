<?php

namespace App\Models;

use CodeIgniter\Model;

class Movimiento extends Model
{
    protected $table = 'movimientos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['monto', 'descripcion', 'estado', 'imagen', 'fecha', 'movimiento', 'id_caja', 'id_comprobante', 'id_usuario'];
}