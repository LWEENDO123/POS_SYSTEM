<?php
/**
 * Controller passes: $firstproduct
 * Model::first() returns a single row (array or null).
 */

$firstproduct = $firstproduct ?? null;
?>

<?php if (is_array($firstproduct)): ?>
    <?= esc($firstproduct['name'] ?? '') ?>
<?php else: ?>
    <p>Not found</p>
<?php endif; ?>

