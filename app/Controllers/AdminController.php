<?php

namespace App\Controllers;

use App\Models\DetallePedido;
use App\Models\Movimiento;
use App\Models\Pedido;

class AdminController extends BaseController
{
    protected $session;
    public function __construct()
    {
        $this->session = \Config\Services::session();
    }
    public function index()
    {

        $movimientoModel = new Movimiento();
        // Obtén la fecha actual en formato 'Y-m-d'
        $currentDate = date('Y-m-d');

        // Establece la hora inicial y final del día actual
        $startDate = $currentDate . ' 00:00:00';
        $endDate = $currentDate . ' 23:59:59';

        // Consulta para obtener el total de ingresos por día (agrupado por año, mes y día)
        $totalIngresos = $movimientoModel
            ->select('DATE(fecha) as fecha, SUM(monto) as total_ingresos')
            ->where('movimiento', 1)
            ->where('id_usuario', $this->session->user_id)
            ->where('fecha >=', $startDate)
            ->where('fecha <=', $endDate)
            ->groupBy('DATE(fecha)')
            ->findAll();

        // Consulta para obtener el total de egresos por día (agrupado por año, mes y día)
        $totalEgresos = $movimientoModel
            ->select('DATE(fecha) as fecha, SUM(monto) as total_egresos')
            ->where('movimiento', 2)
            ->where('id_usuario', $this->session->user_id)
            ->where('fecha >=', $startDate)
            ->where('fecha <=', $endDate)
            ->groupBy('DATE(fecha)')
            ->findAll();

        // Obtén el día de la semana actual (0 = domingo, 1 = lunes, ..., 6 = sábado)
        $currentDayOfWeek = date('w');

        // Calcula el primer día de la semana (domingo) restando el día de la semana actual
        $firstDayOfWeek = date('Y-m-d', strtotime("-$currentDayOfWeek days", strtotime($currentDate)));

        // Establece la hora inicial y final del primer día de la semana
        $startDate = $firstDayOfWeek . ' 00:00:00';

        // Consulta para obtener el total de ingresos por día de la semana
        $totalIngresosPorDiaSemana = $movimientoModel
            ->select('DAYNAME(fecha) as dia_semana, SUM(monto) as total_ingresos')
            ->where('movimiento', 1)
            ->where('id_usuario', $this->session->user_id)
            ->where('fecha >=', $startDate)
            ->where('fecha <=', $endDate)
            ->groupBy('DAYNAME(fecha)')
            ->orderBy('FIELD(dia_semana, "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday")')
            ->findAll();

        // Consulta para obtener el total de egresos por día de la semana
        $totalEgresosPorDiaSemana = $movimientoModel
            ->select('DAYNAME(fecha) as dia_semana, SUM(monto) as total_egresos')
            ->where('movimiento', 2)
            ->where('id_usuario', $this->session->user_id)
            ->where('fecha >=', $startDate)
            ->where('fecha <=', $endDate)
            ->groupBy('DAYNAME(fecha)')
            ->orderBy('FIELD(dia_semana, "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday")')
            ->findAll();        
        
        $data['ingresos'] = [
            'dia' => $totalIngresos,
            'semana' => $totalIngresosPorDiaSemana
        ];
        $data['egresos'] = [
            'dia' => $totalEgresos,
            'semana' => $totalEgresosPorDiaSemana
        ];

        $data['active'] = 'dashboard';

        return view('admin/dashboard', $data);
    }
}
