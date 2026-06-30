<?php

use CodeIgniter\HTTP\SiteURI;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="<?= site_url('studentregister') ?>" method="POST"><br>
        <?= csrf_field() ?> <!-- fixed: add the CSRF token for POST forms -->
        <label>Student Register Form</label><br>

        <label>Firstname</label><br>

        <input type="text" name="firstname" value="<?= old('firstname') ?>"><!-- fixed: value should show old submitted firstname, not the word firstname --><br>

        <label>Lastname</label><br>

        <input type="text" name='lastname' value="<?= old('lastname') ?>"><!-- fixed: value should show old submitted lastname, not the word lastname --><br>

        <label>Email</label><br>

        <input type="text" name='email' value="<?= old('email') ?>"><!-- fixed: value should show old submitted email, not the word email --><br>

        <label>phone</label><br>

        
        <input type="text" name='phone' value="<?= old('phone') ?>"><!-- fixed: value should show old submitted phone, not the word phone --><br>
        
        <label>student_id</label><br>
        <input type="text" name='student_id' value="<?= old('student_id') ?>"><!-- fixed: value should show old submitted student_id, not the word student_id --><br>

        

        <label>password</label><br>

        <input type="password" name='password' value=""><!-- fixed: password should not display the word password --><br>
        <input type="submit" value="register">


    </form>
</body>
</html>
