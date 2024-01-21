<?= $this->extend('admin_layout') ?>

<?= $this->section('content') ?>

<a href="<?= base_url('admin/movimientos/new') ?>" class="btn btn-primary mb-3"><i class="fas fa-plus"></i></a>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Lista de Movimientos</h5>
        <hr>
        <?php $successMessage = session()->getFlashdata('success'); 
        $errorMessage = session()->getFlashdata('error'); 
        ?>
        <?php if ($successMessage) : ?>
            <div class="alert alert-success">
                <?= esc($successMessage) ?>
            </div>
        <?php endif; ?>
        <?php if ($errorMessage) : ?>
            <div class="alert alert-danger">
                <?= esc($errorMessage) ?>
            </div>
        <?php endif; ?>
        <div class="table-responsive">
            <table class="table nowrap" id="moviTable" width="100%">
                <thead>
                    <tr>
                        <th>Acciones</th>
                        <th>ID</th>
                        <th>Movimiento</th>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Usuario</th>
                        <th>Comprobante</th>
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
    let moviTable;
    $(document).ready(function() {
        moviTable = $('#moviTable').DataTable({
            responsive: true,
            "ajax": "<?= base_url('admin/movimientos/show'); ?>",
            "columns": [{
                    "data": null,
                    "render": function(data, type, row) {
                        return '<a href="<?= base_url('admin/movimientos'); ?>/' + row.id + '/edit" class="btn btn-warning"><i class="fas fa-edit"></i></a>' +
                            '<a href="#" class="btn btn-danger" onclick="confirmDelete(' + row.id + ')"><i class="fas fa-trash"></i></a>';
                    }
                },
                {
                    "data": "id"
                },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        if (parseInt(data.movimiento) == 1) {
                            return '<span class="badge bg-success">INGRESO</span>';
                        } else {
                            return '<span class="badge bg-danger">EGRESO</span>';
                        }
                    }
                },
                {
                    "data": "fecha"
                },
                {
                    "data": "monto"
                },
                {
                    "data": "usuario"
                },
                {
                    "data": "comprobante"
                },

            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
            },
            order: [[1, 'desc']]
        });
    });

    function confirmDelete(idComp) {
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
                let url = '<?php echo base_url('admin/movimientos/'); ?>' + idComp;
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
                            moviTable.ajax.reload();
                        }
                    }
                }
            }
        });
    }
</script>
<?= $this->endSection() ?>