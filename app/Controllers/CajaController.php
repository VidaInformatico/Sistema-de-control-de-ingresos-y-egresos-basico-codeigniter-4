<?php

namespace App\Controllers;

use App\Models\Caja;
use App\Models\Movimiento;

class CajaController extends BaseController
{
    protected $session;
    public function __construct()
    {
        $this->session = \Config\Services::session();
        helper(['form']);
    }
    public function index()
    {
        return view('admin/cajas/index');
    }

    public function new()
    {
        return view('admin/cajas/create');
    }

    public function create()
    {
        $data = [];

        if ($this->request->getMethod() === 'post') {
            $validationRules = [
                'monto_inicial' => 'required',
                'fecha_apertura' => 'required'
            ];

            if ($this->validate($validationRules)) {
                $cajaModel = new Caja();
                // Resto del código para los demás campos
                $data['monto_inicial'] = $this->request->getPost('monto_inicial');
                $data['fecha_apertura'] = $this->request->getPost('fecha_apertura');
                $data['id_usuario'] = $this->session->user_id;
                // Inserta los datos en la base de datos
                $cajaModel->insert($data);

                return redirect()->to('admin/cajas')->with('success', 'Caja creado exitosamente.');
            } else {
                $data['validation'] = $this->validator;
            }
        }
        return view('admin/cajas/create', $data);
    }


    public function edit($id)
    {
        $cajaModel = new Caja();
        $data['caja'] = $cajaModel->find($id);
        return view('admin/cajas/edit', $data);
    }

    public function show($id)
    {
        $cajaModel = new Caja();
        $data['data'] = $cajaModel->findAll();
        return $this->response->setJSON($data);
    }

    public function update($id)
    {
        $data = [];

        $cajaModel = new Caja();
        $caja = $cajaModel->find($id);

        // Si el formulario se ha enviado
        if ($this->request->getMethod() === 'put') {
            // Validación de reglas
            $validationRules = [
                'monto_inicial' => 'required',
                'fecha_apertura' => 'required'
            ];

            // Establecer reglas de validación
            if ($this->validate($validationRules)) {
                $data = [
                    'monto_inicial' => $this->request->getPost('monto_inicial'),
                    'fecha_apertura' => $this->request->getPost('fecha_apertura')
                ];

                $cajaModel->update($id, $data);

                return redirect()->to('admin/cajas')->with('success', 'Caja modificado exitosamente.');
            } else {
                // La validación falló, vuelve a cargar la vista con los errores
                $data['validation'] = $this->validator;
            }
            $data['caja'] = $caja;
            return view('admin/cajas/edit', $data);
        }
    }

    public function cerrar()
    {
        $data = [];

        $cajaModel = new Caja();
        $caja = $cajaModel->where([
            'estado' => 1,
            'id_usuario' => $this->session->user_id
        ])->first();

        // Si el formulario se ha enviado
        if ($this->request->getMethod() === 'post' && !empty($caja)) {

            $movimientoModel = new Movimiento();
            $totalIngresos = $movimientoModel->selectSum('monto')
                ->where(['movimiento' => 1, 'id_caja' => $caja['id'], 'id_usuario' => $this->session->user_id])
                ->first();

            $totalEgresos = $movimientoModel->selectSum('monto')
                ->where(['movimiento' => 2, 'id_caja' => $caja['id'], 'id_usuario' => $this->session->user_id])
                ->first();

            $egresos = ($totalEgresos['monto'] == null) ? 0 : $totalEgresos['monto'];
            $ingresos = ($totalIngresos['monto'] == null) ? 0 : $totalIngresos['monto'];
            $data = [
                'fecha_cierre' => date('Y-m-d H:i:s'),
                'egresos' => $egresos,
                'ingresos' => $ingresos,
                'estado' => 0,
            ];

            $cajaModel->update($caja['id'], $data);

            return redirect()->to('admin/cajas')->with('success', 'Caja modificado exitosamente.');
        }
    }

    public function cierre()
    {
        $cajaModel = new Caja();

        $data['caja'] = $cajaModel->where([
            'estado' => 1,
            'id_usuario' => $this->session->user_id
        ])->first();

        if (empty($data['caja'])) {
            return redirect()->to('admin/cajas')->with('error', 'La caja esta cerrada.');
        }
        $movimientoModel = new Movimiento();
        $data['ingresos'] = $movimientoModel->selectSum('monto')
            ->where(['movimiento' => 1, 'id_caja' => $data['caja']['id'], 'id_usuario' => $this->session->user_id])
            ->first();

        $data['egresos'] = $movimientoModel->selectSum('monto')
            ->where(['movimiento' => 2, 'id_caja' => $data['caja']['id'], 'id_usuario' => $this->session->user_id])
            ->first();
        return view('admin/cajas/cierre', $data);
    }
}
