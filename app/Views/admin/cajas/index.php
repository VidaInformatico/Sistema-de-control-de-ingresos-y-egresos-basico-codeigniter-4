<?= $this->extend('admin_layout') ?>

<?= $this->section('content') ?>

<a href="<?= base_url('admin/cajas/new'); ?>" class="btn btn-primary mb-3">Nuevo Caja</a>
<a href="<?= base_url('admin/cajas/cierre') ?>" class="btn btn-info mb-3">Cerrar Caja</a>
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Lista de Cajas</h5>
        <hr>
        <?php $successMessage = session()->getFlashdata('success'); ?>
        <?php if ($successMessage) : ?>
            <div class="alert alert-success">
                <?= esc($successMessage) ?>
            </div>
        <?php endif; ?>
        <div class="table-responsive">
            <table class="table nowrap" id="cajaTable" width="100%">
                <thead>
                    <tr>
                        <th>Acciones</th>
                        <th>ID</th>
                        <th>Monto Inicial</th>
                        <th>Fecha Apertura</th>
                        <th>Fecha Cierre</th>
                        <th>Egresos</th>
                        <th>Ingresos</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    let cajaTable;
    $(document).ready(function() {
        cajaTable = $('#cajaTable').DataTable({
            responsive: true,
            "ajax": "<?= base_url('admin/cajas/show'); ?>",
            "columns": [{
                    "data": null,
                    "render": function(data, type, row) {
                        return '<a href="<?= base_url('admin/cajas'); ?>/' + row.id + '/edit" class="btn btn-warning">Editar</a>' +
                            '<a href="<?= base_url('admin/cajas'); ?>/' + row.id + '/pdf" class="btn btn-danger">PDF</a>';
                    }
                },
                {
                    "data": "id"
                },
                {
                    "data": "monto_inicial"
                },
                {
                    "data": "fecha_apertura"
                },
                {
                    "data": "fecha_cierre"
                },
                {
                    "data": "egresos"
                },
                {
                    "data": "ingresos"
                },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        return (parseFloat(data.monto_inicial) + parseFloat(data.ingresos)
                         - parseFloat(data.egresos)).toFixed(2);
                    }
                }

            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
            },
        });
    });

    function confirmDelete(idUser) {
        Swal.fire({
            title: "Esta seguro de eliminar?",
            text: "¡No podrás revertir esto!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "¡Sí, bórralo!"
        }).then((result) => {
            if (result.isConfirmed) {
                let url = '<?php echo base_url('admin/cajas/'); ?>' + idUser;
                let data = new FormData();
                data.append('_method', 'DELETE');
                //hacer una instancia del objeto XMLHttpRequest 
                const http = new XMLHttpRequest();
                //Abrir una Conexion - POST - GET
                http.open('POST', url, true);
                //Enviar Datos
                http.send(data);
                //verificar estados
                http.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        const res = JSON.parse(this.responseText);
                        Swal.fire({
                            toast: true,
                            position: 'top-right',
                            icon: res.tipo,
                            title: res.msg,
                            showConfirmButton: false,
                            timer: 2000
                        })
                        if (res.tipo == 'success') {
                            cajaTable.ajax.reload();
                        }
                    }
                }
            }
        });
    }
</script>
<?= $this->endSection() ?>