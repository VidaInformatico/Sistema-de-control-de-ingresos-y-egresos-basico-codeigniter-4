<?= $this->extend('admin_layout') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Abrir Caja</h5>
        <hr>
        <?php if (isset($validation)) : ?>
            <div class="alert alert-danger">
                <?= $validation->listErrors() ?>
            </div>
        <?php endif; ?>
        <form action="<?= base_url('admin/cajas'); ?>" method="post" class="row" autocomplete="off">
            <div class="form-group col-md-6 mb-3">
                <label>Monto Inicial</label>
                <input type="text" name="monto_inicial" class="form-control" value="<?= set_value('monto_inicial'); ?>" placeholder="0.00" >
            </div>
            <div class="form-group col-md-6 mb-3">
                <label>Fecha Apertura</label>
                <input type="date" name="fecha_apertura" class="form-control" value="<?= set_value('fecha_apertura'); ?>" >
            </div>

            <div class="col-md-12 text-end">
                <a href="<?= base_url('admin/cajas'); ?>" class="btn btn-danger">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>