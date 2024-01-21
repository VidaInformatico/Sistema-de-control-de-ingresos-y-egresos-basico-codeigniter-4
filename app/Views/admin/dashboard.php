<?php $this->extend('admin_layout'); ?>

<?= $this->section('content') ?>
<!-- Contenido específico de la página del dashboard se insertará aquí -->
<div class="card">
    <div class="card-body">       

        <div class="row">
            <div class="form-group col-md-3">
                <label for="desde">Desde</label>
                <input id="desde" class="form-control" type="date" name="desde">
            </div>
            <div class="form-group col-md-3">
                <label for="hasta">Hasta</label>
                <input id="hasta" class="form-control" type="date" name="hasta">
            </div>
            <div class="col-md-3 ms-auto">
                <div class="d-grid">
                    <button class="btn btn-danger" type="button" onclick="generarReporte('pdf')">PDF</button>
                    <hr>
                    <button class="btn btn-success" type="button" onclick="generarReporte('excel')">Excel</button>
                </div>
            </div>
        </div>

    </div>
</div>

<hr>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div id="graficoDia"></div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div id="graficoSemana"></div>
            </div>
        </div>
    </div>
</div>

<!-- Enlace oculto por defecto -->

<?= $this->endSection() ?>

<?= $this->section('js') ?>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>

<!-- Script para obtener datos de pedidos por mes y generar el gráfico -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configura los datos para Highcharts
        var ingresos = <?php echo json_encode($ingresos['dia']); ?>;
        var egresos = <?php echo json_encode($egresos['dia']); ?>;

        // Procesa los datos para Highcharts
        var categories = [];
        var ingresosData = [];
        var egresosData = [];

        for (var i = 0; i < ingresos.length; i++) {
            categories.push(ingresos[i].fecha); // Asegúrate de tener un campo 'fecha' en tu modelo
            ingresosData.push(parseFloat(ingresos[i].total_ingresos));
            egresosData.push(null); // Null para no mostrar egresos cuando hay ingresos
        }

        for (var i = 0; i < egresos.length; i++) {
            categories.push(egresos[i].fecha); // Asegúrate de tener un campo 'fecha' en tu modelo
            ingresosData.push(null); // Null para no mostrar ingresos cuando hay egresos
            egresosData.push(parseFloat(egresos[i].total_egresos));
        }

        // Configura el gráfico de Highcharts
        Highcharts.chart('graficoDia', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Ingresos y Egresos por Día'
            },
            xAxis: {
                categories: categories
            },
            yAxis: {
                title: {
                    text: 'Monto'
                }
            },
            series: [{
                name: 'Ingresos',
                data: ingresosData
            }, {
                name: 'Egresos',
                data: egresosData
            }],
            plotOptions: {
                column: {
                    stacking: 'normal'
                }
            }
        });


        // Configura los datos para Highcharts
        var ingresosPorDiaSemana = <?php echo json_encode($ingresos['semana']); ?>;
        var egresosPorDiaSemana = <?php echo json_encode($egresos['semana']); ?>;

        // Procesa los datos para Highcharts
        var categoriesDiaSemana = [];
        var ingresosDataDiaSemana = [];
        var egresosDataDiaSemana = [];

        for (var i = 0; i < ingresosPorDiaSemana.length; i++) {
            categoriesDiaSemana.push(ingresosPorDiaSemana[i].dia_semana);
            ingresosDataDiaSemana.push(parseFloat(ingresosPorDiaSemana[i].total_ingresos));
            egresosDataDiaSemana.push(null);
        }

        for (var i = 0; i < egresosPorDiaSemana.length; i++) {
            categoriesDiaSemana.push(egresosPorDiaSemana[i].dia_semana);
            ingresosDataDiaSemana.push(null);
            egresosDataDiaSemana.push(parseFloat(egresosPorDiaSemana[i].total_egresos));
        }

        // Configura el gráfico de Highcharts
        Highcharts.chart('graficoSemana', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Ingresos y Egresos por Día de la Semana'
            },
            xAxis: {
                categories: categoriesDiaSemana
            },
            yAxis: {
                title: {
                    text: 'Monto'
                }
            },
            series: [{
                name: 'Ingresos',
                data: ingresosDataDiaSemana
            }, {
                name: 'Egresos',
                data: egresosDataDiaSemana
            }],
            plotOptions: {
                column: {
                    stacking: 'normal'
                }
            }
        });
    });

    function generarReporte(tipo) {
        // Obtener los valores de las fechas
        var desde = document.getElementById('desde').value;
        var hasta = document.getElementById('hasta').value;

        // Construir la URL según el tipo de reporte
        var url = '';
        if (tipo === 'pdf') {
            url = 'generar-reporte-pdf?desde=' + desde + '&hasta=' + hasta;
            window.open(url, '_blank');
        } else if (tipo === 'excel') {
            url = 'generar-reporte-excel?desde=' + desde + '&hasta=' + hasta;
            window.location.href = url;
        }


    }
</script>
<?= $this->endSection() ?>