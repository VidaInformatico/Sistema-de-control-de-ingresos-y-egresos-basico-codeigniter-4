<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="<?= base_url('admins/assets/js/64d58efce2.js'); ?>" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?= base_url('admins/assets/css/login.css'); ?>" />

    <title>Iniciar | Sesión</title>
</head>

<body>
    <h1>Login</h1>
    <form action="<?= base_url('login'); ?>" method="post" autocomplete="off">
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="text-danger">
                <?= session()->getFlashdata('error'); ?>
            </div>
        <?php endif; ?>
        <div class="row">
            <label for="usuario">Usuario</label>
            <input type="text" id="usuario" name="usuario" placeholder="Usuario">
            <?php if (isset($username_error)) : ?>
                <div class="text-danger mt-2">
                    <?= $username_error ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="row">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Contraseña">
            <?php if (isset($password_error)) : ?>
                <div class="text-danger mt-2">
                    <?= $password_error ?>
                </div>
            <?php endif; ?>
        </div>
        <button type="submit">Login</button>
    </form>
</body>

</html>