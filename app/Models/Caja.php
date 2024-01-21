<?php

namespace App\Models;

use CodeIgniter\Model;

class Caja extends Model
{
    protected $table = 'cajas';
    protected $primaryKey = 'id';
    protected $allowedFields = ['monto_inicial', 'fecha_apertura', 'fecha_cierre', 'ingresos', 'egresos', 'id_usuario']; // Ajusta según tu estructura de base de datos
}