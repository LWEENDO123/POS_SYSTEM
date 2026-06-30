<?php
/** @var array $allproducts */ // fixed: matches the controller key 'allproducts'

foreach ($allproducts as $products) { // fixed: PHP variable names are case-sensitive
    echo "product_id".$products['product_id']."<br>";
    echo "name".$products['name']."<br>";
    echo "barcode".$products['barcode']."<br>";
    echo "price".$products['price']."<br>";
    echo "stock_quantity".$products['stock_quantity']."<br>";
    # code...
}


