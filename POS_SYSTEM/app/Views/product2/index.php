
<?php
// app/Views/product2/index.php
// Controller passes: $allProducts
$items = $allProducts ?? [];
?>

<?php 
/** @var array $allProducts*/
foreach ($allProducts as $item): ?>
    <p>Name: <?= esc($item['name']) ?></p>
<?php endforeach; ?>



  