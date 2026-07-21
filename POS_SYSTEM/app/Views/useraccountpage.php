<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <style>
        /* Main container for the registration box */
        .container {
            width: 400px;
            background-color: #0F6E51;   /* green background */
            margin: 100px auto;          /* center horizontally */
            border-radius: 20px;
            padding: 20px;
            display: flex;
            flex-direction: column;      /* stack children vertically */
            align-items: center;         /* center horizontally */
            box-shadow: 0 4px 8px rgba(0,0,0,0.3); /* nice shadow */
        }

        /* Heading text */
        .container h2 {
            color: white;
            margin-bottom: 10px;
            font-family: sans-serif;
        }

        /* Subtext */
        .container p {
            color: white;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Form styling */
        .container form {
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        .container label {
            color: white;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .container input {
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 20px;
            border: none;
            outline: none;
        }

        .container button {
            border-radius: 20px;
            padding: 10px;
            background-color: white;
            color: #0F6E51;
            font-weight: bold;
            cursor: pointer;
            border: none;
        }

        .container button:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Heading -->
        <h2>Create Account</h2>

        <!-- Subtext -->
        <p>Please fill in your details to register.</p>

        <!-- Registration form -->
        <form action="<?= site_url('create-account') ?>" method="post">
            <label for="firstname">Firstname</label>
            <input type="text" id="firstname" name="firstname" value="<?=esc('firstname')?>">

            <label for="lastname">Lastname</label>
            <input type="text" id="lastname" name="lastname" value="<?=esc('lastname')?>" >

            <?php
            if(session()->getflashdata('message2')):
            
            ?>
            <div style="color: red;">
                <?=session()->getflashdata('message2')?>
            </div>
            <?php endif;?>

            <label for="email">Email</label>
            <input type="email" id="email" name="email"  value="<?=esc('email')?>" >

            
            <label>phone</label>
            <input type="text" name="phone"  value="<?=esc('phone')?>" >

            <label for="password">Password</label>
            <input type="password" id="password" name="password"value="<?=esc('password')?>" >
            <?php
            if(session()->getflashdata('success')):
            
            ?>
            <div style="color: red;">
                <?=session()->getflashdata('success')?>
            </div>
            <?php endif;?>

            



            <button type="submit">Register</button>
        </form>
    </div>

</body>
</html>
