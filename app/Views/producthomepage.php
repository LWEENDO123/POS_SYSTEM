<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <body>
    <div class='userform'>
        <label>Enter New Product</label>
        <form action="<?= site_url('products/create') ?>" method="post">
            
            <label>Name</label><br>
            <input type="text" name="name" placeholder="Enter product name" value="<?= old('name') ?>"><br>

            <label>Barcode</label><br>
            <input type="text" name="barcode" placeholder="Enter barcode" value="<?= old('barcode') ?>"><br>

            <label>Price</label><br>
            <input type="text" name="price" placeholder="Enter price" value="<?= old('price') ?>"><br>

            <label>Stock Quantity</label><br>
            <input type="text" name="stock_quantity" placeholder="Enter stock quantity" value="<?= old('stock_quantity') ?>"><br>

            <label>Category ID</label><br>
            <input type="text" name="category_id" placeholder="Enter category ID" value="<?= old('category_id') ?>"><br>

            <button type="submit">Save Product</button>
        </form>
    </div>

    <style>
        .userform{
            position: absolute;
            left: 500px;
            top: 40px;
            
            
        }
    </style>
</body>
</html>

</body>
</html>