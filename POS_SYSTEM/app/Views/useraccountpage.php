app//view/useraccountpage.php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Welcome to the registration for sign in user</h1>
    <div class="form2">
        
        <form action="<?= site_url('create-account') ?>" method="post">
            <?php if (session()->getFlashdata('message1')): ?>
            <div style="color:red">
                <?= session()->getFlashdata('message1'); ?>
            </div>
            <?php endif; ?>
            
            <label>Firstname</label><br>
            <input type="text"  name="firstname" value="<?= old('firstname')?>" placeholder="enter firstname"><br>
            
            <label>lastname</label><br>
            <input type="text" name="lastname" value="<?= old('lastname')?>" placeholder="enter lastname"><br>
            <?php if (session()->getFlashdata('message2')): ?>
            <div style="color:red">
                <?= session()->getFlashdata('message2'); ?>
            </div>
            <?php endif; ?>
            <label>Email</label><br>
            <input type="text"  name="email" value="<?= old('email')?>" placeholder="enter email"><br>
            <label>password</label><br>
            <input type="password" name="password" value="<?= old('password')?>" placeholder="enter password"><br>

            <button type="submit">
                REGISTER
            </button>
        </form>
    </div>
    <style>
        .form2{
            position: relative;
            left: 50px;
        }
    </style>
</body>
</html>