<?php
/*
RECEIPT PREVIEW FRAGMENT
The popup (Receipts list + Dashboard) fetches Receipt::view_receipt with
?modal=1 and this fragment is returned, so the user stays on the same page.
Same markup/styles as the full-page Dashboard/test2.html receipt.
*/
?>
<style>
  .receipt-preview{
    max-width:360px; margin:0 auto; background:#fff;
    border:1px dashed #c3c7c5; border-radius:12px; padding:32px 24px;
    font-family:'Courier New', monospace; box-shadow:0 2px 12px rgba(0,0,0,.15);
  }
  .receipt-preview .r-header{ text-align:center; margin-bottom:20px; }
  .receipt-preview .r-header h2{ font-size:18px; color:#0A5741; }
  .receipt-preview .r-header p{ font-size:12px; color:#6b7280; }
  .receipt-preview .r-divider{ border:none; border-top:1px dashed #c3c7c5; margin:12px 0; }
  .receipt-preview .r-meta{ font-size:12px; color:#444; display:flex; justify-content:space-between; margin-bottom:4px; }
  .receipt-preview .r-meta span{ color:#333; }
  .receipt-preview .r-items .r-item{ display:flex; justify-content:space-between; font-size:13px; margin-bottom:4px; }
  .receipt-preview .r-total{ display:flex; justify-content:space-between; font-size:16px; font-weight:700; margin-top:8px; color:#0A5741; }
  .receipt-preview .r-footer{ text-align:center; font-size:12px; color:#6b7280; margin-top:20px; }
</style>

<div class="receipt-preview" id="rcptContent">
  <div class="r-header">
    <h2>QuickPOS</h2>
    <p>123 Market Street, City</p>
    <p>Tel: (555) 123-4567</p>
  </div>

  <hr class="r-divider">

  <div class="r-meta">
    <span>#<?= esc($receipt_number ?? 'N/A') ?></span>
    <span><?= esc($generated_at ?? '') ?></span>
  </div>
  <div class="r-meta">
    <span>Cashier:</span>
    <span><?= esc($cashier ?? 'unknown') ?></span>
  </div>
  <div class="r-meta">
    <span>Sale ID:</span>
    <span>#<?= esc($sale['sale_id'] ?? '') ?></span>
  </div>

  <hr class="r-divider">

  <div class="r-items">
    <?php if(!empty($products)): ?>
      <?php foreach($products as $p): ?>
        <div class="r-item">
          <span><?= esc($p['product_name']) ?> x<?= esc($p['qty']) ?></span>
          <span>K<?= esc(number_format((float)$p['total'], 2)) ?></span>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="r-item"><span>No items</span><span>&mdash;</span></div>
    <?php endif; ?>
  </div>

  <hr class="r-divider">

  <div class="r-items">
    <div class="r-item"><span>Subtotal</span><span>K<?= esc(number_format((float)($total_amount ?? 0), 2)) ?></span></div>
  </div>
  <div class="r-total">
    <span>TOTAL</span>
    <span>K<?= esc(number_format((float)($total_amount ?? 0), 2)) ?></span>
  </div>

  <hr class="r-divider">

  <div class="r-meta">
    <span>Payment:</span>
    <span><?= esc($payment_method ?? 'N/A') ?></span>
  </div>

  <div class="r-footer">
    <p>Thank you for your purchase!</p>
    <p>&#9733; &#9733; &#9733; &#9733; &#9733;</p>
  </div>
</div>
