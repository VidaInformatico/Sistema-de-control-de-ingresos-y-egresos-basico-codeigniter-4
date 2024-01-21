<?php

namespace App\Controllers;

use App\Models\Caja;
use App\Models\Comprobante;
use App\Models\Configuracion;
use App\Models\Movimiento;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use TCPDF;

class MovimientoController extends BaseController
{
    protected $session;
    public function __construct()
    {
        $this->session = \Config\Services::session();
        helper(['form']);
    }
    public function index()
    {
        return view('admin/movimientos/index');
    }

    public function new()
    {
        $movimientoModel = new Comprobante();
        $data['comprobantes'] = $movimientoModel->findAll();
        return view('admin/movimientos/create', $data);
    }

    public function create()
    {
        $data = [];

        if ($this->request->getMethod() === 'post') {
            $validationRules = [
                'monto' => 'required',
                'descripcion' => 'required',
                'comprobante' => 'required',
                'radio-switch-name' => 'required',
            ];

            if ($this->validate($validationRules)) {
                $movimientoModel = new Movimiento();
                $cajaModel = new Caja();
                $caja = $cajaModel->where(['estado' => 1, 'id_usuario' => $this->session->user_id])->first();
                if (empty($caja)) {
                    return redirect()->to('admin/movimientos')->with('error', 'La caja esta cerrada.');
                } else {
                    if ($this->request->getPost('radio-switch-name') == 2) {
                        //COMPROBAR SALDO
                        $consultaIngresos = $movimientoModel->selectSum('monto')
                            ->where(['movimiento' => 1, 'id_caja' => $caja['id'], 'id_usuario' => $this->session->user_id])
                            ->first();

                        $consultaEgresos = $movimientoModel->selectSum('monto')
                            ->where(['movimiento' => 2, 'id_caja' => $caja['id'], 'id_usuario' => $this->session->user_id])
                            ->first();

                        $saldo = ($consultaIngresos['monto'] + $caja['monto_inicial']) - $consultaEgresos['monto'];
                        if ($saldo < $this->request->getPost('monto')) {
                            return redirect()->to('admin/movimientos')->with('error', 'Saldo disponible: ' . $saldo);
                        }
                    }
                    $data = [
                        'monto' => $this->request->getPost('monto'),
                        'descripcion' => $this->request->getPost('descripcion'),
                        'fecha' => $this->request->getPost('fecha'),
                        'movimiento' => $this->request->getPost('radio-switch-name'),
                        'id_caja' => $caja['id'],
                        'id_comprobante' => $this->request->getPost('comprobante'),
                        'id_usuario' => $this->session->user_id,
                    ];

                    $movimientoModel->insert($data);

                    return redirect()->to('admin/movimientos')->with('success', 'Movimiento creada exitosamente.');
                }
            } else {
                $data['validation'] = $this->validator;
            }
        }

        return view('admin/movimientos/create', $data);
    }

    public function edit($id)
    {
        $movimientoModel = new Movimiento();
        $data['movimiento'] = $movimientoModel->find($id);
        $movimientoModel = new Comprobante();
        $data['comprobantes'] = $movimientoModel->findAll();

        return view('admin/movimientos/edit', $data);
    }

    public function show($id)
    {
        $movimientoModel = new Movimiento();
        if ($this->session->rol == 'admin') {
            $data['data'] = $movimientoModel->select('movimientos.*, c.nombre AS comprobante, u.usuario')
                ->join('comprobantes AS c', 'movimientos.id_comprobante = c.id')
                ->join('usuarios AS u', 'movimientos.id_usuario = u.id')
                ->where('movimientos.estado', 1)
                ->findAll();
        } else {
            $data['data'] = $movimientoModel->select('movimientos.*, c.nombre AS comprobante, u.usuario')
                ->join('comprobantes AS c', 'movimientos.id_comprobante = c.id')
                ->join('usuarios AS u', 'movimientos.id_usuario = u.id')
                ->where(['movimientos.estado' => 1, 'movimientos.id_usuario' => $this->session->user_id])
                ->findAll();
        }

        return $this->response->setJSON($data);
    }

    public function update($id)
    {
        $data = [];

        $movimientoModel = new Movimiento();
        $comprobante = $movimientoModel->find($id);

        // Si el formulario se ha enviado
        if ($this->request->getMethod() === 'put') {
            // Validación de reglas
            $validationRules = [
                'monto' => 'required',
                'descripcion' => 'required',
                'comprobante' => 'required',
                'radio-switch-name' => 'required',
            ];

            // Establecer reglas de validación
            if ($this->validate($validationRules)) {
                $cajaModel = new Caja();
                $caja = $cajaModel->where(['estado' => 1, 'id_usuario' => $this->session->user_id])->first();
                if (empty($caja)) {
                    return redirect()->to('admin/movimientos')->with('error', 'La caja esta cerrada.');
                } else {
                    if ($this->request->getPost('radio-switch-name') == 2) {
                        //COMPROBAR SALDO
                        $consultaIngresos = $movimientoModel->selectSum('monto')
                            ->where(['movimiento' => 1, 'id_caja' => $caja['id'], 'id_usuario' => $this->session->user_id])
                            ->first();

                        $consultaEgresos = $movimientoModel->selectSum('monto')
                            ->where(['movimiento' => 2, 'id_caja' => $caja['id'], 'id_usuario' => $this->session->user_id])
                            ->first();

                        //MONTO ANTERIOR
                        $anterior = $movimientoModel->find($id);

                        $saldo = ($consultaIngresos['monto'] + $caja['monto_inicial']) - ($consultaEgresos['monto'] - $anterior['monto']);
                        if ($saldo < $this->request->getPost('monto')) {
                            return redirect()->to('admin/movimientos')->with('error', 'Saldo disponible: ' . ($saldo - $anterior['monto']));
                        }
                    }

                    $data = [
                        'monto' => $this->request->getPost('monto'),
                        'descripcion' => $this->request->getPost('descripcion'),
                        'fecha' => $this->request->getPost('fecha'),
                        'movimiento' => $this->request->getPost('radio-switch-name'),
                        'id_comprobante' => $this->request->getPost('comprobante'),
                    ];

                    $movimientoModel->update($id, $data);

                    return redirect()->to('admin/movimientos')->with('success', 'Movimiento modificada exitosamente.');
                }
            } else {
                // La validación falló, vuelve a cargar la vista con los errores
                $data['validation'] = $this->validator;
            }

            $data['comprobante'] = $comprobante;
            return view('admin/movimientos/edit', $data);
        }
    }

    public function delete($id)
    {
        $movimientoModel = new Movimiento();

        $data = $movimientoModel->delete($id);

        if ($data) {
            $res = ['msg' => 'REGISTRO ELIMINADO', 'tipo' => 'success'];
        } else {
            $res = ['msg' => 'ERROR AL ELIMINAR', 'tipo' => 'error'];
        }

        return $this->response->setJSON($res);
    }

    public function generarReporteExcel()
    {
        $movimientoModel = new Movimiento();
        if (empty($_GET['desde']) && empty($_GET['hasta'])) {
            $movimientos = $movimientoModel
                ->where('movimientos.id_usuario', $this->session->user_id)
                ->join('usuarios', 'usuarios.id = movimientos.id_usuario')
                ->orderBy('movimientos.id', 'desc')
                ->findAll();
        } else {
            $desde = $_GET['desde'] . ' 00:00:00';
            $hasta = $_GET['hasta'] . ' 23:23:59';
            $movimientos = $movimientoModel
                ->where('movimientos.id_usuario', $this->session->user_id)
                ->join('usuarios', 'usuarios.id = movimientos.id_usuario')
                ->where('movimientos.fecha >=', $desde)
                ->where('movimientos.fecha <=', $hasta)
                ->orderBy('movimientos.id', 'desc')
                ->findAll();
        }

        // Crear un objeto Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Añadir título
        $titulo = "Reporte de Movimientos";
        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A1', $titulo);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // Añadir encabezados de columna
        $columnas = ['monto', 'descripcion', 'fecha', 'movimiento', 'usuario'];
        foreach ($columnas as $key => $columna) {
            $celda = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($key + 1) . '2';
            $sheet->setCellValue($celda, $columna);

            // Personalizar encabezado con color
            $sheet->getStyle($celda)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('005BF9');
            // Establecer color blanco para el texto
            $sheet->getStyle($celda)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE));
        }

        // Ajustar ancho de columnas
        foreach ($columnas as $key => $columna) {
            $celda = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($key + 1);
            $sheet->getColumnDimension($celda)->setWidth(15);
        }

        // Añadir datos al archivo Excel
        $fila = 3;
        foreach ($movimientos as $movimiento) {
            foreach ($columnas as $key => $columna) {
                $celda = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($key + 1) . $fila;

                // Validar la columna "movimiento"
                if ($columna == 'movimiento') {
                    $valor = ($movimiento[$columna] == 1) ? 'Ingreso' : 'Egreso';
                } else {
                    $valor = $movimiento[$columna];
                }

                $sheet->setCellValue($celda, $valor);
            }
            // Colorear toda la fila según el tipo de movimiento
            $colorFondo = ($movimiento['movimiento'] == 1) ? 'C3F7A9' : 'F7C3A9'; // Verde para Ingresos, Rojo para Egresos
            $sheet->getStyle('A' . $fila . ':E' . $fila)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($colorFondo);
            $fila++;
        }

        // Guardar el archivo Excel
        $writer = new Xlsx($spreadsheet);
        $archivo = WRITEPATH . 'uploads/reporte_movimientos.xlsx';
        $writer->save($archivo);

        // Descargar el archivo
        return $this->response->download($archivo, null);
    }
    public function generarReportePdf()
    {
        $movimientoModel = new Movimiento();
        if (empty($_GET['desde']) && empty($_GET['hasta'])) {
            $movimientos = $movimientoModel
                ->where('movimientos.id_usuario', $this->session->user_id)
                ->join('usuarios', 'usuarios.id = movimientos.id_usuario')
                ->orderBy('movimientos.id', 'desc')
                ->findAll();
        } else {
            $desde = $_GET['desde'] . ' 00:00:00';
            $hasta = $_GET['hasta'] . ' 23:23:59';
            $movimientos = $movimientoModel
                ->where('movimientos.id_usuario', $this->session->user_id)
                ->join('usuarios', 'usuarios.id = movimientos.id_usuario')
                ->where('movimientos.fecha >=', $desde)
                ->where('movimientos.fecha <=', $hasta)
                ->orderBy('movimientos.id', 'desc')
                ->findAll();
        }
        $datos = new Configuracion();
        $empresa = $datos->first();

        // Crear un objeto PDF
        $pdf = new TCPDF();

        //

        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // Agregar una nueva página
        $pdf->AddPage();
        $pdf->SetMargins(5, 0, 5); // Izquierda, superior, derecha
        // Añadir información de la empresa
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 5, $empresa['nombre_comercial'], 0, 1, 'C');
        $pdf->Cell(0, 5, $empresa['direccion'], 0, 1, 'C');
        $pdf->Cell(0, 5, $empresa['telefono'], 0, 1, 'C');
        $pdf->Cell(0, 5, $empresa['correo'], 0, 1, 'C');
        $pdf->Ln(5); // Espacio después de la información de la empresa

        // Establecer el título
        $titulo = "Reporte de Movimientos";
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, $titulo, 0, 1, 'C');
        $pdf->Ln(5); // Espacio después del título

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetFillColor(5, 91, 249); // Establecer color de fondo para encabezado
        $pdf->SetTextColor(255, 255, 255); // Establecer color de texto blanco para encabezado
        $pdf->Cell(20, 10, 'Monto', 1, 0, 'C', 1);
        $pdf->Cell(65, 10, 'Descripción', 1, 0, 'C', 1);
        $pdf->Cell(45, 10, 'Fecha', 1, 0, 'C', 1);
        $pdf->Cell(30, 10, 'Movimiento', 1, 0, 'C', 1);
        $pdf->Cell(40, 10, 'Usuario', 1, 1, 'C', 1);

        // Añadir datos al archivo PDF
        foreach ($movimientos as $movimiento) {
            // Validar la columna "movimiento"
            $tipoMovimiento = ($movimiento['movimiento'] == 1) ? 'Ingreso' : 'Egreso';
            $colorFondo = ($movimiento['movimiento'] == 1) ? [195, 247, 169] : [247, 195, 169]; // Verde para Ingresos, Rojo para Egresos
            $pdf->SetFillColorArray($colorFondo);

            // Añadir encabezados de columna
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(20, 10, $movimiento['monto'], 1, 0, 'C', 1);
            $pdf->Cell(65, 10, $movimiento['descripcion'], 1, 0, 'L', 1);
            $pdf->Cell(45, 10, $movimiento['fecha'], 1, 0, 'C', 1);
            $pdf->Cell(30, 10, $tipoMovimiento, 1, 0, 'C', 1);
            $pdf->Cell(40, 10, $movimiento['nombre'], 1, 0, 'C', 1);

            $pdf->Ln(); // Nueva línea después de cada fila
        }

        // Establecer el color del texto del contenido (no encabezado) a negro
        $pdf->SetTextColor(0, 0, 0);

        // Establecer el tipo de contenido como PDF
        $this->response->setHeader('Content-Type', 'application/pdf');

        // Salida directa a una pestaña del navegador
        $pdf->Output('reporte_movimientos.pdf', 'I');
    }
}
// $this->response->setHeader('Content-Type', 'application/pdf');