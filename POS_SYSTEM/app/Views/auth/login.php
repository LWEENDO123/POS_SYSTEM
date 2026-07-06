<?php
//app/view/auth/login.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <div class="Login">

        <h1>USERLOGIN</h1>
    <p>
        Please Enter your password and username
    </p>

    <?php if (session()->getFlashdata('message')): ?>
    <p style="color:red;"><?= session()->getFlashdata('message') ?></p>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
    <p style="color:green;"><?= session()->getFlashdata('success') ?></p>
    <?php endif; ?>

    <form action="<?= site_url('userlogin') ?>" method="post">


        <label>Username</label><br>
        <input type="text" name="username"  value="<?= old('username') ?>" placeholder="Enter username please"><br>

        <label>Password</label><br>
        <input type="password" name="password"  value=<?= old('password') ?> placeholder="Enter username please"><br>

       
        <button type="submit">
            LOGIN

        </button>
        

        


    </form>
    </div>
    <style>
        .Login{
            
            position: relative;
            margin-left: 200px;
            bottom: -70px;



        }

    </style>


</body>
</html>
