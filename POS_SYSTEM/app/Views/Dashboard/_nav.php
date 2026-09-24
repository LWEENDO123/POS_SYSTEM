<?php
/*
SHARED SIDEBAR NAVIGATION PARTIAL
Included by every Dashboard page so the menu is IDENTICAL everywhere
(same links, same order, same active highlight) - this fixes the
sidebar looking different on each page.

Usage:  <?= view('Dashboard/_nav', ['active' => 'receipt']) ?>
$active: dashboard | newsale | sales | products | receipt
*/
$posnav_items = [
    'dashboard' => ['label' => 'Dashboard', 'url' => base_url('DashBoard/index')],
    'newsale'   => ['label' => 'New Sale',  'url' => base_url('newsales')],
    'sales'     => ['label' => 'Sales',     'url' => base_url('sales')],
    'products'  => ['label' => 'Products',  'url' => base_url('productcontroller')],
    'receipt'   => ['label' => 'Receipt',   'url' => base_url('receipts')],
];
?>
<style>
  /* Scoped class names (posnav-) so they never clash with per-page CSS.
     The green panel makes the nav look the same whether the page's own
     sidebar is white (newsales) or green (receipts/dashboard/sales). */
  .posnav{ display:flex; flex-direction:column; gap:6px; width:100%;
           background:#0A5741; padding:16px 12px; border-radius:10px; }
  .posnav a{ display:block; text-decoration:none; color:#ffffff;
             padding:11px 14px; border-radius:8px; font-size:0.95rem;
             font-weight:500; text-align:left; cursor:pointer;
             font-family:inherit; transition:background-color 160ms ease; }
  .posnav a:hover,
  .posnav a.posnav-active{ background-color:#2c866d; }
</style>
<nav class="posnav">
  <?php foreach ($posnav_items as $key => $item): ?>
    <a href="<?= $item['url'] ?>" class="<?= ($active ?? '') === $key ? 'posnav-active' : '' ?>"><?= esc($item['label']) ?></a>
  <?php endforeach; ?>
</nav>
