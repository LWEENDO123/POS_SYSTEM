<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form Example</title>
    <style>
        /* Main container for the login box */
        .container {
            width: 350px;
            height: auto;              /* let height grow with content */
            background-color: #0F6E51;
            margin: 10% auto;          /* center horizontally with auto margins */
            border-radius: 20px;
            padding: 20px;             /* space inside the box */
            display: flex;
            flex-direction: column;    /* stack children vertically */
            align-items: center;       /* center horizontally */
        }

        /* Heading text */
        .text {
            color: white;
            margin-bottom: 10px;       /* space below heading */
            text-align: center;
        }

        /* Sub-paragraph text */
        .ptag {
            color: white;
            text-align: center;
            margin-bottom: 20px;       /* space below paragraph */
        }

        /* Avatar image */
        .container img {
            width: 70px;
            height: 70px;
            border-radius: 50%;        /* make it circular */
            margin-bottom: 15px;
        }

        /* Form styling */
        .container form {
            width: 100%;               /* form takes full width of container */
            display: flex;
            flex-direction: column;    /* stack inputs vertically */
        }

        .container label {
            color: white;
            margin-bottom: 5px;
        }

        .container input {
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: none;
        }

        .container button {
            border-radius: 20px;
            padding: 10px;
            background-color: white;
            color: red;
            font-weight: bold;
            cursor: pointer;
        }

        .container button:hover {
            background-color: #ddd;    /* hover effect */
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Heading -->
        <h2 class="text">Welcome Back</h2>

        <!-- Avatar image -->
        <img src="<?= base_url('assets/default-avatar-profile-icon-social-media-user-image-gray-avatar-icon-blank-profile-silhouette-illustration-vector.jpg') ?>" alt="User Avatar">




        <!-- Subtext -->
        <p class="ptag">Sign in with your cashier account to open the till.</p>
        <?php if (session()->getFlashdata('success')): ?>
            <div style="color: red;">
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
            <?php endif; ?>

        <?php if (session()->getFlashdata('message')): ?>
        <div style="color: red;">
            <?= esc(session()->getFlashdata('message')) ?>
        </div>
        <?php endif; ?>

        <!-- Login form -->
        <form action="<?= site_url('userlogin')?>" method="post">
            <label for="username">Username</label>
            <input type="text"  name="username" value="<?=esc('username') ?>" >

            <label for="password">Password</label>
            <input type="password" id="password" name="password" value="<?=esc('password') ?>">
            

            

            <button type="submit">LOGIN</button>

           

            
        </form>
    </div>

</body>
</html>
