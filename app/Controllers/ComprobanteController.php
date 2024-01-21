<?php

namespace App\Controllers;

use App\Models\Comprobante;

class ComprobanteController extends BaseController
{
    protected $session;
    public function __construct()
    {
        $this->session = \Config\Services::session();
        helper(['form']);
    }
    public function index()
    {
        return view('admin/comprobantes/index');
    }

    public function new()
    {
        return view('admin/comprobantes/create');
    }

    public function create()
    {
        $data = [];

        if ($this->request->getMethod() === 'post') {
            $validationRules = [
                'codigo' => 'required|min_length[3]|is_unique[comprobantes.nombre]',
                'nombre' => 'required'
            ];

            if ($this->validate($validationRules)) {
                $comprobanteModel = new Comprobante();

                    $data = [
                        'nombre' => $this->request->getPost('nombre'),
                        'codigo' => $this->request->getPost('codigo')
                    ];

                    $comprobanteModel->insert($data);

                    return redirect()->to('admin/comprobantes')->with('success', 'Comprobante creada exitosamente.');

            } else {
                $data['validation'] = $this->validator;
            }
        }

        return view('admin/comprobantes/create', $data);
    }

    public function edit($id)
    {
        $comprobanteModel = new Comprobante();
        $data['comprobante'] = $comprobanteModel->find($id);

        return view('admin/comprobantes/edit', $data);
    }

    public function show($id)
    {
        $comprobanteModel = new Comprobante();
        $data['data'] = $comprobanteModel->findAll();
        return $this->response->setJSON($data);
    }

    public function update($id)
    {
        $data = [];

        $comprobanteModel = new Comprobante();
        $comprobante = $comprobanteModel->find($id);

        // Si el formulario se ha enviado
        if ($this->request->getMethod() === 'put') {
            // Validación de reglas
            $validationRules = [
                'codigo' => 'required|min_length[3]|is_unique[comprobantes.codigo,id,' . $id . ']',
                'nombre' => 'required'
            ];

            // Establecer reglas de validación
            if ($this->validate($validationRules)) {
                $data = [
                    'codigo' => $this->request->getPost('codigo'),
                    'nombre' => $this->request->getPost('nombre'),
                ];

                $comprobanteModel->update($id, $data);

                return redirect()->to('admin/comprobantes')->with('success', 'Comprobante modificada exitosamente.');
            } else {
                // La validación falló, vuelve a cargar la vista con los errores
                $data['validation'] = $this->validator;
            }

            $data['comprobante'] = $comprobante;
            return view('admin/comprobantes/edit', $data);
        }
    }

    public function delete($id)
    {
        $comprobanteModel = new Comprobante();

        $data = $comprobanteModel->delete($id);

        if ($data) {
            $res = ['msg' => 'REGISTRO ELIMINADO', 'tipo' => 'success'];
        } else {
            $res = ['msg' => 'ERROR AL ELIMINAR', 'tipo' => 'error'];
        }

        return $this->response->setJSON($res);
    }
}
