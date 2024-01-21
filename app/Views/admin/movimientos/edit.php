<?= $this->extend('admin_layout') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="<?php echo base_url('admins/assets/css/radio.css'); ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Editar Movimiento</h5>
        <hr>
        <?php if (isset($validation)) : ?>
            <div class="alert alert-danger">
                <?= $validation->listErrors() ?>
            </div>
        <?php endif; ?>
        <form action="<?= base_url('admin/movimientos/' . $movimiento['id']) ?>" method="post" class="row" autocomplete="off">
            <input type="hidden" name="_method" value="PUT">

            <div class="form-group col-md-4 mb-3">
                <label for="monto">Monto</label>
                <input type="text" name="monto" class="form-control" value="<?= set_value('monto', $movimiento['monto']); ?>" placeholder="Monto" required>
            </div>

            <div class="form-group col-md-4 mb-3">
                <label for="fecha">Fecha</label>
                <input type="text" id="fecha" name="fecha" class="form-control" value="<?= set_value('fecha', $movimiento['fecha']); ?>" required>
            </div>

            <div class="form-group col-md-4 mb-3">
                <label for="comprobante">Comprobante</label>
                <select id="comprobante" class="form-control" name="comprobante">
                    <option value="">Seleccionar</option>
                    <?php foreach ($comprobantes as $comprobante) { ?>
                        <option value="<?php echo $comprobante['id']; ?>" <?php echo ($movimiento['id_comprobante'] == $comprobante['id']) ? 'selected' : ''; ?>>
                            <?php echo $comprobante['nombre']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group col-md-4 mb-3">
                <label>Tipo</label>
                <br>
                <ul class="radio-switch ">
                    <li class="radio-switch__item">
                        <input class="radio-switch__input ri5-sr-only" type="radio" name="radio-switch-name" id="radio-1" value="1" <?php echo ($movimiento['movimiento'] == 1) ? 'checked' : ''; ?>>
                        <label class="radio-switch__label" for="radio-1">Ingreso</label>
                    </li>

                    <li class="radio-switch__item">
                        <input class="radio-switch__input ri5-sr-only" type="radio" name="radio-switch-name" id="radio-2" value="2" <?php echo ($movimiento['movimiento'] == 2) ? 'checked' : ''; ?>>
                        <label class="radio-switch__label" for="radio-2">Egreso</label>
                        <div aria-hidden="true" class="radio-switch__marker"></div>
                    </li>
                </ul>
            </div>

            <div class="form-group col-md-8 mb-3">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" class="form-control" name="descripcion" rows="3" placeholder="Descripción"><?= set_value('descripcion', $movimiento['descripcion']); ?></textarea>
            </div>

            <div class="col-md-12 text-end">
                <a href="<?= base_url('admin/movimientos'); ?>" class="btn btn-danger">Cancelar</a>
                <button type="submit" class="btn btn-primary">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="<?php echo base_url('admins/assets/js/radio.js'); ?>"></script>
<script>
    flatpickr("#fecha", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
    });
</script>
<?= $this->endSection() ?>