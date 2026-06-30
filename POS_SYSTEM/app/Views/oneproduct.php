<?php

/** @var string $oneproduct */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="<?= site_url('products/oneproduct') ?>" method="GET"><!-- fixed: form action now matches the route /products/oneproduct -->
        <label>Enter Product user ID</label><br>

        <input type="number" name="product_id" value="<?= old('product_id') ?>"><!-- fixed: old() uses the field name, and number is the correct input type -->

        <input type="submit" name="Find_product" value="FIND">

    </form>

    <?php if (! empty($oneproduct)): ?><!-- fixed: only show product data after a product is found -->
        <p>Product ID: <?= $oneproduct['product_id'] ?></p>
        <p>Name: <?= $oneproduct['name'] ?></p>
        <p>Barcode: <?= $oneproduct['barcode'] ?></p>
        <p>Price: <?= $oneproduct['price'] ?></p>
        <p>Stock Quantity: <?= $oneproduct['stock_quantity'] ?></p>
    <?php endif; ?>
    
</body>
</html>
