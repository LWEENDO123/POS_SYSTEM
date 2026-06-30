<h1>Products Demo</h1>

<h3>All Products</h3>

<?php
/** @var array $allProducts*/
/** @var array $lowStock*/
/** @var array $latestProducts*/
/** @var array $byName*/

?>
<ul>
<?php foreach($allProducts as $p): ?>
    <li><?= $p['name'] ?> (Stock: <?= $p['stock_quantity'] ?>)</li>
<?php endforeach; ?>
</ul>

<h3>Product by ID (1)</h3>
<p><?= $oneProduct['name'] ?? 'Not found' ?></p>

<h3>First Product</h3>
<p><?= $firstProduct['name'] ?? 'None' ?></p>

<h3>Low Stock Products (stock < 10)</h3>
<ul>
<?php foreach($lowStock as $p): ?>
    <li><?= $p['name'] ?> (Stock: <?= $p['stock_quantity'] ?>)</li>
<?php endforeach; ?>
</ul>

<h3>Latest Products</h3>
<ul>
<?php foreach($latestProducts as $p): ?>
    <li><?= $p['name'] ?> (Created: <?= $p['created_at'] ?>)</li>
<?php endforeach; ?>
</ul>

<h3>Search by Name</h3>
<form method="get" action="/products">
    <input type="text" name="name" placeholder="Enter product name">
    <button type="submit">Search</button>
</form>
<ul>
<?php foreach($byName as $p): ?>
    <li><?= $p['name'] ?> (ID: <?= $p['product_id'] ?>)</li>
<?php endforeach; ?>
</ul>
