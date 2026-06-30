<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Products</title>
</head>
<body>

<h2>List of Products</h2>

<?php

/** @var array $products */


foreach($products as $product): ?>

<p>
    <?= $product['name']; ?>
    -
    <?= $product['price']; ?>
</p>

<?php endforeach; ?>



</body>
</html>




