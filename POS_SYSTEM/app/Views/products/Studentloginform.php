<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Student Login</title>
</head>
<body>
    <h1>Student Login</h1>

    <?php if (session()->getFlashdata('success')): ?>
        <p style="color: green;"><?= session()->getFlashdata('success') ?></p>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <p style="color: red;"><?= session()->getFlashdata('error') ?></p>
    <?php endif; ?>

    <?php if (isset($validation)): ?>
        <div style="color: red;">
            <?= $validation->listErrors() ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= site_url('studentlogin') ?>">
        <?= csrf_field() ?>

        <label for="firstname">First name</label>
        <input type="text" id="firstname" name="firstname" value="<?= old('firstname') ?>">

        <br>

        <label for="password">Password</label>
        <input type="password" id="password" name="password">

        <br>

        <button type="submit">Login</button>
    </form>

    <p><a href="<?= site_url('studentregister') ?>">Create student account</a></p>
</body>
</html>
