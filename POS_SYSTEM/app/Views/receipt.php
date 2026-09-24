<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt</title>
    <style>
        body { background:#eef1ef; font-family: Arial, sans-serif; margin:0; padding:20px; }
        .receipt {
            max-width: 420px; margin: 0 auto; background:#fff; border-radius:12px;
            box-shadow: 0 2px 12px rgba(0,0,0,.15); padding:24px; color:#222;
        }
        h2 { margin:0 0 4px; color:#0A5741; text-align:center; }
        .muted { color:#666; font-size:13px; text-align:center; }
        .line { border-top:1px dashed #999; margin:14px 0; }
        .row { display:flex; justify-content:space-between; padding:5px 0; font-size:14px; }
        .row.head { font-weight:bold; border-bottom:1px solid #e0e0e0; color:#0A5741; }
        .total { display:flex; justify-content:space-between; font-weight:bold; font-size:17px; margin-top:6px; color:#0A5741; }
        .meta { font-size:13px; color:#444; }
        .meta span { font-weight:bold; }
    </style>
</head>
<body>
<div class="receipt">
    <h2>Receipt</h2>
    <p class="muted"><?= esc($receipt_number ?? '') ?></p>

    <div class="line"></div>
    <div class="meta">
        <div>Cashier: <span><?= esc($cashier ?? 'unknown') ?></span></div>
        <div>Payment: <span><?= esc($payment_method ?? '-') ?></span></div>
        <div>Date: <span><?= esc($generated_at ?? '') ?></span></div>
        <div>Sale ID: <span>#<?= esc($sale['sale_id'] ?? '') ?></span></div>
    </div>
    <div class="line"></div>

    <div class="row head">
        <div>Item</div>
        <div>Qty</div>
        <div>Price</div>
        <div>Total</div>
    </div>

    <?php if(!empty($products)): ?>
        <?php foreach($products as $p): ?>
            <div class="row">
                <div style="flex:2"><?= esc($p['product_name']) ?></div>
                <div style="width:40px;text-align:center;"><?= esc($p['qty']) ?></div>
                <div style="width:70px;text-align:right;">K<?= esc(number_format((float)$p['price'],2)) ?></div>
                <div style="width:80px;text-align:right;">K<?= esc(number_format((float)$p['total'],2)) ?></div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="row"><div>No products on this receipt.</div></div>
    <?php endif; ?>

    <div class="line"></div>
    <div class="total">
        <div>TOTAL</div>
        <div>K<?= esc(number_format((float)$total_amount,2)) ?></div>
    </div>
</div>
</body>
</html>