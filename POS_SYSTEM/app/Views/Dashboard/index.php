<?php
/** @var string $user */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <header>
        <h1>welcome <?=esc($user)?></h1>

    </header>
    <main>
    <h1></h1>
    <div class="box1">
        <img class="product" src="images/Allproduct.jpeg" width="300" height="230">

        <h5 class="word">
        click on the button below to see all products
        </h5>
        <a href="<?=site_url('DashBoard/index') ?>"><button class="allproducts">
        Submit
        </button>
        </a>
    </div>

    

    <div class="box2">
        
    </div>

    <div class="box3">
        
    </div>


    
    <style>
        .box1{
            border: 2px solid rgb(57, 193, 251);
            width:300px;
            height: 300px;
            border-radius: 20px;
            background-color: rgb(57, 193, 251);
            margin-left:20px;
            margin-top: 90px;
            
            
        }
        .product{
            border-radius: 20px;
            
        }
        .allproducts{
            background-color: rgb(158, 255, 202);
            border-radius: 20px;
            margin-left: 0px;
            margin-top:0px;
            margin-right:80px;

        }
        .word{
            margin-top: 0px;
        
        }
        
    </style>
    </main>
</body>
</html>