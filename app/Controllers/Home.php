<?php

namespace App\Controllers;

use App\Models\Categoria;
use App\Models\Configuracion;
use App\Models\Producto;
use App\Models\Slider;

class Home extends BaseController
{
    public function index(): string
    {
        $data['register'] = false;
        return view('admin/login', $data);
    }
}
