<?= $this->extend('admin_layout') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Cerrar Caja</h5>
        <hr>
        <?php if (isset($validation)) : ?>
            <div class="alert alert-danger">
                <?= $validation->listErrors() ?>
            </div>
        <?php endif; ?>
        <form action="<?= base_url('admin/cajas/cerrar') ?>" method="post" class="row" enctype="multipart/form-data">
            <div class="form-group col-md-6 mb-3">
                <label>Monto Inicial</label>
                <input type="text" class="form-control" value="<?= $caja['monto_inicial']; ?>" placeholder="0.00" disabled >
            </div>

            <div class="form-group col-md-6 mb-3">
                <label>Fecha Apertura</label>
                <input type="text" class="form-control" value="<?= $caja['fecha_apertura']; ?>" disabled>
            </div> 

            <div class="form-group col-md-4 mb-3">
                <label>Ingresos</label>
                <input type="text" class="form-control" value="<?= $ingresos['monto'];  ?>" placeholder="0.00" disabled >
            </div>

            <div class="form-group col-md-4 mb-3">
                <label>Egresos</label>
                <input type="text" class="form-control" value="<?= $egresos['monto']; ?>" placeholder="0.00" disabled >
            </div>

            <div class="form-group col-md-4 mb-3">
                <label>Saldo</label>
                <input type="text" class="form-control" value="<?= number_format(($caja['monto_inicial'] + $ingresos['monto']) - $egresos['monto'], 2); ?>" placeholder="0.00" disabled >
            </div>

            <div class="col-md-12 text-end">
                <a href="<?= base_url('admin/cajas'); ?>" class="btn btn-danger">Cancelar</a>
                <button type="submit" class="btn btn-primary">Cerrar</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>